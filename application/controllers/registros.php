<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Registros extends CI_Controller {

	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_formularios_model' );
		$this->load->model ( 'Admin_registros_model' );

		$this->Admin_formularios_model = new Admin_formularios_model ();
		$this->Admin_registros_model = new Admin_registros_model ();
	}


	function listar($idFormulario = '') {

		$this->auth->tieneAcceso('registros_ver_listado');

		$data = array ();
		$data ['idFormulario'] = $idFormulario;
		$data ['formularios'] = $this->Admin_formularios_model->obtenerInfoFormularios ( $idFormulario );
		$data ['hasFormVinculado'] = !empty($this->Admin_formularios_model->obtenerEvaluacionVinculada($idFormulario));
		$data ['formularios'] ['cantidadRegistros'] = $this->Admin_registros_model->obtenerCantidadRegistros ( $idFormulario );
		if (! $this->Admin_formularios_model->esDeUsuarioFormulario ( $idFormulario ) and ! $this->auth->tengoPermisoDeCompanero ( 'registros_ver_listado', $data ['formularios'] ['id_usuario'] )) {

			$this->output->set_output ( 'no_belong' );
		} else {

			$data ['camposmostrar'] = $this->Admin_formularios_model->obtenerCamposMostrar ( $idFormulario );
			$data ['camposExportar'] = $this->Admin_formularios_model->obtenerCamposExportar ( $idFormulario );

			$mensajes = $this->Admin_formularios_model->obtenerMensajeSorteoPlaza($data['formularios']);

			$data ['mensajeMail'] = $mensajes['mail'];
			$data ['mensajeNOMail'] = $mensajes['nomail'];

			$this->load->view ( 'admin/_header', $data );
			$this->load->view ( 'admin/_sidebar', $data );
			if ($data ['formularios'] ['tipo'] == 'evaluacion') {

				$data ['resumen'] = $this->Admin_registros_model->generarResumen ( $idFormulario );

				$this->load->view ( 'admin/registros_listar_evaluacion_view', $data );
			} else {

				$this->load->view ( 'admin/registros_listar_view', $data );
			}
			$this->load->view ( 'admin/_footer', $data );
		}
	}

	function misinscripciones() {
		$this->auth->tieneAcceso ();

		$data = array ();
		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/misinscripciones_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}


	function ver($idRegistro = '') {

		$this->auth->tieneAcceso ( 'registros_ver_completo' );
		$data = array ();

		$data ['ret'] = $this->Admin_registros_model->obtenerInfoRegistro ( $idRegistro );
		$data ['formularios'] = $this->Admin_formularios_model->obtenerInfoFormularios ( $data ['ret'] ['idForm'] );

		if (! $this->Admin_formularios_model->esDeUsuarioFormulario ( $data ['ret'] ['idForm'] ) and ! $this->auth->tengoPermisoDeCompanero ( 'registros_ver_completo', $data ['formularios'] ['id_usuario'] )) {

			$this->output->set_output ( 'no_belong' );
		} else {
			$data ['idFormulario'] = $idRegistro;

			$this->load->view ( 'admin/_header', $data );
			$this->load->view ( 'admin/_sidebar', $data );
			$this->load->view ( 'admin/registros_ver_view', $data );
			$this->load->view ( 'admin/_footer', $data );
		}
	}


	function vermiregistro($idRegistro = '') {

		$this->auth->tieneAcceso ();
		$data = array ();

		$data ['ret'] = $this->Admin_registros_model->obtenerInfoRegistro ( $idRegistro );
		$data ['formularios'] = $this->Admin_formularios_model->obtenerInfoFormularios ( $data ['ret'] ['idForm'] );

		if (! $this->Admin_formularios_model->esDeUsuarioFormulario ( $data ['ret'] ['idForm'] ) and ! $this->auth->tengoPermisoDeCompanero ( 'registros_ver_completo', $data ['formularios'] ['id_usuario'] )) {

			$this->output->set_output ( 'no_belong' );
		} else {
			$data ['idFormulario'] = $idRegistro;

			$this->load->view ( 'admin/_header', $data );
			$this->load->view ( 'admin/_sidebar', $data );
			$this->load->view ( 'admin/registros_ver_mi_registro_view', $data );
			$this->load->view ( 'admin/_footer', $data );
		}
		
	}

}

?>
