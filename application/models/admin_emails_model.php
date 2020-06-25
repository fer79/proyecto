<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Admin_emails_model extends CI_Model {

	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_model' );
	}


	function listarEmails($idFormulario, $start = 1, $end, &$cantidad, $search = '') {

		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'cron_emails' );

		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'para', $search, false );
				$this->db->or_like ( 'titulo', $search, false );
				$this->db->or_like ( 'mensaje', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (para,titulo,mensaje) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE)', NULL, false );
			}

		}
		$this->db->where('cron_emails.id_formulario', $idFormulario);
		$this->db->order_by ( 'id', 'DESC' );
		$this->db->limit ( $end, $start );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {
			$retorno [] = array (
					'id' => $row->id,
					'fechaCreacion' => $row->fecha,
					'para' => $row->para,
					'titulo' => $row->titulo,
					'mensaje' => $row->mensaje,
					'intentos' => $row->intentos,
					'enviado' => $row->enviado,
					'fechaEnvio' => $row->fecha_enviado,
					'intentos' => $row->intentos,
			);
		}

		$cantidad = $this->db->count_all_results ();
		return $retorno;
	}


	function obtenerCantidadEmails($idFormulario, $search = '') {

		$this->db->select ();
		$this->db->from ( 'cron_emails' );

		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'para', $search, false );
				$this->db->or_like ( 'titulo', $search, false );
			  $this->db->or_like ( 'mensaje', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (para,titulo,mensaje) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE) as relevance', NULL, false );
			}
		}
		$this->db->where('cron_emails.id_formulario', $idFormulario);
		$this->db->order_by ( 'id', 'desc' );


		return $this->db->count_all_results ();
	}

}

?>
