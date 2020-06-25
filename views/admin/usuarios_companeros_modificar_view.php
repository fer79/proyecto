<script type="text/javascript">
var site_url = '<?php echo base_url();?>';


function getConfirm(confirmMessage, callback, onlyaccept){
    confirmMessage = confirmMessage || '';

    onlyaccept = typeof onlyaccept !== 'undefined' ? onlyaccept : false;

    $('#confirmFalse').show();
    
    $('#confirmmodal').modal({show:true,
                          	
                            keyboard: false,
    });

    $('#myModalLabel').html(confirmMessage);

  

    	  $('#confirmmodal').on('click', '#confirmTrue',function(){
    	    	$('#confirmmodal').off('click', '#confirmFalse');//UNBIND NECESARIO
    	    	$('#confirmmodal').off('click', '#confirmTrue');//UNBIND NECESARIO 
    	        $('#confirmmodal').modal('hide');
    	        if (callback) callback(true);
    	    });

    if (!onlyaccept){
        
    
	    $('#confirmmodal').on('click', '#confirmFalse',function(){
	    	$('#confirmmodal').off('click', '#confirmFalse');//UNBIND NECESARIO
	    	$('#confirmmodal').off('click', '#confirmTrue');//UNBIND NECESARIO
	    	
	        $('#confirmmodal').modal('hide');
	        if (callback) callback(false);
	
	    });

    }else{

    	$('#confirmFalse').hide();
		
    }
    
  
}  




