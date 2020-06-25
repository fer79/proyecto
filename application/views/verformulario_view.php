
<script type="text/javascript">
	$(function() {
        
     });

</script>

<div class="content">

	<h1><?php echo $ret['titulo']; ?></h1>
		
		<?php echo $this->breadcrumbs->show(); ?>
	 <div class="colwrapper">
		<div class="colleft">
		  	<?php echo $ret['colizquierda'];?>
	       </div>

		<div class="colmid">
	      <?php if ($ret['tipo'] != 'evaluacion'){ ?>
				<div class="box infobox">
				¿Sabías que: Al ingresar tus datos de Autorelleno en la sección <a
					class="irmicuenta"
					href="<?php echo base_url('adminpanel/micuenta'); ?>"><i
					class="fa fa-link" aria-hidden="true"></i> Mi Cuenta</a> se
				cargarán automáticamente en todos los formularios que vayas a
				completar?
			</div>
			<?php }else{ ?>
						<div class="box warningbox">
				Todas las evaluaciones realizadas en este sistema son <b>absolutamente
					anónimas</b>
			</div>
	
			<?php } ?>			
				<?php echo $formulario; ?>
		  
		  </div>
		<div class="colright">
	       <?php echo $ret['colderecha'];?>
	      </div>
	</div>
</div>
<div class="footer"></div>
</div>
<script>
$(document).ready(function(){
	
	$('.irmicuenta').click(function(e){
		
		if (!confirm('No has enviado tu formulario. Deseas salir de todas formas?  '))
			e.preventDefault();
	});

	$( "#formulario" ).submit(function( event ) {
	  	
	  	var error = false;

	  	$('input[type="file"]').each(function(){

	  		if (this.files[0] != null){
		  		var file_size = this.files[0].size/1024/1024;

		  		if (file_size > 2){
		  			error = true;
		  		}
		  	}
		    
		})
	  	
	  	if (error){
	  		alert('El tamaño máximo permitido para adjuntar archivos es de 10MB. Revisa los archivos que adjuntaste y vuelve a intentarlo.');
	  		event.preventDefault();
      		return false; 
	  	}

	});


	$('input[type="file"]').bind('change', function() {
        	var file_size = this.files[0].size/1024/1024;
        	if (file_size > 10){
        		var msg = 'El tamaño máximo permitido para adjuntar archivos es de 10MB. ' + 
            			  'El tamaño del archivo que estás intentando adjuntar es de ' + Math.round(file_size) + 'MB.';
            	alert(msg);
        	}
    });
	
});
</script>