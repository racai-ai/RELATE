<?php

function getPageContent(){
		global $modules;
		
    $html=file_get_contents(realpath(dirname(__FILE__))."/list.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

		$langs="";
		foreach($modules->getLanguages() as $lang=>$t){
				$langs.="<option value=\"${lang}\">${lang}</option>\n";
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