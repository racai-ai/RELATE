<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/home.html");
    
    return $html;
}

function getPageCSS(){
	return "";
}

function getPageJS(){
	$js=file_get_contents(realpath(dirname(__FILE__))."/home.js");
	$js=str_replace("{{BIBTEX}}",json_encode(["bib"=>file_get_contents(realpath(dirname(__FILE__))."/../bib/home.bib")]),$js);
    return $js;
}

function getPageAdditionalJS(){
    return [
		"extern/ORCID_bibtexParse.js",
		"extern/viewerjs-1.11.2/dist/viewer.min.js",
		"js/bibtex.js",
	];
}
function getPageAdditionalCSS(){
    return [
		"extern/viewerjs-1.11.2/dist/viewer.min.css",
	];
}

?>