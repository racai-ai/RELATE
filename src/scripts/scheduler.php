<?php

require_once "../lib/lib.php";

LOCK_ON_FILE("${LIB_PATH}/../scripts/scheduler.lock");

$settings=new Settings();
$settings->load();

$additionalPath=$settings->get("path","");
if(is_string($additionalPath) && strlen($additionalPath)>0){
    putenv("PATH=$additionalPath");
}

$modules=new Modules();
$modules->load();

$fileRunner=0;

$corpora=new Corpora();

$allMaxRunners=0;

echo date("Y-m-d H:i:s.u")." ".microtime(true)." SCHEDULER START\n";

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
        
        echo "[corpus=".$corpus->getName()."] [type=${tdata['type']}] [task=${task_name}]\n";
        
        $modules->schedule($settings,$corpus,$task_name,$tdata);
        
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

echo date("Y-m-d H:i:s.u")." ".microtime(true)." SCHEDULER END\n";


// UNLOCK_FILE(); // => chemata in fn de shutdown

function scheduleFilesFolder($corpus,$task_name){
            foreach($corpus->getFiles() as $fdata){
                if($fdata['type']=='csv'){
                    scheduleCSVFile(
                        $corpus->getFolderPath()."/files/".$fdata['name'],
                        $fdata,
                        $task_name,
                        $corpus->getName()
                    );
                }else if($fdata['type']=='text'){
                    scheduleFile(
                    		$corpus,
                        "files/".$fdata['name'],
                        $task_name,
                        "text"
                    );
                }
            }
}

function scheduleAudioFolder($corpus,$task_name){
    if(!$corpus->hasAudio())return ;
    foreach($corpus->getAudio() as $fdata){
        scheduleFile(
            $corpus,
            "audio/".$fdata['name'],
            $task_name,
            "audio"
        );
    }
}

function scheduleImageFolder($corpus,$task_name){
    if(!$corpus->hasImage())return ;
    foreach($corpus->getImage() as $fdata){
        scheduleFile(
            $corpus,
            "image/".$fdata['name'],
            $task_name,
            "image"
        );
    }
}

function scheduleVideoFolder($corpus,$task_name){
    if(!$corpus->hasVideo())return ;
    foreach($corpus->getVideo() as $fdata){
        scheduleFile(
            $corpus,
            "video/".$fdata['name'],
            $task_name,
            "video"
        );
    }
}


function scheduleFolder($corpus, $folder,$task_name,$ftype,$ext=false,$filterPrefix=false){

    $folder_path=$corpus->getFolderPath()."/".$folder;
    if(!is_dir($folder_path))return false;
    $dh = opendir($folder_path);
    if($dh===false)return false;
    
    while (($file = readdir($dh)) !== false) {
        $fpath="$folder_path/$file";
        if(!is_file($fpath))continue;
        
        if($ext!==false){
            if(is_array($ext)){
                $found=false;
                foreach($ext as $ec){
                    if(endsWith($fpath,$ec)){$found=true; break;}
                }
                if(!$found)continue;
            }else if(!endsWith($fpath,$ext))continue;
        }
        if($filterPrefix!==false && startsWith($file,$filterPrefix))continue;
        
        scheduleFile($corpus,"$folder/$file",$task_name,$ftype);
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

function scheduleFile($corpus,$fpath,$task,$ftype){
    global $settings,$fileRunner,$minRunner,$maxRunner;
    
    addToRunner($fileRunner,$task,[
            "fpath"=>$corpus->getFolderPath()."/".$fpath,
            "ftype"=>$ftype,
    ],$corpus->getName());
        
    $fileRunner++;
    if($fileRunner>=$maxRunner)$fileRunner=$minRunner;
    
}