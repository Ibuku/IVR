<?php
	$username = 'tm30';
	$password = 'fileopen';
	$address = 'freepbx';
	$connection = ssh2_connect($address, 22);
	var_dump($connection);
        $x = ssh2_auth_password($connection, $username, $password);
	var_dump($x);
        $y = ssh2_scp_send($connection, $localName, $remoteName, 0777);
	var_dump($y);
?>
