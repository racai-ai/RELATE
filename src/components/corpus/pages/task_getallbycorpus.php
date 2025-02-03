<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("read"))die("Invalid corpus");

$tasks=new Task($corpus);

echo json_encode($tasks->getAllByCorpus());

?>