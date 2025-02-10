// Fix dropdowns that close automatically
setInterval(function(){
    var elements=document.getElementsByTagName("select");
    for(var i=0;i<elements.length;i++)elements[i].onclick=function(e){e.stopPropagation();}
},500);

// Show SAVE button in metadata editor when text changed
setInterval(function(){
    if(getAttribute("fileViewerAudio","style").indexOf("block")>0){
        var current=document.getElementById('textFileViewerAudio').value;
        if(current!=currentFileText){
            setAttribute("fileViewerAudioSaveText","style","display:inline;");
            setAttribute("fileViewerAudioUndoText","style","display:inline;");
        }
    }else if(getAttribute("fileViewerImage","style").indexOf("block")>0){
        var current=document.getElementById('textFileViewerImage').value;
        if(current!=currentFileText){
            setAttribute("fileViewerImageSaveText","style","display:inline;");
            setAttribute("fileViewerImageUndoText","style","display:inline;");
        }
    }else if(getAttribute("fileViewerVideo","style").indexOf("block")>0){
        var current=document.getElementById('textFileViewerVideo').value;
        if(current!=currentFileText){
            setAttribute("fileViewerVideoSaveText","style","display:inline;");
            setAttribute("fileViewerVideoUndoText","style","display:inline;");
        }
    }else if(getAttribute("fileViewerMeta","style").indexOf("block")>0){
        var current=document.getElementById('textFileViewerMeta').value;
        if(current!=currentFileText){
            setAttribute("fileViewerMetaSaveText","style","display:inline;");
            setAttribute("fileViewerMetaUndoText","style","display:inline;");
        }
    }
},500);

// Refresh tasks
setInterval(function(){
    if(getAttribute("output3","style").indexOf("block")>0){
        $gridTasks.pqGrid('refreshDataAndView');
    }
},10000);
        

var viewerjsImage=false;

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
var hasAudio={{HAS_AUDIO}};
var hasImage={{HAS_IMAGE}};
var hasVideo={{HAS_VIDEO}};
var hasRights={{HAS_RIGHTS}};
var hasProperties={{HAS_PROPERTIES}};
var hasGold={{HAS_GOLD}};
var hasClassification={{HAS_CLASSIFICATION}};
var classificationProfile={{CLASSIFICATION_PROFILE}};
var last_viewed_file="{{LAST_VIEWED_FILE}}";
var last_viewed_image="{{LAST_VIEWED_IMAGE}}";
var last_viewed_audio="{{LAST_VIEWED_AUDIO}}";
var last_viewed_video="{{LAST_VIEWED_VIDEO}}";
var hasCorrected={{HAS_CORRECTED}};

var $fileViewerCSVgrid=false;
var currentFileView="";
var currentFileText="";

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

function gridAddPDF(){

            var $frm = $("form#crud-form-pdf");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-pdf").dialog({ title: "Add PDF", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-pdf").dialog("open");
} 

function gridLastFileTXT(){
    if(last_viewed_file=="")alert("There is no record of the last file you accessed!");
    else viewFileText(last_viewed_file,true);
} 
function gridLastFileImage(){
    if(last_viewed_image=="")alert("There is no record of the last file you accessed!");
    else viewFileImage(last_viewed_image,true);
} 
function gridLastFileAudio(){
    if(last_viewed_audio=="")alert("There is no record of the last file you accessed!");
    else viewFileAudio(last_viewed_audio,true);
} 
function gridLastFileVideo(){
    if(last_viewed_video=="")alert("There is no record of the last file you accessed!");
    else viewFileVideo(last_viewed_video,true);
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


function gridAddGoldStandoff(){

            var $frm = $("form#crud-form-goldstandoff");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-goldstandoff").dialog({ title: "Add Gold Standoff Metadata File", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-goldstandoff").dialog("open");
} 

function gridAddGoldAnn(){

            var $frm = $("form#crud-form-goldann");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-goldann").dialog({ title: "Add Gold Annotation File", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-goldann").dialog("open");
} 



function gridAddZIPTXT(){

            var $frm = $("form#crud-form-ziptext");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-ziptext").dialog({ title: "Add ZIP archive", buttons: {
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

function gridAddZIPAnnotated(){

            var $frm = $("form#crud-form-zipannotated");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-zipannotated").dialog({ title: "Add File ZIP with Annotated documents", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-zipannotated").dialog("open");
} 

