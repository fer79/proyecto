<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );


class Registros_ajx extends CI_Controller {

	function __construct() {
		parent::__construct ();

		$this->load->model ( 'Admin_registros_model' );
		$this->load->model ( 'Form_validate_model' );
		$this->load->model ( 'Diplomas_model' );

		$this->Diplomas_model = new Diplomas_model ();
		$this->Admin_registros_model = new Admin_registros_model ();
		$this->Form_validate_model = new Form_validate_model ();
		$this->Admin_formularios_model = new Admin_formularios_model ();
	}


	function descargarArchivos($idFormulario) {
		ini_set ( 'memory_limit', '1024M' );
		$this->Admin_registros_model->descargarArchivos ( $idFormulario );
	}


	function buscarPersona() {
		$this->auth->tieneAcceso ( 'registros_ver_listado' );
		$term = $this->input->post ( 'term', TRUE );

		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_registros_model->buscarPersonas ( $term );
		$this->output->set_output ( json_encode ( $ret ) );
	}


	function misinscripciones() {
		$this->auth->tieneAcceso ();
		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_registros_model->misInscripciones ();
		$this->output->set_output ( json_encode ( $ret ) );
	}


	function listar() {

		$this->auth->tieneAcceso ( 'registros_ver_listado' );
		$id = $this->input->post ( 'id', TRUE );

		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_registros_model->listarRegistros ( $id );

		$this->output->set_output ( json_encode ( $ret ) );
	}


	function sorteoplaza() {

		$this->auth->tieneAcceso ( 'registros_sorteo_plaza' );

		$idForm = $this->input->post ( 'idForm', TRUE );
		$cantidad = $this->input->post ( 'cantidad', TRUE );
		$mensaje = $this->input->post ( 'mensaje', TRUE );
		$mensajeNO = $this->input->post ( 'mensajeNO', TRUE );

		$ret = $this->Admin_registros_model->sorteoPlaza ( $idForm, $cantidad, $mensaje, $mensajeNO );

		$this->output->set_output ( json_encode ( $ret ) );
	}


	function reenviarEvaluacion() {

		$id = $this->input->post ( 'id', TRUE );
		$this->Admin_formularios_model->reenviarEvaluacion($id); //Solo reenviamos la misma eval
		$this->output->set_output ( json_encode ( 'ok' ) );
	}


	function sorteobeca() {

		$this->auth->tieneAcceso ( 'registros_sorteo_beca' );

		$idForm = $this->input->post ( 'idForm', TRUE );
		$cantidad = $this->input->post ( 'cantidad', TRUE );
		$mensaje = $this->input->post ( 'mensaje', TRUE );
		$sorteo = $this->input->post ( 'sorteo', TRUE );
		$porcentajedescuento = $this->input->post ( 'porcentajedescuento', TRUE );

		$ret = $this->Admin_registros_model->sorteoBeca ( $idForm, $cantidad, $mensaje, $sorteo, $porcentajedescuento );

		$this->output->set_output ( json_encode ( $ret ) );
	}


	function obtenerBecas() { // Traemos el listado de las becas para el formulario

		$this->auth->tieneAcceso ( 'registros_sorteo_beca' );
		$idForm = $this->input->post ( 'idForm', TRUE );

		$ret = $this->Admin_formularios_model->obtenerCamposBeca ( $idForm );

		$this->output->set_output ( json_encode ( $ret, true ) );
	}


	function habilitarInscripcionTardia() {

		$this->auth->tieneAcceso ( 'registros_habilitar_tarde' );

		$usuario = $this->input->post ( 'usuario', TRUE );
		$id_formulario = $this->input->post ( 'idForm', TRUE );

		if ($this->Admin_formularios_model->habilitarInscripcionTardia ( $id_formulario, $usuario )) {
			header ( 'Content-Type: application/json' );
			$this->output->set_output ( json_encode ( 'ok' ) );
		} else {
			header ( 'Content-Type: application/json' );
			$this->output->set_output ( json_encode ( 'error' ) );
		}
	}


