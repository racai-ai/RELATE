<?php

function runASR($fileContent,$type){

		if($type=="robin_asr_ro"){
				$r=ROBIN_runASR($fileContent);
				return ["text"=>$r['transcription'],"status"=>$r['status'],"comments"=>"","translated"=>"","tts"=>""];
		}else if($type=="robin_asrdev_ro"){
				$r=ROBIN_runASR($fileContent,true);
				return ["text"=>$r['transcription'],"status"=>$r['status'],"comments"=>"","translated"=>"","tts"=>""];
		}else if($type=="en_deepspeech2"){
				$r=ROBIN_runASR($fileContent,false,true);
				return ["text"=>$r['transcription'],"status"=>$r['status'],"comments"=>"","translated"=>"","tts"=>""];
		}

		return ["text"=>"","status"=>"ERROR: Unknown ASR module","comments"=>"Unknown ASR module type[$type]","translated"=>"","tts"=>""];

}

function runCorrection($data,$type){

		if($type=="correction_1"){
				$text=$data['text'];
				if(strlen($text)>0){
						$json=file_get_contents("http://127.0.0.1/ws/cratima/asr_cratima.php?text=".urlencode($text));
						$json=json_decode($json,true);
						$data['text']=$json['text'];
						if(isset($json['comments']))$data['comments'].=$json['comments'];
				}

		}
		
		return $data;

}

function runMT($data,$type){

		if($type=="ro_presidency"){
				if(strlen($data['text']>0){
						$translated=TILDE_Translate($_REQUEST['sysid'],$data['text']);
						$data['translated']=trim($translated,"\"");
				}		
		}
		
		return $data;
}

function runTTS($data,$type){

		if($type=="romanian_tts"){
				$data['tts']="index.php?path=sttsws&lang=ro&text=".urlencode($data['translated']);
	  }else if($type=="en_tts"){
				$data['tts']="index.php?path=sttsws&lang=en&text=".urlencode($data['translated']);
		}
		return $data;

}

if(!isset($_FILES['asrfile']) || !isset($_REQUEST['input']) || !isset($_REQUEST['sysid']) || 
	!isset($_REQUEST['system_asr']) || !isset($_REQUEST['system_correction']) || 
	!isset($_REQUEST['system_mt']) || !isset($_REQUEST['system_tts'])) {
  		die("Invalid usage");
  }

$fname=$_FILES['asrfile']['tmp_name'];
$d=WAV_getDuration($fname);
if($d===false || $d>5*60){
    echo json_encode(["asr"=>"","translated"=>"","error"=>true,"success"=>""]);
    die();
}

$fileContent = file_get_contents($fname);
$input=$_REQUEST['input'];

$data=runASR($fileContent,$_REQUEST['system_asr']);
$data=runCorrection($data,$_REQUEST['system_correction']);
$data=runMT($data,$_REQUEST['system_mt']);
$data=runTTS($data,$_REQUEST['system_tts']);


echo json_encode(["asr"=>$text,"translated"=>$translated,"error"=>false,"success"=>$status,"comments"=>$comments,"tts"=>$data['tts']]);

?>