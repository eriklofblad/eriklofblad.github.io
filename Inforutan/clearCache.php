<?php
 
//The name of the folder.
$folder = 'cache';
 
//Get a list of all of the file names in the folder.
$files = glob($folder . '/*');
 
//Loop through the file list.
foreach($files as $file){
    //Make sure that this is a file and not a directory.
    if(is_file($file)){
        //Use the unlink function to delete the file.
        
        $begins = "cache/cache-";
        if(substr($file, 0, strlen($begins)) === $begins){
            $ends1 = ".json";
            $ends2 = ".html";
            if(endsWith($file, $ends1) || endsWith($file, $ends2)){
                echo $file;
                if(unlink($file)){
                    echo $file. " deleted <br>";
                }else{
                    echo "Unable to delete: ".$file."<br>";
                }
            }
        }
        
    }
}

function endsWith($haystack, $needle){
    $length = strlen($needle);

    if($length == 0){
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

?>