$(document).ready(function(){

    formmodified=0;
    $('form :input').change(function(){
        formmodified=1;
    });
    window.onbeforeunload = confirmExit;
    function confirmExit() {
        if (formmodified == 1) {
            return "No se han guardado los nuevos datos. Desea salir de todas formas?";
        }
    }

    function listarCompaneros(){
    	
    	
    	xhrlistar = $.ajax({
    		url:	site_url+'admin_usuarios_roles_ajx/traerCompaneros',
    		type: 	'POST',
    		data: {id:<?php echo $id_usuario; ?>},
    		success: function(result){ 
    			if(result=='not_logged'){
    				return false;
    			}else if (result=='error'){
    			
    			}else if (result=='vacio'){
    			
    			}else{
    				var data = result;
    				$('#listado tbody').html('');
    				$.each(data, function(i,val) {

    					$d = $('<tr id="id-'+data[i].id+'">\
    								<td>'+data[i].id+'</td> \
    								<td>\
    									'+data[i].usuario+'\
    								</td>\
    								<td>\
    									'+data[i].email+'\
    								</td>\
    								<td>\
    								'+data[i].nombre+'\
    								</td>\
    								<td>\
    								'+data[i].apellidos+'\
    								</td>\
    								<td class="align-right">\
    									<a href="#" data-idcompanero ="'+data[i].id+'" data-permisos=\''+data[i].permisos+'\'  class="botoneditar" ><i class="icon-pencil"></i></a>\
    									<a href="#" data-id="'+data[i].id+'" class="botonborrar"><i class="icon-trash"></i></a>\
    								</td>\
    							</tr>').fadeIn('slow');
    					
    					$('#listado tbody').append($d);
    					
    					
    				});
    				
    				$('#listado tbody tr:even').addClass("alt-row");

    				
    			}
    		}
    	});
    }	


    $('body').on('click','.permisosexpandidos .permisos',function(){

  		var dependencia = JSON.parse($(this).attr('data-dependencia'));
		var id_companero = $(this).parents('.permisosexpandidos').attr('data-idcompanero'); 
  		if ($(this).is(':checked')) {

  			check_dependencia(dependencia,false);

  	        
  	    } else {

  	    	check_dependencia(dependencia,true);

  	    }

  		var permisos = [];
  		$(this).parents('.permisosexpandidos').find('.permisos:checked').each(function(i){
			 permisos[i] = $(this).val();
	     });
		

  		$.ajax({
    		url:	site_url+'admin_usuarios_roles_ajx/actualizarPermisos',
    		type: 	'POST',
    		data: {'id':<?php echo $id_usuario; ?>,'idcompanero':id_companero,'permisos':JSON.stringify(permisos)},
    		success: function(result){ 


				


    		}
  		});
  		

    });

    $('body').on('click','.botonborrar',function(e){

    	var id_companero = $(this).attr('data-id');
		
		getConfirm('Estás seguro que deseas eliminar al compañero?',function(result){


			if (result == true){

				$.ajax({
		    		url:	site_url+'admin_usuarios_roles_ajx/borrarCompanero',
		    		type: 	'POST',
		    		data: {'id':<?php echo $id_usuario; ?>,'idcompanero':id_companero},
		    		success: function(result){ 

		    			listarCompaneros();

		    		}
		  		});
			}
			

			
		});



		e.preventDefault();
			
    });


    
    $('body').on('click','#agregarCompanerobtn',function(e){


    	
    	
		var id_companero = $('#buscarcompanero').val();


		var existe = $('tr#id-'+id_companero).length;
		
		if ((id_companero != '') && (existe == 0)){

			$.ajax({
	    		url:	site_url+'admin_usuarios_roles_ajx/agregarCompanero',
	    		type: 	'POST',
	    		data: {'id':<?php echo $id_usuario; ?>,'idcompanero':id_companero},
	    		success: function(result){ 
	
	
					
	    			listarCompaneros();
	
	    		}
	  		});

		}
			
    });

    
	$('#buscarcompanero').select2({
		ajax: {
			url:	site_url+'admin_usuarios_roles_ajx/buscarCompanero',
			type: 	'POST',
			delay:250,
			data: function (term) {
	            return {
	                term: term
	            };
	        },
	        results: function (data) {
	            return {
	                results: $.map(data, function (item) {
	                    return {
	                        text: item.nombre +" "+ item.apellidos + " (" +item.usuario+ ")",
	                        id: item.id
	                    }
	                })
	            };
	        }
		}
	});
	
	
    $('body').on('click','.botoneditar',function(e){


    	$('.trseleccionado').removeClass('trseleccionado');
    	$('.permisosexpandidos').remove();

    	var id_companero = $(this).attr('data-idcompanero');
		var permisoslist = '<tr class="trseleccionado permisosexpandidos" data-idcompanero="'+id_companero+'"><td colspan=100%>'+$('.items.template.permisoslist').html()+'</td></tr>';
		
		$(this).parents('tr').addClass('trseleccionado');
        $(this).parents('tr').after(permisoslist);

        $("input:checkbox, input:radio").uniform();
        
		var permisos = JSON.parse($(this).attr('data-permisos'));

		$.each(permisos,function(i,val){

			if ($('.permisosexpandidos .permisos[data-key="'+val+'"]').prop('checked') == false){

				$('.permisosexpandidos .permisos[data-key="'+val+'"]').click();
			}
			
		});

		

		e.preventDefault();

    });

    function check_dependencia(dependencia,uncheck){

		if (typeof dependencia != 'undefined'){
		 
			if (uncheck){

				
				$.each(dependencia,function(i,val){
					
					    var dependencia2 = JSON.parse($('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-dependencia'));
						
					    $('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-cantidaddependen',parseInt($('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-cantidaddependen'))-1);
	           	    	
						if ($('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-cantidaddependen') == 0){
							$('.permisosexpandidos .permisos[value="'+val+'"]').prop('disabled',false).removeClass('otrosdependen');
							
						}

       	 		});


			}else{
				$.each(dependencia,function(i,val){

					if ( !$('.permisosexpandidos .permisos[value="'+val+'"]').is(':checked')){
    					var dependencia2 = JSON.parse($('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-dependencia'));
    					check_dependencia(dependencia2);
    				}
        				
        				
    					$('.permisosexpandidos .permisos[value="'+val+'"]').prop('checked',true).prop('disabled',true).addClass('otrosdependen');
    					$('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-cantidaddependen',parseInt($('.permisosexpandidos .permisos[value="'+val+'"]').attr('data-cantidaddependen'))+1);
    					
        				
					
            	});
			}

			$.uniform.update();	
		}
	}


   	 

    listarCompaneros();
});
    </script>

<link rel="stylesheet"
	href="<?php echo base_url();?>css/compiled/form-showcase.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet"
	href="<?php echo base_url();?>css/compiled/form-wizard.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet"
	href="<?php echo base_url();?>css/compiled/new-user.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link href="<?php echo base_url();?>css/lib/bootstrap.datepicker.css"
	type="text/css" rel="stylesheet" />


