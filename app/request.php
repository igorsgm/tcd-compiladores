<?php
require '../vendor/autoload.php';

use App\Compiler\LPS1Compiler;

if ($_SERVER["REQUEST_METHOD"] == "POST" && $code = $_POST['code']) {
	$compiler = new LPS1Compiler($code);

	$compiledCode = $compiler->run();

	var_dump($compiledCode);
	die;
}