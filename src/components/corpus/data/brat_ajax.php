<?php

$a=$_REQUEST['action'];

header('Content-Type: application/json');

if($a=="getCollectionInformation"){
//$_REQUEST['collection']
echo <<<EOT
{"protocol": 1, "description": null, "parent": "_2002", "disambiguator_config": [], "header": [["Document", "string"], ["Modified", "time"], ["Entities", "int"], ["Relations", "int"], ["Events", "int"]], "entity_attribute_types": [], "event_types": [], "ui_names": {"entities": "entities", "events": "events", "relations": "relations", "attributes": "attributes"}, "action": "getCollectionInformation", "normalization_config": [], "items": [["c", null, ".."], ["d", null, "ned.train-doc-123", 1588771383.6057746, 38, 0, 0], ["d", null, "ned.train-doc-46", 1544454293.5455127, 34, 0, 0], ["d", null, "123", 1599210842.9890997, 3, 0, 0], ["d", null, "ned.train-doc-251", 1570642700.4768713, 24, 0, 0], ["d", null, "ned.train-doc-118", 1590637143.2979867, 47, 0, 0], ["d", null, "ned.train-doc-75", 1558703339.1213546, 17, 0, 0], ["d", null, "ned.train-doc-181", 1596835940.9163423, 106, 0, 0], ["d", null, "www", 1590637779.57792, 1, 0, 0], ["d", null, "longs", 1461109792.1669352, 2, 0, 0], ["d", null, "ned.train-doc-134", 1579417408.0313458, 31, 0, 0], ["d", null, "ned.train-doc-236", 1521605676.9869406, 27, 0, 0], ["d", null, "ned.train-doc-27", 1452371778.0, 23, 0, 0], ["d", null, "ned.train-doc-184", 1352355357.0, 29, 0, 0]], "unconfigured_types": [{"borderColor": "darken", "name": "SPAN_DEFAULT", "labels": null, "unused": true, "bgColor": "lightgreen", "type": "SPAN_DEFAULT", "fgColor": "black"}], "messages": [], "event_attribute_types": [], "annotation_logging": false, "search_config": [], "ner_taggers": [], "relation_types": [], "entity_types": [{"borderColor": "darken", "normalizations": [], "name": "ORG", "labels": null, "children": [], "unused": false, "bgColor": "#8fb2ff", "attributes": [], "type": "ORG", "fgColor": "black"}, {"borderColor": "darken", "normalizations": [], "name": "PER", "labels": null, "children": [], "unused": false, "bgColor": "#ffccaa", "attributes": [], "type": "PER", "fgColor": "black"}, {"borderColor": "darken", "normalizations": [], "name": "LOC", "labels": null, "children": [], "unused": false, "bgColor": "lightgreen", "attributes": [], "type": "LOC", "fgColor": "black"}, {"borderColor": "darken", "normalizations": [], "name": "MISC", "labels": null, "children": [], "unused": false, "bgColor": "#f1f447", "attributes": [], "type": "MISC", "fgColor": "black"}], "relation_attribute_types": []}
EOT;
die();
}


if($a=="whoami"){
    echo '{"action": "whoami", "messages": [], "protocol": 1, "user": "'.$user->getProfileJS("name",$user->getUsername()).'"}';
    die();
}

if($a=="loadConf"){
    echo '{"action": "loadConf", "messages": [], "protocol": 1}';
    die();
}

function loadData(){
        $cname=trim($_REQUEST['collection'],'/');
        $fname=$_REQUEST['document'];
        $corpora=new Corpora();
        $corpus=new Corpus($corpora,$cname);
        if(!$corpus->loadData())die("Invalid corpus");
        $meta=$corpus->getFileMeta($fname);
        if($meta===false)die("Invalid file");
        $dir=$corpus->getFolderPath();
        $dir.="/files";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
        
        $text=file_get_contents($fpath);
        
        $dir=$corpus->getFolderPath()."/standoff/";
        @mkdir($dir);
        $dir.=$fname;
        $ann=new BratAnn(changeFileExtension($dir,"ann"));
        $ann->load();
        
        return ["text"=>$text,"corpus"=>$corpus,"ann"=>$ann];        
}

