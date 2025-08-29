// Fix dropdowns that close automatically
setInterval(function(){
    var elements=document.getElementsByTagName("select");
    for(var i=0;i<elements.length;i++)elements[i].onclick=function(e){e.stopPropagation();}
},500);

function convertSize(s){
    if(s.length==0)return 0;
    var data=s.split(" ");
    var sz=parseInt(data[0]);
    if(data[1]=="Kb")return sz*1024;
    if(data[1]=="Mb")return sz*1024*1024;
    if(data[1]=="Gb")return sz*1024*1024*1024;
    if(data[1]=="Tb")return sz*1024*1024*1024*1024;
    return sz;
}

function sizeSort(rowData1,rowData2,dataIndx){
    var s1=convertSize(rowData1[dataIndx]);
    var s2=convertSize(rowData2[dataIndx]);
    
    if(s1>s2)return 1;
    if(s1<s2)return -1;
    return 0;
}

var hasRights={{HAS_RIGHTS}};
var hasProperties={{HAS_PROPERTIES}};

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



function setAttribute(obj,attr,value){
    var ob=document.getElementById(obj);
    if(ob!=null)
        ob.setAttribute(attr,value);
}

function getAttribute(obj,attr){
    var ob=document.getElementById(obj);
    if(ob!=null)
        return ob.getAttribute(attr);
    return "";
}


function showOutput(n,num, hash){
    for(var i=1;i<=num;i++){
        if(i==n){
          setAttribute("output"+i,"style","display:block; height:100%; overflow:auto;");
          setAttribute("bOutput"+i,"class","btn cur-p btn-success");
        }else{
          setAttribute("output"+i,"style","display:none");
          setAttribute("bOutput"+i,"class","btn cur-p btn-secondary");
        }
    }
    
    if(hash!==undefined && hash!==null){
        window.location.hash="#"+hash;    
    }
    
}

var $gridRights=false;

/********** GRID TEAM ******************/
var $gridTeam=false;

function gridTeam_init(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add Member', listeners: [{ click: gridTeam_Add}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Team members"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                    //last_viewed_file=ui.rowData.name;
                    //viewFileText(ui.rowData.name,true);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
            }            
        };
        obj.columnTemplate = { minWidth: '50px', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Supervisor", dataType: "string", dataIndx: "supervisor", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Position", dataType: "string", dataIndx: "position", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "H Cost", dataType: "string", dataIndx: "hour_cost", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Daily H", dataType: "string", dataIndx: "max_daily_hours", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Month H", dataType: "string", dataIndx: "max_month_hours", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Max M Cost", dataType: "string", dataIndx: "max_month_cost", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/project_team_get&project={{PROJECT_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridTeam = $("#gridTeam").pqGrid(obj);

          $("#teamadd-dialog").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTeam" }); },
            autoOpen: false
        });
}

function gridTeam_Add(){
    var $frm = $("form#teamadd-form");
    //$frm.find("input").val("");

    $("#teamadd-dialog").dialog({ title: "Add Team Member", buttons: {
        Add: function () { 
            
            var data = new FormData();
            data.append('path', 'projects/project_team_add');
            data.append('project','{{PROJECT_NAME}}');
            data.append('name', $frm.find("select[name='name']").val());
            data.append('supervisor', $frm.find("select[name='supervisor']").val());
            data.append('position', $frm.find("input[name='position']").val());
            data.append('hour_cost', $frm.find("input[name='hour_cost']").val());
            data.append('max_daily_hours', $frm.find("input[name='max_daily_hours']").val());
            data.append('max_month_hours', $frm.find("input[name='max_month_hours']").val());
            data.append('max_month_cost', $frm.find("input[name='max_month_cost']").val());
            var dia=$(this);
            loadData(data,function(d){
                $gridTeam.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error adding new team member"); });

        },
        Cancel: function () { $(this).dialog("close"); }
    }});
    $("#teamadd-dialog").dialog("open");
} 


/*********** GRID WORK ******************/
var $gridWork=false;

function gridWork_init(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add Work', listeners: [{ click: gridWork_Add}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Edit MONTH', listeners: [{ click: gridWork_EditMonth}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Work"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                    //last_viewed_file=ui.rowData.name;
                    //viewFileText(ui.rowData.name,true);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
            }            
        };
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "WorkItem", dataType: "string", dataIndx: "workitem", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Date", dataType: "string", dataIndx: "date", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Hours", dataType: "string", dataIndx: "hours", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Description", dataType: "string", dataIndx: "description", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/project_work_get&project={{PROJECT_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridWork = $("#gridWork").pqGrid(obj);

        $("#workadd-dialog").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridWork" }); },
            autoOpen: false
        });
        $("#workeditmonth-dialog").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridWork" }); },
            autoOpen: false
        });
}

