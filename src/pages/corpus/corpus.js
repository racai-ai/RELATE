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

var corpus_lang="{{CORPUS_LANG}}";
var recorder_name="{{RECORDER_NAME}}";

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

function gridAddStandoff(){

            var $frm = $("form#crud-form-standoff");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-standoff").dialog({ title: "Add Standoff Metadata File", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-standoff").dialog("open");
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
function gridAddTaskMarcell(){ gridAddTask("marcell","Add task for exporting to Marcell project"); }

function openStatsWordForm(){
    viewFileCSV("statistics/list_wordform.csv","csv2");
    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_wordform.csv";
}

function openStatsLemma(){
    viewFileCSV("statistics/list_lemma.csv","csv2");
    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_lemma.csv";
}

function openStatsWordFormDF(){
    viewFileCSV("statistics/list_wordformdf.csv","csv2");
    ///window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_wordformdf.csv";
}

function openStatsLetters(){
    viewFileCSV("statistics/list_letters.csv","csv2");
    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=csv2&file=statistics/list_letters.csv";
}

var $grid=false;
var $gridStandoff=false;
var $gridTasks=false;
var $gridBasicTagging=false;
var $gridStatistics=false;
var $gridArchives=false;
var $gridAudio=false;

var previousHash="";


function recorderClick(){
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:block;");

		if(recorder_name==="" || recorder_name===false || recorder_name===undefined || recorder_name===null || recorder_name.trim()===""){
				recorder_name=window.prompt("Name used for recorded files:\n\n(Please keep it short, only letters and numbers are allowed)\n","");
				if(recorder_name==="" || recorder_name===false || recorder_name===undefined || recorder_name===null || recorder_name.trim()===""){
			    setAttribute("output","style","display:block;");
			    setAttribute("loading","style","display:none;");
					return ;
				}						
				recorder_name=recorder_name.trim();
				
			 loadData("path=platform/set_profile_values&k0=recorder_name&v0="+recorder_name,function(data){
			 			console.log(data);
			 			data=JSON.parse(data);
			 			if(data["status"]==="OK"){
			 					document.getElementById("divRecorderName").innerText=recorder_name;
						 		recorderShow();
						 }else{
			        alert("Error setting RecorderName");
			        setAttribute("loading","style","display:none;");
			        setAttribute("output","style","display:block;");
						 
						 }
			    },function(){
			        alert("Error setting RecorderName");
			        setAttribute("loading","style","display:none;");
			        setAttribute("output","style","display:block;");
			    });
			    
			    return ;
						
		}

		recorderShow();
    
}

function recorderShow(){
			 loadData("path=recorder/get_data&corpus={{CORPUS_NAME}}",function(data){
			 			console.log(data);
			 			data=JSON.parse(data);
			 			if(data["status"]==="OK"){
							    var h=window.location.hash;
							    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
							    window.location.hash="#recorder:"+previousHash;
							    
							    document.getElementById("recorderSentence").innerText=data["sentence"];
							    document.getElementById("recorderCurrent").innerText=data["current"];
							    document.getElementById("recorderTotal").innerText=data["total"];
							    
									    if(data['current']>data['total']){
									    		setAttribute("divRecorderDone","style","display:block; text-align:center");
									    		setAttribute("divRecorderControls","style","display:none;");
											}else{
									    		setAttribute("divRecorderDone","style","display:none;");
									    		setAttribute("divRecorderControls","style","display:block; text-align:center");
											}
							    

							    setAttribute("loading","style","display:none;");
								document.getElementById("bStopRecorder").disabled = true;
								document.getElementById("bStartRecorder").disabled = false;
						    setAttribute("bStartRecorder","style","display:inline; width:100px; text-align:center");
						    setAttribute("divRecording","style","display:none;");
							    setAttribute("recorderView","style","display:block;");
						 }else{
			        alert("Error loading recorder data");
			        setAttribute("loading","style","display:none;");
			        setAttribute("output","style","display:block;");
						 
						 }
			    },function(){
			        alert("Error loading recorder data");
			        setAttribute("loading","style","display:none;");
			        setAttribute("output","style","display:block;");
			    });

}

