
<script type="text/javascript">
var site_url = 'http://dev.localhost/';

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


function getConfirm(confirmMessage, callback, onlyaccept){
    confirmMessage = confirmMessage || '';

    onlyaccept = typeof onlyaccept !== 'undefined' ? onlyaccept : false;

    $('#confirmFalse').show();

    $('#confirmmodal').modal({show:true,

                            keyboard: false,
    });

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

function listarInscripcionesTardias(){


	$.ajax({
		url:	site_url+'formularios_ajx/obtenerInscripcionesTardias',
		type: 	'POST',
		data: {'id':'<?php echo $idFormulario; ?> '},
		success: function(data){

			$('#zonaInscripcionesTardias').remove();


			if (data.length > 0){
				var $html = '<div id="zonaInscripcionesTardias"><h5 style="border-bottom: 1px solid #f3f3f3;margin-bottom: 10px;color: #31b0d5;;font-weight: bold;">Habilitados a Inscripción Tardía</h5>';

				$.each(data, function(i,val) {

					if(val['usuario'] != null){
					$html+='<div class="inscriptostarde">\
								- '+val['nombre']+' '+val['apellidos']+' (<b>'+val['usuario']+'</b>) '+' - '+val['email']+'\
							</div>';
					}else{

						$html+='<div class="inscriptostarde">\
							- '+val['email']+'\
						</div>';
					}
				});

				$html+='</div>';


				$('.row.header').append($html);

			}
		}

	});
}


function listarRegistros(start){



	$.ajax({
		url:	site_url+'registros_ajx/listar',
		type: 	'POST',
		data: {'id':'<?php echo $idFormulario; ?> '},
		success: function(result){
			if(result=='not_logged'){
				return false;
			}else if (result=='error'){

			}else if (result=='vacio'){

			}else{
				var data = result.listado;
				$('#listado tbody').html('');
 				$('.total b').after(Object.size(data));

				$.each(data, function(i,val) {

					var $marcador = $('<b></b>');
					if (data[i]['permhabilitado'] || data[i]['permpago'] ){

						var notacolor='';
						var notacolorcolor ='#e3e3e3';
						var notacolorclass='';
						var notacoloricon ='icon-bookmark-empty';

						if (data[i]['notacolor'] != ""){
							notacolor = {'color':data[i]['notacolor'],'texto':data[i]['notatexto']}
							notacolorcolor = data[i]['notacolor'];
							notacolorclass='connota';
							notacoloricon ='icon-bookmark';
						}

						$marcador = $('<div><a href="#" title="Agregar Nota"  data-toggle="modal" data-target="#notamodal" data-id="'+data[i]['id']+'" class="agregarnotabtn '+notacolorclass+'" ><i style="color:'+notacolorcolor+'" class="'+notacoloricon+' agregarnota"></i></a></div>');
						$marcador.find('a').attr('data-nota',JSON.stringify(notacolor));


					}

					var estrellitaEvaluacion = '';
					if(data[i]['tieneEvaluacion']){

							estrellitaEvaluacion = '<span class="glyphicon glyphicon-star-empty estrellaevaluacion"   title="Falta Evaluación" aria-hidden="true"></span>';

							if (data[i]['usuYaEvaluo']){

								estrellitaEvaluacion = '<span class="glyphicon glyphicon-star estrellaevaluacion realizoevaluacion"   title="Usuario realizó evaluación" aria-hidden="true"></span>';

							}

					}

					var diploma = '';
					if (data[i]['diploma'] != '')
						diploma = '<a target="_blank" title="Certificado o Diploma" href="'+data[i]['diploma']+'"><i  style="color: rgba(217, 102, 0, 0.71);font-size: 20px;" class="icon-picture" aria-hidden="true"></i></a>';

					var enviarEval = '';
					<?php if(($this->config->item('sorteoplaza_habilitado') == false) && $hasFormVinculado) { ?>

					 enviarEval = '<a class="enviarEval" title="Enviar Evaluacion Nuevamente" data-id="'+data[i]['id']+'" href="#"><i  style="color: #ff723a;font-size: 20px;" class="icon-envelope" aria-hidden="true"></i></a>';

					<?php } ?>

					var seleccionado ='';
					var claseSeleccionado='';
					if (data[i]['seleccionado'] == 1){
						seleccionado = '<span title="Registro seleccionado por sorteo de plazas"  class="label label-success usuarioseleccionado"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></span>'
						claseSeleccionado=' seleccionado ';
					}

					var becas ='';
					var claseBecado='';
					var descuentos = [];

					if (data[i]['becas'] != 'undefined'){

						$.each(data[i]['becas'], function(n,val2) {

							becas += '<span title="Registro seleccionado por sorteo \''+val2['respuesta']+'\'" style="/* border-radius: 18px; */font-size: 12px;width: 100%;display: block;padding: 5px;" class="label label-warning becasorteada">'+val2['respuesta']+'</span>'
							claseBecado=' becado ';
							descuentos.push(val2['porcentajedescuento']);

						});
					}



					var html = '<tr class="'+claseBecado+claseSeleccionado+'" style="text-align:center;" id="id-'+data[i]['id']+'">\
								<td style="width:50px;">'+estrellitaEvaluacion+seleccionado+$marcador.html()+becas+diploma+enviarEval+'</td>\
								<td>'+data[i]['id']+'</td>';


					if (data[i]['campos'] != undefined){
						$.each(data[i]['campos'], function(n,val2) {

								html +='	<td class="hidden-xs hidden-sm">\
										'+data[i]['campos'][n]+'\
									</td>';

						});
					}

					
					var cuotas = data[i]['cuotas']

					var pago1 ='No Pago c/1';
					var pago2 ='No Pago c/2';
					var pago3 ='No Pago c/3';
					var pagocss1 = 'label label-info';
					var pagocss2 = 'label label-info';
					var pagocss3 = 'label label-info';
					var valpago1 = '';
					var valpago2 = '';
					var valpago3 = '';

					if ((data[i]['pago1'] != null) && (data[i]['pago1'] != '')){
						pago1 ='Pago c/1';
						pagocss1 = 'label label-success';
						valpago1 = data[i]['pago1'];
					}
					if ((data[i]['pago2'] != null) && (data[i]['pago2'] != '')){
						pago2 ='Pago c/2';
						pagocss2 = 'label label-success';
						valpago2 = data[i]['pago2'];
					}
					if ((data[i]['pago3'] != null) && (data[i]['pago3'] != '')){
						pago3 ='Pago c/3';
						pagocss3 = 'label label-success';
						valpago3 = data[i]['pago3'];
					}

					var habilitado ='No Habilitado';
					var habilitadocss = 'label label-info';
					if (data[i]['habilitado'] == 1){
						habilitado = 'Habilitado';
						habilitadocss = 'label label-success';
					}


					html+='<td class="hidden-xs hidden-sm">\
									'+data[i]['fecha']+'\
								</td>';

					html+='<td class="col-sm-2 col-xs-2 align-right">';

					html+= '<div class="zonaacciones">';

					if (data[i]['permhabilitado'])
						html+='<a style="margin-right:5px;padding:5px;" data-id="'+data[i]['id']+'" class="habilitarBoton '+habilitadocss+'" href="#"><i class="icon-check"></i> '+habilitado+'</a>';
					else
						html+='<span style="margin-right:5px;padding:5px;" class="habilitarBotonSpan '+habilitadocss+'"><i class="icon-check"></i> '+habilitado+'</span>';

					if ((data[i]['sedebeabonar'] == 1)){


						var mostrarpago = 'display:none;';
						if (data[i]['habilitado'] == 1)
							mostrarpago = 'display:block;';

						if (data[i]['permpago']){

							html+='<a id="'+data[i]['id']+'-1" style="'+mostrarpago+'margin-right:5px;min-width:100px;padding:5px" data-id="'+data[i]['id']+'" class="pagoBoton '+pagocss1+'" data-toggle="modal" data-total-cuotas="'+cuotas+'" data-cuota="1" data-numero="'+valpago1+'" data-descuentos=\''+JSON.stringify(descuentos)+'\' data-target="#pagobotonmodal" href="#"><i class="icon-money"></i> '+pago1+'</a>';

							if (cuotas>1){
								html+='<a id="'+data[i]['id']+'-2" style="'+mostrarpago+'margin-right:5px;min-width:100px;padding:5px" data-id="'+data[i]['id']+'"  class="pagoBoton '+pagocss2+'" data-toggle="modal" data-total-cuotas="'+cuotas+'" id="'+data[i]['id']+'"data-cuota="2" data-numero="'+valpago2+'" data-descuentos=\''+JSON.stringify(descuentos)+'\' data-target="#pagobotonmodal" href="#"><i class="icon-money"></i> '+pago2+'</a>';
							}

							if (cuotas>2){
							html+='<a id="'+data[i]['id']+'-3" style="'+mostrarpago+'margin-right:5px;min-width:100px;padding:5px" data-id="'+data[i]['id']+'" class="pagoBoton '+pagocss3+'" data-toggle="modal" data-total-cuotas="'+cuotas+'" data-cuota="3" data-numero="'+valpago3+'" data-descuentos=\''+JSON.stringify(descuentos)+'\' data-target="#pagobotonmodal" href="#"><i class="icon-money"></i> '+pago3+'</a>';
							}
						}
						else{
							html+='<span style="'+mostrarpago+'margin-right:5px;min-width:100px;padding:5px"  class="pagoBotonSpan '+pagocss1+'"><i class="icon-money"></i> '+pago1+'</span>';

							if (cuotas>1){
								html+='<span style="'+mostrarpago+'margin-right:5px;min-width:100px;padding:5px"  class="pagoBotonSpan '+pagocss2+'"><i class="icon-money"></i> '+pago2+'</span>';
							}

							if (cuotas>2){
								html+='<span style="'+mostrarpago+'margin-right:5px;min-width:100px;padding:5px"  class="pagoBotonSpan '+pagocss3+'"><i class="icon-money"></i> '+pago3+'</span>';
							}
						}
					}

					html+= '</div>';

					if (data[i]['permver'])
						html+='<a href="'+site_url+'adminpanel/formularios/<?php echo $idFormulario; ?>/registros/ver/'+data[i]['id']+'"><i class="icon-eye-open"></i> </a>';


					html+='	</td>\
					</tr>';

					$d = $(html).fadeIn('slow');

					$('#listado tbody').append($d);


				});

				$('#listado tbody tr:even').addClass("alt-row");
				$('.search').quicksearch('#listado tbody tr');
			}
		}
	});
}

$(document).ready(function(){

		 $('#colorpickernota').colorpicker({
	         color: '#ffaa00',
	         container: true,
	         inline: true
	     });

		var idForm ='<?php echo $idFormulario; ?>';



		listarInscripcionesTardias();


		$('#habilitartardemodal #doHabilitarInscripcion').click(function(){

			var usuario = $('#habilitartardemodal').find('#emailpersona').val();
			$('#habilitartardemodal .alert').remove();
			if ($('#habilitartardemodal input#verificadodatos').is(':checked')){

				$('#habilitartardemodal input#verificadodatos').parent().parent().parent().parent().parent().removeClass('textoerror');

				$.ajax({
					url:	site_url+"registros_ajx/habilitarInscripcionTardia",
					data: {'idForm':'<?php echo $idFormulario; ?> ','usuario':usuario},
					type: 	'POST',
					dataType:'JSON',
					async:  true,
					success: function(result){

						if (result == 'error'){

							$('#habilitartardemodal .modal-footer').before('<div class="alert alertgeneral alert-danger">\
									 Error al habilitar a la persona. Debe ingresar un E-mail válido.\
									</div>');

						}else{

							$('#habilitartardemodal').modal('toggle');

							listarInscripcionesTardias();

						}

					}


				});

			}else{
				$('#habilitartardemodal').effect( "shake" );
				$('#habilitartardemodal input#verificadodatos').parent().parent().parent().parent().parent().addClass('textoerror');

			}

			return false;

		});


		$('#emailpersona').select2({
		 formatNoMatches: function() {
		        return 'Persona no encontrada';
		    },
		    createSearchChoice:function(term, data) {
		        if ( $(data).filter( function() {
		          return this.text.localeCompare(term)===0;
		        }).length===0) {
		          return {id:term, text:term};
		        }
		      },
			ajax: {
				url:	site_url+'registros_ajx/buscarPersona',
				type: 	'POST',
				delay:250,
				data: function (term) {
		            return {
		                term: term
		            };
		        },
		        results: function (data) {
		            return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.nombre +" "+ item.apellidos + " (" +item.usuario+ ")",
		                        id: item.id
		                    }
		                })
		            };
		        }
			}
		});



		$('#doNota').click(function(){

			var id = $(this).attr('data-id');

			var color = $('#notamodal #colorpickernota').colorpicker('getValue');
			var texto = $('#notamodal [name="notatexto"]').val();

			$('tr[id="id-'+id+'"] .agregarnotabtn').attr('data-nota',JSON.stringify({'color':color,'texto':texto}));
			$('tr[id="id-'+id+'"] .agregarnotabtn').attr('title',texto).addClass('connota');
			$('tr[id="id-'+id+'"] .agregarnotabtn i').removeClass('icon-bookmark-empty').addClass('icon-bookmark').css('color',color);

			$.ajax({
				url:	site_url+"registros_ajx/agregarnota",
				data: {'id':id,'color':color,'texto':texto},
				type: 	'POST',
				async:  true,
				success: function(result){

					$('#notamodal').modal('toggle');

				}

			});


		});

		$('#doExport').click(function(){

			var campos = []

			$('#exportarModal .camposExportar:checked').each(function(i,o){

				campos.push($(this).attr('data-id')); //Metemos todos los  index en el array

			});

			var filtros = $('#exportarModal [name="filtros"]:checked').val();

			 var form = $('<form></form>').attr('action', site_url+"registros_ajx/exportar").attr('method', 'post');
			    // Add the one key/value
			 form.append($("<input></input>").attr('type', 'hidden').attr('name', 'idForm').attr('value', idForm));
			 form.append($("<input></input>").attr('type', 'hidden').attr('name', 'filtros').attr('value', filtros));
			 form.append($("<input></input>").attr('type', 'hidden').attr('name', 'campos').attr('value', JSON.stringify(campos)));

			    //send request
			 form.appendTo('body').submit().remove();


		});

		<?php if ($this->auth->tieneAcceso('registros_sorteo_plaza',true)){ ?>
			<?php if($formularios['f_sorteo_plaza'] == NULL){ ?>


			$(document).on('click','#doSortearPlaza',function(){


				var cantidad = $('#sorteoplazamodal').find('#cantidadplazas').val();
				var mensaje = $('#sorteoplazamodal').find('#message-text').text();
				var mensajeNO = $('#sorteoplazamodal').find('#message-textNO').text();


				if ($('#sorteoplazamodal input#verificadodatos').is(':checked')){

					$('#sorteoplazamodal input#verificadodatos').parent().parent().parent().parent().parent().removeClass('textoerror');

					$.ajax({
						url:	site_url+"registros_ajx/sorteoplaza",
						data: {'idForm':'<?php echo $idFormulario; ?> ','cantidad':cantidad,'mensaje':mensaje,'mensajeNO':mensajeNO},
						type: 	'POST',
						dataType:'JSON',
						async:  true,
						success: function(result){

							$('#sorteoplazamodal').modal('toggle');

							$('#sorteoplaza').attr('disabled',true);
							$('#sorteobeca').removeAttr('disabled');

							$.each(result, function(i,val) {
								$('tr[id="id-'+val+'"]').addClass('seleccionado');
								$('tr[id="id-'+val+'"] td:first').prepend('<span title="Registro seleccionado por sorteo de plazas" style="border-radius: 18px;font-size: 14px;" class="label label-success">S</span>');

							});
						}


					});

				}else{
					$('#sorteoplazamodal').effect( "shake" );
					$('#sorteoplazamodal input#verificadodatos').parent().parent().parent().parent().parent().addClass('textoerror');

				}

				return false;

			});


			$('#sorteoplazamodal').on('show.bs.modal', function (event) {

				  var button = $(event.relatedTarget); // Button that triggered the modal
				  var titulo = $('#sorteoplazamodal').data('tituloform'); // Extract info from data-* attributes
				  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
				  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
				  var modal = $(this);


				  modal.find('.modal-title').text('Sorteo de Plaza "' + titulo + '"');


				});

			<?php } ?>
		<?php } ?>

		<?php if (($this->config->item('sorteoplaza_habilitado') == true) && $this->auth->tieneAcceso('registros_sorteo_beca',true)){ ?>

			$(document).on('click','#doSortear',function(event){

					/*

					Acá se realiza el sorteo de las becas, o lo que sea.

					*/


					var sorteo = $(this).attr('data-sorteo');
					var cantidad = $('#sorteoindividualmodal').find('#cantidadplazas').val();
					var porcentajedescuento = $('#sorteoindividualmodal').find('#porcentajedescuento').val();
					var mensaje = $('#sorteoindividualmodal').find('#message-text').text();


					if ($('#sorteoindividualmodal input#verificadodatos').is(':checked')){

						$('#sorteoindividualmodal input#verificadodatos').parent().parent().parent().parent().parent().removeClass('textoerror');

						$.ajax({
							url:	site_url+"registros_ajx/sorteobeca",
							data: {'idForm':'<?php echo $idFormulario; ?> ','cantidad':cantidad,'mensaje':mensaje,'sorteo':sorteo,'porcentajedescuento':porcentajedescuento},
							type: 	'POST',
							dataType:'JSON',
							async:  true,
							success: function(result){
								$('#sorteoindividualmodal').modal('toggle');

								$('#becamodal .sorteo[data-sorteo="'+sorteo+'"]').attr('disabled',true).html('Ya sorteado');


								$.each(result, function(i,val) {

									$('tr[id="id-'+val['id_registro']+'"]').addClass('beca');
									$('tr[id="id-'+val['id_registro']+'"] td:first').prepend('<span title="Registro seleccionado por sorteo \''+val['respuesta']+'\'" class="label label-warning">'+val['respuesta']+'</span>');


									var descuento = $.parseJSON($('.pagoBoton[data-id="'+val['id_registro']+'"]').attr('data-descuentos'));

									descuento.push(porcentajedescuento);

									$('.pagoBoton[data-id="'+val['id_registro']+'"]').attr('data-descuentos',JSON.stringify(descuento));



								});

							}


						});

					}else{
						$('#sorteoindividualmodal').effect( "shake" );
						$('#sorteoindividualmodal input#verificadodatos').parent().parent().parent().parent().parent().addClass('textoerror');

					}

				return false;
			});


			<?php echo $idFormulario; ?>

		$('#becamodal').on('show.bs.modal', function (event) {
			$('#becamodal #camposbeca').html('');

			$.ajax({
				url:	site_url+"registros_ajx/obtenerBecas",
				data: {'idForm':'<?php echo $idFormulario; ?> '},
				type: 	'POST',
				dataType:'JSON',
				async:  true,
				success: function(result){


					if (result){
						$.each(result,function(sorteo,datos){

							var campohtml = 'En campo/s: ';

							var i = 0;
							$.each(datos['campos'],function(i,campo){

								if (i != 0)
									campohtml +=', ';

								campohtml += campo['label'].replace(':','');

								i++;

							});

							var realizadotxt = 'Sortear';
							var realizadodisabled='';
							if (datos['realizado'] == true){
								 realizadotxt = 'Ya sorteado';
								 realizadodisabled='disabled="disabled"';

							}




							$('#becamodal #camposbeca').append('<div class="item" style="clear:both;padding: 7px 0px 22px 20px;">\
									<div style="display: block;width: 20px;float: left;height: 48px;padding-top: 11px;margin-right:10px;"><i class="icon-reorder"></i></div>\
									<div style="float:left;width:70%;">\
									<span style="display:block;">'+sorteo+'</span><span style="font-size: 10px;color: #a3a3a3;display:block;height: auto;width: 100%;word-wrap: break-word;">'+campohtml+'</span>\
									</div>\
									<a data-toggle="modal" '+realizadodisabled+' data-target="#sorteoindividualmodal" data-sorteo="'+sorteo+'" class="btn btn-warning check sorteo">'+realizadotxt+'</a>\
								</div>');

						});
					}else{

						$('#becamodal #camposbeca').append('<p><b>- No hay becas para sortear -</b></p>');

					}

				}


			});


		});



		$('#sorteoindividualmodal').on('show.bs.modal', function (event) {

		  var button = $(event.relatedTarget); // Button that triggered the modal
		  var sorteo = button.data('sorteo'); // Extract info from data-* attributes
		  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
		  var modal = $(this);


		  modal.find('.modal-title').text('Sorteo "' + sorteo + '"');
		  modal.find('#doSortear').attr('data-sorteo',sorteo);



		  var messagetext =  modal.find('#message-text');
		  var textooriginal = modal.find('#textooriginal');
		  messagetext.text(textooriginal.text().replace('{{beca_nombre}}','"'+sorteo+'"').replace('{{tituloform}}','"'+modal.data('tituloform')+'"'));



		});



		<?php } ?>

		$(document).on('click', '.enviarEval', function() {
			var boton = $(this);
			var id = $(this).attr('data-id');

			getConfirm('Reenviar E-mail de evaluación?',function(result){


				if (result == true){


					$.ajax({
						url:	site_url+"registros_ajx/reenviarEvaluacion",
						data: {'id':id},
						type: 	'POST',
						dataType: 'html',
						async:  true,
						success: function(result){

						}
					});

				}
			});

			return false;
		});

		<?php if ($this->auth->tieneAcceso('registros_marcar_habilitado',true)){ ?>
		$(document).on('click','.habilitarBoton',function(){
			var boton = $(this);
			var id = $(this).attr('data-id');


			if ($('.pagoBoton[data-id="'+id+'"]').hasClass('label-success')){

				getConfirm('El registro figura como pago, si acepta se borrará dichos datos. ¿ Desea deshabilitarlo de todas formas ?',function(result){
					if (result == true){

						$.ajax({
							url:	site_url+"registros_ajx/habilitar",
							data: {'id':id},
							type: 	'POST',
							dataType: 'html',
							async:  true,
							success: function(result){
								if (boton.hasClass('label-success')){


									$.ajax({
										url:	site_url+"registros_ajx/pagar",
										data: {'id':id},
										type: 	'POST',
										dataType: 'html',
										async:  true,
										success: function(result){

											boton.removeClass('label-success');
											boton.addClass('label-info');
											boton.html('<i class="icon-check"></i>No Habilitado');

											$('.pagoBoton[data-id="'+id+'"]').removeClass('label-success');
											$('.pagoBoton[data-id="'+id+'"]').attr('data-numero','');
											$('.pagoBoton[data-id="'+id+'"]').addClass('label-info');
											$('.pagoBoton[data-id="'+id+'"]').html('<i class="icon-money"></i>No Pago');
										}

									});



								}
							}

						});


					}
				});


			}else{

				getConfirm('Estás seguro?',function(result){


					if (result == true){


						$.ajax({
							url:	site_url+"registros_ajx/habilitar",
							data: {'id':id},
							type: 	'POST',
							dataType: 'html',
							async:  true,
							success: function(result){
								if (boton.hasClass('label-success')){

									boton.removeClass('label-success');
									boton.addClass('label-info');
									boton.html('<i class="icon-check"></i>No Habilitado');
									$('.pagoBoton[data-id="'+id+'"]').hide();
								}else{

									boton.addClass('label-success');
									boton.removeClass('label-info');
									boton.html('<i class="icon-check"></i>Habilitado');
									$('.pagoBoton[data-id="'+id+'"]').show();


								}
							}


						});

					}



				});

			}






		return false;
		});

		<?php }?>

		<?php if ($this->auth->tieneAcceso('registros_marcar_pago',true)){ ?>


		$(document).on('click','#doPagar',function(){
			var id = $(this).attr('data-id');
			var cuota = $('#pagobotonmodal input#ncuota').val();
			var boton = $('#'+id+'-'+cuota);


				var numero = $('#pagobotonmodal input#nboleta').val();

				$('#pagobotonmodal input#verificadodatos').parent().parent().parent().parent().parent().removeClass('textoerror');
				$('#pagobotonmodal input#nboleta').parent().removeClass('has-error');

				if (numero == ''){

					$('#pagobotonmodal').effect( "shake" );
					$('#pagobotonmodal input#nboleta').parent().addClass('has-error');


				}else if (!$('#pagobotonmodal input#verificadodatos').is(':checked')){

					$('#pagobotonmodal').effect( "shake" );
					$('#pagobotonmodal input#verificadodatos').parent().parent().parent().parent().parent().addClass('textoerror');


				}else{

					$.ajax({
						url:	site_url+"registros_ajx/pagar",
						data: {'id':id,'numero':numero,'tipo':1, 'cuota': cuota},
						type: 	'POST',
						dataType: 'html',
						async:  true,
						success: function(result){
							$('#pagobotonmodal').modal('toggle');

							boton.addClass('label-success');
							boton.attr('data-numero',numero);
							boton.removeClass('label-info');
							boton.html('<i class="icon-money"></i> Pago c/' + cuota);
						}

					});

				}


			return false;
		});

		$(document).on('click','#doBorrarPago',function(){
			var id = $(this).attr('data-id');
			var cuota = $('#pagobotonmodal input#ncuota').val();
			var boton = $('#'+id+'-'+cuota);

			getConfirm('Estás seguro que desea eliminar el pago?',function(result){


				if (result == true){


					$.ajax({
						url:	site_url+"registros_ajx/pagar",
						data: {'id':id, 'cuota': cuota},
						dataType: 'html',
						type: 	'POST',
						async:  true,
						success: function(result){

							$('#pagobotonmodal').modal('toggle');
							boton.removeClass('label-success');
							boton.attr('data-numero','');
							boton.addClass('label-info');
							boton.html('<i class="icon-money"></i> No Pago c/'+cuota);
						}

					});
				}



			});


			return false;
		});


		$('#pagobotonmodal').on('show.bs.modal', function (event) {

			  var button = $(event.relatedTarget) // Button that triggered the modal
			  var titulo = $('#pagobotonmodal').data('tituloform'); // Extract info from data-* attributes
			  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
			  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
			  var modal = $(this)

			  	var cuotas = button.attr('data-total-cuotas');
	  			var  descuentoporbeca = '';
		  		var montofinal = GLOBALcostocurso;
				 $.each($.parseJSON(button.attr('data-descuentos')),function(i,val){
					 if (i != 0)
					 descuentoporbeca+=', ';
					 descuentoporbeca+=val+'%';

					montofinal = montofinal - ((val * montofinal)/100);//HACEMOS EL PORCENTAJE

					 i++;
			 	 });
			 	var montoporcuota = montofinal/cuotas;



			  modal.find('#nboleta').val(button.attr('data-numero'));
			  modal.find('#ncuota').val(button.attr('data-cuota'));
			  modal.find('#total-cuotas').html(button.attr('data-total-cuotas'));
			  modal.find('#cuota').html(button.attr('data-cuota'));

		      modal.find('#doPagar').attr('data-id',button.attr('data-id'));
		      modal.find('#doBorrarPago').attr('data-id',button.attr('data-id'));

			  modal.find('#montooriginal').html(GLOBALmonedacostocurso+' '+GLOBALcostocurso);
			  modal.find('#descuentoporbeca').html(descuentoporbeca);
			  modal.find('#montoacobrar').html(GLOBALmonedacostocurso+' '+montofinal);
			  modal.find('#montoacobrarporcuota').html(GLOBALmonedacostocurso+' '+montoporcuota);
			  modal.find('.modal-title').text('Pago de "' + titulo + '"')


			});

		<?php } ?>

		$('#notamodal').on('click','.seleccionarnotaprevia',function(e){

			var color = $(this).attr('data-color');

			$('#notamodal #colorpickernota').colorpicker('setValue',color);


			e.preventDefault();
		});


		$('#notamodal').on('show.bs.modal', function (event) {

			  var button = $(event.relatedTarget) // Button that triggered the modal
			  var modal = $(this)

			  modal.find('#notayautilizado').html('');
			  modal.find('[name="notatexto"]').val('');


			if (typeof button.attr('data-nota') != 'undefined'){
			 	var color =  JSON.parse(button.attr('data-nota'));
			 	modal.find('#colorpickernota').colorpicker('setValue',color.color);
				modal.find('[name="notatexto"]').val(color.texto);

			}
			  modal.find('#doNota').attr('data-id',button.attr('data-id'));

			  $('.agregarnotabtn.connota').each(function(){

				  if (typeof button.attr('data-nota') != 'undefined'){
					  var data = JSON.parse($(this).attr('data-nota'));
					  if (modal.find('#notayautilizado a[data-color="'+data.color+'"]').length == 0){
					  	modal.find('#notayautilizado').append('<a href="#" title="Copiar color" data-color="'+data.color+'" class="seleccionarnotaprevia"> <i style="padding:15px;font-size:23px;color:'+data.color+'" class="icon-bookmark"></i></a>');
					  }
				  }
			  });

		});



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

		$("input:checkbox, input:radio").uniform();


