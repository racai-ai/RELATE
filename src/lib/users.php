<?php

class Users {
    private $path;
    
    public function __construct($path=null){
        global $LIB_PATH;
        
        if($path===null || $path===false || !is_string($path) || strlen($path)==0){
            $this->path="${LIB_PATH}/../DB/users" ;
        }else
            $this->path = $path;
    }

	private function internal_listSubFolder($dir){
        if(!is_dir($dir))return [];

        $dh = opendir($dir);
        if($dh===false)return [];
        
		$users=[];
		
        while (($file = readdir($dh)) !== false) {
            $dpath="$dir/$file";
            if(!is_dir($dpath) || $file=="." || $file=="..")continue;
			
            $fdata="$dpath/user_data.json";
            if(!is_file($fdata)){ // maybe sub-folder
				$users=array_merge($users,$this->internal_listSubFolder($dpath));
				continue;
			}

			$udata=json_decode(file_get_contents($fdata),true);
            $u=new User();
            if($u->loadUser($udata['username'])){            
                $users[]=[
					"username"=>$udata['username'],
					"name"=>$u->getProfile('name',''),
					"created_time"=>$udata['created_time'],
					"rights"=>implode(",",$u->getRights())
				];
            }
        }
        closedir($dh);
		
		return $users;
	}

	public function getList(){
        $dir=$this->path;
        
        if(!is_dir($dir))return [];
		
		return $this->internal_listSubFolder($this->path);
	}
}


?>