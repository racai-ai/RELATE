<?php

class Services {
    private $path;
	private $srv;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
		$this->srv=false;
		
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/services" ;
        }else
            $this->path = $path;
    }
	
	public function getServices(){
		if($this->srv===false)$this->load();
		return $this->srv;
	}
    
    public function load(){
		$this->srv=[];
        $dir=$this->path;
        
        if(!is_dir($dir))return $this->srv;
        
        $dh = opendir($dir);
        if($dh===false)return $this->srv;
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_dir($dpath))continue;
            $fdata="$dpath/service.json";
            if(!is_file($fdata))continue;

            $c=new Service($this,$file);
            if($c->loadData()){            
                $this->srv[$file]=$c;
            }
        }
        closedir($dh);
        
        return $this->srv;    
    }
    
    public function getPath(){ return $this->path; }
	
	public function getByName($name){
		if($this->srv===false)$this->load();

		if(isset($this->srv[$name]))return $this->srv[$name];
		return false;
	}
	
	public function getBy_Lang_Task_StandardInput($lang,$task,$input){
		if($this->srv===false)$this->load();

		$ret=[];
		foreach($this->srv as $s){
			if($s->hasLang($lang) && $s->hasTask($task) && $s->hasStandardInput($input))
				$ret[]=$s;
		}
		return $ret;
	}
}