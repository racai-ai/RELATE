<?php


/*function cleanFolder($folderIn,$folderOut){
    $dh = opendir($folderIn);
    if($dh===false)return false;
    
    while (($file = readdir($dh)) !== false) {
        $fpath="$folderIn/$file";
        $fpathOut="$folderOut/$file";
        if(!is_file($fpath))continue;
        
        cleanFile($fpath,$fpathOut);
    }
    closedir($dh);    
}

function cleanFile($fpathIn,$fpathOut){
    $fin=fopen($fpathIn,"r");
    $fout=fopen($fpathOut,"r");
    $lines="";
    $linesOK=false;
    while(!feof($fin)){
        $line=fgets($fin,10000);
        if($line===false || $line===null)break;
        
        $line=trim($line);
        if(strlen($line)===0){
            if($linesOK)fwrite($fout,$lines."\n");
            $lines="";
            $linesOK=false;
        }else{
            $lines.=$line."\n";
            if($line[0]!='#'){
                $ldata=explode("\t",$line);
                if(count($ldata)>6 && strcasecmp($ldata[6],"0")!=0)$linesOK=true;
            }
        }
    }
    
    fclose($fin);
    fclose($fout);
}
*/
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
            		          $lines="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC RELATE:NE RELATE:NP RELATE:IATE RELATE:EUROVOC\n".$meta.$lines;
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
            	if(count($ldata)>3 && $ldata[3]!='SYM')$nonSym++;
                if(count($ldata)>10 && !isset($allowedNER[$ldata[10]]))$annOK=false;
                if(count($ldata)<13)$annOK=false;
            }
        }
    }
    
    fclose($fout);
}


function runCleanup($text,$fout,$meta){
    global $corpus;
    
    runCleanup_internal($text,$fout,$meta);
    
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
}

function runCleanup_internal($text,$fout,$meta){
    global $runnerFolder,$corpus,$settings,$trun,$taskDesc,$DirectoryAnnotated;
    
    $path=$corpus->getFolderPath()."/$DirectoryAnnotated/";
    $finalFile=$path.$fout;
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

    cleanFileContent($text,$finalFile,$meta);
}

?>