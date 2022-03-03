<?php

$ANONYMIZATION_DEBUG=false;

function ANONYMIZATION_anonymize_text($text, $returnOnlyText=true){

    global $ANONYMIZATION_DEBUG;
    if($ANONYMIZATION_DEBUG){
        $text="Prietena _#PER#1_ei a fost la _#UNK#1_.";
        $anon=["text"=>$text,"mappings"=>["mariei\t_#PER#1_ei","Iasi\t_#UNK#1_"]];
        if($returnOnlyText)
            $anon=$anon['text'];
        return $anon;
    }

    $url="http://127.0.0.1:8202/anonymize?test=test";  // dummy parameter to avoid crash

    /*$out=[];
    foreach(explode("\n",$text) as $line){
            $l=trim($line);
            if(empty($l))$out[]=$line;
            else{
                $anon=file_get_contents("http://127.0.0.1:8202/anonymize?text=".urlencode($line));
                $anon=json_decode($anon,true);
                $anon=$anon['text'];
                $out[]=$anon;
            }
    }
    
    return implode("\n",$out);*/
    
    $ch = curl_init();
    
    $boundary = uniqid();
    $delimiter = '-------------' . $boundary;
    $eol = "\r\n";
    $data="";
    $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="text"; filename="text.txt"'.$eol
                . 'Content-type: text/text'.$eol.$eol
                . $text . $eol;
    $data .= "--" . $delimiter . "--".$eol;

    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20*60, // 20 minutes
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
        //CURLOPT_VERBOSE => 1
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);

    $anon=json_decode($server_output,true);
    if($returnOnlyText)
        $anon=$anon['text'];
    
    return $anon;

}

$ANONYMIZATION_entities=false;

function ANONYMIZATION_addGazetteer($type,$fnameIn){
    global $ANONYMIZATION_entities;
    $fin=fopen($fnameIn,"r");
    if($fin===false){
	   echo "Cannot open [$fnameIn]\n";
	   return ;
    }
    while(!feof($fin)){
	   $line=fgets($fin);
	   if($line===false)break;
	
	   $line=trim($line);
	   if(empty($line))continue;
	
	   $ANONYMIZATION_entities[$type][$line]=true;
    }
    fclose($fin);
     
}

function ANONYMIZATION_deanonymize_text($text){
    global $ANONYMIZATION_entities;

    if($ANONYMIZATION_entities===false){
        $ANONYMIZATION_entities=["PER"=>[],"LOC"=>[],"ORG"=>[]];
        ANONYMIZATION_addGazetteer("PER",dirname(__FILE__)."/pernames-utf8-uniq.txt");
        ANONYMIZATION_addGazetteer("LOC",dirname(__FILE__)."/locnames-utf8-uniq.txt");
        ANONYMIZATION_addGazetteer("ORG",dirname(__FILE__)."/orgnames-utf8-uniq.txt");
        
        foreach($ANONYMIZATION_entities as $k=>$v)$ANONYMIZATION_entities[$k]=array_keys($v);
    }

    $current=[];

    $ret=$text;

    preg_match_all("/_#[A-Z]+#[0-9]+_[a-zA-Z]*/",$text,$matches);
    foreach($matches[0] as $m){
        $type=substr($m,2,3);
        $suff=false;
        $id=$m;
        if(!endsWith($m,"_")){
            $lpos=strrpos($m,"_");
            $suff=substr($m,$lpos+1);
            $id=substr($m,0,$lpos+1);
        }
        
        if(isset($current[$id]))$ent=$current[$id];
        else{
        
            if($type=="UNK"){
                $t=rand(0,count($ANONYMIZATION_entities)-1);
                $type=array_keys($ANONYMIZATION_entities)[$t];
            }
        
            $pos=rand(0,count($ANONYMIZATION_entities[$type])-1);
            $ent=$ANONYMIZATION_entities[$type][$pos];
            if($suff=="ei" && !endsWith($ent,"a")){
                $numtry=0;
                for(;;$pos++,$numtry++){
                    if($numtry>=count($ANONYMIZATION_entities[$type]))break;
                    if($pos>=count($ANONYMIZATION_entities[$type]))$pos=0;
                    $ent=$ANONYMIZATION_entities[$type][$pos];
                    if(endsWith($ent,"a"))break;
                }
            }
            
            $current[$id]=$ent;
        }
        
        if($suff=="ei" && endsWith($ent,"a"))$ent=substr($ent,0,strlen($ent)-1);
        $ent.=$suff;
        
        $ret=str_replace($m,$ent,$ret);        
        
        /*var_dump($type);
        var_dump($suff);
        var_dump($id);
        var_dump($ent);*/
    }
    
    return $ret;

}
