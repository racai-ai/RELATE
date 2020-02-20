<?php

require "lib/lib.php";

require_once "securimage/securimage.php";

registerHandler("teprolinws","data/teprolin/call.php",true);
registerHandler("teprolin/complete","pages/teprolin/complete.php",false);
registerHandler("teprolin/custom","pages/teprolin/custom.php",false);
registerHandler("teprolin/doc_dev","pages/teprolin/doc_dev.php",false);
registerHandler("teprolin/stats","pages/teprolin/stats.php",false);

registerHandler("corola/about","pages/corola/about.php",false);
registerHandler("corola/korap","pages/corola/korap.php",false);
registerHandler("corola/nlpcqp","pages/corola/nlpcqp.php",false);
registerHandler("corola/ocqp","pages/corola/ocqp.php",false);
registerHandler("corola/stats","pages/corola/stats.php",false);
registerHandler("corola/we","pages/corola/we.php",false);

registerHandler("ssla/synthesize","pages/teprolin/synthesize.html",true);

registerHandler("rownws","data/rown/rown.php",true);
registerHandler("rown/rown","pages/rown/rown.php",true);
registerHandler("rown/query","pages/rown/query.php",false);
registerHandler("rown/queryp","pages/rown/queryp.php",false);

registerHandler("translate/ro_en","pages/translate/ro_en.php",false);
registerHandler("translate/en_ro","pages/translate/en_ro.php",false);
registerHandler("translate/dev_doc","pages/translate/dev_doc.php",false);
registerHandler("translatews","data/translate/call.php",true);

registerHandler("robin/asr","pages/robin/asr.php",false);
registerHandler("robinasrws","data/robin/asr.php",true);
registerHandler("robin/tts","pages/robin/tts.php",false);
registerHandler("robinttsws","data/robin/tts.php",true);

registerHandler("platform/login","pages/platform/login.php",false);
registerHandler("platform/register","pages/platform/register.php",false);
registerHandler("platform/logout","pages/platform/logout.php",false);
registerHandler("platform/change_password","pages/platform/change_password.php",false);

registerHandler("downloads/private","pages/downloads/private.php",false,["user"]);
registerHandler("downloads/data","pages/downloads/data.php",true);

registerHandler("corpus/list","pages/corpus/list.php",false,["corpus"]);
registerHandler("corpus/list_get","pages/corpus/list_get.php",true,["corpus"]);
registerHandler("corpus/list_add","pages/corpus/list_add.php",true,["corpus"]);
registerHandler("corpus/corpus","pages/corpus/corpus.php",false,["corpus"]);
registerHandler("corpus/files_get","pages/corpus/files_get.php",true,["corpus"]);
registerHandler("corpus/files_getbasictagging","pages/corpus/files_getbasictagging.php",true,["corpus"]);
registerHandler("corpus/stats_get","pages/corpus/stats_get.php",true,["corpus"]);
registerHandler("corpus/files_add","pages/corpus/files_add.php",true,["corpus"]);
registerHandler("corpus/csv_view","pages/corpus/csv_view.php",false,["corpus"]);
registerHandler("corpus/file_view","pages/corpus/file_view.php",false,["corpus"]);
registerHandler("corpus/csv_get","pages/corpus/csv_get.php",true,["corpus"]);
registerHandler("corpus/file_getdownload","pages/corpus/file_getdownload.php",true,["corpus"]);
registerHandler("corpus/task_getallbycorpus","pages/corpus/task_getallbycorpus.php",true,["corpus"]);
registerHandler("corpus/task_add","pages/corpus/task_add.php",true,["corpus"]);
registerHandler("corpus/archives_get","pages/corpus/archives_get.php",true,["corpus"]);
registerHandler("papers/papers","pages/papers/papers.php",false);

registerHandler("my/authors","pages/my/authors.php",false,["pvf"]);

$PLATFORM=[
  "path"=>"teprolin/complete",   // this is the main page
  "menu"=>[
      [
        "label"=>"TEPROLIN Service",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"Complete Flow", "path"=>"teprolin/complete"],
            ["label"=>"Custom Flow", "path"=>"teprolin/custom"],
            ["label"=>"Operations & Statistics", "path"=>"teprolin/stats"],
            ["label"=>"Developer Documentation", "path"=>"teprolin/doc_dev", "forceHTTP"=>true],
        ]
      ],

      [
        "label"=>"CoRoLa",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"About", "path"=>"corola/about", "forceHTTP"=>true],
            ["label"=>"Korap Search", "path"=>"corola/korap", "forceHTTP"=>true],
            ["label"=>"NL Search", "path"=>"corola/nlpcqp", "forceHTTP"=>true],
            ["label"=>"Audio Search", "path"=>"corola/ocqp", "forceHTTP"=>true],
            ["label"=>"Word Embeddings", "path"=>"corola/we", "forceHTTP"=>true],
            ["label"=>"Statistics", "path"=>"corola/stats", "forceHTTP"=>true],
        ]
      ],

      [
        "label"=>"RoWordNet",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"Query", "path"=>"rown/query"],
            ["label"=>"Aligned Query", "path"=>"rown/queryp"],
        ]
      ],      

      [
        "label"=>"Machine Translation",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"RO - EN", "path"=>"translate/ro_en"],
            ["label"=>"EN - RO", "path"=>"translate/en_ro"],
            ["label"=>"Developer Documentation", "path"=>"translate/dev_doc"],
        ]
      ],
      
      [
        "label"=>"ROBIN ASR",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"ASR", "path"=>"robin/asr"],
        ]
      ],

      [
        "label"=>"ROBIN TTS",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"TTS", "path"=>"robin/tts"],
        ]
      ],

      [
        "label"=>"Downloads",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "rights"=>["user"],
        "menu"=>[
            ["label"=>"Internal", "path"=>"downloads/private","rights"=>["user"]],
        ]
      ],

      [
        "label"=>"Corpora",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "rights"=>["corpus"],
        "menu"=>[
            ["label"=>"List", "path"=>"corpus/list","rights"=>["corpus"]],
        ]
      ],
      
      [
        "label"=>"Citation",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "path"=>"papers/papers",
        "menu"=>[
            ["label"=>"Papers", "path"=>"papers/papers"],
        ]
      ],

      [
        "label"=>"MY",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "rights"=>["pvf"],
        "menu"=>[
            ["label"=>"Authors Format", "path"=>"my/authors","rights"=>["pvf"]],
        ]
      ],

  ]    
];

$settings=new Settings();
$settings->load();

$user=new User();
$user->initFromSession();

if(isset($_REQUEST['path']) && isset($HANDLERS[$_REQUEST['path']]) && $user->hasAccess($HANDLERS[$_REQUEST['path']]['rights']))
    $PLATFORM['path']=$_REQUEST['path'];

require $HANDLERS[$PLATFORM['path']]['fname'];

if(function_exists("PageInit"))PageInit();

session_write_close();
ob_end_flush();

if($HANDLERS[$PLATFORM['path']]['isData'])die();

require "template.php";

?>