<?php

namespace Modules\cleanup;

function cleanFileContent($fcontent,$fpathOut,$meta){
    $fout=fopen($fpathOut,"w");
    $lines="";
    $linesOK=false;
    $numLines=0;
    $nonSym=0;
    $annOK=true;
    $firstWrite=true;
    $allowedNER=array_flip(["O","B-ORG","I-ORG","B-PER","I-PER","B-TIME","I-TIME","B-LOC","I-LOC"]);
    $numColumns=0;
    foreach(explode("\n",$fcontent) as $line){
        $line=trim($line);
        if(strlen($line)===0){
            if($linesOK && $numLines>0 && $nonSym>0 && $annOK){
            	if($firstWrite){
            	    if(!startsWith($lines,"# global.columns")){
                      if($numColumns>12){
            		          $lines="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC RELATE:NE RELATE:NP RELATE:IATE RELATE:EUROVOCID RELATE:EUROVOCMT\n".$meta.$lines;
                      }else{
            		          $lines="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC RELATE:NE RELATE:NP\n".$meta.$lines;
                      }
            	    }
            	}
            	fwrite($fout,$lines."\n");
            	$firstWrite=false;
	          }
            $lines="";
            $linesOK=false;
            $numLines=0;
            $nonSym=0;
            $annOK=true;
        }else{
            $lines.=$line."\n";
            if($line[0]!='#'){
                $ldata=explode("\t",$line);
                if(count($ldata)>$numColumns)$numColumns=count($ldata);
                if(count($ldata)>6 && strcasecmp($ldata[6],"0")!=0){
            	    $linesOK=true;$numLines++;
            	}
            	if(count($ldata)>3 && $ldata[3]!='SYM' && $ldata[3]!='PUNCT')$nonSym++;
                if(count($ldata)>10 && !isset($allowedNER[$ldata[10]]))$annOK=false;
                if(count($ldata)<12)$annOK=false;
            }
        }
    }
    
    fclose($fout);
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
    
    $meta="";
    $p=$data['fpath'];
    $base=substr($data['fpath'],0,strrpos($data['fpath'],'/'));
    $base.="../metadata/";
    $fn=substr($data['fpath'],strrpos($data['fpath'],'/')+1);
    $base.=$fn;
    $base=str_replace(".txt",".conllu",$base);
    if(is_file($base)){
				$meta=file_get_contents($base);
    }
    

    cleanFileContent($contentIn,$finalFile,$meta);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>