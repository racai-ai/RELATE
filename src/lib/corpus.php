<?php

function CORPUS_UASORT_FILES($f1,$f2){return strcasecmp($f1['name'],$f2['name']);}

class Corpus {
    private $corpora;
    private $data;
    private $name;
    
    public function __construct($corpora,$name,$data=null){
        $this->data = $data;
        $this->name = $name;
        $this->corpora = $corpora;
    }
    
    public function getName(){return $this->name;}
    
    public function clear(){ $this->data=[]; }

    public function isValidName($name=false){
        if($name===false)
            $un=$this->name;
        else $un=$name;
        return strlen($un)>3 && preg_match("/[^-_#a-zA-Z0-9ăîâșțĂÎÂȘȚ@(). ]/",$un)===0 && $un[0]!=' ' && $un[strlen($un)-1]!=' ' && $un[0]!='.' && $un[0]!='-' && $un[0]!='@' && strlen($un)<200;    
    }
    
    public function loadData(){
        if(!$this->isValidName()){$this->clear();return false;}
        
        $fdata=$this->corpora->getPath()."/".$this->name."/corpus.json";
        if(!is_file($fdata)){$this->clear();return false;}
        
        $this->data=json_decode(file_get_contents($fdata),true);
        $this->data['name']=$this->name;
        return true;
    }
    
    public function getLang(){
    		if(isset($this->data['lang']))return $this->data['lang'];
    		return false;
	}
		
	public function hasAudio(){
			if(!isset($this->data['audio']))return false;
			return $this->data['audio'];
	}

	public function hasImage(){
			if(!isset($this->data['image']))return false;
			return $this->data['image'];
	}

	public function hasVideo(){
			if(!isset($this->data['video']))return false;
			return $this->data['video'];
	}

	public function hasBratProfiles(){
			if(!isset($this->data['brat_profiles']))return false;
			return $this->data['brat_profiles'];
	}

	public function hasClassificationProfiles(){
			if(!isset($this->data['hasclassification']))return false;
			return ($this->data['hasclassification']==="yes" || $this->data['hasclassification']===true);
	}

	public function hasCorrectedText(){
			if(!isset($this->data['hascorrected']))return false;
			return $this->data['hascorrected'];
	}

	public function hasGoldAnnotations(){
			if(!isset($this->data['gold']))return false;
			return $this->data['gold'];
	}
    
    public function getFolderPath($create=false){
        if(!$this->isValidName($this->name))return false;
    
        $fdata=$this->corpora->getPath();
        if($create)@mkdir($fdata);
        $fdata.="/".$this->name;
        if($create)@mkdir($fdata);
        if(!is_dir($fdata))return false;
        return $fdata;
    }
    
    public function saveData($overwrite){
        if(!$this->isValidName()){$this->clear();return false;}
        
        $fdata=$this->getFolderPath(true);
        if($fdata===false)return false;

        $fdata.="/corpus.json";
        
        if(is_file($fdata) && !$overwrite)return false;
        
        $this->data['name']=$this->name;
        $this->data=file_put_contents($fdata,json_encode($this->data));

        return true;
    }

    public function getAllData(){ return $this->data; }
    
    public function getData($key,$def){
        if(!isset($this->data[$key]))return $def;
        return $this->data[$key];
    }
    
    public function setData($key,$val){
        $this->data[$key]=$val;
    }
    
