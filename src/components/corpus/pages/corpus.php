<?php

function getMetadataNom($meta,$field,$data){
	$nom=$field['nom'];
	$level=0;
	if(isset($field["level"]))$level=$field['level'];
	
	$parentValue="";
	if($level>0){
		$parentValue=$data[$field["parent"]];
	}
	
	$ret=[];
	for($i=0;$i<count($meta["nomenclature"][$nom]);$i++){
		$current=explode("|",$meta["nomenclature"][$nom][$i]);
		if($level==0 || $current[$level-1]==$parentValue)
			$ret[$current[$level]]=true;
	}
	
	return array_keys($ret);
}

$autocomplete_ids=[];
$autocomplete_nom=[];

function getMetadataUploadHTML($corpus){
	global $autocomplete_ids,$autocomplete_nom;
	
    $meta=$corpus->getMetadataProfile();
    $ret="";
	$dataDef=[];
	
    if(is_array($meta) && isset($meta["fields"])){
		foreach($meta["fields"] as $f){$dataDef[$f['field']]=$f['default'];}
		
        foreach($meta["fields"] as $f){
            if($f["onupload"]){
                $ret.="<tr><td>".htmlspecialchars($f['name'])."</td><td>";
                
                if($f['type']=="text"){
                    $ret.="<input type=\"text\" name=\"${f['field']}\" value=\"${f['default']}\" size=\"40\"/>";
				}else if($f['type']=="dropdown"){
                    $ret.="<select name=\"${f['field']}\" onchange=\"metadataDropdownChanged(this);\">";
					$nom=getMetadataNom($meta,$f,$dataDef);
					foreach($nom as $nv){
						$ret.="<option ".(($nv==$f['default'])?("selected=\"selected\""):("")).' value="'.htmlspecialchars($nv).'">'.htmlspecialchars($nv)."</option>";
					}
					$ret.="</select>";
				}else if($f['type']=="autocomplete"){
					$id='metadataUploadForm_'.$f['field'];
					$autocomplete_ids[]=$id;
					$autocomplete_nom[]=getMetadataNom($meta,$f,$dataDef);
					$ret.='<div class="autocomplete-search-container">';
					$ret.='<input type="text" name="'.$f['field'].'" id="'.$id.'" value="'.htmlspecialchars($f['default']).'"/>';
					$ret.='<div class="autocomplete-suggestions">';
					$ret.='<ul></ul>';
					$ret.='</div>';
					$ret.='</div>';
					$ret.='</td><td>'.htmlspecialchars($f["description"])."</td></tr>";
                }else{ $ret.="UNKNOWN[".$f['type']."]";}
                
                $ret.="</td><td>".
                    ((empty($f['description']))?("&nbsp;"):(htmlspecialchars($f['description']))).
                    "</td></tr>\n";
            }
        }
    }
    return $ret;
}

function corpus_generateClassificationHtml($classProfile, $base){
        $classHtml="";
        $classHtml.='<div style="border:1px solid black; margin-top:10px; padding-top:5px" id="'.$base.'_classification_div">';
        $classHtml.='<form id="'.$base.'_classification_form" onsubmit="return false;">';
        foreach($classProfile as $cp){
            $classHtml.='<label for="'.$base.'_classification_'.$cp['variable'].'">'.$cp['message']."</label>";
            $classHtml.='<select name="'.$base.'_classification_'.$cp['variable'].'" id="'.$base.'_classification_'.$cp['variable'].'">';
            foreach($cp['values'] as $v){
                $classHtml.='<option value="'.$v.'">'.$v."</option>";
            }
            $classHtml.='</select>';
        }
        
        $classHtml.='<button type="button" class="btn cur-p btn-secondary" id="'.$base.'_classification_save" onclick="'.$base.'_saveFileClassification();">Save</button>';
        $classHtml.='</form>';        
        $classHtml.="</div>";
        return $classHtml;
}

function corpus_generateCorrectedHtml($base){
        $classHtml="";
        $classHtml.='<div style="border:1px solid black; margin-top:10px; padding-top:5px" id="'.$base.'_corrected_div">';
        $classHtml.="</div>";
        return $classHtml;
}

