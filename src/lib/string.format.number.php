<?php

function formatNumberAsString($num){

    $s="$num";
    $l=strlen($s);
    if($l>6){
        $s1=substr($s,0,$l-6);
        $s2=substr($s,$l-6,1);
        $s=$s1.".".$s2." M";
    }else if($l>3){
        $s1=substr($s,0,$l-3);
        $s2=substr($s,$l-3,1);
        $s=$s1.".".$s2." K";
    }

    return $s;
}

function getTimeStrFromMS($tstr){
    $t=floatval(str_replace(",",".","$tstr"));
    $total_sec=intval($t/1000);
    $ms=intval($t-$total_sec*1000);
    $s=$total_sec%60;
    $total_m=intval($total_sec/60);
    $m=$total_m%60;
    $h=intval($total_m/60);
    return sprintf("%02d:%02d:%02d.%03d",$h,$m,$s,$ms);
}


/*echo formatNumberAsString("0")."\n";
echo formatNumberAsString("10")."\n";
echo formatNumberAsString("100")."\n";
echo formatNumberAsString("1000")."\n";
echo formatNumberAsString("10000")."\n";
echo formatNumberAsString("100000")."\n";
echo formatNumberAsString("1000000")."\n";
echo formatNumberAsString("10000000")."\n";
*/