function closeRecorder(){
    setAttribute("recorderView","style","display:none;");
    setAttribute("output","style","display:block;");
    window.location.hash=previousHash;
}

var recorderStream=false;
var recorderWebAudioRecorder=false;

function startRecorder(){

		navigator.mediaDevices.getUserMedia({audio:true,video:false}).then(function(stream) {
				console.log("getUserMedia() success, stream created, initializing WebAudioRecorder...");

				document.getElementById("bStartRecorder").disabled = true;
				

			 //assign to gumStream for later use 
			 recorderStream = stream;
			 
			 var AudioContext = window.AudioContext || window.webkitAudioContext;			 
			 var audioContext = new AudioContext();
			 /* use the stream */
			 var input = audioContext.createMediaStreamSource(stream);
			 //stop the input from playing back through the speakers 
			 input.connect(audioContext.destination) //get the encoding 
			 var recorder = new WebAudioRecorder(input, {
			     workerDir: "extern/web_audio_recorder/",
			     encoding: "wav",
			     onEncoderLoading: function(recorder, encoding) {
			         // show "loading encoder..." display 
			         console.log("Loading " + encoding + " encoder...");
			     },
			     onEncoderLoaded: function(recorder, encoding) {
			         // hide "loading encoder..." display
			         console.log(encoding + " encoder loaded");
			     }
			 });
			 
				recorder.onComplete = function(recorder, blob) {
				    console.log("Encoding complete");
				    
						var data = new FormData();
						data.append('path', 'recorder/upload');
						data.append('corpus','{{CORPUS_NAME}}');
						data.append('blob', blob);				    
				    loadData(data,function(d){
				    		console.log("Uploaded");
				    		console.log(d);
				    		$gridAudio.pqGrid('refreshDataAndView');
				    		try{
						    		var data=JSON.parse(d);
						    		if(data["status"]==="OK"){
									    document.getElementById("recorderSentence").innerText=data["sentence"];
									    document.getElementById("recorderCurrent").innerText=data["current"];
									    document.getElementById("recorderTotal").innerText=data["total"];
									    
									    if(data['current']>data['total']){
									    		setAttribute("divRecorderDone","style","display:block; text-align:center");
									    		setAttribute("divRecorderControls","style","display:none;");
											}
						    				
										}else{
												alert("Error uploading file");
										}
								}catch(err){
										console.log(err);
										alert("Error uploading file");
								}

								document.getElementById("bStartRecorder").disabled = false;
						    setAttribute("bStartRecorder","style","display:inline; width:100px; text-align:center");
						    setAttribute("divRecording","style","display:none;");
								document.getElementById("bStopRecorder").disabled = true;

						},function(){
								console.log("Error uploading");

								document.getElementById("bStartRecorder").disabled = false;
						    setAttribute("bStartRecorder","style","display:inline; width:100px; text-align:center");
						    setAttribute("divRecording","style","display:none;");
								document.getElementById("bStopRecorder").disabled = true;

						});
						
						
				}	
				
				recorder.onError = function(recorder, err) {
						console.log("Recorder error ");
						console.log(err);
				}
				
				
				recorder.setOptions({
						timeLimit: 60 * 60, // 1h
						encodeAfterRecord: false,
						wav: {
						},
						mp3: {
							bitRate: 160
						}
				});
				
				recorderWebAudioRecorder=recorder;
				
				recorder.startRecording();	

				document.getElementById("bStartRecorder").disabled = false;
		    setAttribute("bStartRecorder","style","display:none");
		    setAttribute("divRecording","style","display:inline; font-color:red; width:100px;");
				document.getElementById("bStopRecorder").disabled = false;
					 

		}).catch(function(err) {
		
				console.log(err); 
				alert("Can not capture audio!");

				document.getElementById("bStartRecorder").disabled = false;
		    setAttribute("bStartRecorder","style","display:inline; width:100px; text-align:center");
		    setAttribute("divRecording","style","display:none;");
				document.getElementById("bStopRecorder").disabled = true;

		});

}

function stopRecorder(){
		document.getElementById("bStopRecorder").disabled = true;
		console.log("Stopping recorder ...");
		recorderStream.getAudioTracks()[0].stop();
		recorderWebAudioRecorder.finishRecording();
}


