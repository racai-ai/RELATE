<?php

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

function runBasicTaggingText_en($text,$fout){
    global $runnerFolder,$corpus,$settings,$trun,$taskDesc,$DirectoryAnnotated;
    
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
