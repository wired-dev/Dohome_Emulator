<?php
date_default_timezone_set('America/Los_Angeles');
use Swoole\Table;
$db = new SQLite3('mysqlitedb.db');
$db->exec('CREATE TABLE devices (id INTEGER, dev_id STRING, dev_name STRING, dev_key STRING, dev_mode INT, mode INT, r INT, g INT, b INT, w INT, m INT, remote INT, repeater INT, _index INT, freq INT)');
//$db = new SQLite3('devices.db');
//$db->busyTimeout(5000);
// WAL mode has better control over concurrency.
// Source: https://www.sqlite.org/wal.html
//$db->exec('PRAGMA journal_mode = wal;');
//$multi=19.60784313725490196078431372549;
//$multi=19.53125;
$cietable = array(0, 2, 4, 7, 9, 11, 13, 15, 17, 20, 22, 24, 26, 28, 30, 33, 35, 37, 39, 41, 43, 46, 48,
                  50, 53, 55, 58, 60, 63, 66, 69, 72, 75, 78, 81, 84, 88, 91, 95, 98, 102, 106, 110, 114,
                  118, 122, 126, 131, 135, 140, 145, 149, 154, 159, 165, 170, 175, 181, 186, 192, 198, 204,
                  210, 216, 222, 229, 235, 242, 249, 256, 263, 270, 277, 285, 292, 300, 308, 316, 324, 332,
                  341, 349, 358, 367, 376, 385, 394, 403, 413, 422, 432, 442, 452, 463, 473, 484, 495, 506,
                  517, 528, 539, 551, 563, 574, 587, 599, 611, 624, 636, 649, 662, 676, 689, 703, 717, 731,
                  745, 759, 774, 788, 803, 818, 834, 849, 865, 880, 897, 913, 929, 946, 963, 980, 997, 1014,
                  1032, 1050, 1068, 1086, 1104, 1123, 1142, 1161, 1180, 1200, 1219, 1239, 1259, 1280, 1300,
                  1321, 1342, 1363, 1385, 1406, 1428, 1450, 1472, 1495, 1518, 1541, 1564, 1588, 1611, 1635,
                  1659, 1684, 1709, 1733, 1759, 1784, 1810, 1835, 1862, 1888, 1915, 1941, 1969, 1996, 2024,
                  2051, 2080, 2108, 2137, 2165, 2195, 2224, 2254, 2284, 2314, 2344, 2375, 2406, 2437, 2469,
                  2501, 2533, 2565, 2598, 2631, 2664, 2697, 2731, 2765, 2799, 2834, 2869, 2904, 2940, 2975,
                  3011, 3048, 3084, 3121, 3158, 3196, 3234, 3272, 3310, 3349, 3388, 3427, 3466, 3506, 3546,
                  3587, 3628, 3669, 3710, 3752, 3794, 3836, 3879, 3922, 3965, 4009, 4053, 4097, 4142, 4187,
                  4232, 4277, 4323, 4369, 4416, 4463, 4510, 4557, 4605, 4653, 4702, 4751, 4800, 4849, 4899, 4949, 5000);
