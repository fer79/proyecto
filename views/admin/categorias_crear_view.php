<script type="text/javascript">
var site_url = 'http://dev.localhost/inscripciones';
</script>

<link rel="stylesheet"
	href="/css/compiled/form-showcase.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet"
	href="/css/compiled/form-wizard.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link rel="stylesheet"
	href="/css/compiled/new-user.css"
	type="text/css" media="screen" />
<!-- this page specific styles -->
<link href="/css/lib/bootstrap.datepicker.css"
	type="text/css" rel="stylesheet" />


<link rel="stylesheet"
	href="/css/bootstrapValidator.min.css" />
<script type="text/javascript"
	src="/js/bootstrapValidator.min.js"></script>
<script type="text/javascript"
	src="/js/language/es_ES.js"></script>



<script type="text/javascript">
       $(document).ready(function() {


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
                            padre: $('#formulario1 #padre option:selected').val()
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
				url: site_url+'admin_categorias_ajx/crear',
				type: 'POST',
				dataType: "json",
				data: {'nombre':nombre,'padre':padre},
				success: function(result){
					
					if(result=='not_logged'){
						
						
					
					}else if (result == 'ok'){
					
						$('#formulario1').prepend('<div class="alert alert-success">\
						<i class="icon-ok-sign"></i> Categoría Creada.\
						</div>').fadeIn('slow');
						 
						  $('html,body').animate({
							scrollTop: $(".alert :visible").offset().top-50
						});

						$('#formulario1 .form-control').each(function() {
							 $(this).val('');
							 
						});
						
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



<!-- main container -->
<div class="content">

	<div id="pad-wrapper" class="form-page new-user">
		<div class="row header">
			<h3>Crear Categoria</h3>
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
										class="form-control input-md">
								</div>
							</div>

							<div class="form-group">
								<label>Padre:</label>
								<div class="ui-select">
									<select style="width: 400px" id="padre">
				                            
				                            
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
																		
																		listarCat ( $padres );
																		?>
				                              
				                            </select>
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

<script src="/js/select2.min.js"></script>
<script src="/js/jquery.uniform.min.js"></script>


<!-- call this page plugins -->
<script type="text/javascript">
        $(function () {

            // add uniform plugin styles to html elements
            $("input:checkbox, input:radio").uniform();

        });
    </script>