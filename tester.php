<?php
$_POST = eval('return ' . file_get_contents('export7.txt') . ';');

if (!isset($_GET['f'])) {
	echo 'file must be supplied';
	exit();

}
//file is whatever ipn script you need to run
$file = $_GET['f'];
if (!isset($file)) {
	echo 'yallo';
	exit();
}

chdir(dirname($file));
require(basename($file));

?>