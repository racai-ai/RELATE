
function again() {
    document.getElementById('text').value="";
    document.getElementById("output").setAttribute("style","display:none");
    document.getElementById("input").setAttribute("style","display:block");

}

function demo(){
document.getElementById('text').value=
"Se promulgă Legea privind respingerea Ordonanței de urgență a Guvernului nr. 75/2018 pentru modificarea și completarea unor acte normative în domeniul protecției mediului și al regimului străinilor și se dispune publicarea acestei legi în Monitorul Oficial al României , Partea I.\n"+
"ORDIN nr. 823 din 27 mai 2021 privind numirea reprezentanților părții române în cadrul organismelor create în temeiul tratatelor bilaterale privind gospodărirea apelor de frontieră\n"+
"EMITENT\n"+
"MINISTERUL MEDIULUI , APELOR ȘI PĂDURILOR\n"+
"Publicat în  MONITORUL OFICIAL nr. 573 din 7 iunie 2021\n"+
"\n"+
"Având în vedere Referatul de aprobare nr. DMRA/192.653/5.04.2021 al Direcției managementul resurselor de apă ,\n"+
"luând în considerare prevederile Acordului dintre Guvernul României și Guvernul Republicii Serbia privind cooperarea în domeniul gospodăririi durabile a apelor transfrontaliere , semnat la București la 5 iunie 2019, aprobat prin Hotărârea Guvernului nr. 725/2020, prevederile Acordului dintre Guvernul României și Guvernul Ucrainei privind cooperarea în domeniul gospodăririi apelor de frontieră, semnat la Galați la 30 septembrie 1997, ratificat prin Legea nr. 16/1999 , prevederile Acordului dintre Guvernul României și Guvernul Republicii Ungare privind colaborarea pentru protecția și utilizarea durabilă a apelor de frontieră, semnat la Budapesta la 15 septembrie 2003 , aprobat prin Hotărârea Guvernului nr. 577/2004 , prevederile Acordului dintre Guvernul României și Guvernul Republicii Moldova privind cooperarea pentru protecția și utilizarea durabilă a apelor Prutului și Dunării, semnat la Chișinău la 28 iunie 2010 , aprobat prin Hotărârea Guvernului nr. 1.092/2010 , în temeiul art. 2 alin. (1) din Hotărârea Guvernului nr. 1.079/2010 pentru reprezentarea în cadrul organismelor create în temeiul tratatelor bilaterale privind gospodărirea apelor de frontieră , al art. 57 alin. (1), (4) și (5) din Ordonanța de urgență a Guvernului nr. 57/2019 privind Codul administrativ, cu modificările și completările ulterioare, precum și al art. 13 alin. (4) din Hotărârea Guvernului nr. 43/2020 privind organizarea și funcționarea Ministerului Mediului , Apelor și Pădurilor ,\n"+
"ministrul mediului, apelor și pădurilor emite prezentul ordin .";
}


function run_ner(){
    var text=document.getElementById('text').value;
    
    if(text.length<10){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");
    document.getElementById("loading").setAttribute("style","display:block");

    loadDataForm("inputForm",function(data){
    
        data=JSON.parse(data);
        
        console.log(data);
        
        if(data.status!="OK")
            document.getElementById("outputText").innerText="ERROR";
        else
            document.getElementById("outputText").innerText="";
        
        var html='<table cellpadding="3">';
        html+='<tr><th>ID</th><th>Type</th><th>Text</th><th>Start</th><th>End</th></tr>';
        
        for(var i=0;i<data.result.length;i++){
            var ob=data.result[i];
            html+='<tr>'+
                '<td>'+myescape(ob.id)+'</td>'+
                '<td>'+myescape(ob.type)+'</td>'+
                '<td>'+myescape(ob.text)+'</td>'+
                '<td>'+ob.start+'</td>'+
                '<td>'+ob.end+'</td>'+
                '</tr>';
        }

        html+='</table>';
    
        document.getElementById("output1").innerHTML=html;
    
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
