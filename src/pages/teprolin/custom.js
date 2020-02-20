function getExecAndOpts(){
    var exec="";
    var opts="";
    
    for(var i=1;i<20;i++){
        var el=document.getElementById("op"+i);
        if(el==null)break;
        if(el.checked){
            if(exec.length>0)exec+=",";
            exec+=el.value;

            for(var j=1;j<10;j++){
              var r=document.getElementById("op"+i+"_r"+j);
              if(r==null)break;
              if(r.checked){
                  if(opts.length>0)opts+="&";
                  opts+=el.value+"="+r.value;
              }
            }

        }
    }

    return {exec:exec,opts:opts};
}