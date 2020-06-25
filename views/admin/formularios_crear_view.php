
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
<link href="<?php echo base_url();?>css/jquery.multiselect.css"
	type="text/css" rel="stylesheet" />
<link rel="stylesheet"
	href="<?php echo base_url();?>css/bootstrap-datetimepicker.min.css"
	type="text/css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>js/wysiwyg/bootstrap3-wysihtml5.css"></link>
<script src="<?php echo base_url();?>js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>js/wysiwyg/bootstrap3-wysihtml5.js"></script>

<link rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>css/bootstrap-tokenfield.min.css"></link>
<script src="<?php echo base_url();?>js/bootstrap-tokenfield.min.js"></script>
<script type="text/javascript">
		var site_url = '<?php echo base_url();?>';
		var habilitarpersonasmal = 0
		$(document).ready(function() {


// 			$('#vincular').select2({
// 	    		ajax: {
// 	    			url:	site_url+'formularios_ajx/obtenerVinculables',
// 	    			type: 	'POST',
// 	    			dataType: 'json',
// 	    			delay:250,
// 	    			data: function (term) {
// 	    	            return {
// 	    	                term: term
// 	    	            };
// 	    	        },
// 	    	        results: function (data) {
// 	    	            return {
// 	    	                results: $.map(data, function (item) {
// 	    	                    return {
// 	    	                        text: item.titulo +" "+ item.apellidos + " (" +item.fechainicio +' - '+ item.fechafin + ")",
// 	    	                        id: item.id
// 	    	                    }
// 	    	                })
// 	    	            };
// 	    	        }
// 	    		}
// 	    	});



			$(".onlynumber").keydown(function (e) {
		        // Allow: backspace, delete, tab, escape, enter and .
		        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		             // Allow: Ctrl+A, Command+A
		            (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
		             // Allow: home, end, left, right, down, up
		            (e.keyCode >= 35 && e.keyCode <= 40)) {
		                 // let it happen, don't do anything
		                 return;
		        }
		        // Ensure that it is a number and stop the keypress
		        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		            e.preventDefault();
		        }
		    });

			 $('#colderecha,#colizquierda').wysihtml5();

			 $('#tipoformulario').change(function(){

			        if ($(this).val() == 'evaluacion'){

			          $('#vincularevaluacion').slideDown();
			          $('#cargahoraria').hide();
			          $('input#sedebeabonar').removeAttr('checked').change();
			          $('#zonapago').hide();
			          $.uniform.update();

			        }else{
			        	$('#cargahoraria').slideDown();
			          $('#vincularevaluacion').hide();
			          $('#zonapago').slideDown();

			        }
			 });


			 $('input#sedebeabonar').change(function(){


        	 if ($(this).is(':checked')){
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('lugarabono', true);
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('costocurso', true);
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('fechaabonoinicio', true);
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('fechaabonofin', true);
                 $('.sedebeabonar').slideDown();

          }else{

                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('lugarabono', false);
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('costocurso', false);
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('fechaabonoinicio', false);
                 $('#formulario1').data('bootstrapValidator').enableFieldValidators('fechaabonofin', false);

                 $('#lugarabono').val('');
                 $('#costocurso').val('0');
                 $('#fechaabonoinicio').val('');
                 $('#fechaabonofin').val('');
                 $('.sedebeabonar').slideUp();
          }

      });



			$('#copiarformulario').change(function(){

				if ($(this).find('option:selected').val() != ''){

						$.ajax({
						url: site_url+'formularios_ajx/obtenerFormulario',
						type: 'POST',
						data: {'id':$(this).val()},
						success: function(result){

							if(result=='not_logged'){


							}else {


								$('#formulario').html(result);

								$('#formulario .droppedField').unbind("click").click(function (e) {
				                    e.preventDefault();
				                    // The following assumes that dropped fields will have a ctrl-defined.
				                    //   If not required, code needs to handle exceptions here.
				                    var me = $(this)
				                    var ctrl = me.attr('data-tipo');
				                    customize_ctrl(ctrl, this.id);

				                    //window["customize_"+ctrl_type](this.id);
				                });

								var index = []
								$('#formulario .droppedField').each(function(i,o){

									index.push($(this).attr('id')); //Metemos todos los  index en el array

								});

				                crearIndex(index); //Creamos index

							}

						}
					});
				}


			});


			var publicado = 1;

			$('.esBorrador').click(function(){
				publicado = 0;
				$('#formulario1').submit();
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
				/*PRIMER PASO*/
				titulo: {
					validators: {
						notEmpty: {
							message: 'El título no debe ser vacío'
						}
					}
				},
				titulo: {
					validators: {
						notEmpty: {
							message: 'El título no debe ser vacío'
						}
					}
				},
				fechainicio: {
					validators: {
						notEmpty: {
							message: 'La fecha de inicio es requerida'
						},
						date: {
							format: 'YYYY-MM-DD H:m:s',
							message: 'La fecha de inicio no es válida'
						}
					}
				},
				fechafin: {
					validators: {
						notEmpty: {
							message: 'La fecha de fin es requerida'
						},
						date: {
							format: 'YYYY-MM-DD H:m:s',
							message: 'La fecha de fin no es válida'
						}
					}
				},
				fechacomienzocurso: {
		            validators: {
		              notEmpty: {
		                message: 'La fecha de comienzo de curso es requerida'
		              },
		              date: {
		                format: 'YYYY-MM-DD',
		                message: 'La fecha de comienzo de curso no es válida'
		              }
		            }
		          },
				/*ABONO*/
				 costocurso: {
		          enabled: false,
		          validators: {
		            notEmpty: {
		              message: 'El costo del curso no puede ser vacío'
		            }
		          }
		        },
				lugarabono: {
					enabled: false,
					validators: {
						notEmpty: {
							message: 'El lugar de abono no debe ser vacío'
						}
					}
				},
				fechaabonoinicio: {
					enabled: false,
					validators: {
						notEmpty: {
							message: 'La fecha de inicio de abono es requerida'
						},
						date: {
							format: 'YYYY-MM-DD',
							message: 'La fecha de inicio de abono no es válida'
						}
					}
				},

				fechaabonofin: {
					enabled: false,
					validators: {
						notEmpty: {
							message: 'La fecha de fin de abono es requerida'
						},
						date: {
							format: 'YYYY-MM-DD',
							message: 'La fecha de fin de abono no es válida'
						}
					}
				},
				/*ABONO*/
				tipoformulario: {
					validators: {
						notEmpty: {
							message: 'El tipo de formulario no debe ser vacío'
						}
					}
				},
				cantidad: {
					validators: {
						integer: {

							message: 'El número no es válido'

						},
						notEmpty: {
							message: 'La cantidad de inscripciones/evaluaciones no debe ser vacío'
						}

					}
				},

		},
		onError:function(e){

			publicado = 1;

		},
		onSuccess: function(e) {

			e.preventDefault();
			$('.alertgeneral').remove();
			var sinmodificar = $('#formulario [modificado="0"]');

			$('#habilitarpersonas').parent().parent().parent().removeClass('has-error').addClass('has-success');
			$('#habilitarpersonas').parent().parent().parent().find('.help-block.error').remove();
			$('#habilitarpersonas').parent().parent().parent().find('.form-control-feedback').remove();
			$('#habilitarpersonas').parent().parent().parent().find('div:first').append('<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-ok" data-bv-icon-for="fechafin" style="display: block;"></i>');

			if( habilitarpersonasmal > 0 ){

				$('#habilitarpersonas').parent().parent().parent().addClass('has-error');
				$('#habilitarpersonas').parent().parent().parent().find('div:first').append('<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-remove" data-bv-icon-for="titulo" style="display: block;"></i>\
                 <small class="help-block" data-bv-validator="notEmpty" data-bv-for="titulo" data-bv-result="INVALID" style="">Existen E-mails inválidos</small>');
				$('html,body').animate({
							scrollTop: $('#habilitarpersonas').parent().parent().parent().offset().top-50
						});
			}else if (sinmodificar.length > 0){

				sinmodificar.each(function(){

					$(this).addClass('modificar-error');

				});


				$('#formulario1').prepend('<div class="alert alertgeneral alert-danger">\
							<i class="icon-remove-sign"></i> Hay campos del formulario con los valores por defecto\
						</div>').fadeIn('slow');

			}else{

			var titulo = $('#formulario1 #titulo').val();
			var fechainicio = $('#formulario1 #fechainicio').val();
			var fechafin = $('#formulario1 #fechafin').val();
			 var fechacomienzocurso = $('#formulario1 #fechacomienzocurso').val();
			var cantidad = $('#formulario1 #cantidad').val();
			var tipoformulario = $('#formulario1 #tipoformulario option:selected').val();
			var colderecha =$('#formulario1 #colderecha').val();
			var colizquierda =$('#formulario1 #colizquierda').val();
			var vincular =$('#formulario1 #vincular').val();
			var categoria = $('#formulario1 #categoria option:selected').val();

			  var cargahoraria = $('#formulario1 [name="cargahoraria"]').val();

			var abonar = $('#formulario1 input#sedebeabonar').is(':checked');

			 var monedacostocurso='';
			  var costocurso = '';
		      var lugarabono='';
		      var fechaabonoinicio='';
		      var fechaabonofin ='';

		      if (abonar){
		        abonar = 1;
		        monedacostocurso = $('#formulario1 #monedacostocurso option:selected').val();
		        costocurso = $('#formulario1 #costocurso').val();
		        lugarabono = $('#formulario1 #lugarabono').val();
		        fechaabonoinicio = $('#formulario1 #fechaabonoinicio').val();
		        fechaabonofin = $('#formulario1 #fechaabonofin').val();
		      }else{

		        abonar = 0;

		      }


		      var emails = $('#habilitarpersonas').parent().text().match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);

		      if (emails == null)
		    	  emails=[];

			var campos =[];
			$('#formulario .droppedField').each(function(i,o){

				campos.push({'id':$(this).attr('id'),'tipo':$(this).attr('data-tipo'),'dependencia':$(this).attr('dependencia'),'dependenciavalor':$(this).attr('dependencia-valor'),'opciones':JSON.parse($(this).attr('opciones'))});

			});


			$.ajax({
				url: site_url+'formularios_ajx/crear',
				type: 'POST',
				dataType: "json",
				data: {'publicado':publicado,'titulo':titulo,'abonar':abonar,'lugarabono':lugarabono,
							 'monedacostocurso':monedacostocurso,'costocurso':costocurso,'fechaabonoinicio':fechaabonoinicio,
							 'fechaabonofin':fechaabonofin,'vincular':vincular,'fechainicio':fechainicio,'fechafin':fechafin,
							 'cantidad':cantidad,'colderecha':colderecha,'colizquierda':colizquierda,'tipoformulario':tipoformulario,
							 'campos':JSON.stringify(campos),'emails':JSON.stringify(emails),'categoria':categoria,
							 'fechacomienzocurso':fechacomienzocurso,'cargahoraria':cargahoraria},
				success: function(result){

					publicado = 1;
					if(result=='not_logged'){


					}else if (result == 'ok'){

						$('#formulario1').prepend('<div class="alert alertgeneral alert-success">\
							<i class="icon-ok-sign"></i> Formulario Creado!.\
						</div>').fadeIn('slow');

						$('html,body').animate({
							scrollTop: $(".alert :visible").offset().top-50
						});


						$('#formulario1').data('bootstrapValidator').resetForm(); // Reseteamos el formulario
						$('#formulario1').html();
						$('#formulario1 select').find('option:first').attr('selected', 'selected');


					}else{

						$('#formulario1').prepend('<div class="alert alertgeneral alert-danger">\
							<i class="icon-remove-sign"></i> Han ocurrido errores, por favor contacta un administrador.\
						</div>').fadeIn('slow');

					}

				}
			});

			publicado = 1;

		}
	}
});

});
	</script>



