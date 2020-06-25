<script type="text/javascript">
var site_url = '<?php echo base_url();?>';


$(document).ready(function(){
	
	
	var idForm ='<?php echo $idFormulario; ?>';

	$('#doExport').click(function(){



		 var form = $('<form></form>').attr('action', site_url+"registros_ajx/exportareva").attr('method', 'post');
			// Add the one key/value
		 form.append($("<input></input>").attr('type', 'hidden').attr('name', 'idForm').attr('value', idForm));
		//send request
		 form.appendTo('body').submit().remove();


	});

	$('#doExportCompleto').click(function(){



		 var form = $('<form></form>').attr('action', site_url+"registros_ajx/exportarevacompleto").attr('method', 'post');
			// Add the one key/value
		 form.append($("<input></input>").attr('type', 'hidden').attr('name', 'idForm').attr('value', idForm));
		//send request
		 form.appendTo('body').submit().remove();


	});


	$('.readmore').readmore({
	  speed: 75,
	  lessLink: '<a href="#">Ver Menos</a>',
	  moreLink: '<a href="#">Ver Más</a>',
	  collapsedHeight:30,
	  blockCSS: 'text-align:right;float:right;font-size: 15px;'
	});


});
</script>



<!-- this page specific styles -->
<link href="<?php echo base_url(); ?>css/lib/morris.css" type="text/css"
	rel="stylesheet" />
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/chart-showcase.css"
	type="text/css" media="screen" />
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/user-list.css"
	type="text/css" media="screen" />

<script
	src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?php echo base_url();?>js/morris.min.js"></script>
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


<div class="modal fade" id="exportarModal" tabindex="-1" role="dialog"
	aria-labelledby="Exportar">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Exportar</h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">

					<div class="settings" style="margin: 5px 0">

						<div class="items">
							<h4 class="title">¿Está seguro que desea exportar los datos?</h4>



						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doExport" class="btn btn-primary">
					<i class="icon-download-alt"></i> Exportar
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="exportarCompletoModal" tabindex="-1" role="dialog"
	aria-labelledby="Exportar">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Exportar datos completos</h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">

					<div class="settings" style="margin: 5px 0">

						<div class="items">
							<h4 class="title">¿Está seguro que desea exportar los datos?</h4>



						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doExportCompleto" class="btn btn-primary">
					<i class="icon-download-alt"></i> Exportar
				</button>
			</div>
		</div>
	</div>
</div>

