<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/custom.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");
    $output=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_output.html");
    
    $operations=TEPROLIN_getOperations();
    $op_string="";
    $i=0;
    foreach($operations as $op){
        $i++;
        $op_string.="<input type=\"checkbox\" name=\"op$i\" value=\"$op\" id=\"op$i\"/><label for=\"op$i\">$op</label><br/>";
        
        $data=TEPROLIN_getAppsForOp($op);
        $j=0;
        $op_string.="<span style=\"display:inline-block;width:50px;\">&nbsp;</span><input type=\"radio\" name=\"op${i}_r\" value=\"default\" checked=\"checked\" id=\"op${i}_r$j\"/><label for=\"op${i}_r$j\">Default</label>";
        foreach($data as $d){
            $j++;
            $op_string.="<span style=\"display:inline-block;width:50px;\">&nbsp;</span><input type=\"radio\" name=\"op${i}_r\" value=\"$d\" id=\"op${i}_r$j\"/><label for=\"op${i}_r$j\">$d</label>";
        }
        $op_string.="<br/>";
        
    }
    
    $html=str_replace("{{operations}}",$op_string,$html);
    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{output}}",$output,$html);
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common.css");
    return $css;
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common.js");
    $js.=file_get_contents(realpath(dirname(__FILE__))."/custom.js");
    return $js;
}

?>