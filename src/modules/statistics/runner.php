<?php

namespace Modules\statistics;

require_once "../lib/extern/getid3/getid3.php";

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

function saveCSV($fname,$newStat,$corpus,$trun){
    
    $fstat=$corpus->getFolderPath()."/statistics/${fname}_".sprintf("%03d",$trun).".csv";
    $fout=fopen($fstat,"a");
    fputcsv($fout,$newStat);
    fclose($fout);
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
        
        if($ftype=='text'){
            if($contentIn===false)$contentIn=file_get_contents($data['fpath']);
            $fTextUppercase="No";
            $t1=mb_strtoupper($contentIn);
            if($t1==$contentIn)$fTextUppercase="Yes";

            $fTextLowercase="No";
            $t1=mb_strtolower($contentIn);
            if($t1==$contentIn)$fTextLowercase="Yes";            
            
            $textData=[
                basename($data['fpath']),
                $fsize,
                intval($lines),
                intval($words),
                $chars,
                $fTextUppercase,
                $fTextLowercase,
            ];
            saveCSV("text.list",$textData,$corpus,$trun);
        }

    }else if($ftype=='conllu'){
        $stat=["tok"=>0,"sent"=>0,"documents"=>1];
        $wordForm=[];
        $lemma=[];
        $lemmaUPOS=[];
        $allIateTerms=[];
        $allEurovocIds=[];
        $allEurovocMts=[];

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
            
            foreach($iateTerms as $term=>$t){
                $data=explode(":",$term);
                if(count($data)==2){
                    if(!isset($allIateTerms[$data[1]]))$allIateTerms[$data[1]]=1;
                    else $allIateTerms[$data[1]]++;
                }
            }
            foreach($eurovocIds as $term=>$t){
                $data=explode(":",$term);
                if(count($data)==2){
                    if(!isset($allEurovocIds[$data[1]]))$allEurovocIds[$data[1]]=1;
                    else $allEurovocIds[$data[1]]++;
                }
            }
            foreach($eurovocMts as $term=>$t){
                $data=explode(":",$term);
                if(count($data)==2){
                    if(!isset($allEurovocMts[$data[1]]))$allEurovocMts[$data[1]]=1;
                    else $allEurovocMts[$data[1]]++;
                }
            }
            
        }
        
        saveStat("stat",$stat,$corpus,$trun);
        saveStat("wordform",$wordForm,$corpus,$trun);
        foreach($wordForm as $w=>$v)$wordForm[$w]=1;
        saveStat("wordformdf",$wordForm,$corpus,$trun);
        saveStat("lemma",$lemma,$corpus,$trun);
        saveStat("chars",$charsArr,$corpus,$trun);
        saveStat("lemma_upos",$lemmaUPOS,$corpus,$trun);

        saveStat("iateterms",$allIateTerms,$corpus,$trun);
        foreach($allIateTerms as $w=>$v)$allIateTerms[$w]=1;
        saveStat("iatetermsdf",$allIateTerms,$corpus,$trun);
        
        saveStat("eurovocids",$allEurovocIds,$corpus,$trun);
        foreach($allEurovocIds as $w=>$v)$allEurovocIds[$w]=1;
        saveStat("eurovocidsdf",$allEurovocIds,$corpus,$trun);
        
        saveStat("eurovocmts",$allEurovocMts,$corpus,$trun);
        foreach($allEurovocMts as $w=>$v)$allEurovocMts[$w]=1;
        saveStat("eurovocmtsdf",$allEurovocMts,$corpus,$trun);
        
        $conllupData=[
            basename($data['fpath']),
            $stat['sent'],
            $stat['tok'],
            count($wordForm),
            count($lemma),
        ];
        foreach(["ADJ","ADP","ADV","AUX","CCONJ","DET","INTJ","NOUN","NUM","PART","PRON","PROPN","PUNCT","SCONJ","SYM","VERB","X"] as $pos){
            $n=0; if(isset($stat["UPOS.${pos}"]))$n=$stat["UPOS.${pos}"];
            $conllupData[]=$n;
        }
        $totalNER=0;
        foreach($stat as $k=>$v)if(startsWith($k,"NER."))$totalNER++;
        $conllupData[]=$totalNER;
        $totalNER=0;
        foreach($stat as $k=>$v)if(startsWith($k,"NER.B-"))$totalNER++;
        $conllupData[]=$totalNER;
        
        saveCSV("conllup.list",$conllupData,$corpus,$trun);
        
    }else if($ftype=="image"){
        $stat=["image.number"=>1,"image.bytes"=>filesize($data['fpath'])];
        $imageSizes=[];
        $imageWidths=[];
        $imageHeights=[];
        $imageChannels=[];
        $imageBits=[];
        $imageMimes=[];
        $imageData=[];
        
        $img=getimagesize($data['fpath']);
        if(!isset($img["channels"]))$img["channels"]=3;
        $imageWidths[$img[0]]=1;
        $imageHeights[$img[1]]=1;
        $imageChannels[$img['channels']]=1;
        $imageBits[$img['bits']]=1;
        $imageSizes[$img[0]."x".$img[1]]=1;
        $imageMimes[$img['mime']]=1;
        
        saveStat("image.stat",$stat,$corpus,$trun);
        saveStat("image.widths",$imageWidths,$corpus,$trun);
        saveStat("image.heights",$imageHeights,$corpus,$trun);
        saveStat("image.channels",$imageChannels,$corpus,$trun);
        saveStat("image.bits",$imageBits,$corpus,$trun);
        saveStat("image.mimes",$imageMimes,$corpus,$trun);
        saveStat("image.sizes",$imageSizes,$corpus,$trun);
        
        $imageData=[basename($data['fpath']),$img[0],$img[1],$img['mime'],$img['channels'],$img['bits'],$stat['image.bytes']];
        saveCSV("image.list",$imageData,$corpus,$trun);
        
    }else if($ftype=="audio"){
        $stat=["audio.number"=>1,"audio.bytes"=>filesize($data['fpath'])];
        $audioChannels=[];
        $audioBits=[];
        $audioCodec=[];
        $audioSampleRate=[];
        $audioMime=[];
        
        $getID3=new \getID3();
        $info=$getID3->analyze($data['fpath']);
        $stat['audio.duration_seconds']=$info['playtime_seconds'];
        
        $audioChannels[$info['audio']['channels']]=1;
        $audioBits[$info['audio']['bits_per_sample']]=1;
        $audioCodec[$info['audio']['codec']]=1;
        $audioSampleRate[$info['audio']['sample_rate']]=1;
        $audioMime[$info['mime_type']]=1;
        
        saveStat("audio.stat",$stat,$corpus,$trun);
        saveStat("audio.channels",$audioChannels,$corpus,$trun);
        saveStat("audio.bits",$audioBits,$corpus,$trun);
        saveStat("audio.codec",$audioCodec,$corpus,$trun);
        saveStat("audio.samplerate",$audioSampleRate,$corpus,$trun);
        saveStat("audio.mime",$audioMime,$corpus,$trun);
        
        $audioData=[
            basename($data['fpath']),
            sprintf("%0.2f",$info['playtime_seconds']),
            getTimeStrFromMS(round($info['playtime_seconds']*1000)),
            $info['audio']['channels'],
            $info['audio']['bits_per_sample'],
            $info['audio']['codec'],
            $info['audio']['sample_rate'],
            $info['mime_type'],
            $stat['audio.bytes']
        ];
        saveCSV("audio.list",$audioData,$corpus,$trun);
    }
    
    storeFile($corpus->getFolderPath()."/changed_statistics.json",json_encode(["changed"=>time()]));            
}


?>