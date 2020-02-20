
function myescape(s){
    s=s.replace("<","&lt;");
    s=s.replace(">","&gt;");
    return s;
}


function phoneticClick2(){
    var tok=currentJSONTok;
    if(tok===false)return ;
    
    showPopupIFrame("index.php?path=ssla/synthesize&text="+tok["_wordform"]);    
}

function showPopupIFrame(url){
    var div=document.getElementById("popupIFrameDiv");
    var ifr=document.getElementById("popupIFrame");
    
    ifr.setAttribute("src",url);
    
    div.setAttribute("style","display:block; position: absolute; top:0; left:0; width:100%; height: 100%; background-color:white;");
}

function hidePopupIFrame(){
    var div=document.getElementById("popupIFrameDiv");
    div.setAttribute("style","display:none");
}

function loadData(data,func){
    loadDataComplete("index.php","POST",data,func);
}

function loadDataComplete(url,method,data,func){
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
    if(data!==undefined && data!==null){
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(data);
    }else{
        xhttp.send();
    }
}

function sendText(){

    var text=document.getElementById("text").value;
    if(text.length<1){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");
    document.getElementById("loading").setAttribute("style","display:block");

    var sysid=document.getElementById("sysid").value;

    
    loadData( 
        "path=translatews&text="+encodeURIComponent(text)+"&sysid="+sysid,
        function(response){
          var obj=JSON.parse(response) ;
        
          document.getElementById("outputTranslate").value=obj['translate'];
          document.getElementById("loading").setAttribute("style","display:none");
          document.getElementById("output").setAttribute("style","display:block; height:100%");
        }
    )
}

function translateAnother(){
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
  var text=document.getElementById('outputTranslate').value;
  post("index.php?path=teprolin/complete",{"text":text},'POST');
}

