<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
	/* Las dependencias son permisos que e te ienen que activar si o si. Por ejemplo, no se puede modificar un usuario sin poder ver el listado. */

$config ['permisosroles'] = array (
		
		'usuarios_crear' => array (
				'descripcion' => 'Permite crear usuarios',
				'dependencia' => array (
						'usuarios_ver_listado' 
				) 
		),
		'usuarios_modificar' => array (
				'descripcion' => 'Permite modificar usuarios',
				'dependencia' => array (
						'usuarios_ver_listado',
						'usuarios_crear' 
				) 
		),
		'usuarios_eliminar' => array (
				'descripcion' => 'Permite eliminar usuarios',
				'dependencia' => array (
						'usuarios_modificar',
						'usuarios_crear',
						'usuarios_ver_listado' 
				) 
		),
		'usuarios_ver_listado' => array (
				'descripcion' => 'Permite listar usuarios',
				'dependencia' => array () 
		),
		'usuarios_modificar_companeros' => array (
				'descripcion' => 'Permite asignar o desasignar compañeros',
				'dependencia' => array (
						'usuarios_ver_listado' 
				) 
		),
		'roles_crear_modificar' => array (
				'descripcion' => 'Permite crear y modificar roles',
				'dependencia' => array () 
		),
		
		'formularios_crear' => array (
				'descripcion' => 'Permite crear formularios',
				'dependencia' => array (
						'formularios_ver_listado' 
				) 
		),
		'formularios_ajenos' => array (
				'descripcion' => 'Permite ver formularios ajenos',
				'dependencia' => array (
						'formularios_ver_listado' 
				) 
		),
		'formularios_modificar' => array (
				'descripcion' => 'Permite modificar formularios',
				'dependencia' => array (
						'formularios_ver_listado',
						'formularios_crear' 
				) 
		),
		'formularios_eliminar' => array (
				'descripcion' => 'Permite eliminar formularios',
				'dependencia' => array (
						'formularios_modificar',
						'formularios_crear',
						'formularios_ver_listado' 
				) 
		),
		'formularios_ver_listado' => array (
				'descripcion' => 'Permite ver el listado de formularios',
				'dependencia' => array () 
		),
		
		'registros_sorteo_plaza' => array (
				'descripcion' => 'Permite hacer sorteo de plazas',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_sorteo_beca' => array (
				'descripcion' => 'Permite hacer sorteo de becas',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_habilitar_tarde' => array (
				'descripcion' => 'Permite habilitar una inscripción tardía',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_marcar_pago' => array (
				'descripcion' => 'Permite marcar como pago un registro',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_marcar_habilitado' => array (
				'descripcion' => 'Permite marcar como habilitado un registro',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_ver_completo' => array (
				'descripcion' => 'Permite ver los datos completos del usuario',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_ver_listado' => array (
				'descripcion' => 'Permite ver el listado completo',
				'dependencia' => array () 
		),
		'registros_exportar' => array (
				'descripcion' => 'Permite exportar los datos',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_eliminar' => array (
				'descripcion' => 'Permite eliminar registros',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		
		'categorias_modificar' => array (
				'descripcion' => 'Permite administrar categorias',
				'dependencia' => array () 
		) 
)
;

$config ['permisoscompaneros'] = array (
		
		'formularios_modificar' => array (
				'descripcion' => 'Permite modificar formularios',
				'dependencia' => array (
						'formularios_ver_listado' 
				) 
		),
		'formularios_eliminar' => array (
				'descripcion' => 'Permite eliminar formularios',
				'dependencia' => array (
						'formularios_modificar',
						'formularios_ver_listado' 
				) 
		),
		'formularios_ver_listado' => array (
				'descripcion' => 'Permite ver el listado de formularios',
				'dependencia' => array () 
		),
		'registros_sorteo_plaza' => array (
				'descripcion' => 'Permite hacer sorteo de plazas',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_sorteo_beca' => array (
				'descripcion' => 'Permite hacer sorteo de becas',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_habilitar_tarde' => array (
				'descripcion' => 'Permite habilitar una inscripción tardía',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_marcar_pago' => array (
				'descripcion' => 'Permite marcar como pago un registro',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_marcar_habilitado' => array (
				'descripcion' => 'Permite marcar como habilitado un registro',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_ver_completo' => array (
				'descripcion' => 'Permite ver los datos completos del usuario',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		),
		'registros_ver_listado' => array (
				'descripcion' => 'Permite ver el listado completo',
				'dependencia' => array () 
		),
		'registros_exportar' => array (
				'descripcion' => 'Permite exportar los datos',
				'dependencia' => array (
						'registros_ver_listado' 
				) 
		) 
)
;

?>
