<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_categorias_ajx extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'Admin_categorias_model' );
		
		$this->Admin_categorias_model = new Admin_categorias_model ();
		$this->load->model ( 'Form_validate_model' );
	}
	function crear() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		$nombre = $this->input->post ( 'nombre', TRUE );
		$padre = $this->input->post ( 'padre', TRUE );
		
		$error = '';
		
		if ($nombre == '') {
			$error = '<div class="alert alert-danger">El nombre no puede ser vacío!</div>';
		} elseif ($padre == 'NULL') {
			$error = '<div class="alert alert-danger">El padre no puede ser vacío!</div>';
		} else {
			
			$retorno = $this->Admin_categorias_model->crear_categoria ( $nombre, $padre );
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function listar() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		$start = $this->input->post ( 'start' ) - 1;
		$max_results = 30;
		$start = $max_results * $start;
		$end = $max_results;
		$cantidad = 0;
		
		$search = $this->input->post ( 'search', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_categorias_model->listarCategorias ( $start, $end, $cantidad, $search );
		
		if ($cantidad > 0) {
			$this->output->set_output ( json_encode ( array (
					'listado' => $ret 
			) ) );
		} else {
			$this->output->set_output ( 'vacio' );
		}
	}
	function modificar() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		$nombre = $this->input->post ( 'nombre', TRUE );
		$padre = $this->input->post ( 'padre', TRUE );
		$id = $this->input->post ( 'id', TRUE );
		
		$error = '';
		
		if ($nombre == '') {
			$error = '<div class="alert alert-danger">El nombre no puede ser vacío!</div>';
		} elseif ($padre == 'NULL') {
			$error = '<div class="alert alert-danger">El padre no puede ser vacío!</div>';
		} else {
			
			$this->Admin_categorias_model->modificar_categoria ( $nombre, $padre, $id );
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function existeCategoria() {
		$nombre = $this->input->post ( 'nombre', TRUE );
		$padre = $this->input->post ( 'padre', TRUE );
		$id = $this->input->post ( 'id', TRUE );
		
		echo json_encode ( array (
				'valid' => ! $this->Admin_categorias_model->existeCategoria ( $nombre, $padre, $id ) 
		) );
	}
	function traerUsuarios() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		$id = $this->input->post ( 'id', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_categorias_model->obtenerUsuarios ( $id );
		$this->output->set_output ( json_encode ( $ret ) );
	}
	function obtenerTotal() {
		$search = $this->input->post ( 'search', TRUE );
		$ret = $this->Admin_categorias_model->obtenerCantidadCategorias ( $search );
		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( array (
				'cantidad' => $ret 
		) ) );
	}
	function buscarUsuario() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		$term = $this->input->post ( 'term', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_categorias_model->buscarUsuarios ( $term );
		$this->output->set_output ( json_encode ( $ret ) );
	}
	function agregarUsuario() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		
		$id = $this->input->post ( 'id', TRUE );
		$idUsuario = $this->input->post ( 'idUsuario', TRUE );
		
		$this->Admin_categorias_model->agregar_usuario ( $id, $idUsuario );
	}
	function borrarUsuario() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		
		$id = $this->input->post ( 'id', TRUE );
		$idUsuario = $this->input->post ( 'idUsuario', TRUE );
		
		$this->Admin_categorias_model->borrar_usuario ( $id, $idUsuario );
	}
	function borrar() {
		$this->auth->tieneAcceso ( 'categorias_modificar' );
		
		$id = $this->input->post ( 'id', TRUE );
		// if ($this->Admin_model->newsbelongs($id)){
		$this->Admin_categorias_model->borrarCategoria ( $id );
		$this->output->set_output ( 'ok' );
		// }else{
		// $this->output->set_output('no_belong');
		// }
	}
}

?>