$table = new swoole_table(1024);
$table->column('dev_key', swoole_table::TYPE_STRING, 128);
$table->column('dev_name', swoole_table::TYPE_STRING, 64);
$table->column('lastbeat', swoole_table::TYPE_INT, 4);
$table->column('mode', swoole_table::TYPE_INT, 2);
$table->column('dev_mode', swoole_table::TYPE_INT, 2);
$table->column('r', swoole_table::TYPE_INT, 4);
$table->column('g', swoole_table::TYPE_INT, 4);
$table->column('b', swoole_table::TYPE_INT, 4);
$table->column('w', swoole_table::TYPE_INT, 4);
$table->column('m', swoole_table::TYPE_INT, 4);
$table->column('remote', swoole_table::TYPE_INT, 2);
$table->column('repeater', swoole_table::TYPE_INT, 2);
$table->column('year', swoole_table::TYPE_INT, 2);
$table->column('mon', swoole_table::TYPE_INT, 2);
$table->column('day', swoole_table::TYPE_INT, 2);
$table->column('hour', swoole_table::TYPE_INT, 2);
$table->column('min', swoole_table::TYPE_INT, 2);
$table->column('sec', swoole_table::TYPE_INT, 2);
$table->column('type', swoole_table::TYPE_INT, 2);
$table->column('index', swoole_table::TYPE_INT, 2);
$table->column('freq', swoole_table::TYPE_INT, 4);
$table->column('ip', swoole_table::TYPE_STRING, 20);
$table->create();
$http = new swoole_http_server("0.0.0.0", 8008);
$http->set(array('open_http_protocol' => true));
$serv=$http->addListener('0.0.0.0',8899,SWOOLE_SOCK_TCP);
$serv->set([
    'dispatch_mode' => 1,
    'open_eof_split' => true,
    'package_eof' => "\r\n",
]);
$http->on('start', function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:8008\n";
});
$static = [
    'css'  => 'text/css',
    'js'   => 'text/javascript',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'jpg'  => 'image/jpg',
    'jpeg' => 'image/jpg',
    'mp4'  => 'video/mp4'
];
$http->on('request', function ($request, $response) use ($table,$static,$db,$cietable) {
    $_indexModes = array("","RGB Fade","Red Fade","Green Fade","Blue Fade","Yellow Fade","Cyan Fade","Purple Fade","White Fade","Red Strobe","Green Strobe","Blue Strobe","Yellow Strobe","Red-Green Fade","Red-Blue Fade","Green-Blue Fade","Red-Green Jump","Red-Blue Jump","Green-Blue Jump","Red-Green Strobe","Red-Blue Strobe","Green-Blue Strobe","RGB Jump","RGB Strobe","White Strobe");
    $_devModes = array("","Manual","indexed?","Auto");
    if(strcmp($request->header['user-agent'],"ESP8266") != 0){
        if (getStaticFile($request, $response, $static)) {
            return;
        } else {
            if(strcmp($request->get['id'],NULL) == 0){
                $response->header("Content-Type", "text/html");
                $response->write("<!DOCTYPE html><html lang=\"en\" ><head><meta charset=\"utf-8\" /><title>Device List</title></head><body>");
                foreach($table as $key => $vaule){
                    $response->write("<a href=\"?id=". $key . "\">" . $table->get($key,"dev_name") ." " . $key . "</a><br>");
                }
                $response->end("</body></html>");
            } else {
                $id=$request->get['id'];
                if(isset($request->post['rVal'])) {
                    $rVal =ltrim($request->post['rVal'],'0');
                } else {
                    $rVal = Reverse_CIE_Lookup($table->get($id,'r'),$cietable);
                }
                if(isset($request->post['gVal'])) {
                    $gVal = ltrim($request->post['gVal'],'0');
                } else {
                    $gVal = Reverse_CIE_Lookup($table->get($id,'g'),$cietable);
                }
                if(isset($request->post['bVal'])) {
                    $bVal =ltrim($request->post['bVal'],'0');
                } else {
                    $bVal = Reverse_CIE_Lookup($table->get($id,'b'),$cietable);
                }
                if(isset($request->post['wVal'])) {
                    $wVal = ltrim($request->post['wVal'],'0');
                } else {
                    $wVal = Reverse_CIE_Lookup($table->get($id,'w'),$cietable);
                }
                if(isset($request->post['mVal'])) {
                    $mVal =ltrim($request->post['mVal'],'0');
                } else {
                    $mVal = $table->get($id,'m');
                }
                if(isset($request->post['dev_index'])) {
                    $indexVal = ltrim($request->post['dev_index'],'0');
                } else {
                    $indexVal = $table->get($id,'index');
                }
                if(isset($request->post['freq'])) {
                    $freqVal = ltrim($request->post['freq'],'0');
                } else {
                    $freqVal = $table->get($id,'freq');
                }
                if(isset($request->post['dev_mode'])){
                    $dev_mode = ltrim($request->post['dev_mode'],'0');
                } else {
                    $dev_mode = $table->get($id,'dev_mode');;
                }
                if($request->post){
                    if(isset($request->post['Save'])){
                        $rValm = $cietable[$rVal];
                        $gValm = $cietable[$gVal];
                        $bValm = $cietable[$bVal];
                        $wValm = $cietable[$wVal];
                        $db->exec("UPDATE devices SET r = $rValm,g = $gValm,b = $bValm,w = $wValm,m = $mVal,_index=$indexVal, dev_mode=$dev_mode WHERE dev_id='" . "$id" . "';");
                    }
                    $table->set($id, ['r' => $cietable[$rVal], 'g' => $cietable[$gVal], 'b' => $cietable[$bVal], 'w' => $wVal,'m' => $mVal]);
                    $table->set($id, ['freq' => $freqVal, 'index' => $indexVal, 'dev_mode'=> $dev_mode ]);
                    $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
                    $client->on("connect", function(swoole_client $cli) use ($table,$id,$dev_mode) {
                        if(strcmp($dev_mode,"1") == 0){
                            $cli->send("{\"cmd\":6,\"r\":".$table->get($id,'r').",\"g\":".$table->get($id,'g').",\"b\":".$table->get($id,'b') . ",\"w\":" . $table->get($id,'w') . ",\"m\":" . $table->get($id,'m') . "}\r\n");
                        } else if(strcmp($dev_mode,"3") == 0){
                            if((date('H') >= 0)&&(date('H') <=8)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":0,\"m\":0}\r\n");
                            } else if((date('H') >= 10)&&(date('H') <=16)){
                                $cli->send("{\"cmd\":6,\"r\":" . rand(0,5000) . ",\"g\":" . rand(0,5000) .",\"b\":" . rand(0,5000) . ",\"w\":" . rand(10,2500) . ",\"m\":" . "200" . "}\r\n");
                            } else if((date('H') >= 17)&&(date('H') <=20)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":4500,\"m\":0}\r\n");
                            } else if((date('H') >= 21)&&(date('H') <=22)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":2500,\"m\":0}\r\n");
                            } else if((date('H') >= 23)&&(date('H') <=0)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":1500,\"m\":0}\r\n");
                            }
                        } else if(strcmp($dev_mode,"2") == 0){
                            $cli->send("{\"cmd\":7,\"index\":".$table->get($id,'index').",\"freq\":".$table->get($id,'freq')."}\r\n");
                        }
                    });
                    $client->on("receive", function(swoole_client $cli, $data) use ($table){
                    });
                    $client->on("error", function(swoole_client $cli){
                    });
                    $client->on("close", function(swoole_client $cli){
                    });
                    $client->connect($table->get($id,'ip'), 5555);
                }
                $rgbcolor = "#". sprintf("%02x",Reverse_CIE_Lookup($table->get($id,'r'),$cietable)). sprintf("%02x",Reverse_CIE_Lookup($table->get($id,'g'),$cietable)).sprintf("%02x",Reverse_CIE_Lookup($table->get($id,'b'),$cietable));
                $response->header("Content-Type", "text/html");
                $response->write("<!DOCTYPE html><html lang=\"en\" ><head><meta charset=\"utf-8\" />
                    <title>Color Picker</title>
                    <!-- add styles --><link href=\"main.css\" rel=\"stylesheet\" type=\"text/css\" />
                    <!-- add scripts --><script src=\"jquery.js\"></script><script src=\"script.js\"></script>
                    </head><body><!-- preview element --><div class=\"preview\"></div><!-- colorpicker element -->
                    <canvas id=\"picker\" var=\"" . $rgbcolor . "\" width=\"300\" height=\"300\"></canvas><br>
                    <form id=\"123\" method=\"post\"><label> R </label> <input type=\"text\" name=\"rVal\" id=\"rVal\" value=\"");
                $response->write(Reverse_CIE_Lookup($table->get($id,'r'),$cietable));
                $response->write("\"/><br/><label> G </label> <input type=\"text\" name=\"gVal\" id=\"gVal\" value=\"");
                $response->write(Reverse_CIE_Lookup($table->get($id,'g'),$cietable));
                $response->write("\"/><br/><label> B </label> <input type=\"text\" name=\"bVal\" id=\"bVal\" value=\"");
                $response->write(Reverse_CIE_Lookup($table->get($id,'b'),$cietable));
                $response->write("\"/><br/><label> W </label> <input type=\"text\" name=\"wVal\" id=\"wVal\" value=\"");
                $response->write(Reverse_CIE_Lookup($table->get($id,'w'),$cietable));
                $response->write("\"/><br/><label> M </label> <input type=\"text\" name=\"mVal\" id=\"mVal\" value=\"");
                $response->write($table->get($id,'m'));
                $response->write("\"/><br/><label>RGB</label> <input type=\"text\" name=\"rgbVal\" id=\"rgbVal\" value=\"0,0,0\"><br/><label>HEX</label> <input type=\"text\" name=\"hexVal\" id=\"hexVal\" value=\"".$rgbcolor."\"/><br/><label>mode</label><select name=\"dev_mode\">");
                    for($x=1;$x<count($_devModes);$x++) {
                        if($x == $dev_mode){
                            $response->write("<option value=\"".$x."\" selected>".$_devModes[$x]."</option>");
                        } else {
                            $response->write("<option value=\"".$x."\">".$_devModes[$x]."</option>");
                        }
                    }
                    $response->write("</select><br /><label>Index?</label><select name=\"dev_index\">");
                    for($x=1;$x<count($_indexModes);$x++) {
                        if($x == $indexVal){
                            $response->write("<option value=\"".$x."\" selected>".$_indexModes[$x]."</option>");
                        } else {
                            $response->write("<option value=\"".$x."\">".$_indexModes[$x]."</option>");
                        }
                    }
                    $response->write("</select><br /><label>Freq:</label><input type=\"text\" name=\"freq\" id=\"freq\" value=\"".$freqVal."\"><br /><input type=\"submit\" value=\"Submit\"><input type=\"submit\" value=\"Save\" name=\"Save\"></form><br />");
                $response->write($table->get($id,'r') . " " . $table->get($id,'g') . " " . $table->get($id,'b') . " " . $table->get($id,'w') . "<br />");
                var_export($table->get($id));
                $response->end("</body></html>");
            }
        }
    } else {
        $dataparts = explode("&", trim($request->rawContent(), "\r\n"));
        $cmd = explode("=", $dataparts[0]);
        $id = explode("=", $dataparts[1]);
        $key = explode("=", $dataparts[2]);
        $status = explode("=", $dataparts[3]);
        $id = $id[1];
        $key = $key[1];
        $status = $status[1];
        $cmd = $cmd[1];
        $status = json_decode($status,true);
        //var_dump($status);
        if(strcmp($cmd,"stat") == 0){
            if($status['type']== 1){
                if(($table->get($id,'r') != $status['r'])||($table->get($id,'g') != $status['g'])||($table->get($id,'b') != $status['b'])||($table->get($id,'w') != $status['w'])||($table->get($id,'m') != $status['m'])){
                    $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
                    $client->on("connect", function(swoole_client $cli) use ($table,$id) {
                        if(strcmp($table->get($id,'dev_mode'),"1") == 0){
                            $cli->send("{\"cmd\":6,\"r\":".$table->get($id,'r').",\"g\":".$table->get($id,'g').",\"b\":".$table->get($id,'b') . ",\"w\":" . $table->get($id,'w') . ",\"m\":" . $table->get($id,'m') . "}\r\n");
                        } else if(strcmp($table->get($id,'dev_mode'),"3") == 0){
                            if((date('H') >= 0)&&(date('H') <=8)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":0,\"m\":0}\r\n");
                            } else if((date('H') >= 8)&&(date('H') <=18)){
                                $cli->send("{\"cmd\":6,\"r\":" . rand(0,5000) . ",\"g\":" . rand(0,5000) .",\"b\":" . rand(0,5000) . ",\"w\":" . rand(10,2500) . ",\"m\":" . "200" . "}\r\n");
                            } else if((date('H') >= 17)&&(date('H') <=20)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":4500,\"m\":0}\r\n");
                            } else if((date('H') >= 21)&&(date('H') <=22)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":2500,\"m\":0}\r\n");
                            } else if((date('H') >= 23)&&(date('H') <=0)){
                                $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":1500,\"m\":0}\r\n");
                            }
                        }
                    });
                    $client->on("receive", function(swoole_client $cli, $data) use ($table){
                        $data = trim($data, "\r\n");
                        $data = json_decode($data,true);
                        if($data["cmd"] != 6) {
                            echo "tcp_cli Receive: $data \r\n";
                        }
                    });
                    $client->on("error", function(swoole_client $cli){
                        //echo "tcp_cli error\n";
                    });
                    $client->on("close", function(swoole_client $cli){
                        //echo "tcp_cli Connection close\n";
                    });
                    $client->connect($request->server['remote_addr'], 5555);
                }
            } else if($status['type']== 2){
                if(($table->get($id,'index') != $status['index'])||($table->get($id,'freq') != $status['freq'])){
                    $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
                    $client->on("connect", function(swoole_client $cli) use ($table,$id) {
                        $cli->send("{\"cmd\":7,\"index\":".$table->get($id,'index').",\"freq\":".$table->get($id,'freq')."}\r\n");    
                    });
                    $client->on("receive", function(swoole_client $cli, $data) use ($table){
                        $data = trim($data, "\r\n");
                        $data = json_decode($data,true);
                        if($data["cmd"] != 7) {
                            echo "tcp_cli Receive: $data \r\n";
                            var_dump($data);
                        }
                    });
                    $client->on("error", function(swoole_client $cli){
                        //echo "tcp_cli error\n";
                    });
                    $client->on("close", function(swoole_client $cli){
                        //echo "tcp_cli Connection close\n";
                    });
                    $client->connect($request->server['remote_addr'], 5555);
                }
            }
            //$table->set($id, ['r' => , 'g' => $status['g'], 'b' => $status['b'], 'w' => $status['w'],'m' => $status['m']]);
        } else if(strcmp($cmd,"ext_stat") == 0){
            $table->set($id, ['remote' => $status['remote'],'repeater' => $status['repeater'],'year' => $status['year'],
                'mon' => $status['mon'],'day' => $status['day'],'hour' => $status['hour'],'min' => $status['min'],
                'sec' => $status['sec'],'type' => $status['type']]);
        }
        $response->header("Content-Type", "text/html");
        $response->end("ok");
    }
});

