<?php

class Task {
    private $corpus;
    private $data;
    
    public function __construct($corpus=null,$data=null){
        $this->data = $data;
        $this->corpus = $corpus;
    }
    
    public function clear(){ $this->data=[]; }
    
    public function getAllByCorpus(){
        if($this->corpus===null || $this->corpus===false || !is_object($this->corpus))return [];
        
        $dir=$this->corpus->getFolderPath();
        if($dir===false)return [];
        
        $dir.="/tasks";
        $ftasks=$this->getTasksFromFolder($dir."/new");
        $ftasks=array_merge($ftasks,$this->getTasksFromFolder($dir."/old"));

        $tasks=[];
        foreach($ftasks as $ft){
            $tasks[]=json_decode(file_get_contents($ft),true);
        }
        
        return $tasks;        
    }
    
    public function getTasksFromFolder($dir){
        $tasks=[];
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if(is_file($dir."/".$file) && endsWith($file,".task"))
                        $tasks[]="$dir/$file";
                }
                closedir($dh);
            }
        }
        return $tasks;    
    }
    
    public function addTask($data){
        if($this->corpus===null || $this->corpus===false || !is_object($this->corpus))return false;
        
        $dir=$this->corpus->getFolderPath(true);
        if($dir===false)return false;
        
        $dir.="/tasks";
        @mkdir($dir);
        $dir.="/new";
        @mkdir($dir);
        
        if(!is_dir($dir))return false;
        
        global $user;
        $un=$user->getUsername();
        if($un===false || !is_string($un) || strlen($un)==0)return false;
        
        $fname=uniqid($un,true).".task";
        
        $data['status']="NEW";
        file_put_contents("$dir/$fname",json_encode($data));
        
        return true;
    }


}