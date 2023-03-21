<?php

// from: https://github.com/renanbr/bibtex-parser

use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;
use RenanBr\BibTexParser\Processor;

require_once "${LIB_PATH}/extern/bibtex-parser/Parser.php";
require_once "${LIB_PATH}/extern/bibtex-parser/ListenerInterface.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Listener.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Exception/ExceptionInterface.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Exception/ParserException.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Exception/ProcessorException.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/TagSearchTrait.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/TagCoverageTrait.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/DateProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/FillMissingProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/KeywordsProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/LatexToUnicodeProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/NamesProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/TagNameCaseProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/TrimProcessor.php";
require_once "${LIB_PATH}/extern/bibtex-parser/Processor/UrlFromDoiProcessor.php";

class Bibtex {
    private $bibtex;
    
    public function __construct($bibtex){
        $this->bibtex = $bibtex;
    }
    
    public function getBibtex(){return $this->bibtex;}

	private function renderHtmlAuthors($entry){
		$ret="";
		if(isset($entry['author'])){
			$first=true;
			foreach($entry['author'] as $author){
				if($first)$first=false;
				else $ret.=" and ";
				$ret.=$author['last'].",".$author['first'];
			}
		}
		return $ret;
	}
	
	private function renderHtmlInproceedings($entry){
		$ret="";
		$ret.=$this->renderHtmlAuthors($entry);
		$ret.=" (".$entry["year"]."). ";
		$ret.=htmlentities(str_replace("{","",str_replace("}","",$entry["title"]))).". ";
		$ret.="In ".htmlentities(str_replace("{","",str_replace("}","",$entry["booktitle"])));
		if(isset($entry["pages"]) && !empty($entry["pages"]))$ret.=", pages ".str_replace("--","-",$entry["pages"]);
		if(isset($entry["publisher"]) && !empty($entry["publisher"]))$ret.=", ".$entry["publisher"];
		if(isset($entry["url"]) && !empty($entry["url"]))$ret.=', <a href="'.$entry["url"].'" target="_blank">'.$entry["url"].'</a> ';
		$ret.=".";
		
		return $ret;
	}

	private function renderHtmlJournal($entry){
		$ret="";
		$ret.=$this->renderHtmlAuthors($entry);
		$ret.=" (".$entry["year"]."). ";
		$ret.=htmlentities(str_replace("{","",str_replace("}","",$entry["title"]))).". ";
		$ret.=htmlentities(str_replace("{","",str_replace("}","",$entry["journal"])));
		if(isset($entry["volume"]) && !empty($entry["volume"]))$ret.=", vol ".str_replace("--","-",$entry["volume"]);
		if(isset($entry["number"]) && !empty($entry["number"]))$ret.=", no ".str_replace("--","-",$entry["number"]);
		if(isset($entry["pages"]) && !empty($entry["pages"]))$ret.=", pages ".str_replace("--","-",$entry["pages"]);
		if(isset($entry["publisher"]) && !empty($entry["publisher"]))$ret.=", ".$entry["publisher"];
		if(isset($entry["url"]) && !empty($entry["url"]))$ret.=', <a href="'.$entry["url"].'" target="_blank">'.$entry["url"].'</a> ';
		$ret.=".";
		
		return $ret;
	}

	private function renderHtmlDataset($entry){
		$ret="";
		$ret.=$this->renderHtmlAuthors($entry);
		if(isset($entry["year"]))$ret.=" (".$entry["year"].")";
		$ret.=". ";
		$ret.=htmlentities(str_replace("{","",str_replace("}","",$entry["title"]))).". ";
		$ret.="Dataset, ".htmlentities(str_replace("{","",str_replace("}","",$entry["publisher"])));
		if(isset($entry["url"]) && !empty($entry["url"]))$ret.=', <a href="'.$entry["url"].'" target="_blank">'.$entry["url"].'</a> ';
		$ret.=".";
		
		return $ret;
	}

	private function renderHtmlPhD($entry){
		$ret="";
		$ret.=$this->renderHtmlAuthors($entry);
		$ret.=" (".$entry["year"]."). ";
		$ret.=htmlentities(str_replace("{","",str_replace("}","",$entry["title"]))).". ";
		$ret.="PhD Thesis";
		if(isset($entry["language"]))$ret.=", in ".$entry["language"];
		if(isset($entry["school"]))$ret.=", ".htmlentities(str_replace("{","",str_replace("}","",$entry["school"])));
		if(isset($entry["url"]) && !empty($entry["url"]))$ret.=', <a href="'.$entry["url"].'" target="_blank">'.$entry["url"].'</a> ';
		$ret.=".";
		
		return $ret;
	}

	public function renderHtml(){
		$listener = new Listener();
		$listener->addProcessor(new Processor\TagNameCaseProcessor(CASE_LOWER));
		$listener->addProcessor(new Processor\NamesProcessor());
		// $listener->addProcessor(new Processor\KeywordsProcessor());
		// $listener->addProcessor(new Processor\DateProcessor());
		// $listener->addProcessor(new Processor\FillMissingProcessor([/* ... */]));
		// $listener->addProcessor(new Processor\TrimProcessor());
		// $listener->addProcessor(new Processor\UrlFromDoiProcessor());		
		//$listener->addProcessor(new Processor\LatexToUnicodeProcessor());
		
		$parser = new Parser();
		$parser->addListener($listener);
	
		$parser->parseString($this->bibtex); // or parseFile('/path/to/file.bib')
		$entries = $listener->export();	
		
		$ret="<ul>";
		foreach($entries as $entry){
			$ret.='<li class="publication">';
			if(strcasecmp($entry["type"],"inproceedings")==0)$ret.=$this->renderHtmlInproceedings($entry);
			else if(strcasecmp($entry["type"],"dataset")==0)$ret.=$this->renderHtmlDataset($entry);
			else if(strcasecmp($entry["type"],"phdthesis")==0)$ret.=$this->renderHtmlPhD($entry);
			else if(strcasecmp($entry["type"],"article")==0)$ret.=$this->renderHtmlJournal($entry);
			
			$dlfname=$entry["citation-key"].'.bib';
			$ret.='<a download="'.$dlfname.'" href="data:application/x-bibtex;name='.$dlfname.';base64,'.base64_encode($entry["_original"]).'" target="_blank">[Download BibTex]</a>';
			
			$ret.='</li>';
		}
		$ret.="</ul>";
		
		return $ret;
	}
}
