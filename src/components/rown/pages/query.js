
function myescape(s){
    s=s.replace("<","&lt;");
    s=s.replace(">","&gt;");
    return s;
}


function lemmaSearchClick(){
    var tok=currentJSONTok;
    if(tok===false)return ;
    
    showPopupIFrame("index.php?path=rown/rown&word="+tok["_lemma"]+"&sid=");
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

    var div=document.getElementById("output");
    div.setAttribute("style","display:none");

    var div=document.getElementById("input");
    div.setAttribute("style","display:block");
}



function showOutput(n,num){
    for(var i=1;i<=num;i++){
        if(i==n){
          document.getElementById("output"+i).setAttribute("style","display:block; height:100%");
          document.getElementById("bOutput"+i).setAttribute("class","btn cur-p btn-success");
        }else{
          document.getElementById("output"+i).setAttribute("style","display:none");
          document.getElementById("bOutput"+i).setAttribute("class","btn cur-p btn-secondary");
        }
    }
    
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

function sendText(){
    var text=document.getElementById("text").value;
    if(text.length<1){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");
    document.getElementById("output").setAttribute("style","display:block");

    showPopupIFrame("index.php?path=rown/rown&word="+text+"&sid=");
}

function demo(){
    document.getElementById("text").value="român";

    sendText();
}



