<?php

if(!isset($_FILES['asrfile']) || !isset($_REQUEST['input']) || !isset($_REQUEST['sysid']))
  die("Invalid usage");

$fname=$_FILES['asrfile']['tmp_name'];
$d=WAV_getDuration($fname);
if($d===false || $d>5*60){
    echo json_encode(["asr"=>"","translated"=>"","error"=>true,"success"=>""]);
    die();
}

$text="";
$comments="";
$status="";
$translated="";

$fileContent = file_get_contents($fname);
$input=$_REQUEST['input'];
if($input=="ro"){
		$result=ROBIN_runASR($fileContent);
		
		// Apply correction
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
		
		$status=$result['status'];
		
		$translated=TILDE_Translate($_REQUEST['sysid'],$text);
		$translated=trim($translated,"\"");
}else{
		// FOR ENGLISH
}
//echo json_encode(["asr"=>$result['data'],"error"=>false,"success"=>$result['success']]);


echo json_encode(["asr"=>$text,"translated"=>$translated,"error"=>false,"success"=>$status,"comments"=>$comments]);

?>