function closeFileViewerText(){
    setAttribute("fileViewerText","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}

function viewFileText(file){
    currentFileView=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:block;");
    setAttribute("fileViewerTextDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");

    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewertext:"+file+":"+previousHash;

    loadData("path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file,function(data){
        document.getElementById('textFileViewerText').value=data;
        setAttribute("loading","style","display:none;");
        setAttribute("fileViewerText","style","display:block;");
        document.getElementById("corpusfilename").innerHTML="File: <b>"+file+"</b>";
    },function(){
        alert("Error loading text");
        setAttribute("loading","style","display:none;");
        setAttribute("output","style","display:block;");
    });
    
}

var $fileViewerCSVgrid=false;
var currentFileView="";

function closeFileViewerCSV(){
    setAttribute("fileViewerCSV","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
    $fileViewerCSVgrid.pqGrid('destroy');
    $fileViewerCSVgrid=false;
    setAttribute("fileViewerCSVgrid","class","");
}

function viewFileCSV(file,type){
    currentFileView=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:block;");
    setAttribute("fileViewerCSVDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");

    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewercsv:"+file+":"+previousHash;

    if($fileViewerCSVgrid!==false)$fileViewerCSVgrid.pqGrid('destroy');

    var toolbar = { items: [ ] };

    var obj = {
        width: "100%"
        , height: 400
        , resizable: true
        , title: "File View"
        , showBottom: false
        , editModel: {clicksToEdit: 2}
        //, scrollModel: { autoFit: true }
        //, toolbar: toolbar
        , editable: false
        , resizable: true
        , selectionModel: { mode: 'single', type: 'row' }
        , filterModel: { on: true, mode: "AND", header: true, type: "local" } 
        
        , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
        ,  wrap: true, hwrap: false
    };
    obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };

    if(type==="conllu"){
        obj.colModel = [
                { title: "ID", dataType: "string", dataIndx: "0" },
                { title: "Form", dataType: "string", dataIndx: "1", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "Lemma", dataType: "string", dataIndx: "2", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "UPOS", dataType: "string", dataIndx: "3", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "XPOS", dataType: "string", dataIndx: "4", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "Feats", dataType: "string", dataIndx: "5", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "Head", dataType: "string", dataIndx: "6", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "Deprel", dataType: "string", dataIndx: "7", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "Deps", dataType: "string", dataIndx: "8", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "Misc", dataType: "string", dataIndx: "9", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "NER", dataType: "string", dataIndx: "10", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "NP", dataType: "string", dataIndx: "11", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "IATE", dataType: "string", dataIndx: "12", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
                { title: "EUROVOC", dataType: "string", dataIndx: "13", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  }
            ];
    }else if(type==="csv2"){
        obj.colModel = [
            { title: "0", dataType: "string", dataIndx: "0", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "1", dataType: "string", dataIndx: "1", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
        ];

    }else{
        obj.colModel = [
            { title: "C0", dataType: "string", dataIndx: "0", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "C1", dataType: "string", dataIndx: "1", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "C2", dataType: "string", dataIndx: "2", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  }
        ];
    }
        obj.dataModel = {
            location: "remote",
            //sorting: "local",
            //sortIndx: "name",
            //sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/csv_get&corpus={{CORPUS_NAME}}&file="+file,
            getData: function (dataJSON) {
                setAttribute("loading","style","display:none;");
                setAttribute("fileViewerCSV","style","display:block;");
                document.getElementById("corpusfilename").innerHTML="File: <b>"+file+"</b>";            
                return { data: dataJSON };
            }
        };
        
        $fileViewerCSVgrid = $("#fileViewerCSVgrid").pqGrid(obj);
}


function initGridFiles(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add TEXT', listeners: [{ click: gridAddTXT}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add CSV/TSV', listeners: [{ click: gridAddCSV}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add ZIP TEXT/StandoffMeta', listeners: [{ click: gridAddZIPTXT}], icon: 'ui-icon-plus' },
                //{ type: 'button', label: 'Edit', listeners: [{ click: gridEdit}], icon: 'ui-icon-pencil' },
                //{ type: 'button', label: 'Delete', listeners: [{ click: gridDelete}], icon: 'ui-icon-minus' }                
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Files list"
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
                if(ui.rowData.type=="csv"){
                    viewFileCSV(ui.rowData.name);
                    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
                }else{
                    viewFileText(ui.rowData.name);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file="+ui.rowData.name;
                }
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
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

          $("#popup-dialog-crud-csv").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-txt").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-ziptext").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
}

function initGridStandoff(){
        var toolbar = { items: [
                { type: 'button', label: 'Add Standoff Metadata file', listeners: [{ click: gridAddStandoff}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Standoff Metadata files list"
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
                if(ui.rowData.type=="csv"){
                    viewFileCSV("standoff/"+ui.rowData.name);
                    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&file=standoff/"+ui.rowData.name;
                }else{
                    viewFileText("standoff/"+ui.rowData.name);
                    //window.location.href="index.php?path=corpus/file_view&corpus={{CORPUS_NAME}}&file=standoff/"+ui.rowData.name;
                }
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
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
            url:"index.php?path=corpus/files_getstandoff&name={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridStandoff = $("#gridStandoff").pqGrid(obj);

          $("#popup-dialog-crud-standoff").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
}



function initGridTasks(){
        var toolbar = { items: [
                { type: 'button', label: 'ANNOTATION', listeners: [{ click: gridAddTaskBasic}], icon: 'ui-icon-plus' },
               // { type: 'button', label: 'Add CHUNKING', listeners: [{ click: gridAddTaskChunking}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'IATE/EUROVOC', listeners: [{ click: gridAddTaskIateEurovoc}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'CLEANUP', listeners: [{ click: gridAddTaskCleanup}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Export MARCELL', listeners: [{ click: gridAddTaskMarcell}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'STATISTICS', listeners: [{ click: gridAddTaskStatistics}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Create ZIP TEXT', listeners: [{ click: gridAddTaskCreateZIPTXT}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Create ZIP ANNOTATED', listeners: [{ click: gridAddTaskCreateZIPBasicTagging}], icon: 'ui-icon-plus' },
            ]
        };
        
        if(corpus_lang=="en"){
            toolbar.items[toolbar.items.length]={ type: 'button', label: 'Add CHUNKING', listeners: [{ click: gridAddTaskChunking}], icon: 'ui-icon-plus' };
        }        

        var obj = {
            width: "99%"
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

          $("#popup-dialog-crud-task-basic").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-udpipe").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-chunk").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-stat").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-createziptxt").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-createzipbasic").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-task-cleanup").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });
        
          $("#popup-dialog-crud-task-iateeurovoc").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });
        
          $("#popup-dialog-crud-task-marcell").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#gridTasks" }); },
            autoOpen: false
        });
}

