<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_usuarios_ajx extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'Admin_usuarios_model' );
		$this->load->model ( 'Form_validate_model' );
		
		$this->Admin_usuarios_model = new Admin_usuarios_model ();
	}
	function crear() {
		$this->auth->tieneAcceso ( 'usuarios_crear' );
		$usuario = $this->input->post ( 'usuario', TRUE );
		$email = $this->input->post ( 'email', TRUE );
		$password = $this->input->post ( 'password', TRUE );
		$password2 = $this->input->post ( 'password2', TRUE );
		$rol = $this->input->post ( 'rol', TRUE );
		
		$enviarmail = $this->input->post ( 'enviarmail', TRUE );
		
		$error = '';
		
		if ($usuario && $password && $password2 && $email && $rol) {
			if (preg_match ( '/\s/', $usuario ) >= 1) {
				$error = '<div class="alert alert-danger">El nombre de usuario no puede contener espacios</div>';
			} elseif (strlen ( $usuario ) < 5) {
				$error = '<div class="alert alert-danger">El nombre de usuario debe tener como mínimo 5 caracteres</div>';
			} elseif (strlen ( $password ) < 5) {
				$error = '<div class="alert alert-danger">El password debe tener como mínimo 5 caracteres</div>';
			} elseif ($this->Admin_usuarios_model->existeUsuario ( $usuario )) {
				$error = '<div class="alert alert-danger">El usuario ya existe, por favor elige otro nombre</div>';
			} elseif ($this->Admin_usuarios_model->existeUsuarioEmail ( $email )) {
				$error = '<div class="alert alert-danger">Ya existe una cuenta vinculada a esta dirección de correo</div>';
			} elseif ($password != $password2) {
				$error = '<div class="alert alert-danger">Los Passwords no coinciden!</div>';
			} else {
				
				$retorno = $this->Admin_usuarios_model->crear_usuario ( $usuario, $password, $email, $rol, $enviarmail );
				
				if ($retorno != 'ok') {
					$error = '<div class="alert alert-danger">No se ha podido enviar el email de validación, verifique el campo E-mail</div>';
				}
			}
		} else {
			$error = '<div class="alert alert-danger">Campos vacíos!</div>';
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function listar() {
		$this->auth->tieneAcceso ( 'usuarios_ver_listado' );
		$start = $this->input->post ( 'start' ) - 1;
		$max_results = 30;
		$start = $max_results * $start;
		$end = $max_results;
		$cantidad = 0;
		
		$search = $this->input->post ( 'search', TRUE );
		
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_usuarios_model->listarUsuarios ( $start, $end, $cantidad, $search );
		
		if ($cantidad > 0) {
			$this->output->set_output ( json_encode ( array (
					'listado' => $ret 
			) ) );
		} else {
			$this->output->set_output ( 'vacio' );
		}
	}
	function modificar() {
		$this->auth->tieneAcceso ( 'usuarios_modificar' );
		$id = $this->input->post ( 'id', TRUE );
		$email = $this->input->post ( 'email', TRUE );
		$password = $this->input->post ( 'password', TRUE );
		$password2 = $this->input->post ( 'password2', TRUE );
		$rol = $this->input->post ( 'rol', TRUE );
		
		$cuentaactiva = $this->input->post ( 'cuentaactiva', TRUE );
		
		if ($cuentaactiva == 1)
			$cuentaactiva = 1;
		else
			$cuentaactiva = 0;
		
		$error = '';
		
		if (($password != '') and (strlen ( $password ) < 5)) {
			$error = '<div class="alert alert-danger">El password debe tener como mínimo 5 caracteres</div>';
		} elseif ($this->Admin_usuarios_model->existeUsuarioEmail ( $email, $id )) {
			$error = '<div class="alert alert-danger">Ya existe una cuenta vinculada a esta dirección de correo</div>';
		} elseif (($password != '') and ($password != $password2)) {
			$error = '<div class="alert alert-danger">Los Passwords no coinciden!</div>';
		} else {
			
			$retorno = $this->Admin_usuarios_model->modificar_usuario ( $id, $password, $email, $rol, $cuentaactiva );
			
			if (! $retorno == 'ok') {
				$error = '<div class="alert alert-danger">No se ha podido enviar el email de validación, verifique el campo E-mail</div>';
			}
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function existeUsuario() {
		$usuario = $this->input->post ( 'usuario', TRUE );
		
		echo json_encode ( array (
				'valid' => ! $this->Admin_usuarios_model->existeUsuario ( $usuario ) 
		) );
	}
	function existeUsuarioEmail() {
		$email = $this->input->post ( 'email', TRUE );
		$id = $this->input->post ( 'id', TRUE );
		
		echo json_encode ( array (
				'valid' => ! $this->Admin_usuarios_model->existeUsuarioEmail ( $email, $id ) 
		) );
	}
	function obtenerTotal() {
		$search = $this->input->post ( 'search', TRUE );
		$ret = $this->Admin_usuarios_model->obtenerCantidadUsuarios ( $search );
		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( array (
				'cantidad' => $ret 
		) ) );
	}
	function borrar() {
		$this->auth->tieneAcceso ( 'usuarios_eliminar' );
		$id = $this->input->post ( 'id', TRUE );
		// if ($this->Admin_model->newsbelongs($id)){
		$this->Admin_usuarios_model->borrarUsuario ( $id );
		$this->output->set_output ( 'ok' );
		// }else{
		// $this->output->set_output('no_belong');
		// }
	}
}

?>