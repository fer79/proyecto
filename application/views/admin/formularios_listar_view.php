<script type="text/javascript">
var site_url = '<?php echo base_url();?>';
var filtro = 'inscripciones';

<?php if ($this->auth->tieneAcceso('formularios_eliminar',true)){ ?>

function borrarYa(id){

	$.ajax({
		type: "POST",
		url: site_url+"formularios_ajx/borrar",
		dataType:"JSON",
		data: {'id':id},
		success: function(result){

			if(result=='not_logged'){
						return false;
			}else if (result=='ok'){

				$('#id-'+id).fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(400);
					$('tr#id-'+id).remove();
					$('.pagination').trigger('update',1)

				});
			}else{

				$('#pad-wrapper').prepend('<div class="alert alert-error">\
                        <i class="icon-error-sign"></i> Error Desconocido.\
						</div>').fadeIn('slow');


			}
		}
	});


}


function clonarYa(id){

	$.ajax({
		type: "POST",
		url: site_url+"formularios_ajx/clonar",
		dataType:"JSON",
		data: {'id':id},
		success: function(result){

			if(result=='not_logged'){
						return false;
			}else if (result =='ok'){

				$('.pagination').trigger('reload_page');

			}else{

				$('#pad-wrapper').prepend('<div class="alert alert-error">\
                        <i class="icon-error-sign"></i> Error Desconocido.\
						</div>').fadeIn('slow');


			}
		}
	});


}


function borrar(id){
	$('.alert').remove();
	var deleteyes = confirm("Estas seguro?");
	if(deleteyes) {

		borrarYa(id);

	}

}

<?php } ?>
var xhrlistar = null;
var xhrbuscar = null;

function listarFormularios(start,search){

	search = typeof search !== 'undefined' ? search : '';


	xhrlistar = $.ajax({
		url:	site_url+'formularios_ajx/listar',
		type: 	'POST',
		data: {'start':start,'filtro':filtro,'search':search},
		success: function(result){
			if(result=='not_logged'){
				return false;
			}else if (result=='error'){

			}else if (result=='vacio'){

			}else{
				var data = result.listado;
				$('#listado tbody').html('');
				$.each(data, function(i,val) {

					var botonEliminado = '';
					if (filtro != 'eliminado'){

						if (val['permeliminar'] == true)
							botonEliminado = '<a href="#" data-id="'+val.id+'" class="botonborrar"><i class="icon-trash"></i></a>';
					}

					var botonVer = '';
					if (val['permverregistro'] == true)
				    	botonVer = '<a href="'+site_url+'adminpanel/formularios/'+val.id+'/registros"><i class="icon-tasks"></i></a>';

				  var botonModificar = '';
					if (val['permmodificar'] == true)
						botonModificar = '<a href="'+site_url+'adminpanel/formularios/modificar/'+val.id+'" ><i class="icon-pencil"></i></a>';

					var botonEmails = '';
 					if (val['permmodificar'] == true && val.cantEmails > 0)
 						botonEmails = '<a href="'+site_url+'adminpanel/formularios/estado-emails/'+val.id+'" ><i class="icon-envelope"></i></a>';


					var botondesplegareval = '';
					if (data[i].evaluaciones.length != 0 ){
						botondesplegareval = '<a href="#" data-id="'+val.id+'" title="Ver Evaluaciones" class="botondesplegareval"><i style="color: #F2A314;" class="icon-chevron-down"></i></a>';
					}


					$d = $('<tr id="id-'+val.id+'">\
								<td><input type="checkbox" class="seleccionitem" data-id="'+val.id+'">'+val.id+'</td> \
								<td class="hidden-xs hidden-sm">\
									<span style="display:block;">'+val.usuario+'</span>\
									<span class="subtext" style="font-weight: lighter;font-size: 10px;">'+val.nombre+' '+val.apellidos+'</span>\
								</td>\
								<td style="position:relative;">\
									<a style="display:block;width:80%" target="blank_" href="'+val.url+'">'+val.titulo+'</a>\
									<span class="subtext">'+val.url+'</span>\
									<span style="position: absolute;right: 3px;top: 13px;font-size:10px;">'+val.estado+'</span>\
								</td>\
								<td class="hidden-xs hidden-sm">\
									'+val.fechainicio+'\
								</td>\
								<td class="hidden-xs">\
									'+val.fechafin+'\
								</td>\
								<td class="hidden-xs">\
									'+val.tipo+'\
								</td>\
								<td class="hidden-xs hidden-sm">\
									'+val.cantInscriptos+'\
								</td>\
								<td class="align-left">\
									'+botonVer+'\
									'+botonModificar+'\
									'+botonEmails+'\
									'+botonEliminado+'\
									'+botondesplegareval+'</td>\
							</tr>').fadeIn('slow');

					$('#listado tbody').append($d);


					if (data[i].evaluaciones.length != 0 ){

						$.each(data[i].evaluaciones, function(z,val2) {

							var botonEliminado2 = '';

							if (filtro != 'eliminado'){

								if (val2['permeliminar'] == true)
									botonEliminado2 = '<a href="#" data-id="'+val2.id+'" class="botonborrar"><i class="icon-trash"></i></a>';
							}

							var botonVer2 = '';
							if (val2['permverregistro'] == true)
						    	botonVer2 = '<a href="'+site_url+'adminpanel/formularios/'+val2.id+'/registros"><i class="icon-tasks"></i></a>';

						  var botonModificar2 = '';
							if (val2['permmodificar'] == true)
								botonModificar2 = '<a href="'+site_url+'adminpanel/formularios/modificar/'+val2.id+'"><i class="icon-pencil"></i></a>';


							$d2 = $('<tr class="evaluaciones" data-padre="'+val.id+'" style="display:none;" id="id-'+val2.id+'">\
									<td >'+val2.id+'</td> \
										<td class="hidden-xs hidden-md">\
											<span style="display:block;">'+val2.usuario+'</span>\
											<span class="subtext" style="font-weight: lighter;font-size: 10px;">'+val2.nombre+' '+val2.apellidos+'</span>\
										</td>\
									<td style="position:relative;" class="nombre">\
										<a style="display:block;width:80%;" target="blank_" href="'+val2.url+'">'+val2.titulo+'</a>\
										<span class="subtext">'+val2.url+'</span>\
										<span style="position: absolute;right: 3px;top: 4px;font-size:9px;">'+val.estado+'</span>\
									</td>\
									<td class="hidden-xs ">\
										'+data[i].evaluaciones[z].fechainicio+'\
									</td>\
									<td class="hidden-xs ">\
										'+data[i].evaluaciones[z].fechafin+'\
									</td>\
									<td class="hidden-xs hidden-md">\
										'+data[i].evaluaciones[z].tipo+'\
									</td>\
									<td class="hidden-xs hidden-md">\
									'+data[i].evaluaciones[z].cantInscriptos+'\
								</td>\
									<td class="col-sm-1 col-xs-1 align-right">\
									'+botonVer2+'\
									'+botonModificar2+'\
									'+botonEliminado2+'</td>\
								</tr>');

							$('#listado tbody').append($d2);
						});
					}


				});

				$('#listado tbody tr:even').addClass("alt-row");


				$('.search').keyup(function(){

					var search = $(this).val();

					if (xhrbuscar != null)
						xhrbuscar.abort();

					if (xhrlistar != null)
						xhrlistar.abort();

					xhrbuscar = $.ajax({
						url:	site_url+'formularios_ajx/obtenerTotal',
						type: 	'POST',
						data: {'filtro':filtro,'search':search},
						success: function(result){
							$('.pagination').trigger('destroy');
							$('.pagination').paginator({

							label_first : '««',
							label_last : '»»',
							label_prev : '«',
							label_next : '»',
							start_page : 1,
							items_per_page : 10,
							total_items :	result.cantidad,
							show_first_last: true,
							num_page_links_to_display : 6,
							onChange: function(cantidad){
								listarFormularios(cantidad,search);


							}

							});
						}
					});


				});


				//$('.search').quicksearch('#listado tbody tr');
			}
		}
	});
}

