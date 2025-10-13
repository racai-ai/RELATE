<?php

class ProjectsOverall {
    private $path;
    private $reports;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/projects_overall" ;
        }else
            $this->path = $path;
        
        $this->reports=false;
    }
    
    public function getFolderPath(){return $this->path;}
    
    public function getReports(){
        if($this->reports!==false)return $this->reports;
        
        $fdata=$this->getFolderPath()."/reports.json";
        if(!is_file($fdata)){return [];}
        
        $this->reports=json_decode(file_get_contents($fdata),true);
        return $this->reports;
    }
    
    public function getReportsGenerated(){
        $fdata=$this->getFolderPath()."/reports_gen";
        if(!is_dir($fdata)){return [];}
        
        $ret=[];
        if ($dh = opendir($fdata)) {
            while (($file = readdir($dh)) !== false) {
                $fpath="$fdata/$file";
                if(is_file($fpath))$ret[]=["name"=>$file];
            }
            closedir($dh);
        }        
        
        return $ret;
    }
    
    public function addUploadedReport($data,$user){
        $this->getReports();
        if($this->reports===false)$this->reports=[];
        
        @mkdir($this->getFolderPath());
        
        $fdata=$this->getFolderPath()."/reports";
        @mkdir($fdata);

        $dpath="$fdata/${data['name']}";

        $file=$_FILES['file']['tmp_name'];
        if(move_uploaded_file($file,$dpath)!==true)return false;
        
        $username="";
        if($user!==false)$username=$user->getUsername();
        $data["added_by"]=$username;
        $data["added_date"]=date("Y-m-d");
        $this->reports[]=$data;
        
        $fdata=$this->getFolderPath(true);
        $fdata.="/reports.json";
        file_put_contents($fdata,json_encode($this->reports));

        return true;
        
    }    

}