function gridAddAnnotated(){

            var $frm = $("form#crud-form-addannotated");
            //$frm.find("input").val("");

            $("#popup-dialog-crud-addannotated").dialog({ title: "Add Annotated File", buttons: {
                Add: function () {
                    $frm.submit();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud-addannotated").dialog("open");
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

function openStatsIATE(){
    viewFileCSV("statistics/list_iate_terms.csv");
}

function openStatsIATEDF(){
    viewFileCSV("statistics/list_iate_termsdf.csv");
}

function openStatsEurovocId(){
    viewFileCSV("statistics/list_eurovoc_ids.csv");
}

function openStatsEurovocIdDF(){
    viewFileCSV("statistics/list_eurovoc_idsdf.csv");
}

function openStatsEurovocMt(){
    viewFileCSV("statistics/list_eurovoc_mt.csv");
}

function openStatsEurovocMtDF(){
    viewFileCSV("statistics/list_eurovoc_mtdf.csv");
}

function openStatsWordForm(){
    viewFileCSV("statistics/list_wordform.csv");
}

function openStatsLemma(){
    viewFileCSV("statistics/list_lemma.csv");
}

function openStatsWordFormDF(){
    viewFileCSV("statistics/list_wordformdf.csv");
}

function openStatsLetters(){
    viewFileCSV("statistics/list_letters.csv");
}

function openStatsLemmaUPOS(){
    viewFileCSV("statistics/list_lemma_upos.csv");
}

function openStatsTextData(){
    viewFileCSV("statistics/text_list.csv",1);
}

function openStatsConllupData(){
    viewFileCSV("statistics/conllup_list.csv",1);
}

function openStatsImageData(){
    viewFileCSV("statistics/image_list.csv",1);
}

function openStatsAudioData(){
    viewFileCSV("statistics/audio_list.csv",1);
}

var $gridFiles=false;
var $gridStandoff=false;
var $gridTasks=false;
var $gridBasicTagging=false;
var $gridStatistics=false;
var $gridArchives=false;
var $gridAudio=false;
var $gridImage=false;
var $gridVideo=false;
var $gridRights=false;

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
							    
									    if(data['current']>data['total'] || data['current']<0){
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
									    
									    if(data['current']>data['total'] || data['current']<0){
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

function closeFileViewerDocx(){
    setAttribute("fileViewerDocx","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}

function closeFileViewerBRAT(){
    setAttribute("fileViewerBrat","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}

function closeFileViewerMeta(){
    setAttribute("fileViewerMeta","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}


function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}
 
function saveMetadata(n,file){
    setAttribute("fileViewerMeta","style","display:none;");
    setAttribute("loading","style","display:block;");

	var form=document.getElementById("metadataEditForm"+n);
	if(form!=null){
		var arr={};
		for ( var i = 0; i < form.elements.length; i++ ) {
			var e = form.elements[i];
			arr[e.name]=e.value;
		}		
		
		content=encodeURIComponent(obj2xml(arr));
	}else{
		content=encodeURIComponent(document.getElementById("metadataEdit"+n).value);
	}

    loadData("path=corpus/file_savemetadatastandoff&corpus={{CORPUS_NAME}}&file="+file+"&meta="+metadataEdit[n].name+"&content="+content,function(data){
        setAttribute("loading","style","display:none;");
        setAttribute("fileViewerMeta","style","display:block;");
    },function(){
        alert("Error saving standoff metadata");
        setAttribute("loading","style","display:none;");
        setAttribute("fileViewerMeta","style","display:block;");
    });

}

function xml2obj(xmlString){
	var arr={};
	
	var STATE_IGNORE=0;
	var STATE_VALUE=1;
	var STATE_TAG=2;
	var STATE_ENDTAG=3;
	
	var path="";
	var value="";
	var valueValid=false;
	var tag="";
	var depth=0;
	var state=STATE_IGNORE;
	
	for(var i=0;i<xmlString.length;i++){
		var c=xmlString[i];
		switch(state){
			case STATE_IGNORE:
				if(c=='<'){tag="";state=STATE_TAG;}
				break;
			case STATE_TAG:
				if(c=='?')state=STATE_IGNORE;
				else if(c=='/')state=STATE_ENDTAG;
				else if(c=='>'){
					depth++;
					if(depth==2)path=tag;
					else if(depth>2)path+="/"+tag;
					value="";
					valueValid=true;
					state=STATE_VALUE;
				}else tag+=c;
				break;
			case STATE_ENDTAG:
				if(c=='>'){
					if(depth>1 && valueValid)arr[path]=value;
					valueValid=false;
					depth--;
					if(depth<2)path="";
					else {
						var pos=path.lastIndexOf("/");
						if(pos!=-1)path=path.substring(0,pos);
					}
					state=STATE_IGNORE;
				}else tag+=c;
				break;
			case STATE_VALUE:
				if(c=='<'){
					tag="";
					state=STATE_TAG;
				}else value+=c;
				break;
		}
	}
				
	
	return arr;
}

function obj2xml(arrIn){
	var xml='<?xml version="1.0" encoding="UTF-8"?'+">\n<Metadata>\n";
	
	var keys=Object.keys(arrIn);
	keys=keys.sort();
	
	var cpath="";
	for(var ki=0;ki<keys.length;ki++){
		var k=keys[ki];
		var v=arrIn[k];
		var pos=k.lastIndexOf("/");
		var fieldName=k;
		var arr=[];
		if(pos!=-1){
			var path=k.substring(0,pos);
			fieldName=k.substring(pos+1);
			if(path!=cpath){
				if(cpath.length>0){
					arr=cpath.split("/");
					for(var i=arr.length-1;i>=0;i--)xml+="    ".repeat($i+1)+"</"+arr[i]+">\n";
				}
				cpath=path;
				arr=cpath.split("/");
				for(var i=0;i<arr.length;i++)xml+="    ".repeat(i+1)+"<"+arr[i]+">\n";
			}
			arr=cpath.split("/");
		}else{
			if(cpath.length>0){
				arr=cpath.split("/");
				for(var i=arr.length-1;i>=0;i--)xml+="    ".repeat(i+1)+"</"+arr[i]+">\n";
			}
			cpath="";
			arr=[];
		}						
		xml+="    ".repeat(arr.length+1)+"<"+fieldName+">"+v+"</"+fieldName+">\n";						
	}
	xml+="</Metadata>\n";
	
	return xml;
}

var metadataSpec={{METADATA_SPEC}};
var metadataUploadAutocomplete_ids={{METADATA_UPLOAD_IDS}};
var metadataUploadAutocomplete_nom={{METADATA_UPLOAD_NOM}};

function getMetadataNom(field,data){
	var nom=field.nom;
	var level=0;
	if(field["level"]!==undefined)level=field.level;
	
	var parentValue="";
	if(level>0){
		parentValue=data[field["parent"]];
	}
	
	var ret={};
	for(var i=0;i<metadataSpec["nomenclature"][nom].length;i++){
		var current=metadataSpec["nomenclature"][nom][i].split("|");
		if(level==0 || current[level-1]==parentValue)
			ret[current[level]]=true;
	}
	
	return Object.keys(ret);
}

function metadataDropdownChanged(el){
	//console.log(el);
	//console.log(el.name);
	//console.log(el.value);
	//console.log(el.form);
	
	var data={};
	var htmlFields={};
	for ( var i = 0; i < el.form.elements.length; i++ ) {
		var e = el.form.elements[i];
		data[e.name]=e.value;
		htmlFields[e.name]=e;
	}		
	//console.log(data);
	
	for(var fi=0;fi<metadataSpec.fields.length;fi++){
		var field=metadataSpec.fields[fi];	
		if(field["parent"]!==undefined && field["parent"]==el.name){
			var htmlElement=htmlFields[field.field];
			var nom=getMetadataNom(field,data);
			if(field.type=="dropdown"){
				var html="";
				for(var ni=0;ni<nom.length;ni++){
					var currentN=nom[ni];
					html+='<option ';
					if(currentN==data[field.field])html+=' selected="selected"';
					html+='value="'+escapeHtml(currentN)+'">'+escapeHtml(currentN)+'</option>';
				}
				htmlElement.innerHTML=html;
			}else if(field.type=="autocomplete"){
				htmlElement.autocompleteData=nom;
			}
		}
	}
}

function fileViewerMeta_saveText(){
	setAttribute("fileViewerMeta","style","display:none;");
	setAttribute("loading","style","display:block;");

	var data = new FormData();
	data.append('path', 'corpus/file_savecontent');
	data.append('corpus','{{CORPUS_NAME}}');
	data.append('file',currentFileView);
	data.append('content', document.getElementById('textFileViewerMeta').value);				    
	loadData(data,function(d){
        currentFileText=document.getElementById('textFileViewerMeta').value;
		setAttribute("fileViewerMetaSaveText","style","display:none;");
		setAttribute("fileViewerMetaUndoText","style","display:none;");

		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerMeta","style","display:block;");
        
        $gridFiles.pqGrid('refreshDataAndView');
	},function(){
		alert("ERROR SAVING FILE");
		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerMeta","style","display:block;");
	});
						
}
 
function fileViewerMeta_revertText(){
	document.getElementById('textFileViewerMeta').value=currentFileText;
	setAttribute("fileViewerMetaSaveText","style","display:none;");
	setAttribute("fileViewerMetaUndoText","style","display:none;");
}

function editFileMetadata(file){
    currentFileView=file;
    last_viewed_file=file;

    setAttribute("output","style","display:none;");
	
    setAttribute("fileViewerText","style","display:none;");
    setAttribute("loading","style","display:block;");

    setAttribute("fileViewerMetaNext","onclick","viewNextFileMeta('"+file+"');");
    setAttribute("fileViewerMetaPrev","onclick","viewPrevFileMeta('"+file+"');");

    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewermeta:"+file+":"+previousHash;

	// First load the text
    loadData("path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file,function(data){
        document.getElementById('textFileViewerMeta').value=data;
		currentFileText=document.getElementById('textFileViewerMeta').value;
		setAttribute("fileViewerMetaSaveText","style","display:none;");
		setAttribute("fileViewerMetaUndoText","style","display:none;");

		// Now load the metadata files
		loadData("path=corpus/file_getmetadatastandoff&corpus={{CORPUS_NAME}}&file="+file,function(data){
		
			data=JSON.parse(data); // list of metadata files
			metadataEdit=data;
			var html="";
			
			// first display PDFs (should be only one)
			for(var i=0;i<data.length;i++){
				if(data[i].content===false){
					if(data[i].name.toLowerCase().endsWith("pdf")){
						html+='<object data="index.php?path=corpus/file_getdownload&view=Y&corpus={{CORPUS_NAME}}&file=standoff/'+data[i].name+'" type="application/pdf" width="100%" height="400px" style="border:1px solid black;">';
						html+='<div>No PDF viewer available</div>';
						html+='</object>';
					}
				}
			}
			document.getElementById("fileViewerTextMetadataPdfDiv").innerHTML=html;
			
			html="";
			var autocomplete_ids=[];
			var autocomplete_nom=[];
			
			// Now display the rest of the metadata files
			for(var i=0;i<data.length;i++){
				if(data[i].content!==false){
					html+='<b>'+data[i].name+"&nbsp;&nbsp;</b>";
					html+='<button type="button" class="btn cur-p btn-secondary" onclick="saveMetadata('+i+','+"'"+file+"'"+');">Save</button><br/>';
					if(data[i].name.toLowerCase().endsWith("xml") && metadataSpec.fields!==undefined){
						var xmlArr=xml2obj(data[i].content);
						html+='<form name="metadataEditForm'+i+'" id="metadataEditForm'+i+'">';
						// hidden fields
						for(var fi=0;fi<metadataSpec.fields.length;fi++){
							var field=metadataSpec.fields[fi];
							var fhidden=false;
							if(field["editHidden"]!==undefined && field["editHidden"]==true){
								html+='<input type="hidden" name="'+field.field+'" value="';
								if(xmlArr[field.field]!==undefined)html+=escapeHtml(xmlArr[field.field]);
								else html+=escapeHtml(field["default"]);
								html+='"/>';
							}							
						}

						// Visible fields
						html+='<table>';
						for(var fi=0;fi<metadataSpec.fields.length;fi++){
							var field=metadataSpec.fields[fi];
							var fhidden=false;
							if(field["editHidden"]!==undefined && field["editHidden"]==true)
								fhidden=true;
							
							var currentFieldValue=field["default"];
							if(xmlArr[field.field]!==undefined)currentFieldValue=xmlArr[field.field];
							else xmlArr[field.field]=currentFieldValue;

							var fdisabled=false;
							var htmlDisabled="";
							if(field["editDisable"]!==undefined && field["editDisable"]==true){
								fdisabled=true;
								htmlDisabled=' disabled="disabled"';
							}

							if(!fhidden){
								html+="<tr><td style=\"vertical-align:top\">"+escapeHtml(field.name)+"</td><td style=\"vertical-align:top\">";
								if(field.type=="text"){
									html+='<input type="text" name="'+field.field+'" value="'+escapeHtml(currentFieldValue)+'"'+htmlDisabled+'/></td><td>'+escapeHtml(field["description"])+"</td></tr>";
								}else if(field.type=="dropdown"){
									html+='<select name="'+field.field+'"'+htmlDisabled+' onchange="metadataDropdownChanged(this);">';
									var nom=getMetadataNom(field,xmlArr);
									for(var ni=0;ni<nom.length;ni++){
										var currentN=nom[ni];
										html+='<option ';
										if(currentN==currentFieldValue)html+=' selected="selected"';
										html+='value="'+escapeHtml(currentN)+'">'+escapeHtml(currentN)+'</option>';
									}
									html+='</select>';
									html+='</td><td>'+escapeHtml(field["description"])+"</td></tr>";
								}else if(field.type=="autocomplete"){
									var id='metadataEditForm'+i+'_'+field.field;
									autocomplete_ids[autocomplete_ids.length]=id;
									autocomplete_nom[autocomplete_nom.length]=getMetadataNom(field,xmlArr);
									html+='<div class="autocomplete-search-container">';
									html+='<input type="text" name="'+field.field+'" id="'+id+'" value="'+escapeHtml(currentFieldValue)+'"'+htmlDisabled+'/>';
									html+='<div class="autocomplete-suggestions">';
									html+='<ul></ul>';
									html+='</div>';
									html+='</div>';
									html+='</td><td>'+escapeHtml(field["description"])+"</td></tr>";
								}
							}							
						}
						html+='</table></form>';
					}else{
						html+='<textarea id="metadataEdit'+i+'" style="width:100%; font-family: Consolas,monaco,monospace; white-space: nowrap; height:200px;">'+escapeHtml(data[i].content)+'</textarea><br/>';            
					}
				}
			}
			
			document.getElementById("fileViewerTextMetadataDiv").innerHTML=html;
			
			for(var i=0;i<autocomplete_ids.length;i++){
				var el=document.getElementById(autocomplete_ids[i]);
				el.autocompleteData=autocomplete_nom[i];
				enableAutocomplete(el);
			}
		
			setAttribute("loading","style","display:none;");
			setAttribute("fileViewerMeta","style","display:block;");
			setAttribute("fileViewerTextMetadataPdfDiv","style","display:inline-block; width:50%; vertical-align:top;");
			setAttribute("fileViewerTextMetadataDiv","style","display:block; width:100%; vertical-align:top;");
			setAttribute("inputFileViewerMetaText","style","display:inline-block; width:45%") ;
		},function(){
			alert("Error loading standoff metadata");
			setAttribute("loading","style","display:none;");
			setAttribute("fileViewerMeta","style","display:block;");
		}); // end load metadata files
    },function(){
		alert("Error loading text");
		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerMeta","style","display:block;");
	});// end load text
}

function viewNextFileBrat(file){
    loadData("path=corpus/file_getnext&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileBrat(data.next);
        }else{
            alert("No additional file found! Maybe end of corpus ?");
        }
    });
}

function viewNextFile(file){
    loadData("path=corpus/file_getnext&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileText(data.next,true);
        }else{
            alert("No additional file found! Maybe end of corpus ?");
        }
    });
}

function viewNextImage(file){
    loadData("path=corpus/file_getnext&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileImage("image/"+data.next,true);
        }else{
            alert("No additional file found! Maybe end of corpus ?");
        }
    });
}
function viewNextAudio(file){
    loadData("path=corpus/file_getnext&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileAudio("image/"+data.next,true);
        }else{
            alert("No additional file found! Maybe end of corpus ?");
        }
    });
}
function viewNextVideo(file){
    loadData("path=corpus/file_getnext&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileVideo("image/"+data.next,true);
        }else{
            alert("No additional file found! Maybe end of corpus ?");
        }
    });
}


function viewNextFileMeta(file){
    loadData("path=corpus/file_getnext&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            editFileMetadata(data.next);
        }else{
            alert("No additional file found! Maybe end of corpus ?");
        }
    });
}