function gridWork_Add(){
    var $frm = $("form#workadd-form");
    //$frm.find("input").val("");

    $("#workadd-dialog").dialog({ title: "Add Work", buttons: {
        Add: function () {
            var data = new FormData();
            data.append('path', 'projects/project_work_add');
            data.append('project','{{PROJECT_NAME}}');
            data.append('workitem', $frm.find("select[name='workitem']").val());
            data.append('name', $frm.find("select[name='name']").val());
            data.append('date', $frm.find("input[name='date']").val());
            data.append('hours', $frm.find("input[name='hours']").val());
            data.append('description', $frm.find("textarea[name='description']").val());
            var dia=$(this);
            loadData(data,function(d){
                $gridWork.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error adding new team member"); });

            
        },
        Cancel: function () { $(this).dialog("close"); }
    }});
    $("#workadd-dialog").dialog("open");
} 

function gridWork_EditMonth(){
    var $frm = $("form#workeditmonth-form");
    //$frm.find("input").val("");

    $("#workeditmonth-dialog").dialog({ title: "Edit MONTH", buttons: {
        Add: function () {
            window.location='index.php?path=projects/project_work_editmonth&project={{PROJECT_NAME}}&month='+$frm.find("input[name='month']").val();
        },
        Cancel: function () { $(this).dialog("close"); }
    }});
    $("#workeditmonth-dialog").dialog("open");
} 


/********** GRID REPORTS ******************/
var $gridReports=false;

function gridReports_init(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add Report', listeners: [{ click: gridReports_Add}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Reports"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                    //last_viewed_file=ui.rowData.name;
                    //viewFileText(ui.rowData.name,true);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
            }            
        };
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Description", dataType: "string", dataIndx: "description", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/project_reports_get&project={{PROJECT_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridReports = $("#gridReports").pqGrid(obj);

          $("#reportsadd-dialog").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridReports" }); },
            autoOpen: false
        });
}

function gridReports_Add(){
    var $frm = $("form#reportsadd-form");
    //$frm.find("input").val("");

    $("#reportsadd-dialog").dialog({ title: "Add Report", buttons: {
        Add: function () { 
            
            var data = new FormData();
            data.append('path', 'projects/project_reports_add');
            data.append('project','{{PROJECT_NAME}}');
            data.append('name', $frm.find("input[name='name']").val());
            data.append('description', $frm.find("textarea[name='description']").val());
            data.append('file', document.getElementById("fileReport").files[0]);
            var dia=$(this);
            loadData(data,function(d){
                $gridReports.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error adding new report"); });

        },
        Cancel: function () { $(this).dialog("close"); }
    }});
    $("#reportsadd-dialog").dialog("open");
} 

/********** GRID REPORTS GENERATED ******************/
var $gridReportsGenerated=false;

function gridReportsGenerated_init(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Generate Monthly', listeners: [{ click: gridReports_GenerateMonth}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Reports"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                    //last_viewed_file=ui.rowData.name;
                    //viewFileText(ui.rowData.name,true);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
                    window.location='index.php?path=projects/project_reports_generated_file&project={{PROJECT_NAME}}&file='+ui.rowData.name;
            }            
        };
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/project_reports_generated_get&project={{PROJECT_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridReportsGenerated = $("#gridReportsGenerated").pqGrid(obj);

        $("#reportsGenerateMonth-dialog").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridReportsGenerated" }); },
            autoOpen: false
        });
}

