<?php
require_once "project.php";
require_once "projects.php";

$months_ro=["","Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];
$months_en=["","January","February","March","April","May","June","July","August","September","October","November","December"];

function getProjectForReport($name){
  $projects=new Projects();
  $prj=new Project($projects,$name);
  if(!$prj->loadData())die("Invalid project");
  if(!$prj->hasRights("admin"))die("Invalid project");
  return $prj;
}

function getReportByName($name){
  $found=false;
  foreach($prj->getReports() as $rep){
      if($rep['name']==$name){$found=true; break;}
  }
  if(!$found)die("Invalid report");
  return $rep;
}

function getReplDataCommon($date, $signdate, $year, $month, $pname){
    global $months_ro,$months_en;
    
    $repl=[
    "{{DATE}}" => $date,
    "{{SIGNDATE}}" => $signdate,
    "{{YEAR}}" => $year,
    "{{MONTH}}" => $month,
    "{{MONTH_RO}}" => $months_ro[$month],
    "{{MONTH_EN}}" => $months_en[$month],
    "{{PERSON}}" => $pname,
    ];


    $weekdays=["sun","mon","tue","wed","thu","fri","sat"];
    $weekdays_ro=["D","L","M","M","J","V","S"];
    $max_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for($i=1;$i<=31;$i++){
        if($i>$max_days){
             $repl["{{WDAY.$i}}"]="";
             $repl["{{WDAY_EN.$i}}"]="";
             $repl["{{WDAY_RO.$i}}"]="";
        }else{
            $repl["{{WDAY.$i}}"]=$weekdays[date("w",strtotime(sprintf("%4d-%02d-%02d",$year,$month,$i)))];
            $repl["{{WDAY_EN.$i}}"]=$weekdays[date("w",strtotime(sprintf("%4d-%02d-%02d",$year,$month,$i)))];
            $repl["{{WDAY_RO.$i}}"]=$weekdays_ro[date("w",strtotime(sprintf("%4d-%02d-%02d",$year,$month,$i)))];
        }
    }


    return $repl;
}    

function getProjectReplData($prj,$prefix,$year,$month,$pname,$date,$signdate){
    global $months_ro,$months_en;
    
    $witems=[""];
    foreach($prj->getWorkItems() as $wi)$witems[]=$wi['name'];
    sort($witems);
    
    $work=$prj->getWorkBreakdown($year, $month);

    $members=[];
    foreach($prj->getTeamMembers() as $m){
        $members[$m["name"]]=$m;
    }

    $repl=[
    "{{SUPERVISOR}}"=> (isset($members[$pname]) && isset($members[$pname]["supervisor"]))?($members[$pname]["supervisor"]):(""),
    "{{POSITION}}"=> (isset($members[$pname]) && isset($members[$pname]["position"]))?($members[$pname]["position"]):(""),
    "{{WORK}}" => (isset($work[$pname]) && isset($work[$pname]['description']))?($work[$pname]['description']):(""),
    "{{DESCRIPTION}}" => (isset($work[$pname]) && isset($work[$pname]['description']))?($work[$pname]['description']):(""),
    ];

    // Current person
    foreach($witems as $wi){
        $repl["{{WI.${wi}.DESCRIPTION}}"]=(isset($work[$pname]) && isset($work[$pname][$wi]) && isset($work[$pname][$wi]["description"]))?($work[$pname][$wi]["description"]):("");
        $totalWIp=0;
        for($i=1;$i<=31;$i++){
            $wiwork=(isset($work[$pname]) && isset($work[$pname][$wi]) && isset($work[$pname][$wi][$i]))?($work[$pname][$wi][$i]):("0");
            $repl["{{WI.${wi}.${i}}}"]=$wiwork;
            $totalWIp+=intval($wiwork);
        }
        $repl["{{WI.${wi}.TOTALHOURS}}"]=$totalWIp;
    }

    // Add OTHER
    $repl["{{WI.OTHER.DESCRIPTION}}"]="";
    $repl["{{WI..DESCRIPTION}}"]="";
    $wi="";
    $totalWIp=0;
    for($i=1;$i<=31;$i++){
        $wiwork=(isset($work[$pname]) && isset($work[$pname][$wi]) && isset($work[$pname][$wi][$i]))?($work[$pname][$wi][$i]):("0");
        $repl["{{WI.${wi}.${i}}}"]=$wiwork;
        $repl["{{WI.OTHER.${i}}}"]=$wiwork;
        $totalWIp+=intval($wiwork);
    }
    $repl["{{WI.${wi}.TOTALHOURS}}"]=$totalWIp;
    $repl["{{WI.OTHER.TOTALHOURS}}"]=$totalWIp;

    // All persons
    foreach($members as $mname=>$member){
        foreach($witems as $wi){
            $repl["{{WI.${wi}.$mname.DESCRIPTION}}"]=(isset($work[$mname]) && isset($work[$mname][$wi]) && isset($work[$mname][$wi]["description"]))?($work[$mname][$wi]["description"]):("");
            $totalWIp=0;
            for($i=1;$i<=31;$i++){
                $wiwork=(isset($work[$mname]) && isset($work[$mname][$wi]) && isset($work[$mname][$wi][$i]))?($work[$mname][$wi][$i]):("0");
                $repl["{{WI.${wi}.$mname.${i}}}"]=$wiwork;
                $totalWIp+=intval($wiwork);
                if(!isset($repl["{{WORK.$mname.${i}}}"]))$repl["{{WORK.$mname.${i}}}"]=0;
                if(!empty($wi))$repl["{{WORK.$mname.${i}}}"]+=intval($wiwork);
            }
            $repl["{{WI.${wi}.$mname.TOTALHOURS}}"]=$totalWIp;
        }
        
        // Add OTHER
        $wi="";
        $repl["{{WI.${wi}.$mname.DESCRIPTION}}"]="";
        $repl["{{WI.OTHER.$mname.DESCRIPTION}}"]="";
        $totalWIp=0;
        for($i=1;$i<=31;$i++){
            $wiwork=(isset($work[$mname]) && isset($work[$mname][$wi]) && isset($work[$mname][$wi][$i]))?($work[$mname][$wi][$i]):("0");
            $repl["{{WI.${wi}.$mname.${i}}}"]=$wiwork;
            $repl["{{WI.OTHER.$mname.${i}}}"]=$wiwork;
            $totalWIp+=intval($wiwork);
            if(!isset($repl["{{WORK.$mname.${i}}}"]))$repl["{{WORK.$mname.${i}}}"]=0;
            $repl["{{WORK.$mname.${i}}}"]+=intval($wiwork);
        }
        $repl["{{WI.${wi}.$mname.TOTALHOURS}}"]=$totalWIp;
        $repl["{{WI.OTHER.$mname.TOTALHOURS}}"]=$totalWIp;
        
    }

    if($prefix===false || empty($prefix))return $repl;
    
    $replRet=[];
    foreach($repl as $k=>$v){
        $replRet["{{".$prefix.substr($k,2)]=$v;
    }
    return $replRet;
}

function processZipFile($zip,$zipname,$repl){
    $content = $zip->getFromName($zipname);
    if($content===false)return false;
    
    //Modify contents:
    foreach($repl as $r=>$c)$content=str_replace($r,$c,$content);
    //Delete the old...
    $zip->deleteName($zipname);
    //Write the new...
    $zip->addFromString($zipname, $content);
    return true;
}


function makeReport($repl,$pname, $date, $rep, $prj){
    $rpath=$prj->getFolderPath()."/reports/".$rep['name'];
    $dpath=$prj->getFolderPath()."/reports_gen/"; @mkdir($dpath);
    $dpath.=$pname."-".$date."-".$rep['name'];

    @unlink($dpath);
    if(!copy($rpath,$dpath))die("Error creating report");

    $zip = new ZipArchive;
    if ($zip->open($dpath) === TRUE) {
        if(processZipFile($zip,"word/document.xml",$repl)){
            $ziptype="docx";
        }else if(processZipFile($zip,"xl/sharedStrings.xml",$repl)){
            $ziptype="xlsx";
            
            // force auto compute formulas on doc open
            $repl['<calcPr']="<calcPr fullCalcOnLoad=\"1\" calcMode=\"auto\"";
            processZipFile($zip,"xl/workbook.xml",$repl);
            unset($repl['<calcPr']);
            
            for($nsheet=1; processZipFile($zip,"xl/worksheets/sheet{$nsheet}.xml",$repl); $nsheet++);
            
        }else{
            die("Cannot find doc to modify");
        }
            
        //And write back to the filesystem.
        $zip->close();
        
        $finalPath=$dpath.".${ziptype}";
        rename($dpath,$finalPath);
        
        //$pdfPath=$dpath.".pdf";
        $soffice=shell_exec("soffice --headless --convert-to pdf \"$finalPath\"");
        
        echo json_encode(["status"=>"OK","soffice"=>$soffice]);
    } else {
        die("Cannot open report");
    }
}

