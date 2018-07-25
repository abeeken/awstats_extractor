<?php
    /* Core extractor function */
    function extractor($file){
        $lines = file($file, FILE_IGNORE_NEW_LINES);

        $data = array();

        $current_key = "";

        /* Check each line */    
        foreach($lines as $key => $line){
            /* Is there a current key? */
            /* If yes, check if we're at the end of the chunk */
            $linecheck = explode("_",$line);
            if($current_key != ""){
                if ($linecheck[0] == "END"){
                    $current_key = "";
                } else {
                    $data[$current_key][] = explode(" ",$line);
                }
            }
            /* If not, write the line */
            /* Check if this is the beginning of a chunk */
            if($linecheck[0] == "BEGIN"){
                $current_key = str_replace(" ","_",$linecheck[1]);
            }
        }

        /* Write the files */
        foreach($data as $key => $chunk){
            $filename = "outputs/".$key."_".$file.".csv";
            $fp = fopen($filename, "w");

            foreach($chunk as $line){
                fputcsv($fp, $line);
            }

            fclose($fp);
        }
    }
?>