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

/*echo formatNumberAsString("0")."\n";
echo formatNumberAsString("10")."\n";
echo formatNumberAsString("100")."\n";
echo formatNumberAsString("1000")."\n";
echo formatNumberAsString("10000")."\n";
echo formatNumberAsString("100000")."\n";
echo formatNumberAsString("1000000")."\n";
echo formatNumberAsString("10000000")."\n";
*/