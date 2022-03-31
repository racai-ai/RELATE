<?php

$msd_ctag = null;

function MSD2CTAG($msd){
    global $msd_ctag;
    if($msd_ctag===null){
		global $LIB_PATH;
        $msd_ctag=[];
		foreach(explode("\n",file_get_contents($LIB_PATH."/msdtag.ro.map")) as $line){
			$line=trim($line);
			$d=explode("\t",$line);
			if(count($d)!==2)continue;
			$msd_ctag[$d[0]]=$d[1];
		}
    }
    
	if(!isset($msd_ctag[$msd]))return $msd;
	return $msd_ctag[$msd];
    
}
