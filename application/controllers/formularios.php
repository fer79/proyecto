<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Formularios extends CI_Controller {

	function __construct() {

		parent::__construct ();
		$this->load->model ( 'Admin_formularios_model' );
		$this->load->model ( 'Admin_categorias_model' );
		$this->load->model ( 'Admin_emails_model' );
		$this->Admin_formularios_model = new Admin_formularios_model ();
		$this->Admin_categorias_model = new Admin_categorias_model ();
		$this->Admin_emails_model = new Admin_emails_model ();
	}


	function listar() {

		$this->auth->tieneAcceso ( 'formularios_ver_listado' );

		$data = array ();
		$data ['cantInscripcion'] = $this->Admin_formularios_model->obtenerCantidadFormularios ( 'inscripcion' );
		$data ['cantEvaluacion'] = $this->Admin_formularios_model->obtenerCantidadFormularios ( 'evaluacion' );
		$data ['cantEliminados'] = $this->Admin_formularios_model->obtenerCantidadFormularios ( 'eliminado' );

		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/formularios_listar_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}


	function crear() {

		$this->auth->tieneAcceso ( 'formularios_crear' );
		$data = array ();

		$data ['ret'] = $this->Admin_formularios_model->obtenerVinculables ();
		$data ['ret_inscripcion'] = json_encode ( $this->Admin_formularios_model->obtenerVinculables (), JSON_HEX_APOS );
		$data ['categorias'] = $this->Admin_categorias_model->obtenerCategoriasUsuario ();

		$this->load->view ( 'admin/_header', $data );
		$this->load->view ( 'admin/_sidebar', $data );
		$this->load->view ( 'admin/formularios_crear_view', $data );
		$this->load->view ( 'admin/_footer', $data );
	}

	function modificar($id = 0) {

		$this->auth->tieneAcceso ( 'formularios_modificar' );
		$data = array ();

		$data ['listado'] = $this->Admin_formularios_model->obtenerVinculables ();
		$data ['categorias'] = $this->Admin_categorias_model->obtenerCategoriasUsuario ();
		$data ['ret'] = $this->Admin_formularios_model->obtenerInfoFormularios ( $id );

		if (! $this->Admin_formularios_model->esDeUsuarioFormulario ( $id ) and ! $this->auth->tengoPermisoDeCompanero ( 'formularios_modificar', $data ['ret'] ['id_usuario'] )) {

			$this->output->set_output ( 'no_belong' );
		}
		else {
			$data ['ret_inscripcion'] = json_encode ( $this->Admin_formularios_model->obtenerVinculables (), JSON_HEX_APOS );
			$data ['idFormulario'] = $id;

			$this->load->view ( 'admin/_header', $data );
			$this->load->view ( 'admin/_sidebar', $data );
			$this->load->view ( 'admin/formularios_modificar_view', $data );
			$this->load->view ( 'admin/_footer', $data );
		}
	}


	function estadoEmails($id = null) {

		//Cambiar luego por permiso especifico
		$this->auth->tieneAcceso ( 'formularios_ver_listado' );

		$idFormulario = $id;
		$data = array ();
		$data['idFormulario'] = $idFormulario;

		//$data ['emails'] =$this->Admin_emails_model->listarEmails ($idFormulario, $start, $end, $cantidad, $search );
		$data ['formularios'] = $this->Admin_formularios_model->obtenerInfoFormularios($idFormulario);

		$data['formularios']['cantidadEmails'] = $this->Admin_emails_model->obtenerCantidadEmails($idFormulario);

		if (! $this->Admin_formularios_model->esDeUsuarioFormulario($idFormulario) and !$this->auth->tengoPermisoDeCompanero ('registros_ver_listado', $data['formularios']['id_usuario'] )) {

			$this->output->set_output ( 'no_belong' );

		} else {

			$this->load->view ( 'admin/_header', $data );
			$this->load->view ( 'admin/_sidebar', $data );
			$this->load->view ( 'admin/formularios_estado_emails_listar_view', $data );
			$this->load->view ( 'admin/_footer', $data );
		}
	}


}

?>
