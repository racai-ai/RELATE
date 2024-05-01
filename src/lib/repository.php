<?php

function UASORT_REPOSITORY_CMP($a,$b){
	return strcasecmp($a->getData("acronym","").$a->getData("title",""),$b->getData("acronym","").$b->getData("title",""));
}

class Repository {
    private $path;
	private $repository;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
		$this->repository=false;
		
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/repository" ;
        }else
            $this->path = $path;
    }
	
	public function getResources(){
		if($this->repository===false)$this->load();
		return $this->repository;
	}
    
    public function load(){
		$this->repository=[];
        $dir=$this->path;
        
        if(!is_dir($dir))return $this->repository;
        
        $dh = opendir($dir);
        if($dh===false)return $this->repository;

        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_dir($dpath))continue;
            $fdata="$dpath/resource.json";
            if(!is_file($fdata))continue;

            $c=new RepositoryResource($this,$file);
            if($c->loadData()){            
                $this->repository[$file]=$c;
            }
        }
        closedir($dh);
        
        return $this->repository;    
    }
    
    public function getPath(){ return $this->path; }
	
	public function getByName($name){
		if($this->repository===false)$this->load();

		if(isset($this->repository[$name]))return $this->repository[$name];
		return false;
	}
	
	public function getResourcesFiltered($types,$media,$search,$start,$num){
		$resources=$this->getResources();
		
		uasort($resources, "UASORT_REPOSITORY_CMP");
		
		if(is_array($types) && count($types)>0){
			foreach($resources as $k=>$res)
				if(!$res->hasType($types))unset($resources[$k]);
		}
		
		if(is_array($media) && count($media)>0){
			foreach($resources as $k=>$res)
				if(!$res->hasMedia($media))unset($resources[$k]);
		}
		
		if(is_string($search) && strlen($search)>0){
			foreach($resources as $k=>$res)
				if(!$res->hasSearch($search))unset($resources[$k]);
		}
		
		$total=count($resources);

		$resources=array_slice($resources,$start,$num);

		return ["total"=>$total,"resources"=>$resources];
	}
	
}