<?php

function EDUCATIONRESOURCE_UASORT_FILES($f1,$f2){return strcasecmp($f1['name'],$f2['name']);}

class EducationResource {
    private $repository;
    private $data;
    private $name;
	private $files;
	
    public function __construct($repository,$name,$data=null){
        $this->data = $data;
        $this->name = $name;
		$this->files=false;
        $this->repository = $repository;
    }
    
    public function getName(){return $this->name;}
	
    public function clear(){ $this->data=[]; }

    public function isValidName($name=false){
        if($name===false)
            $un=$this->name;
        else $un=$name;
        return strlen($un)>3 && preg_match("/[^-_a-zA-Z0-9ăîâșțĂÎÂȘȚ@(). ]/",$un)===0 && $un[0]!=' ' && $un[strlen($un)-1]!=' ' && $un[0]!='.' && $un[0]!='-' && $un[0]!='@' && strlen($un)<200;    
    }
    
    public function loadData(){
        if(!$this->isValidName()){$this->clear();return false;}
        
        $fdata=$this->repository->getPath()."/".$this->name."/resource.json";
        if(!is_file($fdata)){$this->clear();return false;}

        $this->data=json_decode(file_get_contents($fdata),true);
        $this->data['name']=$this->name;
        return true;
    }
    
    public function getLang(){
    		if(isset($this->data['lang']))return $this->data['lang'];
    		return false;
	}
	
	public function hasLang($lang){
		if(isset($this->data['lang']) && is_array($this->data['lang'])){
			$l=array_flip($this->data['lang']);
			if(isset($l[$lang]) || isset($l['*']))return true;
		}
		return false;
	}
	
	public function hasTag($tag){
		if(isset($this->data['tags']) && is_array($this->data['tags'])){
			$l=array_flip($this->data['tags']);
			if(isset($l[$tag]) || isset($l['*']))return true;
		}
		return false;
	}		
	
	public function hasAnyTags($tags){
        foreach($tags as $tag)
            if($this->hasTag($tag))return true;
        return false;
	}		

    public function getFolderPath($create=false){
        if(!$this->isValidName($this->name))return false;
    
        $fdata=$this->repository->getPath();
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

        $fdata.="/resource.json";
        
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
	
	public function getFiles(){
		if($this->files!==false)return $this->files;
		
        $fdata=$this->getFolderPath(true);
        if($fdata===false)return false;
		
		$files=[];
		if (is_dir($fdata)) {
			if ($dh = opendir($fdata)) {
				while (($file = readdir($dh)) !== false) {
					$path="$fdata/$file";
					if(is_file($path) && !endsWith($file,".json")){
						$size=filesize($path);
						if($size>0){
							$unit="b";
							if($size>1024){$size/=1024.0;$unit="Kb";}
							if($size>1024){$size/=1024.0;$unit="Mb";}
							if($size>1024){$size/=1024.0;$unit="Gb";}
							if($size>1024){$size/=1024.0;$unit="Tb";}
							if($size==0 && $unit=="b")$unit="";
							$size=round($size,2)." ".$unit;
							
							$files[$file]=["file"=>$file,"size"=>$size];
						}
					}
				}
				closedir($dh);
			}
		}		
		
		$this->files=$files;
		
		return $files;
	}
	
	public function hasType($check){
		$arr=$this->getData("type",[]);
		if(count(array_intersect($arr,$check))>0)return true;
		return false;
	}
    
	public function hasMedia($check){
		$arr=$this->getData("media",[]);
		if(count(array_intersect($arr,$check))>0)return true;
		return false;
	}
	
	public function hasSearch($check){
		$text=strtolower(implode(" ",[
			$this->getData("acronym",""),
			$this->getData("title",""),
			$this->getData("description_short",""),
			$this->getData("description_long",""),
			implode(" ",$this->getData("authors",[])),
			implode(" ",$this->getData("organisation",[])),
			implode(" ",$this->getData("tasks",[])),
			implode(" ",$this->getData("keywords",[])),
		]));

		if(strpos($text,strtolower($check))!==false)return true;
		
		return false;
	}
}