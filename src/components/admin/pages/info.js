var numLogs=4;

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

function showInfo(n, hash){
    for(var i=1;i<=numLogs;i++){
        if(i==n){
          //setAttribute("output"+i,"style","display:block; height:100%; overflow:auto;");
          setAttribute("bOutput"+i,"class","btn cur-p btn-success");
        }else{
          //setAttribute("output"+i,"style","display:none");
          setAttribute("bOutput"+i,"class","btn cur-p btn-secondary");
        }
    }
    
    if(n==1){
        setAttribute("output1","style","display:none");
        setAttribute("output2","style","display:block; height:100%; overflow:auto;");
    }else{
        setAttribute("output2","style","display:none");
        setAttribute("output1","style","display:block; height:100%; overflow:auto;");
    }        
        
    
    if(hash!==undefined && hash!==null){
        if(window.location.hash===undefined || window.location.hash.substring(1)!==hash)
            window.location.hash="#"+hash;    
        
            
            if(n==1){
                document.getElementById("infoFrameViewer").src="index.php?path=admin/info_get&info="+hash;
            }else{
                setAttribute("loading","style","display:block;");
                setAttribute("output","style","display:none;");
                loadData("path=admin/info_get&info="+hash,function(data){
                    setAttribute("loading","style","display:none;");
                    setAttribute("output","style","display:block;");
                    document.getElementById("infoFileViewer").value=data;		 
                },function(){
                    alert("Error retrieving log data");
                    setAttribute("loading","style","display:none;");
                    setAttribute("output","style","display:block;");
                });
            }

    }
    
}

function showBasedOnHash(hash){
    if(hash=="phpinfo")showInfo(1,hash);
    else if(hash=="df")showInfo(2,hash);
    else if(hash=="cpuinfo")showInfo(3,hash);
    else if(hash=="meminfo")showInfo(4,hash);
    else showInfo(1,"phpinfo");
}




$(document).ready(function () {
    var h = window.location.hash.substr(1);
    
    showBasedOnHash(h);
        
});
