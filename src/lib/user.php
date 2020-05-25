<?php

class User {

    private $username;
    private $profile;
    private $data;
    private $rights;

    public function clear(){
        $this->username=false;
        $this->profile=[];
        $this->data=[];
        $this->rights=[];
    }
    
    public function logout(){
        $this->clear();
        unset($_SESSION['username']);
    }

    public function initFromSession(){
        $this->clear();
        
        if(isset($_SESSION['username'])){
            if(!$this->loadUser($_SESSION['username'])){
                $this->clear();
                unset($_SESSION['username']);
            }
        }
    }
    
    public function isValidUsernameString($un){
        return strlen($un)>2 && preg_match("/[^a-z0-9@.-]/",$un)===0 && $un[0]!='.' && $un[0]!='-' && $un[0]!='@' && strlen($un)<200;
    }
    
    public function getUserPath($un,$create){
        global $LIB_PATH;
        
        $un=strtolower($un);
        if(!$this->isValidUsernameString($un))return false;
        
        $path="${LIB_PATH}/../DB";
        if($create)@mkdir($path);
        $path.="/users";
        if($create)@mkdir($path);
        $path.="/".substr($un,0,1);
        if($create)@mkdir($path);
        $path.="/".substr($un,0,2);
        if($create)@mkdir($path);
        $path.="/${un}";
        if($create)@mkdir($path);

        return $path;    
    }
    
    public function loadUser($un){
        $path=$this->getUserPath($un,false);
        if($path===false)return false;
        
        $dataFile=$path."/user_data.json";
        $profileFile=$path."/user_profile.json";
        $rightsFile=$path."/user_rights.json";
        
        if(!is_file($dataFile))return false;
        $this->data=json_decode(file_get_contents($dataFile),true);
        
        if(!isset($this->data['rand']) || !isset($this->data['hash']))return false;
        
        $this->profile=[];
        if(is_file($profileFile))
            $this->profile=json_decode(file_get_contents($profileFile),true);
            
        $this->rights=[];
        if(is_file($rightsFile))
            $this->rights=json_decode(file_get_contents($rightsFile),true);
        $this->rights=array_flip($this->rights);

        $this->username=$un;

        return true;
    }
    
    public function hasAccess($rights){
        if(!is_array($rights) || empty($rights))return true;
        
        foreach($rights as $r)
          if(isset($this->rights[$r]))return true;
          
        return false;
    }
    
    public function userExists($un){
        $path=$this->getUserPath($un,false);
        if($path===false)return false;
        
        $dataFile=$path."/user_data.json";
        
        return is_file($dataFile);
        
    }
    
    public function getProfileHtml($key,$default){
        return htmlspecialchars($this->getProfile($key,$default));
    }
    
    public function getProfileJS($key,$default){
				return trim(json_encode($this->getProfile($key,$default)),"\"");
		}
    
    public function getProfile($key,$default){
        $ret=$default;
        if(isset($this->profile[$key]))$ret=$this->profile[$key];
        return $ret;
		}
		
		public function setProfile($key,$value){
				$this->profile[$key]=$value;
		}
    
    public function isLoggedIn(){
        return $this->username!==false;
    }
    
    public function doLogin(){
        $un=strtolower($_REQUEST['username']);
        $pass=$_REQUEST['password'];
        
        unset($_SESSION['username']);
        
        if(!$this->loadUser($un)){
            $this->clear();
            return false;
        }
        
        
        $r=$this->data['rand'];
        $h=hash('sha512',$r.":".$un.$pass);
        if($h!=$this->data['hash']){
            $this->clear();
            return false;
        }
        
        $_SESSION['username']=$un;
        
        $this->loadUser($un);
        
        $this->writeHistory("LOGIN");
        
        return true;
    }
    
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function create($data){
        $un=$data['username'];
        $rand=$this->generateRandomString();
        
        $udata=[
            "username" => $un,
            "rand" => $rand,
            "hash" => hash('sha512',$rand.":".$un.$data['password']),
            "created_time" => strftime("%Y-%m-%d %H:%M:%S"),
            "created_ip" => $_SERVER['REMOTE_ADDR'],
            "created_browser" => $_SERVER['HTTP_USER_AGENT']
        ];
        
        $profile=[
            "name" => $data['name'],
            "update_time" => $udata['created_time'],
            "update_ip" => $_SERVER['REMOTE_ADDR'],
            "update_browser" => $_SERVER['HTTP_USER_AGENT']
        ];
        
        $rights=["user"];
        
        $path=$this->getUserPath($un,true);
        if($path===false)return false;
        
        $dataFile=$path."/user_data.json";
        $profileFile=$path."/user_profile.json";
        $rightsFile=$path."/user_rights.json";
        
        file_put_contents($dataFile,json_encode($udata));
        file_put_contents($profileFile,json_encode($profile));
        file_put_contents($rightsFile,json_encode($rights));
        
        return true;
    }
    
    public function writeHistory($action,$params=""){
        if($this->username===false)return false;
        
        $path=$this->getUserPath($this->username,false);
        if($path===false)return false;   
        
        $histFile=$path."/user_hist.json";
        $h=[
          "t"=>strftime("%Y-%m-%d %H:%M:%S"),
          "ip" => $_SERVER['REMOTE_ADDR'],
          "browser" => $_SERVER['HTTP_USER_AGENT'],
          "action" => $action,
          "params" => $params
        ];
        
        @file_put_contents($histFile,json_encode($h)."\n",FILE_APPEND);
        return true;
    }
    
    public function saveProfile(){
        if(!$this->isLoggedIn())return false;
        
        $path=$this->getUserPath($this->username,false);
        if($path===false)return false;   
        
        $fpath=$path."/user_profile.json";
        file_put_contents($fpath,json_encode($this->profile));
		}
    
    public function saveData(){
        if(!$this->isLoggedIn())return false;
        
        $path=$this->getUserPath($this->username,false);
        if($path===false)return false;   
        
        $fpath=$path."/user_data.json";
        file_put_contents($fpath,json_encode($this->data));
    }
    
    public function changePassword($old,$new){
        if(!$this->isLoggedIn())return false;
        
        $r=$this->data['rand'];
        $h=hash('sha512',$r.":".$this->username.$old);
        if($h!=$this->data['hash'])return false;

        $this->data['hash']=hash('sha512',$r.":".$this->username.$new);
        
        $this->saveData();
        
        $this->writeHistory("PASSWORD_CHANGE");
        
        return true;
    }
    
    public function getUsername(){ return $this->username; }
}


?>