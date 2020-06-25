<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['protocol'] = 'smtp';
$config ['charset'] = 'UTF-8';
$config ['wordwrap'] = TRUE;
$config ['mailtype'] = 'text';

// $config['smtp_port'] = '587';
// $config['smtp_host'] = 'smtp.googlemail.com';
// $config['smtp_user'] = 'sistemadeinscripcionesfarq@gmail.com';
// $config['smtp_pass'] = 'sistemadeinscripcionesfarq2016';
// $config['smtp_crypto'] = 'tls';

/* SERVIDORES FARQ NO ANDAN CON HOTMAIL */
$config ['smtp_host'] = 'mail.farq.edu.uy';
$config ['smtp_user'] = 'sistemainscripciones';
$config ['smtp_pass'] = 'Umbrellacorp11';

// $config['smtp_port'] = 'text';
?>