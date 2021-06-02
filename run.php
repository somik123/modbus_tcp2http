<?php


$host_end = intval($_REQUEST['host']);
if($host_end>0 && $host_end <100){
    $port = 502;
    $host = "192.168.1.".$host_end;
    passthru("python3 read_meter.py {$host} {$port}");
    //$retval = shell_exec("python3 read_meter.py 192.168.1.12 13007");
    //$newarr = json_decode($retval,1);
    //echo "<pre>";
    //print_r($newarr);
}
else
    echo "EOF";
?>