	public function createStandoffMetadata($taskDesc,$pathStandoffMetadata){
		$meta=$this->getMetadataProfile();
		if(is_array($meta) && isset($meta["fields"])){
			$metaData=[];
			foreach($meta["fields"] as $f){
				if($f["onupload"] && isset($taskDesc["upload_meta"]) && isset($taskDesc["upload_meta"][$f['field']])){
					$metaData[$f['field']]=$taskDesc["upload_meta"][$f['field']];
				}else $metaData[$f['field']]=$f['default'];
			}
			
			ksort($metaData);
			$ret='<?xml version="1.0" encoding="UTF-8"?'.">\n<Metadata>\n";
			$cpath="";
			foreach($metaData as $k=>$v){
				$pos=strrpos($k,"/");
				$fieldName=$k;
				if($pos!==false){
					$path=substr($k,0,$pos);
					$fieldName=substr($k,$pos+1);
					if($path!=$cpath){
						if(strlen($cpath)>0){
							$arr=explode("/",$cpath);
							for($i=count($arr)-1;$i>=0;$i--)$ret.=str_repeat("    ",$i+1)."</${arr[$i]}>\n";
						}
						$cpath=$path;
						$arr=explode("/",$cpath);
						for($i=0;$i<count($arr);$i++)$ret.=str_repeat("    ",$i+1)."<${arr[$i]}>\n";
					}
					$arr=explode("/",$cpath);
				}else{
						if(strlen($cpath)>0){
							$arr=explode("/",$cpath);
							for($i=count($arr)-1;$i>=0;$i--)$ret.=str_repeat("    ",$i+1)."</${arr[$i]}>\n";
						}
						$cpath="";
						$arr=[];
				}						
				$ret.=str_repeat("    ",count($arr)+1)."<$fieldName>$v</$fieldName>\n";						
			}
			$ret.="</Metadata>\n";
			file_put_contents($pathStandoffMetadata,$ret);
		}
	}

	
    public function addUploadedFile($file,$data){
        global $DirectoryAnnotated;
        if($this->data===null || empty($this->data))return false;
        
        if(!$this->isValidName($this->data['name']))return false;
        
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;

        $dir_meta=$dir; 
		$dir_standoff=$dir;
		$dir_standoff.="/standoff";
		@mkdir($dir_standoff);
		$dir_files=$dir;
		$dir_files.="/files";
		@mkdir($dir_files);
        if($data['type']=="zip_text")$dir.="/zip_text";
        else if($data['type']=="standoff" || $data['type']=="pdf")$dir.="/standoff";
        else if($data['type']=="goldann")$dir.="/gold_ann";
        else if($data['type']=="goldstandoff")$dir.="/gold_standoff";
        else if($data['type']=="annotated")$dir.="/".$DirectoryAnnotated;
        else if($data['type']=="zip_annotated")$dir.="/zip_".$DirectoryAnnotated;
        else $dir.="/files";
        @mkdir($dir);
        $dir_meta.="/meta";
        @mkdir($dir_meta);
        
        $dpath=$dir."/".$data['name'];
        if(is_file($dpath)){
            if($data['type']=="zip_text" || $data['type']=="zip_annotated" || $data['type']=="standoff" || $data['type']=="pdf")
                @unlink($dpath);
            else
                return false;
        }

        if(move_uploaded_file($file,$dpath)!==true)return false;

        if($data['type']=="text" || $data['type']=='csv' || $data['type']=='pdf'){
			if($data['type']=='pdf'){
				$txtName=changeFileExtension($data['name'],"txt");
				$txtFile="${dir_files}/$txtName";
				
				RELATE_pdf2text($dpath, $txtFile);
				
				$data['name']=$txtName;
				$data['type']="text";
				file_put_contents($base_dir."/changed_standoff.json",json_encode(["changed"=>time()]));
			}
			
            file_put_contents($dir_meta."/".$data['name'].".meta",json_encode($data));
			
			if(isset($data['meta'])){
				$pathStandoffMetadata=$dir_standoff."/".changeFileExtension($data['name'],"xml");
				$this->createStandoffMetadata(["upload_meta"=>$data['meta']],$pathStandoffMetadata);
				file_put_contents($base_dir."/changed_standoff.json",json_encode(["changed"=>time()]));
			}

            file_put_contents($base_dir."/changed_files.json",json_encode(["changed"=>time()]));
        }else if($data['type']=="annotated"){
            file_put_contents($base_dir."/changed_annotated.json",json_encode(["changed"=>time()]));
        }else if($data['type']=="standoff"){
            file_put_contents($base_dir."/changed_standoff.json",json_encode(["changed"=>time()]));
        }else if($data['type']=="goldann"){
            file_put_contents($base_dir."/changed_gold_ann.json",json_encode(["changed"=>time()]));
        }else if($data['type']=="goldstandoff"){
            file_put_contents($base_dir."/changed_gold_standoff.json",json_encode(["changed"=>time()]));
        }

        if($data['type']=="zip_text"){
            $tasks=new Task($this);
            global $user;
            $tdata=[
                'corpus' => $data['corpus'],
                'type' => "unzip_text",
                'fname' => $data['name'],
                'desc' => "Unzip TEXT from ".$data['name'],
                'created_by'=>$user->getUsername(),
                'created_date'=>strftime("%Y-%m-%d %H:%M:%S"),
                'upload_meta' => $data['meta']
            ];
            
            if($tasks->addTask($tdata)===false)return false;
        
        }else if($data['type']=="zip_annotated"){
            $tasks=new Task($this);
            global $user;
            $tdata=[
                'corpus' => $data['corpus'],
                'type' => "unzip_annotated",
                'fname' => $data['name'],
                'desc' => "Unzip ANNOTATED files from ".$data['name'],
                'created_by'=>$user->getUsername(),
                'created_date'=>strftime("%Y-%m-%d %H:%M:%S"),
            ];
            
            if($tasks->addTask($tdata)===false)return false;
	}

        return true;        
    }
    
    public function getFiles(){
        if($this->data===null || empty($this->data))return [];

        $corpora=[];
    
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;
        
        if(is_file($dir."/list_files.json") && is_file($dir."/changed_files.json") && filemtime($dir."/list_files.json")>=filemtime($dir."/changed_files.json")){
            $corpora=json_decode(file_get_contents($dir."/list_files.json"),true);
            return $corpora;
        }
        
        $dir_meta=$dir;
        $dir.="/files";
        $dir_meta.="/meta";
        
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            $dpath_meta="${dir_meta}/${file}.meta";
            if(!is_file($dpath) || !is_file($dpath_meta))continue;
            $corpora[]=json_decode(file_get_contents($dpath_meta),true);
        }
        closedir($dh);
        usort($corpora,'CORPUS_UASORT_FILES');
        
        file_put_contents($base_dir."/list_files.json",json_encode(array_values($corpora)));
        
