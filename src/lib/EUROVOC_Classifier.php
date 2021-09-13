<?php

define("EUROVOC_TYPE_FASTTEXT",1);
define("EUROVOC_TYPE_PYEUROVOC",2);

function EUROVOC_Classify($contentIn,$num,$threshold,$process=false,$debug=false, $type=EUROVOC_TYPE_FASTTEXT){
   global $EUROVOC_Classifier_Server_URL, $EUROVOC_Classifier_Server_URLS;
   
   if($type===EUROVOC_TYPE_PYEUROVOC){
   
        $data=PYEUROVOC_Predict($contentIn);
        $data=json_decode($data,true);
        $ret=array_slice($data["id_labels"],0,$num);
   
   }else{
        if($process===false){
            $url="${EUROVOC_Classifier_Server_URL}/classifier_predict";
        }else{
            if($process>=count($EUROVOC_Classifier_Server_URLS))$process=0;
            $url=$EUROVOC_Classifier_Server_URLS[$process]."/classifier_predict";
        }
       
    		$text=cleanupTextForServerFastText($contentIn);
    
    		$ret=callFastTextClassifier($text,$url,$num,$threshold,true);
   }   
   return $ret;
}

?>