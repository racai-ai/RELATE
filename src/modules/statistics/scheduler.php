<?php

namespace Modules\statistics;

function schedule($settings,$corpus,$task_name,$tdata){
      createCorpusFolder($corpus,"statistics");
      clearCorpusFolder($corpus,"statistics");
            
      storeCorpusFile($corpus,"changed_statistics.json",json_encode(["changed"=>time()]));            
        
      scheduleFilesFolder($corpus,$task_name);
      scheduleFolder($corpus,$settings->get("dir.annotated")."/",$task_name,"conllu");
}

?>