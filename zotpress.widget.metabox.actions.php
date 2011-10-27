<?php


    // Include WordPress
    require('../../../wp-load.php');
    define('WP_USE_THEMES', false);

    

    // Set up XML document
    $xml = "";
    
    

    /*
     
        SET DEFAULT STYLE
        
    */

    if (isset($_GET['submit']))
    {
        // Set up error array
        $errors = array("style_empty"=>array(0,"<strong>Style</strong> was left blank."),
                                "style_format"=>array(0,"<strong>Style</strong> was incorrectly formatted."),);
        
        // Check the post variables and record errors
        if (isset($_GET['style']) && trim($_GET['style']) != '')
            if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['style'])) == 1)
                $style = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['style']))));
            else
                $errors['style_format'][0] = 1;
        else
            $errors['style_empty'][0] = 1;
        
        
        // CHECK ERRORS
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // SET DEFAULT STYLE
        if ($errorCheck == false)
        {
            update_option("Zotpress_DefaultStyle", $style);
            
            // Display success XML
            $xml .= "<result success='true' style='".$style."' />\n";
        }
        
        
        // DISPLAY ERRORS
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
        }
    }
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<style>\n";
    echo $xml;
    echo "</style>";

?>