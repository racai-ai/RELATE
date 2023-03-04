// This file requires the ORCID Bibtex Parse
// In RELATE this is available in extern/ORCID_bibtexParse.js

function bibClean(s){
	return s.replace("{","").replace("}","");
}

function renderBib(bib, elementId){
	var data = bibtexParse.toJSON(bib);

	var html="";
	for(var i=0;i<data.length;i++){
		var d=data[i];
		html+="<div style=\"margin:20px; margin-left:50px\">"+
			d.entryTags.author+
			". "+d.entryTags.year+
			". <a href=\""+d.entryTags.url+"\" target=\"_blank\">"+bibClean(d.entryTags.title)+"</a>. "+
			((d.entryTags.booktitle!==undefined)?("In <i>"+bibClean(d.entryTags.booktitle)+"</i>"):(""))+
			((d.entryTags.journal!==undefined)?("<i>"+bibClean(d.entryTags.journal)+"</i>"):(""))+
			((d.entryTags.volume!==undefined)?(", vol. "+bibClean(d.entryTags.volume)):(""))+
			((d.entryTags.number!==undefined)?(", no. "+bibClean(d.entryTags.number)):(""))+
			((d.entryTags.pages!==undefined)?(", pages "+d.entryTags.pages.replace("--","-")):(""))+
			((d.entryTags.publisher!==undefined)?(", "+bibClean(d.entryTags.publisher)):(""))+
			".</div>";
	}

	document.getElementById(elementId).innerHTML=html;
}
