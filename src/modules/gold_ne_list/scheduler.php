<?php

namespace Modules\goldnelist;

function schedule($settings,$corpus,$task_name,$tdata){
   scheduleFolder($corpus,"gold_standoff",$task_name,"conllu",".ann");
}

?>