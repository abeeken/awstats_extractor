<?php
    /* Core extractor function */
    // Additional functionality:
    //  * Column headers - not entirely sure how this is going to work given that we're sequentially reading the files...

    function extractor($file, $date = false){
        //$lines = file($file, FILE_IGNORE_NEW_LINES);
        $handle = fopen($file, "r");
        $current_key = "";

        if($handle){
            /* Check each line */
            while(($line = fgets($handle)) !== false){
                /* Is there a current key? */
                /* If yes, check if we're at the end of the chunk */
                $linecheck = explode("_",$line);
                if($current_key != ""){
                    if ($linecheck[0] == "END"){                        
                        $current_key = "";
                    } else {
                        /* If not, write the line */
                        // Open the CSV for writing
                        // We could possibly have / characters in the filename so we'll need to strip those out                        
                        $filename = "outputs/".$current_key."_".str_replace("/","_",$file).".csv";
                        
                        $data = explode(" ",$line);

                        // There might be carriage returns!
                        foreach($data as $key => $value)
                        {
                            $data[$key] = str_replace(array("\r", "\n"), '', $value);
                        }

                        // Do we need to generate a date?
                        if($date){
                            // We need to split the filename on .                            
                            $filesplit = explode(".",$file);
                            // Date will be on the end of the first element in filesplit, the last 6 characters
                            // As the date is MM/YYYY, we'll add a 01 to the front to give us DD/MM/YYYY
                            $the_date = substr($filesplit[0], -6);
                            $the_date = "01/".substr($the_date, 0, 2)."/".substr($the_date, -4);
                            // Add it to the data to write
                            $data[] = $the_date;
                        }

                        $fp = fopen($filename, "a");
                        fputcsv($fp,$data);
                        fclose($fp);
                    }
                }
                
                /* Check if this is the beginning of a chunk */
                if($linecheck[0] == "BEGIN"){
                    $current_key = explode(" ",$linecheck[1])[0];
                    // Need might to remove carriage returns!
                    $current_key = str_replace(array("\r", "\n"), '', $current_key);
                }
            }

            return("File successfully processed...");
        } else {
            return("Could not open file...");
        }            
    }
?>