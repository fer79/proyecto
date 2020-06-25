<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_usuarios extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_usuarios_model' );
	}
	function listar() {
		$this->auth->tieneAcceso ( 'usuarios_ver_listado' );
		$data = array ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_listar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
	function crear() {
		$this->auth->tieneAcceso ( 'usuarios_crear' );
		$data = array ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_crear_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
	function modificar($id = 0) {
		$this->auth->tieneAcceso ( 'usuarios_modificar' );
		$data = array ();
		
		// /if ($this->Admin_model->newsbelongs($id)){
		
		$data ['ret'] = $this->Admin_usuarios_model->obtenerInfoUsuarios ( $id );
		
		$data ['idPersona'] = $id;
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/usuarios_modificar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
		/*
		 * }else{
		 * $this->output->set_output('no_belong');
		 * }
		 */
	}
}

?>