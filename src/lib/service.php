<?php

function SERVICE_UASORT_FILES($f1,$f2){return strcasecmp($f1['name'],$f2['name']);}

class Service {
    private $services;
    private $data;
    private $name;
	
	private $runData;
    
    public function __construct($services,$name,$data=null){
        $this->data = $data;
        $this->name = $name;
        $this->services = $services;
    }
    
    public function getName(){return $this->name;}
	
	public function getRunData() {return $this->runData;}
    
    public function clear(){ $this->data=[]; }

    public function isValidName($name=false){
        if($name===false)
            $un=$this->name;
        else $un=$name;
        return strlen($un)>3 && preg_match("/[^-_a-zA-Z0-9ăîâșțĂÎÂȘȚ@(). ]/",$un)===0 && $un[0]!=' ' && $un[strlen($un)-1]!=' ' && $un[0]!='.' && $un[0]!='-' && $un[0]!='@' && strlen($un)<200;    
    }
    
    public function loadData(){
        if(!$this->isValidName()){$this->clear();return false;}
        
        $fdata=$this->services->getPath()."/".$this->name."/service.json";
        if(!is_file($fdata)){$this->clear();return false;}
        
        $this->data=json_decode(file_get_contents($fdata),true);
        $this->data['name']=$this->name;
        return true;
    }
    
    public function getLang(){
    		if(isset($this->data['lang']))return $this->data['lang'];
    		return false;
	}
	
	public function hasLang($lang){
		if(isset($this->data['lang']) && is_array($this->data['lang'])){
			$l=array_flip($this->data['lang']);
			if(isset($l[$lang]) || isset($l['*']))return true;
		}
		return false;
	}
	
	public function hasTask($task){
		if(isset($this->data['tasks']) && is_array($this->data['tasks'])){
			$l=array_flip($this->data['tasks']);
			if(isset($l[$task]) || isset($l['*']))return true;
		}
		return false;
	}		
	
	public function hasStandardInput($input){
		if(isset($this->data['standard_input']) && is_array($this->data['standard_input'])){
			$inp=$input;
			if(!is_array($input))$inp=[$input];
			$found=true;
			foreach($inp as $cin){
				if(!isset($this->data['standard_input'][$cin]) || $this->data['standard_input'][$cin]!=1){
					$found=false;
					break;
				}
			}
			return $found;
		}
		return false;
	}
		    
    public function getFolderPath($create=false){
        if(!$this->isValidName($this->name))return false;
    
        $fdata=$this->services->getPath();
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

        $fdata.="/service.json";
        
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
    
	// runner=0 => public
	// params = alternate to REQUEST/FILES, used only for input
	public function run($runner=0,$params=[]){
		$this->runData=["output.status"=>"OK","output.messages"=>[]];
		$this->doInput($params);
		if($this->runData["output.status"]=="OK")$this->doExecute($runner);
		return $this->doOutput();
	}
	
	private function addMessage($status,$msg){
		$this->runData["output.messages"][]=$msg;
		if($status=="ERROR"){$this->runData["output.status"]="ERROR"; return false;}
		return true;
	}
	
	private function doInput($params){
		if(isset($this->data['standard_input']) && is_array($this->data['standard_input'])){
			foreach($this->data['standard_input'] as $k=>$v){
				if( ($k=="audio" || $k=="text" || $k=="conllup")){
					if($v==1){
						if(is_array($params) && isset($params[$k])){
							$this->runData["input.${k}"]=$params[$k];
						}else if(isset($_FILES[$k])){
							if($_FILES[$k]['size']==0 || $_FILES[$k]['error']!==0 || !is_file($_FILES[$k]['tmp_name']))
								return $this->addMessage("ERROR","File [$k] was not uploaded. Check upload or max file size.");
							$this->runData["input.${k}"]=file_get_contents($_FILES[$k]['tmp_name']);
							@unlink($_FILES[$k]['tmp_name']);
						}else if(isset($_REQUEST[$k])){
							$this->runData["input.${k}"]=$_REQUEST[$k];
						}else{
							return $this->addMessage("ERROR","Standard input [$k] was not sent.");
						}
					}
				}else{
					return $this->addMessage("ERROR","Unknown standard_input configuration [$k]");
				}
			}
		}
		
		return true;
	}
	
	private function doExecute($runner){
		if(isset($this->data['execution']) && is_array($this->data['execution'])){
			foreach($this->data['execution'] as $ob){
				if($ob['type']=="API")$this->doExecuteAPI($ob,$runner);
				else 
					return $this->addMessage("ERROR","Unknown execution type [${ob['type']}]");
			}
		}
		return true;
	}
	
	private function getVariable($name){
		if(!isset($this->runData[$name])){ // maybe inside json
		
			$current=$this->runData;
			foreach(explode(".",$name) as $p){
				if(!is_array($current) || !isset($current[$p]))
					return $this->addMessage("ERROR","Unknown variable [${name}]");
				$current=$current[$p];
			}
			if(is_array($current))return json_encode($current);
			return $current;
		}
		
		if(is_array($this->runData[$name]))return json_encode($this->runData[$name]);
		
		return $this->runData[$name];
	}
	
	private function doExecuteAPI($ob,$runner){
		global $settings;

		$url=$ob['endpoints'][0];
		if(count($ob['endpoints'])>1 && $runner>0){
			$c=count($ob['endpoints'])-1;
			$url=$ob['endpoints'][1+(($runner-1) % $c)];
		}
		
		$REQTYPE_GET=0;
		$REQTYPE_POST_REGULAR=1;
		$REQTYPE_POST_RAW=2;
		$REQTYPE_POST_MULTIPART=3;
		
		$reqType=0;
		if($ob['method']=="POST")$reqType=$REQTYPE_POST_REGULAR;
		
		foreach($ob['parameters'] as $p){
			if($p['type']=="file"){$reqType=$REQTYPE_POST_MULTIPART; break;}
			else if($p['name']==""){$reqType=$REQTYPE_POST_RAW; break;}
		}
		
		$debug=$settings->get("service.curl_verbose",false);
		
		$opts=array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			//CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => array(),
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_VERBOSE => $debug
		);
		
		if($debug){
			$streamVerboseHandle = fopen('php://temp', 'w+');
			$opts[CURLOPT_STDERR]=$streamVerboseHandle;
		}
		
		if(isset($ob['headers']) && is_array($ob['headers']) && !empty($ob['headers']))
			$opts[CURLOPT_HTTPHEADER]=$ob['headers'];
		
		switch($reqType){
			case $REQTYPE_GET:
				if(count($ob['parameters'])>0){
					if(strpos($url,"?")!==false)$url.="&";
					else $url.="?";
				}
				$first=true;
				foreach($ob['parameters'] as $p){
					if($p['type']=="file" || $p['name']==""){
						return $this->addMessage("ERROR","Invalid parameter type [${p['name']}:${p['type']}");
					}
					if($first)$first=false;
					else $url.="&";
					$v=$this->getVariable($p['map']);
					if($v===false)return false;
					$url.=urlencode($p['name'])."=".urlencode($v);
				}
				$opts[CURLOPT_URL] = $url;
				
			break;
			
			case $REQTYPE_POST_REGULAR:
				$u="";
				foreach($ob['parameters'] as $p){
					if($p['type']=="file" || $p['name']==""){
						return $this->addMessage("ERROR","Invalid parameter type [${p['name']}:${p['type']}");
					}
					if(strlen($u)>0)$u.="&";
					$v=$this->getVariable($p['map']);
					if($v===false)return false;
					$u.=urlencode($p['name'])."=".urlencode($v);
				}
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_CUSTOMREQUEST] = "POST";
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $u;
				$opts[CURLOPT_HTTPHEADER][]="Content-Type: application/x-www-form-urlencoded";
				$opts[CURLOPT_HTTPHEADER][]="Content-Length: " . strlen($u);
			break;
			
			case $REQTYPE_POST_RAW:
				$pdata="";
				foreach($ob['parameters'] as $p){
					if($p['name']!=""){
						return $this->addMessage("ERROR","Invalid parameter type [${p['name']}:${p['type']}");
					}
					$v=$this->getVariable($p['map']);
					if($v===false)return false;
					$pdata.=$v;
				}
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_CUSTOMREQUEST] = "POST";
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $pdata;
				$opts[CURLOPT_HTTPHEADER][]="Content-Length: " . strlen($pdata);
				
