<?php

class Modules {
    private $modules;
    private $languages;

    public function getModules(){return $this->modules;}
    public function getLanguages(){return $this->languages;}

    public function clear(){
        $this->modules=[];
        $this->languages=[];
    }
    
    public function loadModule($mname,$mpath){
        $data=json_decode(file_get_contents("$mpath/module.json"),true);
        if(isset($data['enabled']) && $data['enabled']===false)return ;
        
        if(isset($data['languages'])){
						$data['lang']=array_flip($data['languages']);
						
						foreach($data['lang'] as $lang=>$n){
								if($lang!="*")$this->languages[$lang]=true;
						}
				}
        $data['mpath']=$mpath;
        $data['mname']=$mname;
        
        $this->modules[$mname]=$data;
    }
    
    public function load(){
        global $LIB_PATH;
        
        $this->clear();
        
        $path="${LIB_PATH}/../modules";
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    $mpath="$path/$file";
                    if(is_dir($mpath) && !startsWith($mpath,".") && is_file("$mpath/module.json")){
                        $this->loadModule($file,$mpath);
                    }
                }
                closedir($dh);
            }
        }

        uasort($this->modules,array($this,"module_sort_cmp"));

        return true;
    }
    
    public function module_sort_cmp($a,$b){
        $o1=0; if(isset($a['order']))$o1=$a['order'];
        $o2=0; if(isset($b['order']))$o2=$b['order'];
        
        return $o1-$o2;
    }
    
    public function getTaskDialog($corpus){
        $ret="";
        foreach($this->modules as $id=>$module){
        		if(!isset($module['lang']) || !isset($module['lang']['*']) && !isset($module['lang'][$corpus->getLang()]))continue;
        		
            foreach($module['tasks'] as $k=>$task){
            		if(isset($task['hidden']) && $task['hidden']==true)continue;
                $ret.="<div id=\"popup-dialog-crud-task-$id-$k\" style=\"display:none;\">\n".
                      "      <form id=\"crud-form-task-$id-$k\" method=\"POST\" action=\"index.php\">\n".
                      "            <input type=\"hidden\" name=\"path\" value=\"corpus/task_add\">\n".
                      "            <input type=\"hidden\" name=\"corpus\" value=\"{{CORPUS_NAME}}\">\n".
                      "            <input type=\"hidden\" name=\"type\" value=\"${task['type']}\">\n".
                      ((isset($task['description']))?("<table align=\"center\"><tbody><tr><td>${task['description']}</td></tr></tbody></table>"):("")).
                      "            <table align=\"center\"><tbody>\n";
                if(isset($task['additionalData'])){
						foreach($task['additionalData'] as $add){
                        
								if(!isset($add['description']))$add['description']=$add['name'];
								if(!isset($add['default']))$add['default']="";
                                
                                if(isset($add['type']) && strcasecmp($add['type'],"select")==0){
								    $ret.="<tr><td>${add['description']}</td><td>";
                                    $ret.="<select name=\"${add['name']}\" id=\"${add['name']}\">";
                                    foreach($add['values'] as $value){
                                        $selected="";
                                        if($value==$add['default'])$selected=" selected=\"true\" ";
                                        $ret.="<option value=\"$value\" $selected>$value</option>";
                                    }
                                    $ret.="</select></td></tr>\n";
                                }else{
								    $ret.="<tr><td>${add['description']}</td><td><input type=\"text\" size=\"50\" name=\"${add['name']}\" id=\"${add['name']}\" placeholder=\"${add['default']}\"/></td></tr>\n";
                                }
						}
				}  
				$valueRunners="";
				if(isset($task['defaultRunners']))$valueRunners=$task['defaultRunners'];    
                $ret.="                <tr><td>Description:</td><td><textarea name=\"desc\" rows=\"4\" cols=\"50\"></textarea></td></tr>\n".
                      "                <tr><td>Runners (optional):</td><td><input type=\"text\" size=\"50\" name=\"runners\" id=\"runners\" value=\"$valueRunners\"/></td></tr>\n".
                      "                <tr><td>Overwrite:</td><td><input type=\"checkbox\" name=\"overwrite\" id=\"overwrite\" value=\"1\"/></td></tr>\n".
                      "            </tbody></table>\n".
                      "        </form>\n".
                      "        </div>\n";    
            }
        }                                  
        return $ret;
    }

    public function getTaskButtons($corpus){
        $ret="";
        foreach($this->modules as $id=>$module){
        		if(!isset($module['lang']) || !isset($module['lang']['*']) && !isset($module['lang'][$corpus->getLang()]))continue;
        
            foreach($module['tasks'] as $k=>$task){
            		if(isset($task['hidden']) && $task['hidden']==true)continue;
                $ret.="{ type: 'button', label: '${task['name']}', listeners: [{ click: function(){gridAddTask('$id-$k','Add task ${task['name']}');}}], icon: 'ui-icon-plus' },";
            }
        }
        return $ret;
    }
    
    public function runModuleFunc($module,$func,$params){
    		$pos=strpos($func,".php:");
    		$fname=substr($func,0,$pos+4);
    		$callback=substr($func,$pos+5);
    		$cpath=$module['mpath']."/$fname";
    		$content=file_get_contents($cpath);
    		
				include_once $cpath;
    		
    		eval("call_user_func_array('$callback',\$params); ");
		}
    
    public function getTaskInit($corpus){
        $ret="";
        foreach($this->modules as $id=>$module){
        		if(!isset($module['lang']) || !isset($module['lang']['*']) && !isset($module['lang'][$corpus->getLang()]))continue;
        
            foreach($module['tasks'] as $k=>$task){
            		if(isset($task['hidden']) && $task['hidden']==true)continue;
    
                $ret.="\$(\"#popup-dialog-crud-task-$id-$k\").dialog({ width: 600, modal: true, open: function () { \$(\".ui-dialog\").position({ of: \"#gridTasks\" }); }, autoOpen: false});";
            }
        }
        return $ret;
    }
    
    public function schedule($settings,$corpus,$task_name,$tdata){
    		
    		$found=false;
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
            		if($tdata['type']==$task['type']){
								     $found=true;
								     
								     $this->runModuleFunc($module,$task['scheduler'],[$settings,$corpus,$task_name,$tdata]);
								     
								     break;
								}
          	}
          	if($found)break;
        }
		}

    public function runTask($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    		
    		$found=false;
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
            		if($taskDesc['type']==$task['type']){
								     $found=true;
								     
								     $this->runModuleFunc($module,$task['runner'],[$runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut]);
								     
								     break;
								}
          	}
          	if($found)break;
        }
		}
		
    public function setTaskDefaults($taskType,&$data){
    		$found=false;
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
            		if($task['type']==$taskType){
            				$found=true;
		                if(isset($task['additionalData'])){
												foreach($task['additionalData'] as $add){
														if(!isset($add['default']))$add['default']="";
														if(!isset($data[$add['name']]) || empty($data[$add['name']]))
																$data[$add['name']]=$add['default'];
												}
										}
										break;
								}      
            }
            if($found)break;
        }                                  
    }
		
    public function setTaskDataFromRequest($taskType,&$data){
   		$found=false;
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
            		if($task['type']==$taskType){
           				$found=true;
		                if(isset($task['additionalData'])){
							foreach($task['additionalData'] as $add){
                               if(isset($_REQUEST[$add['name']]))$data[$add['name']]=$_REQUEST[$add['name']];
                            }
						}
						break;
					}      
            }
            if($found)break;
        }                                  
    }

}

?>