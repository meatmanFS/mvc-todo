<!DOCTYPE HTML>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<title></title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
		<link href="/css/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
		<link href="/css/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="/css/style.css">
	</head>
	<body>
		<?php $this->layout( 'header' ) ?>
		<main role="main" class="container">
			<?php echo $content ?>     
		</main>
		<?php $this->layout( 'footer' ) ?>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
		<script type="text/javascript" src="/js/script.js"></script>
	</body>
</html>