listarRegistros();


});
</script>
<!-- this page specific styles -->
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/chart-showcase.css"
	type="text/css" media="screen" />
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/user-list.css"
	type="text/css" media="screen" />
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/tables.css" type="text/css"
	media="screen" />

<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/ui-elements.css"
	type="text/css" media="screen" />
<link rel="stylesheet"
	href="<?php echo base_url(); ?>css/compiled/elements.css"
	type="text/css" media="screen" />
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
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
				<button type="button" class="btn btn-default" id="confirmFalse"
					data-dismiss="modal">Cancelar</button>
				<button type="button" id="confirmTrue" class="btn btn-primary">
					Aceptar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" style="z-index: 9950;"
	data-tituloform="<?php echo $formularios['titulo']; ?>"
	id="habilitartardemodal" role="dialog"
	aria-labelledby="Sorteo de Plazas">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Habilitar inscripción
					tardía</h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">
					<div class="settings" style="margin: 5px 0">

						<div class="items">

							<p>
								<b>Informar lo siguiente a la persona</b>
							</p>
							<p>
								<b>1.</b> Tendrá un plazo de <b>48Hs</b></b> para realizar la
								inscripción, luego de ese plazo ya no podrá inscribirse.
							</p>
				<?php

if ($formularios ['abonar'] == 1) {

					$periodo = $this->Admin_formularios_model->abonoCalcular72HorasPrevias ( $formularios ['fechacomienzocurso'] );

					?><p>
								<b>2.</b> Deberá abonar entre el <b><?php echo date('d-m-Y',strtotime($periodo['comienzo'])); ?></b>
								y el <b><?php echo date('d-m-Y',strtotime($periodo['fin'])); ?></b>
								en <b><?php echo $formularios['lugarabono']; ?></b>
							</p>

				<?php
				}
				?>
				<br>

							<h5>
								<b>Pasos a realizar</b>
							</h5>
							<hr>
							<p>Busque a la persona por nombre, apellido, usuario o e-mail.</p>
							<p>Si no la encuentra, ingrese un e-mail al que le llegarán los
								datos para realizar la inscripción.</p>

							<div style="padding: 20px;"
								class="col-md-12 col-sm-12 col-xs-12 ">
								<div class="form-group">
									<div class="col-md-12" style="padding: 0;">
										<input type="text" class="col-md-12 search" id="emailpersona"
											placeholder="Buscar persona"> <br> <br>
									</div>
								</div>
								<style>
