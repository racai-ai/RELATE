<?php

class Modules {
    private $modules;

    public function getModules(){return $this->modules;}

    public function clear(){
        $this->modules=[];
    }
    
    public function loadModule($mname,$mpath){
        $data=json_decode(file_get_contents("$mpath/module.json"),true);
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
    
    public function getTaskDialog(){
        $ret="";
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
                $ret.="<div id=\"popup-dialog-crud-task-$id-$k\" style=\"display:none;\">\n".
                      "      <form id=\"crud-form-task-$id-$k\" method=\"POST\" action=\"index.php\">\n".
                      "            <input type=\"hidden\" name=\"path\" value=\"corpus/task_add\">\n".
                      "            <input type=\"hidden\" name=\"corpus\" value=\"{{CORPUS_NAME}}\">\n".
                      "            <input type=\"hidden\" name=\"type\" value=\"${task['type']}\">\n".
                      "            <table align=\"center\"><tbody>\n".
                      "                <tr><td>Description:</td><td><textarea name=\"desc\" rows=\"4\" cols=\"50\"></textarea></td></tr>\n".
                      "                <tr><td>Runners (optional):</td><td><input type=\"text\" size=\"50\" name=\"runners\" id=\"runners\"/></td></tr>\n".
                      "                <tr><td>Overwrite:</td><td><input type=\"checkbox\" name=\"overwrite\" id=\"overwrite\" value=\"1\"/></td></tr>\n".
                      "            </tbody></table>\n".
                      "        </form>\n".
                      "        </div>\n";    
            }
        }                                  
        return $ret;
    }

    public function getTaskButtons(){
        $ret="";
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
                $ret.="{ type: 'button', label: '${task['name']}', listeners: [{ click: function(){gridAddTask('$id-$k','Add task ${task['name']}');}}], icon: 'ui-icon-plus' },";
            }
        }
        return $ret;
    }
    
    public function getTaskInit(){
        $ret="";
        foreach($this->modules as $id=>$module){
            foreach($module['tasks'] as $k=>$task){
    
                $ret.="\$(\"#popup-dialog-crud-task-$id-$k\").dialog({ width: 600, modal: true, open: function () { \$(\".ui-dialog\").position({ of: \"#gridTasks\" }); }, autoOpen: false});";
            }
        }
        return $ret;
    }

}

?>