<div class="content">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3><?php echo $formularios['titulo']; ?></h3>

		</div>

		<!-- Users table -->
		<div class="row" style="min-height: 650px;">
			<div class="col-md-12">
				<div class="filter-block">

					<div class="pull-right">
						<a id="exportarbtn" class="btn btn-default" data-toggle="modal"
							data-target="#exportarModal" style="padding: 3px 6px;"><i
							class="icon-download-alt"></i> Exportar</a>
						<a id="exportarbtn" class="btn btn-default" data-toggle="modal"
							data-target="#exportarCompletoModal" style="padding: 3px 6px;"><i
							class="icon-download-alt"></i> Exportar completo</a>
							

					</div>
					
				</div>

			</div>
			<div class="col-md-12">
               	

                <?php
																$i = 0;
																function imprimirResumen($campos, &$i) {
																	foreach ( $campos as $campo ) {
																		
																		$i ++;
																		
																		if ($campo ['tipo'] == 'grupo') {
																			
																			echo '	<div class="panel-group" id="accordion' . $i . '" role="tablist" >
										  <div class="panel panel-default">
										    <div class="panel-heading" role="tab" id="headingOne' . $i . '">
										      <h4 class="panel-title">
										        <a role="button" class="collapsed in" data-toggle="collapse" data-parent="#accordion' . $i . '" href="#' . $campo ['id'] . '" aria-expanded="false" aria-controls="' . $campo ['id'] . '">
										          ' . $campo ['titulo'] . '
										        </a>
										      </h4>
										    </div>
										    <div id="' . $campo ['id'] . '" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne' . $i . '">
										      <div class="panel-body">';
																			
																			if (isset ( $campo ['hijos'] )) {
																				imprimirResumen ( $campo ['hijos'], $i );
																			}
																			
																			echo '  </div>
										    </div>
										  </div>
										</div>';
																		} elseif ($campo ['tipo'] == 'separador') {
																			
																			echo '<div class="field-box" data-tipo="separador">
		                    		<h5 style="font-weight:bold;">' . $campo ['titulo'] . '</h5>
		                    		<hr>
	                    		 </div>';
																		} elseif ($campo ['tipo'] == 'descarga') {
																		} elseif (($campo ['tipo'] == 'checkboxgroup') or ($campo ['tipo'] == 'radiogroup')) {
																			
																			echo ' <div class="col-md-6 chart">
						                    <h5>' . $campo ['label'] . '</h5>
						                    <div id="' . $campo ['id'] . '" style="height: 250px;"></div>
						                </div>';
																			
																			/*
																			 * echo '<script type="text/javascript">
																			 *
																			 * // Morris Bar Chart
																			 * Morris.Donut({
																			 * element: \''.$campo['id'].'\',
																			 * formatter: function (x) { return x + "%"},
																			 * data: [';
																			 *
																			 * foreach($campo['respuestas'] as $texto => $cant){
																			 * echo '{label: \''.$texto.'\', value: '.$cant.'},';
																			 * }
																			 * echo '],
																			 *
																			 *
																			 * });
																			 *
																			 * </script>';
																			 */
																			
																			echo '<script type="text/javascript">

								        // Morris Bar Chart
								        Morris.Bar({
								            element: \'' . $campo ['id'] . '\',
								            data: [';
																			
																			foreach ( $campo ['respuestas'] as $texto => $cant ) {
																				echo '{texto: \'' . $texto . '\', respuestas: \'' . $cant . '\'},';
																			}
																			echo '],
								            xkey: \'texto\',
								            ykeys: [\'respuestas\'],
								            labels: [\'Respuestas\'],
								            barRatio: 0.4,
								            hideHover:false,
								            xLabelMargin: 10,
								            hideHover: \'auto\',
								            barColors: ["#3d88ba"],
											hoverCallback:function (index, options, content) {
															  var row = options.data[index];
															  var total = 0;
															  $.each(options.data, function(i,val){
																  
																	total += parseInt(val.respuestas);
																
																  
															  });
															  console.log(total);
															  return ((row.respuestas* 100) / total).toFixed(1) + \'%\';
															}
								        });

								</script>';
																		} else {
																			
																			echo ' <div class="row">
	                <div class="col-md-12"> <div class="col-md-12 chart">
						                    <h5>' . $campo ['label'] . " (" . $campo ['total'] . ')</h5>';
																			
																			echo '  <table id="listado" class="table table-hover">
					                        <thead>
					                            <tr>
													 <th class="col-md-1 sortable">
					                                    Respuesta
					                                </th>		                         											
					                            </tr>
					                        </thead>
					                        <tbody>';
																			
																			if (is_array ( $campo ['respuestas'] )) {
																				
																				foreach ( $campo ['respuestas'] as $key => $resp ) {
																					
																					foreach ( $resp ['respuestas'] as $res ) {
																						
																						echo '<tr><td class="readmore">
														' . $res . '
														</td></tr>';
																					}
																				}
																			}
																			echo '          </tbody>
					                    </table>';
																			
																			echo '</div>    </div>                
	            </div>';
																		}
																	}
																}
																
																imprimirResumen ( $resumen, $i );
																
																?>

                   
                </div>
		</div>

		<div id="pagination" class="pagination pull-right"></div>


		<!-- end users table -->
	</div>
</div>

<script src="<?php echo base_url();?>/js/jquery.quicksearch.js"></script>
<script src="<?php echo base_url();?>js/readmore.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.smartpaginator.js"></script>

<style>
h1, h2, h3, h4, h5 {
	font-weight: bold;
}
</style>