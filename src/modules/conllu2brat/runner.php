<?php

namespace Modules\conllu2brat;

function getTokPos($text,$tpos,$word,$pword){
    		$p=mb_strpos($text,$word,$tpos);
    		if($p===false || $p-$tpos>strlen($pword)+20){
						$word=str_replace("_"," ",$word);
						$wdata=explode(" ",$word);
						if(count($wdata)>1)$word=$wdata[0];
						$p=mb_strpos($text,$word,$tpos);
				}
				
				if($p!==false){
						return $p;
				}
				
				return false;
}

function convert2Brat($fcontent,$text,$fpathOut,$settings){
		$brat=[];
		$pword="";
		$currentT=1;
		$tpos=0;
		$ctag='O';
		$ctagStart=0;
    
    $conllup=new \CONLLUP();
    $conllup->readFromString($fcontent);
    $tokIt=$conllup->getTokenIterator();
    foreach($tokIt as $token){
    		$word=$token->get("FORM");
    		if($word===false)continue;

    		$tag=$token->get("RELATE:NE");
    		
    		$pstart=getTokPos($text,$tpos,$word,$pword);
    		if($pstart===false)continue;
    		
    		if($tag=='O' || strncasecmp($tag,"B-",2)==0){
						if($ctag!='O'){
								 $to=$pstart-1;
								 while($to>0){
								 		$c=mb_substr($text,$to,1);
								 		if($c==" " || $c=="\n" || $c=="\r" || $c=="\t")$to--;
								 		else break;
								 }
								 $to++;
								 $t=mb_substr($text,$ctagStart,$to-$ctagStart);
								 $brat[]="T${currentT}\t$ctag $ctagStart $to\t$t";
								 $currentT++;
						}
						if($tag=='O')$ctag='O';
						else {$ctag=substr($tag,2);$ctagStart=$pstart;}
				}
				
				$pword=$word;
				$tpos=$pstart;
				
				while(true){
						$c=mb_substr($text,$tpos,1);
						if(preg_match("/[a-zA-ZăîâșțĂÎÂȘȚ0-9]+/",$c)!==1)break;
						$tpos++;
				}
    }
    
    file_put_contents($fpathOut,implode("\n",$brat));
    @chown($fpathOut,$settings->get("owner_user"));
    @chgrp($fpathOut,$settings->get("owner_group"));

}


function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/standoff/";
    $finalFile=$path.$fnameOut;
    $finalFile=changeFileExtension($finalFile,"ann");
    
    $path=$corpus->getFolderPath()."/files/";
    $textFile=$path.$fnameOut;
    $textFile=changeFileExtension($textFile,"txt");

    echo "Destination for conllu2brat $finalFile\n";
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
    
    convert2Brat($contentIn,file_get_contents($textFile),$finalFile,$settings);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>