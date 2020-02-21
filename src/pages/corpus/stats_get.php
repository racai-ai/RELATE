<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$stats=[];
foreach($corpus->getStatistics() as $k=>$v){
    if(strcasecmp($k,"Entropy.Romanian letters")!==0)
        $stats[]=[$k,$v];
}

echo json_encode($stats);

?>