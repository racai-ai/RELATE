<?php

$LIB_PATH=dirname(__FILE__);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("Europe/Bucharest");

setlocale(LC_CTYPE,"ro_RO.UTF-8");
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//require_once "${LIB_PATH}/vendor/autoload.php";

require_once "${LIB_PATH}/mb_additionals.php";
require_once "${LIB_PATH}/string_utils.php";
require_once "${LIB_PATH}/header_utils.php";
require_once "${LIB_PATH}/msd2upos.php";
require_once "${LIB_PATH}/msd2ctag.php";
require_once "${LIB_PATH}/msd2feats.php";
require_once "${LIB_PATH}/string.format.number.php";
require_once "${LIB_PATH}/wav.php";
require_once "${LIB_PATH}/files.php";
require_once "${LIB_PATH}/modules.php";
require_once "${LIB_PATH}/components.php";
require_once "${LIB_PATH}/runner.php";
require_once "${LIB_PATH}/brat_ann.php";
require_once "${LIB_PATH}/brat2conllu.php";

require_once "${LIB_PATH}/conllup.php";
require_once "${LIB_PATH}/ConllupSentence.php";
require_once "${LIB_PATH}/ConllupSentenceIterator.php";
require_once "${LIB_PATH}/ConllupToken.php";
require_once "${LIB_PATH}/ConllupTokenIterator.php";

require_once "${LIB_PATH}/ServerFastText.php";
require_once "${LIB_PATH}/eurovoc.php";
require_once "${LIB_PATH}/pyeurovoc.php";
require_once "${LIB_PATH}/EUROVOC_Classifier.php";

require_once "${LIB_PATH}/config.php";

require_once "${LIB_PATH}/settings.php";
require_once "${LIB_PATH}/user.php";
require_once "${LIB_PATH}/users.php";
require_once "${LIB_PATH}/locking.php";

require_once "${LIB_PATH}/teprolin.php";
require_once "${LIB_PATH}/rown.php";
require_once "${LIB_PATH}/handlers.php";
require_once "${LIB_PATH}/theme_utils.php";
require_once "${LIB_PATH}/tilde.php";
require_once "${LIB_PATH}/robin.php";
require_once "${LIB_PATH}/romaniantts.php";
require_once "${LIB_PATH}/tts_ssla.php";
require_once "${LIB_PATH}/sentencesplit.php";
require_once "${LIB_PATH}/udpipe.php";
require_once "${LIB_PATH}/asr_moz_deepspeech.php";

require_once "${LIB_PATH}/corpus.php";
require_once "${LIB_PATH}/corpora.php";
require_once "${LIB_PATH}/task.php";

require_once "${LIB_PATH}/IATE_EUROVOC_Client.php";
require_once "${LIB_PATH}/anonymization.php";
require_once "${LIB_PATH}/ttlchunker.php";
require_once "${LIB_PATH}/ner_legalner.php";
require_once "${LIB_PATH}/punctuation.php";

require_once "${LIB_PATH}/pdf2text.php";

require_once "${LIB_PATH}/education.php";
require_once "${LIB_PATH}/educationresource.php";

?>