<!-- main container -->
<div class="content">

	<div id="pad-wrapper" class="form-page">
		<div class="row header">
			<div class="col-md-12">
				<h3>Crear Formulario</h3>
			</div>
		</div>
		<div class="row form-wrapper">
			<!-- left column -->
			<form name="formulario1" id="formulario1">
				<div class="column">


					<div class="form-group">
						<label for="titulo">Título</label>
						<div class="col-md-7">
							<input name="titulo" id="titulo" class="form-control" type="text">
						</div>
					</div>

					<div class="form-group">
						<label for="tipoformulario">Tipo de Formulario:</label>
						<div class="ui-select">
							<select id="tipoformulario" name="tipoformulario">
								<option value="inscripcion" selected="selected">Inscripción</option>
								<option value="evaluacion">Evaluación</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label>Categoría:</label>
						<div class="col-md-7">
							<select class="form-control" style="width: 400px" id="categoria">
                               <?php
									function listarCat($categorias, $nivel = 0) {
										foreach ( $categorias as $categoria ) {

											$linea = '';
											for($i = 0; $i < $nivel; $i ++) {
												$linea .= '----';
											}

											echo '<option value="' . $categoria ['id'] . '">' . $linea . $categoria ['nombre'] . '</option>';

											listarCat ( $categoria ['hijos'], $nivel + 1 );
										}
									}

									if (! empty ( $categorias ))
										listarCat ( $categorias );
									else
										echo '<option value="1">General</option>';
								?>
	                            </select>
							<p class="help-block">El formulario será mostrado solo dentro de
								la categoría seleccionada</p>
						</div>
					</div>

					<div class="form-group wdatepicker">
						<label for="fechainicio">Fecha de Inicio</label>
						<div class="col-md-4">
							<input id="fechainicio" name="fechainicio" type="text"
								placeholder="" class="form-control  input-datepicker">

						</div>
					</div>
					<div class="form-group wdatepicker">
						<label for="fechafin">Fecha de Fin</label>
						<div class="col-md-4">
							<input id="fechafin" name="fechafin" type="text" placeholder=""
								class="form-control  input-datepicker">

						</div>
					</div>

					<div class="form-group wdatepicker">
						<label for="fechacomienzocurso">Fecha de comienzo de curso</label>
						<div class="col-md-4">
							<input id="fechacomienzocurso" name="fechacomienzocurso"
								type="text" placeholder=""
								class="form-control  input-datepicker">
							<p class="help-block">Esta fecha se utilizará para calcular los
								tiempos máximos para una inscripción tardía.</p>

						</div>
					</div>

				  <?php  if ($this->config->item('diploma_habilitado')){ ?>
				  <div style="display: none;" id="cargahoraria" class="form-group ">
						<label for="cargahoraria">Carga Horaria</label>
						<div class="col-md-4">
							<input name="cargahoraria" id="cargahoraria" class="form-control"
								type="cargahoraria">
							<p class="help-block">El valor del campo se utilizará para
								rellenar el diploma</p>

						</div>
					</div>
					<?php } ?>



					 <div style="display: none;" id="vincularevaluacion"
						class="form-group">
						<label for="vincular">Vincular evaluación a</label>
						<div class="col-md-10">
							<select id="vincular" name="vincular">
							</select>
							<p class="help-block">Solo las personas inscriptas y con plaza
								sorteada serán notificadas de la evaluación. A su vez puede
								agregar otros E-mails en el campo debajo para habilitar a esas
								personas.</p>
						</div>
					</div>
					<style>
					.select2-container {
						padding: 0;
						margin-right: 10px;
					}
					</style>
					<h4>Habilitados</h4>
					<hr>
					<div class="form-group ">
						<label for="habilitarpersonas">Permitir a las siguientes personas
							<span
							style="display: block; with: 100%; text-align: center; font-size: 19px; color: #ccc;"><span
								title="Emails Válidos" id="habilitarpersonascantidad"
								style="color: #66afe9; cursor: pointer;">0</span> / <span
								title="Emails Inválidos" id="habilitarpersonascantidadmal"
								style="color: #d9534f; cursor: pointer;">0</span></span>
						</label>
						<div class="col-md-7 col-xs-12">
							<textarea type="text" style="min-height: 50px;"
								class="form-control" id="habilitarpersonas" /></textarea>

							<p class="help-block">Acepta lista de emails separada por ";".
								Puede presionar TAB o Enter luego de agregado cada mail para
								validarlo.Nota: Si el formulario es una inscripción, solo las
								persona con dicho mail vinculado a su usuario podrá acceder</p>
						</div>
					</div>

					<div class="form-group">
						<label for="cantidad">Cantidad de Inscripciones/Evaluaciones</label>
						<div class="col-md-4">
							<input name="cantidad" id="cantidad" class="form-control"
								value="0" type="text">
						</div>
					</div>
					<div id="zonapago" style="display: none;">
						<h4>Pago</h4>
						<hr>

						<div class="form-group">
							<label>Se debe abonar:</label>
							<div class="col-md-7 ctrl-checkboxgroup">
								<label class="chbox checkbox-inline">
									<div class="checker" id="uniform-sedebeabonar">
										<span><div class="checker" id="uniform-sedebeabonar">
												<span><input type="checkbox" id="sedebeabonar" value="1"></span>
											</div></span>
									</div>
								</label>
							</div>
						</div>
						<div style="display: none;" class="form-group sedebeabonar">
							<label for="lugarabono">Lugar para abonar</label>
							<div class="col-md-7">
								<input name="lugarabono" id="lugarabono" class="form-control"
									type="text">
							</div>
						</div>
						<div style="display: none;" class="form-group sedebeabonar">
							<label for="costocurso">Costo del curso</label>
							<div class="col-md-7">


								<select id="monedacostocurso" name="monedacostocurso"
									class="form-control"
									style="float: left; width: 120px; margin-right: 10px;">
									<option value="$U">$U</option>
									<option value="U$S">U$S</option>
								</select> <input name="costocurso" value="0" id="costocurso"
									style="width: 80%" class="form-control onlynumber" type="text">
							</div>
						</div>
						<div style="display: none;"
							class="form-group wdatepicker sedebeabonar">
							<label for="fechaabonoinicio">Inicio del plazo para abonar</label>
							<div class="col-md-4">
								<input id="fechaabonoinicio" name="fechaabonoinicio" type="text"
									placeholder="" class="form-control  input-datepicker">

							</div>
						</div>
						<div style="display: none;"
							class="form-group wdatepicker sedebeabonar">
							<label for="fechaabonofin">Fin del plazo para abonar</label>
							<div class="col-md-4">
								<input id="fechaabonofin" name="fechaabonofin" type="text"
									placeholder="" class="form-control  input-datepicker">

							</div>
						</div>
					</div>


					<h4>Formulario</h4>
					<hr>

					<div class="form-group" style="overflow: hidden; display: block;">
						<label>Texto columna derecha:</label>
						<div class="col-md-7">
							<textarea name="colderecha" id="colderecha" class="form-control"></textarea>
						</div>
					</div>
					<div class="form-group" style="overflow: hidden; display: block;">
						<label>Texto columna izquierda:</label>
						<div class="col-md-7">
							<textarea name="colizquierda" id="colizquierda"
								class="form-control"></textarea>
						</div>
					</div>



					<h4>Campos del Formulario</h4>
					<hr>
					<div class="form-group">
						<label>Copiar campos de formulario:</label>
						<div class="ui-select">
							<select style="width: 400px" id="copiarformulario">
								<option value="">- Ninguno -</option>
	                               <?php
										foreach ( $ret as $formulario ) {

											echo '<option value="' . $formulario ['id'] . '">' . $formulario ['titulo'] . ' (' . $formulario ['fechainicio'] . ' - ' . $formulario ['fechafin'] . ') </option>';
										}
									?>
	                            </select>
						</div>
					</div>
				</div>
				<div class=" stickycolumn"
					style="display: block; width: 100%; overflow: hidden;">
					<div id="formulario" class="droppedFields col-md-8"></div>

					<div id="sticky" class="col-md-4 pull-right row ctrls"
						style="position: relative; margin-top: 20px;">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#texto">Texto</a></li>
							<li><a href="#opciones">Opciones</a></li>
							<li><a href="#archivos">Archivos</a></li>
							<li><a href="#otros">Otros</a></li>
						</ul>
						<div class="tab-content">
							<form id="draggable">
								<div class="tab-pane active" id="texto">

									<div class="field-box selectorField draggableField"
										data-tipo="textbox">
										<label>Campo de texto corto:</label>
										<div class="col-md-7">
											<input class="form-control">
											<p class="help-block">Texto de Ayuda</p>
										</div>
									</div>
									<div class="field-box selectorField draggableField"
										data-tipo="textarea">
										<label>Campo de texto largo:</label>
										<div class="col-md-7">
											<textarea class="form-control" rows="4"></textarea>
											<p class="help-block">Texto de Ayuda</p>
										</div>

									</div>
								</div>
								<div class="tab-pane" id="opciones">

									<div class="field-box selectorField draggableField"
										data-tipo="checkboxgroup">
										<label>Checks:</label>
										<div class="col-md-7 ctrl-checkboxgroup">
											<label class="chbox checkbox-inline">
												<div class="checker" id="uniform-inlineCheckbox1">
													<span><input type="checkbox" id="inlineCheckbox1"
														value="opcion1"></span>
												</div> Opción 1
											</label> <label class="chbox checkbox-inline">
												<div class="checker" id="uniform-inlineCheckbox2">
													<span><input type="checkbox" id="inlineCheckbox2"
														value="opcion2"></span>
												</div> Opción 2
											</label>
											<p class="help-block">Texto de Ayuda</p>
										</div>
									</div>

									<div class="field-box selectorField draggableField"
										data-tipo="combobox">
										<label>Listado:</label>
										<div class="col-md-7">
											<div class="ui-select">
												<select>
													<option selected="">Opción 1</option>
													<option>Opción 2</option>
												</select>
											</div>
											<p class="help-block">Texto de Ayuda</p>
										</div>
									</div>

									<div class="field-box selectorField draggableField"
										data-tipo="radiogroup">
										<label>Opciones:</label>
										<div class="col-md-8 ctrl-radiogroup">
											<label class="radio">
												<div class="radio" id="uniform-optionsRadios1">
													<span class="checked"><input type="radio" value="option1"
														checked=""></span>
												</div> Opción 1
											</label> <label class="radio">
												<div class="radio" id="uniform-optionsRadios2">
													<span><input type="radio" value="option2"></span>
												</div> Opción 2
											</label>
											<p class="help-block">Texto de Ayuda</p>
										</div>

									</div>
								</div>
								<div class="tab-pane" id="archivos">

									<div class="field-box selectorField draggableField"
										data-tipo="descarga">
										<div class="alert alert-info">
											<i class="icon-download-alt"></i> <a class="descarga"
												href="#"> Texto de descarga</a>
										</div>
									</div>

									<div class="field-box selectorField draggableField"
										data-tipo="carga">
										<label>Campo de carga:</label>
										<div class="col-md-7">
											<input class="file" type="file" title="Texto de carga">
											<p class="help-block">Texto de Ayuda</p>
										</div>

									</div>

								</div>
								<div class="tab-pane" id="otros">

									<div class="field-box selectorField draggableField"
										data-tipo="separador">
										<h4>Separador</h4>
										<hr>
									</div>

									<div class="field-box selectorField draggableField"
										data-tipo="grupo">
										<h4 style="color: green;">
											<i class="icon-sitemap"></i><span>Grupo de datos
												(evaluaciones)</span>
										</h4>
										<hr>
									</div>

									<div class="field-box selectorField draggableField"
										data-tipo="date">
										<label>Campo de Fecha:</label>
										<div class="col-md-7">
											<input type="text" value="dd/mm/aaaa"
												class="form-control input-datepicker">
											<p class="help-block">Texto de Ayuda</p>
										</div>
									</div>

								</div>
							</form>
						</div>
					</div>

					<div class="wizard-actions">
						<button type="button" class="btn-glow primary esBorrador">Guardar
							como borrador</button>
						<button type="submit" class="btn-glow success">Publicar</button>
					</div>

			</form>
		</div>

		<!-- right column -->

	</div>
