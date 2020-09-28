<?php

namespace Modules\ner_baseline;

$NER_DATA=false;

function loadResource($fname){
		global $NER_DATA;
        foreach(explode("\n",file_get_contents($fname)) as $line){
            $line=trim($line);
            if(empty($line) || $line[0]=='#')continue;
            
            $line=explode(" ",$line,2);
            if(count($line)!=2)continue;
            
            $NER_DATA[$line[1]]=$line[0];
            
        }
}

function init($corpus){
    global $NER_DATA;
    
    $path=dirname(__FILE__);
    /*foreach(glob("$path/*.gazetteer") as $fname){
         $type=substr($fname,strrpos($fname,"/")+1);
         $type=substr($type,0,strpos($fname,"."));
         foreach(explode("\n",file_get_contents($fname)) as $line){
                $line=trim($line);
                if(empty($line) || $line[0]=='#')continue;
                $NER_DATA[$line]=strtoupper($type);
         }
    }*/
    
    for($i=0;$i<20;$i++){
        $fname="$path/ner_gazette.$i.txt";
        if(!is_file($fname))break;
        loadResource($fname);
    }                       
    
    $path=$corpus->getFolderPath()."/gold_standoff/ne.gazetteer";
    if(is_file($path))loadResource($path);
}

function runBaseline($fcontent,$fpathOut,$corpus){
    global $NER_DATA;
    if($NER_DATA===false || $NER_DATA===null)init($corpus);

    
    $conllup=new \CONLLUP();
    $conllup->readFromString($fcontent);
    foreach($conllup->getSentenceIterator() as $k_sent=>$sentence){
        $p1_tok=false;
        $p1_tag=false;
        $p2_tag=false;
        $p1_word=false;
        $tokIt=$sentence->getTokenIterator();
        foreach($tokIt as $k_tok=>$token){
        
            $tag="O";
            $found=false;
            for($n=5;$n>=1;$n--){
                $word_seq=$token->getWordSeq($n);
                if($word_seq===false)continue;
                
                if(isset($NER_DATA[$word_seq])){$tag=$NER_DATA[$word_seq];$found=true;break;}
                
            }
        
            if(!$found){
                $n=1;
                $lemma=$token->get("LEMMA");
                if(isset($NER_DATA[$lemma]))$tag=$NER_DATA[$lemma];
            }
            
            for($i=0;$i<$n;$i++){
                if($i>0){$tokIt->next();$token=$tokIt->current();}
                
                $word=$token->get("FORM");
                
                if($tag=="O"){
                    $token->set("RELATE:NE",$tag);
                }else{
                      if($p1_tag===$tag){
                          $token->set("RELATE:NE","I-".$tag);
                      }else if($p1_word==="-" && $p2_tag===$tag){
                          $token->set("RELATE:NE","I-".$tag);
                          $p1_tok->set("RELATE:NE","I-".$tag);
                      }else{
                          $token->set("RELATE:NE","B-".$tag);
                      }
                }
                $p2_tag=$p1_tag;
                $p1_tag=$tag;
                $p1_word=$word;
                $p1_tok=$token;
            }            
            
        }
    }
    
    $conllup->writeToFile($fpathOut);

}


function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    echo "Destination for cleanup $finalFile\n";
/*    if(is_file($finalFile)){
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
*/    
    @mkdir($path);    
    
    runBaseline($contentIn,$finalFile,$corpus);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>