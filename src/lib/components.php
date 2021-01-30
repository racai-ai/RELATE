<?php

class Components {
    private $components;

    public function getComponents(){return $this->components;}

    public function clear(){
        $this->components=[];
    }
    
    public function loadComponent($cname,$cpath){
        $data=json_decode(file_get_contents("$cpath/component.json"),true);
        if(isset($data['enabled']) && $data['enabled']===false)return ;
        
        $data['cpath']=$cpath;
        $data['cname']=$cname;
        
        $this->components[$cname]=$data;
    }
    
    public function load(){
        global $LIB_PATH;
        
        $this->clear();
        
        $path="${LIB_PATH}/../components";
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    $cpath="$path/$file";
                    if(is_dir($cpath) && !startsWith($cpath,".") && is_file("$cpath/component.json")){
                        $this->loadComponent($file,$cpath);
                    }
                }
                closedir($dh);
            }
        }

        uasort($this->components,array($this,"component_sort_cmp"));

        return true;
    }
    
    public function component_sort_cmp($a,$b){
        $o1=0; if(isset($a['order']))$o1=$a['order'];
        $o2=0; if(isset($b['order']))$o2=$b['order'];
        
        return $o1-$o2;
    }
    
		public function getMenu(){
				$menu=array();
				
				foreach($this->components as $comp){
						if(!isset($comp['menu']))continue;
						foreach($comp['menu'] as $m){
								if(!isset($m['label']) || isset($m['enabled']) && $m['enabled']===false)continue;
								$l=$m['label'];
								if(!isset($menu[$l]))$menu[$l]=$m;
								else if(isset($m['menu']))
										$menu[$l]['menu']=array_merge($menu[$l]['menu'],$m['menu']);
						}
						//$menu=array_merge($menu,$comp['menu']);
				}
				
				return array_values($menu);
		
		}
		
		public function registerHandlers(){
				foreach($this->components as $n=>$comp){
						if(!isset($comp['handlers']))continue;
						foreach($comp['handlers'] as $handler){
								if(!isset($handler['path']) || !isset($handler['script']))continue;
								$path=$handler['path'];
								$script="components/${n}/${handler['script']}";
								$data=false; if(isset($handler['data']))$data=$handler['data'];
								registerHandler($path,$script,$data);
						}
				}
		}

}

?>