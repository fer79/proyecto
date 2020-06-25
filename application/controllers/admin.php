<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->auth->tieneAcceso ();
		$this->load->model ( 'Admin_usuarios_model' );
	}
	function main() {
		$data = new stdClass ();
		$data->error_message = "";
		
		$data->cantUsuarios = $this->Admin_usuarios_model->obtenerCantidadUsuarios ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/main_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
}

?>