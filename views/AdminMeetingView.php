<?php 
/**
 * 
 */
class AdminMeetingView
{
	public static function business_round_meeting_config()
		{

			$brdb = new BRCalendar;

			?>

				<h1>Reuniones</h1>
				<div class="container">
					<br>
					<h1>Gesti&oacuten de Participantes</h1>
					<div class="list-br-meetings" >
						<form class="form-style" id="filter">
							<label >Buscar </label>
							<input placeholder="Ingrese datos para filtrar..." type="filter" name="meetingFilter" id="meetingFilter">
						</form>
						<?php 
							$meetings = $brdb->userMeetings();
							
							?> 
							<table id="br-meetings" class="widefat" cellspacing="0"> 
								<thead>
									<tr>
										<th class="delete-row;"></th>
									    <th >Nombre y Apellido Solicita</th>
									    <th >Nombre y Apellido Acepta</th>
									    <th >Fecha y Hora</th>
									    <th >Estado</th>
									</tr>
								</thead>
							<tbody id="br-meetings-table">
							<?php
							foreach ( $meetings as $meeting) {
								?>

									<tr id="row-meeting-<?php echo $meeting->id; ?>">
										<td class="delete-row">
											<a href="#" style="text-align: center;" id="<?php echo 'meeting-' . $meeting->id . '-' . $meeting->apellido_solicita . '-'. $meeting->apellido_acepta; ?>" class="btn-delete-meeting">
												<span class="dashicons dashicons-trash"></span>
											</a>
										</td>
										<td class="attrID-Solicita" value="<?php echo $meeting->id_solicita; ?>" >
											<?php echo $meeting->nombre_solicita . ' ' . $meeting->apellido_solicita; ?>
										</td>
										<td class="attrID-Acepta" value="<?php echo $meeting->id_acepta; ?>" >
											<?php echo $meeting->nombre_acepta . ' ' . $meeting->apellido_acepta; ?>
										</td>
										<td class="" value="<?php echo $meeting->fecha_hora; ?>" ><?php echo $meeting->fecha_hora; ?>
										</td>
										<td class="" value="<?php echo $meeting->aceptado; ?>" ><?php echo ($meeting->aceptado == true ? 'Aceptado' : 'Aguardando respuesta'); ?>
										</td>
										
										
									</tr>

								<?php 
								}
							?>
							</tbody>
						</table>
					</div>
					<br>
					<br>
			<?php 
			return true;
		}
	}
 ?>