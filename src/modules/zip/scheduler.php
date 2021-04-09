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

function scheduleUnzipAnnotated($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_".$settings->get("dir.annotated")."/".$tdata['fname'],$task_name,'zip');
}

function scheduleZipStandoff($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_standoff/".$tdata['fname'],$task_name,'zip');
}

function scheduleZipGoldStandoff($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_gold_standoff/".$tdata['fname'],$task_name,'zip');
}

function scheduleZipGoldAnn($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_gold_ann/".$tdata['fname'],$task_name,'zip');
}

function scheduleZipAudio($settings,$corpus,$task_name,$tdata){
    scheduleFile($corpus,"zip_audio/".$tdata['fname'],$task_name,'zip');
}

?>