<?php

namespace Modules\corenlp;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    if(is_file($finalFile)){
        if(filesize($finalFile)>0 && isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fnameOut\n";
            return false;
        }
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
    
    @mkdir($path);    
    
    runBasicTaggingText_en($contentIn,$finalFile,$runner->getRunnerId(),$settings,$runner);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}

$tagConv=[
"CC" => "CCONJ",
"CD" => "NUM",
"DT" => "DET",        
"EX" => "ADV",
"FW" => "X",
"IN" => "ADP",
"JJ" => "ADJ",
"JJR" => "ADJ",
"JJS" => "ADJ",
"LS" => "X",
"MD" => "ADV",
"NN" => "NOUN",
"NNS" => "NOUN",
"NNP" => "PROPN",
"NNPS" => "PROPN",
"PDT" => "DET",
"POS" => "PRON",
"PRP" => "PRON",
"PRP\$" => "PRON",
"RB" => "ADV",
"RBR" => "ADV",
"RBS" => "ADV",
"RP" => "PART",
"SYM" => "SYM",
"TO" => "ADP",
"UH" => "INTJ",
"VB" => "VERB",
"VBD" => "VERB",
"VBG" => "VERB",
"VBN" => "VERB",
"VBP" => "VERB",
"VBZ" => "VERB",
"WDT" => "DET",
"WP" => "PRON",
"WP\$" => "PRON",
"WRB" => "ADV",
];

function runBasicTaggingText_en($text,$fout,$trun,$settings,$runner){
    global $corpus,$taskDesc,$DirectoryAnnotated;
    
    $runnerFolder=$runner->getRunnerFolder();
    $fname="$runnerFolder/input.txt";
    file_put_contents($fname,$text);
    
    $cnlp=$settings->get("tools.StanfordCoreNLP.path");
    passthru("${cnlp}/corenlp_client.sh -annotators tokenize,ssplit,pos,lemma,ner,depparse -file $fname -outputFormat conllu -outputDirectory $runnerFolder");
    
    
    // Convert to UPOS
    $fdata=file_get_contents("${fname}.conllu");
    $ret="";
    foreach(explode("\n",$fdata) as $line){
        if(strlen($line)===0 || $line[0]=='#'){
            $ret.=$line."\n";
            continue;
        }
        $ldata=explode("\t",$line);
        $ldata[3]=convertStanfordPOSToUPOS($ldata[4]);
        
        $ret.=implode("\t",$ldata)."\n";
    }
    file_put_contents($fout,$ret);
    
    
    @unlink($fname);
    @unlink("${fname}.conllu");
        
}


function convertStanfordPOSToUPOS($tag){
    global $tagConv;

    if(!isset($tagConv[$tag]))return "X";
    
    return $tagConv[$tag];
}

?>