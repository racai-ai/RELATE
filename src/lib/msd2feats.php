<?php
// See http://nl.ijs.si/ME/V4/msd/html/msd.msds-ro.html
// and http://nl.ijs.si/ME/V4/msd/html/msd-ro.html
$MSDFEATS=false;

// See http://universaldependencies.org/docsv1/tagset-conversion/ro-multext-uposf.html
$MSDUDFEATS=false;

function MSD2FEATS($msd){
    global $MSDFEATS;
    
    if($MSDFEATS===false){
        $fp=fopen(dirname(__FILE__)."/msd_feats.csv","r");
        $first=true;
        while(!feof($fp)){
            $line=fgetcsv($fp,0,";");
            if($line===null || $line===false)break;
            
            if($first){$first=false;continue;}
            
            if(count($line)<2 || strlen($line[1])==0 || strlen($line[0])==0)continue;
            
            $feats=str_replace(" ","|",trim($line[1]));
            /*$feats=str_replace("Noun|Type","NounType",$feats);
            $feats=str_replace("Verb|Type","VerbType",$feats);
            $feats=str_replace("Adjective|Type","AdjType",$feats);
            $feats=str_replace("Pronoun|Type","PronType",$feats);
            $feats=str_replace("Determiner|Type","DetType",$feats);
            $feats=str_replace("Article|Type","ArtType",$feats);
            $feats=str_replace("Determiner|Type","DetType",$feats);
            $feats=str_replace("Adverb|Type","AdvType",$feats);
            $feats=str_replace("Adposition|Type","AdpType",$feats);
            $feats=str_replace("Conjunction|Type","ConjType",$feats);
            $feats=str_replace("Numeral|Type","NumType",$feats);
            $feats=str_replace("Particle|Type","PartType",$feats);
            $feats=str_replace("Interjection|Type","IntType",$feats);
            $feats=str_replace("Abbreviation|Type","AbbreviationType",$feats);
            $feats=str_replace("Residual|Type","ResidualType",$feats);
            */
            
            $MSDFEATS[trim($line[0])]=$feats;
            
        }
        fclose($fp);
        
    }

    if(isset($MSDFEATS[$msd]))return $MSDFEATS[$msd];
    
    return "_";    
    
}

function MSD2UDFEATS($msd){
    global $MSDUDFEATS;
    
    if($MSDUDFEATS===false){
        $fp=fopen(dirname(__FILE__)."/msd_feats_ud.csv","r");
        $first=true;
        while(!feof($fp)){
            $line=fgetcsv($fp,0,";");
            if($line===null || $line===false)break;
            
            if($first){$first=false;continue;}
            
            if(count($line)<2 || strlen($line[1])==0 || strlen($line[0])==0)continue;
            
            $feats=trim($line[2]);
            
            $MSDUDFEATS[trim($line[0])]=$feats;
            
        }
        fclose($fp);
        
    }

    if(isset($MSDUDFEATS[$msd]))return $MSDUDFEATS[$msd];
    
    return "_";    
    
}


?>