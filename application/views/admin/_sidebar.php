
<!-- sidebar -->
<div id="sidebar-nav">
	<ul id="dashboard-menu">

		<li>
			<a href="<?php echo base_url();?>"> <i class="icon-tasks"></i> <span>Formularios</span></a>
		</li>

		<li class="active">
			<div class="pointer">
				<div class="arrow"></div>
				<div class="arrow_border"></div>
			</div>
			<a href="<?php echo base_url();?>adminpanel"> <i class="icon-home"></i> <span>Home</span></a>
		</li>

    <?php if ($this->auth->tieneAcceso('formularios_ver_listado',true)){ ?>
    <li>
			<a class="dropdown-toggle" href="#">
				<i class="icon-edit"></i> <span>Formularios</span> <i class="icon-chevron-down"></i>
			</a>
			<ul class="submenu">
      	<?php if ($this->auth->tieneAcceso ( 'formularios_ver_listado', true )) { ?>
        <li><a href="<?php echo base_url();?>adminpanel/formularios">Listar Formularios</a></li>
        <?php } ?>

				<?php  if ($this->auth->tieneAcceso('formularios_crear',true)){ ?>
        <li><a href="<?php echo base_url();?>adminpanel/formularios/crear">Crear Formulario</a></li>
        <?php } ?>

        <?php  if ($this->auth->tieneAcceso('categorias_modificar',true)){ ?>
        <li><a href="<?php echo base_url();?>adminpanel/categorias">Categorias</a></li>
        <?php } ?>

      </ul>
		</li>
    <?php } ?>

		<?php if (($this->auth->tieneAcceso ( 'usuarios_ver_listado', true )) and ($this->auth->tieneAcceso ( 'usuarios_crear', true ))) {?>
    <li>
			<a class="dropdown-toggle" href="#"> <i
				class="icon-user"></i> <span>Usuarios</span> <i
				class="icon-chevron-down"></i>
			</a>
			<ul class="submenu">
      	<?php  if ($this->auth->tieneAcceso('usuarios_ver_listado',true)){ ?>
        <li><a href="<?php echo base_url();?>adminpanel/usuarios">Listar Usuarios</a></li>
        <?php } ?>

        <?php  if ($this->auth->tieneAcceso('usuarios_crear',true)){ ?>
        <li><a href="<?php echo base_url();?>adminpanel/usuarios/crear">Crear Nuevo Usuario</a></li>
        <?php } ?>

				<?php  if ($this->auth->tieneAcceso('roles_crear_modificar',true)){ ?>
        <li><a href="<?php echo base_url();?>adminpanel/usuarios/roles-y-permisos">Roles y Permisos</a></li>
        <?php } ?>
      </ul>
		</li>
    <?php } ?>

		<li>
			<a href="<?php echo base_url();?>adminpanel/micuenta"> <i class="icon-cog"></i> <span>Mi Cuenta</span></a>
		</li>

		<li>
			<a href="<?php echo base_url();?>adminpanel/micuenta/misinscripciones">
				<i class="icon-edit"></i> <span>Mis Inscripciones y Evaluaciones</span>
			</a>
		</li>
	</ul>

</div>
<!-- end sidebar -->
