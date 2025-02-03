<?php

require_once "../lib/lib.php";

if(count($argv)!=2){
    die("runner.php <NUMBER>");
}

$trun=intval($argv[1]);

LOCK_ON_FILE("${LIB_PATH}/../scripts/runner.${trun}.lock");

$runnerFolder="${LIB_PATH}/../scripts/runnerq/${trun}";

$runner=new Runner($trun,$runnerFolder);

$settings=new Settings();
$settings->load();

$additionalPath=$settings->get("path","");
if(is_string($additionalPath) && strlen($additionalPath)>0){
    putenv("PATH=$additionalPath");
}


$modules=new Modules();
$modules->load();

$dir=$runnerFolder;
if(!is_dir($dir))die();

$dh = opendir($dir);
if($dh===false)die();

$current=false;
$tdata=false;

$corpora=new Corpora();
$corpus=false;
$taskDesc=false;


while (($file = readdir($dh)) !== false) {
    $dpath="$dir/$file";
    if(!is_file($dpath) || !endsWith($dpath,".task"))continue;

    $fp=fopen($dpath,"r");
    $tdata=json_decode(fgets($fp),true);
    $taskDesc=false;
    $corpus=new Corpus($corpora,$tdata['corpus']);
    if($corpus->loadData()){            
        $taskDesc=json_decode(file_get_contents($corpus->getFolderPath()."/tasks/old/".$tdata['task']),true);
        if($taskDesc['status']!='RUNNING'){
            $taskLock=LOCK_ON_FILE($corpus->getFolderPath()."/tasks/old/".$tdata['task'].".lock");
            
            $taskDesc['status']='RUNNING';
            $taskDesc['date_RUNNING']=date("Y-m-d H:i:s.u")." ".microtime(true);
            storeFile($corpus->getFolderPath()."/tasks/old/".$tdata['task'],json_encode($taskDesc));
            
            UNLOCK_SINGLE_FILE($taskLock);
        }
        
        while(!feof($fp)){
            $line=fgets($fp);
            if($line===false)break;
            
            $data=json_decode($line,true);
            runTask($runner,$data);
        }
    }
    fclose($fp);
    @unlink($dpath);
    
    $found=false;
    for($i=0;$i<$settings->get("TaskRunners",1);$i++){
        if(is_file("${LIB_PATH}/../scripts/runnerq/${i}/${file}")){$found=true;break;}
    }
    
    if(!$found && $taskDesc!==false){
            $taskDesc['status']='DONE';
            $taskDesc['date_DONE']=date("Y-m-d H:i:s.u")." ".microtime(true);
            storeFile($corpus->getFolderPath()."/tasks/old/".$tdata['task'],json_encode($taskDesc));
    }
}
closedir($dh);


function runTask($runner,$data){
    global $current,$tdata,$taskDesc,$corpus,$DirectoryAnnotated,$modules,$settings,$trun;

    $fnameOut=changeFileExtension(substr($data['fpath'],strrpos($data['fpath'],"/")+1),"conllup");
    
    echo "Run Task: [".$taskDesc['type']."] [".$data['fpath']."] [".$data['ftype']."] => [".$fnameOut."]\n";
    
    $contentIn="";
    
    /**************************** SPECIAL TASKS *****/
    /*}else if($taskDesc['type']=='unzip_annotated'){
       runUnzipAnnotated($data['fpath'],$corpus->getFolderPath()."/".$DirectoryAnnotated);
       return ;
    } */
    

    /**************************** REGULAR TASKS *****/

		/* Determine content based on file type */

    /***** CSV *****/
    if($data['ftype']=='csv'){
    		// First check if the file has changed and needs to be opened
		    if($current===false || $current['fpath']!==$data['fpath'] || $current['line']<$data['line']){
		    		// close old file
		        if($current!==false && isset($current['fp']) && $current['fp']!==null && $current['fp']!==false)fclose($current['fp']);
		        
		        $current=$data;
            $fp=fopen($data['fpath'],"r");
            $current['fp']=$fp;
            $current['line']=-1;
		    }
		    
        // read requested line
				$line=false;
        while(!feof($current['fp']) && $current['line']<$data['line']){
            $line=fgetcsv($current['fp'],0,$current['delimiter'],$current['enclosure'],$current['escape']);
            if($line===false)break;
            $current['line']++;
        }
        
        if($line!==false){
        		 // run task for each column
             foreach(explode(",",$current['columns']) as $col){
                  $contentIn=$line[intval($col)];
                  $modules->runTask($runner,$settings,$corpus,$taskDesc,$data,$contentIn,"${current['line']}_${col}_$fnameOut");
                  
             }
        }

    /***** CONLLU *****/
    }else if($data['ftype']=='conllu'){
    		$contentIn=file_get_contents($data['fpath']);
				$modules->runTask($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut);

    /***** TEXT *****/
    }else if($data['ftype']=='text'){
    		$contentIn=file_get_contents($data['fpath']);
				$modules->runTask($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut);

    }else{
			$modules->runTask($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut);
		}
    
}

