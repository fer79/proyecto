<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_usuarios_model extends CI_Model {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_model' );
	}
	function crear_usuario($usuario, $password, $email, $id_rol = '2', $enviarmail = TRUE) {
		$new = $this->auth->nuevoKey (); // GENERAMOS NUEVO HASH

		$this->load->library ( 'email' );

		$this->email->from ( $this->config->item('cron_email_from_email'), 'Sistema de Inscripciones' );
		$this->email->to ( $email );
		$base_url = base_url ();
		$urlval = base_url () . 'login';
		$message = <<<HTML
Felicitaciones $usuario! Te has registrado en El sistema de Inscripciones correctamente.\n
Los datos de tu cuenta son los siguientes:\n
Usuario: $usuario \n
Contraseña: $password \n
Login: $urlval

HTML;

		$this->email->subject ( $usuario . ': Cuenta creada' );
		$this->email->message ( $message );

		if (($enviarmail) and (! $error = $this->email->send ())) {

			return 'not_sent';
		} else {
			$data = array (
					'usuario' => $usuario,
					'password' => sha1 ( $password ),
					'nombre' => $usuario,
					'email' => $email,
					'activo' => 1,
					'key_validacion' => '',
					'fecha_creacion' => date ( 'Y-m-d H:m:s' )
			);
			$this->db->insert ( 'usuarios', $data );
			$last_id = $this->db->insert_id ();
			$this->db->insert ( 'usuarios_roles', array (
					'id_usuario' => $last_id,
					'id_rol' => $id_rol
			) );
			return 'ok';
		}
	}
	function modificar_usuario($id, $password, $email, $id_rol = '2', $cuentaactiva = 1) {
		$data = array (
				'email' => $email,
				'activo' => $cuentaactiva
		);

		if ($password != '') // Si no es vacío es porque no se cambió
			$data ['password'] = sha1 ( $password );

		$this->db->where ( 'id', $id )->update ( 'usuarios', $data );

		$this->db->where ( 'id_usuario', $id )->update ( 'usuarios_roles', array (
				'id_rol' => $id_rol
		) );

		return 'ok';
	}
	function modificar_misdatos($id, $password, $email, $nombre = '', $apellidos = '', $ci = '', $f_nacimiento = '', $ciudadania = '', $residencia = '', $telefono = '', $fax = '', $celular = '', $direccion = '', $ciudad = '', $departamento = '', $cpostal = '', $web = '', $formacionacademica = '', $centrodetitulacion = '', $f_titulacion = '') {
		$data = array (
				'email' => $email,
				'nombre' => $nombre,
				'apellidos' => $apellidos,
				'ci' => $ci,
				'f_nacimiento' => $f_nacimiento,
				'ciudadania' => $ciudadania,
				'residencia' => $residencia,
				'telefono' => $telefono,
				'fax' => $fax,
				'celular' => $celular,
				'direccion' => $direccion,
				'ciudad' => $ciudad,
				'departamento' => $departamento,
				'cpostal' => $cpostal,
				'web' => $web,
				'formacionacademica' => $formacionacademica,
				'centrodetitulacion' => $centrodetitulacion,
				'f_titulacion' => $f_titulacion
		);

		if ($password != '') // Si no es vacío es porque no se cambió
			$data ['password'] = sha1 ( $password );

		$this->db->where ( 'id', $id )->update ( 'usuarios', $data );

		return 'ok';
	}
	function existeUsuarioEmail($email = '', $id = '', $ynoesmia = false) {
		$this->db->select ( 'id' );
		$this->db->from ( 'usuarios' );
		$this->db->where ( 'email', $email );

		if ($ynoesmia)
			$this->db->where ( 'id !=', $id );

		$q = $this->db->get ();

		if ($q->num_rows () >= 1) {

			if ($id != '') {
				if ($q->row ()->id == $id) {
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	function existeUsuario($usuario = '') {
		$this->db->select ( 'id' );
		$this->db->from ( 'usuarios' );
		$this->db->where ( 'usuario', $usuario );

		$q = $this->db->get ();

		if ($q->num_rows () >= 1) {

			return true;
		} else {
			return false;
		}
	}
	function obtenerUsuarioByEmail($email = '') {
		$this->db->select ( 'id' );
		$this->db->from ( 'usuarios' );
		$this->db->where ( 'email', $email );

		$q = $this->db->get ();

		if ($q->num_rows () >= 1) {

			return $q->result ();
		} else {
			return false;
		}
	}
	function listarUsuarios($start = 1, $end, &$cantidad, $search = '') {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'usuarios' );

		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'usuario', $search, false );
				$this->db->or_like ( 'nombre', $search, false );
				$this->db->or_like ( 'email', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (usuario,nombre,email) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE)', NULL, false );
			}
		}

		$this->db->order_by ( 'nombre', 'ASC' );
		$this->db->limit ( $end, $start );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {
			$retorno [] = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'usuario' => $row->usuario,
					'email' => $row->email
			);
		}

		$cantidad = $this->db->count_all_results ();
		return $retorno;
	}
	function obtenerRoles() {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'roles' );
		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retorno [] = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'permisos' => $row->permisos
			);
		}

		return $retorno;
	}
	function obtenerInfoUsuarios($id = '') {
		$retorno = array ();
		// info principal
		$this->db->select ( 'usuarios.id,nombre,apellidos,ci,f_nacimiento,ciudadania,residencia,telefono,fax,celular,direccion,ciudad,departamento,cpostal,web,formacionacademica,centrodetitulacion,f_titulacion,usuario,email,fecha_creacion,activo,usuarios_roles.id_rol as id_rol' );
		$this->db->from ( 'usuarios,usuarios_roles' );
		$this->db->where ( 'usuarios.id', $id );
		$this->db->where ( 'id_usuario', $id );
		$q = $this->db->get ();

		if ($q->num_rows () == 1) {

			$row = $q->row ();

			$retorno = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'apellidos' => $row->apellidos,
					'ci' => $row->ci,
					'f_nacimiento' => $row->f_nacimiento,
					'ciudadania' => $row->ciudadania,
					'residencia' => $row->residencia,
					'telefono' => $row->telefono,
					'fax' => $row->fax,
					'celular' => $row->celular,
					'direccion' => $row->direccion,
					'ciudad' => $row->ciudad,
					'departamento' => $row->departamento,
					'cpostal' => $row->cpostal,
					'web' => $row->web,
					'formacionacademica' => $row->formacionacademica,
					'centrodetitulacion' => $row->centrodetitulacion,
					'f_titulacion' => $row->f_titulacion,

					'usuario' => $row->usuario,
					'rol' => $row->id_rol,
					'email' => $row->email,
					'fecha_creacion' => $row->fecha_creacion,
					'activo' => $row->activo
			)
			;

			return $retorno;
		} else {

			return false;
		}
	}
	function obtenerCantidadUsuarios($search = '') {
		$this->db->select ();
		$this->db->from ( 'usuarios' );

		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'usuario', $search, false );
				$this->db->or_like ( 'nombre', $search, false );
				$this->db->or_like ( 'email', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (usuario,nombre,email) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE) as relevance', NULL, false );
				$this->db->order_by ( 'relevance', 'desc' );
			}
		}

		return $this->db->count_all_results ();
	}
	function borrarUsuario($id = 0) {
		$this->db->delete ( 'usuarios', array (
				'id' => $id
		) );
	}
}

?>
