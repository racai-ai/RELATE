
function again() {
    document.getElementById('text').value="";
    document.getElementById("output").setAttribute("style","display:none");
    document.getElementById("input").setAttribute("style","display:block");

}

function demo(){
document.getElementById('text').value=
"Va mai urma un val nou de Covid-19?";
}


function run_qa(){
    var text=document.getElementById('text').value;
    
    if(text.length<10){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");
    document.getElementById("loading").setAttribute("style","display:block");

    loadDataForm("inputForm",function(data){
    
        data=JSON.parse(data);
        
        console.log(data);
        
        /*if(data.status!="OK")
            document.getElementById("outputText").innerText="ERROR";
        else
            document.getElementById("outputText").innerText=data.new_text;*/
        var html="<table>";
        for(var i=0;i<data.response.length;i++){
            var ob=data.response[i];
            html+="<tr><td>"+ob.snippet.substring(0,ob.start_offset)+"<span style=\"background-color:yellow\">"+
                ob.snippet.substring(ob.start_offset,ob.end_offset)+"</span>"+
                ob.snippet.substring(ob.end_offset)+
                " <a href=\""+ob.url+"\" target=\"blank\">SURSA</a>"+
                "</td></tr>";
        }
        html+="</table>";
        document.getElementById("outputText").innerHTML=html;
        
        document.getElementById("output").setAttribute("style","display:block");
        document.getElementById("loading").setAttribute("style","display:none");
    
    });
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