        return $corpora;    
    
    }
    
    public function getFilesBasicTagging(){
        global $DirectoryAnnotated;
        if($this->data===null || empty($this->data))return [];

        $corpora=[];
    
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;
        
        if(is_file($dir."/list_basictagging.json") && is_file($dir."/changed_basictagging.json") && filemtime($dir."/list_basictagging.json")>=filemtime($dir."/changed_basictagging.json")){
            $corpora=json_decode(file_get_contents($dir."/list_basictagging.json"),true);
            return $corpora;
        }
        
        $dir_meta=$dir;
        $dir.="/".$DirectoryAnnotated;
        $dir_meta.="/meta";
        
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            $dpath_meta="${dir_meta}/${file}.meta";
            if(!is_file($dpath))continue;
            
            $meta=[];
            if(is_file($dpath_meta)){
                $meta=json_decode(file_get_contents($dpath_meta),true);
            }
            if(!isset($meta['name']))$meta['name']=$file;
            
            $meta['type']='conllu';
            
            $size=filesize($dpath);
            $unit="b";
            if($size>1024){$size/=1024.0;$unit="Kb";}
            if($size>1024){$size/=1024.0;$unit="Mb";}
            if($size>1024){$size/=1024.0;$unit="Gb";}
            if($size>1024){$size/=1024.0;$unit="Tb";}
            if($size==0 && $unit=="b")$unit="";
            $size=round($size,2)." ".$unit;

            $meta['size']=$size;
            $corpora[]=$meta;
        }
        closedir($dh);

        file_put_contents($base_dir."/list_basictagging.json",json_encode($corpora));

        
        return $corpora;    
    
    }
    
    public function getFilesStandoff(){
        if($this->data===null || empty($this->data))return [];

        $corpora=[];
    
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;
        
        if(is_file($dir."/list_standoff.json") && is_file($dir."/changed_standoff.json") && filemtime($dir."/list_standoff.json")>=filemtime($dir."/changed_standoff.json")){
            $corpora=json_decode(file_get_contents($dir."/list_standoff.json"),true);
            return $corpora;
        }
        
        $dir_meta=$dir;
        $dir.="/standoff";
        
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_file($dpath))continue;
            
            $meta=[];
            if(!isset($meta['name']))$meta['name']=$file;
            
            $meta['type']='text';
            
            $size=filesize($dpath);
            $unit="b";
            if($size>1024){$size/=1024.0;$unit="Kb";}
            if($size>1024){$size/=1024.0;$unit="Mb";}
            if($size>1024){$size/=1024.0;$unit="Gb";}
            if($size>1024){$size/=1024.0;$unit="Tb";}
            if($size==0 && $unit=="b")$unit="";
            $size=round($size,2)." ".$unit;

            $meta['size']=$size;
            $corpora[]=$meta;
        }
        closedir($dh);

        file_put_contents($base_dir."/list_standoff.json",json_encode($corpora));

        
        return $corpora;    
    
    }

    public function getFilesGoldStandoff(){
        if($this->data===null || empty($this->data))return [];

        $corpora=[];
    
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;
        
        if(is_file($dir."/list_gold_standoff.json") && is_file($dir."/changed_gold_standoff.json") && filemtime($dir."/list_gold_standoff.json")>=filemtime($dir."/changed_gold_standoff.json")){
            $corpora=json_decode(file_get_contents($dir."/list_gold_standoff.json"),true);
            return $corpora;
        }
        
        $dir_meta=$dir;
        $dir.="/gold_standoff";
        
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_file($dpath))continue;
            
            $meta=[];
            if(!isset($meta['name']))$meta['name']=$file;
            
            $meta['type']='text';
            
            $size=filesize($dpath);
            $unit="b";
            if($size>1024){$size/=1024.0;$unit="Kb";}
            if($size>1024){$size/=1024.0;$unit="Mb";}
            if($size>1024){$size/=1024.0;$unit="Gb";}
            if($size>1024){$size/=1024.0;$unit="Tb";}
            if($size==0 && $unit=="b")$unit="";
            $size=round($size,2)." ".$unit;

            $meta['size']=$size;
            $corpora[]=$meta;
        }
        closedir($dh);

        file_put_contents($base_dir."/list_gold_standoff.json",json_encode($corpora));

        
        return $corpora;    
    
    }
    
    public function getFilesGoldAnn(){
        if($this->data===null || empty($this->data))return [];

        $corpora=[];
    
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;
        
        if(is_file($dir."/list_gold_ann.json") && is_file($dir."/changed_gold_ann.json") && filemtime($dir."/list_gold_ann.json")>=filemtime($dir."/changed_gold_ann.json")){
            $corpora=json_decode(file_get_contents($dir."/list_gold_ann.json"),true);
            return $corpora;
        }
        
        $dir_meta=$dir;
        $dir.="/gold_ann";
        
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_file($dpath))continue;
            
            $meta=[];
            if(!isset($meta['name']))$meta['name']=$file;
            
            $meta['type']='text';
            
            $size=filesize($dpath);
            $unit="b";
            if($size>1024){$size/=1024.0;$unit="Kb";}
            if($size>1024){$size/=1024.0;$unit="Mb";}
            if($size>1024){$size/=1024.0;$unit="Gb";}
            if($size>1024){$size/=1024.0;$unit="Tb";}
            if($size==0 && $unit=="b")$unit="";
            $size=round($size,2)." ".$unit;

            $meta['size']=$size;
            $corpora[]=$meta;
        }
        closedir($dh);

        file_put_contents($base_dir."/list_gold_ann.json",json_encode($corpora));

        
        return $corpora;    
    
    }
    

    public function getArchives(){
        global $DirectoryAnnotated;
        if($this->data===null || empty($this->data))return [];

        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;

        $archives=[];
        
        $dirs=[
            "${base_dir}/zip_text",
            "${base_dir}/zip_$DirectoryAnnotated",
            "${base_dir}/zip_standoff",
            "${base_dir}/zip_gold_standoff",
            "${base_dir}/zip_gold_ann",
            "${base_dir}/zip_audio",
            "${base_dir}/marcell-out",
            "${base_dir}/curlicat-out",
        ];
        
        foreach($dirs as $dir){
            if(is_dir($dir)){
            
                $dh = opendir($dir);
                if($dh!==false){
                
                    while (($file = readdir($dh)) !== false) {
                        $dpath="$dir/$file";
                        if(!is_file($dpath)) continue;
                        $meta=[];
                        $meta['fname']=substr($dir,strrpos($dir,'/')+1)."/".$file;
                        $meta['type']='zip';
                        
                        $size=filesize($dpath);
                        $unit="b";
                        if($size>1024){$size/=1024.0;$unit="Kb";}
                        if($size>1024){$size/=1024.0;$unit="Mb";}
                        if($size>1024){$size/=1024.0;$unit="Gb";}
                        if($size>1024){$size/=1024.0;$unit="Tb";}
                        if($size==0 && $unit=="b")$unit="";
                        $size=round($size,2)." ".$unit;
            
                        $meta['size']=$size;
                        $archives[]=$meta;
                    }
                    closedir($dh);
                }
            }
        }
        //file_put_contents($base_dir."/list_basictagging.json",json_encode($corpora));

        
        return $archives;    
    
    }
    
    private function internalGetFileList($subfolders){
        global $DirectoryAnnotated;
        if($this->data===null || empty($this->data))return [];

        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;

        $audio=[];
        
        $dirs=[];
        foreach($subfolders as $sf)$dirs[]="${base_dir}/${sf}";
        
        foreach($dirs as $dir){
            if(is_dir($dir)){
            
                $dh = opendir($dir);
                if($dh!==false){
                
                    while (($file = readdir($dh)) !== false) {
                        $dpath="$dir/$file";
                        if(!is_file($dpath)) continue;
                        $meta=[];
                        $meta['fname']=substr($dir,strrpos($dir,'/')+1)."/".$file;
                        $meta['name']=$file;
                        $meta['type']='audio';
                        
                        $size=filesize($dpath);
                        $unit="b";
                        if($size>1024){$size/=1024.0;$unit="Kb";}
                        if($size>1024){$size/=1024.0;$unit="Mb";}
                        if($size>1024){$size/=1024.0;$unit="Gb";}
                        if($size>1024){$size/=1024.0;$unit="Tb";}
                        if($size==0 && $unit=="b")$unit="";
                        $size=round($size,2)." ".$unit;
            
                        $meta['size']=$size;
                        $audio[]=$meta;
                    }
                    closedir($dh);
                }
            }
        }
        //file_put_contents($base_dir."/list_basictagging.json",json_encode($corpora));

        
        return $audio;    
    
    }
    
    public function getAudio(){
        return $this->internalGetFileList(["audio"]);
    }    
    public function getImage(){
        return $this->internalGetFileList(["image"]);
    }    
    public function getVideo(){
        return $this->internalGetFileList(["video"]);
    }    
    
    public function getAudioCurrent($uname){
				$data=$this->getAudio();
				
				$current=-1;
				$base= "audio/${uname}_";
				foreach($data as $d){
						if(startsWith($d['fname'],$base)){
						    $num=substr($d['fname'],strlen($base));
						    $num=substr($num,0,strrpos($num,"."));
						    $num=intval($num);
						    if($num>$current)$current=$num;
						}
				}
				
				return ($current+1);
		}
		
		public function getAudioData_CSVFile($fpath,$fdata,$totalInit,$uname,$current){
			$total=$totalInit;
			$sent="";
            $dir=$this->getFolderPath();
			$base= "$dir/audio/${uname}_";
    
		    $fp=fopen($fpath,"r");
		    if($fp===false)return ["total"=>$totalInit, "sent"=>"", "current"=>$current];
		    
		    $lnum=-1;
		    while(!feof($fp)){
		        $line=fgetcsv($fp,0,$fdata['delimiter'],$fdata['enclosure'],$fdata['escape']);
		        if($line===false)break;
		        
		        $lnum++;
		        if($lnum<intval($fdata['ignore_rows']))continue;
		        
		        if($line[0]===null)continue;
		        
		        if(strlen($fdata['comment'])>0 && startsWith($line[0],$fdata['comment']))continue;
		
				// add individual columns
                foreach(explode(",",$fdata['columns']) as $col){
                    $text=$line[intval($col)];
                    if(strlen($text)>0){
                        if(!is_file("${base}${total}.wav") && $current<0){$current=$total;$sent=$text;}
						$total++;
					}
				}
		        
		    }
		    
		    fclose($fp);
		    
		    return ["total"=>$total,"sent"=>$sent,"current"=>$current]; 
		}
		
		public function getAudioDataNext($uname){
		
			$total=0;
			$sent="";
            $current=-1;

            $dir=$this->getFolderPath();
			$base= "$dir/audio/${uname}_";
			
            foreach($this->getFiles() as $fdata){
                if($fdata['type']=='csv'){
                		$rdata=$this->getAudioData_CSVFile(
                            $this->getFolderPath()."/files/".$fdata['name'],
                            $fdata,
                            $total,
                            $uname,
                            $current
                        );
                    
                        $total=$rdata['total'];
        				if(strlen($rdata['sent'])>0){
                            $current=$rdata['current'];
                            $sent=$rdata['sent'];
                        }
                }else if($fdata['type']=='text'){
                        if(!is_file("${base}${total}.wav") && $current<0){
                            $current=$total;
							$sent=file_get_contents($this->getFolderPath()."/files/".$fdata['name']);
                        }

                		$total++;
                }
            }
        
            return ["total"=>$total, "sent"=>$sent, "current"=>$current] ;
		}


    private function mergeStatistics($fname,&$stat){
        $newStat=json_decode(file_get_contents($fname),true);
        foreach($newStat as $k=>$v){
            if(!isset($stat[$k]))$stat[$k]=$v;
            else $stat[$k]+=$v;
        }
    }

    public function getStatistics(){
        if($this->data===null || empty($this->data))return [];

        $stat=[];
    
        $dir=$this->getFolderPath();
        if($dir===false)return [];
        $base_dir=$dir;
        
        if(is_file($dir."/list_statistics.json") && is_file($dir."/changed_statistics.json") && filemtime($dir."/list_statistics.json")>=filemtime($dir."/changed_statistics.json")){
            $stat=json_decode(file_get_contents($dir."/list_statistics.json"),true);
            return $stat;
        }
        
        $dir.="/statistics";
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        $stat=["tok"=>0,"sent"=>0,"documents"=>0];
        $wordform=[];
        $wordFirstLow=[];
        $wordFirstUpper=[];
        $wordformdf=[];
        $lemma=[];
        $charsArr=[];
        $lemmaUPOS=[];
        $iateTerms=[];
        $iateTermsdf=[];
        $eurovocIds=[];
        $eurovocIdsdf=[];
        $eurovocMts=[];
        $eurovocMtsdf=[];
        $textCSV=[];
        $conllupCSV=[];
        
        /*** IMAGES ***/
        $imageStat=[];
        $imageSizes=[];
        $imageWidths=[];
        $imageHeights=[];
        $imageChannels=[];
        $imageBits=[];
        $imageMimes=[];
        $imageCSV=[];
        
        /*** AUDIO ***/
        $audioStat=[];
        $audioChannels=[];
        $audioBits=[];
        $audioCodec=[];
        $audioSampleRate=[];
        $audioMime=[];
        $audioCSV=[];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_file($dpath))continue;
            
            if(startsWith($file,"stat_")){
                $this->mergeStatistics($dpath,$stat);
            }else if(startsWith($file,"wordform_")){
                $this->mergeStatistics($dpath,$wordform);
            }else if(startsWith($file,"wordfirstlower_")){
                $this->mergeStatistics($dpath,$wordFirstLow);
            }else if(startsWith($file,"wordfirstupper_")){
                $this->mergeStatistics($dpath,$wordFirstUpper);
            }else if(startsWith($file,"wordformdf_")){
                $this->mergeStatistics($dpath,$wordformdf);
            }else if(startsWith($file,"lemma_upos_")){
                $this->mergeStatistics($dpath,$lemmaUPOS);
            }else if(startsWith($file,"lemma_")){
                $this->mergeStatistics($dpath,$lemma);
            }else if(startsWith($file,"chars_")){
                $this->mergeStatistics($dpath,$charsArr);
            }else if(startsWith($file,"iateterms_")){
                $this->mergeStatistics($dpath,$iateTerms);
            }else if(startsWith($file,"iatetermsdf_")){
                $this->mergeStatistics($dpath,$iateTermsdf);
            }else if(startsWith($file,"eurovocids_")){
                $this->mergeStatistics($dpath,$eurovocIds);
            }else if(startsWith($file,"eurovocidsdf_")){
                $this->mergeStatistics($dpath,$eurovocIdsdf);
            }else if(startsWith($file,"eurovocmts_")){
                $this->mergeStatistics($dpath,$eurovocMts);
            }else if(startsWith($file,"eurovocmtsdf_")){
                $this->mergeStatistics($dpath,$eurovocMtsdf);
            }else if(startsWith($file,"text.list_")){
                $textCSV[]=$dpath;
            }else if(startsWith($file,"conllup.list_")){
                $conllupCSV[]=$dpath;
                
            /**** IMAGES ***/
            }else if(startsWith($file,"image.stat_")){
                $this->mergeStatistics($dpath,$imageStat);
            }else if(startsWith($file,"image.sizes_")){
                $this->mergeStatistics($dpath,$imageSizes);
            }else if(startsWith($file,"image.widths_")){
                $this->mergeStatistics($dpath,$imageWidths);
            }else if(startsWith($file,"image.heights_")){
                $this->mergeStatistics($dpath,$imageHeights);
            }else if(startsWith($file,"image.channels_")){
                $this->mergeStatistics($dpath,$imageChannels);
            }else if(startsWith($file,"image.bits_")){
                $this->mergeStatistics($dpath,$imageBits);
            }else if(startsWith($file,"image.mimes_")){
                $this->mergeStatistics($dpath,$imageMimes);
            }else if(startsWith($file,"image.list_")){
                $imageCSV[]=$dpath;

            /**** AUDIO ***/
            }else if(startsWith($file,"audio.stat_")){
                $this->mergeStatistics($dpath,$audioStat);
            }else if(startsWith($file,"audio.channels_")){
                $this->mergeStatistics($dpath,$audioChannels);
            }else if(startsWith($file,"audio.bits_")){
                $this->mergeStatistics($dpath,$audioBits);
            }else if(startsWith($file,"audio.codec_")){
                $this->mergeStatistics($dpath,$audioCodec);
            }else if(startsWith($file,"audio.samplerate_")){
                $this->mergeStatistics($dpath,$audioSampleRate);
            }else if(startsWith($file,"audio.mime_")){
                $this->mergeStatistics($dpath,$audioMime);
            }else if(startsWith($file,"audio.list_")){
                $audioCSV[]=$dpath;

            }
        }
        closedir($dh);
        
        $stat['Basic.Number of Lines']=$stat['lines']; unset($stat['lines']);
        $stat['Basic.Number of Words']=$stat['words']; unset($stat['words']);
        $stat['Basic.Number of Tokens']=$stat['tok']; unset($stat['tok']);
        $stat['Basic.Number of Characters']=$stat['chars']; unset($stat['chars']);
        $stat['Basic.Number of Sentences']=$stat['sent']; unset($stat['sent']);
        $stat['Basic.Number of Annotated Documents']=$stat['documents']; unset($stat['documents']);
        $stat['Basic.Number of Raw Documents']=count($this->getFiles());
        
        $stat['Basic.Unique Tokens']=count($wordform);
        $stat['Basic.Unique Lemma']=count($lemma);
        
        if(isset($stat['IATE'])){
            $stat['Basic.Number of IATE terms']=$stat['IATE']; unset($stat['IATE']);
            $stat['Basic.Unique IATE terms']=count($iateTerms);
        }

        if(isset($stat['EUROVOCID'])){
            $stat['Basic.Number of EUROVOC IDs']=$stat['EUROVOCID']; unset($stat['EUROVOCID']);
            $stat['Basic.Unique EUROVOC IDs']=count($eurovocIds);
        }
        
        if(isset($stat['EUROVOCMT'])){
            $stat['Basic.Number of EUROVOC MTs']=$stat['EUROVOCMT']; unset($stat['EUROVOCMT']);
            $stat['Basic.Unique EUROVOC MTs']=count($eurovocMts);
        }
        
        $once=0;
        $twice=0;
        $three=0;
        foreach($wordform as $k=>$v){
            if($v===1)$once++;
            else if($v===2)$twice++;
            else if($v===3)$three++;
        }
        
        $stat['Basic.Hapax Legomena']=$once;
        $stat['Basic.Dis Legomena']=$twice;
        $stat['Basic.Tris Legomena']=$three;
        
        $uniqueLemmaUPOS=[];
        foreach($lemmaUPOS as $k=>$v){
            $lu=explode("_",$k,2);
            if(!isset($uniqueLemmaUPOS[$lu[0]]))$uniqueLemmaUPOS[$lu[0]]=1;
            else $uniqueLemmaUPOS[$lu[0]]++;        
        }
        foreach($uniqueLemmaUPOS as $k=>$v){
            $stat["Unique Lemma.$k"]=$v;
        }
        
        $h=0.0;
        $total=0;
        foreach($charsArr as $k=>$v){ $total+=$v;}
        $stat['Basic.Number of Romanian letters']=$total;
        foreach($charsArr as $k=>$v){
            if($v===0)continue;
            $p=floatval($v)/floatval($total);
            $h+=(-$p)*log($p,2.0);
        }
        
        $stat['Entropy.Romanian letters']=$h;
        
        sort($textCSV);
        file_put_contents($base_dir."/statistics/text_list.csv","file,size,lines,words,chars,all_uppercase,all_lowercase\n");
        foreach($textCSV as $csv){
            file_put_contents($base_dir."/statistics/text_list.csv",file_get_contents($csv),FILE_APPEND);
        }
        
        sort($conllupCSV);
        file_put_contents($base_dir."/statistics/conllup_list.csv",
            "file,sentences,tokens,unique_word_forms,unique_lemmas,".
            implode(",",["ADJ","ADP","ADV","AUX","CCONJ","DET","INTJ","NOUN","NUM","PART","PRON","PROPN","PUNCT","SCONJ","SYM","VERB","X"]).
            ",ne_tokens,ne_start_tokens\n");
        foreach($conllupCSV as $csv){
            file_put_contents($base_dir."/statistics/conllup_list.csv",file_get_contents($csv),FILE_APPEND);
        }
        
        /**** IMAGES ****/
        if(count($imageStat)>0){
            $stat=array_merge($stat, $imageStat);
            $minW=-1; $maxW=-1;$minH=-1;$maxH=-1;
            foreach($imageWidths as $k=>$v){
                $stat["image.width.$k"]=$v;
                if($minW==-1 || $minW>$k)$minW=$k;
                if($maxW==-1 || $maxW<$k)$maxW=$k;
            }
            $stat["image.min_width"]=$minW;
            $stat["image.max_width"]=$maxW;
            foreach($imageHeights as $k=>$v){
                $stat["image.height.$k"]=$v;
                if($minH==-1 || $minH>$k)$minH=$k;
                if($maxH==-1 || $maxH<$k)$maxH=$k;
            }
            $stat["image.min_height"]=$minH;
            $stat["image.max_height"]=$maxH;
            foreach($imageSizes as $k=>$v)$stat["image.size.$k"]=$v;
            foreach($imageChannels as $k=>$v)$stat["image.channels.$k"]=$v;
            foreach($imageBits as $k=>$v)$stat["image.bits.$k"]=$v;
            foreach($imageMimes as $k=>$v)$stat["image.mime.$k"]=$v;
            sort($imageCSV);
            file_put_contents($base_dir."/statistics/image_list.csv","file,width,height,mime,channels,bits,filesize\n");
            foreach($imageCSV as $csv){
                file_put_contents($base_dir."/statistics/image_list.csv",file_get_contents($csv),FILE_APPEND);
            }
        }
        
        /**** AUDIO ****/
        if(count($audioStat)>0){
            $audioStat['audio.duration_formatted']=getTimeStrFromMS(round($audioStat['audio.duration_seconds']*1000));
            $audioStat['audio.duration_seconds']=sprintf("%0.2f",$audioStat['audio.duration_seconds']);
            $stat=array_merge($stat, $audioStat);
            foreach($audioChannels as $k=>$v)$stat["audio.channels.$k"]=$v;
            foreach($audioBits as $k=>$v)$stat["audio.bits_per_sample.$k"]=$v;
            foreach($audioCodec as $k=>$v)$stat["audio.codec.$k"]=$v;
            foreach($audioSampleRate as $k=>$v)$stat["audio.sample_rate.$k"]=$v;
            foreach($audioMime as $k=>$v)$stat["audio.mime.$k"]=$v;

            sort($audioCSV);
            file_put_contents($base_dir."/statistics/audio_list.csv","file,duration_seconds,duration_formatted,channels,bits_per_sample,codec,sample_rate,mime,filesize\n");
            foreach($audioCSV as $csv){
                file_put_contents($base_dir."/statistics/audio_list.csv",file_get_contents($csv),FILE_APPEND);
            }
        }

        ksort($stat);

        file_put_contents($base_dir."/list_statistics.json",json_encode($stat));

        arsort($wordform);
        $fp=fopen($base_dir."/statistics/list_wordform.csv","w");
        foreach($wordform as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($wordFirstLow);
        $fp=fopen($base_dir."/statistics/list_wordfirstlower.csv","w");
        foreach($wordFirstLow as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($wordFirstUpper);
        $fp=fopen($base_dir."/statistics/list_wordfirstupper.csv","w");
        foreach($wordFirstUpper as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($wordformdf);
        $fp=fopen($base_dir."/statistics/list_wordformdf.csv","w");
        foreach($wordformdf as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($iateTerms);
        $fp=fopen($base_dir."/statistics/list_iate_terms.csv","w");
        foreach($iateTerms as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($iateTermsdf);
        $fp=fopen($base_dir."/statistics/list_iate_termsdf.csv","w");
        foreach($iateTermsdf as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($eurovocIds);
        $fp=fopen($base_dir."/statistics/list_eurovoc_ids.csv","w");
        foreach($eurovocIds as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($eurovocIdsdf);
        $fp=fopen($base_dir."/statistics/list_eurovoc_idsdf.csv","w");
        foreach($eurovocIdsdf as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($eurovocMts);
        $fp=fopen($base_dir."/statistics/list_eurovoc_mt.csv","w");
        foreach($eurovocMts as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($eurovocMtsdf);
        $fp=fopen($base_dir."/statistics/list_eurovoc_mtdf.csv","w");
        foreach($eurovocMtsdf as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);

        arsort($lemma);
        $fp=fopen($base_dir."/statistics/list_lemma.csv","w");
        foreach($lemma as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);
        
        arsort($charsArr);
        $fp=fopen($base_dir."/statistics/list_letters.csv","w");
        foreach($charsArr as $k=>$v)fputcsv($fp,[$k,$v]);
        fclose($fp);
        
        arsort($lemmaUPOS);
        $fp=fopen($base_dir."/statistics/list_lemma_upos.csv","w");
        foreach($lemmaUPOS as $k=>$v){
            $lu=explode("_",$k,2);
            fputcsv($fp,[$lu[0],$lu[1],$v]);
        }
        fclose($fp);

        return $stat;    
    
    }

    public function openFile($name,$mode="r"){
        if(!$this->isValidName($name))return false;

        $dir=$this->getFolderPath();
        if($dir===false)return false;

        $dir.="/files";
        if(!is_dir($dir))return false;
        
        $fpath=$dir."/$name";
        if(!is_file($fpath))return false;
        
        return fopen($fpath,$mode);
    }
    
    public function getFilePath($name,$internalDir,$checkExisting=true,$returnMetaPath=false){
        if(!$this->isValidName($name))return false;

        $dir=$this->getFolderPath();
        if($dir===false)return false;

        $dir.="/$internalDir";
        if(!is_dir($dir))return false;
        
        $fpath=$dir."/$name";
        if($checkExisting && !is_file($fpath))return false;
        if(!$returnMetaPath)return $fpath;
        
        $mpath=$this->getFolderPath()."/meta/${name}.meta";
        return ["file"=>$fpath, "meta"=>$mpath];
    }

    public function getFilePathBasicTagging($name){ global $DirectoryAnnotated; return $this->getFilePath($name,$DirectoryAnnotated);}
    public function getFilePathStandoff($name){ return $this->getFilePath($name,"standoff");}

    public function openFileBasicTagging($name,$mode="r"){
        $fpath=$this->getFilePathBasicTagging($name);
        if($fpath===false)return false;
        return fopen($fpath,$mode);
    }
    
    public function openFileStandoff($name,$mode="r"){
        $fpath=$this->getFilePathStandoff($name);
        if($fpath===false)return false;
                
        return fopen($fpath,$mode);
    }


    public function getFileMeta($name){
        if(!$this->isValidName($name))return false;

        $dir=$this->getFolderPath();
        if($dir===false)return false;

        $dir.="/meta";
        if(!is_dir($dir))return false;
        
        $fpath=$dir."/${name}.meta";
        if(!is_file($fpath))return false;
        
        return json_decode(file_get_contents($fpath),true);
    }
    
    public function getMetadataProfile(){
        $fpath=$this->getFolderPath()."/standoff/metadata.json";
        if(!is_file($fpath))return [];
        
        return json_decode(file_get_contents($fpath),true);
    
    }
    
    public function getRights(){
        if(!isset($this->data['rights']) || !is_array($this->data['rights']))return [];
        return $this->data['rights'];
    }
    
    public function addRights($pattern, $rights, $user){
        $username="";
        if($user!==false)$username=$user->getUsername();
        if(!isset($this->data['rights']) || !is_array($this->data['rights']))$this->data['rights']=[];
        $this->data['rights'][]=[
            "pattern" => $pattern,
            "rights" => $rights,
            "added_by" => $username,
            "date" => date("Y-m-d")
        ];
    }

    public function editRights($old_pattern, $old_rights, $pattern, $rights, $user){
        $username="";
        if($user!==false)$username=$user->getUsername();
        if(!isset($this->data['rights']) || !is_array($this->data['rights']))$this->data['rights']=[];
        foreach($this->data['rights'] as $k=>$r){
            if($r['pattern']==$old_pattern && $r['rights']==$old_rights){
                $this->data['rights'][$k]=[
                    "pattern" => $pattern,
                    "rights" => $rights,
                    "added_by" => $username,
                    "date" => date("Y-m-d")
                ];
                break;
            }
        }
    }

    public function deleteRights($old_pattern, $old_rights, $user){
        if(!isset($this->data['rights']) || !is_array($this->data['rights']))$this->data['rights']=[];
        
        $ndata=[];
        foreach($this->data['rights'] as $k=>$r){
            if($r['pattern']!=$old_pattern || $r['rights']!=$old_rights){
                $ndata[]=$r;
            }
        }
        $this->data['rights']=$ndata;
    }
    
    public function hasRights($rights, $checkuser=false){
        global $user;
        if($checkuser===false){
            if(!isset($user) || $user===NULL)return true;
            $checkuser=$user;
        }

        if(!isset($this->data['rights']) || !is_array($this->data['rights'])){return true;}
        
        if($this->data['created_by']==$checkuser->getUsername()){return true;}
        
        if($checkuser->hasRights("admin")){return true;}
                
        $levels=["read"=>1, "readwrite"=>2, "admin"=>3];
        if(!isset($levels[$rights]))return false;
        
        foreach($this->data['rights'] as $r){
            if(
                isset($levels[$r['rights']]) && 
                $levels[$r['rights']]>=$levels[$rights] && 
                @preg_match("/^${r['pattern']}\$/i",$checkuser->getUsername())
            ){
                return true;
            }
        }
        
        return false;
    }

}