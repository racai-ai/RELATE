var corpus_lang="{{CORPUS_LANG}}";

function setAttribute(obj,attr,value){
    var ob=document.getElementById(obj);
    if(ob!=null)
        ob.setAttribute(attr,value);
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
    
    /*if(n==6){
          ... functie custom
    } else if(n==7){
          ... functie custom
    } */
}

/* nu merge direct in clipboard, e mai ok ctrl+c
function gridCopy(){
    var $grid = $(this).closest(".pq-grid");
    $grid.pqGrid("copy");
}*/

function gridAddCSV(){

            var $frm = $("form#crud-form-csv");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-csv").dialog({ title: "Add File CSV/TSV", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-csv").dialog("open");
} 

function gridAddTXT(){

            var $frm = $("form#crud-form-txt");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-txt").dialog({ title: "Add File TEXT", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-txt").dialog("open");
} 

function gridAddZIPTXT(){

            var $frm = $("form#crud-form-ziptext");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-ziptext").dialog({ title: "Add File ZIP with TEXT", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-ziptext").dialog("open");
} 

function gridEdit(){
}

function gridDelete(){
}

function gridAddTask(htmlId,title){
      var $frm = $("form#crud-form-task-"+htmlId);
      //$frm.find("input").val("");

      $("#popup-dialog-crud-task-"+htmlId).dialog({ title: title, buttons: {
          Add: function () {
              $frm.submit();
          },
          Cancel: function () {
              $(this).dialog("close");
          }
      }
      });
      $("#popup-dialog-crud-task-"+htmlId).dialog("open");
}

function gridAddTaskBasic(){ gridAddTask("basic","Add Basic Tagging task"); }
function gridAddTaskChunking(){ gridAddTask("chunk","Add Chunking task"); }
function gridAddTaskStatistics(){ gridAddTask("stat","Add Statistics task"); }
function gridAddTaskCreateZIPTXT(){ gridAddTask("createziptxt","Add task for ZIP TEXT creation"); }
function gridAddTaskCreateZIPBasicTagging(){  gridAddTask("createzipbasic","Add task for ZIP ANNOTATED creation"); }
function gridAddTaskCleanup(){ gridAddTask("cleanup","Add Cleanup task"); }
function gridAddTaskIateEurovoc(){ gridAddTask("iateeurovoc","Add task for annotating with IATE and EUROVOC"); }

function openStatsWordForm(){
    window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_wordform.csv";
}

function openStatsLemma(){
    window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_lemma.csv";
}

function openStatsWordFormDF(){
    window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_wordformdf.csv";
}

function openStatsLetters(){
    window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_letters.csv";
}

var $grid=false;
var $gridTasks=false;
var $gridBasicTagging=false;
var $gridStatistics=false;
var $gridArchives=false;

function initGridFiles(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add TEXT', listeners: [{ click: gridAddTXT}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add CSV/TSV', listeners: [{ click: gridAddCSV}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add ZIP TEXT', listeners: [{ click: gridAddZIPTXT}], icon: 'ui-icon-plus' },
                //{ type: 'button', label: 'Edit', listeners: [{ click: gridEdit}], icon: 'ui-icon-pencil' },
                //{ type: 'button', label: 'Delete', listeners: [{ click: gridDelete}], icon: 'ui-icon-minus' }                
            ]
        };        

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Files list"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                if(ui.rowData.type=="csv"){
                    window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
                }else{
                    window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
                }
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name" },
            { title: "Type", dataType: "string", dataIndx: "type" },
            { title: "Description", dataType: "string", dataIndx: "desc" },
            { title: "User", dataType: "string", dataIndx: "created_by" },
            { title: "Creation Date", dataType: "string", dataIndx: "created_date" }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/files_get&name={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $grid = $("#grid").pqGrid(obj);

          $("#popup-dialog-crud-csv").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-txt").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-ziptext").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
}