	function agregarnota() {

		$this->auth->tieneAcceso ( 'registros_ver_listado' );
		$id = $this->input->post ( 'id', TRUE );
		$color = $this->input->post ( 'color', TRUE );
		$texto = $this->input->post ( 'texto', TRUE );
		$this->Admin_registros_model->agregarnota ( $id, $color, $texto );

		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( 'ok' ) );
	}


	function habilitar() {

		$this->auth->tieneAcceso ( 'registros_marcar_habilitado' );
		$id = $this->input->post ( 'id', TRUE );
		$this->Admin_registros_model->habilitar ( $id );

		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( 'ok' ) );
	}


	function pagar() {

		$this->auth->tieneAcceso ( 'registros_marcar_pago' );
		$id = $this->input->post ( 'id', TRUE );
		$numero = $this->input->post ( 'numero', TRUE );
		$tipo = $this->input->post ( 'tipo', TRUE ); // 1 = pagar , 0 = no pagar
		$cuota = $this->input->post ( 'cuota', TRUE );

		$this->Admin_registros_model->pagar ( $id, $numero, $tipo, $cuota );

		/* Obtenemos el id del formulario, en base al registro. */
		$idForm = $this->Admin_formularios_model->obtenerIdFormularioRegistro ( $id );

		$retorno = 'ok';

		if ($this->config->item ( 'diploma_habilitado' ) && ($tipo == 1)) {

			$usuario = $this->Admin_registros_model->obtenerUsuarioRegistro ( $id );
			// Mandamos el diploma, en caso que se deba
			$retorno = $this->Diplomas_model->enviarDiploma ( $idForm, $usuario ['id'], $id );
		}

		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( $retorno ) );
	}


	function exportarevacompleto() {

		$idForm = $this->input->post ( 'idForm', TRUE );


		$form = $this->Admin_formularios_model->obtenerInfoFormularios ( $idForm );

		if ($this->auth->tieneAcceso ( 'registros_exportar', true )) {

			$excel = $this->Admin_registros_model->exportarevacompleto($idForm);
			header ( "Content-type: application/vnd.ms-excel; name='excel'" );
			header ( "Content-Disposition: filename=" . $this->Admin_model->limpiarURL ( $form ['titulo'] ) . '-' . $form ['tipo'] . '-' . date ( 'Y-m-d' ) . '-Completo.xls' );
			header ( "Pragma: no-cache" );
			header ( "Expires: 0" );

			echo chr ( 255 ) . chr ( 254 ) . iconv ( "UTF-8", "UTF-16LE//IGNORE", $excel );
		}
	}


	function exportareva() {

		$idForm = $this->input->post ( 'idForm', TRUE );

		$form = $this->Admin_formularios_model->obtenerInfoFormularios ( $idForm );

		if ($this->auth->tieneAcceso ( 'registros_exportar', true )) {

			$excel = $this->Admin_registros_model->exportareva ( $idForm, $this->Admin_model->limpiarURL ( $form ['titulo'] ) . '-' . $form ['tipo'] . '-' . date ( 'Y-m-d' ) );
		}
	}


	function exportar() {

		$idForm = $this->input->post ( 'idForm', TRUE );
		$filtros = $this->input->post ( 'filtros', TRUE );

		$campos = json_decode ( $this->input->post ( 'campos', TRUE ), FALSE );

		$form = $this->Admin_formularios_model->obtenerInfoFormularios ( $idForm );

		if ($this->auth->tieneAcceso ( 'registros_exportar', true )) {

			$excel = $this->Admin_registros_model->exportar ( $idForm, $campos, $filtros );
			header ( "Content-type: application/vnd.ms-excel; name='excel'" );
			header ( "Content-Disposition: filename=" . $this->Admin_model->limpiarURL ( $form ['titulo'] ) . '-' . $form ['tipo'] . '-' . date ( 'Y-m-d' ) . '.xls' );
			header ( "Pragma: no-cache" );
			header ( "Expires: 0" );

			echo chr ( 255 ) . chr ( 254 ) . iconv ( "UTF-8", "UTF-16LE//IGNORE", $excel );
		}
	}


	function borrarMiInscripcion() {
		$id = $this->input->post ( 'id', TRUE );

		if ($this->Admin_registros_model->perteneceInscripcion ( $id )) {
			if ($this->Admin_registros_model->dentroDePlazo ( $id )) {
				$this->Admin_registros_model->borrarMiInscripcion ( $id );
				$this->output->set_output ( json_encode ( 'ok' ) );
			} else {

				$this->output->set_output ( json_encode ( 'fuera_fecha' ) );
			}
		} else {
			$this->output->set_output ( json_encode ( 'no_pertenece' ) );
		}
	}


	function borrar() {
		// $this->auth->tieneAcceso('registros_eliminar');
		// $id=$this->input->post('id',TRUE);
		// if ($this->Admin_model->newsbelongs($id)){
		// $this->Admin_registros_model->borrarFormulario($id);
		// $this->output->set_output(json_encode('ok'));
		// }else{
		// $this->output->set_output('no_belong');
		// }
	}
}

?>