$serv->on('connect', function ($serv, $fd){  
    //echo "TCP_SRV Client: Connect.\n";
});

$serv->on('receive', function ($serv, $fd, $from_id, $data) use ($table,$db) {
    $data = trim($data, "\r\n");
    //echo "TCP_SRV " . $data ."\r\n";
    $dataparts = explode("&", $data);
    $cmd = explode("=", $dataparts[0]);
    $id = explode("=", $dataparts[1]);
    $key = explode("=", $dataparts[2]);
    $id = $id[1];
    $key = $key[1];
    $cmd = $cmd[1];
    if(strcmp($cmd,"subscribe") == 0){
        //$db->exec('CREATE TABLE  (id INTEGER, dev_id STRING, dev_name STRING, dev_key STRING)');
        $stmt = $db->prepare('SELECT * FROM devices WHERE dev_id=:id');
        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();
        if(!$result->fetchArray()){
            $db->exec("INSERT INTO devices (id, dev_id, dev_name, dev_key,dev_mode,R,G,B,W,M,_index,freq) VALUES ('', '" . $id ."','" . "Not_Set" . "','" . $key . "','1','5000','0','0','0','0','1','1000')");
        }
        $result = $stmt->execute();
        $result = $result->fetchArray();
        $cn = $serv->connection_info($fd);
        $ip=$cn['remote_ip'];
        $name=$result['dev_name'];
        $myindex=$result['_index'];
        $myfreq=$result['freq'];
        $table->set($id, ['dev_key' => "$key", 'dev_name' => "$name", 'lastbeat' => time(), 'r'=> $result['r'], 'g' =>$result['g'], 'b'=>$result['b'],'w'=>$result['w'],'ip'=> "$ip",'index'=> "$myindex",'freq'=> "$myfreq" ]);
    } else if(strcmp($cmd,"keep") == 0){
        $table->set($id, ['lastbeat' => time()]);
    }
    $serv->send($fd, $dataparts[0] . "&res=1\r\n");
});

$serv->on('close', function ($serv, $fd) {
   // echo "TCP_SRV Client:" . $fd . "Close.\n";
});

$http->start();
function getStaticFile(
    swoole_http_request $request,
    swoole_http_response $response,
    array $static
) : bool {
    $staticFile = __DIR__ . $request->server['request_uri'];
    if (! file_exists($staticFile)) {
        return false;
    }
    $type = pathinfo($staticFile, PATHINFO_EXTENSION);
    if (! isset($static[$type])) {
        return false;
    }
    $response->header('Content-Type', $static[$type]);
    $response->sendfile($staticFile);
    return true;
}
function Reverse_CIE_Lookup($cienumber, array $lookuptable) {
    for($x=0;$x<count($lookuptable);$x++) {
        if($lookuptable[$x] == $cienumber){
            return $x;
        }
    }
    return 0;
}
