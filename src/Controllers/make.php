<?php
//echo file_exists(realpath(__DIR__ . '/../..'). '/files/test');
if (!file_exists(realpath(__DIR__ . '/../..'). '/files/test')) {
	$res = mkdir(realpath(__DIR__ . '/../..'). '/files/test', 0777, true);
	chmod(realpath(__DIR__ . '/../..'). '/files/test', 0777);
}
//$res = exec ("find ". realpath(__DIR__ . "/../.."). "/files/testx". " -type d -exec chmod 0750 {} +");
//echo $res
?>
