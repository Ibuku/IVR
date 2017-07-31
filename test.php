<?php

	$username = ''
	$connection = ssh2_connect($address, 22);
        ssh2_auth_password($connection, $username, $password);
        ssh2_scp_send($connection, $localName, $remoteName, 0777);
?>
