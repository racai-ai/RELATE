<?php

namespace Modules\ner_old;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;

    $conllup=new \CONLLUP();
    $conllup->readFromString($contentIn);
    
    foreach($conllup->getSentenceIterator() as $sent){
        $tokens="<s>\t<s>\t<s>\t<s>\t<s>\n";
        $msd=[];
        foreach($sent->getTokenIterator() as $tok){
            $ctag=MSD2CTAG($tok->get("XPOS"));
            $tokens.=$tok->get("FORM")."\t".$tok->get("LEMMA")."\t".$tok->get("XPOS")."\t".substr($tok->get("XPOS"),0,2)."\t".$ctag."\n";
        }
        $tokens.="</s>\t</s>\t</s>\t</s>\t</s>\n";
        
        $ret=file_get_contents("http://89.38.230.23/ner/ner.php?tokens=".urlencode($tokens));
        
        $ret=explode("\n",$ret);
        $n=1;
        $prev="O";
        foreach($sent->getTokenIterator() as $tok){
            $ner="O";
            if($n<count($ret)){
                $line=explode("\t",$ret[$n]);
                $ner=$line[count($line)-1];
                $pner=$ner;
                if($ner!="O"){
                    if($ner==$prev)$ner="I-$ner";
                    else $ner="B-$ner";
                }
                $prev=$pner;
            }
            $tok->set("RELATE:NE",$ner);
            $n++;
        }
    }

    $conllup->writeToFile($finalFile);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>