<script type="text/javascript">
var site_url = '<?php echo base_url();?>';

function borrar(id){
	$('.alert').remove();
	var deleteyes = confirm("Estas seguro?");
	if(deleteyes) {
	
		$.ajax({
			type: "POST",
			url: site_url+"admin_usuarios_ajx/borrar",
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
				
					$('#formulario1').prepend('<div class="alert alert-error">\
                            <i class="icon-error-sign"></i> Error Desconocido.\
							</div>').fadeIn('slow');
							
					$('html,body').animate({
								scrollTop: $(".alert :visible").offset().top-50
							});
				}	
			}
		});
	
	}		

}	
var xhrlistar = null;
var xhrbuscar = null;

function listarUsuarios(start,search){
	
	search = typeof search !== 'undefined' ? search : '';

	xhrlistar = $.ajax({
		url:	site_url+'admin_usuarios_ajx/listar',
		type: 	'POST',
		data: {'start':start,'search':search},
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
								<td class="align-right">\
									<a href="'+site_url+'adminpanel/usuarios/companeros/'+data[i].id+'" class="companeros"><i class="icon-group"></i></a>\
									<a href="'+site_url+'adminpanel/usuarios/modificar/'+data[i].id+'"><i class="icon-pencil"></i></a>\
									<a href="#" data-id="'+data[i].id+'" class="botonborrar"><i class="icon-trash"></i></a>\
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
						url:	site_url+'admin_usuarios_ajx/obtenerTotal', 
						type: 	'POST',
						data: {'search':search},
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
								listarUsuarios(cantidad,search);
				
							
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


$('#companeroModal').on('shown.bs.modal', function () {

	$.ajax({
		url:	site_url+'admin_usuarios_ajx/traerCompaneros', 
		type: 	'POST',

		success: function(result){ 
				 
			
		}
	});
	
});
	

$(document).on('click','.botonborrar',function(){

	var id = $(this).attr('data-id');
	borrar(id);
	return false;
});



$.ajax({
			url:	site_url+'admin_usuarios_ajx/obtenerTotal', 
			type: 	'POST',

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
					listarUsuarios(cantidad);
	
				
				}
	
				});
			}
		});
});
</script>
<!-- this page specific styles -->
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/user-list.css"
	type="text/css" media="screen" />

<div class="modal fade" id="companeroModal" tabindex="-1" role="dialog"
	aria-labelledby="Compañeros">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Exportar</h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">

					<div class="settings" style="margin: 5px 0">

						<div class="items">
							<h4 class="title">Seleccione los campos a exportar</h4>
							<p>El archivo de exportación contendrá los siguientes campos:</p>
				<?php
				
				// foreach ($camposExportar as $campo){
				
				// echo'<div class="item">
				// <i class="icon-reorder"></i>
				// '.$campo['label'].'
				// <input type="checkbox" class="camposExportar check" data-id="'.$campo['id'].'" checked="checked" />
				// </div>';
				
				// }
				
				?>


			<br>
							<h4 class="title">Filtros</h4>
							<p>Exportar aplicando los siguientes filtros</p>

							<div class="item">

								Todos <input type="radio" name="filtros" value="todos"
									class="check" checked="checked" />
							</div>

							<div class="item">

								Solo Habilitados <input type="radio" name="filtros"
									value="solohabilitados" class="check" />
							</div>
							<div class="item">

								Solo Pagos <input type="radio" name="filtros" value="solopagos"
									class="check" />
							</div>
							<div class="item">
								Con beca <input type="radio" name="filtros" value="conbeca"
									class="check" />
							</div>



						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="content">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3>Usuarios</h3>
			<div class="col-md-10 col-sm-12 col-xs-12 pull-right">
				<input type="text" class="col-md-5 search" placeholder="Buscar...">


				<a href="<?php echo base_url();?>adminpanel/usuarios/crear"
					class="btn-flat success pull-right"> <span>&#43;</span> NUEVA
					PERSONA
				</a>
			</div>
		</div>

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
							<th class="col-md-1 sortable align-right"><span class="line"></span>Acciones
							</th>


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