<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_usuarios_roles extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_usuarios_roles_model' );
		$this->load->model ( 'Admin_usuarios_model' );
	}
	function listar() {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$data = array ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_roles_listar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
	function crear() {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$data = array ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_roles_crear_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
	function modificar($id = 0) {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$data = array ();
		
		// /if ($this->Admin_model->newsbelongs($id)){
		
		$data ['ret'] = $this->Admin_usuarios_roles_model->obtenerInfoRoles ( $id );
		
		$data ['idRol'] = $id;
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_roles_modificar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
		/*
		 * }else{
		 * $this->output->set_output('no_belong');
		 * }
		 */
	}
	function companeros($id = 0) {
		$this->auth->tieneAcceso ( 'usuarios_modificar_companeros' );
		$data = array ();
		
		$data ['usuario'] = $this->Admin_usuarios_model->obtenerInfoUsuarios ( $id );
		
		$data ['id_usuario'] = $id;
		// /if ($this->Admin_model->newsbelongs($id)){
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_companeros_modificar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
		/*
		 * }else{
		 * $this->output->set_output('no_belong');
		 * }
		 */
	}
}

?>