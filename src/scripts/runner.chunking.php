<?php


function runChunking($fnameIn,$fnameOut){
    global $runnerFolder,$corpus,$settings;
    
    $path=$corpus->getFolderPath()."/chunking/";
    @mkdir($path);    
    
    $fout=fopen("$path/$fnameOut","w");
    $sent=[];
    $first=true;
    foreach(explode("\n",file_get_contents($fnameIn)) as $line){
        if(strlen($line)===0 || $line[0]=='#'){
            if(count($sent)>0){
                $result=chunkSent($sent);
                if(!$first)fwrite($fout,"\n");
                fwrite($fout,$result);
            }
            $sent=[];
            continue;
            $first=false;
        }
        
        $data=explode("\t",$line);
        $sent[]=[
          "wid" => $data[0],
          "word" => $data[1],
          "lemma" => $data[2],
          "upos" => $data[3],
          "pos" => $data[4],
          "feats" => $data[5],
          "head" => $data[6],
          "deprel" => $data[7],
          "deps" => $data[8],
          "misc" => $data[9],
          "chunk" => "O"
        ];
    }
    
    if(count($sent)>0){
        $result=chunkSent($sent);
        if(!$first)fwrite($fout,"\n");
        fwrite($fout,$result);
    }
    
    fclose($fout);
    
    return true;    
}

function chunkSent($sent){

    $sentStr="";
    foreach($sent as $tok) {
        if(strlen($sentStr)>0)$sentStr.=" ";
        $sentStr.=$tok['word'];
    }

    $sentStr=str_replace(" ","%20",$sentStr);
    
    $result=file_get_contents("http://127.0.0.1:8031/?sent=${sentStr}");
    $data=json_decode($result,true);
    
    $ret="";
    foreach($data['entities'] as $ent){
        $ret.="${ent['type']}\t${ent['text']}\n";
    }
    
    return $ret;
}