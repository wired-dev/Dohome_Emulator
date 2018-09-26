# Dohome_Emulator
I've built a Simple emulator for this bulb your want to setup a hostname file with redirects for it's two host names to your server but otherwise appers to work. it's based upon php/Swoole.

Research Notes:
{"cmd":4}
{"res":0,"cmd":4,"ver":"3.9.8","dev_id":"840d8e4575a9_LED_DOIT","conn":1,"remote":1,"save_off_stat":1,"repeater":0,"portal":0}


command: {"cmd":6,"r":0,"g":0,"b":0,"w":0,"m":5000}// vaules of 0 - 5000 for each varible
							// unsure what m does at this time
returns: {"res":0,"cmd":6}				// seems to be 5000-w

                    //$cli->send("{\"cmd\":4}\r\n");
                    //$cli->send("{\"cmd\":6,\"r\":" . rand(100,5000) . ",\"g\":" . rand(100,5000) .",\"b\":" . rand(100,5000) . ",\"w\":" . rand(10,500) . ",\"m\":" . "200" . "}\r\n");
                    //$cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":0,\"m\":0}\r\n");
                    //if((date('H') >= 0)&&(date('H') <=8)){
                    //  $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":0,\"m\":0}\r\n");
                    //    } else if((date('H') >= 8)&&(date('H') <=18)){
                    //
                    //    } else if((date('H') >= 18)&&(date('H') <=23)){
                    //        $cli->send("{\"cmd\":6,\"r\":500,\"g\":0,\"b\":0,\"w\":4500,\"m\":0}\r\n");
                    //    }
                    //sleep(1);
                    //$cli->send("{\"cmd\":1}\r\n"); // list wifi networks
                    //$cli->send("{\"cmd\":0}\r\n"); // res=4;
                    //$cli->send("{\"cmd\":2}\r\n"); // res=0;
                    //$cli->send("{\"cmd\":3}\r\n"); // res=0;
                    //$cli->send("{\"cmd\":4}\r\n"); // system info
                    //$cli->send("{\"cmd\":5}\r\n"); // res=8;
                    //$cli->send("{\"cmd\":6,\"r\":" . rand(0,5000) . ",\"g\":" . "0" .",\"b\":0,\"w\":" . "50" . ",\"m\":" . "0" . "}\r\n");
                    //$cli->send("{\"cmd\":7,\"index\":9,\"freq\":180000}\r\n");//fade modes index is which one.
                    //$cli->send("{\"cmd\":8}\r\n"); // res=15;
                    //$cli->send("{\"cmd\":9}\r\n"); // date/time;
                    //$cli->send("{\"cmd\":10}\r\n"); // set date/time; {"cmd":10,"year":2018,"month":8,"day":29,"hour":21,"minute":17,"second":7}
                    //$cli->send("{\"cmd\":11}\r\n"); // res=29; set default state {"cmd":11,"type":3,"r":2000,"g":1000,"b":0,"w":5000,"m":5000}
                    //$cli->send("{\"cmd\":12,\"op\":1}\r\n"); // res=0, sets the save_off_stat flag.0/1; auto state saving
                    //$cli->send("{\"cmd\":13}\r\n"); // res=18
                    //$cli->send("{\"cmd\":14}\r\n"); // res=18;
                    //$cli->send("{\"cmd\":15}\r\n"); // res=33;
                    //$cli->send("{\"cmd\":16}\r\n"); // res=5;
                    //$cli->send("{\"cmd\":17}\r\n"); // res=45; auto off timer? {"cmd":17,"time":15,"ts":1}
                    //$cli->send("{\"cmd\":18}\r\n"); // res=0;
                    //$cli->send("{\"cmd\":19}\r\n"); // returns ip address;
                    //$cli->send("{\"cmd\":20}\r\n"); // version;
                    //$cli->send("{\"cmd\":21}\r\n"); // timers;
                    //$cli->send("{\"cmd\":22}\r\n"); // timers;
                    //$cli->send("{\"cmd\":23,\"ts\":}\r\n"); // res=45; delte timer?
                    //$cli->send("{\"cmd\":24}\r\n"); // type 3 color info feedback;
                    //$cli->send("{\"cmd\":25}\r\n"); // type 1 color info;
                    //$cli->send("{\"cmd\":26}\r\n"); // res=18;
                    //$cli->send("{\"cmd\":27}\r\n"); // res=27;
                    //$cli->send("{\"cmd\":28}\r\n"); // res=0;
                    //$cli->send("{\"cmd\":29,\"offset\":3}\r\n"); // res=43;??
                    //$cli->send("{\"cmd\":30,\"en\":0}\r\n"); // res=46; // enable/disable repeater function
                    //$cli->send("{\"cmd\":31}\r\n"); // res=46;
                    //$cli->send("{\"cmd\":32}\r\n"); // res=0;
                    //$cli->send("{\"cmd\":33}\r\n"); // res=47;
                    //$cli->send("{\"cmd\":34}\r\n"); // res=48;
                    //$cli->send("{\"cmd\":35}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":36}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":37}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":38}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":39}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":40}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":41}\r\n"); // res=26;
                    //$cli->send("{\"cmd\":7}\r\n"); // res=15; changed modes to rgb though
                    //$cli->send("{\"cmd\":6,\"r\":6000,\"g\":0,\"b\":0,\"w\":0000,\"m\":0}\r\n");//letting extra 0's hang in front breaks cmd
                    //$cli->send("{\"cmd\":6,\"r\":5000,\"g\":0,\"b\":0,\"w\":0,\"m\":0}\r\n");//breaks it


