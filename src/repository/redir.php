<?php

if(!isset($_REQUEST['res']))die();

header('Location: ../index.php?path=repository/resource&resource='.$_REQUEST['res'],true,307);
die();
