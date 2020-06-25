<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_usuarios_roles_model extends CI_Model {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_model' );
	}
	function crear_rol($nombre, $permisos) {
		$data = array (
				'nombre' => $nombre,
				'permisos' => json_encode ( $permisos ) 
		);
		
		$this->db->insert ( 'roles', $data );
		$last_id = $this->db->insert_id ();
		
		return 'ok';
	}
	function modificar_rol($id, $nombre, $permisos) {
		$data = array (
				'nombre' => $nombre,
				'permisos' => json_encode ( $permisos ) 
		);
		
		$this->db->where ( 'id', $id )->update ( 'roles', $data );
		
		return 'ok';
	}
	function modificar_companero($id, $id_companero, $permisos) {
		$data = array (
				'permisos' => json_encode ( $permisos ) 
		);
		
		$this->db->where ( 'id_usuario', $id )->where ( 'id_companero', $id_companero )->update ( 'usuarios_companeros', $data );
		
		return 'ok';
	}
	function borrar_companero($id, $id_companero, $permisos) {
		$this->db->delete ( 'usuarios_companeros', array (
				'id_usuario' => $id,
				'id_companero' => $id_companero 
		) );
		
		return 'ok';
	}
	function agregar_companero($id, $id_companero) {
		$permisos = array ();
		$data = array (
				'id_usuario' => $id,
				'id_companero' => $id_companero,
				'permisos' => json_encode ( $permisos ) 
		);
		
		$this->db->insert ( 'usuarios_companeros', $data );
		$last_id = $this->db->insert_id ();
		
		return 'ok';
	}
	function obtenerCompaneros($id, $disminuida = false) {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'usuarios_companeros as usuc' );
		$this->db->join ( 'usuarios as usu', 'usu.id = usuc.id_companero', 'left' );
		$this->db->where ( 'id_usuario', $id );
		
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			
			if (! $disminuida)
				$retorno [] = array (
						'id' => $row->id,
						'id_companero' => $row->id_companero,
						'usuario' => $row->usuario,
						'email' => $row->email,
						'nombre' => $row->nombre,
						'apellidos' => $row->apellidos,
						'permisos' => $row->permisos 
				);
			else
				$retorno [] = $row->id;
		}
		
		return $retorno;
	}
	function buscarCompaneros($term) {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'usuarios' );
		$this->db->or_like ( 'nombre', $term, false );
		$this->db->or_like ( 'email', $term, false );
		$this->db->or_like ( 'apellidos', $term, false );
		$this->db->or_like ( 'usuario', $term, false );
		
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			
			$retorno [] = array (
					'id' => $row->id,
					'usuario' => $row->usuario,
					'email' => $row->email,
					'nombre' => $row->nombre,
					'apellidos' => $row->apellidos 
			);
		}
		
		return $retorno;
	}
	function existeRol($rol = '', $id = '') {
		$this->db->select ( 'id' );
		$this->db->from ( 'roles' );
		$this->db->where ( 'nombre', $rol );
		
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
	function listarRoles($start = 1, $end, &$cantidad, $search = '') {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'roles' );
		
		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'nombre', $search, false );
				$this->db->or_like ( 'permisos', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (nombre,permisos) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE)', NULL, false );
			}
		}
		
		$this->db->order_by ( 'nombre', 'ASC' );
		$this->db->limit ( $end, $start );
		
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			$retorno [] = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'permisos' => $row->permisos 
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
	function obtenerInfoRoles($id = '') {
		$retorno = array ();
		// info principal
		$this->db->select ( 'id,nombre,permisos' );
		$this->db->from ( 'roles' );
		$this->db->where ( 'id', $id );
		
		$q = $this->db->get ();
		
		if ($q->num_rows () == 1) {
			
			$row = $q->row ();
			
			$retorno = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'permisos' => json_decode ( $row->permisos ) 
			);
			
			return $retorno;
		} else {
			
			return false;
		}
	}
	function obtenerCantidadRoles($search = '') {
		$this->db->select ();
		$this->db->from ( 'roles' );
		
		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'nombre', $search, false );
				$this->db->or_like ( 'permisos', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (nombre,permisos) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE) as relevance', NULL, false );
				$this->db->order_by ( 'relevance', 'desc' );
			}
		}
		
		return $this->db->count_all_results ();
	}
	function borrarRol($id = 0) {
		$this->db->delete ( 'roles', array (
				'id' => $id 
		) );
	}
}

?>