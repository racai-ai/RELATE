<?php

    if(!isset($_REQUEST['word']))die();
    $word=$_REQUEST['word'];
    if(!isset($_REQUEST['sid']))die();
    $sid=$_REQUEST['sid'];
    
    $data=ROWN_call($word,$sid);
    
     //echo "<!-- \n";var_dump($data);echo "\n -->\n";
     $data=json_decode($data,true);
     //echo "<!-- \n";var_dump($data);echo "\n -->\n";
     
     header('Content-Type: text/turtle');
?>
@prefix dc: <http://purl.org/dc/terms/> .
@prefix ili: <http://ili.globalwordnet.org/ili/> .
@prefix lime: <http://www.w3.org/ns/lemon/lime#> .
@prefix ontolex: <http://www.w3.org/ns/lemon/ontolex#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix schema: <http://schema.org/> .
@prefix skos: <http://www.w3.org/2004/02/skos/core#> .
@prefix synsem: <http://www.w3.org/ns/lemon/synsem#> .
@prefix wn: <http://wordnet-rdf.princeton.edu/ontology#> .
@prefix pwnlemma: <http://wordnet-rdf.princeton.edu/rdf/lemma/> .
@prefix pwnid: <http://wordnet-rdf.princeton.edu/id/> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .

<?php if(!empty($data)){ ?>

    <?php 
		
		function pos2ttl($pos){
				if($pos=="n")return "noun";
				if($pos=="v")return "verb";
				if($pos=="a")return "adjective";
				if($pos=="r")return "adverb";
				
				return $pos;
		}
		
		$ttl_pos=[];
		$ttl_senses=[];
		$ttl_id=[];
		foreach($data['senses'] as $s){
				$id=explode("-",$s['id']);
				
				foreach(explode(",",$s['literal']) as $lit){
						$ttlp=$lit."-".$id[2];
						if(!isset($ttl_pos[$ttlp]))$ttl_pos[$ttlp]=['pos'=>pos2ttl($id[2]),'senses'=>[],'lit'=>$lit];
						
						$sid=$lit."-".$id[1]."-".$id[2];
						$ttl_pos[$ttlp]['senses'][]=$sid;
						$pwid=$id[1]."-".$id[2];
						if(!isset($ttl_senses[$sid])){
								$ttl_senses[$sid]=$pwid;
						}
						
						if(!isset($ttl_id[$pwid])){
								$ttl_id[$pwid]=['pos'=>pos2ttl($id[2]),'def'=>$s['definition'],'rel'=>[]];
						}
						
						foreach($s['relations'] as $rel){
								$rid=explode("-",$rel['tid']);
								$rid=$rid[1]."-".$rid[2];
								$ttl_id[$pwid]['rel'][$rid]=$rel['rel'];
						}						
				}
		}
		
		echo "\n";
		foreach($ttl_pos as $ttlp=>$d){
				echo "<#${ttlp}>\n";
  			echo "   ontolex:canonicalForm [\n";
    		echo "      ontolex:writtenRep \"${d['lit']}\"@ro\n";
    		echo "   ] ;\n";
    		foreach($d['senses'] as $sense){
				echo "   ontolex:sense <#${sense}> ;\n";
				}
				
				echo "   wn:partOfSpeech wn:${d['pos']} ;\n";
				echo "   a ontolex:LexicalEntry .\n\n";
		}
		
		foreach($ttl_senses as $sid=>$pwnid){
				echo "<#${sid}>\n";
   			echo "   ontolex:isLexicalizedSenseOf pwnid:${pwnid} ;\n";
   			echo "   a ontolex:LexicalSense .\n\n";		
		}
		
		foreach($ttl_id as $pwnid=>$d){
				echo "pwnid:${pwnid}\n";
				echo "   wn:partOfSpeech wn:${d['pos']} ;\n";
  			echo "   wn:definition [ rdf:value \"${d['def']}\"@ro ] ;\n";
  			foreach($d['rel'] as $rid=>$type){
  			echo "   wn:${rid} pwnid:${type} ;\n";
				} 
   			echo "   a ontolex:LexicalConcept .\n\n"; 		
		}
		
}
