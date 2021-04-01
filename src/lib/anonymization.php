<?php

function ANONYMIZATION_anonymize_text($text){

    $out=[];
    foreach(explode("\n","$text") as $line){
            $l=trim($line);
            if(empty($l))$out[]=$line;
            else{
                $anon=file_get_contents("http://127.0.0.1:8202/anonymize?text=".urlencode($line));
                $out[]=$anon;
            }
    }
    
    return implode("\n",$out);


}