function getConfirm(confirmMessage, callback, onlyaccept){

    confirmMessage = confirmMessage || '';

    onlyaccept = typeof onlyaccept !== 'undefined' ? onlyaccept : false;

    $('#confirmFalse').show();

    $('#confirmmodal').modal({show:true, keyboard: false,});

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

$(document).on('click','.botondesplegareval',function(e){

	var id = $(this).attr('data-id');

	if ($(this).hasClass('desplegado')){
		$(this).removeClass('desplegado');
		$(this).find('i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
		$('.evaluaciones[data-padre="'+id+'"]')
		 .find('td')
		 .wrapInner('<div style="display: block;" />')
		 .parent()
		 .find('td > div')
		 .slideUp(700, function(){
			 $('.evaluaciones[data-padre="'+id+'"]').hide();
			 var $set = $(this);
			  $set.replaceWith($set.contents());

		 });

	}else{

		$(this).addClass('desplegado');

		$('.evaluaciones[data-padre="'+id+'"]').show()
		 .find('td')
		 .wrapInner('<div style="display: none;" />')
		 .parent()
		 .find('td > div')
		 .slideDown(700, function(){

		  var $set = $(this);
		  $set.replaceWith($set.contents());

		 });

		$(this).find('i').removeClass('icon-chevron-up').addClass('icon-chevron-down');


	}

	e.preventDefault();
});


$('.filtro').click(function(){
	$('.search').val('');
	$('.filtro').removeClass('active');

	$(this).addClass('active');

	filtro = $(this).attr('data-filtro');

	$.ajax({
			url:	site_url+'formularios_ajx/obtenerTotal',
			type: 	'POST',
			data: {'filtro':filtro},
			success: function(result){
				$('.pagination').trigger('destroy');
				$('.pagination').paginator({

				label_first : '««',
				label_last : '»»',
				label_prev : '«',
				label_next : '»',
				start_page : 1,
				items_per_page : 10,
				total_items :	result.cantidad,
				show_first_last: true,
				num_page_links_to_display : 6,
				onChange: function(cantidad){
					listarFormularios(cantidad);


				}

				});
			}
		});
});

$(document).on('click','.botonborrar',function(){

	var id = $(this).attr('data-id');
	borrar(id);
	return false;
});


$('#acciones').change(function(){

	var accion = $(this).find('option:selected').val();
	var seleccionados = $('.seleccionitem:checked');
	if (seleccionados.length == 0){

		getConfirm('Debes seleccionar al menos un elemento',function(){},true);


	}else{

		if (accion == 'clonar'){

			getConfirm('Estás seguro que deseas clonar los <b>'+seleccionados.length+'</b> formularios seleccionados?',function(result){


				if (result == true){

					seleccionados.each(function(){

						var id = $(this).attr('data-id');

						clonarYa(id);

					});

				}

			});

		}else if (accion == 'eliminar'){

			getConfirm('Estás seguro que deseas eliminar <b>'+seleccionados.length+'</b> formularios?',function(result){


				if (result == true){

					seleccionados.each(function(){

						var id = $(this).attr('data-id');

						borrarYa(id);

					});

				}


			});


		}

	}

	$(this).find('option:first').attr('selected','selected');

});


$.ajax({
			url:	site_url+'formularios_ajx/obtenerTotal',
			type: 	'POST',
			data: {'filtro':filtro},
			success: function(result){

				$('.pagination').paginator({

				label_first : '««',
				label_last : '»»',
				label_prev : '«',
				label_next : '»',
				start_page : 1,
				items_per_page : 10,
				total_items :	result.cantidad,
				show_first_last: true,
				num_page_links_to_display : 6,
				onChange: function(cantidad){
					listarFormularios(cantidad);

				}

				});
			}
		});
});
</script>
<!-- this page specific styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>css/compiled/user-list.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url(); ?>css/compiled/tables.css" type="text/css" media="screen" />

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
				<button type="button" class="btn btn-default" id="confirmFalse" data-dismiss="modal">Cancelar</button>
				<button type="button" id="confirmTrue" class="btn btn-primary">Aceptar</button>
			</div>
		</div>
	</div>
