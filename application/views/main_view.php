<div class="content">
	<h1>Inscripciones y Evaluaciones Abiertas</h1>

	<?php	echo $this->breadcrumbs->show(); ?>


		<div class="colmid fullwidth">
			

			<?php
			
			foreach ( $cursosabiertos ['categorias'] as $key => $categoria ) {
				
				if (! empty ( $categoria ['hijos'] ))
					echo '<h2 class="separadorbreadcrumbs">Ir a</h2>';
				
				foreach ( $categoria ['hijos'] as $keyh => $hijo ) {
					echo '<div class="titulohijos"><a href="' . $hijo ['url'] . '" title="Ir a categoria ' . $hijo ['titulo'] . '">' . $hijo ['titulo'] . '</a></div>';
				}
				
				if ($this->input->get ( 'warning' ) == 'nohabilitado')
					echo '<div class="errorMsg warning">No habilitado para dicho formulario</div>';
				
				if ($this->input->get ( 'warning' ) == 'fueradeplazo')
					echo '<div class="errorMsg warning">Te encuentras fuera del plazo</div>';
				
				if ($this->input->get ( 'warning' ) == 'yainscripto')
					echo '<div class="errorMsg warning">Ya has completado el formulario</div>';
				
				echo '<h2>Formularios</h2>';
				
				if (empty ( $categoria ['formularios'] )) {
					echo '<span> - Sin formularios en la categoría -</span>';
				}
				
				foreach ( $categoria ['formularios'] as $curso ) {
					
					if ($curso ['inscripto']) {
						echo '<div class="cursolink">
							<a class="inscripcion" href="' . base_url () . 'adminpanel/micuenta/misinscripciones/" title="' . $curso ['titulo'] . '">' . $curso ['titulo'] . '</a>
							<span class="inscripcionrealizada" >[ Realizada ]</span></div>';
					} else {
						
						if ($curso ['cantidad'] > 0) {
							$restante = $curso ['cantidad'] - $curso ['cantidadRegistros'];
							
							if ($restante > 0) {
								
								echo '<div class="cursolink">
								<a class="inscripcion" href="' . $curso ['url'] . '" alt="' . $curso ['titulo'] . '" title="' . $curso ['titulo'] . '">' . $curso ['titulo'] . '<span style="    color: #333;
    font-size: 11px;
;"> ' . $restante . ' Lugares disponibles</span></a>
						
								<span ><b>Cierre</b> ' . $curso ['fecha_fin'] . '</span></div>';
							}
						} else {
							echo '<div class="cursolink">
							<a class="inscripcion" href="' . $curso ['url'] . '" alt="' . $curso ['titulo'] . '" title="' . $curso ['titulo'] . '">' . $curso ['titulo'] . '</a>
							<span ><b>Cierre</b> ' . $curso ['fecha_fin'] . '</span></div>';
						}
					}
				}
			}
			
			?>

	</div>
</div>
</div>
