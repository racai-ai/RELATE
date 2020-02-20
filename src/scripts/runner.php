<?php

require_once "../lib/lib.php";
require_once "runner.basic_tagging.php";
require_once "ro/basic_tagging.php";
require_once "en/basic_tagging.php";
require_once "tag_rules.php";
require_once "runner.chunking.php";
require_once "runner.unzip.php";
require_once "runner.statistics.php";
require_once "runner.zip.php";
require_once "cleaner_marcell.php";
require_once "runner.iateeurovoc.php";

if(count($argv)!=2){
    die("runner.php <NUMBER>");
}

$trun=intval($argv[1]);

LOCK_ON_FILE("${LIB_PATH}/../scripts/runner.${trun}.lock");

$runnerFolder="${LIB_PATH}/../scripts/runnerq/${trun}";

$settings=new Settings();
$settings->load();

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
            $taskDesc['status']='RUNNING';
            $taskDesc['date_RUNNING']=date("Y-m-d H:i:s.u")." ".microtime(true);
            storeFile($corpus->getFolderPath()."/tasks/old/".$tdata['task'],json_encode($taskDesc));
        }
        
        while(!feof($fp)){
            $line=fgets($fp);
            if($line===false)break;
            
            $data=json_decode($line,true);
            runTask($data);
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


function runTask($data){
    global $current,$tdata,$taskDesc,$corpus,$DirectoryAnnotated;

    $fnameOut=substr($data['fpath'],strrpos($data['fpath'],"/")+1);
    
    echo "Run Task: [".$taskDesc['type']."] [".$data['fpath']."] [".$data['ftype']."] => [".$fnameOut."]\n";
    
    /**************************** SPECIAL TASKS *****/
    if($taskDesc['type']=='unzip_text'){
       runUnzip($data['fpath'],$corpus->getFolderPath()."/files");
       return ;
    }else if($taskDesc['type']=='zip_text'){
       runZip($corpus->getFolderPath()."/files",$corpus->getFolderPath()."/zip_text",$taskDesc['fname']);
       return ;
    }else if($taskDesc['type']=='zip_basic_tagging'){
       runZip($corpus->getFolderPath()."/$DirectoryAnnotated",$corpus->getFolderPath()."/zip_$DirectoryAnnotated",$taskDesc['fname']);
       return ;
    }
    

    /**************************** REGULAR TASKS *****/

    if($current===false || $current['fpath']!==$data['fpath'] || ($current['ftype']=='csv' && $current['line']<$data['line'])){
        if($current!==false && isset($current['fp']) && $current['fp']!==null && $current['fp']!==false)fclose($current['fp']);
        
        $current=$data;
        if($data['ftype']=='csv'){
            $fp=fopen($data['fpath'],"r");
            $current['fp']=$fp;
            $current['line']=-1;
        }
    }

    /***** CSV *****/
    if($data['ftype']=='csv'){
        $line=false;
        while(!feof($current['fp']) && $current['line']<$data['line']){
            $line=fgetcsv($current['fp'],0,$current['delimiter'],$current['enclosure'],$current['escape']);
            if($line===false)break;
            $current['line']++;
        }
        
        if($line!==false){
             foreach(explode(",",$current['columns']) as $col){
                  $text=$line[intval($col)];
                  if($taskDesc['type']=='basic_tagging'){
                      runBasicTaggingText($text,"${current['line']}_${col}_$fnameOut");
                  }else if($taskDesc['type']=='statistics'){
                      runStatistics($text,"${current['line']}_${col}_$fnameOut",$data['ftype'],false);
                  }
             }
        }

    /***** CONLLU *****/
    }else if($data['ftype']=='conllu'){
        if($taskDesc['type']=='chunking'){
            runChunking($data['fpath'],$fnameOut);
        }else if($taskDesc['type']=='statistics'){
            runStatistics(file_get_contents($data['fpath']),$fnameOut,$data['ftype'],$data['fpath']);
        }else if($taskDesc['type']=='cleanup'){
    	    $meta="";
    	    $p=$data['fpath'];
    	    $base=substr($data['fpath'],0,strrpos($data['fpath'],'/'));
    	    $base.="../metadata/";
    	    $fn=substr($data['fpath'],strrpos($data['fpath'],'/')+1);
    	    $base.=$fn;
    	    $base=str_replace(".txt",".conllu",$base);
    	    //var_dump($base);
    	    if(is_file($base)){
    		$meta=file_get_contents($base);
    		//var_dump($meta);
    	    }
            runCleanup(file_get_contents($data['fpath']),$fnameOut,$meta);
        }else if($taskDesc['type']=='iate_eurovoc'){
            runIateEurovoc(file_get_contents($data['fpath']),$fnameOut);
        }

    /***** TEXT *****/
    }else if($data['ftype']=='text'){
        if($taskDesc['type']=='basic_tagging'){
            runBasicTaggingText(file_get_contents($data['fpath']),$fnameOut);
        }else if($taskDesc['type']=='statistics'){
            runStatistics(false,$fnameOut,$data['ftype'],$data['fpath']);
        }        
    }
}

