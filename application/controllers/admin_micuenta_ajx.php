<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_micuenta_ajx extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->auth->tieneAcceso ();
		
		$this->load->model ( 'Admin_usuarios_model' );
		$this->load->model ( 'Form_validate_model' );
	}
	function modificar() {
		$id = $this->auth->id_usuario ();
		$email = $this->input->post ( 'email', TRUE );
		$password = $this->input->post ( 'password', TRUE );
		$password2 = $this->input->post ( 'password2', TRUE );
		$nombre = $this->input->post ( 'nombre', TRUE );
		$apellidos = $this->input->post ( 'apellidos', TRUE );
		$ci = $this->input->post ( 'ci', TRUE );
		$f_nacimiento = $this->input->post ( 'f_nacimiento', TRUE );
		$ciudadania = $this->input->post ( 'ciudadania', TRUE );
		$residencia = $this->input->post ( 'residencia', TRUE );
		$telefono = $this->input->post ( 'telefono', TRUE );
		$fax = $this->input->post ( 'fax', TRUE );
		$celular = $this->input->post ( 'celular', TRUE );
		$direccion = $this->input->post ( 'direccion', TRUE );
		$ciudad = $this->input->post ( 'ciudad', TRUE );
		$departamento = $this->input->post ( 'departamento', TRUE );
		$cpostal = $this->input->post ( 'cpostal', TRUE );
		$web = $this->input->post ( 'web', TRUE );
		$formacionacademica = $this->input->post ( 'formacionacademica', TRUE );
		$centrodetitulacion = $this->input->post ( 'centrodetitulacion', TRUE );
		$f_titulacion = $this->input->post ( 'f_titulacion', TRUE );
		
		$error = '';
		if (($password != '') and (strlen ( $password ) < 5)) {
			$error = '<div class="alert alert-danger">El password debe tener como mínimo 5 caracteres</div>';
		} elseif ($this->Admin_usuarios_model->existeUsuarioEmail ( $email, $id, true )) {
			$error = '<div class="alert alert-danger">Ya existe una cuenta vinculada a esta dirección de correo</div>';
		} elseif (($password != '') and ($password != $password2)) {
			$error = '<div class="alert alert-danger">Los Passwords no coinciden!</div>';
		} else {
			
			$retorno = $this->Admin_usuarios_model->modificar_misdatos ( $id, $password, $email, $nombre, $apellidos, $ci, $f_nacimiento, $ciudadania, $residencia, $telefono, $fax, $celular, $direccion, $ciudad, $departamento, $cpostal, $web, $formacionacademica, $centrodetitulacion, $f_titulacion );
			
			if (! $retorno == 'ok') {
				$error = '<div class="alert alert-danger">No se ha podido enviar el email de validación, verifique el campo E-mail</div>';
			}
		}
		
		if ($error == '')
			$this->output->set_output ( json_encode ( 'ok' ) );
		else
			
			$this->output->set_output ( json_encode ( $error ) );
	}
	function existeUsuarioEmail() {
		$email = $this->input->post ( 'email', TRUE );
		$id = $this->auth->id_usuario ();
		
		echo json_encode ( array (
				'valid' => ! $this->Admin_usuarios_model->existeUsuarioEmail ( $email, $id, TRUE ) 
		) );
	}
}

?>