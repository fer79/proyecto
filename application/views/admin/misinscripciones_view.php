<script type="text/javascript">
var site_url = '<?php echo base_url();?>';

function borrar(id){
	$('.alert').remove();
	var deleteyes = confirm("Estas seguro?");
	if(deleteyes) {

		$.ajax({
			type: "POST",
			url: site_url+"registros_ajx/borrarMiInscripcion",
			dataType:"JSON",
			data: {'id':id},
			success: function(result){

				if(result=='not_logged'){
						return false;
				}else if (result=='ok'){

					$('#id-'+id).fadeTo(400, 0, function () { // Links with the class "close" will close parent
						$(this).slideUp(400);
						$('tr#id-'+id).remove();


					});
				}else{

					$('#formulario1').prepend('<div class="alert alert-error">\
                            <i class="icon-error-sign"></i> Error Desconocido.\
							</div>').fadeIn('slow');

				}
			}
		});

	}
	return false;
}

function listarFormularios(start){

	$.ajax({
		url:	site_url+'registros_ajx/misinscripciones',
		type: 	'POST',
		data: {'start':start},
		success: function(result){
			if(result=='not_logged'){
				return false;
			}else if (result=='error'){

			}else if (result=='vacio'){

			}else{
				var data = result;
				$('#listado tbody').html('');

				$.each(data, function(i,val) {

					var diploma = '';
					if (data[i]['diploma'] != '')
						diploma = '<a target="_blank" title="Certificado o Diploma" href="'+data[i]['diploma']+'"><i  style="color: rgba(217, 102, 0, 0.71);font-size: 20px;" class="icon-picture" aria-hidden="true"></i></a>';

					var mostrar ='';
					if (data[i].mostrar == true)
						mostrar = '<a style="margin-right:5px;" data-id="'+data[i].id+'" class="label label-warning" onclick="borrar('+data[i].id+')" href="#"><i class="icon-trash"></i> Borrar mi inscripción</a>';

					var ver ='<a href="'+ site_url +'adminpanel/micuenta/registros/ver/'+ data[i].id +'"><i class="icon-eye-open"></i> </a>'

					$d = $('<tr id="id-'+data[i].id+'">\
								<td>'+data[i].id+'</td> \
								<td>\
									<a href="'+ site_url +'adminpanel/micuenta/registros/ver/'+ data[i].id +'">'+data[i].nombre+'</a>\
								</td>\
								<td>\
									'+data[i].fecha_respuesta+'\
								</td>\
								<td>\
								'+diploma+'\
								</td>\
								<td class="align-right">\
								'+ mostrar + ver + '</td>\
							</tr>').fadeIn('slow');

					$('#listado tbody').append($d);


				});

				$('#listado tbody tr:even').addClass("alt-row");
				$('.search').quicksearch('#listado tbody tr');
			}
		}
	});
}

$(document).ready(function(){

listarFormularios();

});

</script>
<!-- this page specific styles -->
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/user-list.css"
	type="text/css" media="screen" />
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/tables.css" type="text/css"
	media="screen" />


<div class="content">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3>Mis Inscripciones y Evaluaciones</h3>

		</div>

		<!-- Users table -->
		<div class="table-wrapper orders-table section"
			style="min-height: 650px;">
			<div class="col-md-12">
				<div class="filter-block">
					<div class="pull-right">
						<input type="text" class="search" placeholder="Buscar..">
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<table id="listado" class="table table-hover">
					<thead>
						<tr>
							<th class="col-md-1 sortable">ID Inscripción</th>
							<th class="col-md-4 sortable">Titulo</th>
							<th class="col-md-1 sortable">Fecha de Inscripción</th>
							<th class="col-md-1 sortable">Diploma</th>
							<th class="col-md-1 sortable align-right"><span class="line"></span>Acciones
							</th>

						</tr>
					</thead>
					<tbody>


					</tbody>
				</table>
			</div>
		</div>




		<!-- end users table -->
	</div>
</div>
<script src="<?php echo base_url();?>/js/jquery.quicksearch.js"></script>
