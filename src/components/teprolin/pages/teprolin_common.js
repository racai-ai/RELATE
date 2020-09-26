var currentJSONTokens=false;                                                  
var currentJSONSentences=false;
var currentSentNum=0;
var currentTreeNetwork=false;
var currentJSONTok=false;
var bratLoaded=false;

function JSON2TAB(jsonTokens){
    var conll="";
    for(var i=0;i<jsonTokens.length;i++){
        var sent=jsonTokens[i];
        conll+="<s>\t<s>\t<s>\t<s>\t<s>\t<s>\n";
        for(var j=0;j<sent.length;j++){
          conll+=
              sent[j]["_wordform"]+"\t"+
              sent[j]["_lemma"]+"\t"+
              sent[j]["_msd"]+"\t"+
              sent[j]["_ctg"]+"\t"+
              sent[j]["_phon"]+"\t"+
              sent[j]["_syll"]+"\n";
        }
        conll+="</s>\t</s>\t</s>\t</s>\t</s>\t</s>\n";
    }
    
    return conll;
}

function JSON2XML(jsonTokens){
    var xml='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\n'+"<xml>\n";
    for(var i=0;i<jsonTokens.length;i++){
        var sent=jsonTokens[i];
        xml+="    <S id=\""+(i+1)+"\">\n";
        for(var j=0;j<sent.length;j++){
          xml+=
              "        <W id=\""+(i+1)+"."+(j+1)+"\" "+
              "LEMMA=\""+sent[j]["_lemma"]+"\" "+
              "MSD=\""+sent[j]["_msd"]+"\" "+
              "CTAG=\""+sent[j]["_ctg"]+"\" "+
              "UPOS=\""+sent[j]["upos"]+"\" "+
              "NENT=\""+sent[j]["ner"]+"\" "+
              "PHON=\""+sent[j]["_phon"]+"\" "+
              "SYLL=\""+sent[j]["_syll"]+"\" "+
              "HEAD=\""+sent[j]["_head"]+"\" "+
              "DEPREL=\""+sent[j]["_deprel"]+"\" "+
              "CHUNK=\""+sent[j]["_chunk"]+"\">"+
              sent[j]["_wordform"]+"</W>\n";
        }
        xml+="    </S>\n";
    }
    xml+="</xml>\n";
    
    return xml;
}

function addToFeats(feats,name,value){
    if(value!==undefined && value!==null && value!="" && value!="_"){
        if(feats.length>0)feats+="|";
        feats+=name+"="+value;
    }
    return feats;
}

function getConllValue(value){
    if(value===undefined || value===null || value=="")return "_";
    return value;
}

function JSON2CONLLU(jsonTokens,jsonSentences){
    var conll="";
    for(var i=0;i<jsonTokens.length;i++){
        var sent=jsonTokens[i];
        if(conll.length>0)conll+="\n";
        conll+="# Sentence:"+jsonSentences[i]+"\n";
        for(var j=0;j<sent.length;j++){
          var feats="";
          feats=addToFeats(feats,"Syll",sent[j]["_syll"]);
          feats=addToFeats(feats,"Phon",sent[j]["_phon"]);
          feats=addToFeats(feats,"Expn",sent[j]["_expand"]);
          feats=addToFeats(feats,"Chnk",sent[j]["_chunk"]);
          feats=addToFeats(feats,"NEnt",sent[j]["ner"]);
        
          conll+=
              getConllValue(sent[j]["_id"])+"\t"+
              getConllValue(sent[j]["_wordform"])+"\t"+
              getConllValue(sent[j]["_lemma"])+"\t"+
              getConllValue(sent[j]["upos"])+"\t"+
              getConllValue(sent[j]["_msd"])+"\t"+
              "_"+"\t"+
              getConllValue(sent[j]["_head"])+"\t"+
              getConllValue(sent[j]["_deprel"])+"\t"+
              "_"+"\t"+
              getConllValue(feats)+"\n"
              ;
        }
    }
    
    return conll;
}

