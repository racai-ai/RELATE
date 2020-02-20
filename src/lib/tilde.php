<?php

$TILDE_clientId="u-a2bfbcd2-7a4a-4176-ab90-99c64468fcfc";
$TILDE_appId="myappid";
$TILDE_URL_GetSystemsList="https://www.letsmt.eu/ws/service.svc/json/GetSystemList";
$TILDE_URL_Translate="https://www.letsmt.eu/ws/service.svc/json/Translate";
$TILDE_DEBUG=false;

function TILDE_getSystems(){
    global $TILDE_clientId,$TILDE_appId,$TILDE_URL_GetSystemsList,$TILDE_DEBUG;

    $ch = curl_init();
    $url="${TILDE_URL_GetSystemsList}?appID=${TILDE_appId}";
    if($TILDE_DEBUG)curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    //curl_setopt($ch, CURLOPT_POST, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "client-id: ${TILDE_clientId}",
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    return json_decode($server_output,true);

}

function TILDE_getFromMetadata($metadata,$key){
    foreach($metadata as $m){
        if(strcasecmp($m['Key'],$key)===0)return $m['Value'];
    }
    
    return false;

}

function TILDE_GetRomanianSystems(){
    $systems=TILDE_getSystems();
    $out=[];
    foreach($systems['System'] as $sys){
        if(strcasecmp($sys['SourceLanguage']['Code'],"ro")==0 || strcasecmp($sys['TargetLanguage']['Code'],"ro")==0){
             if(strcasecmp(TILDE_getFromMetadata($sys['Metadata'],"status"),"running")==0){
                $out[]=[
                    "from" => $sys['SourceLanguage']['Code'],
                    "to" => $sys['TargetLanguage']['Code'],
                    "title" => TILDE_getFromMetadata($sys['Metadata'],"title"),
                    "id" => $sys['ID'] 
                ];
             }
        }
    }
    
    return $out;
}

function TILDE_Translate($id,$text){
    global $TILDE_clientId,$TILDE_appId,$TILDE_URL_Translate,$TILDE_DEBUG;

    $ch = curl_init();
    $url="${TILDE_URL_Translate}?appID=${TILDE_appId}&systemID=${id}&options=&text=".urlencode($text);
    if($TILDE_DEBUG)curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    //curl_setopt($ch, CURLOPT_POST, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "client-id: ${TILDE_clientId}",
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    return $server_output;

}

/*$systems=TILDE_getRomanianSystems();
var_dump($systems);
var_dump(TILDE_translate($systems[0]['id'],"Acesta este un al doilea test."));*/

?>