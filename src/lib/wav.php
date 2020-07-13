<?php

function WAV_getMetadata($file) {
  $fp = fopen($file, 'r');
  if (fread($fp,4) == "RIFF") {
    fseek($fp, 20);
    $rawheader = fread($fp, 16);
    if($rawheader===false){fclose($fp);return false;}
    
    $header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);
    
    $pos = ftell($fp);
    while (fread($fp,4) != "data" && !feof($fp)) {
      $pos++;
      fseek($fp,$pos);
    }
    $rawheader = fread($fp, 4);
    fclose($fp);
    
    if($rawheader===false)return false;
    
    $data = unpack('Vdatasize',$rawheader);
    $sec = $data['datasize']/$header['bytespersec'];
    
    return [
        "type"=>$header['type'],
        "channels"=>$header['channels'],
        "samplerate"=>$header['samplerate'],
        "bytesperse"=>$header['bytespersec'],
        "alignment"=>$header['alignment'],
        "bits"=>$header['bits'],
        "datasize"=>$data['datasize'],
        "duration"=>$sec
    ];
    
    /*$minutes = intval(($sec / 60) % 60);
    $seconds = intval($sec % 60);
    return str_pad($minutes,2,"0", STR_PAD_LEFT).":".str_pad($seconds,2,"0", STR_PAD_LEFT);*/
  }
  
  return false;
}

function WAV_getDuration($file){
    $meta=WAV_getMetadata($file);
    if($meta===false)return false;
    return $meta['duration'];
}