<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Diplomas_model extends CI_Model {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'Admin_formularios_model' );
		$this->Admin_formularios_model = new Admin_formularios_model ();
		$this->load->model ( 'Admin_registros_model' );
		$this->Admin_registros_model = new Admin_registros_model ();
		$this->load->model ( 'Cron_email_model' );
		$this->Cron_email_model = new Cron_email_model ();
		$this->load->model ( 'Pdf_ci' );
		$this->Pdf_ci = new Pdf_ci ();
		$this->load->model ( 'Admin_usuarios_model' );
		$this->Admin_usuarios_model = new Admin_usuarios_model ();
		$this->load->model ( 'Admin_model' );
		$this->Admin_model = new Admin_model ();
	}
	function enviarDiploma($id_formulario = 0, $id_usuario = 0, $respuesta_id = 0) {
		$fn = 'diploma_' . $this->config->item ( 'diploma_tipo' );
		
		return $this->$fn ( $id_formulario, $id_usuario, $respuesta_id ); // Llamamos al diploma correspondiente. function diploma_(diploma_tipo)
	}
	
	/* DIPLOMA PARA FENF */
	function diploma_fenf($id_formulario = 0, $id_usuario = 0, $respuesta_id = 0) {
		/*
		 * ID_FORMULARIO siempre es el del formulario de inscripcion, de dónde tomamos el nombre.
		 * ID_USUARIO es el usuario al que le mandamos el diploma
		 *
		 */
		$formulario = $this->Admin_formularios_model->obtenerInfoFormularios ( $id_formulario );
		
		$evaluaciones = array ();
		
		if ($formulario ['tipo'] == 'inscripcion') {
			
			$evaluaciones = $this->Admin_formularios_model->obtenerEvaluacionVinculada ( $formulario ['id'] );
		} elseif ($formulario ['tipo'] == 'evaluacion') {
			
			/* Traemos todos los formularios de evaluacion del de inscripcion */
			if ($formulario ['vincular'] != 0) {
				
				$evaluaciones = $this->Admin_formularios_model->obtenerEvaluacionVinculada ( $formulario ['vincular'] );
				$formulario = $this->Admin_formularios_model->obtenerInfoFormularios ( $formulario ['vincular'] );
			}
		}
		
		$esPago = $this->Admin_registros_model->esPago ( $formulario ['id'], true , $id_usuario);
		$cumplecondicion = ($esPago !== false); // la condición es que esté pago, luego vemos si llenó las evaluaciones
		/* Si evaluó todo lo que tenía que evaluar */
		foreach ( $evaluaciones as $eval ) {
			$cumplecondicion = $cumplecondicion && $this->Admin_registros_model->usuarioRealizoEvaluacion ( $eval ['id'], $id_usuario );
		}
		if ($formulario ['cargahoraria'] != '') {
				
			if ($cumplecondicion) {
				
				$usuario = $this->Admin_usuarios_model->obtenerInfoUsuarios ( $id_usuario );
				
				$data = array (
						
						'persona' => $usuario ['nombre'] . ' ' . $usuario ['apellidos'],
						'curso' => $formulario ['titulo'],
						'fecha' => $formulario ['fechacomienzocurso'],
						'cargahoraria' => $formulario ['cargahoraria'] 
				)
				;
				
				$nombreAttachment = 'Diploma' . '-' . $formulario ['id'] . '-' . $id_usuario . '.pdf';
				$attachment = $this->Pdf_ci->crearPDF ( $nombreAttachment, $data );
				
				if ($attachment) {
					
					$this->Admin_registros_model->agregarDiploma ( $respuesta_id, $nombreAttachment ); // Asignamos el diploma al registro
					
					$this->load->model ( 'Cron_email_model' );
					$base_url = base_url ();
					$urlval = base_url () . 'adminpanel/micuenta/misinscripciones';
					$tituloform = $formulario ['titulo'];
					
					$message = <<<HTML
Adjunto se encuentra el diploma/certificado de  "$tituloform" \n
Puedes ver y descargar tus diplomas desde el panel de usuario en el siguiente link: \n
$urlval
HTML;
					if (! $this->Cron_email_model->agregarAcola ( $id_formulario, $this->auth->id_usuario (), $this->auth->email_usuario ( array (
							'id' => $id_usuario 
					) ), 'Diploma-Certificado de  ' . $tituloform, $message, true, 0, '', '', $attachment )) {
						return 'not_sent';
					} else {
						
						return 'ok';
					}
				} else {
					
					return 'No se pudo crear pdf';
				}
			} else {
				
				return 'No cumple condiciones';
			}
		} else {
			
			return 'El formulario no tiene ingresado la carga horaria. El diploma/comprobante no se ha enviado.';
		}
	}
}

?>