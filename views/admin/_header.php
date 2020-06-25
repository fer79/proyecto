<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/png"
			href="<?php echo base_url();?>img/favicon.png" />
		<!-- bootstrap -->
		<link href="<?php echo base_url(); ?>css/bootstrap/bootstrap.css"
			rel="stylesheet" />
		<link
			href="<?php echo base_url(); ?>css/bootstrap/bootstrap-overrides.css"
			type="text/css" rel="stylesheet" />

		<!-- libraries -->
		<link
			href="<?php echo base_url(); ?>css/lib/jquery-ui-1.10.2.custom.css"
			rel="stylesheet" type="text/css" />

		<link href="<?php echo base_url(); ?>css/lib/uniform.default.css"
			type="text/css" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>css/lib/select2.css"
			type="text/css" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>css/lib/bootstrap.datepicker.css"
			type="text/css" rel="stylesheet" />
		<link href="<?php echo base_url(); ?>css/lib/font-awesome.css"
			type="text/css" rel="stylesheet" />

		<!-- global styles -->
		<link rel="stylesheet" type="text/css"
			href="<?php echo base_url(); ?>css/compiled/layout.css" />
		<link rel="stylesheet" type="text/css"
			href="<?php echo base_url(); ?>css/compiled/elements.css" />
		<link rel="stylesheet" type="text/css"
			href="<?php echo base_url(); ?>css/compiled/icons.css" />

		<link rel="stylesheet" type="text/css"
			href="<?php echo base_url(); ?>css/bootstrap-colorpicker.min.css" />


		<script src="<?php echo base_url();?>js/jquery-1.10.2.min.js"></script>
		<!-- open sans font -->
		<link
			href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'
			rel='stylesheet' type='text/css' />

		<!-- lato font -->
		<link
			href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic'
			rel='stylesheet' type='text/css' />

		<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
		</head>

		<body>

			<!-- navbar -->
			<header class="navbar navbar-inverse" role="banner">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse"
					id="menu-toggler">
					<span class="sr-only">Expandir Navegación</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo base_url(); ?>"> <i
					class="icon-tasks"></i> Sistema de inscripciones y Evaluaciones
				</a>
			</div>
			<ul class="nav navbar-nav pull-right hidden-xs">
				<li class="settings hidden-xs hidden-sm"><a
					href="<?php echo base_url('adminpanel/micuenta'); ?>"
					title="Mi Cuenta" role="button"> <span
						style="padding: 10px; text-transform: capitalize; font-size: 14px; margin-top: -9px; display: block; float: left;"><?php echo $this->auth->usu_nombre(); ?></span>
						<i class="icon-cog"></i>
				</a></li>
				<li class="settings hidden-xs hidden-sm"><a
					href="<?php echo base_url('logout'); ?>" title="Cerrar Sesión"
					role="button"> <i class="icon-signout"></i>
				</a></li>

			</ul>
			</header>
			<!-- end navbar -->