</div>

</div>


<script type="text/javascript">
	$(function () {


			   	docReady();

			   	var formsInscripcion = <?= $ret_inscripcion; ?>;

			 	 $('select[name="vincular"]').css('width','400px').append('<option value="0">Ninguno</option>');
			 	$.each(formsInscripcion,function(index,value){
				  $('select[name="vincular"]').append('<option value="' + value.id+ '">'+value.titulo +'('+ value.fechainicio + ' - ' + value.fechafin +')'+'</option>');
				});

			 	$('select[name="vincular"]').select2()


			   //	 $('#sticky').stickyfloat( {duration: 400,lockBottom:true} );
	            // add uniform plugin styles to html elements
	            $("input:checkbox, input:radio").uniform();

	            $('.nav-tabs a').click(function (e) {
	                e.preventDefault();
	                $(this).tab('show');
	            })

	            	$("#copiarformulario").select2({
		                placeholder: "Selecciona un formulario",
		            });

	            	/*ABONO*/
	            	 $('#fechaabonoinicio,#fechaabonofin,#fechacomienzocurso').datetimepicker({
				    	locale:'es',
				 		format: 'YYYY-MM-DD'
				    });

	            	 $('#tipoformulario').change();

	                 $('#sedebeabonar').change();

	            	  $("#fechaabonoinicio").on("dp.change",function (e) {
			            //$('#fechafin').data("DateTimePicker").minDate(e.date);
			            $(this).datetimepicker('hide');
					 	$('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
			        });

			         $("#fechaabonofin").on("dp.change",function (e) {
			           // $('#fechainicio').data("DateTimePicker").maxDate(e.date);
			            $(this).datetimepicker('hide');
				 		$('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
			        });
			        /*ABONO*/

				    $('#fechainicio,#fechafin').datetimepicker({
				    	locale:'es',
				 		format: 'YYYY-MM-DD HH:mm:ss'
				    });


		            $("#fechacomienzocurso").on("dp.change",function (e) {
		                // $('#fechafin').data("DateTimePicker").minDate(e.date);
		                 $(this).datetimepicker('hide');
		                 $('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
		             });

			       $("#fechainicio").on("dp.change",function (e) {
			            //$('#fechafin').data("DateTimePicker").minDate(e.date);
			            $(this).datetimepicker('hide');
					 	$('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
			        });

			        $("#fechafin").on("dp.change",function (e) {
			           // $('#fechainicio').data("DateTimePicker").maxDate(e.date);
			            $(this).datetimepicker('hide');
				 		$('#formulario1').bootstrapValidator('revalidateField', $(this).attr('id'));
			        });


				 	$('#habilitarpersonas').tokenfield({
						delimiter: [';',','],
						inputType:'email'
					}).on('tokenfield:createdtoken', function (e){
					    // Über-simplistic e-mail validation
					    var re = /\S+@\S+\.\S+/
					    var valid = re.test(e.attrs.value)
					    if (!valid) {
					      $(e.relatedTarget).addClass('invalid')
					       $('#habilitarpersonascantidadmal').html( parseInt($('#habilitarpersonascantidadmal').text()) + 1);
					       habilitarpersonasmal++;

						   if (!$('#habilitarpersonas').parent().parent().parent().hasClass('has-error')){
							$('#habilitarpersonas').parent().parent().parent().removeClass('has-success').addClass('has-error');
							$('#habilitarpersonas').parent().parent().parent().find('div:first').append('<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-remove" data-bv-icon-for="titulo" style="display: block;"></i>\
							 <small class="help-block error" data-bv-validator="notEmpty" data-bv-for="titulo" data-bv-result="INVALID" style="">Existen E-mails inválidos</small>');
						   }

					    }else{
						   $('#habilitarpersonascantidad').html( parseInt($('#habilitarpersonascantidad').text()) + 1);

						  if(habilitarpersonasmal == 0){
								 if (!$('#habilitarpersonas').parent().parent().parent().hasClass('has-success')){
									$('#habilitarpersonas').parent().parent().parent().removeClass('has-error').addClass('has-success');
									$('#habilitarpersonas').parent().parent().parent().find('.help-block.error').remove();
									$('#habilitarpersonas').parent().parent().parent().find('.form-control-feedback').remove();
									$('#habilitarpersonas').parent().parent().parent().find('div:first').append('<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-ok" data-bv-icon-for="fechafin" style="display: block;"></i>');
								 }
							}
					    }
					  }).on('tokenfield:edittoken', function (e) {
					    // Über-simplistic e-mail validation
					    var re = /\S+@\S+\.\S+/
					    var valid = re.test(e.attrs.value)
					    if (!valid) {
					      $(e.relatedTarget).addClass('invalid')
					       $('#habilitarpersonascantidadmal').html( parseInt($('#habilitarpersonascantidadmal').text()) - 1);
					        habilitarpersonasmal--;
							if(habilitarpersonasmal == 0){
								 if (!$('#habilitarpersonas').parent().parent().parent().hasClass('has-success')){
									$('#habilitarpersonas').parent().parent().parent().removeClass('has-error').addClass('has-success');
									$('#habilitarpersonas').parent().parent().parent().find('.help-block.error').remove();
									$('#habilitarpersonas').parent().parent().parent().find('.form-control-feedback').remove();
									$('#habilitarpersonas').parent().parent().parent().find('div:first').append('<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-ok" data-bv-icon-for="fechafin" style="display: block;"></i>');
								 }
							}

					    }else{
						   $('#habilitarpersonascantidad').html( parseInt($('#habilitarpersonascantidad').text()) - 1);
					    }
					  }).on('tokenfield:removedtoken', function (e) {
					     // Über-simplistic e-mail validation
					    var re = /\S+@\S+\.\S+/
					    var valid = re.test(e.attrs.value)
					    if (!valid) {
					      $(e.relatedTarget).addClass('invalid')
					       $('#habilitarpersonascantidadmal').html( parseInt($('#habilitarpersonascantidadmal').text()) - 1);
					        habilitarpersonasmal--;

							if(habilitarpersonasmal == 0){
								 if (!$('#habilitarpersonas').parent().parent().parent().hasClass('has-success')){
									$('#habilitarpersonas').parent().parent().parent().removeClass('has-error').addClass('has-success');
									$('#habilitarpersonas').parent().parent().parent().find('.help-block.error').remove();
									$('#habilitarpersonas').parent().parent().parent().find('.form-control-feedback').remove();
									$('#habilitarpersonas').parent().parent().parent().find('div:first').append('<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-ok" data-bv-icon-for="fechafin" style="display: block;"></i>');
								 }
							}

					    }else{
						   $('#habilitarpersonascantidad').html( parseInt($('#habilitarpersonascantidad').text()) - 1);
					    }
					  });

				});
</script>

<script>

	var _ctrl_index;

	/* Make the control draggable */
	function makeDraggable() {
		$(".selectorField").draggable({ helper: "clone",cursor: "pointer", cancel: null  });
	}


	function crearIndex(indexes){
		//recorremos el array de indexes agregados, para obtener el más alto
		_ctrl_index = 1000;

		$.each(indexes,function(i,o){


			var partes  = o.split('-');

			if (partes[1] > _ctrl_index)
				_ctrl_index = partes[1];
		});

		_ctrl_index++;

	}



	function docReady() {

		compileTemplates();

		makeDraggable();

		crearIndex([]); //Creamos un index

		$( ".droppedFields" ).droppable({
			  activeClass: "activeDroppable",
			  hoverClass: "hoverDroppable",
			  accept: ":not(.ui-sortable-helper)",
			  drop: function( event, ui ) {
				//console.log(event, ui);
				var draggable = ui.draggable;
				draggable = draggable.clone();
				draggable.removeClass("selectorField");
				draggable.addClass("droppedField");
				draggable[0].id = "CAMPO-"+(_ctrl_index++); // Attach an ID to the rendered control
				draggable.appendTo(this);
				/* Once dropped, attach the customization handler to the control */
				draggable.unbind("click").click(function (e) {
										e.preventDefault();
										// The following assumes that dropped fields will have a ctrl-defined.
										//   If not required, code needs to handle exceptions here.
										var me = $(this)
										var ctrl = me.attr('data-tipo');
										customize_ctrl(ctrl, this.id);

										//window["customize_"+ctrl_type](this.id);
								});
				draggable.attr('modificado','0');
				draggable.attr('dependencia','NULL');
				draggable.attr('dependencia-valor','NULL');
				makeDraggable();
			}
		});
		/* Make the droppedFields sortable and connected with other droppedFields containers*/
		$( ".droppedFields" ).sortable({
										cancel: null, // Cancel the default events on the controls
										connectWith: ".droppedFields"
									}).disableSelection();



	}


	if(typeof(console)=='undefined' || console==null) { console={}; console.log=function(){}}

	/* Delete the control from the form */
	function delete_ctrl() {
		if(window.confirm("Desea borrar este campo?")) {
			var ctrl_id = $("#theForm").find("[name=forCtrl]").val()


			$('#formulario [dependencia="'+ctrl_id+'"]').attr('dependencia','NULL');

			$("#"+ctrl_id).remove();
		}
	}

	/* Compile the templates for use */
	function compileTemplates() {
		window.templates = {};
		window.templates.common = Handlebars.compile($("#control-customize-template").html());

		/* HTML Templates required for specific implementations mentioned below */

		// Mostly we donot need so many templates

   window.templates.textbox = Handlebars.compile($("#textbox-template").html());
    window.templates.textarea = Handlebars.compile($("#textarea-template").html());
    window.templates.date = Handlebars.compile($("#date-template").html());

    window.templates.combobox = Handlebars.compile($("#combobox-template").html());
    window.templates.selectmultiplelist = Handlebars.compile($("#combobox-template").html());
    window.templates.radiogroup = Handlebars.compile($("#radiogroup-template").html());
    window.templates.checkboxgroup = Handlebars.compile($("#checkbox-template").html());
    window.templates.separador = Handlebars.compile($("#separador-template").html());
    window.templates.grupo = Handlebars.compile($("#grupo-template").html());
    window.templates.descarga = Handlebars.compile($("#descarga-template").html());
    window.templates.carga = Handlebars.compile($("#carga-template").html());

	}

	// Object containing specific "Save Changes" method
	save_changes = {};

	// Object comaining specific "Load Values" method.
	load_values = {};


	/* Common method for all controls with Label and Name */
	load_values.common = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("select[name=dependencia] option[value='"+div_ctrl.attr('dependencia')+"']").attr('selected','selected');

		var specific_load_method = load_values[ctrl_type];
		if(typeof(specific_load_method)!='undefined') {
			specific_load_method(ctrl_type, ctrl_id);
		}


		if (div_ctrl.attr('dependencia-valor') !== undefined){

			if (form.find('select[name=dependencia] option:selected').attr('data-igual') !== undefined){

				var opcion = '';
				$.each(JSON.parse($(form).find('select[name=dependencia] option:selected').attr('data-igual')),function(i,o){
					var selected = '';

					 if (div_ctrl.attr('dependencia-valor').trim() == o.trim())
				            selected=' selected ';

					opcion+='<option '+selected+' value="'+o+'">'+o+'</option>';

				});

				form.find('select[name=dependencia]').parent().after('<label style="float:none;width:100%;display:block;overflow:hidden;" class="iguala" for="iguala">Valor igual a:</label><div class="iguala ui-select"><select name="iguala">'+opcion+'</select></div>');
			}
		}


		form.find('select[name="dependencia"]').change(function(){


			if ($(this).find('option:selected').attr('data-igual') !== undefined){

				var opcion = '';
				$.each(JSON.parse($(this).find('option:selected').attr('data-igual')),function(i,o){

					opcion+='<option value="'+o+'">'+o+'</option>';


				});


				$(this).parent().after('<label style="float:none;width:100%;display:block;overflow:hidden;" class="iguala" for="iguala">Valor igual a:</label><div class="iguala ui-select"><select name="iguala">'+opcion+'</select></div>');

			}else{

				form.find('.iguala').remove();
			}

		});

		 form.find('textarea[name="options"]').keyup(function(){

    	 form.find("[name=computabeca] option[value!=\"\"]").each(function(){

         	 $(this).remove();

    	 });

    	 $('#computabeca').select2('data', null);

    	$($(this).val().split('\n')).each(function(i,o) {

    	      var item = $.trim(o);

    	      if (item != ''){

    	    	  form.find("[name=computabeca]").append('<option  value="'+item+'">'+item+'</option>');
    	      }

    	 });

    });

	}


	/* Specific method to load values from a textbox control to the customization dialog */
	load_values.separador = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=titulo]").val(div_ctrl.find('h4').text());

	}

	load_values.grupo = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=titulo]").val(div_ctrl.find('h4').find('span').text());

	}

	load_values.descarga = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=texto]").val(div_ctrl.find('a').text());
		form.find("[name=link]").val(div_ctrl.find('a').attr('href'));
	}

	load_values.carga = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=label]").val(div_ctrl.find('label:first').text());
		form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

		var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

		if (esobligatorio)
			form.find("[name=obligatorio]").attr('checked','checked');
		else
			form.find("[name=obligatorio]").removeAttr('checked');

	}

	/* Specific method to load values from a textbox control to the customization dialog */
	load_values.textbox = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=label]").val(div_ctrl.find('label:first').text());
		form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

		var ctrl = div_ctrl.find("input")[0];

		var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

		if (esobligatorio)
			form.find("[name=obligatorio]").attr('checked','checked');
		else
			form.find("[name=obligatorio]").removeAttr('checked');


		if (div_ctrl.attr('opciones') !== undefined) {
			var opciones = JSON.parse(div_ctrl.attr('opciones'));

			if ((opciones.validacion !== undefined) && (opciones.validacion !== null)) {
				$.each(opciones.validacion,function(i,o){

					form.find('[name="validacion"] option[value="'+o+'"]').attr("selected","selected");

				});
			}

			if (opciones.autorellenar !== undefined) {

					form.find('[name="autorellenar"] option[value="'+opciones.autorellenar+'"]').attr("selected","selected");

			}

			if (opciones.mostrarenlistado !== undefined) {

					if (opciones.mostrarenlistado == true )
						form.find("[name=mostrarenlistado]").attr('checked','checked');
					else
						form.find("[name=mostrarenlistado]").removeAttr('checked');

			}

		}

		$("#validacion, #autorellenar").select2({

		});

	}

	load_values.date = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=label]").val(div_ctrl.find('label:first').text());
		form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

		var ctrl = div_ctrl.find("input")[0];

		var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

		if (esobligatorio)
			form.find("[name=obligatorio]").attr('checked','checked');
		else
			form.find("[name=obligatorio]").removeAttr('checked');

		if (div_ctrl.attr('opciones') !== undefined) {
			var opciones = JSON.parse(div_ctrl.attr('opciones'));

			if (opciones.autorellenar !== undefined) {

					form.find('[name="autorellenar"] option[value="'+opciones.autorellenar+'"]').attr("selected","selected");

			}

		}

		$("#validacion, #autorellenar").select2({ });

	}


	/* Specific method to load values from a textbox control to the customization dialog */
	load_values.textarea = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);

		form.find("[name=label]").val(div_ctrl.find('label:first').text());
		form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

		var ctrl = div_ctrl.find("textarea")[0];

		var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

		if (esobligatorio)
			form.find("[name=obligatorio]").attr('checked','checked');
		else
			form.find("[name=obligatorio]").removeAttr('checked');
	}


	/* Specific method to load values from a combobox control to the customization dialog  */
	 load_values.combobox = function(ctrl_type, ctrl_id) {
    var form = $("#theForm");
    var div_ctrl = $("#"+ctrl_id);

    form.find("[name=label]").val(div_ctrl.find('label:first').text());
    form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

    var ctrls = $(div_ctrl.find("select")[0]).find('option');

    var options= '';
    var opcionseleccionada='';
    if (div_ctrl.attr('opciones') !== undefined) {

    	var opciones = JSON.parse(div_ctrl.attr('opciones'));

        if ((opciones.computabeca !== undefined) && (opciones.computabeca !== null)) {

				opcionseleccionada = opciones.computabeca[0];//Siempre va a haber una sola opción en los radiogroup

        }
    }


    ctrls.each(function(i,o) {

		var val = $(o).text().trim();

     	options+=val+'\n';

		var selected = '';
		if (val == opcionseleccionada)
     		selected = 'selected = "selected"';

     	form.find("[name=computabeca]").append('<option '+selected+' value="'+val+'">'+val+'</option>');
    });


    form.find("[name=options]").val($.trim(options));

    var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

    if (esobligatorio)
      form.find("[name=obligatorio]").attr('checked','checked');
    else
      form.find("[name=obligatorio]").removeAttr('checked');
  }
	// Multi-select combobox has same customization features
	load_values.selectmultiplelist = load_values.combobox;


	/* Specific method to load values from a radio group */
	load_values.radiogroup = function(ctrl_type, ctrl_id) {
    var form = $("#theForm");
    var div_ctrl = $("#"+ctrl_id);

    form.find("[name=label]").val(div_ctrl.find('label:first').text());
    form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

    var options= '';
    var ctrls = div_ctrl.find(".ctrl-radiogroup").find("label");
    var radios = div_ctrl.find(".ctrl-radiogroup").find("input");

    var opcionseleccionada='';
    if (div_ctrl.attr('opciones') !== undefined) {

    	var opciones = JSON.parse(div_ctrl.attr('opciones'));

        if ((opciones.computabeca !== undefined) && (opciones.computabeca !== null)) {

				opcionseleccionada = opciones.computabeca[0];//Siempre va a haber una sola opción en los radiogroup

        }
    }


    ctrls.each(function(i,o) {

		var val = $(o).text().trim();

     	options+=val+'\n';

		var selected = '';
		if (val == opcionseleccionada)
     		selected = 'selected = "selected"';

     	form.find("[name=computabeca]").append('<option '+selected+' value="'+val+'">'+val+'</option>');
    });





    form.find("[name=options]").val($.trim(options));

    var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

    if (esobligatorio)
      form.find("[name=obligatorio]").attr('checked','checked');
    else
      form.find("[name=obligatorio]").removeAttr('checked');
  }


	load_values.checkboxgroup = function(ctrl_type, ctrl_id) {
    var form = $("#theForm");
    var div_ctrl = $("#"+ctrl_id);

    form.find("[name=label]").val(div_ctrl.find('label:first').text());
    form.find("[name=ayuda]").val(div_ctrl.find('.help-block').text());

    var options= '';
    var ctrls = div_ctrl.find(".ctrl-checkboxgroup").find("label");
    var checkbox = div_ctrl.find(".ctrl-checkboxgroup").find("input");

     var opcionseleccionada = [];

     if (div_ctrl.attr('opciones') !== undefined) {

    	var opciones = JSON.parse(div_ctrl.attr('opciones'));

        if ((opciones.computabeca !== undefined) && (opciones.computabeca !== null)) {

				opcionseleccionada = opciones.computabeca;//Siempre va a haber una sola opción en los radiogroup

        }
    }



    ctrls.each(function(i,o) {

		var val = $(o).text().trim();

     	options+=val+'\n';

		var selected = '';


		if ($.inArray(val,opcionseleccionada) != -1)
     		selected = 'selected = "selected"';

     	form.find("[name=computabeca]").append('<option '+selected+' value="'+val+'">'+val+'</option>');
    });

    form.find("[name=options]").val($.trim(options));

    var esobligatorio = div_ctrl.find('label i').length; //SI es obligatorio

    if (esobligatorio)
      form.find("[name=obligatorio]").attr('checked','checked');
    else
      form.find("[name=obligatorio]").removeAttr('checked');


    $("#computabeca").select2();
  }

	/* Specific method to load values from a button */
	load_values.btn = function(ctrl_type, ctrl_id) {
		var form = $("#theForm");
		var div_ctrl = $("#"+ctrl_id);
		var ctrl = div_ctrl.find("button")[0];

		form.find("[name=label]").val($(ctrl).text().trim())
	}

	/* Common method to save changes to a control  - This also calls the specific methods */

	save_changes.common = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.attr('dependencia',values.dependencia);

		div_ctrl.attr('modificado','1');

		div_ctrl.attr('dependencia-valor',values.iguala);

		div_ctrl.removeClass('modificar-error');
		var specific_save_method = save_changes[values.type];
		if(typeof(specific_save_method)!='undefined') {
			specific_save_method(values);
		}
	}

	save_changes.separador = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		var ctrl = div_ctrl.find("h4");

		div_ctrl.attr('opciones',JSON.stringify({

			'titulo':values.titulo

		}));



		ctrl.html(values.titulo);
		//console.log(values);
	}

	save_changes.grupo = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		var ctrl = div_ctrl.find("h4").find('span');

		div_ctrl.attr('opciones',JSON.stringify({

			'titulo':values.titulo

		}));



		ctrl.html(values.titulo);
		//console.log(values);
	}


	save_changes.descarga = function(values) {
		var div_ctrl = $("#"+values.forCtrl);
		div_ctrl.find('.descarga').text(values.texto).attr('href',values.link);

		div_ctrl.attr('opciones',JSON.stringify({

			'texto':values.texto,
			'link':values.link
		}));
	}

	save_changes.carga = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		var ctrl = div_ctrl.find("input")[0];


		div_ctrl.attr('opciones',JSON.stringify({

			'label':values.label,
			'ayuda':values.ayuda,
			'obligatorio':values.obligatorio

		}));
		//console.log(values);
	}

	/* Specific method to save changes to a text box */
	save_changes.textbox = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		var ctrl = div_ctrl.find("input")[0];


		div_ctrl.attr('opciones',JSON.stringify({

			'label':values.label,
			'ayuda':values.ayuda,
			'obligatorio':values.obligatorio,
			'validacion':values.validacion,
			'autorellenar':values.autorellenar,
			'mostrarenlistado':values.mostrarenlistado
		}));

		//console.log(values);
	}

	save_changes.date = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		var ctrl = div_ctrl.find("input")[0];


		div_ctrl.attr('opciones',JSON.stringify({

			'label':values.label,
			'ayuda':values.ayuda,
			'obligatorio':values.obligatorio,
			'autorellenar':values.autorellenar

		}));

		//console.log(values);
	}


	save_changes.textarea = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		var ctrl = div_ctrl.find("textarea")[0];


		div_ctrl.attr('opciones',JSON.stringify({

			'label':values.label,
			'ayuda':values.ayuda,
			'obligatorio':values.obligatorio

		}));
		//console.log(values);
	}

	/* Specific method to save changes to a combobox */
	save_changes.combobox = function(values) {
		console.log(values);
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		var ctrl = div_ctrl.find("select")[0];

		$(ctrl).empty();

		var elementos = [];
		$(values.options.split('\n')).each(function(i,o) {

			var item = $.trim(o);

			if (item != ''){
				elementos.push(item);
				$(ctrl).append("<option>"+item+"</option>");
			}

		});


		div_ctrl.attr('opciones',JSON.stringify({

			'label':values.label,
			'ayuda':values.ayuda,
			'obligatorio':values.obligatorio,
			'computabeca':[values.computabeca],
			'opciones':elementos
		}));


	}

	/* Specific method to save a radiogroup */
	save_changes.radiogroup = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var label_template = $(".selectorField .ctrl-radiogroup").find("label:first");
		var radio_template = $(".selectorField .ctrl-radiogroup").find("input:first");

		var ctrl = div_ctrl.find(".ctrl-radiogroup");
		var ayuda = ctrl.find('.help-block').clone();

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		ctrl.empty();

		var elementos = [];
		$(values.options.split('\n')).each(function(i,o) {

			var item = $.trim(o);

			if (item != ''){
				elementos.push(item);
				var label = $(label_template).clone();
				var radio = $(radio_template).clone();
				radio[0].name = values.name;
				radio.val(item);
				label.find('input').after(radio).remove();
				/*Removemos solo el texto*/
				label.contents().filter(function(){
					    return this.nodeType === 3;
				}).remove();
				label.append(item);

				$(ctrl).append(label);
			}
		});
		ctrl.find('span').removeClass('checked');
		ctrl.find('input').attr('checked','');

		ctrl.find('label.radio:first').find('span').addClass('checked');
		ctrl.find('label.radio:first').find('input').attr('checked','checked');
		ctrl.append(ayuda);

		div_ctrl.attr('opciones',JSON.stringify({

      			'label':values.label,
      			'ayuda':values.ayuda,
      			'obligatorio':values.obligatorio,
      			'computabeca':[values.computabeca],
      			'opciones':elementos
		}));
	}

	/* Same as radio group, but separated for simplicity */
	save_changes.checkboxgroup = function(values) {
		var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var label_template = $(".selectorField .ctrl-checkboxgroup").find("label:first");
		var checkbox_template = $(".selectorField .ctrl-checkboxgroup").find("input:first");

		var ctrl = div_ctrl.find(".ctrl-checkboxgroup");
		var ayuda = ctrl.find('.help-block').clone();

		var esobligatorio = values.obligatorio; //SI es obligatorio

		if (esobligatorio)
			div_ctrl.find('label').append('<i class="form-control-feedback glyphicon glyphicon-asterisk"></i>');
		else
			div_ctrl.find('label i').remove();

		ctrl.empty();
		var elementos = [];
		$(values.options.split('\n')).each(function(i,o) {

			var item = $.trim(o);

			if (item != ''){
				elementos.push(item);
				var label = $(label_template).clone();
				var checkbox = $(checkbox_template).clone();
				checkbox.val(item);
				label.find('input').after(checkbox).remove();
				/*Removemos solo el texto*/
				label.contents().filter(function(){
					    return this.nodeType === 3;
				}).remove();
				label.append(item);

				$(ctrl).append(label);
			}


		});

		ctrl.find('span').removeClass('checked');
		ctrl.find('input').attr('checked','');
		ctrl.append(ayuda);

		div_ctrl.attr('opciones',JSON.stringify({

      'label':values.label,
      'ayuda':values.ayuda,
      'obligatorio':values.obligatorio,
      'computabeca':values.computabeca,
      'opciones':elementos
    }));
  }

  // Multi-select customization behaves same as combobox
  save_changes.selectmultiplelist = save_changes.combobox;

  /* Specific method for Button */
  save_changes.btn = function(values) {
    var div_ctrl = $("#"+values.forCtrl);

		div_ctrl.find('label').text(values.label);
		div_ctrl.find('.help-block').text(values.ayuda);

		var ctrl = div_ctrl.find("button")[0];
		$(ctrl).html($(ctrl).html().replace($(ctrl).text()," "+$.trim(values.label)));

		//console.log(values);
	}

	/* Save the changes due to customization
		- This method collects the values and passes it to the save_changes.methods
	*/
	function save_customize_changes(e, obj) {
		//console.log('save clicked', arguments);
		var formValues = {};
		var val=null;
		$("#theForm").find("input, textarea,select").each(function(i,o) {

			formValues['iguala'] = 'NULL';

			if(o.type=="checkbox"){
				val = o.checked;
			}else if(o.type == "select-one"){

				val = $(this).find('option:selected').val();
				if (val == '')
					val = 'NULL';

			}else if(o.type == "select-multiple"){

				val = $(this).val();
				if ((val == null) || (val == ""))
					val = [];
			}else{

				val = o.value;
			}

			formValues[o.name] = val;
		});
		save_changes.common(formValues);
	}

	/*
		Opens the customization window for this
	*/
	function customize_ctrl(ctrl_type, ctrl_id) {
		var ctrl_params = {};
		/* Load the specific templates */
		var specific_template = templates[ctrl_type];

		if(typeof(specific_template)=='undefined') {
			specific_template = function(){console.log('indefinido');return ''; };
		}

		 if (ctrl_type == 'grupo'){

        var modal_header = $("#"+ctrl_id).find('h4').find('span').text();

   		 }else if (ctrl_type == 'separador') {

				var modal_header = $("#"+ctrl_id).find('h4').text();

		}else{
				var modal_header = $("#"+ctrl_id).find('label:first').text();
		}

		var dependencia='';
		$('#formulario .droppedField[data-tipo="checkboxgroup"][id!='+ctrl_id+'][modificado="1"]').each(function(){

			var label = $(this).find('label:first').text();
			var id = $(this).attr('id');

			dependencia += '<option value="'+id+'">'+label+'</option>';

		});

		$('#formulario .droppedField[data-tipo="grupo"][id!='+ctrl_id+'][modificado="1"]').each(function(){

			var label = $(this).find('h4').find('span').text();
			var id = $(this).attr('id');

			dependencia += '<option style="color:green;"value="'+id+'">'+label+'</option>';

		});

		$('#formulario .droppedField[data-tipo="combobox"][id!='+ctrl_id+'][modificado="1"]').each(function(){

			var label = $(this).find('label:first').text();
			var id = $(this).attr('id');

			var texto = [];

			var opciones = JSON.parse($(this).attr('opciones'));

			dependencia += '<option data-igual=\''+JSON.stringify(opciones.opciones)+'\' value="'+id+'">'+label+'</option>';

		});

		var template_params = {
			dependencia:dependencia,
			header:modal_header,
			content: specific_template(ctrl_params),
			type: ctrl_type,
			forCtrl: ctrl_id
		}

		// Pass the parameters - along with the specific template content to the Base template
		var s = templates.common(template_params)+"";


		$("[name=customization_modal]").remove(); // Making sure that we just have one instance of the modal opened and not leaking
		$('<div id="customization_modal" name="customization_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" />').append(s).modal('show');


		setTimeout(function() {
			// For some error in the code  modal show event is not firing - applying a manual delay before load
			load_values.common(ctrl_type, ctrl_id);
		},300);
	}
