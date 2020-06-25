<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Form_validate_model extends CI_Model {
	public $reglas;
	function __construct() {
		parent::__construct ();
		
		$this->load->library ( 'form_validation' ); // CARGAMOS LA LIBRERIA DE VALIDACION
		
		/* CARGAMOS LAS REGLAS PARA LOS FORMULARIOS */
		
		$this->reglas = array (
				'crear_modificar_formularios' => array (
						array (
								'field' => 'titulo',
								'label' => 'titulo',
								'rules' => array (
										array (
												'required',
												'El campo %s no debe ser vacío' 
										) 
								)
								 
						),
						array (
								'field' => 'fechainicio',
								'label' => 'fecha de inicio',
								'rules' => array (
										array (
												'required',
												'El campo %s no debe ser vacío' 
										),
										array (
												'checkDateFormat',
												'El campo %s no es válido' 
										) 
								) 
						),
						array (
								'field' => 'fechafin',
								'label' => 'fecha de fin',
								'rules' => array (
										array (
												'required',
												'El campo %s no debe ser vacío' 
										),
										array (
												'checkDateFormat',
												'El campo %s no es válido' 
										) 
								) 
						),
						array (
								'field' => 'tipoformulario',
								'label' => 'tipo de formulario',
								'rules' => array (
										array (
												'required',
												'El campo %s no debe ser vacío' 
										) 
								)
								 
						),
						array (
								'field' => 'cantidad',
								'label' => 'cantidad',
								'rules' => array (
										array (
												'required',
												'El campo %s no debe ser vacío' 
										) 
								)
								 
						),
						array (
								'field' => 'emails',
								'label' => 'emails',
								'rules' => array (
										array (
												'checkEmailFormat',
												'Existe un email inválido' 
										) 
								)
								 
						) 
				)
				 
		);
	}
	function validate($formulario = '') {
		$totalreglas = array ();
		/* Creamos las reglas */
		foreach ( $this->reglas [$formulario] as $regla ) {
			
			$r = 'trim';
			foreach ( $regla ['rules'] as $reglaIndividual ) {
				
				$r .= '|' . $reglaIndividual [0];
				$this->form_validation->set_message ( $reglaIndividual [0], $reglaIndividual [1] );
			}
			
			$totalreglas [] = array (
					'field' => $regla ['field'],
					'label' => $regla ['label'],
					'rules' => $r 
			);
		}
		
		$this->form_validation->set_rules ( $totalreglas );
		
		if ($this->form_validation->run () == FALSE) {
			
			return $this->form_validation->error_array ();
		} else {
			
			return true;
		}
	}
	public function valid_url($url) {
		$pattern = "/^((ht|f)tp(s?)\:\/\/|~/|/)?([w]{2}([\w\-]+\.)+([\w]{2,5}))(:[\d]{1,5})?/";
		if (! preg_match ( $pattern, $url )) {
			return FALSE;
		}
		
		return TRUE;
	}
}

?>