function viewPrevFileBrat(file){
    loadData("path=corpus/file_getprev&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileBrat(data.prev);
        }else{
            alert("No additional file found! Maybe first file ?");
        }
    });
}

function viewPrevFile(file){
    loadData("path=corpus/file_getprev&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileText(data.prev,true);
        }else{
            alert("No additional file found! Maybe first file ?");
        }
    });
}
function viewPrevImage(file){
    loadData("path=corpus/file_getprev&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileImage("image/"+data.prev,true);
        }else{
            alert("No additional file found! Maybe first file ?");
        }
    });
}
function viewPrevAudio(file){
    loadData("path=corpus/file_getprev&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileAudio("image/"+data.prev,true);
        }else{
            alert("No additional file found! Maybe first file ?");
        }
    });
}
function viewPrevVideo(file){
    loadData("path=corpus/file_getprev&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            viewFileVideo("image/"+data.prev,true);
        }else{
            alert("No additional file found! Maybe first file ?");
        }
    });
}

function viewPrevFileMeta(file){
    loadData("path=corpus/file_getprev&corpus={{CORPUS_NAME}}&current="+file,function(data){
        data=JSON.parse(data);
        if(data.status=="OK"){
            editFileMetadata(data.prev);
        }else{
            alert("No additional file found! Maybe first file ?");
        }
    });
}


