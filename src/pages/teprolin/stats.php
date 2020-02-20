<?php

$STATS_data=[];
$TEPROLIN_ops=false;

function STATS_getData(){
    global $STATS_data;
    
    if(isset($STATS_data['gTokens_total']))return ;
    
    $gRequests=TEPROLIN_getStat("requests","month",12);
    $gData="";
    $total=0;
    foreach($gRequests as $gr){
        if(strlen($gData)>0)$gData.=",";
        $gData.="".log($gr[1]);
        $total+=$gr[1];
    }

    $STATS_data["gRequests_data"]=$gData;
    $STATS_data["gRequests_total"]=$total;
    
    $gRequests=TEPROLIN_getStat("tokens","month",12);
    $gData="";
    $total=0;
    foreach($gRequests as $gr){
        if(strlen($gData)>0)$gData.=",";
        $gData.="".log($gr[1]);
        $total+=$gr[1];
    }

    $STATS_data["gTokens_data"]=$gData;
    $STATS_data["gTokens_total"]=$total;
    
    $gRequests=TEPROLIN_getStat("chars","month",12);
    $gData="";
    $total=0;
    foreach($gRequests as $gr){
        if(strlen($gData)>0)$gData.=",";
        $gData.="".log($gr[1]);
        $total+=$gr[1];
    }

    $STATS_data["gChars_data"]=$gData;
    $STATS_data["gChars_total"]=$total;
    
    
    global $TEPROLIN_ops;
    $TEPROLIN_ops=TEPROLIN_getOperations();
    
    $STATS_data["gOperations_total"]=count($TEPROLIN_ops);
}

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/stats.html");

    STATS_getData();
    global $STATS_data,$TEPROLIN_ops;

    $html=str_replace("{{gRequests_total}}",formatNumberAsString($STATS_data['gRequests_total']),$html);
    $html=str_replace("{{gTokens_total}}",formatNumberAsString($STATS_data['gTokens_total']),$html);
    $html=str_replace("{{gChars_total}}",formatNumberAsString($STATS_data['gChars_total']),$html);
    $html=str_replace("{{gOperations_total}}",$STATS_data['gOperations_total'],$html);
    
    $table="";
    $num=0;
    foreach($TEPROLIN_ops as $op){
        $apps=TEPROLIN_getAppsForOp($op);
        foreach($apps as $ap){
            $num++;
            $table.="<tr><td>$num</td><td>".htmlspecialchars($op)."</td><td>".htmlspecialchars($ap)."</td></tr>\n";
        }
    }
    
    $html=str_replace("{{operations_table}}",$table,$html);

    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js="";
    $js.=file_get_contents(realpath(dirname(__FILE__))."/stats.js");
    
    STATS_getData();
    global $STATS_data;
    
    $js=str_replace("{{gRequests_data}}",$STATS_data['gRequests_data'],$js);
    $js=str_replace("{{gTokens_data}}",$STATS_data['gTokens_data'],$js);
    $js=str_replace("{{gChars_data}}",$STATS_data['gChars_data'],$js);
    
    return $js;
}

?>