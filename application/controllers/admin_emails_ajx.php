<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Admin_emails_ajx extends CI_Controller {

	function __construct() {
		parent::__construct ();

		$this->load->model ( 'Admin_emails_model' );
		$this->load->model ( 'Form_validate_model' );

		$this->Admin_emails_model = new Admin_emails_model ();
	}


	function listar() {

		$this->auth->tieneAcceso ( 'formularios_ver_listado' );
		$idFormulario = $this->input->post('id');
		$start = $this->input->post ( 'start' ) - 1;
		$max_results = 30;
		$start = $max_results * $start;
		$end = $max_results;
		$cantidad = 0;

		$search = $this->input->post ( 'search', TRUE );

		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_emails_model->listarEmails($idFormulario, $start, $end, $cantidad, $search);

		if ($cantidad > 0) {
			$this->output->set_output ( json_encode ( array (
					'listado' => $ret
			) ) );
		} else {
			$this->output->set_output ( 'vacio' );
		}
	}



	function obtenerTotal() {

		$idFormulario = $this->input->post('id');
		$search = $this->input->post('search', TRUE);
		$ret = $this->Admin_emails_model->obtenerCantidadEmails($idFormulario, $search);
		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( array (
				'cantidad' => $ret
		) ) );
	}


}

?>
