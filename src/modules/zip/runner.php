<?php
namespace Modules\zip;

require_once "../lib/extern/pdf-to-text-2017-05-31/PdfToText.phpclass";

function runZip($pathIn,$pathOut,$fnameOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("zip -r -j -1 ".escapeshellarg($pathOut."/".$fnameOut)." ".escapeshellarg($pathIn));
   
}

function runUnzip($fnameIn,$pathOut,$settings,$corpus,$taskDesc){
    
    @mkdir($pathOut);    
    @chown($pathOut,$settings->get("owner_user"));
    @chgrp($pathOut,$settings->get("owner_group"));
    
	$tempOut=tempnam(".","zip");
	@unlink($tempOut);
	@mkdir($tempOut);
    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($tempOut));
    passthru("chown -R ".$settings->get("owner_user").":".$settings->get("owner_group")." ".escapeshellarg($tempOut));
   
    $dir_meta=$corpus->getFolderPath();
    $dir_meta.="/meta";
    @mkdir($dir_meta);
    @chown($dir_meta,$settings->get("owner_user"));
    @chgrp($dir_meta,$settings->get("owner_group"));
    
    $dir_standoff=$corpus->getFolderPath();
    $dir_standoff.="/standoff";
    @mkdir($dir_standoff);
    @chown($dir_standoff,$settings->get("owner_user"));
    @chgrp($dir_standoff,$settings->get("owner_group"));    

    $dir_annotated=$corpus->getFolderPath();
    $dir_annotated.="/".$settings->get("dir.annotated");
    @mkdir($dir_annotated);
    @chown($dir_annotated,$settings->get("owner_user"));
    @chgrp($dir_annotated,$settings->get("owner_group"));    

    $dir_audio=$corpus->getFolderPath();
    $dir_audio.="/audio";
    @mkdir($dir_audio);
    @chown($dir_audio,$settings->get("owner_user"));
    @chgrp($dir_audio,$settings->get("owner_group"));    

    $dir_files=$corpus->getFolderPath();
    $dir_files.="/files";
    @mkdir($dir_files);
    @chown($dir_files,$settings->get("owner_user"));
    @chgrp($dir_files,$settings->get("owner_group"));    
	
	$pos=strrpos($fnameIn,"/");
	$archiveName=$fnameIn;
	if($pos!==false)$archiveName=substr($fnameIn,$pos+1);

    $dh = opendir($tempOut);
	if($dh!==false){
    while (($file = readdir($dh)) !== false) {
        $pathFile=$tempOut."/".$file;
        if(!is_file($pathFile))continue;
        
        $pathMeta=$dir_meta."/".$file;
        $pathStandoff=$dir_standoff."/".$file;
        $pathAnnotated=$dir_annotated."/".$file;
        $pathAudio=$dir_audio."/".$file;
		$pathTxt=$dir_files."/".$file;

        $pathStandoffMetadata=$dir_standoff."/".changeFileExtension($file,"xml");
        
        if(endsWith(strtolower($file),".txt")){
            @rename($pathFile,$pathTxt);
            @chown($pathTxt,$settings->get("owner_user"));
            @chgrp($pathTxt,$settings->get("owner_group"));
            if(!is_file($pathMeta)){
                $fpathMeta=$dir_meta."/".$file.".meta";
                file_put_contents($fpathMeta,json_encode([
                    'name' => $file,
                    'corpus' => $corpus->getData("name","unknown"),
                    'type' => 'text',
                    'desc' => "$archiveName",
                    'created_by' => $taskDesc['created_by'],
                    'created_date' => $taskDesc['created_date']
                ]));
                @chown($fpathMeta,$settings->get("owner_user"));
                @chgrp($fpathMeta,$settings->get("owner_group"));
            }
            
			$corpus->createStandoffMetadata($taskDesc,$pathStandoffMetadata);
			@chown($pathStandoffMetadata,$settings->get("owner_user"));
			@chgrp($pathStandoffMetadata,$settings->get("owner_group"));
            
        }else if(endsWith(strtolower($file),".conllu") || endsWith(strtolower($file),".conllup")){
            @rename($pathFile,$pathAnnotated);
            @chown($pathAnnotated,$settings->get("owner_user"));
            @chgrp($pathAnnotated,$settings->get("owner_group"));
            
        }else if(endsWith(strtolower($file),".wav")){
            @rename($pathFile,$pathAudio);
            @chown($pathAudio,$settings->get("owner_user"));
            @chgrp($pathAudio,$settings->get("owner_group"));

        }else if(endsWith(strtolower($file),".pdf")){
            @rename($pathFile,$pathStandoff);
            @chown($fpathStandoff,$settings->get("owner_user"));
            @chgrp($fpathStandoff,$settings->get("owner_group"));
            
            // RUN PDF TO TEXT
			echo RELATE_pdf2text($pathStandoff, $pathTxt);
            
            // WRITE META
            if(!is_file($pathMeta)){
                $fpathMeta=$dir_meta."/".changeFileExtension($file,"txt").".meta";
                file_put_contents($fpathMeta,json_encode([
                    'name' => changeFileExtension($file,"txt"),
                    'corpus' => $corpus->getData("name","unknown"),
                    'type' => 'text',
                    'desc' => "$archiveName",
                    'created_by' => $taskDesc['created_by'],
                    'created_date' => $taskDesc['created_date']
                ]));
                @chown($fpathMeta,$settings->get("owner_user"));
                @chgrp($fpathMeta,$settings->get("owner_group"));
            }
			
			$corpus->createStandoffMetadata($taskDesc,$pathStandoffMetadata);
			@chown($pathStandoffMetadata,$settings->get("owner_user"));
			@chgrp($pathStandoffMetadata,$settings->get("owner_group"));
			
        }else{
            @rename($pathFile,$pathStandoff);
            @chown($fpathStandoff,$settings->get("owner_user"));
            @chgrp($fpathStandoff,$settings->get("owner_group"));
        }
    }
    closedir($dh);
	} // if dh!==false
	else{
		echo "Cannot open tempOut [$tempOut] directory\n";
	}
	rmdir($tempOut);
        
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_audio.json",json_encode(["changed"=>time()]));            

    @chown($corpus->getFolderPath()."/changed_files.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_files.json",$settings->get("owner_group"));
        
    @chown($corpus->getFolderPath()."/changed_standoff.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_standoff.json",$settings->get("owner_group"));

    @chown($corpus->getFolderPath()."/changed_annotated.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_annotated.json",$settings->get("owner_group"));

    @chown($corpus->getFolderPath()."/changed_audio.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_audio.json",$settings->get("owner_group"));
}

