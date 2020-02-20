<?php

class Settings {

    private $settings;

    public function clear(){
        $this->settings=[];
    }
    
    
    public function load(){
        global $LIB_PATH;
        
        $this->clear();
        
        $path="${LIB_PATH}/../DB/settings.json";

        if(!is_file($path))return false;
        $this->settings=json_decode(file_get_contents($path),true);

        return true;
    }
    
    public function get($key,$default=false){
        if(!isset($this->settings[$key]))return $default;
        return $this->settings[$key];
    }
    
}


?>