function gridReports_GenerateMonth(){
    var $frm = $("form#reportsGenerateMonth-form");
    //$frm.find("input").val("");

    $("#reportsGenerateMonth-dialog").dialog({ title: "Generate Monthly Report", buttons: {
        Add: function () { 
            
            var data = new FormData();
            data.append('path', 'projects/project_reports_generate');
            data.append('project','{{PROJECT_NAME}}');
            data.append('name', $frm.find("select[name='name']").val());
            data.append('type', 'month');
            data.append('date', $frm.find("input[name='month']").val());
            data.append('signdate', $frm.find("input[name='signdate']").val());
            data.append('person', $frm.find("select[name='person']").val());
            var dia=$(this);
            loadData(data,function(d){
                $gridReportsGenerated.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error generating report"); });

        },
        Cancel: function () { $(this).dialog("close"); }
    }});
    $("#reportsGenerateMonth-dialog").dialog("open");
} 


/********** GRID WORK ITEMS ******************/
var $gridWorkItems=false;

function gridWorkItems_init(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add Work Item', listeners: [{ click: gridWorkItems_Add}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Work Items"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                    //last_viewed_file=ui.rowData.name;
                    //viewFileText(ui.rowData.name,true);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
            }            
        };
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Description", dataType: "string", dataIndx: "description", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/project_workitems_get&project={{PROJECT_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridWorkItems = $("#gridWorkItems").pqGrid(obj);

          $("#workitemsadd-dialog").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridWorkItems" }); },
            autoOpen: false
        });
}