function initGridBasicTagging(){
        var toolbar = { };

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Basic tagging"
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
                viewFileCSV("basictagging/"+ui.rowData.name,"conllu");
                //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&type=conllu&file=basictagging/"+ui.rowData.name;
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Type", dataType: "string", dataIndx: "type" },
            { title: "Size", dataType: "string", dataIndx: "size", sortType: sizeSort }
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
            width: "99%"
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
            width: "99%"
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

function initGridAudio(){
        var toolbar = { };

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Audio"
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
            url:"index.php?path=corpus/audio_get&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridAudio = $("#gridAudio").pqGrid(obj);
}

function showBasedOnHash(hash){
    if(hash=="standoff")showOutput(2,8);      
    else if(hash=="tasks")showOutput(3,8);      
    else if(hash=="basictagging")showOutput(4,8);      
    else if(hash=="statistics")showOutput(5,8);      
    else if(hash=="archives")showOutput(6,8);      
    else if(hash=="audio")showOutput(7,8);      
    else if(hash.startsWith("fileviewertext")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileText(file);
    }else if(hash.startsWith("fileviewercsv")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileCSV(file);
    }else if(hash.startsWith("recorder")){
        var data=hash.split(":",3);
        var from=data[1];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        recorderClick();
    }    
}

$(document).ready(function () {
    initGridFiles();
    initGridStandoff();
    initGridTasks(); 
    initGridBasicTagging(); 
    initGridStatistics(); 
    initGridArchives(); 
    initGridAudio(); 
    
    var h = window.location.hash.substr(1);
    
    showBasedOnHash(h);
});