function getPageContent(){
		global $user,$modules;
		
    if(!isset($_REQUEST['name']))return "";

    $corpora=new Corpora();
    $corpus=new Corpus($corpora,$_REQUEST['name']);
    if(!$corpus->loadData())die("Invalid corpus");

    $html=file_get_contents(realpath(dirname(__FILE__))."/corpus.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/corpus_common_loading.html");
    
    $hideaudiobutton=""; if(!$corpus->hasAudio())$hideaudiobutton="display:none";
    $hidegoldbutton=""; if(!$corpus->hasGoldAnnotations())$hidegoldbutton="display:none";
    $hidebratbutton=""; if(!$corpus->hasBratProfiles())$hidebratbutton="display:none";
    $classHtmlFileViewer=""; 
    $classHtmlBrat=""; 
    if($corpus->hasClassificationProfiles()){
      $fname=$corpus->getFolderPath()."/standoff/classification_profile.json";
      if(is_file($fname)){
        $classProfile=json_decode(file_get_contents($fname),true);
        /*
          [
            {"message":"", "variable":"", "values":["","",""]}
          ]
        */
        $classHtmlFileViewer=corpus_generateClassificationHtml($classProfile,"fileViewerText");
        $classHtmlBrat=corpus_generateClassificationHtml($classProfile,"fileViewerBrat");
      }      
    }

    $correctedHtmlBrat="";
    if($corpus->hasCorrectedText()){
        $correctedHtmlBrat=corpus_generateCorrectedHtml("fileViewerBrat");
    }

    
    $modules_task_dialog=$modules->getTaskDialog($corpus);
    $html=str_replace("{{TASK-DIALOG}}",$modules_task_dialog,$html);
    
    $html=str_replace("{{CORPUS_NAME_HTML}}",htmlspecialchars($_REQUEST['name']),$html);
    $html=str_replace("{{CORPUS_NAME}}",$_REQUEST['name'],$html);
    $html=str_replace("{{CORPUS_LANG}}",$corpus->getData("lang",""),$html);
    $html=str_replace("{{RECORDER_NAME}}",$user->getProfileHTML("recorder_name",""),$html);
    $html=str_replace("{{LOADING}}",$loading,$html);
    $html=str_replace("{{METADATA_UPLOAD}}",getMetadataUploadHTML($corpus),$html);
    $html=str_replace("{{hideaudiobutton}}",$hideaudiobutton,$html);
    $html=str_replace("{{hidegoldbutton}}",$hidegoldbutton,$html);
    $html=str_replace("{{hidebratbutton}}",$hidebratbutton,$html);
    $html=str_replace("{{classification_html_fileviewertext}}",$classHtmlFileViewer,$html);
    $html=str_replace("{{classification_html_fileviewerbrat}}",$classHtmlBrat,$html);
    $html=str_replace("{{corrected_html_fileviewerbrat}}",$correctedHtmlBrat,$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/corpus.css");
    return $css;
}

function getPageJS(){
		global $user,$modules;
		global $autocomplete_ids,$autocomplete_nom;

    $corpora=new Corpora();
    $corpus=new Corpus($corpora,$_REQUEST['name']);
    if(!$corpus->loadData())die("Invalid corpus");
    
    $hasAudio="false"; if($corpus->hasAudio())$hasAudio="true";
    $hasGold="false"; if($corpus->hasGoldAnnotations())$hasGold="true";
    $hasBrat="false"; if($corpus->hasBratProfiles())$hasBrat="true";
    $hasClassification=($corpus->hasClassificationProfiles())?("true"):("false");
    $hasCorrected=($corpus->hasCorrectedText())?("true"):("false");
    $hidebratbutton=""; if(!$corpus->hasBratProfiles())$hidebratbutton="display:none";
    
    $classificationProfile="[]";
    if($corpus->hasClassificationProfiles()){
      $fname=$corpus->getFolderPath()."/standoff/classification_profile.json";
      if(is_file($fname)){
        $classProfile=json_decode(file_get_contents($fname),true);
        $classificationProfile=json_encode($classProfile);
      }
    }
    
    $last_viewed_file=$user->getProfile("last_viewed_file_".$_REQUEST['name'],"");

    $meta=$corpus->getMetadataProfile();
	$metaJS="{}";
    if(is_array($meta) && isset($meta["fields"]))$metaJS=json_encode($meta);

    $js=file_get_contents(realpath(dirname(__FILE__))."/corpus.js");
    $js=str_replace("{{TASKS-BUTTONS}}",$modules->getTaskButtons($corpus),$js);
    $js=str_replace("{{TASKS-INIT}}",$modules->getTaskInit($corpus),$js);

    $js=str_replace("{{CORPUS_NAME}}",$_REQUEST['name'],$js);
    $js=str_replace("{{CORPUS_LANG}}",$corpus->getData("lang",""),$js);
    $js=str_replace("{{RECORDER_NAME}}",$user->getProfileJS("recorder_name",""),$js);
    $js=str_replace("{{HAS_AUDIO}}",$hasAudio,$js);
    $js=str_replace("{{HAS_GOLD}}",$hasGold,$js);
    $js=str_replace("{{HAS_BRAT}}",$hasBrat,$js);
    $js=str_replace("{{HAS_CLASSIFICATION}}",$hasClassification,$js);
    $js=str_replace("{{HAS_CORRECTED}}",$hasCorrected,$js);
    $js=str_replace("{{hidebratbutton}}",$hidebratbutton,$js);
    $js=str_replace("{{CLASSIFICATION_PROFILE}}",$classificationProfile,$js);
    $js=str_replace("{{LAST_VIEWED_FILE}}",$last_viewed_file,$js);
    $js=str_replace("{{METADATA_SPEC}}",$metaJS,$js);
    $js=str_replace("{{METADATA_UPLOAD_IDS}}",json_encode($autocomplete_ids),$js);
    $js=str_replace("{{METADATA_UPLOAD_NOM}}",json_encode($autocomplete_nom),$js);

    return $js;
}

function getPageAdditionalCSS(){
    return [
		"extern/pqgrid-2.4.1/pqgrid.min.css",
		"extern/autocomplete/autocomplete.css"
	];
}

function getPageAdditionalJS(){
    return [
		"extern/pqgrid-2.4.1/pqgrid.min.js",
		"extern/web_audio_recorder/WebAudioRecorder.min.js",
		"extern/autocomplete/autocomplete.js",
                "extern/jszip.min.js",
                "extern/docx-preview.js",
                "extern/jszip-utils.js",
	];
}

?>