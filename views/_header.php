<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES">

<meta charset="UTF-8">
	<meta name="robots" content="noodp">
		<link rel="icon" type="image/png"
			href="<?php echo base_url();?>img/favicon.png" />
		<title><?php echo $site_title;?></title>
		<link href="https://fonts.googleapis.com/css?family=Baloo|Open+Sans"
			rel="stylesheet">
			<link href="<?php echo base_url();?>css/style.css" rel="stylesheet"
				type="text/css" />


			<script type="text/javascript"
				src="<?php echo base_url();?>js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript"
				src="<?php echo base_url();?>js/jquery-ui-1.10.2.custom.min.js"></script>
			<script src="<?php echo base_url();?>js/jquery.tablesorter.js"></script>
			<script
				src="<?php echo base_url();?>js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
			<script
				src="<?php echo base_url();?>js/jquery-ui-1.10.3.custom/development-bundle/ui/i18n/jquery-ui-i18n.js"></script>
			<link
				href="<?php echo base_url();?>js/jquery-ui-1.10.3.custom/css/custom-theme/jquery-ui-1.10.3.custom.css"
				rel="stylesheet" type="text/css" />
			<link href="<?php echo base_url();?>js/themes/green/style.css"
				rel="stylesheet" type="text/css" />
			<link rel="stylesheet"
				href="<?php echo base_url();?>assets/css/font-awesome.min.css">

<?php flush(); ?>
<body>
					<div class="wrapper">
						<div class="header">
							<span
								style="position: absolute; display: block; right: 0; top: 0;"><?php if (true){ ?> <span
								style="font-weight: normal; font-weight: lighter; font-size: 14px; color: #aaa;">Bienvenido, <?php echo $this->auth->usu_nombre(); ?></span><?php } ?> <a
								id="loginlink" href="<?php echo base_url(); ?>"><i
									class="fa fa-home" aria-hidden="true"></i> Inicio</a> <span
								style="color: #e7e7e7">|</span>
		<?php if (false){ ?>  <a id="loginlink"
								href="<?php echo base_url(); ?>login"><i class="fa fa-sign-in"
									aria-hidden="true"></i> Login</a> <span style="color: #e7e7e7">|</span>
								<a id="loginlink" href="<?php echo base_url(); ?>registrarse">Registrarse</a>
		<?php }else{ ?>
		<span style="float: right;"><a id="loginlink"
									href="<?php echo base_url(); ?>adminpanel"><i
										class="fa fa-user" aria-hidden="true"></i> Panel</a> <span
									style="color: #e7e7e7">|</span> <a id="loginlink"
									href="<?php echo base_url(); ?>logout"><i
										class="fa fa-sign-out" aria-hidden="true"></i> Cerrar sesión</a>
							</span><?php }?></span>
						</div>
