<script type="text/javascript">
var site_url = '<?php echo base_url();?>';
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

       	 $('input.nospace').keydown(function(e) {
		        if (e.keyCode == 32) {
		            return false;
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
		
			usuario: {
                validators: {
                    notEmpty: {
                        message: 'El usuario no debe ser vacío'
                    },
					remote:{
						message:'Ya existe un usuario con ese nombre',
						url:site_url+'admin_usuarios_ajx/existeUsuario',
						type:'POST'
					},
					stringLength: {
	                    message: 'El usuario debe contener más de 5 caracteres',
	                    min: 5
                	}
                   
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'El email no debe ser vacío'
                    },
                    emailAddress: {
                        message: 'El valor no es una dirección de email correcta'
                    },
					remote:{
						message:'Ya existe una cuenta vinculada a esta dirección de correo',
						url:site_url+'admin_usuarios_ajx/existeUsuarioEmail',
						type:'POST'

					},

                   
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'La contraseña no debe ser vacía'
                    },
					 identical: {
                        field: 'password2',
                         message: 'Los password no coinciden'
                    },
                    stringLength: {
	                    message: 'El password debe contener más de 5 caracteres',
	                    min: 5
                	}
                   
                }
            },
            password2: {
                validators: {
                    notEmpty: {
                        message: 'La contraseña no debe ser vacía'
                    },
					 identical: {
                        field: 'password',
                        message: 'Los password no coinciden'
                    },
                    stringLength: {
	                    message: 'El password debe contener más de 5 caracteres',
	                    min: 5
                	}
                   
                }
            },
			 rol: {
                validators: {
                    notEmpty: {
                        message: 'Debes elegir un rol de usuario'
                    },
      
                }
            },
			
			/*TERCER PASO*/
			
        }, onSuccess: function(e) {
		 
		  
		e.preventDefault();
	  
			  $('.alert').remove();

	
		var usuario = $('#formulario1 #usuario').val();
		var email = $('#formulario1 #email').val();
		var password = $('#formulario1 #password').val();
		var password2 = $('#formulario1 #password2').val();

		var rol = $('#formulario1 #rol').val();

		if ($('#formulario1 #enviarmail').is(':checked'))
			var enviarmail = 1;
		else
			var enviarmail = 0;
		
		
			
		   
				
					
						$.ajax({
							url: site_url+'admin_usuarios_ajx/crear',
							type: 'POST',
							dataType: "json",
							data: {'usuario':usuario,'email':email,'password':password,'password2':password2,'rol':rol,'enviarmail':enviarmail},
							success: function(result){
								
								if(result=='not_logged'){
									
									
								
								}else if (result == 'ok'){
								
									$('#formulario1').prepend('<div class="alert alert-success">\
									<i class="icon-ok-sign"></i> Usuario Creado!.\
									</div>').fadeIn('slow');
									 
									  $('html,body').animate({
										scrollTop: $(".alert :visible").offset().top-50
									});

									$('#formulario1 .form-control').each(function() {
											 $(this).val('');
											 
										});
									
									$('#formulario1').data('bootstrapValidator').resetForm(); // Reseteamos el formulario
									$('#formulario1 select').find('option:first').attr('selected', 'selected');
									$('#formulario1 #enviarmail').addAttr('checked');
									$('#formulario1 #cuentaactiva').addAttr('checked');
								
								}else{
									
									$('#formulario1').prepend(result).fadeIn('slow');
								
								}
								
							}
						});
						
						
					
			
		 }
    });
	
});
    </script>



<!-- main container -->
<div class="content">

	<div id="pad-wrapper" class="form-page new-user">
		<div class="row header">
			<h3>Crear Usuario</h3>
		</div>
		<div class="col-md-9 personal-info">
			<div class="row form-wrapper">


				<div class="column with-sidebar">
					<form id="formulario1"
						action="<?php echo base_url();?>personas/crear">
						<fieldset>
							<div class="form-group">
								<label for="usuario">Usuario</label>
								<div class="col-md-7">
									<input id="usuario" name="usuario" type="text" placeholder=""
										class="form-control input-md nospace">

								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label for="email">E-mail</label>
								<div class="col-md-7">
									<input id="email" data-validation="number" name="email"
										type="text" placeholder="" class="form-control input-md">

								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label for="password">Contraseña</label>
								<div class="col-md-4">
									<input id="password" name="password" type="password"
										placeholder="" class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="password2">Repita la Contraseña</label>
								<div class="col-md-4">
									<input id="password2" name="password2" type="password"
										placeholder="" class="form-control input-md">

								</div>
							</div>

							<div class="form-group">
								<label for="rol">Rol</label>
								<div class="col-md-4">
								<?php
								
								$HTML = '';
								foreach ( $this->Admin_usuarios_model->obtenerRoles () as $row ) { // RECORREMOS EL LISTADO
									
									$HTML .= '<option  value="' . $row ['id'] . '">' . $row ['nombre'] . '</option>
													';
								}
								
								echo '<select id="rol" name="rol" class="ui-select">
											<option value="">Seleccionar..</option>
											' . $HTML . '</select>';
								
								?>
								  </div>
							</div>


							<div class="form-group">
								<label for="enviarmail">Enviar E-mail con los datos</label> <label
									class="checkbox-inline">
									<div class="checker" id="uniform-enviarmail">
										<span><input type="checkbox" name="enviarmail" id="enviarmail"
											value="1" checked></span>
									</div>
								</label>
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


<!-- call this page plugins -->
<script type="text/javascript">
        $(function () {

            // add uniform plugin styles to html elements
            $("input:checkbox, input:radio").uniform();

        });
    </script>