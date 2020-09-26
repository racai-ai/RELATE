
function myescape(s){
    s=s.replace("<","&lt;");
    s=s.replace(">","&gt;");
    return s;
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

var enwordnetObj=false;
var rowordnetObj=false;
var encalls=0;
var rocalls=0;

function addToEnWordnet(obj){
          if(enwordnetObj===false && obj.senses!==undefined)enwordnetObj=obj;
          else{
              if(obj.senses!==undefined)
                  for(var i=0;i<obj.senses.length;i++)
                      enwordnetObj.senses[enwordnetObj.senses.length]=obj.senses[i];
          }
}

function addToRoWordnet(obj){
          if(rowordnetObj===false && obj.senses!==undefined)rowordnetObj=obj;
          else{
              if(obj.senses!==undefined)
                  for(var i=0;i<obj.senses.length;i++)
                      rowordnetObj.senses[rowordnetObj.senses.length]=obj.senses[i];
          }
}

function loadEnBySid(sid){
    loadDataComplete("index.php?path=rownws&word=&sid="+sid+"&wn=en","GET",null,function(response){
          if(response.length==0)response="{}";
          var obj=JSON.parse(response) ;
          addToEnWordnet(obj);
          encalls--;
          if(encalls==0)
            renderWordnet("enwordnet",enwordnetObj,false);
    });
}

function loadRoBySid(sid){
    loadDataComplete("index.php?path=rownws&word=&sid="+sid+"&wn=ro","GET",null,function(response){
          if(response.length==0)response="{}";
          var obj=JSON.parse(response) ;
          addToRoWordnet(obj);
          rocalls--;
          if(rocalls==0)
            renderWordnet("rowordnet",rowordnetObj,false);
          
    });
}

function newLoadRoBySid(sid){
    document.getElementById("rowordnet").innerHTML='<h2 align="center">RoWordNet</h2>';
    rowordnetObj=false;
    rocalls++;
    loadRoBySid(sid);
    //renderWordnet("rowordnet",rowordnetObj,false);
}

function newLoadEnBySid(sid){
    document.getElementById("enwordnet").innerHTML='<h2 align="center">EnWordNet</h2>';
    enwordnetObj=false;
    encalls++;
    loadEnBySid(sid);
    //renderWordnet("enwordnet",enwordnetObj,false);
}


if (typeof(String.prototype.localeCompare) === 'undefined') {
    String.prototype.localeCompare = function(str, locale, options) {
        return ((this == str) ? 0 : ((this > str) ? 1 : -1));
    };
}

function renderWordnet(id,obj,docall){
    var output=document.getElementById(id);
    var ret='';

    if(obj.senses===undefined){
        ret+="Word/Synset not found !";
    }else{ 
    
        for(var i=0;i<obj.senses.length;i++){
            for(var j=i+1;j<obj.senses.length;j++){
                if(obj.senses[j].id.localeCompare(obj.senses[i].id)<0){
                    var t=obj.senses[i];
                    obj.senses[i]=obj.senses[j];
                    obj.senses[j]=t;
                }
            }
        }
    
        for(var i=0;i<obj.senses.length;i++){
            var s=obj.senses[i];
            if(docall===true){
                encalls++;
                loadEnBySid(s.id);
            }
            ret+='<div class="synset">';
            ret+='<div class="s_id" style="word-wrap: break-word;">Synset: '+s.id+' '+s.literal+'</div>';
            ret+='<div class="s_data">('+s.pos+') '+s.definition+'</div>';
            ret+='<div class="s_rel">';
              ret+='<ul>';
              for(var j=0;j<s.relations.length;j++){
                  var rel=s.relations[j];
                  ret+='<li style="word-wrap: break-word;">'+rel.rel+' <a href="#" onclick="newLoadRoBySid(\''+rel.tid+'\');newLoadEnBySid(\''+rel.tid+'\');return false;">'+rel.tid+'</a> '+rel.tliteral+'</li>';
              }                
              ret+='</ul>';
            ret+='</div>';
            ret+='</div>';
        }
    }
    
    output.innerHTML+=ret;

}


function sendText(){
    var text=document.getElementById("text").value;
    if(text.length<1){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");

    loadDataComplete("index.php?path=rownws&word="+text+"&sid=&wn=ro","GET",null,function(response){
          var output=document.getElementById("output");
          output.setAttribute("style","display:block");
          
          var obj=JSON.parse(response) ;
          
          var ret='<div style="display:inline-block; width:40%; vertical-align:top;" id="rowordnet"><h2 align="center">RoWordNet</h2></div>'+
                  '<div style="display:inline-block; width:40%; vertical-align:top;" id="enwordnet"><h2 align="center">EnWordNet</h2></div>';
          
          output.innerHTML=ret;
          
          enwordnetObj=false;
          rowordnetObj=obj;
          
          renderWordnet("rowordnet",rowordnetObj,true);
          //renderWordnet("enwordnet",enwordnetObj,false);
    });

}

function demo(){
    document.getElementById("text").value="român";

    sendText();
}