</script>

<!-- using handlebars for templating, but DustJS might be better for the current purpose -->
<script type="text/javascript"
	src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/1.0.0-rc.3/handlebars.min.js"></script>

<!--
  Starting templates declaration
  DEV-NOTE: Keeping the templates and code simple here for demo use some better template inheritance for multiple controls
-->
<script id="control-customize-template" type="text/x-handlebars-template">
	<div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
	    <h3>{{header}}</h3>
	  </div>
	  <div class="modal-body form-wrapper">

	    <form id="theForm">
	      <input type="hidden" value="{{type}}" name="type"></input>
	      <input type="hidden" value="{{forCtrl}}" name="forCtrl"></input>

	      {{{content}}}
	      <div class="form-group">
	            <label for="label" >Depende de:</label>
	            <div class="col-md-7">
	                <div class="ui-select">
	                	<select name="dependencia">
	                		<option value="">Ninguno</option>
	                    	{{{dependencia}}}
	                	</select>
	               </div>

	            </div>
	       </div>
	    </form>

	  </div>
	  <div class="modal-footer">
	    <button class="btn btn-primary" data-dismiss="modal" onclick='save_customize_changes()'>Guardar</button>
	    <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
	    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" onclick='delete_ctrl()'>Borrar</button>
	  </div>
	  </div>
	</div>
