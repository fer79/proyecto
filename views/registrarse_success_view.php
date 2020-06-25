<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sistema de Inscripciones y Evaluaciones - Login</title>
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png"
	href="<?php echo base_url();?>img/favicon.png" />

<!-- Le styles -->
<link href="<?php echo base_url();?>css/bootstrap.css" rel="stylesheet">
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery.min.js"></script>
<link href="<?php echo base_url();?>css/loginregister.css"
	rel="stylesheet">

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
				<div class="account-wall">
                    <?php
																				
if ($error)
																					echo $error;
																				?>
               	
              
            </div>
				<a href="<?php echo base_url(); ?>login"
					class="text-center new-account">Login</a>
			</div>
		</div>
	</div>
	</div>
	<!-- /container -->
</body>
</html>