function initGridTasks(){
        var toolbar = { items: [
                { type: 'button', label: 'Add BASIC TAGGING', listeners: [{ click: gridAddTaskBasic}], icon: 'ui-icon-plus' },
               // { type: 'button', label: 'Add CHUNKING', listeners: [{ click: gridAddTaskChunking}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add STATISTICS', listeners: [{ click: gridAddTaskStatistics}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Create ZIP TEXT', listeners: [{ click: gridAddTaskCreateZIPTXT}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Create ZIP ANNOTATED', listeners: [{ click: gridAddTaskCreateZIPBasicTagging}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'CLEANUP', listeners: [{ click: gridAddTaskCleanup}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'IATE/EUROVOC', listeners: [{ click: gridAddTaskIateEurovoc}], icon: 'ui-icon-plus' },
            ]
        };
        
        if(corpus_lang=="en"){
            toolbar.items[toolbar.items.length]={ type: 'button', label: 'Add CHUNKING', listeners: [{ click: gridAddTaskChunking}], icon: 'ui-icon-plus' };
        }        

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Corpus tasks"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            /*, rowDblClick: function( event, ui ) {
                window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
            }*/            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Type", dataType: "string", dataIndx: "type" },
            { title: "Status", dataType: "string", dataIndx: "status" },
            { title: "Description", dataType: "string", dataIndx: "desc" },
            { title: "User", dataType: "string", dataIndx: "created_by" },
            { title: "Creation Date", dataType: "string", dataIndx: "created_date" }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "created_date",
            sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/task_getallbycorpus&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridTasks = $("#gridTasks").pqGrid(obj);

          $("#popup-dialog-crud-task-basic").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-chunk").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-stat").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-createziptxt").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-createzipbasic").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-cleanup").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });
        
          $("#popup-dialog-crud-task-iateeurovoc").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });
        
}

function initGridBasicTagging(){
        var toolbar = { };

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Basic tagging"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
                window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=conllu&file=basictagging/"+ui.rowData.name;
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name" },
            { title: "Type", dataType: "string", dataIndx: "type" },
            { title: "Size", dataType: "string", dataIndx: "size" }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/files_getbasictagging&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridBasicTagging = $("#gridBasicTagging").pqGrid(obj);
}

function downloadStats(){
    var data=$gridStatistics.pqGrid("getData",{ dataIndx: [0, 1] });
    
    var txt="";
    for(var i=0;i<data.length;i++){
        txt+=data[i][0]+","+data[i][1]+"\n";
    }        
    
    saveTextAsFile(null,"stats.csv","text/plain",txt);
}

function saveTextAsFile(id,fname,type,content)
{
    var textToWrite = null;
    if(id===null)textToWrite=content;
    else textToWrite=document.getElementById(id).value;
    
    var textFileAsBlob = new Blob([textToWrite], {type:type});
    var fileNameToSaveAs = fname;

    var downloadLink = document.createElement("a");
    downloadLink.download = fileNameToSaveAs;
    downloadLink.innerHTML = "Download File";
    if (window.webkitURL != null)
    {
        // Chrome allows the link to be clicked
        // without actually adding it to the DOM.
        //if(content!=null)downloadLink.href =content;
        //else 
        downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
    }
    else
    {
        // Firefox requires the link to be added to the DOM
        // before it can be clicked.
        //if(content!=null)downloadLink.href =content;
        //else 
        downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
        downloadLink.onclick = destroyClickedElement;
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
    }

    downloadLink.click();
}

function destroyClickedElement(event)
{
    document.body.removeChild(event.target);
}


function initGridStatistics(){
        var toolbar = { items: [
                { type: 'button', label: 'Download Stats', listeners: [{ click: downloadStats}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'View WordForm Stats', listeners: [{ click: openStatsWordForm}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'View Lemma Stats', listeners: [{ click: openStatsLemma}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'View WordForm Doc Freq', listeners: [{ click: openStatsWordFormDF}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'View Letters Stats', listeners: [{ click: openStatsLetters}], icon: 'ui-icon-plus' },
         ]};

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Statistics"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
            , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            /*, rowDblClick: function( event, ui ) {
                window.location.href="index.php?path=corpus/&corpus={{CORPUS_NAME}}&type=conllu&file=basictagging/"+ui.rowData.name;
            } */           
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Key", dataType: "string", dataIndx: "0", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Value", dataType: "float", dataIndx: "1" }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            //sortIndx: "name",
            //sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/stats_get&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridStatistics = $("#gridStatistics").pqGrid(obj);
}

function initGridArchives(){
        var toolbar = { };

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Archives"
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
                window.location.href="index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+ui.rowData.fname;
            }           
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "File", dataType: "string", dataIndx: "fname", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Size", dataType: "string", dataIndx: "size" }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            //sortIndx: "name",
            //sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/archives_get&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridArchives = $("#gridArchives").pqGrid(obj);
}



$(document).ready(function () {
    initGridFiles();
    initGridTasks(); 
    initGridBasicTagging(); 
    initGridStatistics(); 
    initGridArchives(); 
    
    var h = window.location.hash.substr(1);
    
    if(h=="tasks")showOutput(2,8);      
    else if(h=="basictagging")showOutput(3,8);      
    else if(h=="statistics")showOutput(4,8);      
    else if(h=="archives")showOutput(5,8);      
});
