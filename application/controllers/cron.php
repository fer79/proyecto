<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Cron extends CI_Controller {
	function __construct() {
		parent::__construct ();

		$this->load->model ( 'Cron_email_model' );
		$this->load->model ( 'Admin_formularios_model' );

		$this->Cron_email_model = new Cron_email_model ();
		$this->Admin_formularios_model = new Admin_formularios_model ();
	}

	function enviarMails() {
		$this->Cron_email_model->check ();
	}

	function proximosPlazos() {
		$this->Admin_formularios_model->enviarEvaluacionesPorComenzar ();
		$this->Admin_formularios_model->enviarEvaluacionesQueComienzanHoy ();
	}
}

?>
