<?php

require_once "../lib/lib.php";

LOCK_ON_FILE("${LIB_PATH}/../scripts/scheduler.lock");

$settings=new Settings();
$settings->load();
$fileRunner=0;

$corpora=new Corpora();

$allMaxRunners=0;

echo date("Y-m-d H:i:s.u")." ".microtime(true)." START\n";

$task=new Task();
foreach($corpora->getList() as $c){
    $path=$corpora->getPath()."/".$c['name']."/tasks/new";
    if(!is_dir($path))continue;
    
    $ftasks=$task->getTasksFromFolder($path);
    if(!is_array($ftasks) || count($ftasks)==0)continue;
    
    $corpus=new Corpus($corpora,$c['name']);
    $corpus->loadData();
    
    foreach($ftasks as $ftask){
        $minRunner=0;
        $maxRunner=$settings->get("TaskRunners",1);
            
        $tdata=json_decode(file_get_contents($ftask),true);
        $tdata['status']='SCHEDULING';
        $tdata['date_SCHEDULING']=date("Y-m-d H:i:s.u")." ".microtime(true);
        storeFile($ftask,json_encode($tdata));
        
        if(isset($tdata['runners']) && strlen($tdata['runners'])>0){
            $r=$tdata['runners'];
            $pos=strpos($r,"-");
            if($pos!==false){
                list($r1,$r2)=explode("-",$r,2);
                $minRunner=intval($r1);
                $maxRunner=intval($r2)+1;
            }else{
                $minRunner=intval($r);
                $maxRunner=intval($r)+1;
            }
        }
        
        if($maxRunner>$allMaxRunners)$allMaxRunners=$maxRunner;
        
        if($fileRunner<$minRunner || $fileRunner>=$maxRunner)$fileRunner=$minRunner;

        $task_name=substr($ftask,strrpos($ftask,'/')+1);
        
        if($tdata['type']=='basic_tagging'){
            scheduleFilesFolder($corpus,$task_name,$c['name']);
        }else if($tdata['type']=='chunking'){
            scheduleFolder($corpus->getFolderPath()."/$DirectoryAnnotated/",$task_name,$c['name'],"conllu");
        }else if($tdata['type']=='cleanup'){
            scheduleFolder($corpus->getFolderPath()."/$DirectoryAnnotated/",$task_name,$c['name'],"conllu");
        }else if($tdata['type']=='iate_eurovoc'){
            scheduleFolder($corpus->getFolderPath()."/$DirectoryAnnotated/",$task_name,$c['name'],"conllu");
        }else if($tdata['type']=='marcell'){
            scheduleFile($corpus->getFolderPath()."/$DirectoryAnnotated/",$task_name,$c['name'],"conllu");
        }else if($tdata['type']=='statistics'){
            createFolder($corpus->getFolderPath()."/statistics/");
            clearFolder($corpus->getFolderPath()."/statistics/");
            
            storeFile($corpus->getFolderPath()."/changed_statistics.json",json_encode(["changed"=>time()]));            
        
            scheduleFilesFolder($corpus,$task_name,$c['name']);
            scheduleFolder($corpus->getFolderPath()."/$DirectoryAnnotated/",$task_name,$c['name'],"conllu");
        }else if($tdata['type']=='unzip_text'){
            scheduleFile($corpus->getFolderPath()."/zip_text/".$tdata['fname'],$task_name,$c['name'],'zip');
        }else if($tdata['type']=='zip_text'){
            scheduleFile($corpus->getFolderPath()."/zip_text/".$tdata['fname'],$task_name,$c['name'],'zip');
        }else if($tdata['type']=='zip_basic_tagging'){
            scheduleFile($corpus->getFolderPath()."/zip_$DirectoryAnnotated/".$tdata['fname'],$task_name,$c['name'],'zip');
        }
                
        finishRunners($task_name);
        
        $old_path=str_replace("/tasks/new/","/tasks/old/",$ftask);
        $path=$corpora->getPath()."/".$c['name']."/tasks/old";
        if(!is_dir($path))createFolder($path);

        $tdata['status']='SCHEDULED';
        $tdata['date_SCHEDULED']=date("Y-m-d H:i:s.u")." ".microtime(true);
        storeFile($ftask,json_encode($tdata));
        renameFile($ftask,$old_path);
            
    }
}

