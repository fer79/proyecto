<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sistema de Inscripciones y Evaluaciones - Login</title>
<meta name="description" content="">
<link rel="icon" type="image/png"
	href="<?php echo base_url();?>img/favicon.png" />
<meta name="author" content="">

<!-- Le styles -->
<link href="<?php echo base_url();?>css/bootstrap.css" rel="stylesheet">
<link href="<?php echo base_url();?>css/loginregister.css"
	rel="stylesheet">

</head>
<body>
	<div class="container">
		<hr>
		<div class="row">
			<div class="col-sm-6 col-md-4 col-md-offset-4">
				<!-- <h3 style="text-align:center;">Sistema de Inscripciones y Evaluaciones</h3>   -->
				<img src="<?php echo base_url()?>img/logo.png" class="logosistema">
				<div class="account-wall">
					<h5 style="text-align: center;">Login</h5>
             	<?php if(isset($error)){ echo $error;} ?>
                <form class="form-signin" method="POST"
						action="<?php echo base_url()?>login">
						<input name="username" type="text" class="form-control"
							placeholder="Usuario" required autofocus> <input name="password"
							type="password" class="form-control" placeholder="Password"
							required>
						<button class="btn btn-lg btn-primary btn-block" type="submit">
							Conectarse</button>
						<!--<label class="checkbox pull-left">
                    <input type="checkbox" value="remember-me">
                    Recordarme
                </label>
                -->
						<a href="<?php echo base_url(); ?>olvide-mi-contrasena"
							class="pull-right need-help">Olvidé mi contraseña </a><span
							class="clearfix"></span>
					</form>
				</div>
				<a href="<?php echo base_url(); ?>registrarse"
					class="text-center new-account">Crear Cuenta</a> <a
					href="<?php echo base_url(); ?>" class="text-center new-account">Regresar</a>
			</div>
		</div>
	</div>
	</div>
	<!-- /container -->
</body>
</html>