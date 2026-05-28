<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/docs.html");
    
    return $html;
}

function getPageCSS(){
	return "";
}

function getPageJS(){
	$js=file_get_contents(realpath(dirname(__FILE__))."/docs.js");
    return $js;
    return "";
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