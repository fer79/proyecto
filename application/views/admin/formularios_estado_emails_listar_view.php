<script type="text/javascript">
var site_url = '<?php echo base_url();?>';

var xhrlistar = null;
var xhrbuscar = null;

function listarEmails(start,search){

	search = typeof search !== 'undefined' ? search : '';

	xhrlistar = $.ajax({
		url:	site_url+'admin_emails_ajx/listar',
		type: 	'POST',
		data: {'id':'<?php echo $idFormulario; ?> ', 'start':start,'search':search},
		success: function(result){
			if(result=='not_logged'){
				return false;
			}else if (result=='error'){

			}else if (result=='vacio'){

			}else{
				var data = result.listado;
				$('#listado tbody').html('');
				$.each(data, function(i,val) {

					$d = $('<tr id="id-'+data[i].id+'">\
								<td class="col-md-1 col-xs-4">'+data[i].id+'</td> \
								<td class="col-md-1 col-xs-4">\
									'+data[i].fechaCreacion+'\
								</td>\
								<td class="col-md-1 col-xs-4">\
									'+data[i].para+'\
								</td>\
								<td class="col-md-2 col-xs-12">\
								'+ (data[i].titulo).substring(0, 70) + ((data[i].titulo.length >70) ? '...' :  '') +'\
								</td>\
								<td class="col-md-4 col-xs-12" style="max-width:120px">\
								'+ (data[i].mensaje).substring(0, 100) + ((data[i].mensaje.length >100) ? '...' :  '') +'\
								</td>\
								<td class="col-md-1 col-xs-3">\
								'+ ((data[i].enviado == 1) ? 'Si' : 'No') +'\
								</td>\
								<td class="col-md-1 col-xs-3">\
								'+data[i].intentos+'\
								</td>\
								<td class="col-md-1 col-xs-3">\
								'+ ((data[i].fechaEnvio != null) ? data[i].fechaEnvio :  '-') +'\
								</td>\
							</tr>').fadeIn('slow');

					$('#listado tbody').append($d);


				});

				$('#listado tbody tr:even').addClass("alt-row");

				$('.search').keyup(function(){

					var search = $(this).val();

					if (xhrbuscar != null)
						xhrbuscar.abort();

					if (xhrlistar != null)
						xhrlistar.abort();

					xhrbuscar = $.ajax({
						url:	site_url+'admin_emails_ajx/obtenerTotal',
						type: 	'POST',
						data: {'id':'<?php echo $idFormulario; ?> ', 'search':search},

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
								listarEmails(cantidad,search);
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

$(document).ready(function(){

	$.ajax({
			url:	site_url+'admin_emails_ajx/obtenerTotal',
			type: 	'POST',
			data: {'id':'<?php echo $idFormulario; ?> '},

			success: function(result){

				$('.pagination').paginator({

				label_first : '««',
				label_last : '»»',
				label_prev : '«',
				label_next : '»',
				start_page : 1,
				items_per_page : 30,
				total_items :	result.cantidad,
				show_first_last: true,
				num_page_links_to_display : 6,
				onChange: function(cantidad){
					listarEmails(cantidad);

				}

				});
			}
		});
});
</script>

<!-- this page specific styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>css/compiled/user-list.css" type="text/css" media="screen" />


<div class="content">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3>Estado de Emails</h3>
			<div class="col-md-10 col-sm-12 col-xs-12 pull-right">
				<input type="text" class="col-md-5 search" placeholder="Buscar...">
			</div>
		</div>

		<!-- Users table -->
		<div class="row" style="min-height: 650px;">
			<div class="col-md-12 col-xs-12">
				<table id="listado" class="table table-hover">
					<thead>
						<tr>
							<th class="sortable">ID</th>
							<th class="sortable">Fecha de Creación</th>
							<th class="sortable">Para</th>
							<th class="sortable">Título</th>
							<th class="sortable">Mensaje</th>
							<th class="sortable">Enviado</th>
							<th class="sortable">Intentos</th>
							<th class="sortable">Fecha de Envío</th>

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
