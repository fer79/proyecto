<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

	<?php if($this->auth->logged_in()){?>
   <!-- navbar -->
			<header class="navbar navbar-inverse" role="banner">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse"
					id="menu-toggler">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo base_url();?>"> <img
					src="<?php echo base_url(); ?>img/logo.png" alt="logo" />
				</a>
			</div>
			<ul class="nav navbar-nav pull-right hidden-xs">
				<li class="hidden-xs hidden-sm"><input class="search" type="text" />
				</li>
				<li class="notification-dropdown hidden-xs hidden-sm"><a href="#"
					class="trigger"> <i class="icon-warning-sign"></i> <span
						class="count">6</span>
				</a>
					<div class="pop-dialog">
						<div class="pointer right">
							<div class="arrow"></div>
							<div class="arrow_border"></div>
						</div>
						<div class="body">
							<a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
							<div class="notifications">
								<h3>Tienes 6 notificaciones</h3>
								<a href="#" class="item"> <i class="icon-signin"></i> Nueva
									Persona Creada <span class="time"><i class="icon-time"></i> 13
										min.</span>
								</a> <a href="#" class="item"> <i class="icon-signin"></i> Nueva
									Persona Creada <span class="time"><i class="icon-time"></i> 18
										min.</span>
								</a> <a href="#" class="item"> <i class="icon-envelope-alt"></i>
									Nuevo Mensaje de Pedro <span class="time"><i class="icon-time"></i>
										28 min.</span>
								</a> <a href="#" class="item"> <i class="icon-signin"></i> Nueva
									Persona Creada <span class="time"><i class="icon-time"></i> 49
										min.</span>
								</a> <a href="#" class="item"> <i class="icon-download-alt"></i>
									1 Proyecto para revisar <span class="time"><i class="icon-time"></i>
										1 day.</span>
								</a>
								<div class="footer">
									<a href="#" class="logout">Ver todas las notificaciones</a>
								</div>
							</div>
						</div>
					</div></li>
				<li class="notification-dropdown hidden-xs hidden-sm"><a href="#"
					class="trigger"> <i class="icon-envelope"></i>
				</a>
					<div class="pop-dialog">
						<div class="pointer right">
							<div class="arrow"></div>
							<div class="arrow_border"></div>
						</div>
						<div class="body">
							<a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
							<div class="messages">
								<a href="#" class="item"> <img
									src="<?php echo base_url(); ?>img/contact-img.png"
									class="display" alt="user" />
									<div class="name">Alejandra Galván</div>
									<div class="msg">Lorem ipsum dolor sit amet, consectetur
										adipiscing elit. Integer eu molestie felis.</div> <span
									class="time"><i class="icon-time"></i> 13 min.</span>
								</a> <a href="#" class="item"> <img
									src="<?php echo base_url(); ?>img/contact-img2.png"
									class="display" alt="user" />
									<div class="name">Alejandra Galván</div>
									<div class="msg">Lorem ipsum dolor sit amet, consectetur
										adipiscing elit. Integer eu molestie felis.</div> <span
									class="time"><i class="icon-time"></i> 26 min.</span>
								</a> <a href="#" class="item last"> <img
									src="<?php echo base_url(); ?>img/contact-img.png"
									class="display" alt="user" />
									<div class="name">Alejandra Galván</div>
									<div class="msg">Lorem ipsum dolor sit amet, consectetur
										adipiscing elit. Integer eu molestie felis.</div> <span
									class="time"><i class="icon-time"></i> 48 min.</span>
								</a>
								<div class="footer">
									<a href="#" class="logout">Ver todos los mensajes</a>
								</div>
							</div>
						</div>
					</div></li>

				<li class="dropdown"><a href="#"
					class="dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown">
                    <?php echo $this->session->userdata('name');?>
                    <b class="caret"></b>
				</a>
					<ul class="dropdown-menu">
						<li><a href="personal-info.html">Información Personal</a></li>
						<li><a href="#"><i class="fa fa-envelope"></i> Casilla de mensajes
								<span class="badge">7</span></a></li>
						<li><a href="<?php echo base_url()?>adminpanel"><i
								class="fa fa-gear"></i> Panel de Administración</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo base_url()?>logout"><i
								class="fa fa-power-off"></i> Desconectarse</a></li>
					</ul></li>
				<li class="settings hidden-xs hidden-sm"><a
					href="personal-info.html" role="button"> <i class="icon-cog"></i>
				</a></li>
				<li class="settings hidden-xs hidden-sm"><a href="signin.html"
					role="button"> <i class="icon-share-alt"></i>
				</a></li>
			</ul>
			</header>

   <?php }?>