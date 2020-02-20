<?php

require "../lib/lib.php";
require "runner.iateeurovoc.php";

$text=file_get_contents("../DB/corpora/Marcell/basic_tagging_original/mj_00000G0JOVG1KOU61EB2VARAKEB4B6AG.txt");

//$data=IATE_EUROVOC_Annotate($text,1);

runnerIateEurovoc

var_dump($data);
