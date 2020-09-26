var types=["wordform","lemma","poslemma"];
var types_base=[
  "http://89.38.230.23/word_embeddings/",
  "http://89.38.230.23/word_embeddings_lemma/",
  "http://89.38.230.23/word_embeddings_lemma_msd/"
];

var itypes=["text","similar","graph"];
var itypes_base=[
  "",
  "view/similar.html",
  "view/graph.html"
];

var currentToNum=0;
var currentIntNum=0;

function switchTo(to){
    for(var i=0;i<types.length;i++){
        if(types[i]==to){
            document.getElementById("b"+types[i]).setAttribute("class","btn cur-p btn-primary");
            
            currentToNum=i;
            document.getElementById("iframe1").setAttribute("src",types_base[i]+itypes_base[currentIntNum]);
        }else{
            document.getElementById("b"+types[i]).setAttribute("class","btn cur-p btn-secondary");
        }
    }
}

function switchIntTo(to){
    for(var i=0;i<itypes.length;i++){
        if(itypes[i]==to){
            document.getElementById("bi"+itypes[i]).setAttribute("class","btn cur-p btn-primary");

            currentIntNum=i;
            document.getElementById("iframe1").setAttribute("src",types_base[currentToNum]+itypes_base[i]);
        }else{
            document.getElementById("bi"+itypes[i]).setAttribute("class","btn cur-p btn-secondary");
        }
    }

}

