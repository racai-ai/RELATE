<?php

namespace Modules\export_marcell;

function schedule($settings,$corpus,$task_name,$tdata){
     scheduleFile($corpus,$settings->get("dir.annotated"),$task_name,"zip");
}

?>