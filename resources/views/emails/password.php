<!-- resources/views/emails/password.blade.php -->
<html>
	<head></head>
	<body>
		<p>Haga click aqui para restablecer su contraseña: <a href="<?php echo url('password/reset/'.$token) ?>"><?php echo url('password/reset/'.$token) ?></a></p>
	</body>
</html>
