<?php

class CONLLUP {

    private $data;
    private $columns;
    private $columnId;
    
    public static $defaultGlobalColumns="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC";
    public static $defaultTeprolinGlobalColumns="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC RELATE:NE RELATE:NP";
    

/*                      if($numColumns>12){
            		          $lines="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC RELATE:NE RELATE:NP RELATE:IATE RELATE:EUROVOC\n".$meta.$lines;
                      }else{
            		          $lines="# global.columns = ID FORM LEMMA UPOS XPOS FEATS HEAD DEPREL DEPS MISC RELATE:NE RELATE:NP\n".$meta.$lines;
                      }
*/

    public function getColumns(){ return $this->columns;}
    public function setColumns($columns){$this->columns=$columns; $this->columnId=array_flip($this->columns);}
    public function getFirstToken(){
        foreach($this->getTokenIterator() as $tok)return $tok;
        return false;
    }
    public function getNumColumns(){return count($this->columns);}

    public function readFromString($datas){
        $this->data=[];
        $this->columns=[];
        $this->columnId=[];
    
        foreach(explode("\n",$datas) as $line){
            $line=trim($line);

            if(empty($line))$this->data[]=["type"=>"new_sent"];
            else if($line[0]=='#'){
                $this->data[]=["type"=>"comment","content"=>$line];
                if(startsWith($line,"# global.columns")){
                     $cdata=explode("=",$line,2);
                     $this->columns=explode(" ",trim($cdata[1]));
                     $this->columnId=array_flip($this->columns);
                }
            }else{
                $this->data[]=["type"=>"data","content"=>explode("\t",$line)];
            }            
        }
        
        if(empty($this->columns)){ // assume conllu format
            $cdata=explode("=",self::$defaultGlobalColumns,2);
            $this->columns=explode(" ",trim($cdata[1]));
            $this->columnId=array_flip($this->columns);
        }
        
        if($this->getFirstToken()!==false){
    	    for($i=count($this->columns);$i<$this->getFirstToken()->getNumColumns();$i++){
        	$this->columns[]="C${i}";
    	    }
        }
        $this->columnId=array_flip($this->columns);
    }
    
    public function addFileMetadataField($field,$value){
                $newdoc=0;
				for($i=0;$i<count($this->data);$i++){
						$line=$this->data[$i];
						if($line['type']=="new_sent")break;
						if($line['type']=='comment'){
								 $cdata=explode("=",$line['content'],2);
								 if(count($cdata)==2){
								 		$name=trim($cdata[0],"# \t");
								 		if(strcasecmp($name,$field)===0){
												$this->data[$i]['content']="# $field = $value";
												return true;
										}else if(strncasecmp($name,"newdoc",6)==0){
                                                $newdoc=$i;
                                        }
								 }
						}
				}
				
				if($line['type']=="new_sent"){$i=$newdoc;}
				
				array_splice($this->data,$i,1, [$this->data[$i],["type"=>"comment","content"=>"# $field = $value"]]);
				return true;
		}
		
		public function getText(){
				$text="";
				$first=true;
				foreach($this->getTokenIterator() as $tok){
						if($first)$first=false; else $text.=" ";
						$text.=$tok->get("FORM");
				}
				
				return $text;
		}
    
    public function readFromFile($fpath){
        $this->readFromString(file_get_contents($fpath));
    }
    
    public function getNumLines(){return count($this->data);}
    public function getLine($i){
				if($i>=count($this->data))return false;
				return $this->data[$i];
		}
    
    public function getSentenceIterator(){
        return new CONLLUPSentenceIterator($this);
    }
    
    public function getTokenIterator(){
        return new CONLLUPTokenIterator($this,0,count($this->data)-1);
    }

    public function getTokenData($i,$col){
        if(!isset($this->columnId[$col]))return false;
        if($i>=count($this->data) || $this->data[$i]['type']!=='data')return false;
        return $this->data[$i]['content'][$this->columnId[$col]];
    }
    
    public function setTokenData($i,$col,$val){
        if(!isset($this->columnId[$col])){
            $n=count($this->columns);
            $this->columns[]=$col;
            $this->columnId[$col]=$n;
        }
        $this->data[$i]['content'][$this->columnId[$col]]=$val;
    }
    
    public function writeToFile($fpathOut){
        $fout=fopen($fpathOut,"w");
        $head="# global.columns = ".implode(" ",$this->columns);
        fwrite($fout,"$head\n");
        foreach($this->data as $line){
            if($line['type']=='comment'){
                if(!startsWith($line['content'],"# global.columns")){
                    fwrite($fout,"${line['content']}\n");
                }
            }else if($line['type']=='new_sent'){
                fwrite($fout,"\n");
            }else if($line['type']=='data'){
                $pad="";
                for($i=count($line['content']);$i<count($this->columns);$i++)$pad.="\t_";
                fwrite($fout,implode("\t",$line['content'])."$pad\n");
            }
        }
        fclose($fout);
    }

}

?>