			break;
			
			case $REQTYPE_POST_MULTIPART:
				$boundary = uniqid();
				$delimiter = '-------------' . $boundary;
				$eol = "\r\n";
				$data="";
				foreach($ob['parameters'] as $p){
					if($p['name']==""){
						return $this->addMessage("ERROR","Invalid parameter [${p['name']}:${p['type']}");
					}
					$v=$this->getVariable($p['map']);
					if($v===false)return false;
					$data .= "--" . $delimiter . $eol;
					if($p['type']=='file'){
						$fname=(isset($p['filename']) && strlen($p['filename'])>0)?($p['filename']):($p['name']);
						
						$data.= 'Content-Disposition: form-data; name="'.$p['name'].'"; filename="'.$fname.'"'.$eol;
					}else
						$data.= 'Content-Disposition: form-data; name="'.$p['name'].'"'.$eol;
					
					$data.=$eol.$v.$eol;
				}
				$data.="--" . $delimiter . "--".$eol;
				
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_CUSTOMREQUEST] = "POST";
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $data;
				$opts[CURLOPT_HTTPHEADER][]="Content-Type: multipart/form-data; boundary=" . $delimiter;
				$opts[CURLOPT_HTTPHEADER][]="Content-Length: " . strlen($data);
			break;
		} // end switch
		
		set_time_limit(80);
		$ch = curl_init();
		curl_setopt_array($ch,$opts);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		
		if($debug){
			rewind($streamVerboseHandle);
			$verboseLog = stream_get_contents($streamVerboseHandle);

			echo "<pre>cUrl verbose information:\n".htmlspecialchars($verboseLog)."</pre>\n";
			echo "<pre>cUrl result:\n";var_dump($server_output);echo "</pre>\n";
		}
		
		if($server_output===false){
			return $this->addMessage("ERROR","Invalid response invoking [${url}]");
		}

		$this->runData["result_raw"]=$server_output;
		
		if($ob['output_type']=='json')$this->runData["result"]=json_decode($server_output,true);
		
		if(isset($ob['output_map']) && is_array($ob['output_map'])){
			foreach($ob['output_map'] as $m){
				$v=$this->getVariable($m['key']);
				if($v===false)return false;
				$this->runData[$m['map']]=$v;
			}
		}

		return true;
	}
	
	private function pathToArray($path,$value){
		if(empty($path))return $value;
		return [$path[0]=>$this->pathToArray(array_slice($path,1),$value)];
	}
	
	private function doOutput(){
		$out=[];
		foreach($this->runData as $k=>$v){
			if(startsWith($k,"output.")){
				$out=array_merge_recursive($out,$this->pathToArray(array_slice(explode(".",$k),1),$v));
			}
		}
		return $out;
	}
}