<?php

class Corpora {
    private $path;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/corpora" ;
        }else
            $this->path = $path;
    }
    
    public function getList(){
        $corpora=[];
    
        $dir=$this->path;
        
        if(!is_dir($dir))return [];
        
        $dh = opendir($dir);
        if($dh===false)return [];
        
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_dir($dpath))continue;
            $fdata="$dpath/corpus.json";
            if(!is_file($fdata))continue;

            $c=new Corpus($this,$file);
            if($c->loadData()){            
                $corpora[]=$c->getAllData();
            }
        }
        closedir($dh);
        
        return $corpora;    
    }
    
    public function getPath(){ return $this->path; }
}