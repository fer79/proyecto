<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Home_model extends CI_Model {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'Admin_usuarios_model' );
		$this->load->model ( 'Admin_model' );
		$this->load->model ( 'Admin_formularios_model' );
		$this->load->model ( 'Admin_categorias_model' );
		$this->Admin_categorias_model = new Admin_categorias_model ();
		$this->Admin_model = new Admin_model ();
		$this->load->model ( 'Admin_registros_model' );
		$this->Admin_registros_model = new Admin_registros_model ();

		$this->load->model ( 'Diplomas_model' );
		$this->Diplomas_model = new Diplomas_model ();
	}


	function obtenerCursosAbiertos($cat_id = 0) {
		$retorno = array ();

		/* ARMAMOS LOS BREADCRUMBS */
		$bc = array ();
		$this->Admin_categorias_model->obtenerCaminoDesdeCat ( $cat_id, $bc );

		$i = 0;
		foreach ( $bc as $breadcrumb ) {
			if ($i == 0)
				$breadcrumb ['nombre'] = '<i class="fa fa-home" aria-hidden="true"></i> ' . $breadcrumb ['nombre'];
			$i ++;

			$this->breadcrumbs->push ( $breadcrumb ['nombre'], 'categoria/' . $breadcrumb ['id'] . '/' . $this->Admin_model->limpiarURL ( $breadcrumb ['nombre'] ) );
		}

		/* Traemos los hijos */
		$categorias = $this->Admin_categorias_model->obtenerCategorias ( '', $cat_id );

		// if (empty($categorias)){
		// $retorno['categorias'][1]['titulo'] = 'General';
		// $retorno['categorias'][1]['url'] = base_url().'categoria/1/'.$this->Admin_model->limpiarURL('General');
		// $retorno['categorias'][1]['hijos'] = array();
		// }

		foreach ( $categorias as $categoria ) {

			$retorno ['categorias'] [$categoria ['id']] ['titulo'] = $categoria ['nombre'];
			$retorno ['categorias'] [$categoria ['id']] ['url'] = base_url () . 'categoria/' . $categoria ['id'] . '/' . $this->Admin_model->limpiarURL ( $categoria ['nombre'] );
			$retorno ['categorias'] [$categoria ['id']] ['hijos'] = array ();
			$retorno ['categorias'] [$categoria ['id']] ['formularios'] = array ();

			$this->db->select ();
			$this->db->from ( 'formularios' );
			$this->db->where ( 'fecha_inicio  <=', date ( 'Y-m-d H:m:s', time () ) );
			$this->db->where ( 'fecha_fin  >', date ( 'Y-m-d H:m:s', time () ) );
			$this->db->where ( 'eliminado', 0 );
			$this->db->where ( 'publicado', 1 );

			$this->db->where ( 'categoria', $categoria ['id'] );

			// $this->db->where('formularios.id NOT IN ( SELECT id_formulario FROM usuarios_habilitados_formulario)');

			$this->db->order_by ( 'fecha_fin', 'ASC' );

			$q = $this->db->get ();

			foreach ( $q->result () as $row ) {
				$inscripto = false;
				if ($this->yaFuiInscripto ( $row->id ))
					$inscripto = true;

				$cantidadRegistros = $this->Admin_registros_model->obtenerCantidadRegistros ( $row->id );
				$retorno ['categorias'] [$row->categoria] ['formularios'] [] = array (
						'id' => $row->id,
						'titulo' => $row->titulo,
						'fecha_fin' => $row->fecha_fin,
						'url' => base_url () . $row->tipo . '/' . $row->id . '/' . $this->limpiarURL ( $row->titulo ),
						'inscripto' => $inscripto,
						'cantidad' => $row->cantidad,
						'cantidadRegistros' => $cantidadRegistros
				);
			}

			// /*PRIVADAS*/
			// $email = $this->auth->email_usuario(array('id'=>$this->auth->id_usuario()));

			// $this->db->select();
			// $this->db->from('formularios');
			// $this->db->where('fecha_inicio <=',date('Y-m-d H:m:s',time()));
			// $this->db->where('fecha_fin >',date('Y-m-d H:m:s',time()));
			// $this->db->where('eliminado',0);
			// $this->db->where('publicado','1');

			// $this->db->where('categoria',$categoria['id']);

			// //$this->db->where('formularios.id IN (SELECT id_formulario FROM usuarios_habilitados_formulario WHERE email = "'.$email.'")');

			// $this->db->order_by('fecha_fin','ASC');

			// $q=$this->db->get();

			// foreach ($q->result() as $row)
			// {
			// $inscripto = false;
			// if ($this->yaFuiInscripto($row->id))
			// $inscripto = true;

			// $cantidadRegistros = $this->Admin_registros_model->obtenerCantidadRegistros($row->id);

			// $retorno['categorias'][$row->categoria]['formularios'][]=array('id'=>$row->id,'titulo'=>$row->titulo,'fecha_fin'=>$row->fecha_fin,'url'=>base_url().$row->tipo.'/'.$row->id.'/'.$this->limpiarURL($row->titulo),'inscripto'=>$inscripto,'cantidad'=>$row->cantidad,'cantidadRegistros'=>$cantidadRegistros);

			// }

			foreach ( $categoria ['hijos'] as $hijo ) {

				$retorno ['categorias'] [$categoria ['id']] ['hijos'] [$hijo ['id']] ['titulo'] = $hijo ['nombre'];
				$retorno ['categorias'] [$categoria ['id']] ['hijos'] [$hijo ['id']] ['url'] = base_url () . 'categoria/' . $hijo ['id'] . '/' . $this->Admin_model->limpiarURL ( $hijo ['nombre'] );
				$retorno ['categorias'] [$categoria ['id']] ['hijos'] [$hijo ['id']] ['hijos'] = array ();
			}
		}

		/*
		 * PONEMOS AL GENERAL POR LAS DUDAS
		 */

		return $retorno;
	}


	function yaFuiInscripto($id = 0, $id_usuario = null) {
		$this->db->select ();
		$this->db->from ( 'usuario_responde_formulario,formularios' );

		if ($id_usuario == null) {
			$this->db->where ( 'usuario_responde_formulario.id_usuario', $this->auth->id_usuario () );
		} else {
			$this->db->where ( 'usuario_responde_formulario.id_usuario', $id_usuario );
		}

		$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', FALSE );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
		$q = $this->db->get ();

		if ($q->num_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}
	function obtenerDatosFormulario($id = '') {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'formularios,usuarios_crean_formularios,usuarios' );
		$this->db->where ( 'usuarios.id', 'usuarios_crean_formularios.id_usuario', false );
		$this->db->where ( 'formularios.id', 'usuarios_crean_formularios.id_formulario', false );
		$this->db->where ( 'formularios.id', $id );
		$q = $this->db->get ();

		if ($q->num_rows () > 0) {

			$row = $q->row ();
			$cantidadRegistros = $this->Admin_registros_model->obtenerCantidadRegistros ( $row->id );
			$retorno = array (
					'id_usuario' => $row->id_usuario,
					'categoria' => $row->categoria,
					'vincular' => $row->vincular,
					'id' => $row->id,
					'titulo' => $row->titulo,
					'abonar' => $row->abonar,
					'tipo' => $row->tipo,
					'formulario' => json_decode ( $row->formulario, true ),
					'colderecha' => $row->colderecha,
					'colizquierda' => $row->colizquierda,
					'fecha_fin' => $row->fecha_fin,
					'url' => base_url () . $row->tipo . '/' . $row->id . '/' . $this->limpiarURL ( $row->titulo ),
					'cantidad' => $row->cantidad,
					'cantidadRegistros' => $cantidadRegistros
			);
		}

		return $retorno;
	}

	function generarHTML($formulario = '', $tipo = '', $abonar = 0) {

		$tabindex = 0;
		$html = '<form action="" method="post" enctype="multipart/form-data" id="formulario" accept-charset="utf-8">';

		$infoUsuario = $this->Admin_usuarios_model->obtenerInfoUsuarios ( $this->auth->id_usuario () );
		$camposAutorelleno = $this->config->item ( 'campos_autorelleno' );

		foreach ( $formulario as $campo ) {

			$id = $campo ['id'];

			$idJS = str_replace ( '-', '', $id );

			// SI EL CAMPO ES OBLIGATORIO
			$obligatorio = '';
			if (isset ( $campo ['opciones'] ['obligatorio'] ) && ($campo ['opciones'] ['obligatorio'] == true))
				$obligatorio = ' <span class="obligatorio">*</span>';

			$oculto = '';
			$depende = '';
			if ($campo ['dependencia'] != "NULL") { // SI ES UN CAMPO OCULTO

				$campoPadre = $this->Admin_formularios_model->obtenerCamposFormulario ( $formulario, $campo ['dependencia'] );

				if ($campoPadre ['tipo'] != 'grupo') {

					$oculto = ' style="display:none;" ';
					$depende = ' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . trim ( $campo ['dependenciavalor'] ) . '" ';
				}
			}

			$autorrellenar = '';
			if ((isset ( $campo ['opciones'] ['autorellenar'] )) && ($campo ['opciones'] ['autorellenar'] != "NULL")) {

				if (isset ( $camposAutorelleno [$campo ['opciones'] ['autorellenar']] )) // Si es un campo de autorrelleno
					$autorrellenar = $infoUsuario [$campo ['opciones'] ['autorellenar']];
			}

			// SI luego de enviado tiene errores
			$error = '';
			if (form_error ( $id ) != '')
				$error = ' error ';

			if ($campo ['tipo'] == 'descarga') {

				$campo ['opciones'] ['label'] = $campo ['opciones'] ['texto'];
				$campo ['opciones'] ['ayuda'] = '';
			}

			if (($campo ['tipo'] != 'separador') && ($campo ['tipo'] != 'grupo')) {

				$labelClass = '';
				if ($tipo == 'evaluacion') {
					$labelClass = 'evaluacion';
				}

				$html .= '<div ' . $oculto . $depende . ' id="contenedormaximo">

								<label   class="label ' . $labelClass . '" for="' . $id . '" id="label_' . $id . '">' . $campo ['opciones'] ['label'] . $obligatorio . '</label>
										<div id="contenedor' . $id . '" class="campos"> <span class="labelcontainer">';
			}

			if ($campo ['tipo'] == 'textbox') {

				$html .= '<input  class="campo ' . $error . '" type="text" id="' . $id . '" name="' . $id . '" tabindex="' . $tabindex . '" value="' . set_value ( $id, $autorrellenar ) . '">';
			} elseif ($campo ['tipo'] == 'textarea') {

				$html .= '<textarea class="campo ' . $error . '"  id="' . $id . '" name="' . $id . '" style="width: 300px;height: 80px;" tabindex="' . $tabindex . '">' . set_value ( $id ) . '</textarea>';
			} elseif ($campo ['tipo'] == 'checkboxgroup') {

				$brackets = '';

				if (count ( $campo ['opciones'] ['opciones'] ) >> 1)
					$brackets = '[]';

				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {

					$html .= '<label class="inline"><input class="campo ' . $error . '" type="checkbox" name="' . $id . $brackets . '"class="campo noborder" id="' . $id . '" name="' . $id . '" tabindex="' . $tabindex . '" value="' . $opcion . '" ' . set_checkbox ( $id, $opcion ) . '>' . $opcion . '</label>';
				}

				$html .= '<script type="text/javascript">';

				$html .= "$(document).ready(function(){


										$('#" . $id . "').change(function(){


											if ($(this).is(':checked')){
												$('[dependencia=\"" . $id . "\"]').slideToggle();
											}else{

												$('[dependencia=\"" . $id . "\"]').hide();
											}

										});

										$('#" . $id . "').change();

									});";

				$html .= '</script>';
			} elseif ($campo ['tipo'] == 'combobox') {

				$html .= '<select class="campo ' . $error . '" name="' . $id . '" id="' . $id . '" tabindex="' . $tabindex . '">
                              <option value="">Seleccionar...</option>';

				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {

					$html .= '<option ' . set_select ( $id, $opcion ) . '>' . $opcion . '</option>';
				}
				$html .= '</select>';

				$html .= '<script type="text/javascript">';

				$html .= "$(document).ready(function(){


										$('#" . $id . "').change(function(){

											$('[dependencia=\"" . $id . "\"][dependencia-valor!=\"'+$(this).val()+'\"]').hide();

											$('[dependencia=\"" . $id . "\"][dependencia-valor=\"'+$(this).val()+'\"]').slideDown();


										});

										$('#" . $id . "').change();

									});";

				$html .= '</script>';
			} elseif ($campo ['tipo'] == 'radiogroup') {

				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {

					$html .= '<label class="inline"><input class="radio ' . $error . '"  name="' . $id . '" type="radio" value="' . $opcion . '" ' . set_radio ( $id, $opcion ) . ' tabindex="' . $tabindex . '">' . $opcion . '</label>';
				}
			} elseif ($campo ['tipo'] == 'carga') {

				$html .= '<input class="campo ' . $error . '" type="file" id="' . $id . '" name="' . $id . '"  tabindex="' . $tabindex . '" value="">';
			} elseif ($campo ['tipo'] == 'descarga') {

				$html .= '<div class="lnk"><span style="color:#333;">↓ Descargue el archivo:</span> <a href="' . $campo ['opciones'] ['link'] . '" target="_blank" class="">' . $campo ['opciones'] ['texto'] . '</a></div>';
			} elseif ($campo ['tipo'] == 'separador') {

				$html .= '<h2>' . $campo ['opciones'] ['titulo'] . '</h2>';
			} elseif ($campo ['tipo'] == 'grupo') {

				$html .= '<h2>' . $campo ['opciones'] ['titulo'] . '</h2>';
			} elseif ($campo ['tipo'] == 'date') {

				$html .= '<input  class="campo ' . $error . '" type="text" id="' . $id . '" name="' . $id . '" tabindex="' . $tabindex . '" value="' . set_value ( $id, $autorrellenar ) . '">';

				$html .= "<script type='text/javascript'>
								$(document).ready(function(){

									$( '#" . $id . "' ).datepicker( $.datepicker.regional[ 'es' ] );

								});
								</script>";
			}

			if (($campo ['tipo'] != 'separador') && ($campo ['tipo'] != 'grupo')) {

				if ($campo ['tipo'] == 'carga') {

					$max = min($this->config->item('upload_max_filesize'), $this->parse_size ( ini_get ( 'upload_max_filesize' ) ));

					$html .= '<label class="sublabel" for="' . $id . '" id="sublabel_' . $id . '">' . $campo ['opciones'] ['ayuda'] . '(Máximo:' . $max . 'MB)</label>
									' . form_error ( $id ) . '
									</span>  </div></div>';
				} else {

					$html .= '<label class="sublabel" for="' . $id . '" id="sublabel_' . $id . '">' . $campo ['opciones'] ['ayuda'] . '</label>
										' . form_error ( $id ) . '
										</span>  </div></div>';
				}
			}

			$tabindex ++;
		}

		if ( ($abonar == 1) && ($tipo = 'inscripcion') ){

			$tabindex ++;
			$html .= '<br><label class="label " for="label_cuotas" id="label_cuotas">Cantidad de cuotas <span class="obligatorio">*</span></label>';
			$html .= '<select class="campo" name="cuotas" id="cuotas" tabindex="' . $tabindex . '">
                              <option value="">Seleccionar cuotas</option>';

			$html .= '<option value="1" selected>1 cuota</option>';
			$html .= '<option value="3" >3 cuotas</option>';
			$html .= '</select>';
		}

		$html .= '<div class="submitline">
			          <input name="Submit" type="submit" tabindex="' . $tabindex . '" value="Enviar" class="enviar">
			          <input name="enviado" type="hidden" value="si">
			        </div>';
		$html .= '</form>';

		return $html;
	}

	function parse_size($size) {
		return preg_replace ( '/[^0-9\.]/', '', $size ); // Remove the non-numeric characters from the size.
	}
	function reglaTexto($regla = '') {
		$textos ['numeric'] = 'El campo %s no es numérico';
		$textos ['valid_email'] = 'El campo %s no es un E-mail válido';
		$textos ['required'] = 'El campo %s es obligatorio';
		return $textos [$regla];
	}


	function guardarDatos($id_formulario = '', $reglas = array(), $cuotas) {
		$fecha_hoy = date ( 'Y-m-d H:i:s' );
		$data = array (
				'id_usuario' => $this->auth->id_usuario (),
				'id_formulario' => $id_formulario,
				'fecha_respuesta' => $fecha_hoy,
				'cuotas' => $cuotas,
				'pago1' => null,
				'pago2' => null,
				'pago3' => null
		);

		$this->db->insert ( 'usuario_responde_formulario', $data );
		$last_id = $this->db->insert_id ();


		foreach ( $reglas ['reglas'] as $regla ) {

			if (! isset ( $reglas ['archivos'] [$regla ['field']] )) {
				$valor = $this->input->post ( $regla ['field'], TRUE );

				if (is_array ( $valor ))
					$valor = json_encode ( $valor );

				if ($valor == false)
					$valor = '';
			} else
				$valor = $reglas ['archivos'] [$regla ['field']] ['file_name'];

			$data = array (
					'id_respuesta' => $last_id,
					'id_campo' => $regla ['field'],
					'respuesta' => $valor
			);

			$this->db->insert ( 'usuarios_respuestas_formularios', $data );
		}

		$this->db->select ( 'titulo' );
		$this->db->from ( 'formularios' );
		$this->db->where ( 'id', $id_formulario );

		$q = $this->db->get ();

		if ($q->num_rows () == 1) {

			$row = $q->row ();

			$this->load->model ( 'Cron_email_model' );
			$base_url = base_url ();
			$urlval = base_url () . 'adminpanel/micuenta/misinscripciones';

			$message = <<<HTML
Has completado correctamente el formulario "$row->titulo" el día $fecha_hoy \n
Puedes ver tus inscripciones y evaluaciones desde el panel de usuario en el siguiente link: \n
$urlval
HTML;
			if (! $this->Cron_email_model->agregarAcola ( $id_formulario, $this->auth->id_usuario (), $this->auth->email_usuario ( array (
					'id' => $this->auth->id_usuario ()
			) ), 'Has completado el formulario ' . $row->titulo, $message, true )) {
				return 'not_sent';
			}

			if ($this->config->item ( 'diploma_habilitado' )) {
				// Mandamos el diploma, en caso que se deba
				$this->Diplomas_model->enviarDiploma ( $id_formulario, $this->auth->id_usuario (), $last_id );
			}
		}

		return true;
	}


	function cumpleCondicion($formulario = array(), $dependencia = '', $dependenciavalor = '') {
		$valor = '';

		if ($dependencia != 'NULL') {

			foreach ( $formulario as $campo ) {

				if ($campo ['id'] == $dependencia) {

					$valor = $this->input->post ( $dependencia, TRUE );
					break;
				}
			}
			if ($campo ['tipo'] == 'grupo') {
				return true;
			} elseif ($campo ['tipo'] == 'checkboxgroup') {
				return (($valor != '') and ($valor != false));
			} else {
				return (trim ( $dependenciavalor ) == trim ( $valor ));
			}
		} else {

			return true;
		}
	}
	function generarReglasNivelesInferiores($formulario = array(), &$archivos = array(), &$reglas = array(), $id_superior = '') {
		$noValidan = array (
				'separador',
				'descarga'
		); // elementos que no hay que validar

		foreach ( $formulario as $campo ) {

			$hijos = array ();

			if (! in_array ( $campo ['tipo'], $noValidan )) { // SI NO HAY QUE VALIDAR, ENTONCES LO IGNORAMOS

				$id = $campo ['id'];

				if (($campo ['dependencia'] == $id_superior) and ($this->cumpleCondicion ( $formulario, $campo ['dependencia'], trim ( $campo ['dependenciavalor'] ) ))) {

					if ($campo ['tipo'] != 'grupo') {
						$validacion = '';
						if (isset ( $campo ['opciones'] ['validacion'] ) && ($campo ['tipo'] != 'carga')) {
							foreach ( $campo ['opciones'] ['validacion'] as $regla ) {

								if ($validacion == '')
									$validacion = $regla;
								else
									$validacion .= '|' . $regla;

								$this->form_validation->set_message ( $regla, $this->reglaTexto ( $regla ) );
							}
						}

						if (isset ( $campo ['opciones'] ['obligatorio'] ) && ($campo ['tipo'] != 'carga') && ($campo ['opciones'] ['obligatorio'] == true)) {

							if ($validacion == '')
								$validacion = 'required';
							else
								$validacion .= '|required';

							$this->form_validation->set_message ( 'required', $this->reglaTexto ( 'required' ) );
						}

						if ($campo ['tipo'] == 'carga') {

							$archivos [] = $id;
						}

						$reglas [] = array (
								'field' => $id,
								'label' => str_replace ( ':', '', $campo ['opciones'] ['label'] ),
								'rules' => $validacion
						);
					}
					$this->generarReglasNivelesInferiores ( $formulario, $archivos, $reglas, $id );
				}
			}
		}

		return $reglas;
	}
	function generarReglas($formulario = array()) {
		$archivos = array ();
		$reglas = array ();
		$this->generarReglasNivelesInferiores ( $formulario, $archivos, $reglas, 'NULL' );

		return array (
				'reglas' => $reglas,
				'archivos' => $archivos
		);
	}
	function esRequerido($formulario = array(), $id = '') {
		$requerido = false;

		foreach ( $formulario as $campo ) {

			if ($campo ['id'] == $id) {

				$requerido = (isset ( $campo ['opciones'] ['obligatorio'] ) && ($campo ['opciones'] ['obligatorio'] == true));
				break;
			}
		}

		return $requerido;
	}
	function validar($formulario = array()) {
		$this->form_validation->set_error_delimiters ( '<span class="textoerror">[*] ', '</span>' );

		$reglas_archivos = $this->generarReglas ( $formulario );

		$reglas = $reglas_archivos ['reglas'];

		$this->form_validation->set_rules ( $reglas );

		$archivos = $reglas_archivos ['archivos'];

		if ($this->form_validation->run () == FALSE) {

			foreach ( $archivos as $id ) { // SUBIMOS SOLO LOS ARCHIVOS QUE DEBAN SER SUBIDOS

				$d_error = 'Recuerda subir los archivos nuevamente.';

				$this->form_validation->add_custom_error ( $id, $d_error );
				$this->form_validation->run ();
			}

			return false;
		} else {

			$config ['upload_path'] = $this->config->item ( 'upload_path' );
			$config ['allowed_types'] = 'odt|docx|doc|pdf|jpeg|jpg|png';
			$config ['max_size'] = '20480'; // 20MB
			$config ['encrypt_name'] = true;
			$this->load->library ( 'upload', $config );

			if (! empty ( $archivos )) {
				$data = true;
				foreach ( $archivos as $id ) {

					$d_error = '<span class="alert">Recuerda subir los archivos nuevamente.</span>';

					$this->form_validation->add_custom_error ( $id, $d_error );
					$this->form_validation->run ();
				}

				foreach ( $archivos as $id ) { // SUBIMOS SOLO LOS ARCHIVOS QUE DEBAN SER SUBIDOS

					if ($data != false) { // / ESTO MODIFIQUE LA ULTIMA VEZ, NO SE QUE ONDA

						if (! $this->upload->do_upload ( $id )) {

							if ($_FILES [$id] ['error'] == 4) { // ES VACIO, Y ES REQUERIDO MOSTRAMOS ERROR, SINO TODO ESTÁ OK

								if ($this->esRequerido ( $formulario, $id ))
									$data = false;
							} else {
								$data = false;
							}

							$display_error = trim ( strip_tags ( $this->upload->display_errors () ) );

							if ($display_error == 'You did not select a file to upload.')
								$d_error = 'No has seleccionado un archivo a subir';
							elseif ($display_error == 'The filetype you are attempting to upload is not allowed.')
								$d_error = 'El tipo de archivo que intentas subir no está permitido.';

							$this->form_validation->add_custom_error ( $id, $d_error );
							$this->form_validation->run ();
						} else {

							if (! is_array ( $data ))
								$data = array ();

							$data [$id] = $this->upload->data ();
						}
					}
				}
				if ($data != false)
					return array (
							'reglas' => $reglas,
							'archivos' => $data
					);
				else
					return false;
			} else {
				return array (
						'reglas' => $reglas,
						'archivos' => array ()
				);
			}
		}
	}
	function limpiarURL($str) {
		$tildes = array (
				'á',
				'é',
				'í',
				'ó',
				'ú',
				'ñ',
				'Á',
				'É',
				'Í',
				'Ó',
				'Ú',
				'Ñ'
		);
		$vocales = array (
				'a',
				'e',
				'i',
				'o',
				'u',
				'n',
				'A',
				'E',
				'I',
				'O',
				'U',
				'N'
		);
		$str = trim ( $str );
		$str = preg_replace ( '~\s{2,}~', ' ', $str );
		$str = str_replace ( $tildes, $vocales, $str );

		$str = preg_replace ( "/[^a-zA-Z0-9-\s]/", '', $str );

		// Quitar espacios
		$str = str_replace ( " ", "-", $str );

		// Pasar a minúsculas
		$str = strtolower ( $str );
		return $str;
	}
}
?>
