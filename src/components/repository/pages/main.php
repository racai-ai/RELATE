<?php

function renderResource($res){
	$desc=$res->getData("description_short",$res->getData("description_long","No description"));
	if(strlen($desc)>1000)$desc=substr($desc,0,997)."...";
	
	$authors="";
	foreach($res->getData("authors",[]) as $aut){
		if(strlen($authors)>0)$authors.="; ";
		$authors.=$aut;
	}
	
	$link="repository/".$res->getName();
	
	$resTitle=$res->getData("title",$res->getName());
	if(strlen($res->getData("acronym",""))>0)$resTitle=$res->getData("acronym","")." - ".$resTitle;
	
	$resHtml='<div class="resource">'.
		'<div class="resourceTitle"><a class="resourceLink" href="'.$link.'">'.htmlentities($resTitle).'</a></div>';
	
	if(strlen($authors)>0)
		$resHtml.='<div class="resourceItem"><b>Author(s):</b></div><div class="resourceItemContent">'.htmlentities($authors).'</div>';
	
	$resHtml.=
		'<div class="resourceItem"><b>Description:</b></div><div class="resourceItemContent">'.htmlentities($desc).'</div>'.
		'<div class="resourceItem"><a href="'.$link.'">View resource</a></div>'.
		"</div>";
	return $resHtml;

}

function makeResources($resList){
	$html="";
	$first=true;
	foreach($resList as $res){
		if($first)$first=false;
		else $html.='<div class="resourceSpace">&nbsp;</div>';
		
		$html.=renderResource($res);
	}
	return $html;
}

function getPageContent(){
	
	$search=""; $hidden_search="";
	if(isset($_REQUEST['search'])){
		$search=trim($_REQUEST['search']);
		$hidden_search='<input type="hidden" name="search" value="'.htmlentities($search).'"/>';
	}
	
	$checked_type_lr=""; $hidden_type_lr="";
	$checked_type_lm=""; $hidden_type_lm="";
	$checked_type_lt=""; $hidden_type_lt="";
	
	$types=[];
	if(isset($_REQUEST['type_lr'])){$types[]="lr";$checked_type_lr=' checked="checked" ';$hidden_type_lr='<input type="hidden" name="type_lr" value="1"/>';}
	if(isset($_REQUEST['type_lm'])){$types[]="lm";$checked_type_lm=' checked="checked" ';$hidden_type_lm='<input type="hidden" name="type_lm" value="1"/>';}
	if(isset($_REQUEST['type_lt'])){$types[]="lt";$checked_type_lt=' checked="checked" ';$hidden_type_lt='<input type="hidden" name="type_lt" value="1"/>';}

	$checked_media_text=""; $hidden_media_text="";
	$checked_media_speech=""; $hidden_media_speech="";
	$checked_media_image=""; $hidden_media_image="";
	
	$media=[];
	if(isset($_REQUEST['media_text'])){$media[]="text";$checked_media_text=' checked="checked" ';$hidden_media_text='<input type="hidden" name="media_text" value="1"/>';}
	if(isset($_REQUEST['media_speech'])){$media[]="speech";$checked_media_speech=' checked="checked" ';$hidden_media_speech='<input type="hidden" name="media_speech" value="1"/>';}
	if(isset($_REQUEST['media_image'])){$media[]="image";$checked_media_image=' checked="checked" ';$hidden_media_image='<input type="hidden" name="media_image" value="1"/>';}

	$num=10;
	$start=0;
	if(isset($_REQUEST['start']))$start=intval($_REQUEST['start']);
	
	$repository=new Repository();
	$result=$repository->getResourcesFiltered($types,$media,$search,$start,$num);
	$resList=$result["resources"];

	$pageHtml="<div style=\"text-align:center\">";
	if($start>0){
		$pageHtml.="<form method=\"GET\" style=\"display:inline-block\">${hidden_type_lr}${hidden_type_lm}${hidden_type_lt}${hidden_media_text}${hidden_media_speech}${hidden_media_image}${hidden_search}";
		$pageHtml.='<input type="hidden" name="path" value="'.$_REQUEST['path'].'"/>';
		$ns=$start-$num; if($ns<0)$ns=0;
		$pageHtml.='<input type="hidden" name="start" value="'.$ns.'"/>';
		$pageHtml.='<input type="submit" name="p" value="<<"/>';
		$pageHtml.="</form>";
	}
	$pageHtml.='<div style="display:inline-block; padding:10px;">Showing '.($start+1).' - '.($start+count($resList))." out of ${result['total']}</div>";
	if($start+$num<$result['total']){
		$pageHtml.="<form method=\"GET\" style=\"display:inline-block\">${hidden_type_lr}${hidden_type_lm}${hidden_type_lt}${hidden_media_text}${hidden_media_speech}${hidden_media_image}${hidden_search}";
		$ns=$start+$num; 
		$pageHtml.='<input type="hidden" name="start" value="'.$ns.'"/>';
		$pageHtml.='<input type="submit" name="p" value=">>"/>';
		$pageHtml.='<input type="hidden" name="path" value="'.$_REQUEST['path'].'"/>';
		$pageHtml.="</form>";
	}
	$pageHtml.="</div>";
	
	
    $html=file_get_contents(realpath(dirname(__FILE__))."/main.html");
	$html=str_replace("{{RESOURCES}}",makeResources($resList),$html);
	$html=str_replace("{{PAGE}}",$pageHtml,$html);
	$html=str_replace("{{CHECKED_TYPE_LR}}",$checked_type_lr,$html);
	$html=str_replace("{{CHECKED_TYPE_LM}}",$checked_type_lm,$html);
	$html=str_replace("{{CHECKED_TYPE_LT}}",$checked_type_lt,$html);
	$html=str_replace("{{CHECKED_MEDIA_TEXT}}",$checked_media_text,$html);
	$html=str_replace("{{CHECKED_MEDIA_SPEECH}}",$checked_media_speech,$html);
	$html=str_replace("{{CHECKED_MEDIA_IMAGE}}",$checked_media_image,$html);
	$html=str_replace("{{SEARCH}}",htmlentities($search),$html);
	
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/resource.css");
    
    return $css;
}

function getPageJS(){
}

?>