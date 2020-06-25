<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_usuarios_roles_ajx extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'Admin_usuarios_roles_model' );
	}
	function crear() {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$nombre = $this->input->post ( 'nombre', TRUE );
		$permisos = json_decode ( $this->input->post ( 'permisos', TRUE ), FALSE );
		
		$error = '';
		
		if (($nombre != '') && (! empty ( $permisos ))) {
			
			$retorno = $this->Admin_usuarios_roles_model->crear_rol ( $nombre, $permisos );
		} else {
			$error = '<div class="alert alert-danger">Nombre vacíos!</div>';
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function listar() {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$start = $this->input->post ( 'start' ) - 1;
		$max_results = 30;
		$start = $max_results * $start;
		$end = $max_results;
		$cantidad = 0;
		
		$search = $this->input->post ( 'search', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_usuarios_roles_model->listarRoles ( $start, $end, $cantidad, $search );
		
		if ($cantidad > 0) {
			$this->output->set_output ( json_encode ( array (
					'listado' => $ret 
			) ) );
		} else {
			$this->output->set_output ( 'vacio' );
		}
	}
	function modificar() {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$id = $this->input->post ( 'id', TRUE );
		$nombre = $this->input->post ( 'nombre', TRUE );
		$permisos = json_decode ( $this->input->post ( 'permisos', TRUE ), FALSE );
		
		$error = '';
		
		if (($nombre != '') && (! empty ( $permisos ))) {
			
			$retorno = $this->Admin_usuarios_roles_model->modificar_rol ( $id, $nombre, $permisos );
		} else {
			$error = '<div class="alert alert-danger">Nombre vacíos!</div>';
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function existeRol() {
		$nombre = $this->input->post ( 'nombre', TRUE );
		$id = $this->input->post ( 'id', TRUE );
		
		$this->output->set_output ( json_encode ( array (
				'valid' => ! $this->Admin_usuarios_roles_model->existeRol ( $nombre, $id ) 
		) ) );
	}
	function obtenerTotal() {
		$search = $this->input->post ( 'search', TRUE );
		$ret = $this->Admin_usuarios_roles_model->obtenerCantidadRoles ( $search );
		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( array (
				'cantidad' => $ret 
		) ) );
	}
	function borrar() {
		$this->auth->tieneAcceso ( 'roles_crear_modificar' );
		$id = $this->input->post ( 'id', TRUE );
		// if ($this->Admin_model->newsbelongs($id)){
		$this->Admin_usuarios_roles_model->borrarRol ( $id );
		$this->output->set_output ( 'ok' );
		// }else{
		// $this->output->set_output('no_belong');
		// }
	}
	function actualizarPermisos() {
		$this->auth->tieneAcceso ( 'usuarios_modificar_companeros' );
		
		$id = $this->input->post ( 'id', TRUE );
		$idcompanero = $this->input->post ( 'idcompanero', TRUE );
		$permisos = json_decode ( $this->input->post ( 'permisos', FALSE ), TRUE );
		
		$this->Admin_usuarios_roles_model->modificar_companero ( $id, $idcompanero, $permisos );
	}
	function traerCompaneros() {
		$this->auth->tieneAcceso ( 'usuarios_modificar_companeros' );
		$id = $this->input->post ( 'id', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_usuarios_roles_model->obtenerCompaneros ( $id );
		$this->output->set_output ( json_encode ( $ret ) );
	}
	function buscarCompanero() {
		$this->auth->tieneAcceso ( 'usuarios_modificar_companeros' );
		$term = $this->input->post ( 'term', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_usuarios_roles_model->buscarCompaneros ( $term );
		$this->output->set_output ( json_encode ( $ret ) );
	}
	function agregarCompanero() {
		$this->auth->tieneAcceso ( 'usuarios_modificar_companeros' );
		
		$id = $this->input->post ( 'id', TRUE );
		$idcompanero = $this->input->post ( 'idcompanero', TRUE );
		
		$this->Admin_usuarios_roles_model->agregar_companero ( $id, $idcompanero );
	}
	function borrarCompanero() {
		$this->auth->tieneAcceso ( 'usuarios_modificar_companeros' );
		
		$id = $this->input->post ( 'id', TRUE );
		$idcompanero = $this->input->post ( 'idcompanero', TRUE );
		
		$this->Admin_usuarios_roles_model->borrar_companero ( $id, $idcompanero );
	}
}

?>