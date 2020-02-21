<?php

function runBasicTaggingText_ro($text,$fout){
    global $runnerFolder,$corpus,$settings,$trun,$taskDesc,$DirectoryAnnotated;
    
    //$text=preg_replace("/[^ a-zA-Z0-9.,;_+%&\$#()\\[\\]ăîâșțĂÎÂȘȚ]/u"," ",$text);
    //$text=preg_replace("/[ ]+/"," ",$text);
    
    //echo $text;

    $parId=0;
    $par=$text;
    $sentId=0;
    foreach(explode("\n",$text) as $par){
        $par=trim($par);
        if(strlen($par)!=0){
        
            $parId++;
            
            $json=json_decode(TEPROLIN_call(["text"=>$par],$trun+1),true);   // ,"exec"=>"named-entity-recognition"
            
            if(isset($json['teprolin-result']) && isset($json['teprolin-result']['tokenized'])){
                foreach($json['teprolin-result']['tokenized'] as &$sent){
                    foreach($sent as &$tok){
                        if(!isset($tok['upos']) && isset($tok['_msd']))
                            $tok['upos']=MSD2UPOS($tok['_msd']);
                            
                        if(!isset($tok['ner'])){
                            $tok['ner']="O";
                            if(isset($tok['_ner']) && strlen($tok['_ner'])>0)
                                $tok['ner']=$tok['_ner'];
                            if(isset($tok['_bner']) && strlen($tok['_bner'])>0){
                                $tok['ner']="O";//$tok['_bner'];
                                // skip for now BIONER
                                /*if(startsWith($tok['ner'],"I-") || startsWith($tok['ner'],"B-"))
                                    $tok['ner']=substr($tok['ner'],2);*/
                            }
                        }
                    }
                }
            }
            
            list($conllu,$sentId)=TEPROLIN_json2conllu("ro_legal",$json,$sentId,false);
            
            if($parId==1){
                file_put_contents($fout,implode("\n",$conllu));
            }else{
                file_put_contents($fout,"\n".implode("\n",$conllu),FILE_APPEND);
            }
        }
    }
        
}
