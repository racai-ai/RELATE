<?php
session_start();

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
require_once "${LIB_PATH}/msd2upos.php";
require_once "${LIB_PATH}/msd2feats.php";
require_once "${LIB_PATH}/string.format.number.php";
require_once "${LIB_PATH}/wav.php";
require_once "${LIB_PATH}/files.php";

require_once "${LIB_PATH}/config.php";

require_once "${LIB_PATH}/settings.php";
require_once "${LIB_PATH}/user.php";
require_once "${LIB_PATH}/locking.php";

require_once "${LIB_PATH}/teprolin.php";
require_once "${LIB_PATH}/rown.php";
require_once "${LIB_PATH}/handlers.php";
require_once "${LIB_PATH}/theme_utils.php";
require_once "${LIB_PATH}/tilde.php";
require_once "${LIB_PATH}/robin.php";

require_once "${LIB_PATH}/corpus.php";
require_once "${LIB_PATH}/corpora.php";
require_once "${LIB_PATH}/task.php";

require_once "${LIB_PATH}/IATE_EUROVOC_Client.php";

?>