function runUnzipAnnotated($fnameIn,$pathOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    
    @chown($pathOut,$settings->get("owner_user"));
    @chgrp($pathOut,$settings->get("owner_group"));

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
    passthru("chown -R ".$settings->get("owner_user").":".$settings->get("owner_group")." ".escapeshellarg($pathOut));
   
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
    @chown($corpus->getFolderPath()."/changed_annotated.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_annotated.json",$settings->get("owner_group"));
}

function runnerZipText($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/files",$corpus->getFolderPath()."/zip_text",$taskDesc['fname']);
}

function runnerZipAnnotated($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    runZip($corpus->getFolderPath()."/".$settings->get("dir.annotated"),$corpus->getFolderPath()."/zip_".$settings->get("dir.annotated"),$taskDesc['fname']);
}

function runnerUnzipText($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
       runUnzip($data['fpath'],$corpus->getFolderPath()."/files",$settings,$corpus,$taskDesc);
}

function runnerUnzipAnnotated($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
       runUnzip($data['fpath'],$corpus->getFolderPath()."/".$settings->get("dir.annotated"),$settings,$corpus,$taskDesc);
}

function runnerZipStandoff($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/standoff",$corpus->getFolderPath()."/zip_standoff",$taskDesc['fname']);
}

function runnerZipGoldStandoff($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/gold_standoff",$corpus->getFolderPath()."/zip_gold_standoff",$taskDesc['fname']);
}

function runnerZipGoldAnn($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/gold_ann",$corpus->getFolderPath()."/zip_gold_ann",$taskDesc['fname']);
}

function runnerZipAudio($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/audio",$corpus->getFolderPath()."/zip_audio",$taskDesc['fname']);
}

?>