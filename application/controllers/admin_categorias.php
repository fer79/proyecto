<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_categorias extends CI_Controller {
	function __construct() {
		parent::__construct ();
		// $this->load->helper('recaptchalib');
		
		$this->load->model ( 'Admin_categorias_model' );
		
		$this->Admin_categorias_model = new Admin_categorias_model ();
	}
	function listar() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		$data = array ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/categorias_listar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
	function crear() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		
		$data = array ();
		
		$data ['padres'] = $this->Admin_categorias_model->obtenerCategorias ();
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/categorias_crear_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
	function modificar($id = '') {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		
		$data = array ();
		$data ['ret'] = $this->Admin_categorias_model->categoriaInfo ( $id );
		$data ['padres'] = $this->Admin_categorias_model->obtenerCategorias ( $id );
		
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/categorias_modificar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}
}

?>