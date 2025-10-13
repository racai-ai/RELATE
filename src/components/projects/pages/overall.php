<?php
require_once realpath(dirname(__FILE__))."/../data/project.php";
require_once realpath(dirname(__FILE__))."/../data/projects.php";
require_once realpath(dirname(__FILE__))."/../data/person.php";
require_once realpath(dirname(__FILE__))."/../data/persons.php";
require_once realpath(dirname(__FILE__))."/../data/projects_overall.php";

function getPersonsHtml(){
    $persons=new Persons();
    $ret="";
    foreach($persons->getList() as $per){
        $ret.="<option value=\"".htmlspecialchars($per['name'])."\">".htmlspecialchars($per['name'])."</option>\n";
    }
    return $ret;
}

function getReportsHtml($prj){
    $ret="";
    foreach($prj->getReports() as $r){
        $ret.="<option value=\"".htmlspecialchars($r['name'])."\">".htmlspecialchars($r['name'])."</option>\n";
    }
    return $ret;
}

function getPageContent(){
    global $user,$modules;
		
    $html=file_get_contents(realpath(dirname(__FILE__))."/overall.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/common_loading.html");
    
    $prj=new ProjectsOverall();
    $hidepropertiesbutton="display:none";
    $hiderightsbutton="display:none";
    /*if($prj->hasRights("admin")){
        $hidepropertiesbutton="";
        $hiderightsbutton="";
    }*/
    
    $html=str_replace("{{PROJECT_REPORTS}}",getReportsHtml($prj),$html);
    $html=str_replace("{{CURRENT_DATE}}",date("Y-m-d"),$html);
    $html=str_replace("{{LOADING}}",$loading,$html);
    $html=str_replace("{{hidepropertiesbutton}}",$hidepropertiesbutton,$html);
    $html=str_replace("{{hiderightsbutton}}",$hiderightsbutton,$html);
    $html=str_replace("{{propdesc}}","",$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/overall.css");
    return $css;
}

function getPageJS(){
    global $user,$modules;

    $projects=new Projects();
    //$prj=new Project($projects,$_REQUEST['name']);
    //if(!$prj->loadData())die("Invalid project");
    
    $hasRights="false"; 
    $hasProperties="false";
    /*if($prj->hasRights("admin")){
        $hasRights="true"; 
        $hasProperties="true";
    }*/

    
    $js=file_get_contents(realpath(dirname(__FILE__))."/overall.js");
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