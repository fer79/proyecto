<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class pdf_ci extends CI_Controller {
	private $folder = '';
	public function __construct() {
		parent::__construct ();
		// cargamos la libreria html2pdf
		$this->load->library ( 'html2pdf' );
		// cargamos el modelo pdf_model
		
		$this->folder = $this->config->item ( 'upload_path_diplomas' );
	}
	private function createFolder() {
		if (! is_dir ( $this->folder )) {
			mkdir ( $this->folder, 0777 );
		}
	}
	public function crearPDF($nombredearchivo = '', $data = array(), $view = 'diplomas_fenf') {
		
		// establecemos la carpeta en la que queremos guardar los pdfs,
		// si no existen las creamos y damos permisos
		$this->createFolder ();
		
		// importante el slash del final o no funcionará correctamente
		$this->html2pdf->folder ( $this->folder );
		
		// establecemos el nombre del archivo
		$this->html2pdf->filename ( $nombredearchivo );
		
		// establecemos el tipo de papel
		$this->html2pdf->paper ( $view, 'landscape' );
		
		// hacemos que coja la vista como datos a imprimir
		// importante utf8_decode para mostrar bien las tildes, ñ y demás
		
		$this->html2pdf->html ( mb_convert_encoding ( $this->load->view ( 'pdfs/' . $view, $data, true ), 'HTML-ENTITIES', 'UTF-8' ) );
		
		// si el pdf se guarda correctamente lo mostramos en pantalla
		if ($this->html2pdf->create ( 'save' )) {
			// $this->show($nombredearchivo);
			return $this->folder . $nombredearchivo;
		}
	}
	
	// funcion que ejecuta la descarga del pdf
	public function downloadPdf() {
		// si existe el directorio
		if (is_dir ( $this->folder )) {
			// ruta completa al archivo
			$route = base_url ( "files/pdfs/test.pdf" );
			// nombre del archivo
			$filename = "test.pdf";
			// si existe el archivo empezamos la descarga del pdf
			if (file_exists ( "./files/pdfs/" . $filename )) {
				header ( "Cache-Control: public" );
				header ( "Content-Description: File Transfer" );
				header ( 'Content-disposition: attachment; filename=' . basename ( $route ) );
				header ( "Content-Type: application/pdf" );
				header ( "Content-Transfer-Encoding: binary" );
				header ( 'Content-Length: ' . filesize ( $route ) );
				readfile ( $route );
			}
		}
	}
	
	// esta función muestra el pdf en el navegador siempre que existan
	// tanto la carpeta como el archivo pdf
	public function show($nombredearchivo = '') {
		if (is_dir ( $this->folder )) {
			$route = $this->folder . $nombredearchivo;
			
			if (file_exists ( $this->folder . $nombredearchivo )) {
				header ( 'Content-type: application/pdf' );
				header ( 'Content-disposition: attachment; filename=' . $nombredearchivo );
				readfile ( $route );
			}
		}
	}
}
/* End of file pdf_ci.php */
/* Location: ./application/controllers/pdf_ci.php */