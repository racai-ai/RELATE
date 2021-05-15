<?php

namespace Modules\teprolin;

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
    
    $allowedNER=[];
    if(isset($taskDesc['allowed_ner'])){
        $an=trim($taskDesc['allowed_ner']);
        if(strlen($an)>0){
            $allowedNER=explode(",",$taskDesc['allowed_ner']);
            if(count($allowedNER)>0)$allowedNER=array_flip($allowedNER);
        }
    }
    
    $useBIONER=false;
    $useNER=false;
    $stripBI=false;
    if(isset($taskDesc['use_bioner']) && strcasecmp($taskDesc['use_bioner'],"YES")==0)$useBIONER=true;
    if(isset($taskDesc['use_ner']) && strcasecmp($taskDesc['use_ner'],"YES")==0)$useNER=true;
    if(isset($taskDesc['strip_bi']) && strcasecmp($taskDesc['strip_bi'],"YES")==0)$stripBI=true;
    
    runBasicTaggingText_ro($contentIn,$finalFile,$runner->getRunnerId(),$allowedNER,$useNER,$useBIONER,$stripBI);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}

function runBasicTaggingText_ro($text,$fout,$trun,$allowedNER,$useNER,$useBIONER,$stripBI){
    
    //$text=preg_replace("/[^ a-zA-Z0-9.,;_+%&\$#()\\[\\]ăîâșțĂÎÂȘȚ]/u"," ",$text);
    //$text=preg_replace("/[ ]+/"," ",$text);
    
    //echo $text;

    $parId=0;
    $par=$text;
    $sentId=0;
    foreach(explode("\n",$text) as $par){
        $par=trim($par);
        if(strlen($par)!=0){
        
            $parId++;
            
            $segments=[];
            if(strlen($par)>8*1024){
        				$segments=SentenceSplit_makeSegments(8*1024, ["text"=>$par],$trun+1);
            }else $segments[]=$par;
            
            foreach($segments as $segment){
								$json=json_decode(TEPROLIN_call(["text"=>$segment],$trun+1),true);   // ,"exec"=>"named-entity-recognition"
            
			        	if(isset($json['teprolin-result']) && isset($json['teprolin-result']['tokenized'])){
			            	    foreach($json['teprolin-result']['tokenized'] as &$sent){
						                	foreach($sent as &$tok){
						                    	    if(!isset($tok['upos']) && isset($tok['_msd']))
						                        	$tok['upos']=MSD2UPOS($tok['_msd']);
						                            
						                    	    if(!isset($tok['ner'])){
								                        	$tok['ner']="O";
								                        	if(isset($tok['_ner']) && strlen($tok['_ner'])>0 && $useNER)
								                            	    $tok['ner']=$tok['_ner'];
								                        	if(isset($tok['_bner']) && strlen($tok['_bner'])>0 && $useBIONER){
								                            	    $tok['ner']=$tok['_bner']; // "O"
								                                // skip for now BIONER
								                                /*if(startsWith($tok['ner'],"I-") || startsWith($tok['ner'],"B-"))
								                                    $tok['ner']=substr($tok['ner'],2);*/
								                        	}
								                            if($stripBI && startsWith($tok['ner'],"I-") || startsWith($tok['ner'],"B-"))
								                                    $tok['ner']=substr($tok['ner'],2);
                                                            if(count($allowedNER)>0 && !isset($allowedNER[$tok['ner']]))
                                                                    $tok['ner']='O';
						                    	    }
						                	}
			            	    }
			        	}
            
			        	list($conllu,$sentId)=TEPROLIN_json2conllu("ro_legal",$json,$sentId,false);
			            
			        	if($parId==1){
			            	    file_put_contents($fout,\CONLLUP::$defaultTeprolinGlobalColumns."\n".implode("\n",$conllu));
			        	}else{
			            	    file_put_contents($fout,"\n".implode("\n",$conllu),FILE_APPEND);
			        	}
            }
        }
    }
        
}

?>