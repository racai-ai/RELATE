<?php

class Project {
    private $projects;
    private $data;
    private $name;
    private $work;
    private $reports;
    
    public function __construct($projects,$name,$data=null){
        $this->data = $data;
        $this->name = $name;
        $this->projects = $projects;
        $this->work=false;
        $this->reports=false;
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
        
        $fdata=$this->projects->getPath()."/".$this->name."/project.json";
        if(!is_file($fdata)){$this->clear();return false;}
        
        $this->data=json_decode(file_get_contents($fdata),true);
        $this->data['name']=$this->name;
        return true;
    }
        
    public function getFolderPath($create=false){
        if(!$this->isValidName($this->name))return false;
    
        $fdata=$this->projects->getPath();
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

        $fdata.="/project.json";
        
        if(is_file($fdata) && !$overwrite)return false;
        
        $this->data['name']=$this->name;
        file_put_contents($fdata,json_encode($this->data));

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
    
    public function getWork(){
        if($this->work!==false)return $this->work;
        
        $fdata=$this->getFolderPath()."/work.json";
        if(!is_file($fdata)){return [];}
        
        $this->work=json_decode(file_get_contents($fdata),true);
        return $this->work;
    }
    
    public function addWork($w,$user){
        $this->getWork(); // loads the work file if not loaded
        $username="";
        if($user!==false)$username=$user->getUsername();
        $w["added_by"]=$username;
        $w["added_date"]=date("Y-m-d");
        if($this->work===false || !is_array($this->work))$this->work=[];
        $this->work[]=$w;
    }        
    
    public function saveWork(){
        $fdata=$this->getFolderPath(true);
        if($fdata===false)return false;

        $fdata.="/work.json";
        file_put_contents($fdata,json_encode($this->work));

        return true;
    }
    
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

    public function addTeamMember($member,$user){
        if(!isset($this->data['team']) || !is_array($this->data['team']))$this->data['team']=[];
        $username="";
        if($user!==false)$username=$user->getUsername();
        $member["added_by"]=$username;
        $member["added_date"]=date("Y-m-d");
        $this->data['team'][]=$member;
    }
    
    public function getTeamMembers(){
        if(!isset($this->data['team']) || !is_array($this->data['team']))$this->data['team']=[];
        return $this->data['team'];
    }

    public function addWorkItem($member,$user){
        if(!isset($this->data['workitems']) || !is_array($this->data['workitems']))$this->data['workitems']=[];
        $username="";
        if($user!==false)$username=$user->getUsername();
        $member["added_by"]=$username;
        $member["added_date"]=date("Y-m-d");
        $this->data['workitems'][]=$member;
    }
    
    public function getWorkItems(){
        if(!isset($this->data['workitems']) || !is_array($this->data['workitems']))$this->data['workitems']=[];
        return $this->data['workitems'];
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
    
    public function getWorkBreakdown($year, $month){
        $dint=$year*100+$month;
        $witems=[""];
        foreach($this->getWorkItems() as $wi)$witems[]=$wi['name'];
        sort($witems);

        $work=[];
        foreach($this->getWork() as $w){
            $dw=intval(str_replace("-","",substr($w['date'],0,7)));
            if($dw!=$dint)continue;

            $pname=$w['name'];
            if(!isset($work[$pname])){
                $work[$pname]=["description"=>""];
                foreach($witems as $wi){
                    $work[$pname][$wi]=["description"=>""];
                    for($i=1;$i<=31;$i++)$work[$pname][$wi][$i]=0;
                }
            }

            $wi="";
            if(isset($w['workitem']))$wi=$w['workitem'];
            if(!isset($work[$pname][$wi]))$wi="";

            if(isset($w['description']) && strlen($w['description'])>0){
                if(strlen($work[$pname]['description'])>0)$work[$pname]['description'].="; ";
                $work[$pname]['description'].=$w['description'];

                if(strlen($work[$pname][$wi]['description'])>0)$work[$pname][$wi]['description'].="; ";
                $work[$pname][$wi]['description'].=$w['description'];
            }
            
            $work[$pname][$wi][intval(substr($w['date'],8))]+=$w['hours'];
        }
        
        return $work;
    }
    
    public function getWorkDayTotal($year,$month){
        $work=$this->getWorkBreakdown($year,$month);
        $total=[];
        foreach($work as $per=>$widata){
            unset($widata['description']);
            $total[$per]=[];
            for($i=1;$i<=31;$i++){
                $total[$per][$i]=0;
                foreach($widata as $w){
                    
                    $total[$per][$i]+=$w[$i];
                }
            }
        }
        return $total;
    }
    
    public function syncWork($work, $year, $month, $user){
        $username="";
        if($user!==false)$username=$user->getUsername();        
        
        $dint=$year*100+$month;
        $witems=[""];
        foreach($this->getWorkItems() as $wi)$witems[]=$wi['name'];
        sort($witems);
        
        $newWork=[];
        foreach($this->getWork() as $w){
            $dw=intval(str_replace("-","",substr($w['date'],0,7)));
            if($dw!=$dint){$newWork[]=$w;continue;}

            $pname=$w['name'];
            if(!isset($work[$pname])){$newWork[]=$w;continue;}
        }
        
        foreach($work as $person => $wdata){
            if(isset($wdata['description']) && strlen($wdata['description'])>0){
                $newWork[]=["name"=>$person, "workitem"=>"", "hours"=>0, "description"=>$wdata['description'], "date"=>sprintf("%4d-%02d-%02d",$year,$month,1), "added_by"=>$username, "added_date"=>date("Y-m-d")];
            }
            //var_dump($wdata);
            //var_dump($witems);
            foreach($witems as $wi){
                if(!isset($wdata[$wi]))continue;
                for($i=1;$i<=31;$i++){
                    if(isset($wdata[$wi][$i]) && intval($wdata[$wi][$i])>0){
                        $newWork[]=["name"=>$person, "workitem"=>$wi, "hours"=>intval($wdata[$wi][$i]), "description"=>"", "date"=>sprintf("%4d-%02d-%02d",$year,$month,$i), "added_by"=>$username, "added_date"=>date("Y-m-d")];
                    }

                }
            }
        }
        
        $this->work=$newWork;
        $this->saveWork();
    }
                

}