.select2-container {
	padding: 0;
	margin-right: 10px;
}
</style>
							</div>

							<div class="form-group">

								<div class="checker" id="uniform-inlineCheckbox1">
									<span><input type="checkbox" id="verificadodatos" value="1"></span>
								</div>
								He verificado que todos los datos son correctos
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doHabilitarInscripcion"
					class="btn btn-primary">Habilitar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" style="z-index: 9950;"
	data-tituloform="<?php echo $formularios['titulo']; ?>"
	id="sorteoplazamodal" tabindex="-1" role="dialog"
	aria-labelledby="Sorteo de Plazas">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">
					<div class="settings" style="margin: 5px 0">

						<div class="items">

							<p>Tenga en cuenta que una vez sorteado no podrá volver a
								sortear, por favor verifique bien los datos</p>

							<div class="form-group">
								<label for="recipient-name" class="control-label">Cantidad de
									Plazas a sortear:</label> <input type="text"
									class="form-control onlynumber" value="1" id="cantidadplazas">
							</div>
							<div class="form-group">
								<label for="message-text" class="control-label">Mensaje que
									llegará por mail a los sorteados:</label>
								<textarea class="form-control" style="height: 200px;"
									id="message-text"><?php echo $mensajeMail; ?></textarea>
							</div>
							<div class="form-group">
								<label for="message-textNO" class="control-label">Mensaje que
									llegará por mail a los NO sorteados:</label>
								<textarea class="form-control" style="height: 200px;"
									id="message-textNO"><?php echo $mensajeNOMail; ?></textarea>
							</div>
							<div class="form-group">
								<div class="checker" id="uniform-inlineCheckbox1">
									<span><input type="checkbox" id="verificadodatos" value="1"></span>
								</div>
								He verificado que todos los datos son correctos
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doSortearPlaza" class="btn btn-primary">
					Sortear</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" style="z-index: 9950;"
	data-tituloform="<?php echo $formularios['titulo']; ?>"
	id="pagobotonmodal" tabindex="-1" role="dialog" aria-labelledby="Pago">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">
					<div class="settings" style="margin: 5px 0">

						<div class="items">

							<p>Por favor, verifique bien todos los datos</p>
							<div class="form-group">
								<b>Cuota:</b>
								<span id="cuota"></span>/<span id="total-cuotas"></span>
								<input type="hidden" class="form-control" value="" id="ncuota">
							</div>
							<div class="form-group">
								<b>Costo total original:</b>
								<p id="montooriginal"></p>
							</div>
							<div class="form-group">
								<b>Descuentos aplicados:</b>
								<p id="descuentoporbeca"></p>
							</div>
							<div class="form-group">
								<b>Monto total luego de descuentos:</b>
								<p id="montoacobrar"></p>
							</div>

							<div class="form-group">
								<b>Monto final a cobrar por cuota:</b>
								<p style="color: red; font-weight: bold; font-size: 20px;" id="montoacobrarporcuota"></p>
							</div>
							<div class="form-group">
								<label for="recipient-name" class="control-label">Nº de Boleta:</label>
								<input type="text" class="form-control" value="" id="nboleta">
							</div>
							<div class="form-group">
								<div class="checker" id="uniform-inlineCheckbox1">
									<span><input type="checkbox" id="verificadodatos" value="1"></span>
								</div>
								He verificado que todos los datos son correctos
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doBorrarPago" class="btn btn-danger">
					Borrar Pago</button>
				<button type="button" id="doPagar" class="btn btn-primary">Pagar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" style="z-index: 9950;"
	data-tituloform="<?php echo $formularios['titulo']; ?>"
	id="sorteoindividualmodal" tabindex="-1" role="dialog"
	aria-labelledby="Sorteos">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">
					<div class="settings" style="margin: 5px 0">

						<div class="items">

							<p>Tenga en cuenta que una vez sorteado no podrá volver a
								sortear, por favor verifique bien los datos</p>

							<div class="form-group">
								<label for="recipient-name" class="control-label">Cantidad a
									sortear:</label> <input type="text"
									class="form-control onlynumber" value="1" id="cantidadplazas">
							</div>

							<div class="form-group">
								<label for="recipient-name" class="control-label">Porcentaje de
									descuento (%):</label> <input type="text"
									class="form-control onlynumber" value="0"
									id="porcentajedescuento">
							</div>

							<div class="form-group">
								<label for="message-text" class="control-label">Mensaje que
									llegará por mail:</label>
								<textarea class="form-control" style="height: 200px;"
									id="message-text"></textarea>
								<textarea style="display: none;" id="textooriginal"><?php echo $this->config->item('mensaje_beca_sorteado')?></textarea>
							</div>
							<div class="form-group">
								<div class="checker" id="uniform-inlineCheckbox1">
									<span><input type="checkbox" id="verificadodatos" value="1"></span>
								</div>
								He verificado que todos los datos son correctos
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doSortear" class="btn btn-primary">
					Sortear</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="notamodal" tabindex="-1" role="dialog"
	aria-labelledby="Notas">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Notas</h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">
					<div class="settings" style="margin: 5px 0">

						<div class="items">
							<h4 class="title">Agregar Notas al registro</h4>
							<br>
							<p>Puede destacar con un color y agregar una nota al registro</p>

							<div class="form-group">
								<label for="message-text" class="control-label">Copiar color:</label>
								<div id="notayautilizado"></div>
							</div>

							<div class="form-group">
								<label for="notacolor" class="control-label">Color:</label>
								<div id="colorpickernota" class="inl-bl"></div>
							</div>

							<div class="form-group">
								<label for="notatexto" class="control-label">Texto:</label>
								<textarea class="form-control" name="notatexto" style="height: 100px;"></textarea>
							</div>

						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doNota" class="btn btn-primary">Guardar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="becamodal" tabindex="-1" role="dialog"
	aria-labelledby="Sorteos">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Sorteos</h4>
			</div>
			<div class="modal-body">
				<div class="pop-dialog full">
					<div class="settings" style="margin: 5px 0">

						<div class="items">
							<h4 class="title">Siguientes sorteos disponibles</h4>
							<br>
							<p>
								Deberá realizar los sorteos uno a uno.<br>Tenga en cuenta que
								pueden no estar en órden, usted deberá realizarlos según su
								prioridad
							</p>
							<div id="camposbeca"></div>

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


