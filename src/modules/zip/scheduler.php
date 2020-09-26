<?php

namespace Modules\zip;

function scheduleZipText($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_text/".$tdata['fname'],$task_name,'zip');
}

function scheduleZipAnnotated($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_".$settings->get("dir.annotated")."/".$tdata['fname'],$task_name,'zip');
}

function scheduleUnzipText($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_text/".$tdata['fname'],$task_name,'zip');
}

?>