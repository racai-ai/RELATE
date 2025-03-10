<?php

function callPunctuation($url,$text){
    $ch = curl_init();
    
    $boundary = uniqid();
    $delimiter = '-------------' . $boundary;
    $eol = "\r\n";
    $data="";
    $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="text"; filename="text.txt"'.$eol
                . 'Content-type: text/text'.$eol.$eol
                . implode("\n",explode(" ",$text)) . $eol;
    $data .= "--" . $delimiter . "--".$eol;

    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
          //"Authorization: Bearer $TOKEN",
          "Content-Type: multipart/form-data; boundary=" . $delimiter,
          "Content-Length: " . strlen($data)
      
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        //CURLOPT_VERBOSE => $ROBIN_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);

    return $server_output;
}

function restorePunctuationText($url,$text){
    $data=callPunctuation($url,$text);
    
    $data=json_decode($data,true);
    $newt="";
    $rdata=explode(" ",$data['result']);
    $tdata=explode(" ",$text);
    $ndata=[];
    for($i=0;$i<count($tdata);$i++){
        $ndata[]=$tdata[$i];
        if($rdata[$i]=="COMMA")$ndata[]=",";
        if($rdata[$i]=="PERIOD")$ndata[]=".";
    }
    
    if($ndata[count($ndata)-1]!=".")$ndata[]=".";
    
    return implode(" ",$ndata);
}

?>