function loadClassification(file){
        if(hasClassification){
        
          var frm=document.getElementById('fileViewerText_classification_form');
          if(frm!==undefined)frm.reset();
          frm=document.getElementById('fileViewerBrat_classification_form');
          if(frm!==undefined)frm.reset();
          frm=document.getElementById('fileViewerAudio_classification_form');
          if(frm!==undefined)frm.reset();
          frm=document.getElementById('fileViewerImage_classification_form');
          if(frm!==undefined)frm.reset();
          frm=document.getElementById('fileViewerVideo_classification_form');
          if(frm!==undefined)frm.reset();
        
          loadData("path=corpus/file_getclassification&corpus={{CORPUS_NAME}}&file="+file,function(data){
                data=JSON.parse(data);
                console.log(data);
                if(data.status=="OK"){
                    for(var i=0;i<classificationProfile.length;i++){
                        var key=classificationProfile[i].variable;
                        var value=false;
                        if(data['data'][key]!==undefined)value=data['data'][key];
                        if(value!==false){
                            var el=false;
                            el=document.getElementById('fileViewerText_classification_'+key);
                            if(el!==undefined)el.value=value;
                            
                            el=document.getElementById('fileViewerBrat_classification_'+key);
                            if(el!==undefined)el.value=value;

                            el=document.getElementById('fileViewerAudio_classification_'+key);
                            if(el!==undefined)el.value=value;

                            el=document.getElementById('fileViewerImage_classification_'+key);
                            if(el!==undefined)el.value=value;

                            el=document.getElementById('fileViewerVideo_classification_'+key);
                            if(el!==undefined)el.value=value;
                        }
                    }
                
                }
          });
        }
    
}    

function viewFileText(file,showBrat=false){
    currentFileView=file;
    last_viewed_file=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:block;");
    setAttribute("fileViewerTextDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");
    setAttribute("fileViewerTextBrat","onclick","closeFileViewerText();viewFileBrat('"+file+"');");
    setAttribute("fileViewerTextMetadata","onclick","closeFileViewerText();editFileMetadata('"+file+"');");
    setAttribute("fileViewerTextNext","onclick","viewNextFile('"+file+"');");
    setAttribute("fileViewerTextPrev","onclick","viewPrevFile('"+file+"');");
    
    setAttribute("fileViewerTextMetadataDiv","style","display:none");
    setAttribute("inputFileViewerText","style","display:inline-block; width:100%") ;

    if(!showBrat){
        setAttribute("fileViewerTextBrat","style","display:none;");
        setAttribute("fileViewerText_classification_div","style","display:none;");
    }else{
        setAttribute("fileViewerTextBrat","style","{{hidebratbutton}}");
        setAttribute("fileViewerText_classification_div","style","display:block; border:1px solid black; margin-top:10px; padding:5px");
    } 
		
    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewertext:"+file+":"+previousHash;

    loadData("path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file,function(data){
        document.getElementById('textFileViewerText').value=data;
        setAttribute("loading","style","display:none;");
        setAttribute("fileViewerText","style","display:block;");
        document.getElementById("corpusfilename").innerHTML="File: <b>"+file+"</b>";

        loadClassification(file);
        
        console.log("hasCorrected=");console.log(hasCorrected);
        
        if(hasCorrected && showBrat){
            document.getElementById('fileViewerBrat_corrected_div').innerText="";
          loadData("path=corpus/file_getcorrected&corpus={{CORPUS_NAME}}&file="+file,function(data){
                data=JSON.parse(data);
                console.log(data);
                if(data.status=="OK"){
                    document.getElementById('fileViewerBrat_corrected_div').innerText=data.data;
                }
          });
        
        }
    },function(){
        alert("Error loading text");
        setAttribute("loading","style","display:none;");
        setAttribute("output","style","display:block;");
    });
    
}

