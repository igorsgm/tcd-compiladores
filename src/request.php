<?php
include_once('LPS1Compiler.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && $code = $_POST['code']) {
	$compiler = new LPS1Compiler($code);
}