<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/authors.html");

    $frm="";
    if(isset($_REQUEST['text'])){
	$text=$_REQUEST['text'];
	$text=str_replace(" and ",", ",$text);
	$names=explode(",",$text);
	for($ni=0;$ni<count($names);$ni++){
	    $name=trim($names[$ni]);
	    if($ni==count($names)-1 && $ni>0)$frm.=" and ";
	    else if(strlen($frm)>0)$frm.=", ";
	    $n=explode(" ",trim($name));
	    for($i=count($n)-1;$i>=0;$i--){
		$cn=trim($n[$i]);
		if($i==count($n)-1)$frm.=$cn.", ";
		else{
		    $frm.=mb_substr($cn,0,1).".";
		}
	    }
	}
	
	//$frm=htmlspecialchars($frm);
    }

    $html=str_replace("{{formatted}}",$frm,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
}

?>