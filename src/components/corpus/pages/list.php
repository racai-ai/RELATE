<?php

function getPageContent(){
		global $modules;
		
    $html=file_get_contents(realpath(dirname(__FILE__))."/list.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

		$langs="";
		$languages=array_keys($modules->getLanguages());
		sort($languages);
		
		foreach($languages as $lang){
				$sel="";
				if(strcasecmp($lang,"ro")==0)$sel="selected=\"true\"";
				$langs.="<option value=\"${lang}\" $sel>${lang}</option>\n";
		}

    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{languages}}",$langs,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/list.js");
    
    return $js;
}

function getPageAdditionalCSS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.css"];
}

function getPageAdditionalJS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.js"];
}


?>