<?php

namespace Modules\anonymization;

function schedule($settings,$corpus,$task_name,$tdata){
   scheduleFolder($corpus,"files",$task_name,"txt");
}

?>