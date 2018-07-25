<?php
    /* Awstats Extractor */
    /* Pull info from AWstats files into CSVs */
    /* Command line or web app */

    include_once("core.php");

    /* Get file from querystring - could be used to pull in from form submission */
    $file = $_GET['file'];

    extractor($file);
?>