</script>

<script id="descarga-template" type="text/x-handlebars-template">
    <div class="form-group">
            <label for="label" >Texto de descarga:</label>
            <div class="col-md-7">
                <input name="texto" class="form-control">
            </div>
         </div>
         <div class="form-group">
            <label for="label" >Link:</label>
            <div class="col-md-7">
                <input name="link" class="form-control">
            </div>
         </div>

</script>

<script id="separador-template" type="text/x-handlebars-template">
              <div class="form-group">
                            <label for="titulo" >Título:</label>
                            <div class="col-md-7">
                                <input name="titulo" class="form-control">
                            </div>
                             </div>
</script>

<script id="grupo-template" type="text/x-handlebars-template">
              <div class="form-group">
                            <label for="titulo" >Título:</label>
                            <div class="col-md-7">
                                <input name="titulo" class="form-control">
                            </div>
                             </div>
</script>

<script id="carga-template" type="text/x-handlebars-template">
      <div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>

             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
          <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>
</script>

<script id="textbox-template" type="text/x-handlebars-template">
      <div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>
            <div class="form-group">
                            <label>Validar datos:</label>
                            <div class="col-md-7">
                              <select  id="validacion" name="validacion" style="width:250px" multiple class="select2">

                                <option value="numeric">Numérico</option>
                                <option value="valid_email">Email Válido</option>


                              </select>
                            </div>
             </div>
             <div class="form-group">
                            <label>Auto-rellenar con:</label>
                            <div class="col-md-7">
                              <select  id="autorellenar" name="autorellenar" style="width:250px"  class="select2">

                  				<option value="NULL">- Ninguno -</option>
                                <?php
									foreach ( $this->config->item ( 'campos_autorelleno' ) as $key => $dato ) {

										if ($dato ['disponible'] == 'textbox')
											echo '<option value="' . $key . '">' . $dato ['texto'] . '</option>';
									}
								?>

                              </select>
                            </div>
             </div>

             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
           <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>

          <div class="form-group">
            <label for="label" >Mostrar en listado:</label>
            <div class="col-md-7">
                <input name="mostrarenlistado" type="checkbox" class="checkbox">
            </div>
           </div>
