<?php

namespace Modules\statistics;

function saveStat($fname,$newStat,$corpus,$trun){
    
    $fstat=$corpus->getFolderPath()."/statistics/${fname}_${trun}.json";
    $stat=[];
    if(is_file($fstat))$stat=json_decode(file_get_contents($fstat),true);

    foreach($newStat as $k=>$v){
        if(!isset($stat[$k]))$stat[$k]=$v;
        else $stat[$k]+=$v;
    }
    
    storeFile($fstat,json_encode($stat));
}

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){

		$ftype=$data['ftype'];
		$fnameIn=$data['fpath'];
		$runnerFolder=$runner->getRunnerFolder();
		$trun=$runner->getRunnerId();
    
    if($ftype=='text' || $ftype=='csv'){
        if($contentIn===false)$fname=$fnameIn;
        else{
            $fname="$runnerFolder/input.txt";
            storeFile($fname,$contentIn);
        }
                
        $wc=$settings->get("tools.wc.path");
        $cmd="${wc} ".escapeshellarg($fname)." > ".escapeshellarg($fname.".wc");
        echo "Running wc: $cmd\n";
        passthru($cmd);
        
        if($contentIn!==false)@unlink($fname);
        
        //list($lines,$words,$chars,$rest)=explode(" ",file_get_contents("${fname}.wc"),4);
        sscanf(file_get_contents("${fname}.wc"),"%d%d%d",$lines,$words,$chars);
        @unlink("${fname}.wc");
        
        $fsize=0;
        if($contentIn===false)$fsize=filesize($fnameIn);else $fsize=strlen($contentIn);
        saveStat("stat",["lines"=>intval($lines),"words"=>intval($words),"chars"=>intval($chars),"bytes"=>$fsize],$corpus,$trun);
    }else if($ftype=='conllu'){
        $stat=["tok"=>0,"sent"=>0,"documents"=>1];
        $wordForm=[];
        $lemma=[];
        $lemmaUPOS=[];

        $chars="abcdefghijklmnopqrstuvqxyzăîâșț";
        $charsArr=[];
        for($i=0;$i<mb_strlen($chars);$i++)$charsArr[mb_substr($chars,$i,1)]=0;
        
        $conllup=new \CONLLUP();
        $conllup->readFromString($contentIn);
        foreach($conllup->getSentenceIterator() as $k_sent=>$sentence){
            $stat['sent']++;
            $iateTerms=[];
            $eurovocIds=[];
            $eurovocMts=[];
            foreach($sentence->getTokenIterator() as $k_tok=>$token){
                $stat['tok']++;  
                
                $id=$token->get("ID");
                $form=$token->get("FORM");
                $lem=$token->get("LEMMA");
                $upos=$token->get("UPOS");
                $xpos=$token->get("XPOS");
                $feats=$token->get("FEATS");
                $head=$token->get("HEAD");
                $deprel=$token->get("DEPREL");
                $deps=$token->get("DEPS");
                $misc=$token->get("MISC");
                $ner=$token->get("RELATE:NE");
                $iate=$token->get("RELATE:IATE");
                $eurovocid=$token->get("RELATE:EUROVOCID");
                $eurovocmt=$token->get("RELATE:EUROVOCMT");
                
                if($iate!==false && $iate!=="_"){
                    foreach(explode(";",$iate) as $term)$iateTerms[$term]=true;
                }
                if($eurovocid!==false && $eurovocid!=="_"){
                    foreach(explode(";",$eurovocid) as $term)$eurovocIds[$term]=true;
                }
                if($eurovocmt!==false && $eurovocmt!=="_"){
                    foreach(explode(";",$eurovocmt) as $term)$eurovocMts[$term]=true;
                }
                
                //list($id,$form,$lem,$upos,$xpos,$feats,$head,$deprel,$deps,$misc,$ner,$rest)=explode("\t",$line,12);
                
                if(!isset($stat["UPOS.${upos}"]))$stat["UPOS.${upos}"]=0;
                $stat["UPOS.${upos}"]++;    
    
                if(!isset($stat["NER.${ner}"]))$stat["NER.${ner}"]=0;
                $stat["NER.${ner}"]++;    
                
                if(!isset($stat["DEPREL.${deprel}"]))$stat["DEPREL.${deprel}"]=0;
                $stat["DEPREL.${deprel}"]++;    

                if(!isset($stat["XPOS.${xpos}"]))$stat["XPOS.${xpos}"]=0;
                $stat["XPOS.${xpos}"]++;    

                if(!isset($wordForm[$form]))$wordForm[$form]=1;
                else $wordForm[$form]++;
                
                if(!isset($lemma[$lem]))$lemma[$lem]=1;
                else $lemma[$lem]++;
                
                $lemUPOS=$upos."_".$lem;
                if(!isset($lemmaUPOS[$lemUPOS]))$lemmaUPOS[$lemUPOS]=1;
                else $lemmaUPOS[$lemUPOS]++;
                
                for($i=0;$i<mb_strlen($form);$i++){
                    $c=mb_strtolower(mb_substr($form,$i,1));
                    if(isset($charsArr[$c]))$charsArr[$c]++;
                }
            }

            if(!isset($stat["IATE"]))$stat["IATE"]=0;
            $stat["IATE"]+=count($iateTerms);    
            if(!isset($stat["EUROVOCID"]))$stat["EUROVOCID"]=0;
            $stat["EUROVOCID"]+=count($eurovocIds);    
            if(!isset($stat["EUROVOCMT"]))$stat["EUROVOCMT"]=0;
            $stat["EUROVOCMT"]+=count($eurovocMts);    
            
        }
        
        
        
        saveStat("stat",$stat,$corpus,$trun);
        saveStat("wordform",$wordForm,$corpus,$trun);
        foreach($wordForm as $w=>$v)$wordForm[$w]=1;
        saveStat("wordformdf",$wordForm,$corpus,$trun);
        saveStat("lemma",$lemma,$corpus,$trun);
        saveStat("chars",$charsArr,$corpus,$trun);
        saveStat("lemma_upos",$lemmaUPOS,$corpus,$trun);
    }
    
    storeFile($corpus->getFolderPath()."/changed_statistics.json",json_encode(["changed"=>time()]));            
}


?>