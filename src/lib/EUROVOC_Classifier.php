<?php

function EUROVOC_Classify($contentIn,$num,$threshold,$process=false,$debug=false){
   global $EUROVOC_Classifier_Server_URL, $EUROVOC_Classifier_Server_URLS;
   
    if($process===false){
        $url="${EUROVOC_Classifier_Server_URL}/classifier_predict";
    }else{
        if($process>=count($EUROVOC_Classifier_Server_URLS))$process=0;
        $url=$EUROVOC_Classifier_Server_URLS[$process]."/classifier_predict";
    }
   
		$text=cleanupTextForServerFastText($contentIn);

		$ret=callFastTextClassifier($text,$url,$num,$threshold,true);
   
   return $ret;
}

?>