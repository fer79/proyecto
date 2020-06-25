<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Admin_model extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function generarHTML($formulario = '') {
		foreach ( $formulario as $campo ) {
			
			if ($campo ['tipo'] == 'textbox') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="textbox" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                             <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                             <div class="col-md-7">
                              <input class="form-control">
                              <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p>
                            </div>                            
                          </div>';
			} elseif ($campo ['tipo'] == 'textarea') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="textarea" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                              <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                              <div class="col-md-7">
                              <textarea class="form-control" rows="4"></textarea>
                            
                              <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p>
                              </div>                            
                              </div>';
			} elseif ($campo ['tipo'] == 'checkboxgroup') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="checkboxgroup" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                              <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                              <div class="col-md-7 ctrl-checkboxgroup">';
				
				$contador = 0;
				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {
					
					echo '<label class="chbox checkbox-inline">
                                  <div class="checker" id="uniform-inlineCheckbox' . ($contador ++) . '"><span><div class="checker" id="uniform-inlineCheckbox' . ($contador) . '"><span><input type="checkbox" id="inlineCheckbox' . $contador . '" value="' . $opcion . '"></span></div></span></div> ' . $opcion . '
                                  </label>';
				}
				
				echo '
                              <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p>    
                              </div>
                              </div>';
			} elseif ($campo ['tipo'] == 'combobox') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="combobox" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                              <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                              
                              <div class="col-md-7">
                              <div class="ui-select">
                              <select>';
				
				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {
					
					echo '<option>' . $opcion . '</option>';
				}
				
				echo '</select>
                                 
                              </div>
                              <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p> 
                              </div>
                              </div>';
			} elseif ($campo ['tipo'] == 'radiogroup') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="radiogroup" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                              <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                              <div class="col-md-7 ctrl-radiogroup">';
				
				$contador = 0;
				foreach ( $campo ['opciones'] ['opciones'] as $opcion ) {
					
					if ($contador == 0) {
						echo '<label class="radio">
                                      <div class="radio" id="uniform-optionsRadios' . ($contador ++) . '"><span class="checked"><div class="radio"><span class="checked"><input type="radio" value="' . $opcion . '" checked=""></span></div></span></div>
                                      ' . $opcion . '
                                      </label>';
					} else {
						
						echo '<label class="radio">
                                      <div class="radio" id="uniform-optionsRadios' . ($contador ++) . '"><span><div class="radio"><span><input type="radio" value=" ' . $opcion . '"></span></div></span></div>
                                      ' . $opcion . '
                                      </label>';
					}
				}
				
				echo '
                              <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p>    
                              </div>
                              </div>';
			} elseif ($campo ['tipo'] == 'descarga') {
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="descarga" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                              <div class="alert alert-info">
                                  <i class="icon-download-alt"></i>
                                 <a class="descarga" href="' . $campo ['opciones'] ['link'] . '">' . $campo ['opciones'] ['texto'] . '</a>
                              </div>
                           </div>';
			} elseif ($campo ['tipo'] == 'carga') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="carga" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                             <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                               <div class="col-md-7">
                                <input class="file" type="file">
                                
                                 <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p>    
                               </div>
                                   
                               </div>';
			} elseif ($campo ['tipo'] == 'separador') {
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="separador" id="' . $campo ['id'] . '"  modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                                  <h4>' . $campo ['opciones'] ['titulo'] . '</h4>
                                  <hr>
                                 </div>';
			} elseif ($campo ['tipo'] == 'grupo') {
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="grupo" id="' . $campo ['id'] . '"  modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                                   <h4 style="color:green;"><i class="icon-sitemap" ></i><span>' . $campo ['opciones'] ['titulo'] . '<span></h4>
                                  <hr>
                                 </div>';
			} elseif ($campo ['tipo'] == 'date') {
				
				$obligatorio = '';
				if ($campo ['opciones'] ['obligatorio'] == true)
					$obligatorio = '<i class="form-control-feedback glyphicon glyphicon-asterisk" data-bv-icon-for="' . $campo ['id'] . '" style=""></i>';
				
				echo '<div class="field-box draggableField ui-draggable ui-draggable-handle droppedField" data-tipo="date" id="' . $campo ['id'] . '" modificado="1" opciones=\'' . json_encode ( $campo ['opciones'] ) . '\' dependencia="' . $campo ['dependencia'] . '" dependencia-valor="' . $campo ['dependenciavalor'] . ' ">
                                      <label>' . $campo ['opciones'] ['label'] . ' ' . $obligatorio . '</label>
                                       <div class="col-md-7">
                                        <input type="text" value="dd/mm/aaaa" class="form-control input-datepicker">
                                       
                                         <p class="help-block">' . $campo ['opciones'] ['ayuda'] . '</p>    
                                       </div>
                                  </div>';
			}
		}
	}
	function strReplaceAssoc(array $replace, $subject) {
		return str_replace ( array_keys ( $replace ), array_values ( $replace ), $subject );
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
	function isJSON($string) {
		return is_string ( $string ) && is_array ( json_decode ( $string, true ) ) ? true : false;
	}
}

?>