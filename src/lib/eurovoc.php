<?php

$EUROVOC_ID_MT=false;
$EUROVOC_ID_TEXT=false;
$EUROVOC_MT_TEXT=false;

$EUROVOC_DOMAINS=[
"04"=>"POLITICS",
"08"=>"INTERNATIONAL RELATIONS",
"10"=>"EUROPEAN UNION",
"12"=>"LAW",
"16"=>"ECONOMICS",
"20"=>"TRADE",
"24"=>"FINANCE",
"28"=>"SOCIAL QUESTIONS",
"32"=>"EDUCATION AND COMMUNICATIONS",
"36"=>"SCIENCE",
"40"=>"BUSINESS AND COMPETITION",
"44"=>"EMPLOYMENT AND WORKING CONDITIONS",
"48"=>"TRANSPORT",
"52"=>"ENVIRONMENT",
"56"=>"AGRICULTURE, FORESTRY AND FISHERIES",
"60"=>"AGRI-FOODSTUFFS",
"64"=>"PRODUCTION, TECHNOLOGY AND RESEARCH",
"66"=>"ENERGY",
"68"=>"INDUSTRY",
"72"=>"GEOGRAPHY",
"76"=>"INTERNATIONAL ORGANISATIONS"
];

function EUROVOC_load(){
    global $EUROVOC_ID_MT,$EUROVOC_ID_TEXT,$EUROVOC_MT_TEXT;
    
    if($EUROVOC_ID_MT===false){
        $EUROVOC_ID_MT=[];
        $EUROVOC_ID_TEXT=[];
        $EUROVOC_MT_TEXT=[];
        
        $fp=fopen(dirname(__FILE__)."/eurovoc_export_ro.csv","r");
        $first=true;
        while(!feof($fp)){
            $line=fgetcsv($fp,0,";");
            if($line===null || $line===false)break;
            
            if($first){$first=false;continue;}
            
            if(count($line)!==5)continue;
            
            $id=$line[0];
            $mt=$line[4];
            $pos=strpos($mt," ");
            if($pos!==false)$mt=substr($mt,0,$pos);
            
            $EUROVOC_ID_MT[$id]=$mt;
            
            $EUROVOC_ID_TEXT[$id]=$line[1];
            if(!empty($line[3]))$EUROVOC_ID_TEXT[$id].=" ".$line[3];
            
            
            $EUROVOC_MT_TEXT[$mt]=substr($line[4],$pos+1);
       }
       fclose($fp);
    }
}

function EUROVOC_getMT($ids){

    global $EUROVOC_ID_MT;
    
    EUROVOC_load();
    
    $ret=[];
    foreach($ids as $id)if(isset($EUROVOC_ID_MT[$id]))$ret[$EUROVOC_ID_MT[$id]]=true;
    
    return array_keys($ret);
}

function EUROVOC_getIdText($ids){

    global $EUROVOC_ID_TEXT;
    
    EUROVOC_load();
    
    $ret=[];
    foreach($ids as $id)if(isset($EUROVOC_ID_TEXT[$id]))$ret[$id]=$EUROVOC_ID_TEXT[$id];
    
    return $ret;
}

function EUROVOC_getMTText($ids){

    global $EUROVOC_MT_TEXT;
    
    EUROVOC_load();
    
    $ret=[];
    foreach($ids as $id)if(isset($EUROVOC_MT_TEXT[$id]))$ret[$id]=$EUROVOC_MT_TEXT[$id];
    
    return $ret;
}

function EUROVOC_getDomains($mt){

    global $EUROVOC_DOMAINS;
    
    EUROVOC_load();
    
    $ret=[];
    foreach($mt as $id){
        $mid=substr($id,0,2);
        $ret[$mid]=true;
    }
    
    return array_keys($ret);
}

function EUROVOC_getDomainText($ids){

    global $EUROVOC_DOMAINS;
    
    EUROVOC_load();
    
    $ret=[];
    foreach($ids as $id)if(isset($EUROVOC_DOMAINS[$id]))$ret[$id]=$EUROVOC_DOMAINS[$id];
    
    return $ret;
}
