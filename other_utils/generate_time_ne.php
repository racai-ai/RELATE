<?php

$fnameOut="ne.10.gazetteer";

$mdays=[
"ianuarie" => 31,
"februarie" =>29,
"martie" => 31,
"aprilie" => 30,
"mai" => 31,
"iunie" => 30,
"iulie" => 31,
"august" => 31,
"septembrie" => 30,
"octombrie" => 31,
"noiembrie" => 30,
"decembrie" => 31
];

$add=["întâi"];

$fout=fopen($fnameOut,"w");
foreach($mdays as $month => $days){
    $mupper=strtoupper($month);
    fwrite($fout,"TIME $month\n");
    fwrite($fout,"TIME $mupper\n");
    
    for($i=1;$i<=$days;$i++){
        fwrite($fout,"TIME $i $month\n");
        fwrite($fout,"TIME $i $mupper\n");
    }
    
    foreach($add as $a){
        fwrite($fout,"TIME $a $month\n");
        fwrite($fout,"TIME $a $mupper\n");
    }
}

fclose($fout);
?>