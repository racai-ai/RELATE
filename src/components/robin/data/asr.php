<?php

if(!isset($_FILES['asrfile']))
  die("Invalid usage");

$fname=$_FILES['asrfile']['tmp_name'];
$d=WAV_getDuration($fname);
if($d===false || $d>5*60){
    echo json_encode(["asr"=>"","error"=>true,"success"=>""]);
    die();
}

$fileContent = file_get_contents($fname);
$result=ROBIN_runASR($fileContent);
//echo json_encode(["asr"=>$result['data'],"error"=>false,"success"=>$result['success']]);

// Apply correction
$text="";
$comments=[];
if(isset($result['transcription'])){
		$text=$result['transcription'];
		if(strlen($text)>0){
				$json=file_get_contents("http://127.0.0.1/ws/cratima/asr_cratima.php?text=".urlencode($text));
				$json=json_decode($json,true);
				$text=$json['text'];
				if(isset($json['comments']))$comments=$json['comments'];
		}
}

echo json_encode(["asr"=>$text,"error"=>false,"success"=>$result['status'],"comments"=>$comments]);

?>