<?php
require '../vendor/autoload.php';

use App\Compiler\LPS1Compiler;

if ($_SERVER["REQUEST_METHOD"] == "POST" && $code = $_POST['code']) {
	$compiler = new LPS1Compiler($code);

	echo json_encode($compiler->execute(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}