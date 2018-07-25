<?php
    /* Core extractor function */
    function extractor($file){
        //$lines = file($file, FILE_IGNORE_NEW_LINES);
        $handle = fopen($file, "r");
        $current_key = "";

        if($handle){
            /* Check each line */
            while(($line = fgets($handle)) !== false){
                /* Is there a current key? */
                /* If yes, check if we're at the end of the chunk */
                echo $line;
                $linecheck = explode("_",$line);
                if($current_key != ""){
                    if ($linecheck[0] == "END"){                        
                        $current_key = "";
                    } else {
                        /* If not, write the line */
                        // Open the CSV for writing
                        $filename = "outputs/".$current_key."_".$file.".csv";
                        $fp = fopen($filename, "a");
                        $data = explode(" ",$line);
                        fputcsv($fp,$data);
                        fclose($fp);
                    }
                }
                
                /* Check if this is the beginning of a chunk */
                if($linecheck[0] == "BEGIN"){
                    $current_key = str_replace(" ","_",$linecheck[1]);
                    // Need to remove carriage returns!
                    $current_key = str_replace(array("\r", "\n"), '', $current_key);            
                }
            }

            return("File successfully processed...");
        } else {
            return("Could not open file...");
        }            
    }
?>