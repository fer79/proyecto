<script type="text/javascript">
var site_url = '<?php echo base_url();?>';
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


    function check_dependencia(dependencia,uncheck){

		if (typeof dependencia != 'undefined'){
		 
			if (uncheck){

				
				$.each(dependencia,function(i,val){
					
					    var dependencia2 = JSON.parse($('.permisos[value="'+val+'"]').attr('data-dependencia'));
						
					    $('.permisos[value="'+val+'"]').attr('data-cantidaddependen',parseInt($('.permisos[value="'+val+'"]').attr('data-cantidaddependen'))-1);
	           	    	
						if ($('.permisos[value="'+val+'"]').attr('data-cantidaddependen') == 0){
							$('.permisos[value="'+val+'"]').prop('disabled',false).removeClass('otrosdependen');
							
						}

       	 		});


			}else{
				$.each(dependencia,function(i,val){

					if ( !$('.permisos[value="'+val+'"]').is(':checked')){
    					var dependencia2 = JSON.parse($('.permisos[value="'+val+'"]').attr('data-dependencia'));
    					check_dependencia(dependencia2);
    				}
        				
        				
    					$('.permisos[value="'+val+'"]').prop('checked',true).prop('disabled',true).addClass('otrosdependen');
    					$('.permisos[value="'+val+'"]').attr('data-cantidaddependen',parseInt($('.permisos[value="'+val+'"]').attr('data-cantidaddependen'))+1);
    					
        				
					
            	});
			}

			$.uniform.update();	
		}
	}


   	 $('.permisos').click(function(){

   		 var dependencia = JSON.parse($(this).attr('data-dependencia'));

   		if ($(this).is(':checked')) {

   			check_dependencia(dependencia,false);

   	        
   	    } else {

   	    	check_dependencia(dependencia,true);

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
			
        }).bootstrapValidator({
		
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
						message:'Ya existe un rol con ese nombre',
						url:site_url+'admin_usuarios_roles_ajx/existeRol',
						type:'POST',
						data: {
                            id: '<?php echo $idRol;?>'
                        }
					}
					
               
                }
            },
			'permisos[]': {
                validators: {
                    notEmpty: {
                        message: 'Debes elegir al menos un permiso'
                    },
      
                }
            }
			
			/*TERCER PASO*/
			
        }, onSuccess: function(e) {
		 
		  
		e.preventDefault();
	  
			  $('.alert').remove();

	
			  var nombre = $('#formulario1 #nombre').val();

				 var permisos = [];
				 $('#formulario1 .permisos:checked').each(function(i){
					 permisos[i] = $(this).val();
			        });
		

						$.ajax({
							url: site_url+'admin_usuarios_roles_ajx/modificar',
							type: 'POST',
							dataType: "json",
							data: {'id':'<?php echo $idRol;?>','nombre':nombre,'permisos':JSON.stringify(permisos)},
							success: function(result){
								
								if(result=='not_logged'){
									
									
								
								}else if (result == 'ok'){
								
									$('#formulario1').prepend('<div class="alert alert-success">\
									<i class="icon-ok-sign"></i> Rol modificado!.\
									</div>').fadeIn('slow');
									 
									  $('html,body').animate({
										scrollTop: $(".alert :visible").offset().top-50
									});

									formmodified = 0;
									
									$('#formulario1').data('bootstrapValidator').resetForm(); // Reseteamos el formulario
									$('#formulario1 .permisos:checked').each(function(i){
										$(this).prop('checked',false);
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
<div class="content">

	<div id="pad-wrapper" class="form-page new-user">
		<div class="row header">
			<h3>Modificar Rol</h3>
		</div>
		<div class="col-md-9 personal-info">
			<div class="row form-wrapper">

				<div class="column with-sidebar">
					<form id="formulario1">

						<fieldset>
							<div class="form-group">
								<label for="usuario">Nombre</label>
								<div class="col-md-7">
									<input id="nombre" value="<?php  echo $ret['nombre']; ?>"
										name="nombre" type="text" placeholder=""
										class="form-control input-md">
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<div class="items">
										<h4 class="title">Seleccione los permisos que tendrá el rol</h4>
									<?php foreach ($this->config->item('permisosroles') as $key=>$datos){ ?>
		                           	<div
											style="height: 85px; width: 32%; float: left; display: block;"
											class="item">
										
										<?php
										echo '<div style="padding:20px;padding-right:0;display:block; width:80%;float:left;box-sizing: border-box;">';
										echo '<span style="width:100%;display:block;font-size:15px;font-weight:bold;overflow-wrap: break-word;">' . $key . '</span>';
										echo '<span style="width:100%;display:block;">' . $datos ['descripcion'] . '</span>';
										echo '</div>';
										?>
										<div
												style="overflow-wrap: break-word; padding: 20px; width: 20%; box-sizing: border-box; float: left;">
												<input name="permisos[]" data-cantidaddependen="0"
													<?php if (in_array($key,$ret['permisos'])){ echo 'checked="checked"'; } ?>
													data-dependencia='<?php echo json_encode($datos['dependencia'])?>'
													class="permisos" type="checkbox" class="check"
													value="<?php echo $key; ?>" />
											</div>
										</div>
									
		                            <?php } ?>
		                            
		                          		                          
                        		</div>
								</div>
							</div>
						</fieldset>
						<div class="wizard-actions">
							<button type="submit" class="btn-glow success">Guardar</button>
						</div>
					</form>
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
<script src="<?php echo base_url();?>js/bootstrap.datepicker.js"></script>


<!-- call this page plugins -->
<script type="text/javascript">
        $(function () {

            // add uniform plugin styles to html elements
            $("input:checkbox, input:radio").uniform();
			
			 // datepicker plugin
            $('.input-datepicker').datepicker({
			
			format: 'dd/mm/yyyy',
			}
			).on('changeDate', function (ev) {
                $(this).datepicker('hide');
				$('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
            });
			
        
        });
    </script>