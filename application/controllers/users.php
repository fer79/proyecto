<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Users extends CI_Controller {
	function __construct() {
		parent::__construct ();
		// $this->load->helper('recaptchalib');
	}
	function ver_login() {
		$referer = $this->auth->referer ();

		if (! $this->auth->tieneAcceso ( '', true )) {
			if ($this->input->post ())
				$this->login ( $referer );
			else
				$this->load->view ( 'login_view' );
		} else {
			redirect ( base_url () );
		}
	}
	function reset_password() {
		if ($this->auth->tieneAcceso ( '', true )) {
			redirect ( base_url () );
			exit ();
		}

		$data = new stdClass ();
		$data->error = '';

		if (! $this->auth->logged_in ()) {
			if (! $this->input->post ( 'submit' )) {
				$this->load->view ( 'password_olvidado_view', $data );
			} else {

				$email = $this->input->post ( 'email', TRUE );
				$recaptcha_response_field = $this->input->post ( 'g-recaptcha-response' );

				if ($this->auth->checkRecaptcha ( $recaptcha_response_field ) == false) {
					$data->error = '<div class="alert alert-danger">Captcha incorrecto!</div>';
				} else {

					if (! $this->auth->existe_email ( $email )) {

						$data->error = '<div class="alert alert-danger">No existe una cuenta vinculada a esta dirección de correo</div>';
						$data->email = '';
					} else {
						$respuesta = $this->auth->enviar_cambiar_password ( $email );

						if ($respuesta == 'not_sent') {
							$data->error = '<div class="alert alert-danger">No se ha podido enviar el E-mail de cambio de contraseña, por favor intenta nuevamente o contacta un administrador.</div>';
							$data->email = '';
						} else {
							$data->error = '<div class="alert alert-success">Revisa tu casilla de correo. Hemos enviado un correo con los datos de tu nueva contraseña</div>';
						}
					}
				}
				$this->load->view ( 'password_olvidado_view', $data );
			}
		} else {
			redirect ( base_url () );
		}
	}
	function reenviar_validacion($email = '') {
		$data = new StdClass ();
		if ($this->auth->tieneAcceso ( '', true )) {
			redirect ( base_url () );
			exit ();
		}
		$email = $this->security->xss_clean ( $email );

		if ($retorno = $this->auth->reenviar_validacion ( $email )) {

			if ($retorno == 'activo') {
				$data->error = '<div class="alert alert-danger">El usuario ya fue activado!</div>';
			} elseif ($retorno == 'not_sent') {
				$data->error = '<div class="alert alert-danger">No se ha podido enviar el mail de activación. <a href="' . base_url () . 'usuario/revalidar/' . $email . '">Enviar E-mail nuevamente</a></div>';
			} elseif ($retorno == 'bad_email') {
				$data->error = '<div class="alert alert-danger">El E-mail es incorrecto o no existe.';
			} else {

				$data->error = '<div class="alert alert-success">E-mail de enviado. <br> Revisa tu casilla de correo, recibirás las instrucciones para validar tu cuenta. Por las dudas, revisa SPAM o sino <br> <a href="' . base_url () . 'usuario/revalidar/' . $email . '">Enviar E-mail nuevamente</a></div>';
			}
		}

		$this->load->view ( 'registrarse_success_view', $data );
	}
	function validar($hash = '', $email = '') {
		$data = new StdClass ();
		if ($this->auth->tieneAcceso ( '', true )) {
			redirect ( base_url () );
			exit ();
		}
		$hash = $this->security->xss_clean ( $hash );
		$email = $this->security->xss_clean ( $email );

		$data->error = '';

		if ($retorno = $this->auth->validar_cuenta ( $hash, $email )) {
			if ($retorno == 'activo') {
				$data->error = '<div class="alert alert-danger">El usuario ya fue activado!</div>';
			} elseif ($retorno == 'bad_hash') {
				$data->error = '<div class="alert alert-danger">El código de activación es incorrecto <a href="' . base_url () . 'usuario/revalidar/' . $email . '">Enviar E-mail nuevamente</a></div>';
			} elseif ($retorno == 'bad_email') {
				$data->error = '<div class="alert alert-danger">El E-mail es incorrecto o no existe.';
			} elseif ($retorno == 'not_sent') {
				$data->error = '<div class="alert alert-danger">No se ha podido enviar el mail de activación. <a href="' . base_url () . 'usuario/revalidar/' . $email . '">Enviar E-mail nuevamente</a></div>';
			} else {

				$data->error = '<div class="alert alert-success">Tu cuenta ha sido validada.</div>';
			}
		}

		$this->load->view ( 'registrarse_success_view', $data );
	}
	function registrarse() {
		$data = new StdClass ();
		if ($this->auth->tieneAcceso ( '', true )) {
			redirect ( base_url () );
			exit ();
		}
		if (! $this->auth->logged_in ()) {
			$data->error = '';
			$data->success = false;
			$data->username = '';
			$data->email = '';

			if (! $this->input->post ( 'submit' )) {
				$this->load->view ( 'registrarse_view', $data );
			} else {
				$username = trim ( $this->input->post ( 'username', TRUE ) );
				$password = $this->input->post ( 'password', TRUE );
				$password2 = $this->input->post ( 'password2', TRUE );
				$email = trim ( $this->input->post ( 'email', TRUE ) );
				$recaptcha_response_field = $this->input->post ( 'g-recaptcha-response' );
				$data->username = $username;
				$data->email = $email;

				if ($this->auth->checkRecaptcha ( $recaptcha_response_field ) == false) {
					$data->error = '<div class="alert alert-danger">Captcha incorrecto!</div>';
				} else {
					if ($username && $password && $password2 && $email) {
						if (preg_match ( '/\s/', $username ) >= 1) {
							$data->error = '<div class="alert alert-danger">El nombre de usuario no puede contener espacios</div>';
							$data->username = '';
						} elseif (strlen ( $username ) < 5) {
							$data->error = '<div class="alert alert-danger">El nombre de usuario debe tener como mínimo 5 caracteres</div>';
							$data->username = '';
						} elseif (strlen ( $password ) < 5) {
							$data->error = '<div class="alert alert-danger">El password debe tener como mínimo 5 caracteres</div>';
						} elseif ($this->auth->existe_usuario ( $username )) {
							$data->username = '';
							$data->error = '<div class="alert alert-danger">El usuario ya existe, por favor elige otro nombre</div>';
							$data->username = '';
						} elseif ($this->auth->existe_email ( $email )) {

							$data->error = '<div class="alert alert-danger">Ya existe una cuenta vinculada a esta dirección de correo</div>';
							$data->email = '';
						} elseif ($password != $password2) {

							$data->error = '<div class="alert alert-danger">Los Passwords no coinciden!</div>';
						} else {

							$retorno = $this->auth->crear_usuario ( $username, $password, $email );
							if (! $retorno == 'ok') {
								$data->error = '<div class="alert alert-danger">No se ha podido enviar el email de validación, verifique el campo E-mail</div>';
							} else {
								$data->success = true;
								$data->error = '<div class="alert alert-success">Felicitaciones <b><?php echo $username ?></b>! Te has registrado en el Sistema de Inscripciones correctamente.<br>
								Solo resta un paso más, validar tu cuenta. Revisa tu casilla de correo, recibirás las instrucciones.<a href="' . base_url () . 'usuario/revalidar/' . $email . '">Enviar E-mail nuevamente</a></div>';
							}
						}
					} else {
						$data->error = '<div class="alert alert-danger">Campos vacíos!</div>';
					}
				}

				if ($data->success)
					$this->load->view ( 'registrarse_success_view', $data );
				else
					$this->load->view ( 'registrarse_view', $data );
			}
		} else {
			redirect ( base_url () );
		}
	}
	function login($referer = '') {
		if ($this->auth->tieneAcceso ( '', true )) {
			redirect ( base_url () );
			exit ();
		}

		$username = $this->input->post ( 'username', TRUE );
		$password = $this->input->post ( 'password', TRUE );

		$data = new stdClass ();

		if (! empty ( $username ) and ! empty ( $password )) {
			if ($userdata = $this->auth->try_login ( $username, $password )) {
				if ($userdata == 'inactivo') {

					$email = $this->auth->email_usuario ( array (
							'usuario' => $username
					) );

					$data->error = '<div class="alert alert-danger">La cuenta no ha sido activada aún. Verifica tu casilla de correo<br>
							Si no has recibido el E-mail de validación <a href="' . base_url () . 'usuario/revalidar/' . $email . '">Envia el E-mail nuevamente</a></div></div>';
					$this->load->view ( 'login_view', $data );
				} else {
					if ($referer == '')
						redirect ( base_url () );
					else
						redirect ( $referer );
				}
			} else {
				$data->error = '<div class="alert alert-danger">Usuario o Contraseña incorrectos</div>';
				$this->load->view ( 'login_view', $data );
			}
		} else {

			$data->error = '<div class="alert alert-danger">Campos vacíos!</div>';
			$this->load->view ( 'login_view', $data );
		}
	}
	function logout() {
		if (! $this->auth->tieneAcceso ( '', true )) {
			redirect ( base_url () );
			exit ();
		}
		$this->auth->logout ( 'login' );
		redirect ( base_url () );
	}
}

?>
