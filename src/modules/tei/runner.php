<?php
namespace Modules\tei;

function runnerTEI2Text($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $folderText=$corpus->getFolderPath()."/files";
    $folderMeta=$corpus->getFolderPath()."/meta";
    
    $base=basename($data['fpath']);
    $baseFile=$base;
    $lastPos=strrpos($base,".");
    if($lastPos!==false)$base=substr($base,0,$lastPos);
    
    $content=$contentIn;
    $n=0;
    $p1=strpos($content,"</teiHeader>");
    if($p1!==false)$content=substr($content,$p1);
    while(true){
        $p1=strpos($content,"<p>");
        if($p1===false)break;
        
        $content=substr($content,$p1+3);
        $p2=strpos($content,"</p>");
        if($p2===false)break;
        
        $text=trim(substr($content,0,$p2));
        $content=substr($content,$p2+4);
        
        $n++;
        file_put_contents("$folderText/${base}_${n}.txt",$text);
        file_put_contents("$folderMeta/${base}_${n}.txt.meta",json_encode([
            "name"=>"${base}_${n}.txt",
            "corpus"=>$corpus->getName(),
            "type"=>"text",
            "desc"=>"Extracted from ${baseFile} by TEI2Text",
            "created_by"=>$taskDesc['created_by'],
            "created_date"=>date("Y-M-d")
        ]));
        
        @chown("$folderText/${base}_${n}.txt",$settings->get("owner_user"));
        @chgrp("$folderText/${base}_${n}.txt",$settings->get("owner_group"));
        @chown("$folderMeta/${base}_${n}.txt.meta",$settings->get("owner_user"));
        @chgrp("$folderMeta/${base}_${n}.txt.meta",$settings->get("owner_group"));
        
    }

    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
    @chown($corpus->getFolderPath()."/changed_files.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_files.json",$settings->get("owner_group"));

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    @chown($corpus->getFolderPath()."/changed_basictagging.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_basictagging.json",$settings->get("owner_group"));
    
}

function runnerCONLLUP2TEI($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $folderAnn=$corpus->getFolderPath()."/".$settings->get("dir.annotated");
    
    $base=basename($data['fpath']);
    $lastPos=strrpos($base,".");
    if($lastPos!==false)$base=substr($base,0,$lastPos);

    $cout="";
    
    $content=$contentIn;
    $n=0;
    $p1=strpos($content,"</teiHeader>");
    if($p1!==false){
        $cout.=substr($content,0,$p1);
        $content=substr($content,$p1);
    }
    while(true){
        $p1=strpos($content,"<p>");
        if($p1===false)break;
        
        $cout.=substr($content,0,$p1+3);
        
        $content=substr($content,$p1+3);
        $p2=strpos($content,"</p>");
        if($p2===false)break;
        $content=substr($content,$p2);

        $n++;
        $fname="${folderAnn}/${base}_${n}.conllup";
        if(is_file($fname)){
            $conllup=new \CONLLUP();
            $conllup->readFromFile($fname);
            $sentences=$conllup->getSentenceIterator();
            foreach($sentences as $sent){
                $cout.="<s>\n";
                foreach($sent->getTokenIterator() as $tok){
                    if($tok->get('UPOS')=="PUNCT"){
                        $cout.="<pc".
                            " pos=\"".htmlentities($tok->get('UPOS'),ENT_XML1)."\"".
                            " msd=\"".htmlentities($tok->get('XPOS'),ENT_XML1)."\"".
                            "\" >".
                            htmlentities($tok->get('FORM'),ENT_XML1).
                            "</pc>\n";
                    }else{
                        $cout.="<w join=\"right\"".
                            " pos=\"".htmlentities($tok->get('UPOS'),ENT_XML1)."\"".
                            " msd=\"".htmlentities($tok->get('XPOS'),ENT_XML1)."\"".
                            " lemma=\"".htmlentities($tok->get('LEMMA'),ENT_XML1)."".
                            "\" >".
                            htmlentities($tok->get('FORM'),ENT_XML1).
                            "</w>\n";
                    }
                }
                $cout.="</s>\n";
            }
        }
        
    }
    
    $cout.=$content;
    
    file_put_contents($data['fpath'], $cout);
    @chown($data['fpath'],$settings->get("owner_user"));
    @chgrp($data['fpath'],$settings->get("owner_group"));
    
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
    @chown($corpus->getFolderPath()."/changed_standoff.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_standoff.json",$settings->get("owner_group"));

}


?>