</script>

<script id="textarea-template" type="text/x-handlebars-template">
      <div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>

             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
           <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>
</script>
<script id="date-template" type="text/x-handlebars-template">
      <div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>
             <div class="form-group">
                            <label>Auto-rellenar con:</label>
                            <div class="col-md-7">
                              <select  id="autorellenar" name="autorellenar" style="width:250px"  class=" select2">
                    <option value="NULL">- Ninguno -</option>
                                <?php

									foreach ( $this->config->item ( 'campos_autorelleno' ) as $key => $dato ) {

										if ($dato ['disponible'] == 'date')
											echo '<option value="' . $key . '">' . $dato ['texto'] . '</option>';
									}

								?>

                              </select>
                            </div>
             </div>
             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
           <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>
</script>

<script id="radiogroup-template" type="text/x-handlebars-template">
<div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>

             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
 				<div class="form-group">
                            <label for="options">Opciones</label>
                            <div class="col-md-7">
                                <textarea name="options" rows="5" class="form-control"></textarea>
                            </div>
               </div>

				 <div class="form-group">
           		 <label for="label" >Opción que computa para beca:</label>
           			<div class="col-md-7">
                		<div class="ui-select">
                			<select name="computabeca">
                				<option value="">Ninguno</option>

                			</select>
               			</div>
                   	</div>
       			</div>

           <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>
