<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_micuenta extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_usuarios_model' );
	}
	function modificar() {
		$this->auth->tieneAcceso ();
		$data = array ();
		
		$id = $this->auth->id_usuario ();
		// /if ($this->Admin_model->newsbelongs($id)){
		
		$data ['ret'] = $this->Admin_usuarios_model->obtenerInfoUsuarios ( $id );
		
		$data ['idPersona'] = $id;
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/micuenta_view', $data );
		$this->load->view ( 'admin/_footer', $data );
		/*
		 * }else{
		 * $this->output->set_output('no_belong');
		 * }
		 */
	}
}

?>