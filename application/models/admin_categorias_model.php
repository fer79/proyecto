<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_categorias_model extends CI_Model {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_model' );
	}
	function crear_categoria($nombre, $padre) {
		$data = array (
				'nombre' => $nombre,
				'padre' => $padre 
		);
		
		$this->db->insert ( 'categorias', $data );
		$last_id = $this->db->insert_id ();
		
		return 'ok';
	}
	function modificar_categoria($nombre, $padre, $id) {
		$data = array (
				'nombre' => $nombre,
				'padre' => $padre 
		);
		
		$this->db->where ( 'id', $id )->update ( 'categorias', $data );
		
		return 'ok';
	}
	function listarCategorias($start = 1, $end, &$cantidad, $search = '') {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'categorias' );
		$this->db->where ( 'padre !=', '0' );
		
		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'nombre', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (nombre) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE)', NULL, false );
			}
		}
		
		$this->db->order_by ( 'id,padre', 'ASC' );
		$this->db->limit ( $end, $start );
		
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			$retorno [] = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'padre' => $row->padre 
			);
		}
		
		$cantidad = $this->db->count_all_results ();
		return $retorno;
	}
	private function obtenerCategoriasHijos($cat_id = 0, &$yaAgregados) {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'categorias' );
		$this->db->where ( 'categorias.padre', $cat_id );
		$this->db->order_by ( 'padre', 'ASC' );
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			if (! in_array ( $row->id, $yaAgregados )) {
				$hijos = $this->obtenerCategoriasHijos ( $row->id, $yaAgregados );
				$retorno [] = array (
						'id' => $row->id,
						'nombre' => $row->nombre,
						'hijos' => $hijos 
				);
				$yaAgregados [] = $row->id;
			}
		}
		
		return $retorno;
	}
	
	/*
	 *
	 * Obtenemos las categorias. Por defecto todo el mundo tiene General, quiere decir que hereda hacia abajo permisos a todas.
	 * Si agregamos gente a una lista en particular, pierde los permisos de general, pero hereda hacia abajo.
	 * Para darle permisos generales hay que eliminarlo de todas las otras categorias.
	 * beso.
	 *
	 */
	function obtenerCategoriasUsuario() {
		$retorno = array ();
		
		if (! $this->auth->es_admin ()) {
			$this->db->select ();
			$this->db->from ( 'categorias,categorias_usuarios' );
			$this->db->where ( 'id_categoria', 'categorias.id', FALSE );
			$this->db->where ( 'id_usuario', $this->auth->id_usuario () );
		} else {
			$this->db->select ();
			$this->db->from ( 'categorias' );
		}
		
		$this->db->order_by ( 'padre', 'ASC' );
		$q = $this->db->get ();
		
		$yaAgregados = array ();
		foreach ( $q->result () as $row ) {
			if (! in_array ( $row->id, $yaAgregados )) {
				$hijos = $this->obtenerCategoriasHijos ( $row->id, $yaAgregados );
				$retorno [] = array (
						'id' => $row->id,
						'nombre' => $row->nombre,
						'hijos' => $hijos 
				);
				$yaAgregados [] = $row->id;
			}
		}
		
		return $retorno;
	}
	function obtenerCaminoDesdeCat($cat_id = 0, &$retorno) {
		$this->db->select ();
		$this->db->from ( 'categorias' );
		$this->db->where ( 'id', $cat_id );
		
		$q = $this->db->get ();
		
		if ($q->num_rows () == 1) {
			$row = $q->row ();
			
			$this->obtenerCaminoDesdeCat ( $row->padre, $retorno );
			$retorno [] = array (
					'id' => $row->id,
					'nombre' => $row->nombre 
			);
		}
	}
	
	/*
	 *
	 * Obtenemos las categorias para mostrar en el crear.
	 *
	 *
	 */
	function obtenerCategorias($id = '', $cat_id_buscar = '') {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'categorias' );
		$this->db->order_by ( 'padre', 'ASC' );
		
		if ($id != '')
			$this->db->where ( 'id !=', $id );
		
		if ($cat_id_buscar != '') // Solo traemos esa cat y sus hijas
			$this->db->where ( 'id', $cat_id_buscar );
		
		$q = $this->db->get ();
		
		$yaAgregados = array ();
		foreach ( $q->result () as $row ) {
			if (! in_array ( $row->id, $yaAgregados )) {
				$hijos = $this->obtenerCategoriasHijos ( $row->id, $yaAgregados );
				$retorno [] = array (
						'id' => $row->id,
						'nombre' => $row->nombre,
						'hijos' => $hijos 
				);
				$yaAgregados [] = $row->id;
			}
		}
		
		return $retorno;
	}
	function categoriaInfo($id = '') {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'categorias' );
		$this->db->where ( 'id', $id );
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			$retorno = array (
					'id' => $row->id,
					'nombre' => $row->nombre,
					'padre' => $row->padre 
			);
		}
		
		return $retorno;
	}
	function obtenerCantidadCategorias($search = '') {
		$this->db->select ();
		$this->db->from ( 'categorias' );
		
		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'nombre', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (nombre)  as relevance AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE)', NULL, false );
				$this->db->order_by ( 'relevance', 'desc' );
			}
		}
		
		return $this->db->count_all_results ();
	}
	function existeCategoriaConId($id = 0) {
		$this->db->select ( 'id' );
		$this->db->from ( 'categorias' );
		
		$this->db->where ( 'id', $id );
		
		$q = $this->db->get ();
		
		return $q->num_rows () > 0;
	}
	function existeCategoria($nombre = '', $padre = 0, $id = '') {
		$this->db->select ( 'id' );
		$this->db->from ( 'categorias' );
		$this->db->where ( 'nombre', $nombre );
		$this->db->where ( 'padre', $padre );
		
		if ($id != '')
			$this->db->where ( 'id !=', $id );
		
		$q = $this->db->get ();
		
		return $q->num_rows () > 0;
	}
	function borrar_usuario($id, $idUsuario) {
		$this->db->delete ( 'categorias_usuarios', array (
				'id_categoria' => $id,
				'id_usuario' => $idUsuario 
		) );
		
		return 'ok';
	}
	function agregar_usuario($id, $idUsuario) {
		$permisos = array ();
		$data = array (
				'id_categoria' => $id,
				'id_usuario' => $idUsuario 
		);
		
		$this->db->insert ( 'categorias_usuarios', $data );
		$last_id = $this->db->insert_id ();
		
		return 'ok';
	}
	function buscarUsuarios($term) {
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
	function obtenerUsuarios($id, $disminuida = false) {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'categorias_usuarios as usuc' );
		$this->db->join ( 'usuarios as usu', 'usu.id = usuc.id_usuario', 'left' );
		$this->db->where ( 'id_categoria', $id );
		
		$q = $this->db->get ();
		
		foreach ( $q->result () as $row ) {
			
			if (! $disminuida)
				$retorno [] = array (
						'id' => $row->id_usuario,
						'usuario' => $row->usuario,
						'email' => $row->email,
						'nombre' => $row->nombre,
						'apellidos' => $row->apellidos 
				);
			else
				$retorno [] = $row->id_usuario;
		}
		
		return $retorno;
	}
	function borrarCategoria($id) {
		$this->db->where ( 'categoria', $id )->update ( 'formularios', array (
				'categoria' => 1 
		) ); // Pasamos todos los formularios al primer nivel
		$this->db->where ( 'padre', $id )->update ( 'categorias', array (
				'padre' => 1 
		) ); // Pasamos todos los hijos al primer nivel.
		
		$this->db->delete ( 'categorias', array (
				'id' => $id 
		) );
		$this->db->delete ( 'categorias_usuarios', array (
				'id_categoria' => $id 
		) );
		return 'ok';
	}
}

?>