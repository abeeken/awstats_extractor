<?php
    include_once("core.php");

    if(isset($argv[1])){
        $file = $argv[1];
    } else {
        echo "Incorrect number of arguments passed...";
        exit;
    }

    extractor($file);
?>