function getAnnotation($cdata){
        $corpus=$cdata['corpus'];
        $text=$cdata['text'];
        $ann=$cdata['ann'];
        //$sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $text);
        //$text=str_replace("\r","",$text);
        //$text=trim($text);
        
        $s_offsets=[];
        $last=0;
        /*foreach($sentences as $sent){
            $len=mb_strlen($sent);
            $s_offsets[]=[$last,$last+$len-1];
            $last=$last+$len;
        } */
        $isTok=true;
        for($i=0;$i<mb_strlen($text);$i++){
            $c=mb_substr($text,$i,1);
            if($c=="\n"){
                if($isTok && $i>0)$s_offsets[]=[$last,$i];
                $isTok=false;
            }else{
                if(!$isTok){$last=$i;$isTok=true;}
            }
        }
        $s_offsets[]=[$last,$i-1];
        
        $t_offsets=[];
        $last_t=0;
        $isTok=true;
        for($i=0;$i<mb_strlen($text);$i++){
            $c=mb_substr($text,$i,1);
            if($c==' ' || $c=="\n" || $c=="\t" || $c=="\r"){
                if($isTok && $i>0)$t_offsets[]=[$last_t,$i];
                $isTok=false;
            }else{
                if(!$isTok){$last_t=$i;$isTok=true;}
            }
        }
        $t_offsets[]=[$last_t,$i-1];
                
        return [
            "modifications"=> [], 
            "normalizations"=> [], 
            "ctime" => 1590637143.2979867, 
            "triggers"=> [], 
            "text" => $text,
            "source_files"=> ["ann", "txt"], 
            "mtime"=>1590637143.2979867, 
            "messages" =>[], 
            "sentence_offsets"=>$s_offsets, 
            "relations"=> [], 
            "entities"=> $ann->getForBrat(), // [["T1", "PER", [[65, 83]]], ["T2", "MISC", [[84, 92]]]],
            "comments" => [], //[["T1", "AnnotatorNotes", "asdfasd"]],
            "token_offsets"=> $t_offsets, //[],//[[0, 3], [4, 5], [6, 43], [44, 46], [47, 64], [65, 72], [73, 75], [76, 83], [84, 86], [87, 92], [93, 102], [103, 104], [105, 106], [107, 118], [119, 126], [127, 129], [130, 143], [144, 148], [149, 157], [158, 162], [163, 165], [166, 181], [182, 186], [187, 192], [193, 199], [200, 206], [207, 215], [216, 222], [223, 226], [227, 233], [234, 242], [243, 251], [252, 256], [257, 265], [266, 269], [270, 288], [289, 291], [292, 312], [313, 318], [319, 327], [328, 329], [330, 338], [339, 344], [345, 346], [347, 357], [358, 365], [366, 370], [371, 373], [374, 379], [380, 384], [385, 387], [388, 391], [392, 396], [397, 406], [407, 414], [415, 418], [419, 424], [425, 440], [441, 444], [445, 450], [451, 453], [454, 457], [458, 463], [464, 467], [468, 482], [483, 489], [490, 496], [497, 500], [501, 512], [513, 526], [527, 533], [534, 540], [541, 544], [545, 549], [550, 560], [561, 565], [566, 578], [579, 581], [582, 584], [585, 595], [596, 605], [606, 613], [614, 618], [619, 624], [625, 634], [635, 638], [639, 649], [650, 654], [655, 657], [658, 663], [664, 668], [669, 671], [672, 675], [676, 680], [681, 690], [691, 698], [699, 702], [703, 708], [709, 724], [725, 728], [729, 734], [735, 737], [738, 741], [742, 746], [747, 753], [754, 757], [758, 762], [763, 772], [773, 774], [775, 779], [780, 784], [785, 791], [792, 796], [797, 798], [799, 800], [801, 803], [804, 815], [816, 825], [826, 830], [831, 833], [834, 841], [842, 849], [850, 852], [853, 856], [857, 861], [862, 873], [874, 876], [877, 883], [884, 899], [900, 906], [907, 909], [910, 917], [918, 924], [925, 928], [929, 933], [934, 938], [939, 943], [944, 952], [953, 966], [967, 971], [972, 976], [977, 986], [987, 998], [999, 1000], [1001, 1011], [1012, 1021], [1022, 1023], [1024, 1025], [1026, 1030], [1031, 1037], [1038, 1044], [1045, 1048], [1049, 1051], [1052, 1057], [1058, 1061], [1062, 1071], [1072, 1082], [1083, 1084], [1085, 1088], [1089, 1091], [1092, 1095], [1096, 1105], [1106, 1107], [1108, 1112], [1113, 1114], [1115, 1120], [1121, 1128], [1129, 1139], [1140, 1145], [1146, 1147], [1148, 1156], [1157, 1160], [1161, 1175], [1176, 1185], [1186, 1189], [1190, 1200], [1201, 1212], [1213, 1221], [1222, 1227], [1228, 1236], [1237, 1250], [1251, 1259], [1260, 1269], [1270, 1272], [1273, 1286], [1287, 1294], [1295, 1296], [1297, 1301], [1302, 1307], [1308, 1309], [1310, 1331], [1332, 1333], [1334, 1340], [1341, 1347], [1348, 1350], [1351, 1355], [1356, 1360], [1361, 1365], [1366, 1374], [1375, 1379], [1380, 1392], [1393, 1407], [1408, 1409], [1410, 1416], [1417, 1420], [1421, 1423], [1424, 1428], [1429, 1431], [1432, 1434], [1435, 1439], [1440, 1446], [1447, 1450], [1451, 1459], [1460, 1466], [1467, 1473], [1474, 1477], [1478, 1480], [1481, 1485], [1486, 1490], [1491, 1495], [1496, 1500], [1501, 1507], [1508, 1511], [1512, 1520], [1521, 1525], [1526, 1533], [1534, 1538]], 
            "action"=>"getDocument", 
            "attributes"=> [], 
            "equivs"=> [], 
            "events"=> [], 
        ];

}

