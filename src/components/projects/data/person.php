<?php

class Person {
    private $persons;
    private $data;
    private $name;
    private $unavailable_cache;
    
    public function __construct($persons,$name,$data=null){
        $this->data = $data;
        $this->name = $name;
        $this->persons = $persons;
        $this->unavailable_cache=[];
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
        
        $fdata=$this->persons->getPath()."/".$this->name."/person.json";
        if(!is_file($fdata)){$this->clear();return false;}
        
        $this->data=json_decode(file_get_contents($fdata),true);
        $this->data['name']=$this->name;
        return true;
    }
        
    public function getFolderPath($create=false){
        if(!$this->isValidName($this->name))return false;
    
        $fdata=$this->persons->getPath();
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

        $fdata.="/person.json";
        
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
        return true;
        /*
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
        
        return false;*/
    }

    public function addUnavailability($data, $user){
        $username="";
        if($user!==false)$username=$user->getUsername();
        if(!isset($this->data['unavailability']) || !is_array($this->data['unavailability']))$this->data['unavailability']=[];
        $data["added_by"] = $username;
        $data["added_date"]=date("Y-m-d");
        
        $this->data['unavailability'][]=$data;
        
        $this->saveData(true);
    }

    public function getUnavailabilityList(){
        if(!isset($this->data['unavailability']) || !is_array($this->data['unavailability']))return [];
        return $this->data['unavailability'];
    }
    
    public function isUnavailable($y,$m,$d){
        $dint=($y*100+$m)*100+$d;
        if(!isset($this->unavailable_cache[$dint]))
            $this->unavailable_cache[$dint]=$this->isUnavailable_internal($dint);
        return $this->unavailable_cache[$dint];
    }
        
    public function isUnavailable_internal($dint){
        if(!isset($this->data['unavailability']) || !is_array($this->data['unavailability']))return false;
        
        foreach($this->data['unavailability'] as $un){
            if(!isset($un['date_start']) || !isset($un['date_end']))continue;
            $ds=intval(str_replace('-',"",$un['date_start']));
            $de=intval(str_replace('-',"",$un['date_end']));
            if($dint>=$ds && $dint<=$de)return true;
        }
        return false;
    }

}