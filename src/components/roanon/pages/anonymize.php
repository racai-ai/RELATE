<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/anonymize.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/anonymize.js");
    
	$js=str_replace("{{BIBTEX}}",json_encode(["bib"=>file_get_contents(realpath(dirname(__FILE__))."/../bib/anonymization.bib")]),$js);
    
    return $js;
}

function getPageAdditionalJS(){
    return [
		"extern/ORCID_bibtexParse.js",
		"js/bibtex.js",
	];
}

?>