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



<script type="text/javascript">
       $(document).ready(function() {


    	   function listarUsuarios(){
    	    	
    	    	
    	    	xhrlistar = $.ajax({
    	    		url:	site_url+'admin_categorias_ajx/traerUsuarios',
    	    		type: 	'POST',
    	    		data: {id:<?php echo $ret['id']; ?>},
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

    	    $('body').on('click','.botonborrar',function(e){

    	    	var idUsuario = $(this).attr('data-id');
    			
    			getConfirm('Estás seguro que deseas eliminar al usuario?',function(result){


    				if (result == true){

    					$.ajax({
    			    		url:	site_url+'admin_categorias_ajx/borrarUsuario',
    			    		type: 	'POST',
    			    		data: {'id':<?php echo $ret['id']; ?>,'idUsuario':idUsuario},
    			    		success: function(result){ 

    			    			listarUsuarios();

    			    		}
    			  		});
    				}
    				

    				
    			});



    			e.preventDefault();
    				
    	    });

    	    $('body').on('click','#agregarCompanerobtn',function(e){


    	    	
    	    	
    			var idUsuario = $('#buscarcompanero').val();


    			var existe = $('tr#id-'+idUsuario).length;
    			
    			if ((idUsuario != '') && (existe == 0)){

    				$.ajax({
    		    		url:	site_url+'admin_categorias_ajx/agregarUsuario',
    		    		type: 	'POST',
    		    		data: {'id':<?php echo $ret['id']; ?>,'idUsuario':idUsuario},
    		    		success: function(result){ 
    		
    		
    						
    		    			listarUsuarios();
    		
    		    		}
    		  		});

    			}
    				
    	    });
			

    		$('#buscarcompanero').select2({
    			ajax: {
    				url:	site_url+'admin_categorias_ajx/buscarUsuario',
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


    $('#formulario1').on('init.field.bv', function(e, data) {
            // data.bv      --> The BootstrapValidator instance
            // data.field   --> The field name
            // data.element --> The field element

            var $parent    = data.element.parents('.form-group'),
                $icon      = $parent.find('.form-control-feedback[data-bv-icon-for="' + data.field + '"]'),
                options    = data.bv.getOptions(),                      // Entire options
                validators = data.bv.getOptions(data.field).validators; // The field validators
			
		
				if ( validators.notEmpty && options.feedbackIcons && options.feedbackIcons.required) {
					// The field uses notEmpty validator
					// Add required icon
					$icon.addClass(options.feedbackIcons.required).show();
				}
			
        })
	.bootstrapValidator({
		
		excluded:[], // No excluimos nada
		
		feedbackIcons: {
			required: 'glyphicon glyphicon-asterisk',
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
			
        },
        fields: {
		
			nombre: {
                validators: {
                    notEmpty: {
                        message: 'El nombre no debe ser vacío'
                    },
					remote:{
						message:'Ya existe una categoría con ese nombre y ese padre',
						url:site_url+'admin_categorias_ajx/existeCategoria',
						type:'POST',
						data: {
                            padre	: $('#formulario1 #padre option:selected').val(),
                            id		:'<?php echo $ret['id']; ?>'
                        }
					},
					
               
                }
            }
			
			/*TERCER PASO*/
			
        }, onSuccess: function(e) {
		 
		  
		e.preventDefault();
	  
			  $('.alert').remove();

	
		var nombre = $('#formulario1 #nombre').val();
		var padre = $('#formulario1 #padre').val();
		


			$.ajax({
				url: site_url+'admin_categorias_ajx/modificar',
				type: 'POST',
				dataType: "json",
				data: {'id':'<?php echo $ret['id']; ?>','nombre':nombre,'padre':padre},
				success: function(result){
					
					if(result=='not_logged'){
						
						
					
					}else if (result == 'ok'){
					
						$('#formulario1').prepend('<div class="alert alert-success">\
						<i class="icon-ok-sign"></i> Categoría modificada.\
						</div>').fadeIn('slow');
						 
						  $('html,body').animate({
							scrollTop: $(".alert :visible").offset().top-50
						});

					}else{
						
						$('#formulario1').prepend('<div class="alert alert-danger">\
						<i class="icon-remove-sign"></i> Han ocurrido errores, por favor contacta un administrador.\
						</div>').fadeIn('slow');
					
					}
					
				}
			});
						
						
					
			
		 }
    });


    listarUsuarios();
});
    </script>



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
			<h3>Modificar Categoria</h3>
		</div>
		<div class="col-md-9 personal-info">
			<div class="row form-wrapper">


				<div class="column with-sidebar">
					<form id="formulario1">
						<fieldset>
							<div class="form-group">
								<label for="usuario">Nombre</label>
								<div class="col-md-7">
									<input id="nombre" name="nombre" type="text" placeholder=""
										value="<?php echo $ret['nombre']; ?>"
										class="form-control input-md">
								</div>
							</div>
							<div class="form-group">
								<label>Padre:</label>
								<div class="ui-select">
									<select style="width: 400px" id="padre">
				                                                             <?php
																																																																	function listarCat($categorias, $nivel = 0, $seleccionado = 0) {
																																																																		foreach ( $categorias as $categoria ) {
																																																																			
																																																																			$linea = '';
																																																																			
																																																																			for($i = 0; $i < $nivel; $i ++) {
																																																																				$linea .= '----';
																																																																			}
																																																																			
																																																																			$seleccionadoAux = '';
																																																																			if ($categoria ['id'] == $seleccionado)
																																																																				$seleccionadoAux = 'selected';
																																																																			
																																																																			echo '<option ' . $seleccionadoAux . ' value="' . $categoria ['id'] . '">' . $linea . $categoria ['nombre'] . '</option>';
																																																																			
																																																																			listarCat ( $categoria ['hijos'], $nivel + 1, $seleccionado );
																																																																		}
																																																																	}
																																																																	
																																																																	listarCat ( $padres, 0, $ret ['padre'] );
																																																																	
																																																																	?>
				                            </select>
								</div>
							</div>
						</fieldset>
						<div class="wizard-actions">
							<button type="submit" class="btn-glow success">Guardar</button>
						</div>
					</form>
					<div class="column with-sidebar">
						<div class="col-md-12 col-sm-12 col-xs-12 ">
							<div class="form-group">
								<label for="usuario">Buscar: </label>
								<div class="col-md-7" style="padding: 0;">
									<input type="text" class="col-md-5 search" id="buscarcompanero"
										placeholder="Agregar nuevo usuario">
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