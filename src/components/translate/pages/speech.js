document.getElementById("bStopRecorder").disabled = true;

function myescape(s){
    s=s.replace("<","&lt;");
    s=s.replace(">","&gt;");
    return s;
}

function loadData(data,func,error){
    loadDataComplete("index.php","POST",data,null,func,error);
}

function loadDataForm(formId,func){
    loadDataComplete("index.php","POST",null,formId,func);
}

function loadDataComplete(url,method,data,formId,func,error){
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
    xhttp.open(method, url, true);
    if(formId!==undefined && formId!==null){
        var form=document.getElementById(formId);
        var fd=new FormData(form);
        xhttp.send(fd);
    }else if(data!==undefined && data!==null){
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
    
}

function ASRResultCallback(response){
          var obj=JSON.parse(response) ;
        
          if(obj.error){
              alert("The file cannot be processed");
              document.getElementById("loading").setAttribute("style","display:none");
              document.getElementById("output").setAttribute("style","display:block; height:100%");
              return ;
          }
          
          var input="{{input}}";
        
          document.getElementById("outputASR").value=obj['asr'];
          document.getElementById("outputTranslated").value=obj['translated'];
          var pathtts="";
          if(input=="ro")pathtts="index.php?path=sttsws&lang=en&text="+encodeURIComponent(obj['translated']);
          else pathtts="index.php?path=sttsws&lang=ro&text="+encodeURIComponent(obj['translated']);
          document.getElementById("ttssrcid").src=pathtts;
          document.getElementById("ttssrcid").play();
          document.getElementById("loading").setAttribute("style","display:none");
          document.getElementById("output").setAttribute("style","display:block");
          showOutput(3,3,'results');
}

function runASR(){

    var text=document.getElementById("asrfile").value;
    if(text.length<1){alert("Please select a WAV file"); return ;}

    document.getElementById("output").setAttribute("style","display:none");
    document.getElementById("loading").setAttribute("style","display:block");

    loadDataForm( 
        "asr-form",
        ASRResultCallback
    )
}

function runAnother(){
    document.getElementById("asrfile").value="";
    showOutput(1,3,'file');
    //document.getElementById("input").setAttribute("style","display:block");
    //document.getElementById("output").setAttribute("style","display:none");
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
        if(content!=null)downloadLink.href =content;
        else downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
    }
    else
    {
        // Firefox requires the link to be added to the DOM
        // before it can be clicked.
        if(content!=null)downloadLink.href =content;
        else downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
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


/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the paramiters to add to the url
 * @param {string} [method=post] the method to use on the form
 */

function post(path, params, method='post') {

  // The rest of this code assumes you are not using a library.
  // It can be made less wordy if you use one.
  const form = document.createElement('form');
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

function runAnalysis(){
  var text=document.getElementById('outputASR').value;
  text=text.toLowerCase();
  text = text.charAt(0).toUpperCase() + text.slice(1).trim() + ".";
  post("index.php?path=teprolin/complete",{"text":text},'POST');
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
			 var audioContext = new AudioContext({sampleRate:16000});
			 /* use the stream */
			 var input = audioContext.createMediaStreamSource(stream);
			 //stop the input from playing back through the speakers 
			 //input.connect(audioContext.destination) //get the encoding
			  
			 var recorder = new WebAudioRecorder(input, {
			     workerDir: "extern/web_audio_recorder/",
			     encoding: "wav",
			     numChannels:1,
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
				    
				    document.getElementById("output").setAttribute("style","display:none");
				    document.getElementById("loading").setAttribute("style","display:block");

						document.getElementById("bStartRecorder").disabled = false;
				    setAttribute("bStartRecorder","style","display:inline; width:100px; text-align:center");
						document.getElementById("bStopRecorder").disabled = true;
				    
						var data = new FormData();
						data.append('path', 'stranslatews');
						data.append('input','{{input}}');
						data.append('sysid','{{sysid}}');
						data.append('asrfile', blob);				    
				    loadData(data,
							ASRResultCallback
							,function(){
								console.log("Error uploading");


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
				document.getElementById("bStopRecorder").disabled = false;
					 

		}).catch(function(err) {
		
				console.log(err); 
				alert("Can not capture audio!");

				document.getElementById("bStartRecorder").disabled = false;
		    setAttribute("bStartRecorder","style","display:inline; width:100px; text-align:center");
				document.getElementById("bStopRecorder").disabled = true;

		});

}

function stopRecorder(){
		document.getElementById("bStopRecorder").disabled = true;
		console.log("Stopping recorder ...");
		recorderStream.getAudioTracks()[0].stop();
		recorderWebAudioRecorder.finishRecording();
}
