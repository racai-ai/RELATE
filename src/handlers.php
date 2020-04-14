<?php
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
registerHandler("corpus/files_getstandoff","pages/corpus/files_getstandoff.php",true,["corpus"]);
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


?>