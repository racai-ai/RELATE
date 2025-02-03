<?php

if(!isset($_REQUEST['info']))die("Invalid call");

$info=$_REQUEST['info'];

if($info=="phpinfo"){
    phpinfo();
}else if($info=="diskinfo"){
    passthru("df -h 2>&1");
    echo "\n\n/proc/mounts\n";
    passthru("cat /proc/mounts 2>&1");
    echo "\n\n/proc/partitions\n";
    passthru("cat /proc/partitions 2>&1");
    
}else if($info=="cpuinfo"){
    echo "/proc/cpuinfo\n";
    passthru("cat /proc/cpuinfo 2>&1");
    echo "\n\n/proc/loadavg\n";
    passthru("cat /proc/loadavg 2>&1");
    echo "\n\n/proc/stat\n";
    passthru("cat /proc/stat 2>&1");
    
}else if($info=="meminfo"){
    passthru("cat /proc/meminfo 2>&1");
    
}else if($info=="devinfo"){
    echo "/proc/devices\n";
    passthru("cat /proc/devices 2>&1");
    echo "\n\nlspci\n";
    passthru("lspci 2>&1");

}else if($info=="netinfo"){
    echo "netstat -r\n";
    passthru("netstat -r 2>&1");
    echo "\n\nnetstat -s -e\n";
    passthru("netstat -s -e 2>&1");
    echo "\n\nnetstat -a -n\n";
    passthru("netstat -a -n 2>&1");
    echo "\n\ifconfig\n";
    passthru("ifconfig 2>&1");
    echo "\n\nipconfig /all\n";
    passthru("ipconfig /all 2>&1");
    echo "\n\nip addr show\n";
    passthru("ip addr show 2>&1");


}else die("Invalid call");

