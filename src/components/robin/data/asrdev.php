<?php

if(!isset($_FILES['asrfile']))
  die("Invalid usage");

$fname=$_FILES['asrfile']['tmp_name'];
$d=WAV_getDuration($fname);
if($d===false || $d>5*60){
    echo json_encode(["asr"=>"","error"=>true,"success"=>"","message"=>"Maximum file time 5 minutes"]);
    die();
}

$fileContent = file_get_contents($fname);
$result=ROBIN_runASR($fileContent,true);
if(isset($result['transcription'])){
    die(json_encode(["asr"=>$result['transcription'],"error"=>false,"success"=>$result['status']]));
}else{

    $trans="";
    foreach($result['transcriptions'] as $t){
        if(strlen($trans)>0)$trans.=" ";
        $trans.=$t['transcription'];
    }

    die(json_encode(["asr"=>$trans,"error"=>false,"success"=>$result['status']]));

}

?>