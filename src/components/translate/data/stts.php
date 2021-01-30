<?php

$options=["text","lang"];

$data=[];
foreach($options as $o){
    if(isset($_REQUEST[$o]))$data[$o]=trim($_REQUEST[$o]);
}


if(!isset($data['text']) || !isset($data['lang']) || strlen($data['text'])<2)
  	die("Invalid usage");

if($data['lang']=="en"){
		$data=file_get_contents("http://127.0.0.1:7011/api/tts?text=".urlencode($data['text']));

        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Fri, 06 Nov 1987 12:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Type: audio/x-wav', false);
        header('Content-Disposition: attachment; filename="synthesis_' . gmdate('YMdHis') . '.wav";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($data));
        echo $data;
						
		die();
}else{
		// Romanian
		
		$data=ROMANIANTTS_runTTS($data['text']);
		$data=file_get_contents(trim($data));
		
        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Fri, 06 Nov 1987 12:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Type: audio/mpeg', false);
        header('Content-Disposition: attachment; filename="synthesis_' . gmdate('YMdHis') . '.mp3";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($data));
        echo $data;
						
		die();
}
?>