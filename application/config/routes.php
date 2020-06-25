<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * | -------------------------------------------------------------------------
 * | URI ROUTING
 * | -------------------------------------------------------------------------
 * | This file lets you re-map URI requests to specific controller functions.
 * |
 * | Typically there is a one-to-one relationship between a URL string
 * | and its corresponding controller class/method. The segments in a
 * | URL normally follow this pattern:
 * |
 * | example.com/class/method/id/
 * |
 * | In some instances, however, you may want to remap this relationship
 * | so that a different class/function is called than the one
 * | corresponding to the URL.
 * |
 * | Please see the user guide for complete details:
 * |
 * | http://codeigniter.com/user_guide/general/routing.html
 * |
 * | -------------------------------------------------------------------------
 * | RESERVED ROUTES
 * | -------------------------------------------------------------------------
 * |
 * | There area two reserved routes:
 * |
 * | $route['default_controller'] = 'welcome';
 * |
 * | This route indicates which controller class should be loaded if the
 * | URI contains no data. In the above example, the "welcome" class
 * | would be loaded.
 * |
 * | $route['404_override'] = 'errors/page_missing';
 * |
 * | This route will tell the Router what URI segments to use if those provided
 * | in the URL cannot be matched to a valid route.
 * |
 */

$route ['default_controller'] = "home/main/1";
$route ['404_override'] = 'home/error404';
$route ['error404'] = 'home/error404';

$route ['login'] = 'users/ver_login';
$route ['logout'] = 'users/logout';
$route ['registrarse'] = 'users/registrarse';

$route ['enviarmails'] = 'cron/enviarmails';

$route ['categoria/(:num)/(:any)'] = 'home/main/$1';
$route ['(inscripcion|evaluacion)/(:num)/(:any)'] = 'home/verformulario/$2';

$route ['olvide-mi-contrasena'] = 'users/reset_password';

$route ['usuario/revalidar/(:any)'] = 'users/reenviar_validacion/$1';
$route ['usuario/validar/(:any)/(:any)'] = 'users/validar/$1/$2';

$route ['adminpanel'] = 'admin/main';

$route ['adminpanel/categorias/modificar/(:num)'] = 'admin_categorias/modificar/$1';
$route ['adminpanel/categorias/crear'] = 'admin_categorias/crear';
$route ['adminpanel/categorias'] = 'admin_categorias/listar';

$route ['adminpanel/formularios/(:num)/registros'] = 'registros/listar/$1';
$route ['adminpanel/formularios/(:num)/registros/ver/(:num)'] = 'registros/ver/$2';

$route ['adminpanel/formularios/modificar/(:num)'] = 'formularios/modificar/$1';
$route ['adminpanel/formularios/crear'] = 'formularios/crear';
$route ['adminpanel/formularios'] = 'formularios/listar';

$route ['adminpanel/usuarios/companeros/(:num)'] = 'admin_usuarios_roles/companeros/$1';

$route ['adminpanel/usuarios/roles-y-permisos/modificar/(:num)'] = 'admin_usuarios_roles/modificar/$1';
$route ['adminpanel/usuarios/roles-y-permisos/crear'] = 'admin_usuarios_roles/crear';
$route ['adminpanel/usuarios/roles-y-permisos'] = 'admin_usuarios_roles/listar';

$route ['adminpanel/usuarios/modificar/(:num)'] = 'admin_usuarios/modificar/$1';
$route ['adminpanel/usuarios/crear'] = 'admin_usuarios/crear';
$route ['adminpanel/usuarios'] = 'admin_usuarios/listar';


$route ['adminpanel/formularios/estado-emails/(:num)'] = 'formularios/estadoEmails/$1';

$route ['adminpanel/micuenta'] = 'admin_micuenta/modificar';
$route ['adminpanel/micuenta/misinscripciones'] = 'registros/misinscripciones';
$route ['adminpanel/micuenta/registros/ver/(:num)'] = 'registros/vermiregistro/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
