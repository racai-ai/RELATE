<?php

$analysisText=[
"TYPE"=>"fake",
"SCORE"=>0.001,
"ALG.1.SCORE"=>0.001,
"ALG.2.SCORE"=>0.001
];

$analysisImage=[
"TYPE"=>"fake",
"SCORE"=>0.001,
"ALG.1.SCORE"=>0.001,
"ALG.1.DETAIL"=>"Image manipulation detected",
"ALG.1.MASK"=>"components/single_deepnewsdef/img/figure1_masked.jpg",
"ALG.2.SCORE"=>0.001,
"ALG.2.DETAIL"=>"Random details",
"ALG.3.SCORE"=>0.001,
"ALG.3.DETAIL"=>"Random details",

];

$displayAnalysisText=false;
$displayAnalysisImage=false;

function isImageUploaded($name) {
    if(empty($_FILES) || !isset($_FILES[$name])) {
        return false;       
    } 
    $file = $_FILES[$name];
    if(!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])){
        return false;
    }   
    return true;
}

function PageInit(){
    global $analysisText, $analysisImage,$displayAnalysisText,$displayAnalysisImage;

    if(isset($_REQUEST['text']) && strlen($_REQUEST['text'])>5){
        $displayAnalysisText=true;
    }

    if(isImageUploaded("image")){
        $displayAnalysisImage=true;
    }

}


function getPageContent(){
    global $analysisText, $analysisImage,$displayAnalysisText,$displayAnalysisImage;
    
    $html=file_get_contents(realpath(dirname(__FILE__))."/demo.html");
    
    foreach($analysisText as $k=>$v){
        $html=str_replace("{{TEXT_$k}}",$v,$html);
    }
    foreach($analysisImage as $k=>$v){
        $html=str_replace("{{IMAGE_$k}}",$v,$html);
    }
    
    $html=str_replace("{{DISPLAY_ANALYSIS_TEXT}}",$displayAnalysisText?("block"):("none"),$html);
    $html=str_replace("{{DISPLAY_ANALYSIS_IMAGE}}",$displayAnalysisImage?("block"):("none"),$html);
    
    
    return $html;
}

function getPageCSS(){
	return "";
}

function getPageJS(){
	$js=file_get_contents(realpath(dirname(__FILE__))."/demo.js");
    return $js;
}

function getPageAdditionalJS(){
    return [
		"extern/viewerjs-1.11.2/dist/viewer.min.js",
	];
}
function getPageAdditionalCSS(){
    return [
		"extern/viewerjs-1.11.2/dist/viewer.min.css",
	];
}

?>