<div class="modal fade" id="exportarModal" tabindex="-1" role="dialog"
	aria-labelledby="Exportar">
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

				foreach ( $camposExportar as $campo ) {

					echo '<div class="item">
								<i class="icon-reorder"></i>
								' . $campo ['label'] . '
								<input type="checkbox" class="camposExportar check" data-id="' . $campo ['id'] . '" checked="checked" />
							</div>';
				}

				?>


			<br>
							<h4 class="title">Filtros</h4>
							<p>Exportar aplicando los siguientes filtros</p>

							<div class="item">
								Todos <input type="radio" name="filtros" value="todos" class="check" checked="checked" />
							</div>

							<div class="item">
								Habilitados <input type="radio" name="filtros" value="solohabilitados" class="check" />
							</div>

							<div class="item">
								No Habilitados <input type="radio" name="filtros" value="nohabilitados" class="check" />
							</div>

							<div class="item">
								Pagos <input type="radio" name="filtros" value="solopagos" class="check" />
							</div>

							<div class="item">
								No Pagos <input type="radio" name="filtros" value="nopagos" class="check" />
							</div>

							<div class="item">
								Con beca <input type="radio" name="filtros" value="conbeca" class="check" />
							</div>

							<div class="item">
								Sin beca <input type="radio" name="filtros" value="sinbeca" class="check" />
							</div>

              <div class="item">
                Sorteados <input type="radio" name="filtros" value="sorteados" class="check" />
              </div>

						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="doExport" class="btn btn-primary">
					<i class="icon-download-alt"></i> Exportar
				</button>
			</div>
		</div>
	</div>
