<?php

namespace Modules\rescan_folders;

function schedule($settings,$corpus,$task_name,$tdata){
    $filesFolder=$corpus->getFolderPath(false)."/".$settings->get("dir.files","files");
    $metaFolder=$corpus->getFolderPath(false)."/".$settings->get("dir.meta","meta");
    if(!is_dir($filesFolder)){
        echo "No files folder found [$filesFolder]\n";
    }else{
        $dh = opendir($filesFolder);
        if($dh===false){
            echo "Cannot open files folder [$filesFolder]\n";
        }else{
            while (($file = readdir($dh)) !== false) {
                $dpath="$filesFolder/$file";
                $dpath_meta="${metaFolder}/${file}.meta";
                if(!is_file($dpath))continue;
                if(!is_file($dpath_meta)){
                    scheduleFile($corpus, "files/$file", $task_name, "text");
                }
            }
            closedir($dh);
            
        }
        
    }
    
    file_put_contents($corpus->getFolderPath(false)."/changed_files.json",json_encode(["changed"=>time()]));
    file_put_contents($corpus->getFolderPath(false)."/changed_annotated.json",json_encode(["changed"=>time()]));
    file_put_contents($corpus->getFolderPath(false)."/changed_standoff.json",json_encode(["changed"=>time()]));
    file_put_contents($corpus->getFolderPath(false)."/changed_gold_ann.json",json_encode(["changed"=>time()]));
    file_put_contents($corpus->getFolderPath(false)."/changed_gold_standoff.json",json_encode(["changed"=>time()]));
    
}

?>