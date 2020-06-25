<script type="text/javascript">
var site_url = 'http://dev.localhost/inscripciones/';
</script>

<link rel="stylesheet"
	href="http://dev.localhost/css/compiled/form-showcase.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet"
	href="http://dev.localhost/css/compiled/form-wizard.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet"
	href="http://dev.localhost/css/compiled/new-user.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link href="http://dev.localhost/css/lib/bootstrap.datepicker.css"
	type="text/css" rel="stylesheet" />


<link rel="stylesheet"
	href="http://dev.localhost/css/bootstrapValidator.min.css" />
<script type="text/javascript"
	src="http://dev.localhost/js/bootstrapValidator.min.js"></script>
<script type="text/javascript"
	src="http://dev.localhost/js/language/es_ES.js"></script>






<!-- main container -->
<div class="content">

	<div id="pad-wrapper" class="form-page new-user ver-registro">
		<div class="row header">
			<h3><?php echo $ret['nombre'];  ?></h3>

			<span style="display: block; width: 100%; font-size: 14px;"><b>Fecha
					de realizado:</b> <?php echo $ret['fecha_respuesta'];  ?></span> <span
				style="display: block; width: 100%; font-size: 14px; margin-bottom: 10px;"><b>Usuario:</b> <?php echo $ret['usuario']['usuario'];  ?></span>
				
				  <?php
						
						$pagocss = 'label-info';
						$pago = 'No pago';
						if ($ret ['pago'] != '') {
							$pagocss = 'label-success';
							$pago = 'Pago: #' . $ret ['pago'];
						}
						
						$habilitadocss = 'label-info';
						$habilitado = 'No habilitado';
						if ($ret ['habilitado'] == 1) {
							$habilitadocss = 'label-success';
							$habilitado = 'Habilitado';
						}
						
						echo '<span style="" class="habilitarBotonSpan label ' . $habilitadocss . '" ><i class="icon-check"></i> ' . $habilitado . '</a></span>
						<span style="" class="pagoBotonSpan label ' . $pagocss . '"><i class="icon-money"></i> ' . $pago . '</span>';
						
						?>
				 	
				 	</div>



		<div class="col-md-9 personal-info">
			<div class="row form-wrapper">


				<div class="column with-sidebar">
					<fieldset>

					<?php
					
					foreach ( $ret ['retorno'] as $id => $campo ) {
						
						if ($campo ['tipo'] == 'separador') {
							
							echo '<div class="field-box" data-tipo="separador">
	                    		<h5 style="font-weight:bold;">' . $campo ['titulo'] . '</h5>
	                    		<hr>
                    		 </div>';
						} else {
							
							if (is_array ( $campo ['respuesta'] )) { // Hay mas de una respuesta, caso combobox, tickbox.
								
								$respuesta = '';
								$i = 1;
								
								foreach ( $campo ['respuesta'] as $res ) {
									
									$respuesta .= $res;
									
									if ($i != count ( $campo ['respuesta'] )) {
										
										$respuesta .= ',';
									}
									
									$i ++;
								}
							} else {
								
								$respuesta = $campo ['respuesta'];
							}
							
							echo '<div class="form-group">
								  <label  for="usuario">' . $campo ['label'] . '</label>
								  <div class="col-md-7">
									<span>' . $respuesta . '</span>
								  </div>
								</div>';
						}
					}
					
					?>
								
							
					</fieldset>

				</div>
			</div>

		</div>
		<!-- side right column -->
		<div class="col-md-3 form-sidebar">
			<h6>Archivos Subidos</h6>
			<p>Los siguientes son los archivos subidos al formulario:</p>
			<ul>
                        

                    <?php
																				
																				foreach ( $ret ['archivos'] as $id => $campo ) {
																					
																					echo ' <li><a href="' . base_url () . 'archivos/' . $campo ['respuesta'] . '"><i class="icon-download-alt"></i>' . $campo ['label'] . '</a></li>';
																				}
																				?>
                    </ul>
		</div>
	</div>
</div>

<script src="http://dev.localhost/js/select2.min.js"></script>
<script src="http://dev.localhost/js/jquery.uniform.min.js"></script>

