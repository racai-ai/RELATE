<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$stats=[];
foreach($corpus->getStatistics() as $k=>$v){
    $stats[]=[$k,$v];
}

echo json_encode($stats);

?>