function JSON2CONLLX(jsonTokens,jsonSentences){
    var conll="";
    for(var i=0;i<jsonTokens.length;i++){
        var sent=jsonTokens[i];
        if(conll.length>0)conll+="\n";
        conll+="# Sentence:"+jsonSentences[i]+"\n";
        for(var j=0;j<sent.length;j++){
        
          var feats="";
          feats=addToFeats(feats,"Syll",sent[j]["_syll"]);
          feats=addToFeats(feats,"Phon",sent[j]["_phon"]);
          feats=addToFeats(feats,"Expn",sent[j]["_expand"]);
          feats=addToFeats(feats,"Chnk",sent[j]["_chunk"]);
          feats=addToFeats(feats,"NEnt",sent[j]["ner"]);
          
        
          conll+=
              getConllValue(sent[j]["_id"])+"\t"+
              getConllValue(sent[j]["_wordform"])+"\t"+
              getConllValue(sent[j]["_lemma"])+"\t"+
              getConllValue(sent[j]["_ctg"])+"\t"+
              getConllValue(sent[j]["_msd"])+"\t"+
              getConllValue(feats)+"\t"+
              getConllValue(sent[j]["_head"])+"\t"+
              getConllValue(sent[j]["_deprel"])+"\t"+
              "_"+"\t"+
              "_"+"\n"
              ;
        }
    }
    
    return conll;
}

function JSON2CHUNKS(jsonTokens,jsonSentences){
    var ret="";
    for(var i=0;i<jsonTokens.length;i++){
        var sent=jsonTokens[i];
        if(ret.length>0)ret+="\n";
        ret+="Sentence: "+myescape(jsonSentences[i])+"<br/>\n";
        ret+="<table class=\"chunktable\"><tr><td>ID</td><td>Chunk</td></tr>";
        var chunks={};
        for(var j=0;j<sent.length;j++){
          if(sent[j]["_chunk"].length==0)continue;
          var arr=sent[j]["_chunk"].split(",");
          for(var k=0;k<arr.length;k++){
              var c=arr[k];
              if(typeof chunks[c] === 'undefined')
                  chunks[c]=sent[j]['_wordform'];
              else
                  chunks[c]+=" "+sent[j]['_wordform'];
          }
        }

        for(var key in chunks){
            ret+="<tr><td>"+key+"</td><td>"+myescape(chunks[key])+"</td></tr>";
        }
        
        ret+="</table>";
    }
    
    return ret;
}



function myescape(s){
    s=s.replace("<","&lt;");
    s=s.replace(">","&gt;");
    return s;
}