command: {"cmd":7,"index":1,"freq":250}\r\n
returns: {"res":0,"cmd":7}
// known modes, Freq is ms between changes (10-180000 unsure of upper limits).
// Only uses RGB LEDS not the white ones.
//01 colorful fade,    02 red fade,        03 green fade, 
//04 blue fade,        05 yellow fade,     06 cyan fade,
//07 purple fade,      08 white fade,      09 red strobe,
//10 green strobe,     11 blue strobe,     12 yellow strobe,
//13 red-green fade,   14 red-blue fade,   15 green-blue fade, 
//16 red-green jump,   17 red-blue jump,   18 green-blue jump,
//19 red-green strobe, 20 red-blue strobe, 21 green-blue strobe,
//22 colorful jump,    23 colorful strobe, 24 white strobe.
tcpdump host 10.10.0.214 || host 10.10.0.248 || host 11528.78.23 -w /mnt/sda1/bulblog.cap

{"cmd":4}
{"res":0,"cmd":4,"ver":"3.9.8","dev_id":"840d8e4575a9_LED_DOIT","conn":1,"remote":1,"save_off_stat":1,"repeater":0,"portal":0}
{"cmd":6,"r":0,"g":0,"b":0,"w":0,"m":5000}//
{"res":0,"cmd":6}
{"cmd":6,"r":0,"g":0,"b":0,"w":215,"m":4784}
{"res":0,"cmd":6}
{"cmd":6,"r":0,"g":0,"b":0,"w":549,"m":4450}
{"res":0,"cmd":6}
{"cmd":6,"r":0,"g":0,"b":0,"w":705,"m":4294}
{"res":0,"cmd":6}
{"cmd":6,"r":0,"g":0,"b":0,"w":803,"m":4196}
{"res":0,"cmd":6}
{"cmd":6,"r":0,"g":0,"b":0,"w":882,"m":4117}
{"res":0,"cmd":6}
{"cmd":6,"r":0,"g":2450,"b":5000,"w":0,"m":0}
{"res":0,"cmd":6}
{"cmd":7,"index":1,"freq":250} // sets fade modes index 1 full color fade, 2 red fade, 3 green fade.....
// known modes, Freq is ms between changes (10-180000 unsure of upper limits).
// Only uses RGB LEDS not the white ones.
//01 colorful fade,    02 red fade,        03 green fade, 
//04 blue fade,        05 yellow fade,     06 cyan fade,
//07 purple fade,      08 white fade,      09 red strobe,
//10 green strobe,     11 blue strobe,     12 yellow strobe,
//13 red-green fade,   14 red-blue fade,   15 green-blue fade, 
//16 red-green jump,   17 red-blue jump,   18 green-blue jump,
//19 red-green strobe, 20 red-blue strobe, 21 green-blue strobe,
//22 colorful jump,    23 colorful strobe, 24 white strobe.

