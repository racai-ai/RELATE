<?php

class BRAT2CONLLU {
		public function getTokPos($text,$tpos,$word,$pword){
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
		
		public function convertBrat2Conllu($fcontent,$text,$anncontent,$fpathOut){
		
				$ann=[];
				foreach(explode("\n",$anncontent) as $line){
						$ldata=explode("\t",$line);
						if(count($ldata)!==3)continue;
						
						list($type,$from,$to)=explode(" ",$ldata[1],3);
						
						$ann[]=["type"=>$type,"from"=>intval($from),"to"=>intval($to)];
				}
		
				$ctag='O';
				$ctagStart=0;
				$pword="";
				$tpos=0;
		    
		    $conllup=new \CONLLUP();
		    $conllup->readFromString($fcontent);
		    $tokIt=$conllup->getTokenIterator();
		    foreach($tokIt as $token){
		    		$word=$token->get("FORM");
		    		if($word===false)continue;
		    		
		    		$pstart=$this->getTokPos($text,$tpos,$word,$pword);
		    		if($pstart===false){$ctag='O';continue;}
		    		
		    		$tag='O';
						foreach($ann as $cann){
								if($cann['from']<=$pstart && $cann['to']>$pstart){
										$tag=$cann['type'];
								}
						}
						
						if($tag!='O'){
								if($ctag!=$tag)$token->set("RELATE:NE","B-".$tag);
								else $token->set("RELATE:NE","I-".$tag);
						}else $token->set("RELATE:NE",$tag);
		
						$ctag=$tag;
						$pword=$word;
						$tpos=$pstart;
						while(true){
								$c=mb_substr($text,$tpos,1);
								if(preg_match("/[a-zA-ZăîâșțĂÎÂȘȚ0-9]+/",$c)!==1)break;
								$tpos++;
						}
		
		    }
		    
		    $conllup->writeToFile($fpathOut);
		    
		
		}

}

?>