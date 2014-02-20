<?php
    $path = $_REQUEST['path'];
    if ((strpos($path, "techshroom") !== FALSE) or (strpos($path, "http://") === FALSE)) {
        die('techshroom!');
    }
    header("Content-Type: application/octet-stream");
    if(isset($_REQUEST['filename'])) {
        $name = $_REQUEST['filename'];
    } else {
        $name = "dl.txt";
    }
    header("Content-Disposition: attachment; filename=".$name);
    readfile($_REQUEST['path']);
?>