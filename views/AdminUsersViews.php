<?php 
	/**
	 * 
	 */
	class AdminUsersViews
	{
		public static function business_round_admin_users_view()
		{
			$brdb = new BRUser;
			?>
				<div class="container">

					<?php if ( filter_input( INPUT_GET, 'new' ) === 'success' ) : ?>

					 <div class="notice notice-success is-dismissible">
					 	<h3>El Participante se ha creado correctamente.	</h3>
					 	<br>
					 </div>

					<?php endif ?>

					<?php if ( filter_input( INPUT_GET, 'new' ) === 'failed' ) : ?>

					 <div class="notice notice-error is-dismissible">
					 	<h3>Este nombre de usuario o correo electr&oacutenico ya est&aacute siendo utilizado por otro participante.	</h3>
					 	<p>Por favor seleccione otro nombre de usuario.</p>
					 	<br>
					 </div>

					<?php endif ?>

					<br>
					<h1>Gesti&oacuten de Participantes</h1>
					<div class="list-br-users" id="list-users-table" >
						<form class="form-style" id="filter">
							<label >Buscar</label>
							<input placeholder="Ingrese datos para filtrar..." type="filter" name="userFilter" id="userFilter">
						</form>
						<?php 
							$users = $brdb->getUser();
							
							?> 
							<table id="br-users" class="widefat fixed" cellspacing="0"> 
								<thead>
									<tr>
									    <th ></th>
									    <th >Nombre</th>
									    <th >Apellido</th>
									    <th >Tipo Participante</th>
									    <th >Empresa</th>
									    <th >Telefono</th>
									    <th >Ciudad</th>
									    <th >Provincia</th>
									    <th >DNI</th>
									    <th >REPA</th>
									    <th >Borrar Participante</th>
									</tr>
								</thead>
							<tbody id="br-users-table">
							<?php

							foreach ( $users as $user) {
								?>
											<tr id="row-user-<?php echo $user->id_wp_user; ?>">
												<td class="attrID" value="<?php echo $user->id_wp_user; ?>" style="display: none;">
													<?php echo $user->id_wp_user; ?>
												</td>
												<td class="" > 
													<a href="#" class="button btnuser"><span class="dashicons dashicons-edit"></span></a> 
												</td>
												<td class="attrName" value="<?php echo $user->first_name; ?>" >
													<?php echo $user->first_name; ?>
													
												</td>
												<td class="attrLastname" value="<?php echo $user->last_name; ?>" >
													<?php echo $user->last_name; ?>
														
												</td>
												<td class="attrTipo" value="<?php echo $user->tipo_participante; ?>" >
													<?php echo $user->tipo_participante; ?>
												</td>
												<td class="attrEmpresa" value="<?php echo $user->empresa; ?>" >
													<?php echo $user->empresa; ?>
													
												</td>
												<td class="attrTel" value="<?php echo $user->telefono; ?>" >
													<?php echo $user->telefono; ?>
													
												</td>
												<td class="attrCiudad" value="<?php echo $user->ciudad; ?>" >
													<?php echo $user->ciudad; ?>
													
												</td>
												<td class="attrProvincia" value="<?php echo $user->provincia; ?>" >
													<?php echo $user->provincia; ?>
													
												</td>
												<td class="attrDni" value="<?php echo $user->dni; ?>" >
													<?php echo $user->dni; ?>
													
												</td>
												<td class="attrRepa" value="<?php echo $user->repa; ?>" >
													<?php echo $user->repa; ?>
													
												</td>
												
												<td class="attrEmail" value="<?php echo $user->email; ?>" style="display: none;">
													<?php echo $user->email; ?>
													
												</td>
												<td class="attrRol" value="<?php echo $user->rol; ?>" style="display: none;">
													<?php echo $user->rol; ?>
												</td>
												<td class="attrProyecto" value="<?php echo $user->proyecto; ?>" style="display: none;">
													<?php echo $user->proyecto; ?>
												</td>
												<td><a href="#" style="background-color: #ff5656; color: white;" class="button pull-right btn-delete-user" id="delete-user-<?php echo $user->id_wp_user; ?>">X</a></td>
											
											</tr>

								<?php
							} 
						?>
							</tbody>
						</table>
						<br>
						<br>
						<a href="#" id="nuevo-user-btn" class="button btn-primary" style="margin-left: 10px; background-color: #32373c; color: white;">Agregar Participante</a>
					</div>
					<br>
					<br>

						<div class="form-style" id="userForm" style="display: none">
							<h2>Modificar</h2>
							<form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" id="updateUserForm">
								<input type="hidden" name="action" value="update_user">
								<input type="hidden" name="id_wp_user" value="" id="id_wp_user">
								
								<label for="nombre">Nombre</label>
								<input data-validation="length" data-validation-length="min3" type="text" name="nombre" id="nombre">
								
								<label for="apellido">Apellido</label>
								<input data-validation="length" data-validation-length="min3" type="text" name="apellido" id="apellido">
								
								<label for="tipo_participante">Tipo Participante</label>
								<select name="tipo_participante" id="tipo_participante">
								  <option value="Player">Player</option>
								  <option value="Productor">Productor</option>
								</select>
								
								<?php $tipo = ($user->tipo_participante == 'Productor' ? 'Proyecto' : 'Empresa') ?>
								<label for="empresa" id="lblEmpresa1"><?php echo $tipo ?></label>
								<input data-validation="length" data-validation-length="min2" type="text" name="empresa" id="empresa">
								
								<label for="telefono">Telefono</label>
								<input data-validation="number" type="text" name="telefono" id="telefono">
								
								<label for="ciudad">Ciudad</label>
								<input data-validation="length" data-validation-length="min4" type="text" name="ciudad" id="ciudad">
								
								<label for="provincia">Provincia</label>
								<input data-validation="length" data-validation-length="min4" type="text" name="provincia" id="provincia">
								
								<label for="dni">DNI</label>
								<input data-validation="number" type="text" name="dni" id="dni">
								
								<label for="repa">REPA</label>
								<input type="text" name="repa" id="repa">
																
								<label for="email">Email</label>
								<input data-validation="email" data-validation-length="min4" type="text" name="email" id="email">
								
								<label for="rol">Rol</label>
								<input type="text" name="rol" id="rol">
								<label for="proyecto">Proyecto</label>
								<input type="text" name="proyecto" id="proyecto">
					
								<a class="button button-cancel" href="<?php echo admin_url( 'admin.php?page=Mercado' ); ?>" >
									<div>
										Cancelar
									</div>
								</a>
								<input type="submit" name="" id="btnsave"/>
							</form>
							
						</div>

						<div class="form-style" id="newUserForm" style="display: none">
							<h2>Modificar</h2>
							<form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" id="updateUserForm">
								<input type="hidden" name="action" value="create_new_user">
								<input type="hidden" name="id_wp_user" value="" id="id_wp_user">
								
								<label for="nombre">Nombre</label>
								<input data-validation="length" data-validation-length="min3" type="text" name="nombre" id="nombre">
								
								<label for="apellido">Apellido</label>
								<input data-validation="length" data-validation-length="min3" type="text" name="apellido" id="apellido">

								<br>
								<br>
								<br>
								
								<label for="usuario">Usuario</label>
								<input data-validation="length" data-validation-length="min3" type="text" name="usuario" id="usuario">

								<label for="password">Contrase&ntildea</label>
								<input name="password" data-validation="length" data-validation-length="min8" type="password" id="password-user">
								<a href="#" style="margin-left: 160px;" id="show-pass-user">Mostrar</a>
								<a href="#" class="pull-right" id="gen-pass-user">Generar Contrase&ntildea</a>
								<br>
								<br>
								<br>

								
								<label for="tipo_participante">Tipo Participante</label>
								<select name="tipo_participante" id="tipo_participante">
								  <option value="Player">Player</option>
								  <option value="Productor">Productor</option>
								</select>
								
								<?php $tipo = ($user->tipo_participante == 'Productor' ? 'Proyecto' : 'Empresa') ?>
								<label for="empresa" id="lblEmpresa2"><?php echo $tipo ?></label>
								<input data-validation="length" data-validation-length="min2" type="text" name="empresa" id="empresa">
								
								<label for="telefono">Telefono</label>
								<input data-validation="number" type="text" name="telefono" id="telefono">
								
								<label for="ciudad">Ciudad</label>
								<input data-validation="length" data-validation-length="min4" type="text" name="ciudad" id="ciudad">
								
								<label for="provincia">Provincia</label>
								<input data-validation="length" data-validation-length="min4" type="text" name="provincia" id="provincia">
								
								<label for="dni">DNI</label>
								<input data-validation="number" type="text" name="dni" id="dni">
								
								<label for="repa">REPA</label>
								<input type="text" name="repa" id="repa">
																
								<label for="email">Email</label>
								<input data-validation="email" data-validation-length="min4" type="text" name="email" id="email">
								
								<label for="rol">Rol</label>
								<input type="text" name="rol" id="rol">
								<label for="proyecto">Proyecto</label>
								<input type="text" name="proyecto" id="proyecto">
					
								<a class="button button-cancel" href="<?php echo admin_url( 'admin.php?page=Mercado' ); ?>" >
									<div>
										Cancelar
									</div>
								</a>
								<input type="submit" name="" id="btnsave"/>
							</form>
							
						</div>
				</div>
			<?php	
			
			return true;	
		}
		
	}
 ?>