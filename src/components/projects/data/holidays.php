<?php

class Holidays {
    private $data;
    private $path;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/projects_holidays.json" ;
        }else
            $this->path = $path;    
        
        if(is_file($this->path)){
            $this->data=json_decode(file_get_contents($this->path),true);
        }else $this->data=[];
        
    }
        
    public function getList(){ return $this->data; }
    
    public function isHoliday($year, $month, $day){
        $ds=sprintf("%4d-%02d-%02d",$year,$month,$day);
        foreach($this->data as $hol){
            if($hol['date']==$ds)return true;
        }
        return false;
    }
        
    public function add($hol, $user){
        $username="";
        if($user!==false)$username=$user->getUsername();
        $hol["added_by"] = $username;
        $hol["added_date"] = date("Y-m-d");
        $this->data[]=$hol;
        $this->saveData();
    }

    public function saveData(){
        file_put_contents($this->path,json_encode($this->data));
        return true;
    }

}