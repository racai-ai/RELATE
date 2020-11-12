<?php

function ROWN_call($word,$sid,$port=8012){
	//if($port==8012)
	return file_get_contents("http://corolaws.racai.ro:${port}/rown?text=".urlencode($word)."&sid=$sid");

    /*$ch = curl_init();
    
    $data="------WebKitFormBoundary6XfRB2HxKQdC87hB\r\nContent-Disposition: form-data; name=\"text\"\r\n\r\n{{data_text}}\r\n------WebKitFormBoundary6XfRB2HxKQdC87hB\r\nContent-Disposition: form-data; name=\"sid\"\r\n\r\n{{data_sid}}\r\n------WebKitFormBoundary6XfRB2HxKQdC87hB--\r\n";    
    $data=str_replace("{{data_text}}",$word,$data);    
    $data=str_replace("{{data_sid}}",$sid,$data);    
    
    curl_setopt($ch, CURLOPT_URL,"http://corolaws.racai.ro:${port}/rown");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary6XfRB2HxKQdC87hB',
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    return $server_output;
      */
}

?>