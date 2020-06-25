<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sistema de Inscripciones y Evaluaciones - Login</title>
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png" href="<?php echo base_url();?>img/favicon.png" />

<!-- Le styles -->
<link href="<?php echo base_url();?>css/bootstrap.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.min.js"></script>
<link href="<?php echo base_url();?>css/loginregister.css" rel="stylesheet">

</head>

<script type="text/javascript">
$(document).ready(function() {
    $('input.nospace').keydown(function(e) {
        if (e.keyCode == 32) {
            return false;
        }
    });
});
</script>

<body>

	<script>
	  var RecaptchaOptions={
	      theme:'red',
	       tabindex : 5

	  };
  </script>

	<div class="container">
		<hr>
		<div class="row">
			<div class="col-sm-6 col-md-4 col-md-offset-4">
				<!-- <h3 style="text-align:center;">Sistema de Inscripciones y Evaluaciones</h3>   -->
				<img src="<?php echo base_url()?>img/logo.png" class="logosistema">
				<div class="account-wall">
					<h5 style="text-align: center;">Registrarse</h5>
         	<?php if(isset($error)){ echo $error;} ?>
          <form class="form-signin" method="POST" action="<?php echo base_url()?>registrarse">
						<input name="username" type="text" value="<?php echo $username;?>" class="form-control" placeholder="Usuario" required autofocus>
						<input name="email" type="text" value="<?php echo $email;?>" class="form-control" placeholder="E-mail" required autofocus>
						<input name="password" type="password" class="form-control" placeholder="Password" required>
						<input name="password2" type="password" class="form-control" placeholder="Repetir Password" required>
            <?php recaptcha(); ?>
          	<input name="submit" value="1" type="hidden">
						<button class="btn btn-lg btn-primary btn-block" type="submit">Crear cuenta</button>
						<!--
						<label class="checkbox pull-left">
                <input type="checkbox" value="remember-me">
                Recordarme
            </label>
            <a href="#" class="pull-right need-help">Necesitas ayuda? </a><span class="clearfix"></span>
           	-->
				 </form>
				</div>
				<a href="<?php echo base_url(); ?>" class="text-center new-account">Regresar</a>
			</div>
		</div>
	</div>
	</div>
	<!-- /container -->
</body>
</html>