GET /mobile_app/get_open_id.php?uid=dohome%40wired-one.com&password=plus15&type=android HTTP/1.1
Host: xinfeng.doit.am
Connection: Keep-Alive
Accept-Encoding: gzip
User-Agent: okhttp/3.10.0

HTTP/1.1 200 OK
Server: nginx/1.10.2
Date: Sun, 26 Aug 2018 06:07:14 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/5.6.28

63
{"ret":"1","open_id":"59f858d2f526903de235ad9faa4a8a71","token":"0ce1128cd931f4252dee6113fd7a2029"}
0

GET /mobile_app/device_list_led.php?open_id=59f858d2f526903de235ad9faa4a8a71&token=0ce1128cd931f4252dee6113fd7a2029 HTTP/1.1
Host: xinfeng.doit.am
Connection: Keep-Alive
Accept-Encoding: gzip
User-Agent: okhttp/3.10.0

HTTP/1.1 200 OK
Server: nginx/1.10.2
Date: Sun, 26 Aug 2018 06:07:15 GMT
Content-Type: application/json;charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/5.6.28

7
...null
0
¬_>³R&ò¡_JEyÚ%@-¦$sN

øP#>W=ÓpNUëî
ºùÑHTTP/1.1 200 OK
Server: nginx/1.10.2
Date: Sun, 26 Aug 2018 06:07:15 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/5.6.28

76
{"ver":"3.13.0","url1":"http://xinfeng.doit.am/upgrade/u1.bin","url2":"http://xinfeng.doit.am/upgrade/u2.bin"}
0


{"cmd":10,"year":2018,"month":8,"day":27,"hour":19,"minute":51,"second":45}
{"res":0,"cmd":10}
{"cmd":29,"offset":3}
{"res":0,"cmd":29}
{"cmd":9}
{"res":0,"cmd":9,"year":2018,"mon":8,"day":27,"hour":19,"min":51,"sec":45}
{"cmd":21}
{"res":0,"cmd":21,"timers":[]}
{"cmd":14,"ts":1535424730,"year":2018,"month":8,"day":27,"hour":20,"minute":0,"second":0,"r":0,"g":0,"b":0,"w":5000,"m":0,"type":1,"repeat":1}
{"res":0,"cmd":14}

{"cmd":11,"type":3,"r":2000,"g":1000,"b":0,"w":5000,"m":5000}
{"res":0,"cmd":11}


{"cmd":12,"op":1}
{"res":0,"cmd":12}
{"cmd":14,"ts":1535424789,"year":2018,"month":8,"day":27,"hour":19,"minute":55,"second":0,"r":0,"g":0,"b":0,"w":5000,"m":0,"type":1,"repeat":1}
{"res":0,"cmd":14}
{"cmd":17,"time":5,"ts":1}
{"res":0,"cmd":17}

{"cmd":21}
{"res":0,"cmd":21,"timers":[{"index":0,"ts":1535424730,"type":1,"repeat":1,"year":2018,"mon":8,"day":27,"hour":20,"min":0,"sec":0},{"index":1,"ts":1,"type":4,"repeat":0,"year":2018,"mon":8,"day":27,"hour":18,"min":57,"sec":55}]}
{"cmd":22}
{"res":0,"cmd":22,"timers":[{"index":1,"ts":1,"year":2018,"mon":8,"day":27,"hour":18,"min":57,"sec":55}]}
{"cmd":23,"ts":1535424730}
{"res":0,"cmd":23}




