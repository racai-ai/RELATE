<?php

class Projects {
    private $path;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/projects" ;
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
            $fdata="$dpath/project.json";
            if(!is_file($fdata))continue;

            $c=new Project($this,$file);
            if($c->loadData()){
                if($c->hasRights("read")){
                    $corpora[]=$c->getAllData();
                }
            }
        }
        closedir($dh);
        
        return $corpora;    
    }
    
    public function getPath(){ return $this->path; }
    
    public function getOtherWorkTotal($exceptProject,$year,$month){
        $total=[];
        foreach($this->getList() as $prjlistentry){
            $prjname=$prjlistentry['name'];
            if($prjname==$exceptProject)continue;
            $prj=new Project($this,$prjname);
            $prj->loadData();
            
            if($prj->getData("ignore",0)===1)continue;
            
            $work=$prj->getWorkDayTotal($year,$month);
            foreach($work as $per=>$w){
                if(!isset($total[$per]))$total[$per]=[];
                for($i=1;$i<=31;$i++){
                    if(!isset($total[$per][$i]))$total[$per][$i]=0;
                    $total[$per][$i]+=$w[$i];
                }
            }
        }
        return $total;
                
    }
}