echo date("Y-m-d H:i:s.u")." ".microtime(true)." END\n";


// UNLOCK_FILE(); // => chemata in fn de shutdown

function scheduleFilesFolder($corpus,$task_name,$c_name){
            foreach($corpus->getFiles() as $fdata){
                if($fdata['type']=='csv'){
                    scheduleCSVFile(
                        $corpus->getFolderPath()."/files/".$fdata['name'],
                        $fdata,
                        $task_name,
                        $c_name
                    );
                }else if($fdata['type']=='text'){
                    scheduleFile(
                        $corpus->getFolderPath()."/files/".$fdata['name'],
                        $task_name,
                        $c_name,
                        "text"
                    );
                }
            }
}


function scheduleFolder($folder,$task,$corpus,$ftype){
    $dh = opendir($folder);
    if($dh===false)return false;
    
    while (($file = readdir($dh)) !== false) {
        $fpath="$folder/$file";
        if(!is_file($fpath))continue;
        
        scheduleFile($fpath,$task,$corpus,$ftype);
    }
    closedir($dh);    

    return true;
}

function getRunnerQPath($trun,$create=false){
    global $LIB_PATH;
    
    $dir="${LIB_PATH}/../scripts";
    $dir.="/runnerq";
    if($create===true)createFolder($dir);
    $dir.="/${trun}";
    if($create===true)createFolder($dir);
    
    return $dir;
}

function addToRunner($trun,$task,$data,$corpus){
    $dir=getRunnerQPath($trun,true);
    $ftask=$dir."/${task}.tmp";
    if(!is_file($ftask))                                                            
        storeFile($dir."/${task}.tmp",json_encode(["corpus"=>$corpus,"task"=>$task])."\n");
        
    storeFile($dir."/${task}.tmp",json_encode($data)."\n",FILE_APPEND);
}

function finishRunners($task){
    global $settings,$allMaxRunners;
    
    for($i=0;$i<$allMaxRunners;$i++){
        $dir=getRunnerQPath($i);
        $fn1="$dir/${task}.tmp";
        $fn2="$dir/${task}";
        if(is_file($fn1))
            renameFile($fn1,$fn2);                                                            
    }
}

function scheduleCSVFile($fpath,$fdata,$task,$corpus){
    global $settings,$minRunner,$maxRunner;
    
    $fp=fopen($fpath,"r");
    if($fp===false)return ;
    
    $trun=$minRunner;
    
    $lnum=-1;
    while(!feof($fp)){
        $line=fgetcsv($fp,0,$fdata['delimiter'],$fdata['enclosure'],$fdata['escape']);
        if($line===false)break;
        
        $lnum++;
        if($lnum<intval($fdata['ignore_rows']))continue;
        
        if($line[0]===null)continue;
        
        if(strlen($fdata['comment'])>0 && startsWith($line[0],$fdata['comment']))continue;

        addToRunner($trun,$task,[
            "fpath"=>$fpath,
            "ftype"=>"csv",
            "line"=>$lnum,
            "delimiter"=>$fdata['delimiter'],
            "enclosure"=>$fdata['enclosure'],
            "escape"=>$fdata['escape'],
            "columns"=>$fdata['columns']
        ],$corpus);
        
        $trun++;
        if($trun>=$maxRunner)$trun=$minRunner;
    }
    
    fclose($fp);
    
}

function scheduleFile($fpath,$task,$corpus,$ftype){
    global $settings,$fileRunner,$minRunner,$maxRunner;
    
    addToRunner($fileRunner,$task,[
            "fpath"=>$fpath,
            "ftype"=>$ftype,
    ],$corpus);
        
    $fileRunner++;
    if($fileRunner>=$maxRunner)$fileRunner=$minRunner;
    
}