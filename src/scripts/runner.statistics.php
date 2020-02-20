<?php

function runStatistics($text,$fnameOut,$ftype,$fnameIn){
    global $runnerFolder,$corpus,$settings,$trun;
    
    if($ftype=='text' || $ftype=='csv'){
        if($text===false)$fname=$fnameIn;
        else{
            $fname="$runnerFolder/input.txt";
            storeFile($fname,$text);
        }
                
        $wc=$settings->get("tools.wc.path");
        $cmd="${wc} ".escapeshellarg($fname)." > ".escapeshellarg($fname.".wc");
        echo "Running wc: $cmd\n";
        passthru($cmd);
        
        if($text!==false)@unlink($fname);
        
        //list($lines,$words,$chars,$rest)=explode(" ",file_get_contents("${fname}.wc"),4);
        sscanf(file_get_contents("${fname}.wc"),"%d%d%d",$lines,$words,$chars);
        @unlink("${fname}.wc");
        
        saveStat("stat",["lines"=>intval($lines),"words"=>intval($words),"chars"=>intval($chars)]);
    }else if($ftype=='conllu'){
        $stat=["tok"=>0,"sent"=>0,"documents"=>1];
        $wordForm=[];
        $lemma=[];

        $chars="abcdefghijklmnopqrstuvqxyzăîâșț";
        $charsArr=[];
        for($i=0;$i<mb_strlen($chars);$i++)$charsArr[mb_substr($chars,$i,1)]=0;
        
        foreach(explode("\n",$text) as $line){
            $line=trim($line);
            if(startsWith($line,"# sent_id")){ $stat['sent']++; continue; }
            
            if(startsWith($line,"#") || strlen($line)==0)continue;
            
            $stat['tok']++;  
            
            list($id,$form,$lem,$upos,$xpos,$feats,$head,$deprel,$deps,$misc,$ner,$rest)=explode("\t",$line,12);
            
            if(!isset($stat["UPOS.${upos}"]))$stat["UPOS.${upos}"]=0;
            $stat["UPOS.${upos}"]++;    

            if(!isset($stat["NER.${ner}"]))$stat["NER.${ner}"]=0;
            $stat["NER.${ner}"]++;    
            
            if(!isset($wordForm[$form]))$wordForm[$form]=1;
            else $wordForm[$form]++;
            
            if(!isset($lemma[$lem]))$lemma[$lem]=1;
            else $lemma[$lem]++;
            
            for($i=0;$i<mb_strlen($form);$i++){
                $c=mb_strtolower(mb_substr($form,$i,1));
                if(isset($charsArr[$c]))$charsArr[$c]++;
            }
            
        }
        
        
        
        saveStat("stat",$stat);
        saveStat("wordform",$wordForm);
        foreach($wordForm as $w=>$v)$wordForm[$w]=1;
        saveStat("wordformdf",$wordForm);
        saveStat("lemma",$lemma);
        saveStat("chars",$charsArr);
    }
    
    storeFile($corpus->getFolderPath()."/changed_statistics.json",json_encode(["changed"=>time()]));            
    
}

function saveStat($fname,$newStat){
    global $trun,$corpus;
    
    $fstat=$corpus->getFolderPath()."/statistics/${fname}_${trun}.json";
    $stat=[];
    if(is_file($fstat))$stat=json_decode(file_get_contents($fstat),true);

    foreach($newStat as $k=>$v){
        if(!isset($stat[$k]))$stat[$k]=$v;
        else $stat[$k]+=$v;
    }
    
    storeFile($fstat,json_encode($stat));
}