if($a=="getDocument"){
//$_REQUEST['collection']
//$_REQUEST['document']
/*echo <<<EOT
{"modifications": [], "normalizations": [], "ctime": 1595443510.6016016, "triggers": [["T4", "Marry", [[21, 30]]], ["T2", "Be-born", [[18, 20]]], ["T7", "Transfer-money", [[14, 20]]], ["T11", "Life", [[18, 20]]], ["T12", "Be-born", [[10, 20]]], ["T13", "Transfer-ownership", [[14, 20]]]], "text": "test00001\\nMy name is test00001.\\n", "source_files": ["ann", "txt"], "mtime": 1595443510.6016016, "messages": [], "sentence_offsets": [[0, 9], [10, 31]], "relations": [["R1", "Employment", [["Arg1", "T3"], ["Arg2", "T5"]]]], "entities": [["T1", "GPE", [[10, 12]]], ["T3", "Person", [[13, 17]]], ["T5", "GPE", [[21, 30]]], ["T6", "GPE", [[13, 20]]], ["T9", "GPE", [[11, 14]]], ["T8", "Person", [[14, 17]]], ["T10", "Organization", [[21, 30]]]], "comments": [["E1", "AnnotatorNotes", "Prueba de Bryan"], ["T10", "AnnotatorNotes", "Bryan"], ["T1", "AnnotationError", "Error: annotation cannot have crossing span with T9"], ["T3", "AnnotationError", "Error: Person cannot be contained in Geo-political entity (T6)"], ["T3", "AnnotationError", "Error: annotation cannot have crossing span with T9"], ["T3", "AnnotationError", "Error: Person cannot contain Person (T8)"], ["T5", "AnnotationError", "Error: Geo-political entity cannot have identical span with Organization T10"], ["T6", "AnnotationError", "Error: Geo-political entity cannot contain Person (T3)"], ["T6", "AnnotationError", "Error: annotation cannot have crossing span with T9"], ["T6", "AnnotationError", "Error: Geo-political entity cannot contain Person (T8)"], ["T9", "AnnotationError", "Error: annotation cannot have crossing span with T1"], ["T9", "AnnotationError", "Error: annotation cannot have crossing span with T3"], ["T9", "AnnotationError", "Error: annotation cannot have crossing span with T6"], ["T8", "AnnotationError", "Error: Person cannot be contained in Person (T3)"], ["T8", "AnnotationError", "Error: Person cannot be contained in Geo-political entity (T6)"], ["T10", "AnnotationError", "Error: Organization cannot have identical span with Geo-political entity T5"], ["E1", "AnnotationIncomplete", "Incomplete: exactly 2 Person arguments required for event"], ["E2", "AnnotationIncomplete", "Incomplete: exactly one Person argument required for event"], ["E3", "AnnotationIncomplete", "Incomplete: exactly one Beneficiary argument required for event"], ["E5", "AnnotationIncomplete", "Incomplete: exactly one Person argument required for event"], ["E6", "AnnotationIncomplete", "Incomplete: exactly one Buyer argument required for event"], ["E6", "AnnotationIncomplete", "Incomplete: exactly one Seller argument required for event"], ["E6", "AnnotationIncomplete", "Incomplete: exactly one Artifact argument required for event"]], "token_offsets": [[0, 9], [10, 12], [13, 17], [18, 20], [21, 31]], "action": "getDocument", "attributes": [["A1", "Confidence", "E4", "High"]], "equivs": [], "events": [["E1", "T4", []], ["E2", "T2", []], ["E3", "T7", [["Recipient-Arg", "T5"], ["Giver-Arg", "T5"]]], ["E4", "T11", []], ["E5", "T12", []], ["E6", "T13", []]], "protocol": 1}
EOT;*/
        $ann=getAnnotation(loadData());
        $ann["protocol"]=1;
        echo json_encode($ann);
    die();
}

if($a=="deleteSpan"){
//collection
//document
//id
//type
//offsets
    $cdata=loadData();
    $cdata['ann']->deleteById($_REQUEST['id']);
    $cdata['ann']->save();
    
    echo json_encode([
        "action" => "deleteSpan", 
        "edited" => [], 
        "messages" => [], 
        "protocol" => 1, 
        "annotations" => getAnnotation($cdata)
    ]);
die();
}

if($a=="createSpan"){
//collection
//document
//type
//offsets => [[107,118]]
//comment
//attributes
//normalizations
    $cdata=loadData();
    $newid=$cdata['ann']->addAnnotation($_REQUEST['type'],json_decode($_REQUEST['offsets']),$cdata['text']); 
    $cdata['ann']->save();
    echo json_encode([
        "edited"=> [[$newid]], 
        "protocol" => 1, 
        "messages" => [], 
        "undo"=> "{\"action\": \"add_tb\", \"attributes\": \"{}\", \"normalizations\": \"[]\", \"id\": \"$newid\"}", 
        "action"=> "createSpan", 
        "annotations"=>getAnnotation($cdata)
    ]);
die();

}

/*if($a=="login"){

echo <<<EOT
{"action": "login", "messages": [["Hello!", "comment", 3]], "protocol": 1}
EOT;
die();
} */

?>