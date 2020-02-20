<?php

$data = file_get_contents('php://input');
echo ROBIN_runTTS($data);

?>