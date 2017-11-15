<!doctype html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="assets/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="assets/custom.css"/>

	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/custom.js"></script>

	<title>TCD - Compiladores</title>
</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<a class="navbar-brand" href="#">TCD</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="navbar-collapse">
				<ul class="nav navbar-nav sider-navbar">
					<li id="profile">
						<figure class="profile-userpic">
							<img src="assets/imgs/igor.jpg" class="img-responsive" alt="Profile Picture">
						</figure>
						<div class="profile-usertitle">
							<div class="profile-usertitle-name">Igor Moraes</div>
							<div class="profile-usertitle-title">RA: 21501099</div>
						</div>
					</li>
					<li id="profile">
						<figure class="profile-userpic">
							<img src="assets/imgs/erick.jpg" class="img-responsive" alt="Profile Picture">
						</figure>
						<div class="profile-usertitle">
							<div class="profile-usertitle-name">Erick Rennan</div>
							<div class="profile-usertitle-title">RA:</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<section id="page-keeper">
		<div class="row">
			<div class="col-sm-12 program-title">
				Projeto B - Compiladores
			</div>
		</div>
		<div class="container-fluid main-container">

			<div class="row">

				<form data-form="code" action="src/request.php" method="post" role="form">
					<div class="col-sm-6">
						<div data-panel="editor" class="panel panel-default panel-editor">
							<div class="panel-heading">
								<h3 class="panel-title">LPS1 (Linguagem de Programação Simples 1)</h3>
							</div>
							<div class="panel-body">
								<textarea id="code" title="Editor" name="code" data-panel-textarea="editor" class="box-sizing-border"></textarea>
							</div>
						</div>
					</div>
				</form>

				<!-- Console-->
				<div class="col-sm-6">
					<div data-panel="console" class="panel panel-default panel-console">
						<div class="panel-heading">
							<h3 class="panel-title">Console</h3>
						</div>
						<div data-panel-body="console" class="panel-body">
							>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Button -->
		<div class="form-group">
			<label class="col-md-4 control-label" for="compile-button"></label>
			<div class="col-md-4 text-center">
				<button id="compile" name="compile-button" class="btn btn-success btn-lg btn-block">Compilar</button>
			</div>
		</div>
	</section>
</body>
</html>