</div>

<div class="content container-fluid">

	<div id="pad-wrapper" class="users-list">
		<div class="row header">
			<h3 style="margin-bottom: 20px;"><?php echo $formularios['titulo']; ?></h3>

			<span style="display: block; width: 100%; font-size: 14px;"> <b>Comienzo de curso</b>
					<?php if ($formularios ['fechacomienzocurso'] != null)
						echo $formularios ['fechacomienzocurso'];
					else
						echo ' - '?>


      </span>
      <span style="display: block; width: 100%; font-size: 14px;"> <b>Estado</b>

                	<?php
																	$finalizado = false;

																	if ($formularios ['publicado']) {

																		if (date ( 'Y-m-d H:m:s', strtotime ( $formularios ['fechainicio'] ) ) <= date ( 'Y-m-d H:m:s', time () ) && date ( 'Y-m-d H:m:s', strtotime ( $formularios ['fechafin'] ) ) > date ( 'Y-m-d H:m:s', time () )) {
																			echo '<span class="label label-danger">En ejecución</span>';
																		} else {

																			if (date ( 'Y-m-d H:m:s', strtotime ( $formularios ['fechafin'] ) ) <= date ( 'Y-m-d H:m:s', time () )) {
																				echo '<span class="label label-warning">Finalizado</span>';
																				$finalizado = true;
																			} else {
																				echo '<span class="label label-success">Publicado</span>';
																			}
																		}
																	} else {

																		echo '<span class="label label-info">Borrador</span>';
																	}

																	?>


                </span>

                <?php if ($formularios['abonar'] == 1){?>


        <script type="text/javascript">
						/*DECLARAMOS LA VARIABLE PARA PODER UTILIZAR LOS VALORES LUEGO*/
						var GLOBALcostocurso ='<?php echo $formularios['costocurso'];?>';
						var GLOBALmonedacostocurso ='<?php echo $formularios['monedacostocurso'];?>';

        </script>


			<span style="display: block; width: 100%; font-size: 14px;"><b>Fecha
					de abono:</b> <?php echo $formularios['fechaabonoinicio'].' a '.$formularios['fechaabonofin'];  ?></span>
			<span style="display: block; width: 100%; font-size: 14px;"><b>Costo:</b> <?php echo $formularios['monedacostocurso'].' '.$formularios['costocurso'];  ?></span>



                <?php

} else {
																	?>
                	<span
				style="display: block; width: 100%; font-size: 14px;"><b> - No se
					debe abonar -</b></span>
               <?php
																}
																?>


                 <?php

