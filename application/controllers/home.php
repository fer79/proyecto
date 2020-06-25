<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Home extends CI_Controller {

	public function __construct() {

		parent::__construct ();
		$this->load->model ( 'Home_model' );
		$this->load->model ( 'Admin_formularios_model' );
		$this->load->model ( 'Admin_categorias_model' );
		$this->load->library ( 'form_validation' );

		$this->Home_model = new Home_model ();
		$this->Admin_categorias_model = new Admin_categorias_model ();
		$this->Admin_formularios_model = new Admin_formularios_model ();
	}

	function error404() {
		redirect ( base_url () );
	}

	function main($categoria = 0) {

		if (! $this->Admin_categorias_model->existeCategoriaConId ( $categoria )) {
			$this->error404 ();
			die ();
		}

		// $this->load->model('Diplomas_model');
		// $this->Diplomas_model = new Diplomas_model();
		// $this->Diplomas_model->enviarDiploma(234,1);
		// $this->load->model('Pdf_ci');
		// $this->Pdf_ci = new Pdf_ci();
		// $this->load->view('pdfs/diplomas_fenf', array('curso'=>'Diploma en y en no se que mas y en no se que mas mobiliarioo se que mas y ','fecha','fecha'=>date('Y-m-d'),'cargahoraria'=>'20HS','persona'=>'Nicolás Torres Gussoni'));

		// $this->Pdf_ci->crearPDF('asdasdasd.pdf',array('curso'=>'Diploma en y en no se que mas y en no se que mas mobiliario','fecha'=>date('Y-m-d'),'cargahoraria'=>'20HS','persona'=>'Nicolás Torres Gussoni'));

		$data ['cursosabiertos'] = $this->Home_model->obtenerCursosAbiertos ( $categoria );
		// $data['cursosabiertosprivados'] = $this->Home_model->obtenerCursosAbiertosPrivados();

		$data ['site_title'] = "Sistema de Inscripciones y Evaluaciones";

		$this->load->view ( '_header', $data );
		// $this->load->view('_sidebar', $data);
		$this->load->view ( 'main_view', $data );
		$this->load->view ( '_footer', $data );
	}


	function verformulario($id = '') {

		if (isset ( $_SERVER ['CONTENT_LENGTH'] ) && intval ( $_SERVER ['CONTENT_LENGTH'] ) > 0 && count ( $_POST ) === 0) {
			throw new Exception ( 'PHP discarded POST data because of request exceeding post_max_size.' . ini_get ( 'post_max_size' ) );
		}

		$this->auth->tieneAcceso (); // Si no está logueado no entra a ningun lado

		$data = array ();

		$data ['ret'] = $this->Home_model->obtenerDatosFormulario ( $id );

		/* ARMAMOS LOS BREADCRUMBS */
		$bc = array ();
		$this->Admin_categorias_model->obtenerCaminoDesdeCat ( $data ['ret'] ['categoria'], $bc );

		$i = 0;
		foreach ( $bc as $breadcrumb ) {
			if ($i == 0)
				$breadcrumb ['nombre'] = '<i class="fa fa-home" aria-hidden="true"></i> ' . $breadcrumb ['nombre'];
			$i ++;

			$this->breadcrumbs->push ( $breadcrumb ['nombre'], 'categoria/' . $breadcrumb ['id'] . '/' . $this->Admin_model->limpiarURL ( $breadcrumb ['nombre'] ) );
		}

		$this->breadcrumbs->push ( $data ['ret'] ['titulo'], base_url () . $data ['ret'] ['url'] );

		if (! $this->Admin_formularios_model->esDeUsuarioFormulario ( $id ) and ! $this->auth->tengoPermisoDeCompanero ( 'registros_ver_listado', $data ['ret'] ['id_usuario'] )) {
			// Si no es mi formulario ni de mi compañero, checkeamos:
		  // Fui inscripto

			$habilitadoTarde = false;

			if ($this->Home_model->yaFuiInscripto ( $id )) {

				redirect ( base_url () . '?warning=yainscripto' );
				exit ();
			}

			// Estoy dentro del plazo
			if (! $this->Admin_formularios_model->dentroDePlazo ( $id )) {

				if (! $this->Admin_formularios_model->estaHabilitadoTardio ( $id )) {
					redirect ( base_url () . '?warning=fueradeplazo' );
					exit ();
				} else {

					$habilitadoTarde = true;
				}
			}

			// Estoy habilitado y no estoy habilitado a inscripcion tardia
			if (! $habilitadoTarde && ! $this->Admin_formularios_model->estoyHabilitado ( $id, $data ['ret'] ['vincular'], $data ['ret'] ['tipo'] )) {

				redirect ( base_url () . '?warning=nohabilitado' );
				exit ();
			}
		}

		/* SI YA SE LLENARON LOS CUPOS */
		if (($data ['ret'] ['cantidad'] > 0) and (($data ['ret'] ['cantidad'] - $data ['ret'] ['cantidadRegistros']) == 0)) {
			redirect ( base_url () );
			exit ();
		}

		$data ['site_title'] = "Sistema de Inscripciones | " . $data ['ret'] ['titulo'];

		if ($this->input->post ( 'enviado' ) == 'si') {

			if (($datos_guardar = $this->Home_model->validar ( $data ['ret'] ['formulario'] )) == FALSE) { // SI NO VALIDA O NO SE SUBEN LOS ARCHIVOS
				$data ['formulario'] = '<span class="textoerror" style="font-size:15px;">[*] Debe subir los archivos nuevamente</span>' . $this->Home_model->generarHTML ( $data ['ret'] ['formulario'], $data ['ret'] ['tipo'] );
				$this->load->view ( '_header', $data );
				$this->load->view ( 'verformulario_view', $data );
				$this->load->view ( '_footer', $data );

			} else { // TODO LINDO Y LISTO PARA GUARDAR

				$cuotas = $this->input->post('cuotas');

				$codigo = $this->Home_model->guardarDatos ( $id, $datos_guardar, $cuotas );

				$data ['formulario'] = $this->Home_model->generarHTML ( $data ['ret'] ['formulario'], $data ['ret'] ['tipo'] );

				$this->load->view ( '_header', $data );
				$this->load->view ( 'exito_view', $data );
				$this->load->view ( '_footer', $data );
			}
		} else {
			$data ['formulario'] = $this->Home_model->generarHTML ( $data ['ret'] ['formulario'], $data ['ret'] ['tipo'] , $data ['ret'] ['abonar']);
			$this->load->view ( '_header', $data );
			$this->load->view ( 'verformulario_view', $data );
			$this->load->view ( '_footer', $data );
		}
	}
}
