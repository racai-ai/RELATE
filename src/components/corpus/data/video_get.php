<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("read"))die("Invalid corpus");

echo json_encode($corpus->getVideo());

?>