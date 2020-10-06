function play(){
    var text=document.getElementById('text').value;
    
    if(text.length<2){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");
    document.getElementById("loading").setAttribute("style","display:block");

    
    
    var speaker=document.getElementById('speaker').value;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.overrideMimeType('text/plain; charset=x-user-defined');  
    xmlhttp.onreadystatechange = function() {
	    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
        if(xmlhttp.status==200){
            var data = xmlhttp.response;
    		    var arrayBuffer = new ArrayBuffer(data.length);
    		    var bufferView = new Uint8Array(arrayBuffer);
    		    for (i = 0; i < data.length; i++) {
    		    	bufferView[i] = data[i].charCodeAt(0);
    		    }
    		    var blob = new Blob([bufferView], { type: 'audio/wav' });
    		    var url = window.URL.createObjectURL(blob)
    		    $("#audioplayer").attr("src", url);
    		    $("#audioplayer").trigger("load");
            
              document.getElementById("outputText").innerText=text;
              document.getElementById("loading").setAttribute("style","display:none");
              document.getElementById("output").setAttribute("style","display:block; height:100%");
          }else xmlhttp.error();
      }
    };
    xmlhttp.error = function() {
          alert("Error processing text");
          document.getElementById("loading").setAttribute("style","display:none");
          document.getElementById("input").setAttribute("style","display:block; height:100%");
    };
         
    var theUrl = "index.php?path=robinttsws";
    xmlhttp.open("POST", theUrl, true);
    xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xmlhttp.send(JSON.stringify({'language': 'ro', 'speaker': speaker, 'text': text}));
}

function myescape(s){
    s=s.replace("<","&lt;");
    s=s.replace(">","&gt;");
    return s;
}


function loadDataForm(formId,func){
    loadDataComplete("index.php","POST",null,formId,func);
}

function loadDataComplete(url,method,data,formId,func){
    var xhttp = false;
    
    if (window.XMLHttpRequest) {
        // code for modern browsers
        xhttp = new XMLHttpRequest();
    } else {
        // code for old IE browsers
        xhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response=this.responseText;
        func(response);
      }
    };    
    xhttp.open(method, url, true);
    if(formId!==undefined && formId!==null){
        var form=document.getElementById(formId);
        var fd=new FormData(form);
        xhttp.send(fd);
    }else if(data!==undefined && data!==null){
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(data);
    }else{
        xhttp.send();
    }
}

function runAnother(){
    document.getElementById("text").value="";
    document.getElementById("input").setAttribute("style","display:block");
    document.getElementById("output").setAttribute("style","display:none");
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
  var text=document.getElementById('text').value;
  //text=text.toLowerCase();
  //text = text.charAt(0).toUpperCase() + text.slice(1).trim() + ".";
  post("index.php?path=teprolin/complete",{"text":text},'POST');
}
