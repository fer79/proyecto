<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );


class Cron_email_model extends CI_Model {

	function __construct() {
		parent::__construct ();

		$this->load->model ( 'Admin_formularios_model' );

		$this->Admin_formularios_model = new Admin_formularios_model ();
	}

	function check() {

		$retorno = array ();
		$this->db->select ();
		$this->db->from ( 'cron_emails' );
		$this->db->where ( 'eliminado', 0 );
		$this->db->where ( 'enviado', 0 );
		$this->db->order_by ( 'prioridad desc,id asc' ); // ordenamos por prioridad, y enviamos los más viejos primero (por id asc)

		$q = $this->db->get ();

		$i = 1;
		$max_por_vez = $this->config->item ( 'cron_email_max_to_send' );
		$cron_email_max_reintentos = $this->config->item ( 'cron_email_max_reintentos' );

		foreach ( $q->result () as $row ) {

			if ($row->intentos >= $cron_email_max_reintentos) {

				$this->db->where ( 'id', $row->id )->update ( 'cron_emails', array (
						'eliminado' => 1
				) );

				if ($row->id_formulario != 0) {

					$form = $this->Admin_formularios_model->obtenerInfoFormularios ( $row->id_formulario );
					$base_url = base_url ();
					$titulomail = $row->titulo;
					$para = $row->para;
					$idmail = $row->id;
					$message = <<<HTML
El email ID: $idmail con título: "$titulomail", para la dirección de correo: "$para" no ha podido ser enviado luego de $cron_email_max_reintentos intentos..\n
Es posible que la dirección de correo  no exista.\n
Si usted no sabe como resolver el problema, contacte con el administrador del sistema.
HTML;

					/* A CADA CREADOR DEL FORMULARIO SE LO ENVIAMOS */
					foreach ( $form ['creadores'] as $creador ) {
						//$this->agregarAcola ( 0, 0, $creador ['email'], 'Alerta envío de email fallido', $message, true );
					}
				}
			} else {
				if ($i <= $max_por_vez) {

					if (! $this->enviarEmail ( $row->para, $row->titulo, $row->mensaje, $row->desde_email, $row->desde_nombre )) {

						$this->db->where ( 'id', $row->id );
						$this->db->set ( 'intentos', 'intentos+1', FALSE );
						$this->db->update ( 'cron_emails' );
					} else {

						$this->db->where ( 'id', $row->id )->update ( 'cron_emails', array (
								'enviado' => 1,
								'fecha_enviado' => date ( 'Y-m-d H:i:s' )
						) );
					}
				} else {
					break;
				}

				$i ++;
			}
		}

		return;
	}

	function enviarEmail($para = '', $titulo = '', $mensaje = '', $desde_email = '', $desde_nombre = '', $attachment = '') {

		$this->load->library ( 'email' );

		if (! empty ( $desde_email ) and (! empty ( $desde_nombre )))
			$this->email->from ( $desde_email, $desde_nombre );
		else
			$this->email->from ( $this->config->item ( 'cron_email_from_email' ), $this->config->item ( 'cron_email_from_nombre' ) );

		$this->email->to ( $para );
		$this->email->subject ( $titulo );
		$this->email->message ($mensaje . $this->config->item('cron_email_signature'));




		if ($attachment != '') {

			$this->email->attach ( $attachment, 'attachment' );
		}

		if (! $this->email->send ()) {

			return false;
		} else {

			echo $this->email->print_debugger ();
			return true;
		}
	}

	/* Los mails se agregan a una cola y ella se encarga de enviarlos urgentemente o en la próxima pasada */
	function agregarAcola($id_formulario = 0, $id_usuario = 0, $para = '', $titulo = '', $mensaje = '', $enviarYA = false, $prioridad = 0, $desde_email = '', $desde_nombre = '', $attachment = '') {
		$fueEnviado = true;
		if ($enviarYA == true)
			$fueEnviado = $this->enviarEmail ( $para, $titulo, $mensaje, $desde_email, $desde_nombre, $attachment );

		if (! $fueEnviado) {
			$prioridad = 100;
		}

		$fecha = date ( 'Y-m-d H:i:s' );
		$data_principal = array (
				'id_formulario' => $id_formulario,
				'id_usuario' => $id_usuario,
				'para' => $para,
				'titulo' => $titulo,
				'mensaje' => $mensaje,
				'desde_email' => $desde_email,
				'desde_nombre' => $desde_nombre,
				'prioridad' => $prioridad,
				'fecha' => $fecha
		);

		if ($enviarYA && $fueEnviado) {
			$data_principal ['enviado'] = 1;
			$data_principal ['fecha_enviado'] = $fecha;
		}

		$this->db->insert ( 'cron_emails', $data_principal );

		return $fueEnviado;
	}
}

?>
