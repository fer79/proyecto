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

$route ['default_controller'] = "home/main";
$route ['404_override'] = 'home/error404';
$route ['error404'] = 'home/error404';

$route ['login'] = 'users/ver_login';
$route ['logout'] = 'users/logout';
$route ['registrarse'] = 'users/registrarse';

$route ['olvide-mi-contrasena'] = 'users/reset_password';

$route ['usuario/revalidar/(:any)'] = 'users/reenviar_validacion/$1';
$route ['usuario/validar/(:any)/(:any)'] = 'users/validar/$1/$2';

$route ['adminpanel'] = 'admin/main';

$route ['adminpanel/personas/modificar/(:num)'] = 'personas/modificar/$1';
$route ['adminpanel/personas/crear'] = 'personas/crear';
$route ['adminpanel/personas'] = 'personas/listar';

$route ['adminpanel/usuarios/modificar/(:num)'] = 'admin_usuarios/modificar/$1';
$route ['adminpanel/usuarios/crear'] = 'admin_usuarios/crear';
$route ['adminpanel/usuarios'] = 'admin_usuarios/listar';

$route ['adminpanel/proyectos/modificar/(:num)'] = 'proyectos/modificar/$1';
$route ['adminpanel/proyectos/crear'] = 'proyectos/crear';
$route ['adminpanel/proyectos'] = 'proyectos/listar';

/* FRONTEND */

$route ['preguntas-frecuentes'] = 'home/faq';

$route ['personas/pagina/(:num)'] = "home/personas/$1";
$route ['personas'] = 'home/personas/1';

$route ['proyectos/pagina/(:num)'] = "home/proyectos/$1";
$route ['proyectos'] = 'home/proyectos/1';


/* End of file routes.php */
/* Location: ./application/config/routes.php */