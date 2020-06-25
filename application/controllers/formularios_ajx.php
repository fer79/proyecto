<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Formularios_ajx extends CI_Controller {

	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_formularios_model' );
		$this->load->model ( 'Form_validate_model' );

		$this->Admin_formularios_model = new Admin_formularios_model ();
		$this->Form_validate_model = new Form_validate_model ();
	}

	function crear() {

		$this->auth->tieneAcceso ( 'formularios_crear' );
		$publicado = $this->input->post ( 'publicado', TRUE );
		$titulo = $this->input->post ( 'titulo', TRUE );
		$fechainicio = $this->input->post ( 'fechainicio', TRUE );
		$fechafin = $this->input->post ( 'fechafin', TRUE );
		$tipoformulario = $this->input->post ( 'tipoformulario', TRUE );
		$cantidad = $this->input->post ( 'cantidad', TRUE );
		$colderecha = $this->input->post ( 'colderecha', TRUE );
		$colizquierda = $this->input->post ( 'colizquierda', TRUE );
		$campos = $this->input->post ( 'campos', TRUE );
		$vincular = $this->input->post ( 'vincular', TRUE );
		$categoria = $this->input->post ( 'categoria', TRUE );
		$emails = $this->input->post ( 'emails', TRUE );
		$abonar = $this->input->post ( 'abonar', TRUE );
		$lugarabono = $this->input->post ( 'lugarabono', TRUE );
		$costocurso = $this->input->post ( 'costocurso', TRUE );
		$monedacostocurso = $this->input->post ( 'monedacostocurso', TRUE );
		$fechaabonoinicio = $this->input->post ( 'fechaabonoinicio', TRUE );
		$fechaabonofin = $this->input->post ( 'fechaabonofin', TRUE );
		$fechacomienzocurso = $this->input->post ( 'fechacomienzocurso', TRUE );
		$cargahoraria = $this->input->post ( 'cargahoraria', TRUE );

		$hasErrors = false;

		// VALIDAMOS EL RESTO DE LOS CAMPOS
		if ((($retornoValidacion = $this->Form_validate_model->validate ( 'crear_modificar_formularios' )) === true) and (! $hasErrors)) {
			$this->Admin_formularios_model->crearFormulario ( $titulo, $fechainicio, $fechafin, $tipoformulario, $cantidad, $campos, $colderecha, $colizquierda, $vincular, $abonar, $lugarabono, $costocurso, $monedacostocurso, $fechaabonofin, $fechaabonoinicio, $emails, $publicado, $categoria, $fechacomienzocurso, $cargahoraria );
			$this->output->set_output ( json_encode ( 'ok' ) );
		} else {
			$this->output->set_output ( json_encode ( $retornoValidacion ) );
		}
	}


	function listar() {

		$this->auth->tieneAcceso ( 'formularios_ver_listado' );
		$start = $this->input->post ( 'start' ) - 1;
		$max_results = 10;
		$start = $max_results * $start;
		$end = $max_results;
		$cantidad = 0;

		$filtro = $this->input->post ( 'filtro', TRUE );
		$search = $this->input->post ( 'search', TRUE );

		header ( 'Content-Type: application/json' );
		$ret = $this->Admin_formularios_model->listarFormularios ( $start, $end, $cantidad, $filtro, $search );

		if ($cantidad > 0) {
			$this->output->set_output ( json_encode ( array (
					'listado' => $ret
			) ) );
		} else {
			$this->output->set_output ( 'vacio' );
		}
	}


	function modificar() {

		$this->auth->tieneAcceso ( 'formularios_modificar' );
		$id = $this->input->post ( 'id', TRUE );
		$titulo = $this->input->post ( 'titulo', TRUE );
		$publicado = $this->input->post ( 'publicado', TRUE );
		$fechainicio = $this->input->post ( 'fechainicio', TRUE );
		$fechafin = $this->input->post ( 'fechafin', TRUE );
		$tipoformulario = $this->input->post ( 'tipoformulario', TRUE );
		$cantidad = $this->input->post ( 'cantidad', TRUE );
		$colderecha = $this->input->post ( 'colderecha', TRUE );
		$colizquierda = $this->input->post ( 'colizquierda', TRUE );
		$campos = $this->input->post ( 'campos', TRUE );
		$vincular = $this->input->post ( 'vincular', TRUE );
		$categoria = $this->input->post ( 'categoria', TRUE );
		$emails = $this->input->post ( 'emails', TRUE );
		$abonar = $this->input->post ( 'abonar', TRUE );
		$lugarabono = $this->input->post ( 'lugarabono', TRUE );
		$costocurso = $this->input->post ( 'costocurso', TRUE );
		$monedacostocurso = $this->input->post ( 'monedacostocurso', TRUE );
		$fechaabonoinicio = $this->input->post ( 'fechaabonoinicio', TRUE );
		$fechaabonofin = $this->input->post ( 'fechaabonofin', TRUE );
		$fechacomienzocurso = $this->input->post ( 'fechacomienzocurso', TRUE );
		$cargahoraria = $this->input->post ( 'cargahoraria', TRUE );
		$hasErrors = false;

		// VALIDAMOS EL RESTO DE LOS CAMPOS
		if ((($retornoValidacion = $this->Form_validate_model->validate ( 'crear_modificar_formularios' )) === true) and (! $hasErrors)) {
			$this->Admin_formularios_model->modificarFormulario ( $id, $titulo, $fechainicio, $fechafin, $tipoformulario, $cantidad, $campos, $colderecha, $colizquierda, $vincular, $abonar, $lugarabono, $costocurso, $monedacostocurso, $fechaabonofin, $fechaabonoinicio, $emails, $publicado, $categoria, $fechacomienzocurso, $cargahoraria );
			$this->output->set_output ( json_encode ( 'ok' ) );
		} else {
			$this->output->set_output ( json_encode ( $retornoValidacion ) );
		}
	}

	// function obtenerVinculables(){
	// $term=$this->input->post('term',TRUE);

	// $this->output->set_output(json_encode($this->Admin_formularios_model->obtenerVinculables($term)));

	// }
	function obtenerTotal() {

		$filtro = $this->input->post ( 'filtro', TRUE );
		$search = $this->input->post ( 'search', TRUE );

		$ret = $this->Admin_formularios_model->obtenerCantidadFormularios ( $filtro, $search );
		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( array (
				'cantidad' => $ret
		) ) );
	}


	function obtenerFormulario() {
		$id = $this->input->post ( 'id', TRUE );
		header ( 'Content-Type: text/plain' );
		$this->Admin_model->generarHTML ( $this->Admin_formularios_model->obtenerFormulario ( $id ) );
	}


	function obtenerInscripcionesTardias() {
		$id = $this->input->post ( 'id', TRUE );

		header ( 'Content-Type: application/json' );
		$this->output->set_output ( json_encode ( $this->Admin_formularios_model->obtenerInscripcionesTardias ( $id ) ) );
	}


	function borrar() {

		$this->auth->tieneAcceso ( 'formularios_eliminar' );
		$id = $this->input->post ( 'id', TRUE );
		// if ($this->Admin_model->newsbelongs($id)){
		$this->Admin_formularios_model->borrarFormulario ( $id );
		$this->output->set_output ( json_encode ( 'ok' ) );
		// }else{
		// $this->output->set_output('no_belong');
		// }
	}


	function clonar() {

		$this->auth->tieneAcceso ( 'formularios_crear' );
		$id = $this->input->post ( 'id', TRUE );
		// if ($this->Admin_model->newsbelongs($id)){
		$this->Admin_formularios_model->clonarFormulario ( $id );
		$this->output->set_output ( json_encode ( 'ok' ) );
		// }else{
		// $this->output->set_output('no_belong');
		// }
	}
	
}

?>