function viewFileDocx(file,showBrat=false){
    currentFileView=file;
    last_viewed_file=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:block;");
    setAttribute("fileViewerDocxDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");

    setAttribute("inputFileViewerText","style","display:inline-block; width:100%") ;
    
		
    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewerdocx:"+file+":"+previousHash;

    JSZipUtils.getBinaryContent("index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file, function(err, data) {
        if(err) {
            alert("Error loading text");
            setAttribute("loading","style","display:none;");
            setAttribute("output","style","display:block;");
            return ;
            throw err; // or handle err
        }

         docx.renderAsync(data, document.getElementById("inputFileViewerDocx"));

        setAttribute("loading","style","display:none;");
        setAttribute("fileViewerDocx","style","display:block;");
        document.getElementById("corpusfilename").innerHTML="File: <b>"+file+"</b>";
        
    });
    
}

function fileViewerText_saveFileClassification(){
    var data={};
    for(var i=0;i<classificationProfile.length;i++){
        var key=classificationProfile[i].variable;
        var value=document.getElementById('fileViewerText_classification_'+key).value;
        data[key]=value;
    }


    loadData("path=corpus/file_saveclassification&corpus={{CORPUS_NAME}}&file="+currentFileView+"&data="+encodeURIComponent(JSON.stringify(data)),function(d){
    });    
    
}

function fileViewerBrat_saveFileClassification(){
    var data={};
    for(var i=0;i<classificationProfile.length;i++){
        var key=classificationProfile[i].variable;
        var value=document.getElementById('fileViewerBrat_classification_'+key).value;
        data[key]=value;
    }


    loadData("path=corpus/file_saveclassification&corpus={{CORPUS_NAME}}&file="+currentFileView+"&data="+encodeURIComponent(JSON.stringify(data)),function(d){
    });    
    
}


function fileViewerAudio_saveFileClassification(){
    var data={};
    for(var i=0;i<classificationProfile.length;i++){
        var key=classificationProfile[i].variable;
        var value=document.getElementById('fileViewerAudio_classification_'+key).value;
        data[key]=value;
    }


    var cf=currentFileView;
    if(cf.startsWith("audio/"))cf=cf.substring(6);

    loadData("path=corpus/file_saveclassification&corpus={{CORPUS_NAME}}&file="+cf+"&data="+encodeURIComponent(JSON.stringify(data)),function(d){
    });    
    
}

function fileViewerImage_saveFileClassification(){
    var data={};
    for(var i=0;i<classificationProfile.length;i++){
        var key=classificationProfile[i].variable;
        var value=document.getElementById('fileViewerImage_classification_'+key).value;
        data[key]=value;
    }


    var cf=currentFileView;
    if(cf.startsWith("image/"))cf=cf.substring(6);

    loadData("path=corpus/file_saveclassification&corpus={{CORPUS_NAME}}&file="+cf+"&data="+encodeURIComponent(JSON.stringify(data)),function(d){
    });    
    
}

function fileViewerVideo_saveFileClassification(){
    var data={};
    for(var i=0;i<classificationProfile.length;i++){
        var key=classificationProfile[i].variable;
        var value=document.getElementById('fileViewerVideo_classification_'+key).value;
        data[key]=value;
    }

    var cf=currentFileView;
    if(cf.startsWith("video/"))cf=cf.substring(6);

    loadData("path=corpus/file_saveclassification&corpus={{CORPUS_NAME}}&file="+cf+"&data="+encodeURIComponent(JSON.stringify(data)),function(d){
    });    
    
}

function fileViewerAudio_saveText(){
	setAttribute("fileViewerAudio","style","display:none;");
	setAttribute("loading","style","display:block;");

	var data = new FormData();
	data.append('path', 'corpus/file_savecontent');
	data.append('corpus','{{CORPUS_NAME}}');
	data.append('file',changeFileExtension(currentFileView.substring(6),"txt"));
	data.append('content', document.getElementById('textFileViewerAudio').value);				    
	loadData(data,function(d){
        currentFileText=document.getElementById('textFileViewerAudio').value;
		setAttribute("fileViewerAudioSaveText","style","display:none;");
		setAttribute("fileViewerAudioUndoText","style","display:none;");

		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerAudio","style","display:block;");
        
        $gridFiles.pqGrid('refreshDataAndView');
	},function(){
		alert("ERROR SAVING FILE");
		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerAudio","style","display:block;");
	});
						
}
 
function fileViewerAudio_revertText(){
	document.getElementById('textFileViewerAudio').value=currentFileText;
	setAttribute("fileViewerAudioSaveText","style","display:none;");
	setAttribute("fileViewerAudioUndoText","style","display:none;");
}

function fileViewerImage_saveText(){
	setAttribute("fileViewerImage","style","display:none;");
	setAttribute("loading","style","display:block;");

	var data = new FormData();
	data.append('path', 'corpus/file_savecontent');
	data.append('corpus','{{CORPUS_NAME}}');
	data.append('file',changeFileExtension(currentFileView.substring(6),"txt"));
	data.append('content', document.getElementById('textFileViewerImage').value);				    
	loadData(data,function(d){
        currentFileText=document.getElementById('textFileViewerImage').value;
		setAttribute("fileViewerImageSaveText","style","display:none;");
		setAttribute("fileViewerImageUndoText","style","display:none;");

		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerImage","style","display:block;");
        
        $gridFiles.pqGrid('refreshDataAndView');
	},function(){
		alert("ERROR SAVING FILE");
		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerImage","style","display:block;");
	});
						
}
 