</script>

<script id="checkbox-template" type="text/x-handlebars-template">
<div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>

             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
 				<div class="form-group">
                            <label for="options">Opciones</label>
                            <div class="col-md-7">
                                <textarea name="options" rows="5" class="form-control"></textarea>
                            </div>
               </div>

				 <div class="form-group">
           		 <label for="label" >Opción que computa para beca:</label>
           			<div class="col-md-7">

                			<select name="computabeca" id="computabeca" style="width:250px" multiple class="select2">


                			</select>

                   	</div>
       			</div>

           <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>
</script>

<script id="combobox-template" type="text/x-handlebars-template">
<div class="form-group">
                            <label for="label" >Etiqueta:</label>
                            <div class="col-md-7">
                                <input name="label" class="form-control">
                            </div>
                             </div>

             <div class="form-group">
                            <label for="ayuda">Texto de Ayuda:</label>
                            <div class="col-md-7">
                                <input name="ayuda" class="form-control">
                            </div>
               </div>
 				<div class="form-group">
                            <label for="options">Opciones</label>
                            <div class="col-md-7">
                                <textarea name="options" rows="5" class="form-control"></textarea>
                            </div>
               </div>

				 <div class="form-group">
           		 <label for="label" >Opción que computa para beca:</label>
           			<div class="col-md-7">
                		<div class="ui-select">
                			<select name="computabeca">
                				<option value="">Ninguno</option>

                			</select>
               			</div>
                   	</div>
       			</div>

           <div class="form-group">
            <label for="label" >Obligatorio:</label>
            <div class="col-md-7">
                <input name="obligatorio" type="checkbox" class="checkbox">
            </div>

         </div>
</script>
<!-- End of templates -->
