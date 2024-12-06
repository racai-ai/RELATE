<?php

//require_once "${LIB_PATH}/bibtex.php";

function renderResource($res){
	$desc=$res->getData("description_long",$res->getData("description_short","Nu are descriere"));
	
	$authors="";
	foreach($res->getData("authors",[]) as $aut){
		if(strlen($authors)>0)$authors.="; ";
		$authors.=$aut;
	}
	
	//$link="repository/".$res->getName();
	
	/*$path=$_SERVER['REQUEST_URI'];
	$pos=strrpos($path,'/index.php');
	if($pos!==false)$path=substr($path,0,$pos+1);
	$path.=$link;
	$path=((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']))?("https://"):("http://")).$_SERVER['SERVER_NAME'].$path;
    */
    
	/*$bibtex="";
	$papers=$res->getData("papers",[]);
	if(is_array($papers) && !empty($papers)){
		foreach($papers as $paper){
			$bibtex.=$paper."\n\n";
		}
		
	}
	
	$bibres=$res->getData("bib_resource","");
	$durls=$res->getData("download_urls",[]);
	if(strlen($bibres)==0 && $res->hasType(["lr"]) && (count($res->getFiles())>0 || is_array($durls) && !empty($durls) ) && ($res->getData("imported_ele",0)!==1)){
		$authorsbib="";
		foreach($res->getData("authors",[]) as $aut){
			if(strlen($authorsbib)>0)$authorsbib.=" and ";
			$authorsbib.=$aut;
		}		
		$title=$res->getData("title",$res->getName());
		$acr=$res->getData("acronym","");
		$titlebib=$title.((strlen($acr)>0)?(" ($acr)"):(""));
		$bibres="@dataset{".$res->getName()."-dataset,\n";
		if(strlen($authorsbib)>0)$bibres.='author={'.$authorsbib.'},'."\n";
		$bibres.='title= {{'.$titlebib.'}},'."\n";
		if(strlen($res->getData("year",""))==4)$bibres.='year='.$res->getData("year","0").','."\n";
		$bibres.='publisher={RELATE Repository},'."\n".'url= {'.$path.'}'."\n".'}'."\n\n";
		//$bibtex.=$bibres;
		//$bibtex.="\n\n";
	}*/

	
	$resTitle=$res->getData("title",$res->getName());
	if(strlen($res->getData("acronym",""))>0)$resTitle=$res->getData("acronym","")." - ".$resTitle;
	
	$resHtml= '<div class="resource">'.
		'<div class="resourceTitle"><a class="resourceLink">'.htmlentities($resTitle).'</a></div>';
	if(strlen($authors)>0)
		$resHtml.='<div class="resourceItem"><b>Autor(i):</b></div><div class="resourceItemContent">'.htmlentities($authors)."</div>";

	$org=$res->getData("organisation",[]);
	if(is_array($org) && count($org)>0){
		//$resHtml.='<div class="resourceItem"><b>Organisation:</b>&nbsp;&nbsp;';
		$resHtml.='<div class="resourceItemContent">';
		$first=true;
		foreach($org as $o){
			if($first)$first=false;
			else $resHtml.=";";
			$resHtml.=htmlentities($o);
		}
		$resHtml.="</div>";
	}
			
	//$resHtml.='<div class="resourceItem"><b>Stable RELATE URL:</b>&nbsp;&nbsp;<a href="'.$path.'">'.$path.'</a></div>';
	$resHtml.='<div class="resourceItem"><b>Licență:</b>&nbsp;&nbsp;'.htmlentities($res->getData("license","Not provided")).'</div>';

	$sz=$res->getData("size","");
	if(strlen($sz)>0)$resHtml.='<div class="resourceItem"><b>Size:</b>&nbsp;&nbsp;'.htmlentities($sz).'</div>';
	
	$durls=$res->getData("download_urls",[]);
	$files=$res->getFiles();
	if((is_array($durls) && !empty($durls)) || (is_array($files) && !empty($files))){
		$resHtml.='<div class="resourceItem"><b>Descărcare:</b></div><div class="resourceItemContent"><ul>';
		if(is_array($files)){
			foreach($files as $file=>$data){
				$url="index.php?path=edu/get_file&resource=".$res->getName()."&file=".$file;
				$resHtml.='<li><a href="'.$url.'" target="_blank">'.$file.'</a> ('.$data['size'].')</li>';
			}
		}
		if(is_array($durls)){
			foreach($durls as $url){
				$resHtml.='<li><a href="'.$url.'" target="_blank">'.$url.'</a></li>';
			}
		}
			
		$resHtml.="</ul></div>";
	}

	/*if(strlen($bibtex)+strlen($bibres)>0){
		
		$dlfname="citations-".$res->getName().".bib";
		$resHtml.='<div class="resourceItem"><b>Please include one or more of the following references in your research work:</b>'.
			'<a download="'.$dlfname.'" href="data:application/x-bibtex;name='.$dlfname.';base64,'.base64_encode($bibtex."\n\n".$bibres).'">[Download BibTex]</a>'.
			'</div><div class="resourceItemContent">';
		//var_dump($bibtex);
		$bibParser=new Bibtex($bibtex);
		$resHtml.=$bibParser->renderHtml();
		$resHtml.='</div>';
		
		if(strlen($bibres)>0){
			$resHtml.='<div class="resourceItem"><b>The "Dataset"-type reference is:</b>'.
				'</div><div class="resourceItemContent">';
			$bibParser=new Bibtex($bibres);
			$resHtml.=$bibParser->renderHtml();
			$resHtml.='</div>';
		}	
	}*/
	
	$descClass="resourceDescription";
	if($res->getData("description_long_type","")=="formatted")$descClass="resourceDescriptionPre";
	
	$resHtml.='<div class="resourceItem"><b>Descriere:</b></div><div class="resourceItemContent '.$descClass.'"><p>'.htmlentities($desc).'</p></div>';

	$ack=$res->getData("acknowledgement","");
	if(strlen($ack)>0)$resHtml.='<div class="resourceItem"><b>Acknowledgement:</b></div><div class="resourceItemContent">'.htmlentities($ack).'</div>';

	if($res->getData("imported_ele",0)===1){
		$resHtml.='<div class="resourceItem"><b>Disclaimer:</b></div><div class="resourceItemContent">This resource metadata was imported automatically. Please check carefully the additional details provided in the listed URLs. For corrections to this metadata please contact us.</div>';
	}
	
	$resHtml.="</div>";
	return $resHtml;


}

function getPageContent(){
	$resHtml="";
	if(isset($_REQUEST['resource'])){
		$repository=new Education();
		$res=$repository->getByName($_REQUEST['resource']);
		if($res!==false){
			$resHtml=renderResource($res);
		}
	}
	
    $html=file_get_contents(realpath(dirname(__FILE__))."/resource.html");
	$html=str_replace("{{RESOURCE}}",$resHtml,$html);
	
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/resource.css");
    
    return $css;
}

function getPageJS(){
}

?>