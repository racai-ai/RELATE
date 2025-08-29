<?php
require_once realpath(dirname(__FILE__))."/../data/project.php";
require_once realpath(dirname(__FILE__))."/../data/projects.php";
require_once realpath(dirname(__FILE__))."/../data/person.php";
require_once realpath(dirname(__FILE__))."/../data/persons.php";

function getPageContent(){
    global $user,$modules;
		
    if(!isset($_REQUEST['name']))return "";

    $persons=new Persons();
    $per=new Person($persons,$_REQUEST['name']);
    if(!$per->loadData())die("Invalid person");
    if(!$per->hasRights("read"))die("Invalid person");

    $html=file_get_contents(realpath(dirname(__FILE__))."/person.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/common_loading.html");
    
    $hidepropertiesbutton="display:none";
    $hiderightsbutton="display:none";
    if($per->hasRights("admin")){
        $hidepropertiesbutton="";
        //$hiderightsbutton="";
    }
    
    $html=str_replace("{{PERSON_NAME_HTML}}",htmlspecialchars($_REQUEST['name']),$html);
    $html=str_replace("{{PERSON_NAME}}",$_REQUEST['name'],$html);
    $html=str_replace("{{CURRENT_DATE}}",date("Y-m-d"),$html);
    $html=str_replace("{{LOADING}}",$loading,$html);
    $html=str_replace("{{hidepropertiesbutton}}",$hidepropertiesbutton,$html);
    $html=str_replace("{{hiderightsbutton}}",$hiderightsbutton,$html);
    $html=str_replace("{{propdesc}}",htmlspecialchars($per->getData("desc","")),$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/person.css");
    return $css;
}

function getPageJS(){
    global $user,$modules;

    $persons=new Persons();
    $per=new Person($persons,$_REQUEST['name']);
    if(!$per->loadData())die("Invalid person");
    
    $hasRights="false"; 
    $hasProperties="false";
    if($per->hasRights("admin")){
        //$hasRights="true"; 
        $hasProperties="true";
    }

    
    $js=file_get_contents(realpath(dirname(__FILE__))."/person.js");
    $js=str_replace("{{PERSON_NAME}}",$_REQUEST['name'],$js);
    $js=str_replace("{{HAS_RIGHTS}}",$hasRights,$js);
    $js=str_replace("{{HAS_PROPERTIES}}",$hasProperties,$js);

    return $js;
}

function getPageAdditionalCSS(){
    return [
		"extern/pqgrid-2.4.1/pqgrid.min.css",
		"extern/autocomplete/autocomplete.css",
        "extern/viewerjs-1.11.2/dist/viewer.min.css",
	];
}

function getPageAdditionalJS(){
    return [
		"extern/pqgrid-2.4.1/pqgrid.min.js",
		"extern/autocomplete/autocomplete.js",
        "extern/viewerjs-1.11.2/dist/viewer.min.js",

	];
}

?>