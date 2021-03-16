<?php

namespace Modules\change_terms_marcell;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    global $LIB_PATH;
    
	$cwd=getcwd();
    @chdir("${LIB_PATH}/../modules/change_terms_marcell/marcell");
    
    $pathIn="tmp".$runner->getRunnerId().".conllup";
    file_put_contents($pathIn,$contentIn);
    
    $cmd="./marcell_change_terms.sh ".$settings->get("tools.python.venv")." ".escapeshellarg($pathIn)." ".
        escapeshellarg($corpus->getFolderPath()."/".$settings->get("dir.annotated")."/".$fnameOut);
    echo "RUNNING [$cmd]\n";
    passthru($cmd);

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
        
    @chdir($cwd);

}

?>