function JSON2Tree(jsonTokens){
    document.getElementById("outputTreeSent").innerText=currentJSONSentences[currentSentNum];

    var gnodes = new vis.DataSet([]);
    var gedges = new vis.DataSet([]);

    var container = document.getElementById('outputTree');
    var gdata = {
      nodes: gnodes,
      edges: gedges
    };
    var options = {
      layout:{
          hierarchical:{
              enabled: true,
              levelSeparation:60,
              blockShifting:true,
              parentCentralization:true,

          }
      }
    };
    var network = new vis.Network(container, gdata, options);
    currentTreeNetwork=network;

    gnodes.add({
        id:0,
        label:"_root",
        //color:"#101010"
    });
    
        var sent=jsonTokens[currentSentNum];
        for(var j=0;j<sent.length;j++){
            gnodes.add({
                id:sent[j]["_id"],
                label:sent[j]["_wordform"],
                //color:"#101010"
            });
        }
        
    
        var sent=jsonTokens[currentSentNum];
        for(var j=0;j<sent.length;j++){
            gedges.add({
                from:sent[j]["_id"],
                to:sent[j]["_head"],
                label:sent[j]["_deprel"],
                arrows:"to"
            });
        }
        
        
    network.on("click",function(data){
        if(data["nodes"][0]<=0)return ;
        var tok=currentJSONTokens[currentSentNum][data["nodes"][0]-1];
        currentJSONTok=tok;
        
        var td_style='style="vertical-align:top;border:1px solid blue; border-collapse:collapse;"';
        var td_start='<td '+td_style+'>';
        
        var html="";
        html+='<table style="border:1px solid blue; border-collapse:collapse;">'+
        '<tr>'+td_start+'Word '+
            '<br/><button type="button" class="btn cur-p btn-secondary" onclick="wordSearchClick();"><i class="fa fa-search"></i></button>'+
            '</td>'+td_start+tok["_wordform"]+'</td></tr>'+
        '<tr>'+td_start+'Lemma '+
            '<br/><button type="button" class="btn cur-p btn-secondary" onclick="lemmaSearchClick();"><i class="fa fa-tree"></i></button>'+
            '</td>'+td_start+tok["_lemma"]+'</td></tr>'+
        '<tr>'+td_start+'U-POS</td>'+td_start+tok["upos"]+'</td></tr>'+
        '<tr>'+td_start+'CTAG</td>'+td_start+tok["_ctg"]+'</td></tr>'+
        '<tr>'+td_start+'MSD</td>'+td_start+tok["_msd"]+'</td></tr>'+
        '<tr>'+td_start+'Chunk</td>'+td_start+tok["_chunk"]+'</td></tr>'+
        '<tr>'+td_start+'Named Entity</td>'+td_start+tok["ner"]+'</td></tr>'+
        '<tr>'+td_start+'Phonetic '+
            '<br/><button type="button" class="btn cur-p btn-secondary" onclick="phoneticClick();"><i class="fa fa-play"></i></button>'+
            '&nbsp;<button type="button" class="btn cur-p btn-secondary" onclick="phoneticClick2();"><i class="fa fa-assistive-listening-systems"></i></button>'+
            '</td>'+td_start+tok["_phon"]+'</td></tr>'+
        '<tr>'+td_start+'Syllables</td>'+td_start+tok["_syll"]+'</td></tr>'+
        '<tr>'+td_start+'Similar Words</td><td id="treeSimilarWords" '+td_style+'>&nbsp;</td></tr>'+
        '<tr>'+td_start+'Similar Lemma</td><td id="treeSimilarLemma" '+td_style+'>&nbsp;</td></tr>'+
        '<tr>'+td_start+'Similar Lemma + POS</td><td id="treeSimilarLemmaPos" '+td_style+'>&nbsp;</td></tr>'+
        '</table>';
        
        document.getElementById("treeNodeProperties").innerHTML=html;
        
        loadDataComplete("https://corolaws.racai.ro/word_embeddings/view/get_vectors_similar.php?word="+tok["_wordform"]+"&top=10","GET",null,function(d){
            d=JSON.parse(d);
            var html="";
            for(var i=0;i<d["words"].length;i++){
                html+=myescape(d["words"][i])+"<br/>";
            }
            document.getElementById("treeSimilarWords").innerHTML=html;
        });

        loadDataComplete("https://corolaws.racai.ro/word_embeddings_lemma/view/get_vectors_similar.php?word="+tok["_lemma"]+"&top=10","GET",null,function(d){
            d=JSON.parse(d);
            var html="";
            for(var i=0;i<d["words"].length;i++){
                html+=myescape(d["words"][i])+"<br/>";
            }
            document.getElementById("treeSimilarLemma").innerHTML=html;
        });

        loadDataComplete("https://corolaws.racai.ro/word_embeddings_lemma_msd/view/get_vectors_similar.php?word="+tok["_msd"].substring(0,2)+"_"+tok["_lemma"]+"&top=10","GET",null,function(d){
            d=JSON.parse(d);
            var html="";
            for(var i=0;i<d["words"].length;i++){
                html+=myescape(d["words"][i])+"<br/>";
            }
            document.getElementById("treeSimilarLemmaPos").innerHTML=html;
        });
        
    });
        

}