<link rel="stylesheet"
	href="<?php echo base_url();?>css/bootstrapValidator.min.css" />
<script type="text/javascript"
	src="<?php echo base_url();?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript"
	src="<?php echo base_url();?>js/language/es_ES.js"></script>

<!-- main container -->

<div class="modal fade" style="z-index: 9999;" id="confirmmodal"
	tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="confirmFalse"
					data-dismiss="modal">Cancelar</button>
				<button type="button" id="confirmTrue" class="btn btn-primary">
					Aceptar</button>
			</div>
		</div>
	</div>
</div>

<div class="content">

	<div id="pad-wrapper" class="form-page new-user">
		<div class="row header">
			<h3>Asignar compañeros a "<?php echo $usuario['nombre']; ?>"</h3>


		</div>
		<div class="col-md-9 personal-info">
			<div class="row form-wrapper">

				<div class="column with-sidebar">

					<div class="col-md-12 col-sm-12 col-xs-12 ">
						<div class="form-group">
							<label for="usuario">Buscar: </label>
							<div class="col-md-7" style="padding: 0;">
								<input type="text" class="col-md-5 search" id="buscarcompanero"
									placeholder="Agregar nuevo compañero">
								<div class="btn-glow" id="agregarCompanerobtn">
									<i class="icon-plus"></i>Agregar
								</div>
							</div>
						</div>
						<style>
.select2-container {
	padding: 0;
	margin-right: 10px;
}
</style>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 ">
						<form id="formulario1">

							<fieldset>
								<!-- Users table -->
								<div class="row" style="min-height: 650px;">
									<div class="col-md-12">
										<table id="listado" class="table table-hover">
											<thead>
												<tr>
													<th class="col-md-1 sortable">ID</th>
													<th class="col-md-1 sortable">Usuario</th>

													<th class="col-md-1 sortable">E-mail</th>
													<th class="col-md-1 sortable">Nombre</th>
													<th class="col-md-1 sortable">Apellidos</th>
													<th class="col-md-1 sortable align-right"><span
														class="line"></span>Acciones</th>


												</tr>
											</thead>
											<tbody>


											</tbody>
										</table>
									</div>
								</div>


								<div class="items template permisoslist" style="display: none;">
									<h4 class="title">Seleccione los permisos que tendrá el
										compañero</h4>
											<?php foreach ($this->config->item('permisoscompaneros') as $key=>$datos){ ?>
				                           	<div
										style="height: 100px; width: 32%; float: left; display: block;"
										class="item">
												
												<?php
												echo '<div style="padding:20px;padding-right:0;display:block; width:80%;float:left;box-sizing: border-box;">';
												echo '<span style="width:100%;display:block;font-size:15px;font-weight:bold;overflow-wrap: break-word;">' . $key . '</span>';
												echo '<span style="width:100%;display:block;height:34px;">' . $datos ['descripcion'] . '</span>';
												echo '</div>';
												?>
												<div
											style="overflow-wrap: break-word; padding: 20px; width: 20%; box-sizing: border-box; float: left;">
											<input name="permisos[]" data-cantidaddependen="0"
												data-key="<?php echo $key; ?>"
												data-dependencia='<?php echo json_encode($datos['dependencia'])?>'
												class="permisos" type="checkbox" class="check"
												value="<?php echo $key; ?>" />
										</div>
									</div>
											
				                            <?php } ?>
				                            
				                          		                          
		                        		</div>

							</fieldset>
						</form>
					</div>
				</div>
			</div>

		</div>
		<!-- side right column -->
		<div class="col-md-3 form-sidebar">
			<div class="alert alert-info">
				<i class="icon-lightbulb pull-left"></i> Alguna nota
			</div>
			<h6>Notas de ayuda</h6>
			<p>Seleccionar alguno de los siguientes tipos:</p>
			<ul>
				<li><a href="#">Subir vCard</a></li>
				<li><a href="#">Importar desde CSV</a></li>
				<li><a href="#">Importar desde Excel</a></li>
			</ul>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>js/select2.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.uniform.min.js"></script>


<!-- call this page plugins -->
<script type="text/javascript">
        $(function () {

            // add uniform plugin styles to html elements
            $("input:checkbox, input:radio").uniform();

        
        });
    </script>