function fileViewerImage_revertText(){
	document.getElementById('textFileViewerImage').value=currentFileText;
	setAttribute("fileViewerImageSaveText","style","display:none;");
	setAttribute("fileViewerImageUndoText","style","display:none;");
}

function fileViewerVideo_saveText(){
	setAttribute("fileViewerVideo","style","display:none;");
	setAttribute("loading","style","display:block;");

	var data = new FormData();
	data.append('path', 'corpus/file_savecontent');
	data.append('corpus','{{CORPUS_NAME}}');
	data.append('file',changeFileExtension(currentFileView.substring(6),"txt"));
	data.append('content', document.getElementById('textFileViewerVideo').value);				    
	loadData(data,function(d){
        currentFileText=document.getElementById('textFileViewerVideo').value;
		setAttribute("fileViewerVideoSaveText","style","display:none;");
		setAttribute("fileViewerVideoUndoText","style","display:none;");

		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerVideo","style","display:block;");
	        
        $gridFiles.pqGrid('refreshDataAndView');
    },function(){
		alert("ERROR SAVING FILE");
		setAttribute("loading","style","display:none;");
		setAttribute("fileViewerVideo","style","display:block;");
	});
						
}
 
function fileViewerVideo_revertText(){
	document.getElementById('textFileViewerVideo').value=currentFileText;
	setAttribute("fileViewerVideoSaveText","style","display:none;");
	setAttribute("fileViewerVideoUndoText","style","display:none;");
}

