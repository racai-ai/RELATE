<?php
require_once "project.php";
require_once "projects.php";
require_once "person.php";
require_once "persons.php";
require_once "holidays.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['month']))die("Invalid call");

$date=$_REQUEST['month'];
$month=0;
if(strlen($date)>=7)$month=intval(substr($date,5,2));
$dint=intval(str_replace("-","",$_REQUEST['month']));
$year=intval($dint/100);

$months_ro=["","Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];
$months_en=["","January","February","March","April","May","June","July","August","September","October","November","December"];

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

$holidays=new Holidays();

function isDayEditable($year,$month,$d,$person){
    $days=[1=>31,2=>29,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31];
    if($d>$days[$month])return false;
    
    if(isWeekend("$year-$month-$d"))return false;
    
    $max_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
    if($d>$max_days)return false;
    
    global $holidays;
    if($holidays->isHoliday($year,$month,$d))return false;
    
    if($person->isUnavailable($year, $month, $d))return false;
    
    return true;
}


$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$persons=new Persons();

$otherTotals=$projects->getOtherWorkTotal($_REQUEST['project'],$year,$month);

$witems=[];
foreach($prj->getWorkItems() as $wi)$witems[]=$wi['name'];
sort($witems);
$witems[]="";

$work=$prj->getWorkBreakdown($year, $month);
$totalDay=$prj->getWorkDayTotal($year,$month);

foreach($prj->getTeamMembers() as $per){
    $pname=$per['name'];
    if(!isset($work[$pname])){
        $work[$pname]=["description"=>""];
        foreach($witems as $wi){
            $work[$pname][$wi]=[];
            for($i=1;$i<=31;$i++)$work[$pname][$wi][$i]=0;
        }
    }
    if(!isset($work[$pname]['description']))$work[$pname]['description']="";
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Month editor</title>
<style>
.noedit {
    background-color:gray;
}
.noedit input {
    background-color:gray;
}

input {
    width: 30px;
}

.other {
    background-color: lightgreen;
}
.total {
    background-color: lightgreen;
}
.error {
    background-color: red;
    color:white;
}

.person, .summary {
    background-color: antiquewhite;
    padding: 10px;
    font-weight: bold;
    font-size: large;
}
.person input, h1 input, .summary input {
    width: 150px;
}

.totals {
    font-size: large;
    font-weight: bold;
    padding: 0px;
    margin: 2px;
}

.description span{
    vertical-align:top;
    font-size:large;
}

.errormessages {
    color: red;
}

</style>

<script>
var otherTotals=<?php echo json_encode($otherTotals);?>;
var witems=<?php echo json_encode($witems);?>;
var members=<?php echo json_encode($prj->getTeamMembers());?>;
var persons=<?php echo json_encode($persons->getList()); ?>;

function valueChange(el){
    var data=el.id.split("#");
    updateTotals(data[0]);
}

function getProjectMaxDailyHours(person){
    for(var i=0;i<members.length;i++){
        if(members[i]['name']==person)return parseInt(members[i]['max_daily_hours']);
    }
    return 0;
}

function getProjectHourCost(person){
    for(var i=0;i<members.length;i++){
        if(members[i]['name']==person)return parseFloat(members[i]['hour_cost']);
    }
    return 0;
}

function getOverallMaxDailyHours(person){
    for(var i=0;i<persons.length;i++){
        if(persons[i]['name']==person)return parseInt(persons[i]['max_daily_hours']);
    }
    return 0;
}

function updateTotalsSummary(){
    var monthTotalHours=0;
    var monthTotalCost=0;
    members.forEach(function(mem){
        monthTotalHours+=parseInt(document.getElementById("total#"+mem['name']).innerText);
        monthTotalCost+=parseInt(document.getElementById("totalcost#"+mem['name']).innerText);
    });
    document.getElementById("total#month").innerText=monthTotalHours;
    document.getElementById("totalcost#month").innerText=monthTotalCost;
    
}

function updateTotals(person){
    var allErrors="";
    var allTotal=0;
   
    for(var d=1;d<=31;d++){
        ret=updateTotalsDay(person,d);
        allErrors+=ret["error"];
        allTotal+=ret["total"];
    }
    
    var allCost=allTotal * getProjectHourCost(person);
    document.getElementById("errormessages#"+person).innerText=allErrors;
    document.getElementById("total#"+person).innerText=allTotal;
    document.getElementById("totalcost#"+person).innerText=allCost;
    
    updateTotalsSummary();
}

function addClass(el, cls){
    var prev=document.getElementById(el).getAttribute('class');
    prev+=" "+cls+" ";
    document.getElementById(el).setAttribute('class',prev);
}

function removeClass(el, cls){
    var prev=document.getElementById(el).getAttribute('class');
    prev=prev.replace(" "+cls+" ","");
    document.getElementById(el).setAttribute('class',prev);
}
    

function updateTotalsDay(person, day){
    
    var totalOther=0;
    if((person in otherTotals) && (day in otherTotals[person]))totalOther+=otherTotals[person][day];
    
    var totalPrj=0;
    witems.forEach(function(witem){
        totalPrj+=parseInt(document.getElementById(person+"#"+witem+"#"+day).value);
    });
    
    var total=totalOther+totalPrj;
    document.getElementById("total#"+person+"#"+day).innerText=total;
    
    var prj_maxDailyHours=getProjectMaxDailyHours(person);
    var all_maxDailyHours=getOverallMaxDailyHours(person);
    
    var error="";
    if(totalPrj>prj_maxDailyHours && prj_maxDailyHours>0){
        error+=day+": Project Max Daily Hours\n";
    }
    
    if(total>all_maxDailyHours && all_maxDailyHours>0){
        error+=day+": Overall Max Daily Hours\n";
    }
    
    if(error.length>0){
        console.log(error);
        addClass("total#"+person+"#"+day,"error");
    }else{
        removeClass("total#"+person+"#"+day,"error");
    }
     
    return {error:error, total:total};
}

function saveData(){
    var wdata={};
    members.forEach(function(member){
        var pname=member['name'];
        wdata[pname]={};
        wdata[pname]['description']=document.getElementById("description#"+pname).value;
        witems.forEach(function(witem){
            wdata[pname][witem]={};
            for(var i=1;i<=31;i++){
                wdata[pname][witem][i]=parseInt(document.getElementById(pname+"#"+witem+"#"+i).value);
            }
        });
    });
    
    //console.log(data);
    var data = new FormData();
    data.append('path', 'projects/project_work_syncmonth');
    data.append('project','<?php echo $_REQUEST['project'];?>');
    data.append('month','<?php echo $_REQUEST['month'];?>');
    data.append('work',JSON.stringify(wdata));
    loadData(data,function(d){
        console.log("OK");
    }, function(){ alert("Error synchronizing work"); });

}

function viewProject(){
    window.location='index.php?path=projects/project&name=<?php echo $_REQUEST['project'];?>';
}

function loadData(data,func,error){
    loadDataComplete("index.php","POST",data,func,error);
}

function loadDataComplete(url,method,data,func,error){
    var xhttp = false;
    
    if (window.XMLHttpRequest) {
        // code for modern browsers
        xhttp = new XMLHttpRequest();
    } else {
        // code for old IE browsers
        xhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4){
        if(this.status == 200) {
          var response=this.responseText;
          func(response);
        }else{
          if(error!==undefined && error!==null)
            error();
        }
      }
    };
    if(error!==undefined && error!==null)
      xhttp.error=error;    
      
    xhttp.open(method, url, true);
    if(data!==undefined && data!==null){
    		if(!(data instanceof FormData)){
        		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        xhttp.send(data);
    }else{
        xhttp.send();
    }
}

</script>
</head>
<body>
<?php
echo "<h1>".$months_ro[$month]." ".intval(($dint/100)).    
    "   <input type=\"button\" onclick=\"viewProject();\" name=\"viewproject\" value=\"View Project\"/>".
    "</h1>\n";

$monthTotalHours=0;
$monthTotalCost=0;

foreach($prj->getTeamMembers() as $per){
    $pname=$per['name'];
    $person=new Person($persons, $pname);
    $person->loadData();
    $totalHours=0;
    echo "<div class=\"teamMember\">";
    echo "<p class=\"person\">$pname  <input type=\"button\" onclick=\"saveData();\" name=\"save\" value=\"Save\"/></p>";
    
    echo "<table>";
    
    // Days
    echo "<tr><td>&nbsp;</td>";
    for($i=1;$i<=31;$i++){
        if(!isDayEditable($year,$month,$i,$person))$cls="noedit";
        else $cls="edit";
        echo "<td class=\"$cls\" align=\"center\">$i</td>";
    }
    echo "</tr>\n";

    foreach($witems as $wi){
        echo "<tr><td>$wi</td>";
        for($i=1;$i<=31;$i++){
            if(!isDayEditable($year,$month,$i,$person))$cls="noedit";
            else $cls="edit";
            echo "<td class=\"$cls\">".
                "<input type=\"text\" id=\"${pname}#${wi}#$i\" value=\"".$work[$pname][$wi][$i]."\" size=\"2\" onchange=\"valueChange(this);\"/>".
                "</td>";
            $totalHours+=$work[$pname][$wi][$i];
        }
        echo "</tr>";
    }
    
    // Other
    echo "<tr class=\"other\"><td>OTHER</td>";
    for($i=1;$i<=31;$i++){
        if(!isDayEditable($year,$month,$i,$person))$cls="noedit";
        else $cls="edit";
        $n=0;
        if(isset($otherTotals[$pname]) && isset($otherTotals[$pname][$i]))$n=$otherTotals[$pname][$i];
        echo "<td class=\"$cls\">$n</td>";
    }
    echo "</tr>\n";    
    
    // Total
    echo "<tr class=\"total\"><td>TOTAL</td>";
    for($i=1;$i<=31;$i++){
        if(!isDayEditable($year,$month,$i,$person))$cls="noedit";
        else $cls="edit";
        $n1=0;
        if(isset($otherTotals[$pname]) && isset($otherTotals[$pname][$i]))$n1=$otherTotals[$pname][$i];
        $n2=0;
        if(isset($totalDay[$pname]) && isset($totalDay[$pname][$i]))$n2=$totalDay[$pname][$i];
        $n=$n1+$n2;
        echo "<td class=\"$cls\" id=\"total#$pname#$i\">$n</td>";
    }
    echo "</tr>\n";

    echo "</table>";
    
    $member=[];
    foreach($prj->getTeamMembers() as $tm){
        if($tm['name']==$pname)$member=$tm;
    }
    $totalCost=$totalHours*$member['hour_cost'];
    echo "<p class=\"totals\">Total hours: <span id=\"total#$pname\">$totalHours</span></p>";
    echo "<p class=\"totals\">Total cost: <span id=\"totalcost#$pname\">$totalCost</span></p>";
    $monthTotalHours+=$totalHours;
    $monthTotalCost+=$totalCost;
    
    echo "<p class=\"errormessages\" id=\"errormessages#$pname\"></p>";
    
    echo "<p class=\"description\"><span>Description:</span><textarea id=\"description#$pname\" rows=\"4\" cols=\"150\">".$work[$pname]["description"]."</textarea></p>";
    
    echo "</div>\n";
}

echo "<div class=\"summary\">";
echo "<p class=\"person\">Month Summary  <input type=\"button\" onclick=\"saveData();\" name=\"save\" value=\"Save\"/></p>";
echo "<p class=\"totals\">Total hours: <span id=\"total#month\">$monthTotalHours</span></p>";
echo "<p class=\"totals\">Total cost: <span id=\"totalcost#month\">$monthTotalCost</span></p>";
echo "</div>\n";

?>
<script>
members.forEach(function(member){updateTotals(member['name']);});
</script>
</body>
</html>
