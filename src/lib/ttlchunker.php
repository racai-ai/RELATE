<?php

$TTLCHUNKER_URL=["http://127.0.0.1:9101/chunker"];
$TTLCHUNKER_DEBUG=false;

function TTLChunker_chunkSentence($msd,$process=false,$debug=false){
	global $TTLCHUNKER_URL, $TTLCHUNKER_DEBUG;
    
    if($process===false || $process>=count($TTLCHUNKER_URL))$process=0;
    $url=$TTLCHUNKER_URL[$process];

    $data=file_get_contents("$url?text=".urlencode(implode("\n",$msd)));
    if($debug || $TTLCHUNKER_DEBUG)var_dump($data);
    $data=json_decode($data,true);    
    
    return $data;

}

?>