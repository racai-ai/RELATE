<?php

namespace Modules\ner_old;

function schedule($settings,$corpus,$task_name,$tdata){
   scheduleFolder($corpus,$settings->get("dir.annotated"),$task_name,"conllu");
}

?>