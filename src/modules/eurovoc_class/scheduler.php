<?php

namespace Modules\eurovoc_class;

function schedule($settings,$corpus,$task_name,$tdata){
    scheduleFolder($corpus,$settings->get("dir.annotated"),$task_name,"conllu");
}

?>