function phoneticClick(){
    var tok=currentJSONTok;
    if(tok===false)return ;
    
    showPopupIFrame("https://corolaws.racai.ro/corola_sound_search/index.php?search_type=0&search="+tok["_wordform"]+"&search2_type=0&search2=&start=0&count=20&show_word=on&show_pos_ctag=on&context=5&embedded=y");    
}

function phoneticClick2(){
    var tok=currentJSONTok;
    if(tok===false)return ;
    
    showPopupIFrame("index.php?path=ssla/synthesize&text="+tok["_wordform"]);    
}


function wordSearchClick(){
    var tok=currentJSONTok;
    if(tok===false)return ;
    
    showPopupIFrame("https://korap.racai.ro/?q="+tok["_wordform"]+"&collection=&ql=poliqarp&cutoff=1");
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
}

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
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(data);
    }else{
        xhttp.send();
    }
}

function sendText(){

    var text=document.getElementById("text").value;
    if(text.length<10){alert("Please enter a valid text"); return ;}

    document.getElementById("input").setAttribute("style","display:none");
    document.getElementById("loading").setAttribute("style","display:block");

    //document.getElementById("outputText").value=text;
    
    var eo=getExecAndOpts();
    var exec=eo.exec;
    var opts=eo.opts;
    
    var errorfn=function(){
        alert("Error accessing TEPROLIN web service");
        document.getElementById("loading").setAttribute("style","display:none");
        document.getElementById("input").setAttribute("style","display:block; height:100%");
    };
    
    loadData( 
        "path=teprolinws&text="+encodeURIComponent(text)+"&"+opts+"&exec="+exec,
        function(response){
          //document.getElementById("outputJSON").value=response;
          
          var obj={};
          try{
              obj=JSON.parse(response) ;
          }catch(err){
              errorfn();
              return;
          }
          
          document.getElementById("outputJSON").value=JSON.stringify(obj,null,4);
          
          document.getElementById("outputText").value=obj['teprolin-result']['text'];
          
          var conll=JSON2CONLLU(obj["teprolin-result"]["tokenized"],obj["teprolin-result"]["sentences"]);
          document.getElementById("outputCONLLU").value=conll;
          if(conll.length>0){
              document.getElementById("bOutput2").style="display:inline-block";
          }else{
              document.getElementById("bOutput2").style="display:none";
          }

          conll=JSON2CONLLX(obj["teprolin-result"]["tokenized"],obj["teprolin-result"]["sentences"]);
          document.getElementById("outputCONLLX").value=conll;
          if(conll.length>0){
              document.getElementById("bOutput3").style="display:inline-block";
          }else{
              document.getElementById("bOutput3").style="display:none";
          }

          var xml=JSON2XML(obj["teprolin-result"]["tokenized"],obj["teprolin-result"]["sentences"]);
          document.getElementById("outputXML").value=xml;
          if(xml.length>0){
              document.getElementById("bOutput4").style="display:inline-block";
          }else{
              document.getElementById("bOutput4").style="display:none";
          }
          
          currentJSONTokens=obj["teprolin-result"]["tokenized"];
          currentJSONSentences=obj["teprolin-result"]["sentences"];
          currentSentNum=0;
          
          if(currentJSONTokens.length==0){
              document.getElementById("bOutput6").style="display:none";
          }else{
              document.getElementById("bOutput6").style="display:inline-block";          
          }
          
          
          var bratData=JSON2BRAT(currentJSONTokens);
          console.log(bratData);
          if(currentJSONTokens.length==0 || bratData["entities"].length==0){
              document.getElementById("bOutput7").style="display:none";
          }else{
              document.getElementById("bOutput7").style="display:inline-block";  
              bratLoaded=false;
          }
          
          var chunks=JSON2CHUNKS(obj["teprolin-result"]["tokenized"],obj["teprolin-result"]["sentences"]);
          document.getElementById("output8").innerHTML=chunks;
          
          
          document.getElementById("loading").setAttribute("style","display:none");
          showOutput(1,7);
          document.getElementById("output").setAttribute("style","display:block; height:100%");
        
        }, 
        errorfn
    )
}


