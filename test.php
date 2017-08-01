<?php
$username = 'tm30';
$password = 'fileopen';
$address = 'freepbx';
$localName = 'test.txt';
$remoteName = '/tmp/test.txt';
$connection = ssh2_connect($address, 22);
var_dump($connection);
$x = ssh2_auth_password($connection, $username, $password);
//$x = ssh2_auth_none($connection, $username);
var_dump($x);
$y = ssh2_scp_send($connection, $localName, $remoteName, 0777);
var_dump($y);
?>