if ($formularios ['cantidad'] != 0) {

																		echo '<span style="display:block;width:100%;font-size:14px;padding:10px;padding-left:0px;"><b>Cupos restantes: ' . ($formularios ['cantidad'] - $formularios ['cantidadRegistros']) . '</b></span>';
																	}
																	?>

            </div>

		<div class="table-wrapper orders-table section" style="min-height: 650px;">
			<div class="col-md-12">
				<div class="filter-block">
					<div class="pull-left">
            <?php if (($this->config->item('sorteoplaza_habilitado') == true) && $this->auth->tieneAcceso('registros_sorteo_plaza',true) && $this->auth->tengoPermisoDeCompanero('registros_sorteo_plaza',$formularios['id_usuario']) ){ ?><a
							class="btn btn-success" id="sorteoplaza"
							<?php if($formularios['f_sorteo_plaza'] != NULL){ echo 'disabled = "disabled"'; } ?>
							data-toggle="modal" data-target="#sorteoplazamodal"
							style="padding: 3px 6px;">Sortear Plazas</a><?php } ?>
                    	<?php if ($this->auth->tieneAcceso('registros_sorteo_beca',true) && $this->auth->tengoPermisoDeCompanero('registros_sorteo_beca',$formularios['id_usuario'])){ ?><a
							class="btn btn-warning" id="sorteobeca"
							<?php if($formularios['f_sorteo_plaza'] == NULL){ echo 'disabled = "disabled"'; } ?>
							data-toggle="modal" data-target="#becamodal"
							style="padding: 3px 6px;">Sorteos</a><?php }  ?>
                    	<?php if ($this->auth->tieneAcceso('registros_habilitar_tarde',true) && $this->auth->tengoPermisoDeCompanero('registros_habilitar_tarde',$formularios['id_usuario']) && $finalizado && ($formularios['fechacomienzocurso'] != null)){ ?><a
							style="padding: 3px 6px;" class="btn btn-info"
							id="inscripciontardia" data-toggle="modal"
							data-target="#habilitartardemodal"><i class="icon-time"></i>
							Habilitar inscripción tardía</a><?php }  ?>
          </div>
					<div class="pull-right">

  					<?php if ($this->auth->tengoPermisoDeCompanero('registros_exportar', $formularios['id_usuario'])){ ?>
              <a id="exportarbtn" class="btn btn-default" data-toggle="modal" data-target="#exportarModal" style="padding: 3px 6px;">
                <i class="icon-download-alt"></i> Exportar
              </a>
            <?php } ?>

            <div class="pull-right total">
							<b>Total:</b>
						</div>
						<input type="text" class="search" placeholder="Buscar..">
					</div>
				</div>

			</div>
			<div class="col-md-12">
				<table id="listado" class="table table-hover">
					<thead>
						<tr>

							<th class=" sortable">STATUS</th>
							<th class=" sortable">ID</th>

							<?php
							if (isset ( $camposmostrar ) and ! empty ( $camposmostrar ))
								foreach ( $camposmostrar as $nombre ) {
									echo ' <th class="hidden-xs hidden-sm sortable">' . $nombre . '</th>';
								}
							?>
              <th class="hidden-xs hidden-sm sortable "><span class="line"></span>Fecha de Registro</th>
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

<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="http://dev.localhost//js/jquery.quicksearch.js"></script>
<script src="http://dev.localhost/js/jquery.smartpaginator.js"></script>
