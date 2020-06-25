<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Admin_registros_model extends CI_Model {

	function __construct() {
		parent::__construct ();

		$this->load->model ( 'Admin_formularios_model' );
		$this->load->model ( 'Admin_model' );
		$this->load->model ( 'Cron_email_model' );

		$this->Admin_formularios_model = new Admin_formularios_model ();

		$this->Admin_model = new Admin_model ();

		$this->Cron_email_model = new Cron_email_model ();
	}


	function listarRegistros($id = '', $filtro = '') {

		$formulario = $this->Admin_formularios_model->obtenerFormulario ( $id );

		$formularioInfo = $this->Admin_formularios_model->obtenerInfoFormularios ( $id );

		/* CHECKEAMOS PERMISOS */
		$permvercompleto = false;
		if ($this->auth->tengoPermisoDeCompanero ( 'registros_ver_completo', $formularioInfo ['id_usuario'] ) and ($this->auth->tieneAcceso ( 'formularios_modificar', true )))
			$permvercompleto = true;

		/* CHECKEAMOS PERMISOS */
		$permver = false;
		if ($this->auth->tengoPermisoDeCompanero('registros_ver_completo', $formularioInfo['id_usuario']))
			$permver = true;

		/* CHECKEAMOS PERMISOS */
		$permhabilitado = false;
		if ($this->auth->tengoPermisoDeCompanero ( 'registros_marcar_habilitado', $formularioInfo ['id_usuario'] ) and ($this->auth->tieneAcceso ( 'formularios_modificar', true )))
			$permhabilitado = true;

		/* CHECKEAMOS PERMISOS */
		$permpago = false;
		if ($this->auth->tengoPermisoDeCompanero ( 'registros_marcar_pago', $formularioInfo ['id_usuario'] ) and ($this->auth->tieneAcceso ( 'formularios_modificar', true )))
			$permpago = true;

		$retorno = array ();
		$this->db->select ( 'usuario_responde_formulario.id,usuario_responde_formulario.id_usuario,cuotas,pago1,pago2,pago3,habilitado,diploma,notacolor,notatexto,seleccionado,fecha_respuesta' );
		$this->db->from ( 'usuario_responde_formulario' );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
		// $this->db->order_by('fecha_respuesta','asc');
		$this->db->order_by ( 'habilitado', 'desc' );
		$this->db->order_by ( 'seleccionado', 'desc' );

		if ($filtro == 'habilitado')
			$this->db->where ( 'usuario_responde_formulario.habilitado', '1' );
		elseif ($filtro == 'pago')
			$this->db->where ( 'usuario_responde_formulario.pago1 !=', '' );

		$q = $this->db->get ();

		$formsEvaluacion = $this->Admin_formularios_model->obtenerEvaluacionVinculada ( $id ); // Formulario de eval

		foreach ( $q->result () as $row ) // Por cada respuesta
		{
			$idRegistro = $row->id;
			$campos = array ();
			foreach ( $formulario as $campo ) {

				if (($campo ['tipo'] == 'textbox') and (isset ( $campo ['opciones'] ['mostrarenlistado'] )) and ($campo ['opciones'] ['mostrarenlistado'] == true)) {

					$respuesta = $this->obtenerRespuestaCampo ( $campo ['id'], $idRegistro );

					if (empty ( $respuesta ))
						$res = '';
					else
						$res = $respuesta ['respuesta'];

					$campos [$campo ['id']] = $res;
				}
			}

			$becas = $this->obtenerBecasRegistro ( $idRegistro );

			/*
			 * VEMOS SI EL USUARIO EVALUO
			 */

			$tieneEvaluacion = false;
			$usuYaEvaluo = true;

			if (empty ( $formsEvaluacion )) {
				$tieneEvaluacion = false;
				$usuYaEvaluo = false;
			} else {

				foreach ( $formsEvaluacion as $formEvaluacion ) {

					$usuYaEvaluo = $usuYaEvaluo && $this->usuarioYaEvaluo ( $formEvaluacion, $row->id_usuario ); // EL usuario realizó la evaluación?
					$tieneEvaluacion = true;
				}
			}

			$diploma = '';
			if ($row->diploma != '')
				$diploma = base_url () . 'archivos/diplomas/' . $row->diploma;

			$retorno [] = array (
					'id' => $idRegistro,
					'tieneEvaluacion' => $tieneEvaluacion,
					'usuYaEvaluo' => $usuYaEvaluo,
					'diploma' => $diploma,
					'fecha' => $row->fecha_respuesta,
					'cuotas' => $row->cuotas,
					'pago1' => $row->pago1,
					'pago2' => $row->pago2,
					'pago3' => $row->pago3,
					'notacolor' => $row->notacolor,
					'notatexto' => $row->notatexto,
					'habilitado' => $row->habilitado,
					'seleccionado' => $row->seleccionado,
					'becas' => $becas,
					'permcompleto' => $permvercompleto,
					'permver' => $permver,
					'permhabilitado' => $permhabilitado,
					'permpago' => $permpago,
					'sedebeabonar' => $formularioInfo ['abonar'],
					'campos' => $campos
			);
		}

		return array (
				'cantidad' => count ( $retorno ),
				'listado' => $retorno
		);
	}


	function usuarioFueInscripto($id = 0, $id_usuario = null) {
		$this->db->select ();
		$this->db->from ( 'usuario_responde_formulario,formularios' );

		$this->db->where ( 'usuario_responde_formulario.id_usuario', $id_usuario );

		$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', FALSE );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
		$q = $this->db->get ();

		if ($q->num_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}


	function usuarioYaEvaluo($formEvaluacion = array(), $id_usuario = null) {

		/* Averiguamos si en caso que haya que evaluar, el usuario evaluó */
		if (! empty ( $formEvaluacion )) {

			$hoy = date ( 'Y-m-d H:m:s' );
			$fecha_inicio = $formEvaluacion ['fechainicio'];

			if (strtotime ( $hoy ) >= strtotime ( $fecha_inicio )) {

				if ($this->usuarioFueInscripto ( $formEvaluacion ['id'], $id_usuario )) {

					return true;
				} else {

					return false;
				}
			}
		} else {

			return false;
		}
	}


	function obtenerCantidadRegistros($id = '') {
		$this->db->from ( 'usuario_responde_formulario' );
		$this->db->where ( 'id_formulario', $id );
		return $this->db->count_all_results ();
	}


	function obtenerTodasRespuestaCampo($idCampo = '', $idFormulario = '') {

		$archivos = array ();
		$this->db->select ( 'fecha_respuesta,usuario_responde_formulario.id as respuesta_id,id_campo,respuesta,cuotas,pago1,pago2,pago3,habilitado' );
		$this->db->from ( 'usuario_responde_formulario,usuarios_respuestas_formularios' );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', $idFormulario );
		$this->db->where ( 'usuarios_respuestas_formularios.id_respuesta', 'usuario_responde_formulario.id', false );
		$this->db->order_by ( 'fecha_respuesta', 'desc' );
		$q = $this->db->get ();

		$retorno = '';
		foreach ( $q->result () as $row ) {

			if ($row->id_campo == $idCampo) {
				if ($this->Admin_model->isJSON ( $row->respuesta )) {
					$retorno [$row->respuesta_id] = array (
							'respuestas' => json_decode ( $row->respuesta, TRUE )
					);
				} else {

					$respuesta = array ();

					if ($row->respuesta != '')
						$respuesta = array (
								$row->respuesta
						);

					$retorno [$row->respuesta_id] = array (
							'respuestas' => $respuesta
					);
				}
			}
		}

		return $retorno;
	}


	function obtenerRespuestaCampo($idCampo = '', $idRegistro = '') {
		$archivos = array ();
		$this->db->select ('fecha_respuesta,usuario_responde_formulario.id as respuesta_id,id_campo,respuesta,cuotas,pago1,pago2,pago3,habilitado');
		$this->db->from ( 'usuario_responde_formulario,usuarios_respuestas_formularios' );
		$this->db->where ( 'usuario_responde_formulario.id', $idRegistro );
		$this->db->where ( 'usuarios_respuestas_formularios.id_respuesta', 'usuario_responde_formulario.id', false );
		$this->db->order_by ( 'fecha_respuesta', 'desc' );
		$q = $this->db->get ();

		$retorno = array ();

		foreach ( $q->result () as $row ) {

			if ($row->id_campo == $idCampo) {
				if ($this->Admin_model->isJSON ( $row->respuesta )) {
					$retorno = array (
							'id_respuesta' => $row->respuesta_id,
							'respuesta' => json_decode ( $row->respuesta, TRUE )
					);
				} else {

					$respuesta = '';

					if ($row->respuesta != '')
						$respuesta = $row->respuesta;

					$retorno = array (
							'id_respuesta' => $row->respuesta_id,
							'respuesta' => $respuesta
					);
				}
			}
		}

		return $retorno;
	}


	function usuarioRealizoEvaluacion($id_formulario = 0, $id_usuario = 0) {
		$this->db->select ();
		$this->db->from ( 'usuario_responde_formulario' );
		$this->db->where ( 'usuario_responde_formulario.id_usuario', $id_usuario );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', $id_formulario );
		$q = $this->db->get ();

		if ($q->num_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}


	function descargarArchivos($idFormulario) {
		$archivos = array ();
		$formulario = $this->Admin_formularios_model->obtenerFormulario ( $idFormulario, FALSE, $nombre, $idForm );
		$this->load->library ( 'zip' );

		foreach ( $formulario as $campo ) {

			$ret = array ();

			if ($campo ['tipo'] == 'carga') {

				$respuesta = $this->obtenerTodasRespuestaCampo ( $campo ['id'], $idFormulario );

				foreach ( $respuesta as $id => $resp ) {

					if (! empty ( $resp ['respuestas'] )) {

						// echo $this->config->item('upload_path').$resp['respuestas'][0];

						$this->zip->read_file ( 'archivos/' . $resp ['respuestas'] [0], $id . '/' . $campo ['opciones'] ['label'] . '/' . $resp ['respuestas'] [0] );
					}
				}
			}
		}

		$this->zip->download ( 'archivo.zip' );
	}

	/*
	 *
	 *
	 * ESTO ES LO QUE GENERA LOS RESUMENES DE LAS EVALUACIONES
	 *
	 *
	 */
	function generarResumen($idFormulario = '') {
		$retorno = array ();
		$grupos = array ();
		$formulario = $this->Admin_formularios_model->obtenerFormulario ( $idFormulario, FALSE, $nombre, $idForm );

		foreach ( $formulario as $campo ) {

			$ret = array ();

			if ($campo ['tipo'] == 'carga') {
			} elseif ($campo ['tipo'] == 'separador') {

				$ret = array (
						'id' => $campo ['id'],
						'tipo' => $campo ['tipo'],
						'titulo' => $campo ['opciones'] ['titulo']
				);
			} elseif ($campo ['tipo'] == 'grupo') {

				$grupos [$campo ['id']] ['id'] = $campo ['id'];
				$grupos [$campo ['id']] ['tipo'] = $campo ['tipo'];
				$grupos [$campo ['id']] ['titulo'] = $campo ['opciones'] ['titulo'];
			} elseif ($campo ['tipo'] == 'descarga') {
			} elseif (($campo ['tipo'] == 'checkboxgroup') or ($campo ['tipo'] == 'radiogroup')) {

				$respuestas = $this->obtenerTodasRespuestaCampo ( $campo ['id'], $idFormulario );

				$total = count ( $respuestas );

				$respAux = array ();

				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {

					$respAux [$opcion] = 0;

					if ((isset ( $respuestas )) && (is_array ( $respuestas ))) {
						foreach ( $respuestas as $id => $respuesta ) {

							if (is_array ( $respuesta ['respuestas'] )) {

								foreach ( $respuesta ['respuestas'] as $resp ) {

									if ($resp == $opcion)
										$respAux [$opcion] ++;
								}
							} else {

								if ($respuesta ['respuestas'] == $opcion)
									$respAux [$opcion] ++;
							}
						}
					}
				}

				$ret = array (
						'id' => $campo ['id'],
						'tipo' => $campo ['tipo'],
						'label' => $campo ['opciones'] ['label'],
						'respuestas' => $respAux,
						'total' => $total
				);
			} else {
				$respuestas = $this->obtenerTodasRespuestaCampo ( $campo ['id'], $idFormulario );

				$total = count ( $respuestas );

				$ret = array (
						'id' => $campo ['id'],
						'tipo' => $campo ['tipo'],
						'label' => $campo ['opciones'] ['label'],
						'respuestas' => $respuestas,
						'total' => $total
				);
			}

			if ($campo ['dependencia'] != "NULL") {

				$campoPadre = $this->Admin_formularios_model->obtenerCamposFormulario ( $formulario, $campo ['dependencia'] );

				if ($campoPadre ['tipo'] == 'grupo') {
					if (! empty ( $ret ))
						$grupos [$campo ['dependencia']] ['hijos'] [] = $ret;
				} else {
					if (! empty ( $ret ))
						$retorno [] = $ret;
				}
			} else {
				if (! empty ( $ret ))
					$retorno [] = $ret;
			}
		}

		if (! empty ( $grupos )) {
			foreach ( $grupos as $key => $valor ) {

				$retorno [] = $valor;
			}
		}

		return $retorno;
	}


	function imprimirResumen($campos, &$i, $nivel, $titulonivel = '') {
		$formatoTitulos = array (
				'fill' => array (
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array (
								'argb' => 'FFCCFFCC'
						)
				),
				'borders' => array (
						'bottom' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN
						),
						'right' => array (
								'style' => PHPExcel_Style_Border::BORDER_MEDIUM
						)
				)
		);

		$formatoTitulosTabla = array (
				'fill' => array (
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array (
								'argb' => '12121212'
						)
				),
				'borders' => array (
						'bottom' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN
						),
						'right' => array (
								'style' => PHPExcel_Style_Border::BORDER_MEDIUM
						)
				),
				'font' => array (
						'bold' => true,
						'color' => array (
								'argb' => 'FFFFFFFF'
						)
				)
		);

		$formatoTotalTabla = array (
				'fill' => array (
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array (
								'argb' => 'E3E3E3E3'
						)
				),
				'font' => array (

						'bold' => true
				)

		);

		$formatoSeparador = array (
				'fill' => array (
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array (
								'argb' => 'e3e3e3'
						)
				),
				'borders' => array (
						'bottom' => array (
								'style' => PHPExcel_Style_Border::BORDER_MEDIUM
						)
				)
		);

		if ($nivel == 0) {
			$this->excel->setActiveSheetIndex ( 0 );
			$this->excel->getActiveSheet ()->setTitle ( 'Datos Generales' );
		} else {
			$this->excel->createSheet ( $nivel );
			$this->excel->setActiveSheetIndex ( $nivel );
			$this->excel->getActiveSheet ()->setTitle ( substr ( $titulonivel, 0, 30 ) )->setCellValue ( 'A1', $titulonivel )->getStyle ( 'A1' )->applyFromArray ( $formatoSeparador );
			$this->excel->getActiveSheet ()->mergeCells ( 'A1:P1' );
			$i ++;
		}
		foreach ( $campos as $campo ) {

			$i ++;

			if ($campo ['tipo'] == 'grupo') {

				$nivel ++; // Nos vamos dentro de un grupo, así que vamos al proximo worksheet
				$z = 0;

				if (isset ( $campo ['hijos'] )) {

					$this->imprimirResumen ( $campo ['hijos'], $z, $nivel, $campo ['titulo'] );
				}
			} elseif ($campo ['tipo'] == 'separador') {

				$this->excel->getActiveSheet ()->setCellValue ( 'A' . $i, $campo ['titulo'] )->getStyle ( 'A' . $i )->applyFromArray ( $formatoSeparador );
				$this->excel->getActiveSheet ()->mergeCells ( 'A' . $i . ':Q' . $i );
				$i ++;
			} elseif ($campo ['tipo'] == 'descarga') {
			} elseif (($campo ['tipo'] == 'checkboxgroup') or ($campo ['tipo'] == 'radiogroup')) {

				$this->excel->getActiveSheet ()->setCellValue ( 'A' . $i, $campo ['label'] )->getStyle ( 'A' . $i )->applyFromArray ( $formatoTitulos );
				$this->excel->getActiveSheet ()->mergeCells ( 'A' . $i . ':Q' . $i );
				$i ++;

				$titulo = $this->excel->getActiveSheet ()->getTitle ();

				$respuestas = array ();
				$totalRespuestas = 0;
				$respuestas [] = array (
						'Opción',
						'Valor',
						'Porcentaje'
				);
				foreach ( $campo ['respuestas'] as $texto => $respuesta ) {

					if (empty ( $respuesta ))
						$respuesta = '0';

					$respuestas [] = array (
							$texto,
							$respuesta
					);

					$totalRespuestas = $totalRespuestas + $respuesta;
				}

				foreach ( $respuestas as $key => &$respuesta ) {
					if ($key != 0) {

						if ($totalRespuestas == 0)
							$totalRespuestas = 1;

						$respuesta [2] = number_format ( $respuesta [1] * 100 / $totalRespuestas, 1 ) . '%';
					}
				}

				$altoTabla = count ( $respuestas );

				$this->excel->getActiveSheet ()->setCellValue ( 'A' . ($i + $altoTabla), 'Total' );
				$this->excel->getActiveSheet ()->setCellValue ( 'B' . ($i + $altoTabla), $totalRespuestas );
				$this->excel->getActiveSheet ()->getStyle ( 'A' . ($i + $altoTabla) )->applyFromArray ( $formatoTotalTabla );
				$this->excel->getActiveSheet ()->getStyle ( 'B' . ($i + $altoTabla) )->applyFromArray ( $formatoTotalTabla );

				$this->excel->getActiveSheet ()->fromArray ( $respuestas, NULL, 'A' . $i );
				$this->excel->getActiveSheet ()->getColumnDimension ( 'A' )->setAutoSize ( true );

				$this->excel->getActiveSheet ()->getStyle ( 'A' . $i )->applyFromArray ( $formatoTitulosTabla );
				$this->excel->getActiveSheet ()->getStyle ( 'B' . $i )->applyFromArray ( $formatoTitulosTabla );
				$this->excel->getActiveSheet ()->getStyle ( 'C' . $i )->applyFromArray ( $formatoTitulosTabla );

				$dataseriesLabels1 = array ()
				// new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$'.$i.':$B$'.($i+$altoTabla-1), NULL, 1)
				;

				$xAxisTickValues1 = array (
						new PHPExcel_Chart_DataSeriesValues ( 'String', "'" . $titulo . "'" . '!$A$' . $i . ':$A$' . ($i + $altoTabla - 1), NULL, 1 )
				);
				$dataSeriesValues1 = array (

						new PHPExcel_Chart_DataSeriesValues ( 'String', "'" . $titulo . "'" . '!$B$' . $i . ':$B$' . ($i + $altoTabla - 1), NULL, 1 )
				)
				;

				// Build the dataseries
				$series1 = new PHPExcel_Chart_DataSeries ( PHPExcel_Chart_DataSeries::TYPE_BARCHART, // plotType
												PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // plotGrouping
												range ( 0, count ( $dataSeriesValues1 ) - 1 ), // plotOrder
												$dataseriesLabels1, // plotLabel
												$xAxisTickValues1, // plotCategory
												$dataSeriesValues1 ) // plotValues
												;

				$layout1 = new PHPExcel_Chart_Layout ();
				$layout1->setShowVal ( TRUE ); // Initializing the data labels with Values
				$layout1->setShowPercent ( TRUE );

				// Set the series in the plot area
				$plotarea1 = new PHPExcel_Chart_PlotArea ( $layout1, array (
						$series1
				) );
				// Set the chart legend
				$legend1 = new PHPExcel_Chart_Legend ( PHPExcel_Chart_Legend::POSITION_TOPRIGHT, NULL, false );

				$title1 = new PHPExcel_Chart_Title ( $campo ['label'] );
				$yAxisLabel1 = new PHPExcel_Chart_Title ( 'Cantidad de Respuestas' );
				$xAxisLabel1 = new PHPExcel_Chart_Title ( 'Opciones' );

				// Create the chart
				$chart1 = new PHPExcel_Chart ( 'chart' . $i, // name
																				$title1, // title
																				$legend1, // legend
																				$plotarea1, // plotArea
																				true, // plotVisibleOnly
																				0, // displayBlanksAs
																				$xAxisLabel1, // xAxisLabel
																				$yAxisLabel1 ) // yAxisLabel
																				;

				// Set the position where the chart should appear in the worksheet
				$chart1->setTopLeftPosition ( 'D' . $i );
				$chart1->setBottomRightPosition ( 'Q' . ($i + 20) );

				// Add the chart to the worksheet
				$this->excel->getActiveSheet ()->addChart ( $chart1 );

				$i = ($i + 20);
			} else {

				$this->excel->getActiveSheet ()->setCellValue ( 'A' . $i, $campo ['label'] . " (" . $campo ['total'] . ')' )->getStyle ( 'A' . $i )->applyFromArray ( $formatoTitulos );

				if (is_array ( $campo ['respuestas'] )) {

					foreach ( $campo ['respuestas'] as $key => $resp ) {

						foreach ( $resp ['respuestas'] as $res ) {
							$i ++;
							$this->excel->getActiveSheet ()->setCellValue ( 'A' . $i, trim ( $res ) );
						}
					}
				} else {
					$i ++;
					$this->excel->getActiveSheet ()->setCellValue ( 'A' . $i, 'Sin datos' );
				}
			}
		}
	}

	function generarResumenCompleto($idForm) {

		$retorno = array ();
		$camposExportar = array ();

		$formularioInfo = $this->Admin_formularios_model->obtenerInfoFormularios ( $idForm );
		$formulario = $this->Admin_formularios_model->obtenerFormulario ( $idForm );

		$registros = $this->Admin_formularios_model->obtenerRegistros ( $idForm );

		foreach ( $registros as $id => $registro ) {

			$dato = $this->obtenerInfoRegistro ( $id );

			$retorno [$id] ['habilitado'] = $dato ['habilitado'];
			$retorno [$id] ['cuotas'] = $dato ['cuotas'];
			$retorno [$id] ['pago1'] = $dato ['pago1'];
			$retorno [$id] ['pago2'] = $dato ['pago2'];
			$retorno [$id] ['pago3'] = $dato ['pago3'];
			$retorno [$id] ['becas'] = $dato ['becas'];
			foreach ( $formulario as $campo ) {


				if ($campo ['tipo'] == 'carga') {
				} elseif ($campo ['tipo'] == 'separador') {
				} elseif ($campo ['tipo'] == 'grupo') {
				} elseif ($campo ['tipo'] == 'descarga') {
				} else {

					$camposExportar [$campo ['id']] = array (
							'id' => $campo ['id'],
							'label' => $campo ['opciones'] ['label']
					);

					$respuesta = $this->obtenerRespuestaCampo ( $campo ['id'], $id );

					if (empty ( $respuesta ))
						$resp = '';
						else
							$resp = $respuesta ['respuesta'];

							$retorno [$id] ['respuestas'] [] = $resp;
				}

			}
		}

		$excel = "<table border='0' style='font-family: Arial'>";
		$excel .= '<tr>';
		$excel .= "<th>ID</th>";
		foreach ( $camposExportar as $campo ) {
			$excel .= "<th>" . $campo ['label'] . "</th>";
		}
		$excel .= "</tr>";

		foreach ( $retorno as $idResp => $datos ) {
			$excel .= "<tr>";
			$excel .= "<td>" . $idResp . "</td>";
			foreach ( $datos ['respuestas'] as $respuesta ) {

				if (isset ( $respuesta ) && (! empty ( $respuesta ))) {

					if (is_array ( $respuesta )) {
						$resaux = '';
						$i = 1;
						foreach ( $respuesta as $res ) {

							$resaux .= $res;

							if ($i != count ( $respuesta ))
								$resaux .= ', ';

								$i ++;
						}

						$respuesta = $resaux;
					}

					$excel .= '<td>' . $respuesta . '</td>';
				} else
					$excel .= '<td></td>';
			}

			$excel .= "</tr>";
		}
		$excel .= "</table>";

		return $excel;

	}

	function exportarevacompleto($idForm = '') {
		return $this->generarResumenCompleto ( $idForm );
	}

	function exportareva($idForm = '', $filename = '') {
		$this->load->library ( 'excel' );
		$datosEva = $this->generarResumen ( $idForm );

		$i = 0;
		$this->imprimirResumen ( $datosEva, $i, 0 ); // llamamos a la funcion que genera el excel. '0' es el nivel, para saber en qué worksheet estamos.

		// Redirect output to a client’s web browser (Excel2007)
		header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xlsx"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0

		// save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		                           // if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter ( $this->excel, 'Excel2007' );
		$objWriter->setIncludeCharts ( TRUE );
		// force user to download the Excel file without writing it to server's HD
		$objWriter->save ( 'php://output' );
	}


	function exportar($idForm = '', $campos = array(), $filtros = 'todos') {

		$retorno = array ();
		$camposExportar = array ();

		$formularioInfo = $this->Admin_formularios_model->obtenerInfoFormularios($idForm);
		$formulario = $this->Admin_formularios_model->obtenerFormulario($idForm);

		$registros = $this->Admin_formularios_model->obtenerRegistros($idForm );

		foreach ( $registros as $id => $registro ) {

			$dato = $this->obtenerInfoRegistro($id);

			$add = false;

			if ($filtros == 'todos') {

				$add = true;
			} elseif ($filtros == 'solohabilitados') {

				if ($dato ['habilitado'] == 1)
					$add = true;
			} elseif ($filtros == 'nohabilitados') {

				if ($dato ['habilitado'] != 1)
					$add = true;
			} elseif ($filtros == 'solopagos') {

				if ($dato ['pago1'] != '')
					$add = true;
			} elseif ($filtros == 'nopagos') {

				if ($dato ['pago1'] == '')
					$add = true;
			} elseif ($filtros == 'conbeca') {

				if (! empty ( $dato ['becas'] ))
					$add = true;
			} elseif ($filtros == 'sinbeca') {

				if (empty ( $dato ['becas'] ))
					$add = true;
			/*
			} elseif ($filtros == 'sorteados') {

					if (isset($dato['seleccionado']) && ($dato['seleccionado'] == 1) )
						$add = true;
			*/
			} else {

				$add = true;
			}

			if ($add) {

				$retorno [$id] ['habilitado'] = $dato ['habilitado'];
				$retorno [$id] ['cuotas'] = $dato ['cuotas'];
				$retorno [$id] ['pago1'] = $dato ['pago1'];
				$retorno [$id] ['pago2'] = $dato ['pago2'];
				$retorno [$id] ['pago3'] = $dato ['pago3'];
				$retorno [$id] ['becas'] = $dato ['becas'];

				foreach ( $formulario as $campo ) {

					if (in_array ( $campo ['id'], $campos )) {

						if ($campo ['tipo'] == 'carga') {
						} elseif ($campo ['tipo'] == 'separador') {
						} elseif ($campo ['tipo'] == 'grupo') {
						} elseif ($campo ['tipo'] == 'descarga') {
						} else {

							$camposExportar [$campo ['id']] = array (
									'id' => $campo ['id'],
									'label' => $campo ['opciones'] ['label']
							);

							$respuesta = $this->obtenerRespuestaCampo ( $campo ['id'], $id );

							if (empty ( $respuesta ))
								$resp = '';
							else
								$resp = $respuesta ['respuesta'];

							$retorno [$id] ['respuestas'] [] = $resp;
						}
					}
				}
			}
		}

		$excel = "<table border='0' style='font-family: Arial'>";
		$excel .= '<tr>';
		$excel .= "<th>ID</th>";
		foreach ( $camposExportar as $campo ) {
			$excel .= "<th>" . $campo ['label'] . "</th>";
		}
		$excel .= "<th>Pago</th>";
		$excel .= "<th>Cuotas</th>";
		$excel .= "<th>Nº Cuota 1</th>";
		$excel .= "<th>Nº Cuota 2</th>";
		$excel .= "<th>Nº Cuota 3</th>";
		$excel .= "<th>Monto Pago</th>";
		$excel .= "<th>Habilitado</th>";
		$excel .= "<th>Becas</th>";
		$excel .= "</tr>";

		foreach ( $retorno as $idResp => $datos ) {
			$excel .= "<tr>";
			$excel .= "<td>" . $idResp . "</td>";
			foreach ( $datos ['respuestas'] as $respuesta ) {

				if (isset ( $respuesta ) && (! empty ( $respuesta ))) {

					if (is_array ( $respuesta )) {
						$resaux = '';
						$i = 1;
						foreach ( $respuesta as $res ) {

							$resaux .= $res;

							if ($i != count ( $respuesta ))
								$resaux .= ', ';

							$i ++;
						}

						$respuesta = $resaux;
					}

					$excel .= '<td>' . $respuesta . '</td>';
				} else
					$excel .= '<td></td>';
			}
			$montofinal = '-';
			$cuotas = $datos ['cuotas'];
			$pagadas = 0;

			if ( ($datos ['pago1'] != null) && ($datos ['pago1'] != '')) {
				$pagadas++;
			}
			if ( ($datos ['pago2'] != null) && ($datos ['pago2'] != '')) {
				$pagadas++;
			}
			if ( ($datos ['pago3'] != null) && ($datos ['pago3'] != '')) {
				$pagadas++;
			}

			if ($cuotas == $pagadas) {
				$montofinal = $formularioInfo ['costocurso'];
				$pago = 'Si';
				$colorP = 'style="background-color:green;color:white;"';
			} else {
				$pago = 'No';
				$colorP = 'style="background-color:red;color:white;"';
			}

			if ($datos ['habilitado'] == 1) {
				$habilitado = 'Si';
				$colorH = 'style="background-color:green;color:white;"';
			} else {
				$habilitado = 'No';
				$colorH = 'style="background-color:red;color:white;"';
			}

			$excel .= "<td " . $colorP . ">" . $pago . "</td>";
			$excel .= "<td " . $colorP . ">" . $datos ['cuotas']. "</td>";
			$excel .= "<td " . $colorP . ">" . $datos ['pago1'] . "</td>";
			$excel .= "<td " . $colorP . ">" . $datos ['pago2'] . "</td>";
			$excel .= "<td " . $colorP . ">" . $datos ['pago3'] . "</td>";

			$i = 1;
			$becas = '';

			foreach ( $datos ['becas'] as $beca ) {

				$becas .= $beca ['respuesta'] . '(' . $beca ['porcentajedescuento'] . '%)';

				if ($datos ['pago1'] != '')
					$montofinal = $montofinal - (($beca ['porcentajedescuento'] * $montofinal) / 100); // HACEMOS EL PORCENTAJE

				if ($i != count ( $datos ['becas'] ))
					$becas .= ', ';

				$i ++;
			}

			if ($becas != '') {

				$colorB = 'style="background-color:orange;color:black;"';
			} else {

				$colorB = '';
			}
			$excel .= "<td " . $colorP . ">" . $montofinal . "</td>";
			$excel .= "<td " . $colorH . ">" . $habilitado . "</td>";
			$excel .= "<td " . $colorB . ">" . $becas . "</td>";

			$excel .= "</tr>";
		}
		$excel .= "</table>";

		return $excel;
	}


	function obtenerBecasRegistro($idRegistro) {
		$retorno = array ();
		$this->db->select ( 'sorteo_becas.respuesta,porcentajedescuento,fecha_sorteo' );
		$this->db->from ( 'sorteo_becas' );
		$this->db->join ( 'usuarios_respuestas_formularios', 'usuarios_respuestas_formularios.id = sorteo_becas.id_respuestas_campos', 'left' );
		$this->db->where ( 'usuarios_respuestas_formularios.id_respuesta', $idRegistro );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retorno [] = array (
					'respuesta' => $row->respuesta,
					'porcentajedescuento' => $row->porcentajedescuento,
					'fecha_sorteo' => $row->fecha_sorteo
			);
		}

		return $retorno;
	}


	function obtenerInfoRegistro($idRegistro = '') {

		$retorno = array ();
		$archivos = array ();
		$formulario = $this->Admin_formularios_model->obtenerFormulario($idRegistro, TRUE, $nombre, $idForm );

		foreach ( $formulario as $campo ) {

			if ($campo ['tipo'] == 'carga') {

				$respuesta = $this->obtenerRespuestaCampo($campo ['id'], $idRegistro);

				if (! empty ( $respuesta ['respuesta'] )) {

					$archivos [] = array (
							'id' => $campo ['id'],
							'tipo' => $campo ['tipo'],
							'label' => $campo ['opciones'] ['label'],
							'respuesta' => $respuesta ['respuesta']
					);
				}
			} elseif ($campo ['tipo'] == 'separador') {

				$retorno [] = array (
						'id' => $campo ['id'],
						'tipo' => $campo ['tipo'],
						'titulo' => $campo ['opciones'] ['titulo']
				);
			} elseif ($campo ['tipo'] == 'grupo') {
			} elseif ($campo ['tipo'] == 'descarga') {
			} else {
				$respuesta = $this->obtenerRespuestaCampo ( $campo ['id'], $idRegistro );

				if (empty ( $respuesta ))
					$resp = '';
				else
					$resp = $respuesta ['respuesta'];

				$retorno [] = array (
						'id' => $campo ['id'],
						'tipo' => $campo ['tipo'],
						'label' => $campo ['opciones'] ['label'],
						'respuesta' => $resp
				);
			}
		}

		/* OBTENEMOS LAS BECAS PARA ESE REGISTRO */
		$camposBeca = $this->obtenerBecasRegistro($idRegistro);

		return array (
				'pago1' => $this->esPago ( $idRegistro, false, 0, 1),
				'pago2' => $this->esPago ( $idRegistro, false, 0, 2),
				'pago3' => $this->esPago ( $idRegistro, false, 0, 3),
				'cuotas' => $this->getCuotas ( $idRegistro ),
				'habilitado' => $this->esHabilitado ( $idRegistro ),
				'retorno' => $retorno,
				'archivos' => $archivos,
				'nombre' => $nombre,
				'idForm' => $idForm,
				'fecha_respuesta' => $this->obtenerFechaRegistro ( $idRegistro ),
				'usuario' => $this->obtenerUsuarioRegistro ( $idRegistro ),
				'becas' => $camposBeca,
				'seleccionado' => $this->esSeleccionado($idRegistro)
		);
	}

	function esHabilitado($id = '') {
		$this->db->select ( 'habilitado' );
		$q = $this->db->get_where ( 'usuario_responde_formulario', array (
				'id' => $id
		), 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			return ($row->habilitado == 1);
		}
	}


	function randomArr(&$arr, $num = 1) {
		shuffle ( $arr );
		shuffle ( $arr );
		shuffle ( $arr );
		shuffle ( $arr );

		if ($num >= count ( $arr ))
			$num = count ( $arr );

		$r = array ();
		for($i = 0; $i < $num; $i ++) {
			$r [] = $arr [$i];
		}

		$nor = array ();
		for($i = $num; $i < count ( $arr ); $i ++) {
			$nor [] = $arr [$i];
		}

		$arr = $nor;

		return $r;
	}


	function listarRegistrosID($id = '', $filtro = '', $camposBeca = array(), $sorteo = '') {
		$retorno = array ();

		if ($filtro == 'habilitado') {

			$this->db->select ( 'usuario_responde_formulario.id,cuotas,pago1,pago2,pago3,habilitado,fecha_respuesta' );
			$this->db->from ( 'usuario_responde_formulario' );
			$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
			$this->db->where ( 'usuario_responde_formulario.habilitado', '1' );
		} elseif ($filtro == 'pago') {

			$this->db->select ( 'usuario_responde_formulario.id,cuotas,pago1,pago2,pago3,habilitado,fecha_respuesta' );
			$this->db->from ( 'usuario_responde_formulario' );
			$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
			$this->db->where ( 'usuario_responde_formulario.pago1 !=', '' );
		} elseif ($filtro == 'seleccionado') {

			$this->db->select ( 'usuario_responde_formulario.id,cuotas,pago1,pago2,pago3,habilitado,fecha_respuesta' );
			$this->db->from ( 'usuario_responde_formulario' );
			$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
			$this->db->where ( 'usuario_responde_formulario.seleccionado', '1' );
			$this->db->where ( 'usuario_responde_formulario.habilitado', '1' );
		} elseif ($filtro == 'sin_beca') { // REGISTROS DE USUARIOS SIN OTRA BECA EN EL MISMO FORMULARIO
			$this->db->select ( 'usuario_responde_formulario.id,count(sorteo_becas.id) cantidad' );
			$this->db->from ( 'usuario_responde_formulario' );
			$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
			$this->db->where ( 'usuario_responde_formulario.seleccionado', '1' );
			$this->db->where ( 'usuario_responde_formulario.habilitado', '1' );
			$this->db->join ( 'usuarios_respuestas_formularios', 'usuario_responde_formulario.id = usuarios_respuestas_formularios.id_respuesta' );
			$this->db->join ( 'sorteo_becas', 'usuarios_respuestas_formularios.id = sorteo_becas.id_respuestas_campos', 'left' );
			$this->db->group_by ( 'usuario_responde_formulario.id' );
			// La respuesta del usuario que no tenga beca. Porque no le vamos a dar doble beca viteh'
		}

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			if ($filtro == 'sin_beca') { // aca obtenemos los registros, que no tienen otras becas y que aparte califican para la beca seleccionada
				if ($row->cantidad == 0) { // Si tiene alguna beca ya, no lo sorteamos.

					/*
					 * Por cada campo que puede dar la beca seleccionada buscamos si la respuesta de la persona fue la indicada
					 *
					 */

					foreach ( $camposBeca [$sorteo] ['campos'] as $campo ) {

						$this->db->select ( 'respuesta,id' );
						$query = $this->db->get_where ( 'usuarios_respuestas_formularios', array (
								'id_campo' => $campo ['id'],
								'id_respuesta' => $row->id
						) );

						if ($query->num_rows () == 1) {

							$respuestas_campos = $query->row_array ();

							if ($this->Admin_model->isJSON ( $respuestas_campos ['respuesta'] )) { // Si es JSOn es porque es un array de respuestas

								$respuestas = json_decode ( $respuestas_campos ['respuesta'] );

								if (in_array ( $sorteo, $respuestas )) { // Si una de las respuestas es la que califica para el sorteo

									if (! in_array ( $row->id, $retorno )) // Vemos de no agregarlo dos veces, en caso que se cumpla con dos condiciones
										$retorno [$row->id] = array (
												'id_registro' => $row->id,
												'id_respuestas_campos' => $respuestas_campos ['id'],
												'respuesta' => $sorteo
										);
								}
							} elseif (trim ( $respuestas_campos ['respuesta'] ) == trim ( $sorteo )) {

								if (! in_array ( $row->id, $retorno )) // Vemos de no agregarlo dos veces, en caso que se cumpla con dos condiciones
									$retorno [$row->id] = array (
											'id_registro' => $row->id,
											'id_respuestas_campos' => $respuestas_campos ['id'],
											'respuesta' => $sorteo
									);
							}
						}
					}
				}
			} else {

				$retorno [] = $row->id;
			}
		}

		return $retorno;
	}

	/* Sorteamos las plazas para una inscripción */
	function sorteoPlaza($idForm = '', $cantidad = 0, $message = '', $messageNO = '') {

		$regHabilitados = $this->listarRegistrosID ( $idForm, 'habilitado' );

		$resultados = $this->randomArr ( $regHabilitados, $cantidad ); // $regHabilitados luego solo tiene a los no habilitados

		$formulario = $this->Admin_formularios_model->obtenerInfoFormularios ( $idForm );

		$tituloForm = $formulario ['titulo'];

		foreach ( $resultados as $idRegistro ) {

			$usuario = $this->obtenerUsuarioRegistro ( $idRegistro );
			$base_url = base_url ();

			$this->Cron_email_model->agregarAcola ( $idForm, $usuario ['id'], $usuario ['email'], 'Has sido seleccionado dentro del cupo de ' . $tituloForm, $message, false, 100 );

			$this->db->set ( 'seleccionado', 1 );
			$this->db->where ( 'id', $idRegistro );
			$this->db->update ( 'usuario_responde_formulario' );
		}

		foreach ( $regHabilitados as $idRegistro ) {

			$usuario = $this->obtenerUsuarioRegistro ( $idRegistro );
			$base_url = base_url ();

			$this->Cron_email_model->agregarAcola ( $idForm, $usuario ['id'], $usuario ['email'], 'Lamentablemente no has sido seleccionado dentro del cupo de ' . $tituloForm, $messageNO, false, 100 );
		}

		$this->db->set ( 'f_sorteo_plaza', date ( 'Y-m-d H:i:s' ) );
		$this->db->where ( 'id', $idForm );
		$this->db->update ( 'formularios' );

		return $resultados;
	}

	/* Sorteamos las plazas para una inscripción */
	function sorteoBeca($idForm = '', $cantidad, $mensaje, $sorteo, $porcentajedescuento) {
		$camposBeca = $this->Admin_formularios_model->obtenerCamposBeca ( $idForm ); // Obtenemos todos los campos del formulario, que computen para beca.

		$formulario = $this->Admin_formularios_model->obtenerInfoFormularios ( $idForm );

		$tituloForm = $formulario ['titulo'];

		$regSeleccionado = $this->listarRegistrosID ( $idForm, 'sin_beca', $camposBeca, $sorteo );

		$resultados = $this->randomArr ( $regSeleccionado, $cantidad );

		$this->db->select ( 'sorteos_realizados' );
		$q = $this->db->get_where ( 'formularios', array (
				'id' => $idForm
		), 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row_array ();
			$becasform = json_decode ( $row ['sorteos_realizados'], true );

			if (! is_array ( $becasform )) {

				$becasform = array ();
			}
		}

		$becasform [] = $sorteo; // agregamos el sorteo nuevo

		foreach ( $resultados as $datos ) {

			$this->db->insert ( 'sorteo_becas', array (
					'id_respuestas_campos' => $datos ['id_respuestas_campos'],
					'respuesta' => $datos ['respuesta'],
					'fecha_sorteo' => date ( 'Y-m-d H:i:s' ),
					'porcentajedescuento' => $porcentajedescuento
			) );

			$usuario = $this->obtenerUsuarioRegistro ( $datos ['id_registro'] );
			$base_url = base_url ();

			$message = $mensaje;

			$this->Cron_email_model->agregarAcola ( $idForm, $usuario ['id'], $usuario ['email'], 'Has sido sorteado/a en ' . $tituloForm, $message, false, 100 );
		}

		$this->db->set ( 'sorteos_realizados', json_encode ( $becasform ) );
		$this->db->where ( 'id', $idForm );
		$this->db->update ( 'formularios' );

		return $resultados;
	}


	function obtenerFechaRegistro($idRegistro = '') {
		$this->db->select ( 'fecha_respuesta' );
		$q = $this->db->get_where ( 'usuario_responde_formulario', array (
				'id' => $idRegistro
		), 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			return $row->fecha_respuesta;
		}
	}


	function obtenerUsuarioRegistro($idRegistro = '') {
		$this->db->select ( 'usuario,id_usuario,email' );
		$this->db->from ( 'usuario_responde_formulario,usuarios' );
		$this->db->where ( 'usuario_responde_formulario.id', $idRegistro );
		$this->db->where ( 'usuarios.id', 'usuario_responde_formulario.id_usuario', FALSE );
		$q = $this->db->get ();

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			return array (
					'usuario' => $row->usuario,
					'id' => $row->id_usuario,
					'email' => $row->email
			);
		} else {

			return false;
		}
	}


	function misInscripciones() {
		$this->db->select ( 'usuario_responde_formulario.id as respId,formularios.id as formId,tipo, formularios.titulo, fecha_respuesta, diploma, fecha_inicio, fecha_fin' );
		$this->db->from ( 'usuario_responde_formulario,formularios' );
		$this->db->where ( 'usuario_responde_formulario.id_usuario', $this->auth->id_usuario () );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', FALSE );
		$this->db->order_by ( 'formId', 'DESC' );
		$q = $this->db->get ();

		$result = array ();

		foreach ( $q->result () as $row ) {

			$hoy = new DateTime ( date ( 'Y-m-d H:m:s' ) );
			$fecha_inicio = new DateTime ( $row->fecha_inicio );
			$fecha_fin = new DateTime ( $row->fecha_fin );

			/* Si estamos dentro de los plazos, podemos eliminar la inscripción */
			$mostrar = false;
			if ((($hoy <= $fecha_fin) and ($hoy >= $fecha_inicio)) or ($this->Admin_formularios_model->esDeUsuarioFormulario ( $row->formId )))
				$mostrar = true;

			if ($row->tipo == 'evaluacion')
				$mostrar = false;

			$diploma = '';
			if ($row->diploma != '')
				$diploma = base_url () . 'archivos/diplomas/' . $row->diploma;

			$result [] = array (
					'id' => $row->respId,
					'nombre' => $row->titulo,
					'fecha_respuesta' => $row->fecha_respuesta,
					'diploma' => $diploma,
					'mostrar' => $mostrar
			);
		}

		return $result;
	}


	function esPago($id = '', $respuestaEsFormulario = false, $id_usuario = 0, $cuota = 1) {

		if ($cuota == 1){
			$this->db->select ('pago1');
		}
		elseif ($cuota == 2){
			$this->db->select ('pago2');
		}
		elseif ($cuota == 3){
			$this->db->select ('pago3');
		}

		
		if ($respuestaEsFormulario) {
			$q = $this->db->get_where ( 'usuario_responde_formulario', array (
					'id_formulario' => $id,
					'id_usuario' => $id_usuario
			), 1, 0 );
		} else {
			$q = $this->db->get_where ( 'usuario_responde_formulario', array (
					'id' => $id
			), 1, 0 );
		}
		if ($q->num_rows () == 1) {
			$row = $q->row ();

			if ($cuota == 1){
				if ( ($row->pago1 != null) && ($row->pago1 != '') )
					return $row->pago1;
				else
					return false;
			}
			elseif ($cuota == 2){
				if ( ($row->pago2 != null) && ($row->pago2 != '') )
					return $row->pago2;
				else
					return false;
			}
			elseif ($cuota == 3){
				if ( ($row->pago3 != null) && ($row->pago3 != '') )
					return $row->pago3;
				else
					return false;
			}

		}
	}


	function getCuotas($id = '') {


		$this->db->select ('cuotas ');
		
		$q = $this->db->get_where ( 'usuario_responde_formulario', array ('id' => $id), 1, 0 );
	
		if ($q->num_rows () == 1) {
			$row = $q->row ();

			if (($row->cuotas != null) && ($row->cuotas != '') )
				return $row->cuotas;
			else
				return false;
		}
	}


	function esSeleccionado($id = '', $respuestaEsFormulario = false, $id_usuario = 0) {

		$this->db->select('seleccionado');
		if ($respuestaEsFormulario) {
			$q = $this->db->get_where ( 'usuario_responde_formulario', array (
					'id_formulario' => $id,
					'id_usuario' => $id_usuario
			), 1, 0 );
		} else {
			$q = $this->db->get_where ( 'usuario_responde_formulario', array (
					'id' => $id
			), 1, 0 );
		}
		if ($q->num_rows () == 1) {
			$row = $q->row ();

			if ($row->seleccionado != '')
				return $row->seleccionado;
			else
				return false;
		}
	}

	function habilitar($id = '') {

		$this->db->select ( 'habilitado, id_formulario' );
		$q = $this->db->get_where ( 'usuario_responde_formulario', array (
				'id' => $id
		), 1, 0 );

		if ($q->num_rows () == 1) {
			$row = $q->row ();
			$id_formulario = $row->id_formulario;

			if ($row->habilitado == 1) {

				if ($this->config->item('sorteoplaza_habilitado') == false) {
					$this->db->set ( 'seleccionado', 0 );
				}

				$this->db->set ( 'habilitado', 0 );
				$this->db->where ( 'id', $id );
				$this->db->update ( 'usuario_responde_formulario' );
			} else {

				if ($this->config->item('sorteoplaza_habilitado') == false) {
					$this->db->set ( 'seleccionado', 1 );
				}

				$this->db->set ( 'habilitado', 1 );
				$this->db->where ( 'id', $id );
				$this->db->update ( 'usuario_responde_formulario' );

				// SI NO TENEMOS SORTEOS DE PLAZA ENVIAMOS EL MAIL DE CONFIRMACIÓN CUANDO LO HABILITAMOS
				if ($this->config->item('sorteoplaza_habilitado') == false) {

					$formulario = $this->Admin_formularios_model->obtenerInfoFormularios($id_formulario);
					$mensajes = $this->Admin_formularios_model->obtenerMensajeSorteoPlaza($formulario);

					$tituloForm = $formulario ['titulo'];

					$usuario = $this->obtenerUsuarioRegistro ( $id );

					if (!empty($this->Admin_formularios_model->obtenerEvaluacionVinculada($id_formulario))) {
						$this->Admin_formularios_model->reenviarEvaluacion($id);
					}

					$this->Cron_email_model->agregarAcola ( $id_formulario, $usuario ['id'], $usuario ['email'], 'Has sido seleccionado dentro del cupo de ' . $tituloForm, $mensajes['mail'], true );
				}

			}
		}
	}

	function agregarnota($id = '', $color = '#000', $texto = '') {
		$data_principal = array (
				'notacolor' => $color,
				'notatexto' => $texto
		)
		;

		$this->db->where ( 'id', $id )->update ( 'usuario_responde_formulario', $data_principal );
	}


	function pagar($id = '', $numero = '', $tipo = 0, $cuota = 1) {

		if ($tipo == 0) {
			if ($cuota == 1){
				$this->db->set ( 'pago1', '' );
			}
			elseif ($cuota == 2){
				$this->db->set ( 'pago2', '' );
			}
			elseif ($cuota == 3){
				$this->db->set ( 'pago3', '' );
			}
			$this->db->where ( 'id', $id );
			$this->db->update ( 'usuario_responde_formulario' );
		} else {
			
			if ($cuota == 1){
				$this->db->set ( 'pago1', $numero );
			}
			elseif ($cuota == 2){
				$this->db->set ( 'pago2', $numero );
			}
			elseif ($cuota == 3){
				$this->db->set ( 'pago3', $numero );
			}
			$this->db->where ( 'id', $id );
			$this->db->update ( 'usuario_responde_formulario' );
			
		}
	}


	function agregarDiploma($id = 0, $diploma = '') {
		$this->db->set ( 'diploma', $diploma );
		$this->db->where ( 'id', $id );
		$this->db->update ( 'usuario_responde_formulario' );
	}


	function dentroDePlazo($idInscripcion) {
		$this->db->select ( 'usuario_responde_formulario.id as respId,formularios.id as formId, formularios.titulo, fecha_respuesta, fecha_inicio, fecha_fin' );
		$this->db->from ( 'usuario_responde_formulario,formularios' );
		$this->db->where ( 'usuario_responde_formulario.id', $idInscripcion );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', FALSE );
		$q = $this->db->get ();

		$result = array ();
		$mostrar = false;
		if ($q->num_rows () == 1) {
			$row = $q->row ();

			$hoy = new DateTime ( date ( 'Y-m-d H:m:s' ) );
			$fecha_inicio = new DateTime ( $row->fecha_inicio );
			$fecha_fin = new DateTime ( $row->fecha_fin );

			/* Si estamos dentro de los plazos, podemos eliminar la inscripción */

			if ((($hoy <= $fecha_fin) and ($hoy >= $fecha_inicio)) or ($this->Admin_formularios_model->esDeUsuarioFormulario ( $row->formId )))
				$mostrar = true;
		}

		return $mostrar;
	}
	function perteneceInscripcion($idInscripcion = 0) {
		$q = $this->db->get_where ( 'usuario_responde_formulario', array (
				'id' => $idInscripcion,
				'id_usuario' => $this->auth->id_usuario ()
		), 1, 0 );
		return ($q->num_rows () == 1);
	}
	function borrarMiInscripcion($idInscripcion = 0) {
		$this->db->delete ( 'usuario_responde_formulario', array (
				'id' => $idInscripcion
		) );
		$this->db->delete ( 'usuarios_respuestas_formularios', array (
				'id_respuesta' => $idInscripcion
		) );
	}
	function buscarPersonas($term) {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'usuarios' );
		$this->db->or_like ( 'nombre', $term, false );
		$this->db->or_like ( 'email', $term, false );
		$this->db->or_like ( 'apellidos', $term, false );
		$this->db->or_like ( 'usuario', $term, false );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retorno [] = array (
					'id' => $row->id,
					'usuario' => $row->usuario,
					'email' => $row->email,
					'nombre' => $row->nombre,
					'apellidos' => $row->apellidos
			);
		}

		return $retorno;
	}

	/*
	 * function borrarFormulario($id=0){
	 * $this->db->delete('personas', array('id' => $id));
	 * $this->db->delete('personas_listado', array('id_persona' => $id));
	 * $this->db->delete('crea_persona', array('id_persona' => $id));
	 * $this->db->delete('proyectos_personas', array('id_persona' => $id));
	 * }
	 */
}

?>
