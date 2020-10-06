<?php

$operations=TEPROLIN_getOperations();

$options=["text","exec"];

$options=array_merge($options,$operations);

$data=[];
foreach($options as $o){
    if(isset($_REQUEST[$o]))$data[$o]=$_REQUEST[$o];
}

if(!isset($data['text']) || !isset($data['exec']))
  die("Invalid usage");

if(strlen($data['exec'])==0)unset($data['exec']);
  
$json=json_decode(TEPROLIN_call($data),true);

if(isset($json['teprolin-result']) && isset($json['teprolin-result']['tokenized'])){
    foreach($json['teprolin-result']['tokenized'] as &$sent){
        foreach($sent as &$tok){
            if(!isset($tok['upos']) && isset($tok['_msd']))
                $tok['upos']=MSD2UPOS($tok['_msd']);
                
            if(!isset($tok['ner'])){
                $tok['ner']="";
                if(isset($tok['_ner']))$tok['ner']=$tok['_ner'];
                if(isset($tok['_bner']) && strlen($tok['_bner'])>0){
                    $tok['ner']=$tok['_bner'];
                    if(startsWith($tok['ner'],"I-") || startsWith($tok['ner'],"B-"))
                        $tok['ner']=substr($tok['ner'],2);
                }
            }
        }
    }
}

echo json_encode($json);

?>