function gridWorkItems_Add(){
    var $frm = $("form#workitemsadd-form");
    //$frm.find("input").val("");

    $("#workitemsadd-dialog").dialog({ title: "Add Work Item", buttons: {
        Add: function () { 
            
            var data = new FormData();
            data.append('path', 'projects/project_workitems_add');
            data.append('project','{{PROJECT_NAME}}');
            data.append('name', $frm.find("input[name='name']").val());
            data.append('description', $frm.find("textarea[name='description']").val());
            var dia=$(this);
            loadData(data,function(d){
                $gridWorkItems.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error adding new work item"); });

        },
        Cancel: function () { $(this).dialog("close"); }
    }});
    $("#workitemsadd-dialog").dialog("open");
} 



function gridEdit(){
}

function gridDelete(){
}

var previousHash="";

function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}
 

function changeFileExtension(file,ext){
		var p=file.lastIndexOf(".");
		if(p==-1)return file+"."+ext;
		return file.substring(0,p+1)+ext;
}

function gridRights_add(){
    var $frm = $("form#crud-form-rights");
    $frm.find("input").val("");

    $("#popup-dialog-crud-rights").dialog({ title: "Add access control entry", buttons: {
        Add: function () {
            var pattern=$frm.find("input[name='username_pattern']").val();
            var rights=$frm.find("select[name='rights']").val();
            if(pattern.length<1 || rights.length<1){alert("Invalid data"); return;}
            
            var data = new FormData();
            data.append('path', 'projects/project_rights_add');
            data.append('project','{{PROJECT_NAME}}');
            data.append('pattern', pattern);
            data.append('rights', rights);
            var dia=$(this);
            loadData(data,function(d){
                $gridRights.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error setting new rights"); });
        },
        Cancel: function () {
            $(this).dialog("close");
        }
    }
    });
    $("#popup-dialog-crud-rights").dialog("open");
}    

function gridRights_edit(){
    var $frm = $("form#crud-form-rights");
    $frm.find("input").val("");

	var data=$gridRights.pqGrid("selection", {type:'row', method:'getSelection'});
	if(data===undefined || data[0]===undefined){alert("Select an access control entry to edit");return ;}
	data=data[0].rowData;;

	$frm.find("input[name='username_pattern']").val(data['pattern']);
	$frm.find("select[name='rights']").val(data['rights']);

    var old_pattern=data['pattern'];
    var old_rights=data['rights'];

    $("#popup-dialog-crud-rights").dialog({ title: "Edit access control entry", buttons: {
        Add: function () {
            var pattern=$frm.find("input[name='username_pattern']").val();
            var rights=$frm.find("select[name='rights']").val();
            if(pattern.length<1 || rights.length<1){alert("Invalid data"); return;}
            
            var data = new FormData();
            data.append('path', 'projects/project_rights_edit');
            data.append('project','{{PROJECT_NAME}}');
            data.append('pattern', pattern);
            data.append('rights', rights);
            data.append('old_pattern', old_pattern);
            data.append('old_rights', old_rights);
            var dia=$(this);
            loadData(data,function(d){
                $gridRights.pqGrid('refreshDataAndView');
                dia.dialog("close");
            }, function(){ alert("Error setting new rights"); });
        },
        Cancel: function () {
            $(this).dialog("close");
        }
    }
    });
    $("#popup-dialog-crud-rights").dialog("open");
}    

function gridRights_delete(){
	var data=$gridRights.pqGrid("selection", {type:'row', method:'getSelection'});
	if(data===undefined || data[0]===undefined){alert("Select an access control entry to delete");return ;}
	data=data[0].rowData;
    var old_pattern=data['pattern'];
    var old_rights=data['rights'];
    
    if(!confirm("Delete entry ["+data['pattern']+"] ==> ["+data['rights']+"] ?"))return ;
    var data = new FormData();
    data.append('path', 'projects/project_rights_delete');
    data.append('project','{{PROJECT_NAME}}');
    data.append('old_pattern', old_pattern);
    data.append('old_rights', old_rights);
		
    loadData(data,function(d){
        $gridRights.pqGrid('refreshDataAndView');
    },function(){
        alert("Error deleting access control entry");
    });
}

function initGridRights(){
        if(!hasRights)return ;

        var toolbar = { items:[
            { type: 'button', label: 'Add', listeners: [{ click: gridRights_add}], icon: 'ui-icon-plus' },
            { type: 'button', label: 'Edit', listeners: [{ click: gridRights_edit}], icon: 'ui-icon-edit' },
            { type: 'button', label: 'Delete', listeners: [{ click: gridRights_delete}], icon: 'ui-icon-minus' },
        ]};

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Access control"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                //viewFileVideo(ui.rowData.fname);
                gridRights_edit();
            }           
        };
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Username Pattern", dataType: "string", dataIndx: "pattern", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Rights", dataType: "string", dataIndx: "rights", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            //sortIndx: "name",
            //sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/project_rights_get&project={{PROJECT_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridRights = $("#gridRights").pqGrid(obj);
        
        $("#popup-dialog-crud-rights").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridRights" }); },
            autoOpen: false
        });
        
}


function showBasedOnHash(hash){
    var n=8;
    if(hash=="project")showOutput(1,n);
    else if(hash=="team")showOutput(2,n);
    else if(hash=="work")showOutput(3,n);
    else if(hash=="reports")showOutput(6,n);
    else if(hash=="reportsgen")showOutput(7,n);
    else if(hash=="workitems")showOutput(8,n);
    else if(hash=="properties" && hasProperties)showOutput(4,n);
    else if(hash=="rights" && hasRights)showOutput(5,n);
}

$(document).ready(function () {
    gridTeam_init();
    gridWork_init();
    gridReports_init();
    gridReportsGenerated_init();
    gridWorkItems_init();
    initGridRights(); 
	    
    var h = window.location.hash.substr(1);
    
    showBasedOnHash(h);
        
});
