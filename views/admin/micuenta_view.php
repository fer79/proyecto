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
						      url:site_url+'admin_micuenta_ajx/existeUsuarioEmail',
						      type:'POST'
					      },
              }
            },
            password: {
              validators: {
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


			/*TERCER PASO*/

        }, onSuccess: function(e) {


		e.preventDefault();

			  $('.alert').remove();


		var usuario = $('#formulario1 #usuario').val();
		var email = $('#formulario1 #email').val();
		var password = $('#formulario1 #password').val();
		var password2 = $('#formulario1 #password2').val();

		var nombre = $('#formulario1 #nombre').val();
		var apellidos = $('#formulario1 #apellidos').val();
		var ci = $('#formulario1 #ci').val();
		var f_nacimiento = $('#formulario1 #f_nacimiento').val();
		var ciudadania = $('#formulario1 #ciudadania').val();
		var residencia = $('#formulario1 #residencia').val();
		var telefono = $('#formulario1 #telefono').val();
		var fax = $('#formulario1 #fax').val();
		var celular = $('#formulario1 #celular').val();
		var direccion = $('#formulario1 #direccion').val();
		var ciudad = $('#formulario1 #ciudad').val();
		var departamento = $('#formulario1 #departamento').val();
		var cpostal = $('#formulario1 #cpostal').val();
		var web = $('#formulario1 #web').val();
		var formacionacademica = $('#formulario1 #formacionacademica').val();
		var centrodetitulacion = $('#formulario1 #centrodetitulacion').val();
		var f_titulacion = $('#formulario1 #f_titulacion').val();

						$.ajax({
							url: site_url+'admin_micuenta_ajx/modificar',
							type: 'POST',
							dataType: "json",
							data: {	'email':email,
									'password':password,
									'password2':password2,
									'nombre':nombre,
									'apellidos':apellidos,
									'ci':ci,
									'f_nacimiento':f_nacimiento,
									'ciudadania':ciudadania,
									'residencia':residencia,
									'telefono':telefono,
									'fax':fax,
									'celular':celular,
									'direccion':direccion,
									'ciudad':ciudad,
									'departamento':departamento,
									'cpostal':cpostal,
									'web':web,
									'formacionacademica':formacionacademica,
									'centrodetitulacion':centrodetitulacion,
									'f_titulacion':f_titulacion},
							success: function(result){

								if(result=='not_logged'){


								}else if (result == 'ok'){

									$('#formulario1').prepend('<div class="alert alert-success">\
									<i class="icon-ok-sign"></i> Tus datos han sido modificados!.\
									</div>').fadeIn('slow');

									  $('html,body').animate({
										scrollTop: $(".alert :visible").offset().top-50
									});

									formmodified = 0;

									$('#formulario1').data('bootstrapValidator').resetForm(); // Reseteamos el formulario


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

<link rel="stylesheet" href="<?php echo base_url();?>css/compiled/form-showcase.css" type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet" href="<?php echo base_url();?>css/compiled/form-wizard.css" type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet" href="<?php echo base_url();?>css/compiled/new-user.css" type="text/css" media="screen" />
<!-- this page specific styles -->
<link href="<?php echo base_url();?>css/lib/bootstrap.datepicker.css" type="text/css" rel="stylesheet" />


<link rel="stylesheet" href="<?php echo base_url();?>css/bootstrapValidator.min.css" />
<script type="text/javascript" src="<?php echo base_url();?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/language/es_ES.js"></script>

<!-- main container -->
<div class="content">

	<div id="pad-wrapper" class="form-page new-user">
		<div class="row header">
			<h3>Mi cuenta</h3>
		</div>
		<div class="col-md-9 personal-info">
			<div class="row form-wrapper">

				<div class="column with-sidebar">
					<form id="formulario1">

						<fieldset>

							<h4>Datos de cuenta</h4>
							<hr>
							<div class="form-group">
								<label for="usuario">Usuario</label>
								<div class="col-md-7">
									<input id="usuario" value="<?php  echo $ret['usuario']; ?>" disabled name="usuario" type="text" placeholder="" class="form-control input-md nospace">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label for="email">E-mail</label>
								<div class="col-md-7">
									<input id="email" value="<?php  echo $ret['email']; ?>" data-validation="number" name="email" type="text" placeholder="" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label for="password">Contraseña</label>
								<div class="col-md-4">
									<input id="password" name="password" type="password" placeholder="" class="form-control input-md">
								</div>
							</div>
							<div class="form-group">
								<label for="password2">Repita la Contraseña</label>
								<div class="col-md-4">
									<input id="password2" name="password2" type="password" placeholder="" class="form-control input-md">
								</div>
							</div>

							<h4>Datos de AutoRelleno</h4>
							<div class="alert alert-info">
								<i class="icon-exclamation-sign"></i> Los siguientes datos
								pueden ser utilizados para rellenar los formularios siempre que
								los mismos estén diseñados para admitir estos campos
							</div>
							<hr>

							<h4>Datos Generales</h4>

							<div class="form-group">
								<label for="ci">CI / DNI</label>
								<div class="col-md-7">
									<input id="ci" value="<?php  echo $ret['ci']; ?>" name="ci" type="text" placeholder="" class="form-control input-md">
								</div>
							</div>
							<div class="form-group">
								<label for="nombre">Nombre</label>
								<div class="col-md-7">
									<input id="nombre" value="<?php  echo $ret['nombre']; ?>" name="nombre" type="text" placeholder="" class="form-control input-md">
								</div>
							</div>
							<div class="form-group">
								<label for="apellidos">Apellidos</label>
								<div class="col-md-7">
									<input id="apellidos" value="<?php  echo $ret['apellidos']; ?>" name="apellidos" type="text" placeholder="" class="form-control input-md">
								</div>
							</div>
							<div class="wdatepicker form-group">
								<label for="f_nacimiento">Fecha de Nacimiento</label>
								<div class="col-md-7">
									<input id="f_nacimiento" value="<?php  echo $ret['f_nacimiento']; ?>" name="f_nacimiento" type="text" placeholder="" class="form-control input-datepicker">
								</div>
							</div>

							<h4>Ubicación</h4>

							<div class="form-group">
								<label for="ciudadania">Ciudadanía</label>
								<div class="col-md-7">
									<input id="ciudadania"
										value="<?php  echo $ret['ciudadania']; ?>" name="ciudadania"
										type="text" placeholder="" class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="residencia">Residencia</label>
								<div class="col-md-7">
									<input id="residencia" value="<?php  echo $ret['residencia']; ?>" name="residencia" type="text" placeholder="" class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="departamento">Departamento</label>
								<div class="col-md-7">
									<input id="departamento" value="<?php  echo $ret['departamento']; ?>" name="departamento" type="text" placeholder="" class="form-control input-md">
								</div>
							</div>
							<div class="form-group">
								<label for="ciudad">Ciudad</label>
								<div class="col-md-7">
									<input id="ciudad" value="<?php  echo $ret['ciudad']; ?>"
										name="ciudad" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="direccion">Dirección</label>
								<div class="col-md-7">
									<input id="direccion" value="<?php  echo $ret['direccion']; ?>"
										name="direccion" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="cpostal">Código Postal</label>
								<div class="col-md-7">
									<input id="cpostal" value="<?php  echo $ret['cpostal']; ?>"
										name="cpostal" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<h4>Contacto</h4>

							<div class="form-group">
								<label for="celular">Celular</label>
								<div class="col-md-7">
									<input id="celular" value="<?php  echo $ret['celular']; ?>"
										name="celular" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="telefono">Teléfono</label>
								<div class="col-md-7">
									<input id="telefono" value="<?php  echo $ret['telefono']; ?>"
										name="telefono" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="fax">Fax</label>
								<div class="col-md-7">
									<input id="fax" value="<?php  echo $ret['fax']; ?>" name="fax"
										type="text" placeholder="" class="form-control input-md">

								</div>
							</div>

							<h4>Formación</h4>

							<div class="form-group">
								<label for="formacionacademica">Formación Académica</label>
								<div class="col-md-7">
									<input id="formacionacademica"
										value="<?php  echo $ret['formacionacademica']; ?>"
										name="formacionacademica" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<div class="form-group">
								<label for="centrodetitulacion">Centro de Titulación</label>
								<div class="col-md-7">
									<input id="centrodetitulacion"
										value="<?php  echo $ret['centrodetitulacion']; ?>"
										name="centrodetitulacion" type="text" placeholder=""
										class="form-control input-md">

								</div>
							</div>
							<div class="wdatepicker form-group">
								<label for="f_titulacion">Fecha de Titulación</label>
								<div class="col-md-7">
									<input id="f_titulacion"
										value="<?php  echo $ret['f_titulacion']; ?>"
										name="f_titulacion" type="text" placeholder=""
										class="form-control input-datepicker">

								</div>
							</div>

							<h4>Otros</h4>

							<div class="form-group">
								<label for="web">Web / Blog</label>
								<div class="col-md-7">
									<input id="web" value="<?php  echo $ret['web']; ?>" name="web"
										type="text" placeholder="" class="form-control input-md">

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
			<!--
      <div class="alert alert-info">
        <i class="icon-lightbulb pull-left"></i>
                       Alguna nota
      </div>
      -->
			<h6>Autorelleno</h6>
			<p>Al llenar los datos de autorelleno una vez, no tendrá que volver a
				ingresarlos nuevamente en futuras inscripciones. De esta forma el
				proceso se hace más sencillo y rápido.</p>
			  <!--
        <ul>
          <li><a href="#">Subir  vCard</a></li>
          <li><a href="#">Importar desde CSV</a></li>
          <li><a href="#">Importar desde Excel</a></li>
        </ul>
        -->
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
		}).on('changeDate', function (ev) {

        $(this).datepicker('hide');
				$('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
    });

  });
</script>