</div>


<div class="content container-fluid">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3>Formularios</h3>

			<div class="pull-right">

      	<?php if ($this->auth->tieneAcceso('formularios_crear',true)){ ?>
        	<a href="<?php echo base_url();?>adminpanel/formularios/crear"
					class="btn-flat success pull-right"> <span>&#43;</span> NUEVO FORMULARIO
					</a>
  		 	<?php } ?>
      </div>
		</div>

		<!-- Users table -->
		<div class="table-wrapper orders-table section" style="min-height: 650px;">

			<div class="col-md-12">
				<div class="row filter-block">
					<div class="col-md-3 col-xs-12 pull-left">
						<div class="ui-select col-xs-12">
							<select id="acciones">
								<option>- Seleccionar acción -</option>
								<option value="clonar">Clonar</option>
                <?php if ($this->auth->tieneAcceso('formularios_eliminar',true)){ ?>
									<option value="eliminar">Eliminar</option>
								<?php } ?>
              </select>
						</div>
					</div>
					<div class="col-md-4 col-xs-12 pull-left">
						<div class="btn-group pull-right">
							<input type="text" class="search" placeholder="Buscar..">

						</div>

					</div>
					<div class="col-md-5 col-xs-12 pull-left">
						<div class="btn-group">

							<button data-filtro="inscripcion"
								class="filtro glow active middle large btn-flat btn">Inscripciones (<?php echo $cantInscripcion; ?>)</button>
							<button data-filtro="evaluacion"
								class="filtro glow right large btn-flat btn">Evaluaciones (<?php echo $cantEvaluacion; ?>)</button>
							<button data-filtro="eliminado"
								class="filtro  glow right large btn-flat btn">Eliminados (<?php echo $cantEliminados; ?>)</button>
						</div>

					</div>
				</div>

			</div>
			<table id="listado" class="table table-hover col-md-12">
				<thead>
					<tr>
						<th class=" sortable"><input type="checkbox"> ID</th>
						<th class=" hidden-xs hidden-sm sortable">AUTOR</th>
						<th class=" sortable col-sm-5">Titulo</th>
						<th class=" hidden-xs  sortable">Fecha de Inicio</th>
						<th class=" hidden-xs  sortable">Fecha de Fin</th>
						<th class=" hidden-xs hidden-sm sortable">Tipo de Formulario</th>
						<th class=" hidden-xs hidden-sm sortable">Cantidad Inscriptos</th>
						<th class="col-sm-1 col-xs-1 sortable align-right"><span class="line"></span>Acciones</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>

	<div id="pagination" class="pagination pull-right"></div>


	<!-- end users table -->
</div>
</div>
<script src="<?php echo base_url();?>/js/jquery.quicksearch.js"></script>
<script src="<?php echo base_url();?>js/jquery.smartpaginator.js"></script>