function showOutput(n,num){
    for(var i=1;i<=num;i++){
        if(i==n){
          document.getElementById("output"+i).setAttribute("style","display:block; height:100%; overflow:auto;");
          document.getElementById("bOutput"+i).setAttribute("class","btn cur-p btn-success");
        }else{
          document.getElementById("output"+i).setAttribute("style","display:none");
          document.getElementById("bOutput"+i).setAttribute("class","btn cur-p btn-secondary");
        }
    }
    
    if(n==6){
          JSON2Tree(currentJSONTokens);
    } else if(n==7){
          if(!bratLoaded){
            var bratData=JSON2BRAT(currentJSONTokens);
            document.getElementById('bratData').value = JSON.stringify(bratData);  
            document.getElementById('bratForm').submit(); 
            bratLoaded=true;
          } 
    }
}

function TreePrevSent(){
    if(currentSentNum>0){
        currentSentNum--;
        JSON2Tree(currentJSONTokens);
    }
}

function TreeNextSent(){
    if(currentSentNum<currentJSONSentences.length-1){
        currentSentNum++;
        JSON2Tree(currentJSONTokens);
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

function demo(){
    document.getElementById("text").value=
"Fiscul va face verificări la firmele indicate de CNSP, iar pe zona de dezvoltare va acorda granturi, precum cele pentru primării.\n"+
//"În cel de-al doilea caz este vorba despre vestitul fond de 10 miliarde euro, sumă totală pentru 20 de ani.\n\n"+
//"Președintele Comisiei Naționale de Strategie și Prognoză, Ion Ghizdeanu, devine unul dintre cei mai importanți oameni, mai ales pentru primari, fiind cel care va acorda bani pentru dezvoltare.\n\n"+ 
//"După ce anul trecut prin OUG 114 se înființa Fondul de Dezvoltare și Investiții, pe site-ul instituției au fost publicate, în dezbatere, normele metodologice. Este, de fapt, o schemă de granturi cu un plafon de circa 2,37 miliarde lei, anual. Practic, CNSP se va împrumuta de la Ministerul Finanțelor, din Trezorerie, anual.\n\n"+
"Diabetul zaharat este un sindrom caracterizat prin valori crescute ale concentrației glucozei în sânge (hiperglicemie) și dezechilibrarea metabolismului.\n"+
//"Hormonul numit insulină permite celulelor corpului să folosească glucoza ca sursă de energie. Când secreția de insulină este insuficientă sau când insulina nu-și îndeplinește rolul în organism, afecțiunea se numește diabet zaharat. Diabetul poate fi ținut sub control printr-o supraveghere atentă a dietei și a greutății și prin exerciții fizice, ca supliment al tratamentului medical.";
"";    
    sendText();
}



function JSON2BRAT(jsonTokens){
    var text="";
    var entities=[];
    
    var currentEnt="";
    var currentStart=0;
    var entId=0;
    
    for(var i=0;i<jsonTokens.length;i++){
        var sent=jsonTokens[i];
        for(var j=0;j<sent.length;j++){
            var tok=sent[j];
            
            if(tok["ner"]!=currentEnt){
                if(currentEnt!="" && currentEnt!=undefined && currentEnt!="O"){
                     entId++;
                     entities[entities.length]=[
                        "T"+entId,currentEnt,[[currentStart,text.length-1]]
                     ];
                }
                
                currentEnt=tok["ner"];
                currentStart=text.length;
            }
            text+=tok["_wordform"]+" ";
        }
        if(currentEnt!="" && currentEnt!=undefined && currentEnt!="O"){
            entId++;
            entities[entities.length]=[
             "T"+entId,currentEnt,[[currentStart,text.length-1]]
            ];
        }
        text+="\n";
        currentEnt="";
    }

    var docData={
        text:text,
        entities:entities
    };

    return docData;
}

