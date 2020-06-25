<script type="text/javascript">
var site_url = '<?php echo base_url();?>';

function borrar(id){
	$('.alert').remove();
	var deleteyes = confirm("Estas seguro?");
	if(deleteyes) {
	
		$.ajax({
			type: "POST",
			url: site_url+"admin_usuarios_roles_ajx/borrar",
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

function listarRoles(start,search){
	
	search = typeof search !== 'undefined' ? search : '';

	xhrlistar = $.ajax({
		url:	site_url+'admin_usuarios_roles_ajx/listar',
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
					var editar = '<td class="align-right"></td>';
					if (data[i].id != 1){
						 editar = '<td class="align-right">\
							<a href="'+site_url+'adminpanel/usuarios/roles-y-permisos/modificar/'+data[i].id+'"><i class="icon-pencil"></i></a>\
							<a href="#" data-id="'+data[i].id+'" class="botonborrar"><i class="icon-trash"></i></a>\
						</td>';
					}
					
					$d = $('<tr id="id-'+data[i].id+'">\
								<td>'+data[i].id+'</td> \
								<td>\
								'+data[i].nombre+'\
								</td>\
								'+editar+'\
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
						url:	site_url+'admin_usuarios_roles_ajx/obtenerTotal', 
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
								listarRoles(cantidad,search);
				
							
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

$(document).on('click','.botonborrar',function(){

	var id = $(this).attr('data-id');
	borrar(id);
	return false;
});



$.ajax({
			url:	site_url+'admin_usuarios_roles_ajx/obtenerTotal', 
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
					listarRoles(cantidad);
	
				
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

<div class="content">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3>Roles</h3>
			<div class="col-md-10 col-sm-12 col-xs-12 pull-right">
				<input type="text" class="col-md-5 search" placeholder="Buscar...">


				<a
					href="<?php echo base_url();?>adminpanel/usuarios/roles-y-permisos/crear"
					class="btn-flat success pull-right"> <span>&#43;</span> NUEVO ROL
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
							<th class="col-md-1 sortable">Nombre</th>
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