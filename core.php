<?php
    /* Core extractor function */
    // Additional functionality:
    //  * Column headers - not entirely sure how this is going to work given that we're sequentially reading the files...
    //      * POSSIBLE SOLUTION:
    //      * Keep a buffer of all # lines in the run up to a BEGIN
    //      * When we hit a begin, check if we need the buffer
    //      * If we do, the pointer in the headers table will say at what position in the buffer the headers are
    //      * At the end of a chunk, clear down the buffer.

    function extractor($file, $date = false){
        //$lines = file($file, FILE_IGNORE_NEW_LINES);
        $handle = fopen($file, "r");
        $current_key = "";
        $buffer = array();

        // Headers array - name => (extra_fields, pos in buffer)
        $headers = array(
            "MISC" => array(false, 0),
            "TIME" => array(false, 0),
            "DOMAIN" => array(false, 0),
            "CLUSTER" => array(false, 0),
            "LOGIN" => array(false, 0),
            "ROBOT" => array(false, 0),
            "DOMAIN" => array(false, 0),
            "WORMS" => array(false, 0),
            "DOMAIN" => array(false, 0),
            "EMAILSENDER" => array(false, 0),
            "EMAILRECEIVER" => array(false, 0),
            "FILETYPES" => array(false, 0),
            "DOWNLOADS" => array("url", 0),
            "SIDER_404" => array(false, 0),
            "SIDER_403" => array(false, 0),
            "SIDER_400" => array(false, 0),
            "VISITOR" => array(false, 0),
            "DAY" => array(false, 0),
            "SESSION" => array(false, 0),
            "DOMAIN" => array(false, 0),
            "SIDER" => array(false, 0),
        );

        if($handle){
            /* Check each line */
            while(($line = fgets($handle)) !== false){
                /* Is there a current key? */
                /* If yes, check if we're at the end of the chunk */
                $linecheck = explode("_",$line,2);
                if($current_key != ""){
                    if ($linecheck[0] == "END"){                        
                        $current_key = "";
                        $buffer = array();
                    } else {
                        /* If not, write the line */                     
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

                    // Do we need to grab the csv headers from the buffer?
                    if(array_key_exists($current_key,$headers)){
                        $data = explode(" - ",$buffer[$headers[$current_key][1]]);

                        // There might be carriage returns!
                        foreach($data as $key => $value)
                        {
                            $data[$key] = str_replace(array("\r", "\n"), '', $value);
                        }

                        $data[] = "date";

                        if($headers[$current_key][0] != false){
                            array_unshift($data,$headers[$current_key][0]);
                        }

                        $filename = "outputs/".$current_key."_".str_replace("/","_",$file).".csv";
                        $fp = fopen($filename, "a");
                        fputcsv($fp,$data);
                        fclose($fp);
                    }
                }

                /* Or does it need adding to the buffer? */
                if(substr($line, 0, 1) == "#"){
                    $buffer[] = $line;
                }
            }

            return("File successfully processed...");
        } else {
            return("Could not open file...");
        }            
    }
?>