function closeFileViewerAudio(){
    setAttribute("fileViewerAudio","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}

function viewFileAudio(file){
    currentFileView=file;
    last_viewed_audio=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:none;");
    setAttribute("fileViewerAudio","style","display:block;");
    setAttribute("fileViewerAudioDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");
    setAttribute("inputFileViewerAudioSource","src","index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file);
    setAttribute("fileViewerAudioNext","onclick","viewNextAudio('"+file+"');");
    setAttribute("fileViewerAudioPrev","onclick","viewPrevAudio('"+file+"');");

    document.getElementById("corpusfilename").innerHTML="File: <b>"+file.substring(6)+"</b>";            

	var audio=document.getElementById("inputFileViewerAudio");
	audio.load();

    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#filevieweraudio:"+file+":"+previousHash;

    loadClassification(changeFileExtension(file.substring(6),"txt"));

    loadData("path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+changeFileExtension(file.substring(6),"txt"),function(data){
        if(data=="Invalid file")data="";
        document.getElementById('textFileViewerAudio').value=data;
		currentFileText=document.getElementById('textFileViewerAudio').value;
    },function(){
        alert("Error loading text file");
    });

}

function fileViewerAudioDelete(){
    if(!confirm("Delete file ["+currentFileView+"] ?"))return ;
		
    loadData("path=corpus/file_delete&corpus={{CORPUS_NAME}}&file="+currentFileView,function(data){
    		$gridAudio.pqGrid('refreshDataAndView');
				closeFileViewerAudio();
    },function(){
        alert("Error deleting file");
    });
		
		
}



function closeFileViewerImage(){
    setAttribute("fileViewerImage","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}

function viewFileImage(file){
    currentFileView=file;
    last_viewed_image=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:none;");
    setAttribute("fileViewerImage","style","display:block;");
    setAttribute("fileViewerImageDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");
    setAttribute("inputFileViewerImage","src","index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file);
    setAttribute("fileViewerImageNext","onclick","viewNextImage('"+file+"');");
    setAttribute("fileViewerImagePrev","onclick","viewPrevImage('"+file+"');");

    document.getElementById("corpusfilename").innerHTML="File: <b>"+file.substring(6)+"</b>";            
    
    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewerimage:"+file+":"+previousHash;

    if(viewerjsImage===false){
        viewerjsImage=new Viewer(document.getElementById('inputFileViewerImage'), {
          inline: false,
          toolbar: {
            zoomIn: 4,
            zoomOut: 4,
            oneToOne: 4,
            reset: 4,
          },  
          viewed() {
            viewer.zoomTo(1);
          },
        });
    }        

    loadClassification(changeFileExtension(file.substring(6),"txt"));

    loadData("path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+changeFileExtension(file.substring(6),"txt"),function(data){
        if(data=="Invalid file")data="";
        document.getElementById('textFileViewerImage').value=data;
		currentFileText=document.getElementById('textFileViewerImage').value;
    },function(){
        alert("Error loading text file");
    });
}

function fileViewerImageDelete(){
		if(!confirm("Delete file ["+currentFileView+"] ?"))return ;
		
    loadData("path=corpus/file_delete&corpus={{CORPUS_NAME}}&file="+currentFileView,function(data){
    		$gridImage.pqGrid('refreshDataAndView');
				closeFileViewerImage();
    },function(){
        alert("Error deleting file");
    });
}


function closeFileViewerVideo(){
    setAttribute("fileViewerVideo","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
}

function viewFileVideo(file){
    currentFileView=file;
    last_file_video=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:none;");
    setAttribute("fileViewerVideo","style","display:block;");
    setAttribute("fileViewerVideoDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");
    setAttribute("inputFileViewerVideoSource","src","index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file);
    setAttribute("fileViewerVideoNext","onclick","viewNextVideo('"+file+"');");
    setAttribute("fileViewerVideoPrev","onclick","viewPrevVideo('"+file+"');");

    document.getElementById("corpusfilename").innerHTML="File: <b>"+file.substring(6)+"</b>";            

    var video=document.getElementById("inputFileViewerVideo");
    video.load();

    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewervideo:"+file+":"+previousHash;

    loadClassification(changeFileExtension(file.substring(6),"txt"));

    loadData("path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+changeFileExtension(file.substring(6),"txt"),function(data){
        if(data=="Invalid file")data="";
        document.getElementById('textFileViewerVideo').value=data;
		currentFileText=document.getElementById('textFileViewerVideo').value;
    },function(){
        alert("Error loading text file");
    });

}

function fileViewerVideoDelete(){
    if(!confirm("Delete file ["+currentFileView+"] ?"))return ;
		
    loadData("path=corpus/file_delete&corpus={{CORPUS_NAME}}&file="+currentFileView,function(data){
    		$gridVideo.pqGrid('refreshDataAndView');
				closeFileViewerVideo();
    },function(){
        alert("Error deleting file");
    });
		
		
}


function changeFileExtension(file,ext){
		var p=file.lastIndexOf(".");
		if(p==-1)return file+"."+ext;
		return file.substring(0,p+1)+ext;
}

function saveToGold(corpus,file){
    setAttribute("fileViewerBrat","style","display:none; ");
    setAttribute("loading","style","display:block;");
    
    loadData("path=corpus/savegold&corpus={{CORPUS_NAME}}&file="+file,function(data){
    		alert(data);
        setAttribute("loading","style","display:none;");
    		setAttribute("fileViewerBrat","style","display:block; height:100%;");
    },function(){
        alert("Error saving to GOLD");
        setAttribute("loading","style","display:none;");
    		setAttribute("fileViewerBrat","style","display:block; height:100%;");
    });
    
}

function viewFileBrat(file){
    currentFileView=file;
    last_viewed_file=file;
    setAttribute("output","style","display:none;");
    setAttribute("loading","style","display:block;");
    setAttribute("fileViewerBratDownload","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+file+"';");
    setAttribute("fileViewerBratDownloadAnn","onclick","window.location='index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file=standoff/"+changeFileExtension(file,"ann")+"';");
    setAttribute("fileViewerBratSaveGold","onclick","saveToGold('{{CORPUS_NAME}}','"+file+"');");
    setAttribute("fileViewerBratNext","onclick","viewNextFileBrat('"+file+"');");
    setAttribute("fileViewerBratPrev","onclick","viewPrevFileBrat('"+file+"');");

    var h=window.location.hash;
    if(h!==undefined && h!=false && h.length>1)previousHash=h.substring(1);    
    window.location.hash="#fileviewerbrat:"+file+":"+previousHash;

    document.getElementById('bratForm').action="brat/index.xhtml#/{{CORPUS_NAME}}/"+file;  
    document.getElementById('bratForm').submit(); 
    
    setAttribute("loading","style","display:none;");
    setAttribute("fileViewerBrat","style","display:block; height:100%;");

    loadClassification(file);

    console.log("hasCorrected=");console.log(hasCorrected);
    
    if(hasCorrected){
        document.getElementById('fileViewerBrat_corrected_div').innerText="";
      loadData("path=corpus/file_getcorrected&corpus={{CORPUS_NAME}}&file="+file,function(data){
            data=JSON.parse(data);
            console.log(data);
            if(data.status=="OK"){
                document.getElementById('fileViewerBrat_corrected_div').innerText=data.data;
            }
      });
    
    }
    
    
}

function closeFileViewerCSV(){
    setAttribute("fileViewerCSV","style","display:none;");
    setAttribute("output","style","display:block;");
    document.getElementById("corpusfilename").innerHTML="";
    window.location.hash=previousHash;
    $fileViewerCSVgrid.pqGrid('destroy');
    $fileViewerCSVgrid=false;
    setAttribute("fileViewerCSVgrid","class","");
}

function viewFileCSV(file,useHeader){
    if(arguments.length==1)useHeader=0;
    
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
        
    loadData("path=corpus/file_getcolumns&corpus={{CORPUS_NAME}}&file="+file+"&useHeader="+useHeader,function(dataColumns){
        try{
            dataColumns=JSON.parse(dataColumns);
        }catch(ex){
            console.log(dataColumns);
            alert("Error getting column names");
            setAttribute("loading","style","display:none;");
            setAttribute("output","style","display:block;");
            return;
        }
        obj.colModel=[];
        for(var i=0;i<dataColumns['columns'].length;i++){
            var col=dataColumns['columns'][i];
            obj.colModel[obj.colModel.length]={
                title:col.name,
                dataType:col.type,
                dataIndx:i,
                filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }
            };
        }
        $fileViewerCSVgrid = $("#fileViewerCSVgrid").pqGrid(obj);
    },function(){
        alert("Error getting column names");
        setAttribute("loading","style","display:none;");
        setAttribute("output","style","display:block;");
	});
}


function initGridFiles(){
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add TEXT', listeners: [{ click: gridAddTXT}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add CSV/TSV', listeners: [{ click: gridAddCSV}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add PDF', listeners: [{ click: gridAddPDF}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add ZIP', listeners: [{ click: gridAddZIPTXT}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Access Last File', listeners: [{ click: gridLastFileTXT}], icon: 'ui-icon-plus' },
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
                    last_viewed_file=ui.rowData.name;
                    viewFileText(ui.rowData.name,true);
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
            { title: "Type", dataType: "string", dataIndx: "type", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Description", dataType: "string", dataIndx: "desc", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "User", dataType: "string", dataIndx: "created_by", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Creation Date", dataType: "string", dataIndx: "created_date", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "name",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/files_get&name={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridFiles = $("#grid").pqGrid(obj);

          $("#popup-dialog-crud-csv").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-txt").dialog({ width: 1000, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-ziptext").dialog({ width: 1000, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

          $("#popup-dialog-crud-pdf").dialog({ width: 1000, modal: true,
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
                }else if(ui.rowData.name.toLowerCase().endsWith(".docx")){
                    viewFileDocx("standoff/"+ui.rowData.name);
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
            sortDir: "up",
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

function initGridGoldAnn(){
    if(!hasGold)return ;
        var toolbar = { items: [
                { type: 'button', label: 'Add Gold Annotation file', listeners: [{ click: gridAddGoldAnn}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Gold Annotation files list"
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
                    viewFileCSV("goldann/"+ui.rowData.name);
                    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&file=standoff/"+ui.rowData.name;
                }else{
                    viewFileText("goldann/"+ui.rowData.name);
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
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/files_getgoldann&name={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridGoldAnn = $("#gridGoldAnn").pqGrid(obj);

          $("#popup-dialog-crud-goldann").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
}

function initGridGoldStandoff(){
    if(!hasGold)return ;
        var toolbar = { items: [
                { type: 'button', label: 'Add Gold Standoff Metadata file', listeners: [{ click: gridAddGoldStandoff}], icon: 'ui-icon-plus' },
            ]
        };        

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Gold Standoff Metadata files list"
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
                    viewFileCSV("goldstandoff/"+ui.rowData.name);
                    //window.location.href="index.php?path=corpus/csv_view&corpus={{CORPUS_NAME}}&file=standoff/"+ui.rowData.name;
                }else{
                    viewFileText("goldstandoff/"+ui.rowData.name);
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
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/files_getgoldstandoff&name={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridGoldStandoff = $("#gridGoldStandoff").pqGrid(obj);

          $("#popup-dialog-crud-goldstandoff").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
}


function initGridTasks(){
        var toolbar = { items: [
                {{TASKS-BUTTONS}}
            ]
        };
        
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

        {{TASKS-INIT}}

}

function initGridBasicTagging(){
        var toolbar = { items:[
                { type: 'button', label: 'Add ZIP Annotated', listeners: [{ click: gridAddZIPAnnotated}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Add Annotated File', listeners: [{ click: gridAddAnnotated}], icon: 'ui-icon-plus' },
         ]};

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
                viewFileCSV("basictagging/"+ui.rowData.name);
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
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/files_getbasictagging&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridBasicTagging = $("#gridBasicTagging").pqGrid(obj);
        
          $("#popup-dialog-crud-zipannotated").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
          $("#popup-dialog-crud-addannotated").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
        
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
                { type: 'button', label: 'WordForm Stats', listeners: [{ click: openStatsWordForm}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Lemma Stats', listeners: [{ click: openStatsLemma}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'WordForm Doc Freq', listeners: [{ click: openStatsWordFormDF}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Letters Stats', listeners: [{ click: openStatsLetters}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Lemma UPOS Stats', listeners: [{ click: openStatsLemmaUPOS}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'IATE Stats', listeners: [{ click: openStatsIATE}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'IATE Doc Freq', listeners: [{ click: openStatsIATEDF}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'EUROVOC ID Stats', listeners: [{ click: openStatsEurovocId}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'EUROVOC ID Doc Freq', listeners: [{ click: openStatsEurovocIdDF}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'EUROVOC MT Stats', listeners: [{ click: openStatsEurovocMt}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'EUROVOC MT Doc Freq', listeners: [{ click: openStatsEurovocMtDF}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Text Data', listeners: [{ click: openStatsTextData}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Annotated Data', listeners: [{ click: openStatsConllupData}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Image Data', listeners: [{ click: openStatsImageData}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Audio Data', listeners: [{ click: openStatsAudioData}], icon: 'ui-icon-plus' },
                
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
			if(!hasAudio)return ;

        var toolbar = { items:[
            { type: 'button', label: 'Access Last File', listeners: [{ click: gridLastFileAudio}], icon: 'ui-icon-plus' },
        ]};

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
                //window.location.href="index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+ui.rowData.fname;
                viewFileAudio(ui.rowData.fname);
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


function initGridImage(){
        if(!hasImage)return ;

        var toolbar = { items:[
            { type: 'button', label: 'Access Last File', listeners: [{ click: gridLastFileImage}], icon: 'ui-icon-plus' },
        ]};

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Images"
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
                //window.location.href="index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+ui.rowData.fname;
                viewFileImage(ui.rowData.fname);
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
            url:"index.php?path=corpus/image_get&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridImage = $("#gridImage").pqGrid(obj);
}


function initGridVideo(){
        if(!hasVideo)return ;

        var toolbar = { items:[
            { type: 'button', label: 'Access Last File', listeners: [{ click: gridLastFileVideo}], icon: 'ui-icon-plus' },
        ]};

        var obj = {
            width: "99%"
            , height: 400
            , resizable: true
            , title: "Video"
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
                //window.location.href="index.php?path=corpus/file_getdownload&corpus={{CORPUS_NAME}}&file="+ui.rowData.fname;
                viewFileVideo(ui.rowData.fname);
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
            url:"index.php?path=corpus/video_get&corpus={{CORPUS_NAME}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $gridVideo = $("#gridVideo").pqGrid(obj);
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
            data.append('path', 'corpus/rights_add');
            data.append('corpus','{{CORPUS_NAME}}');
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
            data.append('path', 'corpus/rights_edit');
            data.append('corpus','{{CORPUS_NAME}}');
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
    data.append('path', 'corpus/rights_delete');
    data.append('corpus','{{CORPUS_NAME}}');
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
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
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
            url:"index.php?path=corpus/rights_get&corpus={{CORPUS_NAME}}",
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
    if(hash=="standoff")showOutput(2,13);
    else if(hash=="tasks")showOutput(3,13);
    else if(hash=="basictagging")showOutput(4,13);
    else if(hash=="statistics")showOutput(5,13);
    else if(hash=="archives")showOutput(6,13);
    else if(hash=="audio" && hasAudio)showOutput(7,13);
    else if(hash=="image" && hasImage)showOutput(8,13);
    else if(hash=="video" && hasVideo)showOutput(9,13);
    else if(hash=="goldstandoff" && hasGold)showOutput(10,13);
    else if(hash=="goldann" && hasGold)showOutput(11,13);
    else if(hash=="properties" && hasProperties)showOutput(12,13);
    else if(hash=="rights" && hasRights)showOutput(13,13);
    else if(hash.startsWith("fileviewertext")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        var vbrat=false; if(from=="files")vbrat=true;
        viewFileText(decodeURI(file),vbrat);
    }else if(hash.startsWith("fileviewerdocx")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        var vbrat=false; if(from=="files")vbrat=true;
        viewFileDocx(decodeURI(file),vbrat);
    }else if(hash.startsWith("filevieweraudio")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileAudio(decodeURI(file));
    }else if(hash.startsWith("fileviewerimage")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileImage(decodeURI(file));
    }else if(hash.startsWith("fileviewervideo")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileVideo(decodeURI(file));
    }else if(hash.startsWith("fileviewercsv")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileCSV(decodeURI(file));
    }else if(hash.startsWith("fileviewerbrat")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        viewFileBrat(decodeURI(file));
    }else if(hash.startsWith("fileviewermeta")){
        var data=hash.split(":",3);
        var file=data[1];
        var from=data[2];
        window.location.hash="#"+from;
        showBasedOnHash(from);
        editFileMetadata(decodeURI(file));
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
    initGridImage(); 
    initGridVideo(); 
    initGridGoldAnn(); 
    initGridGoldStandoff(); 
    initGridRights(); 
	
	for(var i=0;i<metadataUploadAutocomplete_ids.length;i++){
		var el=document.getElementById(metadataUploadAutocomplete_ids[i]);
		el.autocompleteData=metadataUploadAutocomplete_nom[i];
		enableAutocomplete(el);
	}
		
    
    var h = window.location.hash.substr(1);
    
    showBasedOnHash(h);
        
});
