<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Admin_formularios_model extends CI_Model {

	function __construct() {

		parent::__construct ();
		$this->load->model ( 'Admin_model' );
		$this->load->model ( 'Admin_usuarios_model' );
		$this->load->model ( 'Admin_usuarios_roles_model' );
		$this->load->helper ( 'email' );

		$this->Admin_model = new Admin_model ();
		$this->Admin_usuarios_roles_model = new Admin_usuarios_roles_model ();

		set_time_limit ( 60 );
	}


	function esDeUsuarioFormulario($id = '') {

		$rol = $this->auth->getuserRol ( $this->auth->id_usuario () );

		if ($rol ['id'] == 1) {
			return true;
		} else {
			$this->db->select ();
			$this->db->from ( 'formularios,usuarios_crean_formularios' );
			$this->db->where ( 'usuarios_crean_formularios.id_usuario', $this->auth->id_usuario () );
			$this->db->where ( 'usuarios_crean_formularios.id_formulario', 'formularios.id', FALSE );
			$this->db->where ( 'usuarios_crean_formularios.id_formulario', $id );

			$q = $this->db->get ();

			return ($q->num_rows () == 1);
		}
	}


	function estoyHabilitado($id = '', $vinculo = null, $tipoFormulario = 'inscripcion') {

		if ($tipoFormulario == 'inscripcion') {

			$this->db->select ();
			$this->db->from ( 'usuarios_habilitados_formulario' );
			$this->db->where ( 'usuarios_habilitados_formulario.id_formulario', $id );

			$q = $this->db->get ();

			/* Si efectivamente hay habilitados */
			if ($q->num_rows () > 0) {

				$email = $this->auth->email_usuario ( array (
						'id' => $this->auth->id_usuario ()
				) );

				$this->db->select ();
				$this->db->from ( 'usuarios_habilitados_formulario' );
				$this->db->where ( 'usuarios_habilitados_formulario.id_formulario', $id );
				$this->db->where ( 'usuarios_habilitados_formulario.email', $email );

				$q = $this->db->get ();

				if ($q->num_rows () > 0)
					return true;
				else
					return false;
			} else {

				return true;
			}
		} else {

			/* SI TENEMOS UN VINCULO ENTONCES CHECKEAMOS QUE LA PERSONA HAYA CURSADO LA INSCRIPCION */
			if ($vinculo != null && $vinculo != 0) {

				$checkearpago = false;
				if ($this->config->item('sorteoplaza_habilitado') == true ) {
					$checkearpago = true;
				}

				$cursaron = $this->obtenerPersonasQueCursaron ( $vinculo, $checkearpago );

				if (isset ( $cursaron [$this->auth->id_usuario ()] ))
					return true;
			}

			$this->db->select ();
			$this->db->from ( 'usuarios_habilitados_formulario' );
			$this->db->where ( 'usuarios_habilitados_formulario.id_formulario', $id );

			$q = $this->db->get ();

			/* Si efectivamente hay habilitados */
			if ($q->num_rows () > 0) {

				$email = $this->auth->email_usuario ( array (
						'id' => $this->auth->id_usuario ()
				) );

				$this->db->select ();
				$this->db->from ( 'usuarios_habilitados_formulario' );
				$this->db->where ( 'usuarios_habilitados_formulario.id_formulario', $id );
				$this->db->where ( 'usuarios_habilitados_formulario.email', $email );

				$q = $this->db->get ();

				if ($q->num_rows () > 0)
					return true;
				else
					return false;
			} else {

				return false;
			}
		}
	}


	function dentroDePlazo($idFormulario = '') {

		$this->db->select ( 'fecha_inicio, fecha_fin' );
		$this->db->from ( 'formularios' );
		$this->db->where ( 'formularios.id', $idFormulario );

		$q = $this->db->get ();

		$result = array ();
		$mostrar = false;

		if ($q->num_rows () == 1) {
			$row = $q->row ();

			$hoy = date ( 'Y-m-d H:m:s' );
			$fecha_inicio = $row->fecha_inicio;
			$fecha_fin = $row->fecha_fin;

			/* Si estamos dentro de los plazos, podemos eliminar la inscripción */

			if ((strtotime ( $hoy ) <= strtotime ( $fecha_fin )) and (strtotime ( $hoy ) >= strtotime ( $fecha_inicio )))
				$mostrar = true;
		}

		return $mostrar;
	}


	function crearFormulario($titulo, $fechainicio, $fechafin, $tipoformulario, $cantidad, $campos, $colderecha, $colizquierda, $vincular, $abonar, $lugarabono, $costocurso, $monedacostocurso, $fechaabonofin, $fechaabonoinicio, $emails, $esBorrador, $categoria = 1, $fechacomienzocurso = '', $cargahoraria = '') {

		if (($abonar != 1) and ($abonar != true)) {

			$abonar = 0;
			$lugarabono = '';
			$fechaabonofin = '';
			$fechaabonoinicio = '';
			$costocurso= 0 ;
		} else {

			$abonar = 1;
		}

		$fechainicio = strtotime ( $fechainicio );
		$fechainicio = date ( 'Y-m-d H:i:s', $fechainicio );

		$fechafin = strtotime ( $fechafin );
		$fechafin = date ( 'Y-m-d H:i:s', $fechafin );

		$fechaabonofin = strtotime ( $fechaabonofin );
		$fechaabonofin = date ( 'Y-m-d', $fechaabonofin );

		$fechaabonoinicio = strtotime ( $fechaabonoinicio );
		$fechaabonoinicio = date ( 'Y-m-d', $fechaabonoinicio );

		$fechacomienzocurso = strtotime ( $fechacomienzocurso );
		$fechacomienzocurso = date ( 'Y-m-d', $fechacomienzocurso );

		$data_principal = array (
				'titulo' => $titulo,
				'fecha_inicio' => $fechainicio,
				'fecha_fin' => $fechafin,
				'tipo' => $tipoformulario,
				'cantidad' => $cantidad,
				'formulario' => $campos,
				'colderecha' => $colderecha,
				'colizquierda' => $colizquierda,
				'vincular' => $vincular,
				'abonar' => $abonar,
				'lugarabono' => $lugarabono,
				'costocurso' => $costocurso,
				'monedacostocurso' => $monedacostocurso,
				'fechaabonofin' => $fechaabonofin,
				'fechaabonoinicio' => $fechaabonoinicio,
				'publicado' => $esBorrador,
				'categoria' => $categoria,
				'fechacomienzocurso' => $fechacomienzocurso,
				'cargahoraria' => $cargahoraria
		);

		$this->db->insert ( 'formularios', $data_principal );
		$last_id = $this->db->insert_id ();
		if ($this->db->affected_rows () == 1) {

			$emails = json_decode ( $emails, false );

			foreach ( $emails as $email ) {

				$this->db->insert ( 'usuarios_habilitados_formulario', array (
						'id_formulario' => $last_id,
						'email' => $email
				) );
			}

			$this->db->insert ( 'usuarios_crean_formularios', array (
					'id_usuario' => $this->auth->id_usuario (),
					'id_formulario' => $last_id
			) );

			return true;
		} else {

			return false;
		}
	}

	function modificarFormulario($id, $titulo, $fechainicio, $fechafin, $tipoformulario, $cantidad, $campos, $colderecha, $colizquierda, $vincular, $abonar, $lugarabono, $costocurso, $monedacostocurso, $fechaabonofin, $fechaabonoinicio, $emails, $esBorrador, $categoria = 1, $fechacomienzocurso = '', $cargahoraria = '') {

		if (($abonar != 1) and ($abonar != true)) {

			$abonar = 0;
			$lugarabono = '';
			$fechaabonofin = '';
			$fechaabonoinicio = '';
			$costocurso= 0 ;
		} else {

			$abonar = 1;
		}

		$fechainicio = strtotime ( $fechainicio );
		$fechainicio = date ( 'Y-m-d H:i:s', $fechainicio );

		$fechafin = strtotime ( $fechafin );
		$fechafin = date ( 'Y-m-d H:i:s', $fechafin );

		$fechaabonofin = strtotime ( $fechaabonofin );
		$fechaabonofin = date ( 'Y-m-d', $fechaabonofin );

		$fechaabonoinicio = strtotime ( $fechaabonoinicio );
		$fechaabonoinicio = date ( 'Y-m-d', $fechaabonoinicio );

		$fechacomienzocurso = strtotime ( $fechacomienzocurso );
		$fechacomienzocurso = date ( 'Y-m-d', $fechacomienzocurso );


		$data_principal = array (
				'titulo' => $titulo,
				'fecha_inicio' => $fechainicio,
				'fecha_fin' => $fechafin,
				'tipo' => $tipoformulario,
				'cantidad' => $cantidad,
				'formulario' => $campos,
				'colderecha' => $colderecha,
				'colizquierda' => $colizquierda,
				'vincular' => $vincular,
				'abonar' => $abonar,
				'lugarabono' => $lugarabono,
				'costocurso' => $costocurso,
				'monedacostocurso' => $monedacostocurso,
				'fechaabonofin' => $fechaabonofin,
				'fechaabonoinicio' => $fechaabonoinicio,
				'publicado' => $esBorrador,
				'categoria' => $categoria,
				'fechacomienzocurso' => $fechacomienzocurso,
				'cargahoraria' => $cargahoraria
		);

		$this->db->where ( 'id', $id )->update ( 'formularios', $data_principal );

		$this->db->delete ( 'usuarios_habilitados_formulario', array (
				'id_formulario' => $id
		) );
		$emails = json_decode ( $emails, false );
		foreach ( $emails as $email ) {

			$this->db->insert ( 'usuarios_habilitados_formulario', array (
					'id_formulario' => $id,
					'email' => $email
			) );
		}
	}

	function clonarFormulario($id) {
		$formulario = $this->obtenerInfoFormularios ( $id );

		$this->crearFormulario ( '(Copia) ' . $formulario ['titulo'], $formulario ['fechainicio'], $formulario ['fechafin'], $formulario ['tipo'], $formulario ['cantidad'], json_encode ( $formulario ['formulario'] ), $formulario ['colderecha'], $formulario ['colizquierda'], $formulario ['vincular'], $formulario ['abonar'], $formulario ['lugarabono'], $formulario ['costocurso'], $formulario ['monedacostocurso'], $formulario ['fechaabonofin'], $formulario ['fechaabonoinicio'], json_encode ( array () ), 0 );
	}

	function obtenerFormulario($id = '', $conIdRegistro = false, &$nombre = '', &$idForm = '') {
		$retorno = array ();
		// info principal

		if (! $conIdRegistro) {
			$this->db->select ( 'formulario,titulo' );
			$this->db->from ( 'formularios' );
			$this->db->where ( 'id', $id );
		} else {

			$this->db->select ( 'formulario,titulo,formularios.id as idForm' );
			$this->db->from ( 'formularios,usuario_responde_formulario' );
			$this->db->where ( 'usuario_responde_formulario.id', $id );
			$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', FALSE );
		}

		$q = $this->db->get ();

		if ($q->num_rows () == 1) {

			$row = $q->row ();
			$nombre = $row->titulo;
			if (isset ( $row->idForm ))
				$idForm = $row->idForm;
			$retorno = json_decode ( $row->formulario, TRUE );
		}

		return $retorno;
	}

	function obtenerIdFormularioRegistro($id_registro = '') {
		$retorno = array ();

		$this->db->select ( 'formulario,titulo,formularios.id as idForm' );
		$this->db->from ( 'formularios,usuario_responde_formulario' );
		$this->db->where ( 'usuario_responde_formulario.id', $id_registro );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', FALSE );

		$q = $this->db->get ();

		if ($q->num_rows () == 1) {

			$row = $q->row ();

			$retorno = $row->idForm;
		}

		return $retorno;
	}

	function obtenerCamposFormulario($formulario = array(), $campoid = '') {
		$retorno = false;
		foreach ( $formulario as $campo ) {

			if ($campo ['id'] == $campoid) {

				$retorno = $campo;
				break;
			}
		}

		return $retorno;
	}

	function obtenerCamposMostrar($id = '') {
		$formulario = $this->obtenerFormulario ( $id );
		$retorno = false;

		foreach ( $formulario as $campo ) {

			if ((isset ( $campo ['opciones'] ['mostrarenlistado'] ) and ($campo ['opciones'] ['mostrarenlistado'] == true))) {

				$retorno [] = $campo ['opciones'] ['label'];
			}
		}

		return $retorno;
	}

	function obtenerCamposBeca($idForm = '') {
		$formulario = $this->obtenerFormulario ( $idForm );
		$retorno = array ();

		foreach ( $formulario as $campo ) {

			if ($this->auth->tieneAcceso ( 'registros_sorteo_beca', true )) {
				/* Si el usuario es creador del formulario, puede exportar cualquier cosa, sino solo los campos a mostrar en listado */

				if ($campo ['tipo'] == 'carga') {
				} elseif ($campo ['tipo'] == 'grupo') {
				} elseif ($campo ['tipo'] == 'separador') {
				} elseif ($campo ['tipo'] == 'descarga') {
				} elseif ($campo ['tipo'] == 'textarea') {
				} elseif ($campo ['tipo'] == 'textbox') {
				} elseif ($campo ['tipo'] == 'date') {
				} else {

					if ((isset ( $campo ['opciones'] ['computabeca'] )) and (! empty ( $campo ['opciones'] ['computabeca'] ))) {

						foreach ( $campo ['opciones'] ['computabeca'] as $campobeca ) {

							if ($campobeca != 'NULL') {

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

								$realizado = false;
								if (in_array ( $campobeca, $becasform ))
									$realizado = true;

								$retorno [$campobeca] ['realizado'] = $realizado;
								$retorno [$campobeca] ['campos'] [] = array (
										'id' => $campo ['id'],
										'label' => $campo ['opciones'] ['label']
								); // puede haber una opción en más de un campo que habilite para la beca, solo asumimos que si los textos son iguales apuntan a la misma beca
							}
						}
					}
				}
			}
		}

		return $retorno;
	}

	function obtenerCamposExportar($id = '') {
		$formulario = $this->obtenerFormulario ( $id );
		$retorno = false;

		foreach ( $formulario as $campo ) {

			if ($this->auth->tieneAcceso ( 'registros_exportar', true )) {
				/* Si el usuario es creador del formulario, puede exportar cualquier cosa, sino solo los campos a mostrar en listado */
				if ($this->esDeUsuarioFormulario ( $id )) {

					if ($campo ['tipo'] == 'carga') {
					} elseif ($campo ['tipo'] == 'grupo') {
					} elseif ($campo ['tipo'] == 'separador') {
					} elseif ($campo ['tipo'] == 'descarga') {
					} else {

						$retorno [] = array (
								'id' => $campo ['id'],
								'label' => $campo ['opciones'] ['label']
						);
					}
				} else {
					if ((isset ( $campo ['opciones'] ['mostrarenlistado'] ) and ($campo ['opciones'] ['mostrarenlistado'] == true))) {

						$retorno [] = array (
								'id' => $campo ['id'],
								'label' => $campo ['opciones'] ['label']
						);
					}
				}
			}
		}

		return $retorno;
	}

	function obtenerRegistros($id = '') {
		$formulario = $this->obtenerFormulario ( $id );

		$retorno = array ();
		$this->db->select ( 'fecha_respuesta,usuario_responde_formulario.id as respuesta_id,id_campo,respuesta' );
		$this->db->from ( 'usuario_responde_formulario,usuarios_respuestas_formularios' );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', $id );
		$this->db->where ( 'usuarios_respuestas_formularios.id_respuesta', 'usuario_responde_formulario.id', false );
		$this->db->order_by ( 'fecha_respuesta', 'desc' );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {
			$retorno [$row->respuesta_id] ['fecha_respuesta'] = $row->fecha_respuesta;

			if (($campo = $this->obtenerCamposFormulario ( $formulario, $row->id_campo )) and (isset ( $campo ['opciones'] ['mostrarenlistado'] ) and ($campo ['opciones'] ['mostrarenlistado'] == true))) {

				$retorno [$row->respuesta_id] ['campos'] [$campo ['id']] = $row->respuesta;
			}
		}

		return $retorno;
	}

	function obtenerVinculables() {
		if (! $this->auth->tieneAcceso ( 'formularios_ajenos', true )) {
			$companeros = $this->auth->tengoPermisoDeCompanero ( 'formularios_ver_listado' );
			$companeros [] = $this->auth->id_usuario ();
			$this->db->where_in ( 'usuarios_crean_formularios.id_usuario', $companeros ); // O mis formularios
		}

		$this->db->select ( 'formularios.id,titulo,cantidad,fecha_inicio,fecha_fin,tipo,vincular,count(urf.id) as cantInscriptos,usuarios.id as usuid,usuario,nombre,apellidos' );
		$this->db->from ( 'formularios,usuarios_crean_formularios,usuarios' );
		$this->db->where ( 'usuarios.id', 'usuarios_crean_formularios.id_usuario', false );
		$this->db->where ( 'formularios.id', 'usuarios_crean_formularios.id_formulario', false );

		// if ($search != ''){
		// $trozos=preg_split('/\s+/', $search);
		// $numero=count($trozos);
		// if ($numero==1) {
		// $this->db->or_like('titulo',$search,false);
		// } elseif ($numero>1) {
		// $this->db->where('MATCH (titulo) AGAINST ("'.$search.'" IN BOOLEAN MODE) as relevance',NULL,false);
		// $this->db->order_by('relevance','desc');
		// }
		// }

		$retorno = array ();

		$this->db->join ( 'usuario_responde_formulario as urf', 'formularios.id = urf.id_formulario', 'left' );
		$this->db->group_by ( 'formularios.id' );
		$this->db->where ( 'formularios.eliminado', 0 );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retorno [] = array (
					'id' => $row->id,
					'url' => base_url () . $row->tipo . '/' . $row->id . '/' . $this->Admin_model->limpiarURL ( $row->titulo ),
					'titulo' => $row->titulo,
					'fechainicio' => $row->fecha_inicio,
					'fechafin' => $row->fecha_fin,
					'tipo' => $row->tipo,
					'cantidad' => $row->cantidad,
					'cantInscriptos' => $row->cantInscriptos,
					'usuario' => $row->usuario,
					'nombre' => $row->nombre,
					'apellidos' => $row->apellidos
			);
		}

		return $retorno;
	}

	/*
	function obtenerEstadoEmails($formId = '') {

		$retorno = array ();

		$this->db->select ( 'id, para, titulo, mensaje, intentos, fecha AS fecha_creado, enviado, fecha_enviado, eliminado' );
		$this->db->from ( 'cron_emails AS c' );
		$this->db->where ( 'c.id_formulario', $formId);

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retorno [] = array (
					'id' => $row->id,
					//'url' => base_url () . $row->tipo . '/' . $row->id . '/' . $this->Admin_model->limpiarURL ( $row->titulo ),
					'para' => $row->para,
					'titulo' => $row->titulo,
					'mensaje' => $row->mensaje,
					'intentos' => $row->intentos,
					'fecha_creado' => $row->fecha_creado,
					'enviado' => $row->enviado,
					'fecha_enviado' => $row->fecha_enviado,
					'eliminado' => $row->eliminado
			);
		}


		return $retorno;
	}
	*/



	function permisoVerFormulario($id = '') {

		$rol = $this->auth->getuserRol ( $this->auth->id_usuario () );

		//Si no soy de comision evaluadora inicialmente podría ver los formularios
		if ($rol ['id'] != 10) {
			return true;
		} else {
			//Soy un usuario de una comisión y solo puedo ver los formularios que tengo asignados.
			$this->db->select ();
			$this->db->from ( 'usuarios_permisos_formularios' );
			$this->db->where ( 'usuarios_permisos_formularios.id_usuario', $this->auth->id_usuario () );
			$this->db->where ( 'usuarios_permisos_formularios.id_formulario', $id );

			$q = $this->db->get ();

			return ($q->num_rows () == 1);
		}
	}


	function listarFormularios($start = 1, $end, &$cantidad, $tipo = 'inscripcion', $search = '') {

		if ($tipo == 'evaluacion')
			$tipo = 'evaluacion';
		elseif ($tipo == 'inscripcion')
			$tipo = 'inscripcion';
		elseif ($tipo == 'eliminado')
			$tipo = 'eliminado';
		else
			$tipo = 'inscripcion';

		$retorno = array ();

		if (! $this->auth->tieneAcceso ( 'formularios_ajenos', true )) {
			$companeros = $this->auth->tengoPermisoDeCompanero ( 'formularios_ver_listado' );
			$companeros [] = $this->auth->id_usuario ();
			$this->db->where_in ( 'usuarios_crean_formularios.id_usuario', $companeros ); // O mis formularios
		}

		$this->db->select ( 'f.id, f.titulo, f.cantidad, f.fecha_inicio, f.publicado, f.fecha_fin, f.tipo, f.vincular, count(distinct(urf.id)) as cantInscriptos, usuarios.id as usuid,usuario,nombre,apellidos, count(ce.id) AS cantEmails' );
		$this->db->from ( 'formularios AS f,usuarios_crean_formularios, usuarios' );
		$this->db->where ( 'usuarios.id', 'usuarios_crean_formularios.id_usuario', false );
		$this->db->where ( 'f.id', 'usuarios_crean_formularios.id_formulario', false );
		$this->db->group_by ( 'f.id' );

		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			// if ($numero==1) {
			$this->db->or_like ( 'f.titulo', $search, false );
			// } elseif ($numero>1) {
			// $this->db->where('MATCH (titulo) as relevance AGAINST (\''.$search.'\' IN BOOLEAN MODE) ',NULL,false);
			// $this->db->order_by('relevance','desc');
			// }
		}

		$this->db->join ( 'cron_emails as ce', 'f.id = ce.id_formulario', 'left' );
		$this->db->join ( 'usuario_responde_formulario as urf', 'f.id = urf.id_formulario', 'left' );
		$this->db->group_by ( 'f.id' );
		if ($tipo == 'eliminado') {
			$this->db->where ( 'f.eliminado', 1 );
		} else {
			$this->db->where ( 'f.eliminado', 0 );
			$this->db->where ( 'f.tipo', $tipo );
		}

		$this->db->order_by ( 'id', 'desc' );
		$this->db->limit ( $end, $start );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retornoEva = array ();
			if (($tipo == 'inscripcion') or ($tipo == 'eliminado')) {

				if (! $this->auth->tieneAcceso ( 'formularios_ajenos', true )) {
					$this->db->where_in ( 'usuarios_crean_formularios.id_usuario', $companeros ); // O mis formularios
				}

				$this->db->select ( 'f.id, f.titulo, f.cantidad, f.fecha_inicio, f.publicado, f.fecha_fin, f.tipo, f.vincular, count(distinct(urf.id)) as cantInscriptos, usuarios.id as usuid,usuario,nombre,apellidos , count(ce.id) AS cantEmails' );
				$this->db->from ( 'formularios AS f,usuarios_crean_formularios,usuarios' );
				$this->db->where ( 'usuarios.id', 'usuarios_crean_formularios.id_usuario', false );
				$this->db->where ( 'f.id', 'usuarios_crean_formularios.id_formulario', false );

				$this->db->join ( 'cron_emails as ce', 'f.id = ce.id_formulario', 'left' );
				$this->db->join ( 'usuario_responde_formulario as urf', 'f.id = urf.id_formulario', 'left' );
				$this->db->where ( 'f.tipo', 'evaluacion' );
				$this->db->where ( 'f.eliminado', 0 );
				$this->db->where ( 'f.vincular', $row->id );
				$this->db->group_by ( 'f.id' );

				$q2 = $this->db->get ();

				foreach ( $q2->result () as $row2 ) {

					if ($row2->publicado) {

						if (date ( 'Y-m-d H:m:s', strtotime ( $row2->fecha_inicio ) ) <= date ( 'Y-m-d H:m:s', time () ) && date ( 'Y-m-d H:m:s', strtotime ( $row2->fecha_fin ) ) > date ( 'Y-m-d H:m:s', time () )) {
							$estadoAux2 = '<span class="label label-danger">En ejecución</span>';
						} else {

							if (date ( 'Y-m-d H:m:s', strtotime ( $row2->fecha_fin ) ) <= date ( 'Y-m-d H:m:s', time () ))
								$estadoAux2 = '<span class="label label-warning">Finalizado</span>';
							else
								$estadoAux2 = '<span class="label label-success">Publicado</span>';
						}
					} else {

						$estadoAux2 = '<span class="label label-info">Borrador</span>';
					}

					/* CHECKEAMOS PERMISOS */
					$permmodificar2 = false;
					if ($this->auth->tengoPermisoDeCompanero ( 'formularios_modificar', $row2->usuid ) or ($row2->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
						$permmodificar2 = true;

						/* CHECKEAMOS PERMISOS */
					$permeliminar2 = false;
					if ($this->auth->tengoPermisoDeCompanero ( 'formularios_eliminar', $row2->usuid ) or ($row2->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
						$permeliminar2 = true;

						/* CHECKEAMOS PERMISOS */
					$permverregistro2 = false;
					if ($this->auth->tengoPermisoDeCompanero ( 'registros_ver_listado', $row2->usuid ) or ($row2->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
						$permverregistro2 = true;

					/* CHECKEAMOS PERMISOS */
					$permvercompleto2 = false;
					if ($this->auth->tengoPermisoDeCompanero ( 'registros_ver_completo', $row2->usuid ) and ($row2->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
						$permvercompleto2 = true;

					if ($this->permisoVerFormulario($row2->id)){

						$retornoEva [] = array (
								'permmodificar' => $permmodificar2,
								'estado' => $estadoAux2,
								'permeliminar' => $permeliminar2,
								'permverregistro' => $permverregistro2,
								'permvercompleto' => $permvercompleto2,
								'id' => $row2->id,
								'url' => base_url () . $row2->tipo . '/' . $row2->id . '/' . $this->Admin_model->limpiarURL ( $row2->titulo ),
								'titulo' => $row2->titulo,
								'fechainicio' => $row2->fecha_inicio,
								'fechafin' => $row2->fecha_fin,
								'tipo' => $row2->tipo,
								'cantidad' => $row2->cantidad,
								'vincular' => $row2->vincular,
								'cantInscriptos' => $row2->cantInscriptos,
								'usuario' => $row2->usuario,
								'nombre' => $row2->nombre,
								'apellidos' => $row2->apellidos,
								'cantEmails' => $row2->cantEmails
						);
					}
				}
			}

			if ($row->publicado) {

				if (date ( 'Y-m-d H:m:s', strtotime ( $row->fecha_inicio ) ) <= date ( 'Y-m-d H:m:s', time () ) && date ( 'Y-m-d H:m:s', strtotime ( $row->fecha_fin ) ) > date ( 'Y-m-d H:m:s', time () )) {
					$estadoAux = '<span class="label label-danger">En ejecución</span>';
				} else {

					if (date ( 'Y-m-d H:m:s', strtotime ( $row->fecha_fin ) ) <= date ( 'Y-m-d H:m:s', time () ))
						$estadoAux = '<span class="label label-warning">Finalizado</span>';
					else
						$estadoAux = '<span class="label label-success">Publicado</span>';
				}
			} else {

				$estadoAux = '<span class="label label-info">Borrador</span>';
			}

			/* CHECKEAMOS PERMISOS */
			$permmodificar = false;
			if ($this->auth->tengoPermisoDeCompanero ( 'formularios_modificar', $row->usuid ) or ($row->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
				$permmodificar = true;

				/* CHECKEAMOS PERMISOS */
			$permeliminar = false;
			if ($this->auth->tengoPermisoDeCompanero ( 'formularios_eliminar', $row->usuid ) or ($row->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
				$permeliminar = true;

				/* CHECKEAMOS PERMISOS */
			$permverregistro = false;
			if ($this->auth->tengoPermisoDeCompanero ( 'registros_ver_listado', $row->usuid ) or ($row->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
				$permverregistro = true;

			/* CHECKEAMOS PERMISOS */
			$permvercompleto = false;
			if ($this->auth->tengoPermisoDeCompanero ( 'registros_ver_completo', $row->usuid  ) or ($row->usuid == $this->auth->id_usuario () and $this->auth->tieneAcceso ( 'formularios_modificar', true )))
				$permvercompleto = true;


			if ($this->permisoVerFormulario($row->id)){
				$retorno [] = array (
						'permmodificar' => $permmodificar,
						'estado' => $estadoAux,
						'permeliminar' => $permeliminar,
						'permverregistro' => $permverregistro,
						'permvercompleto' => $permvercompleto,
						'id' => $row->id,
						'url' => base_url () . $row->tipo . '/' . $row->id . '/' . $this->Admin_model->limpiarURL ( $row->titulo ),
						'titulo' => $row->titulo,
						'fechainicio' => $row->fecha_inicio,
						'fechafin' => $row->fecha_fin,
						'tipo' => $row->tipo,
						'cantidad' => $row->cantidad,
						'evaluaciones' => $retornoEva,
						'cantInscriptos' => $row->cantInscriptos,
						'usuario' => $row->usuario,
						'nombre' => $row->nombre,
						'apellidos' => $row->apellidos,
						'cantEmails' => $row->cantEmails
				);
			}
		}
		$cantidad = $this->db->count_all_results ();
		return $retorno;
	}

	/* OBTENEMOS LA CANTIDAD DE FORMULARIOS A PAGINAR */
	function obtenerCantidadFormularios($tipo = 'inscripcion', $search = '') {

		$rol = $this->auth->getuserRol ( $this->auth->id_usuario () );

		if ($tipo == 'evaluacion')
			$tipo = 'evaluacion';
		elseif ($tipo == 'inscripcion')
			$tipo = 'inscripcion';
		elseif ($tipo == 'eliminado')
			$tipo = 'eliminado';
		else
			$tipo = 'inscripcion';

		if (! $this->auth->tieneAcceso ( 'formularios_ajenos', true )) {
			$companeros = $this->auth->tengoPermisoDeCompanero ( 'formularios_ver_listado' );
			$companeros [] = $this->auth->id_usuario ();
			$this->db->where_in ( 'usuarios_crean_formularios.id_usuario', $companeros ); // O mis formularios
		}

		$this->db->select ( 'formularios.id,titulo,cantidad,fecha_inicio,fecha_fin,tipo,vincular,count(urf.id) as cantInscriptos,usuario,nombre,apellido' );
		//$this->db->from ( 'formularios,usuarios_crean_formularios,usuarios');

		
		
		if (($rol['id'] != 10)){
			$this->db->from ( 'formularios,usuarios_crean_formularios,usuarios');
		
			//Soy un usuario de una comisión y solo puedo ver los formularios que tengo asignados.
			//$this->db->join ( 'usuarios_permisos_formularios', 'usuarios_permisos_formularios.id_formulario = formularios.id', 'left' );
		}
		else{
			$this->db->from ( 'formularios,usuarios_crean_formularios,usuarios, usuarios_permisos_formularios' );

		}

		$this->db->where ( 'usuarios.id', 'usuarios_crean_formularios.id_usuario', false );
		$this->db->where ( 'formularios.id', 'usuarios_crean_formularios.id_formulario', false );


		
		if (($rol ['id'] == 10)) {
			$this->db->where ( 'usuarios_permisos_formularios.id_usuario', $this->auth->id_usuario (), false );
			$this->db->where ( 'usuarios_permisos_formularios.id_formulario', 'formularios.id', false );
		}

		if ($search != '') {
			$trozos = preg_split ( '/\s+/', $search );
			$numero = count ( $trozos );
			if ($numero == 1) {
				$this->db->or_like ( 'formularios.titulo', $search, false );
			} elseif ($numero > 1) {
				$this->db->where ( 'MATCH (formularios.titulo) AGAINST ("' . trim ( $search ) . '" IN BOOLEAN MODE)', NULL, false );
			}
		}

		if ($tipo == 'eliminado') {
			$this->db->where ( 'formularios.eliminado', 1 );
		} else {
			$this->db->where ( 'formularios.eliminado', 0 );
			$this->db->where ( 'formularios.tipo', $tipo );
		}

		return $this->db->count_all_results ();
	}

	/*
	 *
	 * -- --------------------------------------------------------
	 *
	 * --
	 * -- Estructura de tabla para la tabla `usuarios_tardia_formulario`
	 * --
	 *
	 * CREATE TABLE IF NOT EXISTS `usuarios_tardia_formulario` (
	 * `id` int(11) NOT NULL,
	 * `id_formulario` int(11) NOT NULL,
	 * `id_usuario` int(11) DEFAULT NULL,
	 * `email` varchar(255) DEFAULT NULL,
	 * `fecha_habilitado` datetime NOT NULL
	 * ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	 *
	 * --
	 * -- Índices para tablas volcadas
	 * --
	 *
	 * --
	 * -- Indices de la tabla `usuarios_tardia_formulario`
	 * --
	 * ALTER TABLE `usuarios_tardia_formulario`
	 * ADD PRIMARY KEY (`id`);
	 *
	 * --
	 * -- AUTO_INCREMENT de las tablas volcadas
	 * --
	 *
	 * --
	 * -- AUTO_INCREMENT de la tabla `usuarios_tardia_formulario`
	 * --
	 * ALTER TABLE `usuarios_tardia_formulario`
	 * MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
	 *
	 *
	 *
	 */
	function obtenerInscripcionesTardias($id_formulario = 0) {
		$this->db->select ( 'usuarios.usuario,usuarios.nombre,usuarios.apellidos,usuarios.email,usuarios_tardia_formulario.email emailtardia' );
		$this->db->from ( 'usuarios_tardia_formulario' );
		$this->db->join ( 'usuarios', 'usuarios_tardia_formulario.id_usuario = usuarios.id', 'left' );
		$this->db->where ( 'id_formulario', $id_formulario );

		$q = $this->db->get ();

		$return = array ();
		foreach ( $q->result () as $row ) {

			if ($row->email == null)
				$row->email = $row->emailtardia;

			$return [] = $row;
		}

		return $return;
	}

	function estaHabilitadoTardio($id_formulario) {
		$user = $this->auth->getuserinfo ();

		$this->db->select ();
		$this->db->from ( 'usuarios_tardia_formulario' );
		$this->db->where ( 'id_formulario', $id_formulario );
		$this->db->where ( 'id_usuario', $user ['id'] );
		$this->db->or_where ( 'email', $user ['email'] );
		$this->db->order_by ( 'fecha_habilitado', 'DESC' );
		$this->db->limit ( 1 );

		$q = $this->db->get ();

		if ($q->num_rows () >= 1) {

			$row = $q->row ();

			$ahora = time ();
			$habilitado = strtotime ( $row->fecha_habilitado );
			$diff = $ahora - $habilitado;

			return ((($diff / 60 / 60)) < 48);
		} else {

			return false;
		}
	}

	function habilitarInscripcionTardia($id_formulario = 0, $usuario = '') {
		$formulario = $this->obtenerInfoFormularios ( $id_formulario );
		$tituloForm = $formulario ['titulo'];

		$urlval = $formulario ['url'];

		if (is_numeric ( $usuario )) { // Lo buscamos por el id

			$email = $this->auth->email_usuario ( array (
					'id' => $usuario
			) );

			$data_principal = array (
					'id_formulario' => $id_formulario,
					'id_usuario' => $usuario,
					'fecha_habilitado' => date ( 'Y-m-d H:i:s' )
			);

			$this->db->insert ( 'usuarios_tardia_formulario', $data_principal );

			if ($this->db->affected_rows () == 1) {

				$last_id = $this->db->insert_id ();

				$message = <<<HTML
Hemos habilitado un período especial para ti. Dispones de 48Hs para realizar la inscripción a "$tituloForm". Una vez excedido ese plazo, no podrás inscribirte. \n

Para acceder al formulario utilice el siguiente link: \n
$urlval \n
\n


HTML;

				if ($formulario ['abonar'] == 1) {

					$periodo = $this->abonoCalcular72HorasPrevias ( $formulario ['fechacomienzocurso'] );
					$perdiodoComienzo = date ( 'd-m-Y', strtotime ( $periodo ['comienzo'] ) );
					;
					$periodoFin = date ( 'd-m-Y', strtotime ( $periodo ['fin'] ) );
					;
					$lugar = $formulario ['lugarabono'];
					$message .= <<<HTML
IMPORTANTE: Deberá abonar entre el $perdiodoComienzo y el $periodoFin en $lugar \n
HTML;
				}

				$this->Cron_email_model->agregarAcola ( $id_formulario, $usuario, $email, 'Habilitación de inscripción tardía "' . $tituloForm . '"', $message, true ); // Lo enviamos ya

				return true;
			} else {

				return false;
			}

			return true;
		} else {

			if (valid_email ( $usuario )) {

				$email = $usuario;

				$data_principal = array (
						'id_formulario' => $id_formulario,
						'email' => $usuario,
						'fecha_habilitado' => date ( 'Y-m-d H:i:s' )
				);

				$this->db->insert ( 'usuarios_tardia_formulario', $data_principal );

				if ($this->db->affected_rows () == 1) {

					$last_id = $this->db->insert_id ();

					$message = <<<HTML
Hemos habilitado un período especial para ti. Dispones de 48Hs para realizar la inscripción a "$tituloForm". Una vez excedido ese plazo, no podrás inscribirte. \n

Si no posee una cuenta en el sistema, deberá crearla utilizando la dirección de E-mail: $email \n

Para acceder al formulario utilice el siguiente link: \n
$urlval \n
\n


HTML;

					if ($formulario ['abonar'] == 1) {

						$periodo = $this->abonoCalcular72HorasPrevias ( $formulario ['fechacomienzocurso'] );
						$perdiodoComienzo = date ( 'd-m-Y', strtotime ( $periodo ['comienzo'] ) );
						;
						$periodoFin = date ( 'd-m-Y', strtotime ( $periodo ['fin'] ) );
						;
						$lugar = $formulario ['lugarabono'];
						$message .= <<<HTML
IMPORTANTE: Deberá abonar entre el $perdiodoComienzo y el $periodoFin en $lugar \n
HTML;
					}

					$this->Cron_email_model->agregarAcola ( $id_formulario, $usuario, $email, 'Habilitación de inscripción tardía "' . $tituloForm . '"', $message, true ); // Lo enviamos ya

					return true;
				} else {

					return false;
				}
			} else {

				return false;
			}
		}
	}

	function obtenerInfoFormularios($id = '') {
		$retorno = array ();
		// info principal
		$this->db->select ();
		$this->db->from ( 'formularios,usuarios_crean_formularios,usuarios' );
		$this->db->where ( 'usuarios.id', 'usuarios_crean_formularios.id_usuario', false );
		$this->db->where ( 'formularios.id', 'usuarios_crean_formularios.id_formulario', false );
		$this->db->where ( 'formularios.id', $id );
		$q = $this->db->get ();

		if ($q->num_rows () > 0) {

			// TRAEMOS LOS MAILS ASIGNADOS
			$emails = array ();
			$this->db->select ();
			$this->db->from ( 'usuarios_habilitados_formulario' );
			$this->db->where ( 'id_formulario', $id );
			$u = $this->db->get ();
			foreach ( $u->result () as $row ) {

				$emails [] = $row->email;
			}

			$creadores = array ();
			$this->db->select ();
			$this->db->from ( 'usuarios_crean_formularios,usuarios' );
			$this->db->where ( 'id_formulario', $id );
			$this->db->where ( 'id_usuario', 'usuarios.id', FALSE );
			$v = $this->db->get ();

			foreach ( $v->result () as $row ) {

				$creadores [] = array (
						'id' => $row->id,
						'usuario' => $row->usuario,
						'email' => $row->email
				);
			}

			$row = $q->row ();

			if ($row->publicado) {

				if (date ( 'Y-m-d H:m:s', strtotime ( $row->fecha_inicio ) ) <= date ( 'Y-m-d H:m:s', time () ) && date ( 'Y-m-d H:m:s', strtotime ( $row->fecha_fin ) ) > date ( 'Y-m-d H:m:s', time () )) {
					$estadoAux = '<span class="label label-danger">En ejecución</span>';
				} else {

					if (date ( 'Y-m-d H:m:s', strtotime ( $row->fecha_fin ) ) <= date ( 'Y-m-d H:m:s', time () ))
						$estadoAux = '<span class="label label-warning">Finalizado</span>';
					else
						$estadoAux = '<span class="label label-success">Publicado</span>';
				}
			} else {

				$estadoAux = '<span class="label label-info">Borrador</span>';
			}

			$retorno = array (
					'id' => $id,
					'estado' => $estadoAux,
					'categoria' => $row->categoria,
					'url' => base_url () . $row->tipo . '/' . $row->id_formulario . '/' . $this->Admin_model->limpiarURL ( $row->titulo ),
					'titulo' => $row->titulo,
					'publicado' => $row->publicado,
					'fechainicio' => $row->fecha_inicio,
					'fechafin' => $row->fecha_fin,
					'colderecha' => $row->colderecha,
					'colizquierda' => $row->colizquierda,
					'tipo' => $row->tipo,
					'cantidad' => $row->cantidad,
					'formulario' => json_decode ( $row->formulario, TRUE ),
					'vincular' => $row->vincular,
					'abonar' => $row->abonar,
					'costocurso' => $row->costocurso,
					'monedacostocurso' => $row->monedacostocurso,
					'fechaabonoinicio' => $row->fechaabonoinicio,
					'fechaabonofin' => $row->fechaabonofin,
					'fechacomienzocurso' => $row->fechacomienzocurso,
					'cargahoraria' => $row->cargahoraria,
					'lugarabono' => $row->lugarabono,
					'f_sorteo_plaza' => $row->f_sorteo_plaza,
					'emails' => $emails,
					'id_usuario' => $row->id_usuario,
					'creadores' => $creadores
			);
		}

		return $retorno;
	}

	function work_days_from_date($days, $forward, $date = NULL) {
		if (! $date) {
			$date = date ( 'Y-m-d' ); // if no date given, use todays date
		}

		while ( $days != 0 ) {
			$forward == 1 ? $day = strtotime ( $date . ' +1 day' ) : $day = strtotime ( $date . ' -1 day' );
			$date = date ( 'Y-m-d', $day );
			if (date ( 'N', strtotime ( $date ) ) <= 5) // if it's a weekday
			{
				$days --;
			}
		}
		return $date;
	}

	/* Calculamos el rango en el cual la persona que se inscribió de forma tardía puede abonar */
	/* Tienen que ser 72 horas previas al quinto día hábil antes del inicio */
	function abonoCalcular72HorasPrevias($fechacomienzocurso = '') {

		/* Obtenemos el 5to día habil previo al inicio y le restamos 3 días hábiles */
		$fechacomienzo = $this->work_days_from_date ( 5 + 3, false, $fechacomienzocurso );
		$fechafin = $this->work_days_from_date ( 5, false, $fechacomienzocurso );

		/* DEVOLVEMOS EL PERÍODO PARA PAGAR */
		return array (
				'comienzo' => $fechacomienzo,
				'fin' => $fechafin
		);
	}

	function borrarFormulario($id = 0) {
		$this->db->where ( 'id', $id )->update ( 'formularios', array (
				'eliminado' => 1
		) );
	}

	function obtenerPersonasQueCursaron($idForm = 0, $checkearpago = true) {
		
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'usuarios,usuario_responde_formulario' );
		$this->db->where ( 'id_formulario', $idForm );
		$this->db->where ( 'id_usuario', 'usuarios.id', FALSE );
		$this->db->where ( 'habilitado', '1' );

		if ($checkearpago)
			$this->db->where ( 'pago1 !=', '' );

		$this->db->where ( 'seleccionado', '1' );

		$q = $this->db->get ();

		foreach ( $q->result () as $row ) {

			$retorno [$row->id_usuario] = array (
					'id' => $row->id_usuario,
					'email' => $row->email
			);
		}

		return $retorno;
	}

	function obtenerMensajeSorteoPlaza($formulario) {

		$tituloForm = $formulario ['titulo'];
		$monto = $formulario ['titulo'];
		$replace = array (

				'{{tituloform}}' => $tituloForm
		);

		$mensajeMail = $this->Admin_model->strReplaceAssoc ( $replace, $this->config->item ( 'mensaje_plaza_sorteado' ) );
		$mensajeNOMail = $this->Admin_model->strReplaceAssoc ( $replace, $this->config->item ( 'mensaje_plaza_NO_sorteado' ) );

		$abonar = $formulario ['abonar'];

		if ($abonar == 1) {

			$fechaabonoinicio = $formulario ['fechaabonoinicio'];
			$fechaabonofin = $formulario ['fechaabonofin'];
			$lugarabono = $formulario ['lugarabono'];
			$monedacostocurso = $formulario ['monedacostocurso'];
			$costocurso = $formulario ['costocurso'];

			/* REMPLAZAMOS TODO SEGUN EL CONFIG PARA GENERAR EL TEXTO */
			$replace = array (

					'{{fechaabonoinicio}}' => $fechaabonoinicio,
					'{{fechaabonofin}}' => $fechaabonofin,
					'{{lugarabono}}' => $lugarabono,
					'{{monedacostocurso}}' => $monedacostocurso,
					'{{costocurso}}' => $costocurso
			);

			$mensajeMail .= $this->Admin_model->strReplaceAssoc ( $replace, $this->config->item ( 'mensaje_plaza_sorteado_abonar' ) );
		}

		return array('mail' => $mensajeMail, 'nomail' => $mensajeNOMail);
	}

	function enviarEvaluacionesPorComenzar() {
		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'formularios' );
		$this->db->where ( 'email_previo_comienzo_enviado', 0 ); // Todavia no enviamos el mail
		$this->db->where ( 'DATEDIFF(NOW(),fecha_inicio) <=', $this->config->item ( 'cron_anticipacion_envio_evaluaciones_aviso' ) );
		$this->db->where ( 'tipo', 'evaluacion' );
		$this->db->where ( 'eliminado', '0' );
		$this->db->where ( 'publicado', '1' );

		$q = $this->db->get ();
		var_dump ( $q->result () );
		foreach ( $q->result () as $row ) {

			$base_url = base_url ();
			$titulo = $row->titulo;
			$fcomienzo = $row->fecha_inicio;
			$ffin = $row->fecha_fin;

			if ($row->vincular != 0) { // Si está vinculada a un formulario de inscripción obtenemos todos los que quedaron en el cupo, están habilitados y pagaron (se supone son los que formularon el curso)

				$message = <<<HTML
Te recordamos que el período de evaluación de "$titulo" comienza el $fcomienzo y termina el $ffin \n
Se te enviará una notificación nuevamente cuando comience el período y el formulario se encuentre habilitado.
HTML;

				$cursaron = $this->obtenerPersonasQueCursaron ( $row->vincular, false );

				foreach ( $cursaron as $persona ) {
					$this->Cron_email_model->agregarAcola ( $row->id, $persona ['id'], $persona ['email'], 'Recordatorio de evaluación "' . $row->titulo . '"', $message );
				}
			}

			// Si no está vinculada a nada, seguro tenemos que tomar las personas asignadas

			$registrarse_url = base_url () . 'registrarse';
			// TRAEMOS LOS MAILS ASIGNADOS
			$this->db->select ();
			$this->db->from ( 'usuarios_habilitados_formulario' );
			$this->db->where ( 'id_formulario', $row->id );
			$u = $this->db->get ();

			foreach ( $u->result () as $habilitado ) {

				$email = $habilitado->email;

				$message = <<<HTML
Te recordamos que el período de evaluación de "$titulo" comienza el $fcomienzo y termina el $ffin \n

Si aún no tienes cuenta en el sistema, puedes crearla en el siguiente link $registrarse_url ,recuerda registrarte utilizando $email cómo email ya que la evaluación estará habilitada solo para la cuenta relacionada a dicha casilla.\n

Se te enviará una notificación nuevamente cuando comience el período y el formulario se encuentre habilitado.
HTML;

				$this->Cron_email_model->agregarAcola ( $row->id, 0, $habilitado->email, 'Recordatorio de evaluación "' . $row->titulo . '"', $message );
			}

			$this->db->where ( 'id', $row->id )->update ( 'formularios', array (
					'email_previo_comienzo_enviado' => 1
			) );
		}
	}

	function obtenerEvaluacionVinculada($id_formulario_inscripcion = 0) {
		$retorno = array ();
		$this->db->select ( 'id' );
		$this->db->from ( 'formularios' );
		$this->db->where ( 'formularios.eliminado', 0 );
		$this->db->where ( 'formularios.vincular', $id_formulario_inscripcion );

		$q2 = $this->db->get ();

		foreach ( $q2->result () as $row2 ) {
			$retorno [] = $this->obtenerInfoFormularios ( $row2->id );
		}

		return $retorno;
	}

	function reenviarEvaluacion($id_inscripcion = 0) {
		$this->db->select ('titulo, email, formularios.id as formid, fecha_fin, fecha_inicio,usuarios.id as userid');
		$this->db->from ( 'formularios, usuario_responde_formulario, usuarios' );
		$this->db->where ( 'usuario_responde_formulario.id_formulario', 'formularios.id', false);
		$this->db->where ( 'usuario_responde_formulario.id_usuario', 'usuarios.id', false);
		$this->db->where ( 'usuario_responde_formulario.id', $id_inscripcion );
		$this->db->where ( 'eliminado', '0' );
		$this->db->where ( 'publicado', '1' );

		$q = $this->db->get ();
		if ($q->num_rows () == 1) {

			$row = $q->row ();
			$base_url = base_url();
			$email = $row->email;
			$titulo = $row->titulo;
			$fcomienzo = $row->fecha_inicio;
			$ffin = $row->fecha_fin;

			$message = <<<HTML
			La evaluación de "$titulo" ha comenzado y finaliza el $ffin \n

Podrás acceder a ella en $base_url una vez te hayas logueado a tu cuenta. \n

Te recordamos que todas las evaluaciones realizadas en este sistema son absolutamente anónimas.
HTML;

			$this->Cron_email_model->agregarAcola ( $row->formid, $row->userid, $row->email, 'Debes evaluar "' . $row->titulo . '"', $message, true );
		}

	}

	function enviarEvaluacionesQueComienzanHoy($id_inscripcion = 0) {
		$retorno = array ();
		$this->db->select ();

		$this->db->from ( 'formularios' );
		$this->db->where ( 'fecha_inicio  <=', date ( 'Y-m-d H:m:s', time () ) );
		$this->db->where ( 'email_comienzo_enviado', 0 );
		$this->db->where ( 'tipo', 'evaluacion' );
		$this->db->where ( 'fecha_fin  >', date ( 'Y-m-d H:m:s', time () ) );
		$this->db->where ( 'eliminado', '0' );
		$this->db->where ( 'publicado', '1' );


		$q = $this->db->get ();
		foreach ( $q->result () as $row ) {

			$base_url = base_url ();
			$titulo = $row->titulo;
			$fcomienzo = $row->fecha_inicio;
			$ffin = $row->fecha_fin;

			if ($row->vincular != 0) { // Si está vinculada a un formulario de inscripción obtenemos todos los que quedaron en el cupo, están habilitados y pagaron (se supone son los que formularon el curso)

				$cursaron = $this->obtenerPersonasQueCursaron ( $row->vincular );

				$message = <<<HTML
La evaluación de "$titulo" ha comenzado y finaliza el $ffin \n

Podrás acceder a ella en $base_url una vez te hayas logueado a tu cuenta. \n

Te recordamos que todas las evaluaciones realizadas en este sistema son absolutamente anónimas.
HTML;

				foreach ( $cursaron as $persona ) {

					$this->Cron_email_model->agregarAcola ( $row->id, $persona ['id'], $persona ['email'], 'Debes evaluar "' . $row->titulo . '"', $message );
				}
			}

			$registrarse_url = base_url () . 'registrarse';

			// TRAEMOS LOS MAILS ASIGNADOS
			$this->db->select ();
			$this->db->from ( 'usuarios_habilitados_formulario' );
			$this->db->where ( 'id_formulario', $row->id );
			$u = $this->db->get ();

			foreach ( $u->result () as $habilitado ) {

				$email = $habilitado->email;

				$message = <<<HTML
La evaluación de " $titulo " ha comenzado y finaliza el $ffin \n

Podrás acceder a ella en $base_url una vez te hayas logueado a tu cuenta.\n

Si aún no tienes cuenta en el sistema, puedes crearla en el siguiente link $registrarse_url ,recuerda registrarte utilizando $email cómo email ya que la evaluación estará habilitada solo para la cuenta relacionada a dicha casilla. \n
HTML;

				$this->Cron_email_model->agregarAcola ( $row->id, 0, $habilitado->email, 'Recordatorio de evaluación "' . $row->titulo . '"', $message );
			}

			$this->db->where ( 'id', $row->id )->update ( 'formularios', array (
					'email_comienzo_enviado' => 1
			) );
		}
	}
}

?>
