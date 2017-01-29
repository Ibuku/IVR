<?php
$path = "/home/httpd/html/index.php";
$file = basename($path);         // $file is set to "index.php"
$_file = basename($path, ".php"); // $file is set to "index"

echo $file;
echo $_file;
