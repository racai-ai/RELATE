<?php

namespace Modules\tei;

function scheduleTEI2Text($settings,$corpus,$task_name,$tdata){
    scheduleFolder($corpus,"standoff",$task_name,'tei',["xml","teixml"]);
}

function scheduleCONLLUP2TEI($settings,$corpus,$task_name,$tdata){
    scheduleFolder($corpus,"standoff",$task_name,'tei',["xml","teixml"]);
}


?>