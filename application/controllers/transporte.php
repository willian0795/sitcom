<?php
define ("SISTEMA","5");
define ("APROBADOR_AUTOMATICO",39);
class Transporte extends CI_Controller
{
    
    function __construct()
	{
        parent::__construct();
		date_default_timezone_set('America/El_Salvador');
		$this->load->model('transporte_model');
		$this->load->model('seguridad_model');
		$this->load->model('usuario_model');
		$this->load->library("mpdf");
    	if(!$this->session->userdata('id_usuario')) {
    		$_SESSION['url']="http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']; //para poder redireccionar 
			redirect('index.php/sessiones');
		}
    }
	
	function index()
	{
		$this->solicitud();
  	}
	
	/*
	*	Nombre: solicitud
	*	Objetivo: Carga la vista para la creacion del solicitudes de transporte
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna.
	*/
	function solicitud($estado_transaccion=NULL,$id_solicitud=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),65); /*Verificacion de permiso para crear solicitudes*/
		$data['id_modulo']=65;
		$band=1;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
					$data['empleados']=$this->transporte_model->consultar_empleado($this->session->userdata('nr'));
					foreach($data['empleados'] as $val) {
						$band=0;
						$data['info']=$this->transporte_model->info_adicional($val['NR']);
					}
					break;
				case 2:
					$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
					$data['empleados']=$this->transporte_model->consultar_empleados_seccion($id_seccion['id_seccion']);
					break;
				case 3:
					$data['empleados']=$this->transporte_model->consultar_empleados();
					break;
				case 4:
					$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
					if($id_seccion['id_seccion']==52 || $id_seccion['id_seccion']==53 || $id_seccion['id_seccion']==54 || $id_seccion['id_seccion']==55 || $id_seccion['id_seccion']==56 || $id_seccion['id_seccion']==57 || $id_seccion['id_seccion']==58 || $id_seccion['id_seccion']==59 || $id_seccion['id_seccion']==60 || $id_seccion['id_seccion']==61 || $id_seccion['id_seccion']==64 || $id_seccion['id_seccion']==65 || $id_seccion['id_seccion']==66) {
						$data['empleados']=$this->transporte_model->consultar_empleados_seccion($id_seccion['id_seccion']);	
						
					}
					else {
						$data['empleados']=$this->transporte_model->consultar_empleados_depto();
					}

					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			$data['solicitud']=$this->transporte_model->consultar_solicitud($id_solicitud,"1,2,0");

			if($band)	
				$data['info']=$this->transporte_model->info_adicional($data['solicitud']['id_empleado_solicitante']);
			$data['solicitud_destinos']=$this->transporte_model->consultar_destinos($data['solicitud']['id_solicitud_transporte']);
			$data['solicitud_acompanantes']=$this->transporte_model->acompanantes_internos($data['solicitud']['id_solicitud_transporte']);
			$data['acompanantes']=$this->transporte_model->consultar_empleados($this->session->userdata('nr'), $id_solicitud);
			$data['municipios']=$this->transporte_model->consultar_municipios();
			pantalla('transporte/solicitud',$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}
	
	/*
	*	Nombre: control_solicitudes
	*	Objetivo: Control 
	*	Hecha por: Jhonatan
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function control_solicitudes($estado_transaccion=NULL,$accion=NULL)
	{
 		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),66);
 		$data['id_modulo']=66;
		if(isset($data['id_permiso'])&&$data['id_permiso']>1) {
				$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
				$id_seccion_val=$id_seccion['id_seccion'];
				
				$id_empl=$this->transporte_model->consultar_empl($this->session->userdata('nr'));
				
				$id_empleado=$id_empl[0][id_empleado];
				$ministro=$this->transporte_model->consultar_cargo($id_empleado);

				if($ministro[0][funcional]=='MINISTRO') $M=1;
				else $M=0;

				switch ($data['id_permiso']) {
					case 2:
						/*echo "<script type='text/javascript'> alert('M=".$M." Permiso=".$data['id_permiso']." id_empleado=".$id_empleado."');</script>";*/
						if($M==1) $data['datos']=$this->transporte_model->solicitudes_por_seccion_estado($id_seccion_val,1,0);
						else $data['datos']=$this->transporte_model->solicitudes_por_seccion_estado($id_seccion_val,1,$id_empleado);			
						break;
					case 3:
						if($M==1) $data['datos']=$this->transporte_model->todas_solicitudes_por_estado(1,0);
						else $data['datos']=$this->transporte_model->todas_solicitudes_por_estado(1,$id_empleado);
						break;
					case 4:
							$departamental=$this->transporte_model->is_departamental($id_seccion_val);


						if($departamental){
							if($M==1)$data['datos']=$this->transporte_model->solicitudes_por_seccion_estado($id_seccion_val,1,0);
							else $data['datos']=$this->transporte_model->solicitudes_por_seccion_estado($id_seccion_val,1,$id_empleado);
								
						}else{/// para san salvador
							if($M==1) $data['datos']=$this->transporte_model->todas_solicitudes_sanSalvador(1,0);
							else $data['datos']=$this->transporte_model->todas_solicitudes_sanSalvador(1,$id_empleado);
						}

						break;
				}
				
				$data['estado_transaccion']=$estado_transaccion;
				if($accion==0)
					$data['accion']="denega";
				if($accion==2)
					$data['accion']="aproba";					 
				pantalla('transporte/ControlSolicitudes',$data);
		}
		else 
		{
			echo "No tiene permisos para acceder";
		}
	}
	
	/*
	*	Nombre: datos_de_solictudes
	*	Objetivo: Mostrar informacion General de una mision, a fin de que el Jefe de Unidad o Departamento tenga una aplia vision para aprobar o denegar un solicitud
	*	Hecha por: Jhonatan
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Cargada mediante Ajax desde la pantalla de control de solicitudes
	*/
	function datos_de_solicitudes($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),66);
		if(isset($data['id_permiso'])&& $data['id_permiso']>=2 ) {
			$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
			$datos['d']=$this->transporte_model->datos_de_solicitudes($id, $id_seccion['id_seccion']);
			$datos['a']=$this->transporte_model->acompanantes_internos($id);
			$datos['f']=$this->transporte_model->destinos($id);
			$datos['observaciones']=$this->transporte_model->observaciones($id);
			$datos['id']=$id;
			$this->load->view('transporte/dialogoAprobacion',$datos);
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}
	
	/*
	*	Nombre: aprobar _solicitud
	*	Objetivo: Registrar la aprobacion hecha por un Jefe de Unidad o Departamento
	*	Hecha por: Jhonatan
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function aprobar_solicitud()
	{
		
		$data['permiso']=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),66);
		if($data['permiso']>=2 && $data['permiso']!=NULL){
			$this->db->trans_start();
			$id=$this->input->post('ids'); //id solicitud
			$estado=$this->input->post('resp'); //estado de la solicitud
			$descrip=$this->input->post('observacion'); //Observacion
			
			$id_solicitud_transporte=$id;
			if($estado ==2 || $estado== 0)
			{
				$this->transporte_model->aprobar($id,$estado, $this->session->userdata('id_usuario'));
				if($descrip!="")
					$this->transporte_model->insertar_descripcion($id,$descrip,2);
				$this->db->trans_complete();
				$tr=($this->db->trans_status()===FALSE)?0:1;
				
				if($tr==1 && $estado==2)
				{
					#robertohqz@gmail.com
					# Se comenta para evitar notificaciones por correo al jefe de transporte
					# de acuerdo a su solicitud.
					#enviar_correo_automatico_administracion($id_solicitud_transporte,68);
				}
				enviar_correo_automatico_usuarios($id_solicitud_transporte);
				
				/********************************************FUNCI�N DE BIT�CORA************************************************/
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$id_user=$this->session->userdata('id_usuario');
				$user=$this->session->userdata('usuario');
				if($estado==2)
					$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." aprob� la solicitud de transporte con ID ".$id_solicitud_transporte,4);
				else if ($estado==0)
					$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." deneg� la solicitud de transporte con ID ".$id_solicitud_transporte,4);
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				ir_a("index.php/transporte/control_solicitudes/".$tr."/".$estado);
			}
			else {
				$this->db->trans_complete();
				ir_a("index.php/transporte/control_solicitudes/0/0");
			}
		}
		else {
			echo 'No tiene permisos para acceder';
		}	
		
	}
	
	/*
	*	Nombre: asignar_vehiculo_motorista
	*	Objetivo: Carga la vista de Asignaciones de veh�culos y Motoristas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function asignar_vehiculo_motorista($estado_transaccion=NULL,$accion=NULL)
	{
		



		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),68);
		$data['id_modulo']=68;
		if($data['id_permiso']!=NULL) {
			
			if($data['id_permiso']==2) {// para solicitudes locales
				$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
				$data['datos']=$this->transporte_model->solicitudes_por_asignar($id_seccion['id_seccion']);	
				$data['estado_transaccion']=$estado_transaccion;
				pantalla('transporte/asignacion_veh_mot',$data);
			}
			else if($data['id_permiso']==3) { // Para solicitudes nacionales
				$data['datos']=$this->transporte_model->todas_solicitudes_por_asignar();	
				$data['estado_transaccion']=$estado_transaccion;
				pantalla('transporte/asignacion_veh_mot',$data);
			}
			else if($data['id_permiso']==4) { // Para solicitudes departamentales
				$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
				if($id_seccion['id_seccion']==52 || $id_seccion['id_seccion']==53 || $id_seccion['id_seccion']==54 || $id_seccion['id_seccion']==55 || $id_seccion['id_seccion']==56 || $id_seccion['id_seccion']==57 || $id_seccion['id_seccion']==58 || $id_seccion['id_seccion']==59 || $id_seccion['id_seccion']==60 || $id_seccion['id_seccion']==61 || $id_seccion['id_seccion']==64 || $id_seccion['id_seccion']==65 || $id_seccion['id_seccion']==66) /*Oficina Departamental*/
				{
					$data['datos']=$this->transporte_model->solicitudes_por_asignar($id_seccion['id_seccion']);	
					$data['estado_transaccion']=$estado_transaccion;
				}
				else /*San Salvador*/
				{
					$data['datos']=$this->transporte_model->solicitudes_por_asignar_depto();	
					$data['estado_transaccion']=$estado_transaccion;
				}
				$data['estado_transaccion']=$estado_transaccion;
				if($accion==0)
					$data['accion']="denega";
				if($accion==3)
					$data['accion']="aproba";					 
				

				pantalla('transporte/asignacion_veh_mot',$data);
			}else{ //id_permiso 1
					echo "No tiene permisos para acceder";
			}
			
		}
		else
		{
			echo "No tiene permisos para acceder";
		}
	}

	/*
	*	Nombre: cargar_datos_solicitud
	*	Objetivo: Funci�n para cargar datos de solicitudes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 16/07/2014
	*	Observaciones: Ninguna
	*/
	function cargar_datos_solicitud($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),68);
		if($data['id_permiso']!=NULL) {
			if($data['id_permiso']>2) {
				$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
				$d=$this->transporte_model->datos_de_solicitudes($id, $id_seccion['id_seccion']);
				$a=$this->transporte_model->acompanantes_internos($id);
				$f=$this->transporte_model->destinos($id);
				$observaciones=$this->transporte_model->observaciones($id);
				
				$solicitud_actual=$this->transporte_model->consultar_fecha_solicitud($id);
				//////////consulta la fecha, hora de entrada, y hora de salida de la solicitud actual, para luego compararla con otras solicitudes ya aprobadas.					
				foreach($solicitud_actual as $row) {
					$fecha=$row->fecha;
					$entrada=$row->entrada;
					$salida=$row->salida;
				}
				
				if(($data['id_permiso']==2 || $data['id_permiso']==4) && ($id_seccion['id_seccion']==52 || $id_seccion['id_seccion']==53 || $id_seccion['id_seccion']==54 || $id_seccion['id_seccion']==55 || $id_seccion['id_seccion']==56 || $id_seccion['id_seccion']==57 || $id_seccion['id_seccion']==58 || $id_seccion['id_seccion']==59 || $id_seccion['id_seccion']==60 || $id_seccion['id_seccion']==61 || $id_seccion['id_seccion']==64 || $id_seccion['id_seccion']==65 || $id_seccion['id_seccion']==66))//Oficinas departamentales//
				{
					$vehiculos_disponibles=$this->transporte_model->vehiculos_disponibles($fecha,$entrada,$salida,$id_seccion['id_seccion']);
					/*aqu� se comparan la fecha, hora de entrada y de salida de la solicitud actual con las que ya tiene veh�culo asignado, para mostrar �nicamente los posibles vehiculos a utilizar */
				}
				else if($data['id_permiso']==4)
				{
					$vehiculos_disponibles=$this->transporte_model->vehiculos_disponibles_central($fecha,$entrada,$salida);
					/*aqu� se comparan la fecha, hora de entrada y de salida de la solicitud actual con las que ya tiene veh�culo asignado, para mostrar �nicamente los posibles vehiculos a utilizar */
				}
				else if($data['id_permiso']==3)
				{
					$vehiculos_disponibles=$this->transporte_model->vehiculos_disponibles_nacional($fecha,$entrada,$salida);
					/*aqu� se comparan la fecha, hora de entrada y de salida de la solicitud actual con las que ya tiene veh�culo asignado, para mostrar �nicamente los posibles vehiculos a utilizar */
				}
								
				echo 
				"
				<form id='form' action='".base_url()."index.php/transporte/asignar_veh_mot' method='post'>
				<input type='hidden' id='resp' name='resp' />
				<input type='hidden' name='id_solicitud' value='".$id."' />
				
				<fieldset>      
					<legend align='left'>Informaci�n de la Solicitud</legend>
					";
					foreach($d as $datos)
					{
						$nombre=ucwords($datos->nombre);
						$seccion=ucwords($datos->seccion);
						$fechaS=$datos->fechaS;
						$fechaM=$datos->fechaM;
						$salida=$datos->salida;
						$entrada=$datos->entrada;
						$requiere=$datos->req;
						$acompanante=ucwords($datos->acompanante);
						$id_empleado=$datos->id_empleado_solicitante;
					}
				
				echo "Nombre: <strong>".$nombre."</strong> <br>
				Secci�n: <strong>".$seccion."</strong> <br>
				Fecha de solicitud: <strong>".$fechaS."</strong> <br>
				Fecha de misi�n: <strong>".$fechaM."</strong> <br>
				Hora de salida: <strong>".$salida."</strong> <br>
				Hora de regreso: <strong>".$entrada."</strong> <br>
				
				</fieldset>
				<br />";
	?>
    	<fieldset>
        <legend align='left'>Observaciones</legend>
		<?php
            if(count($observaciones)!=0)
            foreach($observaciones as $val) {
                switch($val['quien_realiza']) {
                    case 1:
                        $quien="Por parte del solicitante";
                        break;
                    case 2:
                        $quien="Por parte del Jefe de Departamento o Secci&oacute;n";
                        break;
                    case 3:
                        $quien="Por parte del Jefe de Servicios Generales";
                        break;
                    default:
                        $quien="General";
                }
                echo $quien.":<br><li><strong>".$val['observacion'].".</strong></li><br>";					
            }
            if(count($observaciones)==0)
                echo "<strong>(No hay observaciones)</strong>";
		?>
        </fieldset>
	<?php
            
    echo "<br />
				
				<fieldset>
				<legend align='left'>Destinos</legend>
				
				<table cellspacing='0' align='center' class='table_design'>
						<thead>
							<th>
								Municipio
							</th>
							<th>
								Lugar de destino
							</th>
							<th>
								Direcci�n
							</th>
							<th>
								Misi�n Encomendada
							</th>
						</thead>
						<tbody>
							";
							foreach($f as $r)
							{
								echo "<tr><td>".$r->municipio."</td>";
								echo "<td>".$r->destino."</td>";
								echo "<td>".$r->direccion."</td>";
								echo "<td>".$r->mision."</td></tr>";
							}
						echo "
						</tbody>
					</table>
				
				</fieldset>
				
			    <br />
			   
				<fieldset>
					<legend align='left'>Acompa�antes</legend>
					
					";
					foreach($a as $acompa)
					{
						echo "<strong>".ucwords($acompa->nombre)."</strong> <br />";
					}
					echo "<strong>".$acompanante."</strong>";
					if(count($acompa)==0 && $acompanante=="")
						echo "<strong>(No hay acompa&ntilde;antes)</strong>";
				echo "
				</fieldset>
				<br>
				<fieldset>
				<legend align='left'>Informaci&oacute;n del Veh�culo</legend>
					<p>
					<label>N&deg; Placa <font color='#FF0000'>*</font></label>
				   <select class='select' name='vehiculo' id='vehiculo' style='width:100px;' onchange='motoristaf(this.value,".$id.")'>
				   ";
				   
					foreach($vehiculos_disponibles as $v) {
						/*echo "<option value='".$v->id_vehiculo."'>".$v->placa." - ".$v->nombre." - ".$v->modelo."</option>";*/
						echo "<option value='".$v->id_vehiculo."'>".$v->placa."</option>";
					}
				   
				   echo "    
				   </select>
					</p>   
				</fieldset>
				<br>
				<fieldset>
					<legend align='left'>Informaci&oacute;n de Motorista</legend>
				";
				if($requiere==1) {
				echo "
						<label>Nombre <font color='#FF0000'>*</font></label>
						<div id='cont-select' style='width:350px; display:inline-block;'>
							<select name='motorista' id='motorista'>
							</select>
						</div>
					";
				}
				else {
					echo "Nombre: <strong>".$nombre."</strong>";
					echo "<input type='hidden' name='motorista' value='".$id_empleado."'>";
				}
				echo "
					</fieldset>
				 
       	<br>
		<fieldset>
			<legend align='left'>Informaci&oacute;n  Adicional</legend>
					<label for='observacion' id='lobservacion' class='label_textarea'>Observaci�n</label>
					<textarea class='tam-4' id='observacion' tabindex='2' name='observacion'/></textarea>
				</fieldset>
				<p style='text-align: center;'>
					<button type='submit' id='asignar' name='asignar' class='boton_validador' onclick='enviar(3)'>Asignar</button>&nbsp;&nbsp;&nbsp;
					<button type='submit' id='asignar' name='asignar' class='boton_validador' onclick='enviar(0)'>Denegar</button>
				</p>
				</form>
				";
				
				echo "<script>
					$('select').prepend('<option value=\"\" selected=\"selected\"></option>');
					$('.select').kendoComboBox({
						autoBind: false,
						filter: 'contains'
					});
					$('#vehiculo').validacion({
						men: 'Debe seleccionar un item'
					});";
				if($requiere==1)
					echo "$('#motorista').kendoComboBox({
							autoBind: false,
							filter: 'contains',
							enable: false
						});";

				echo "$('#observacion').validacion({
						req: false,
						lonMin: 10
					});
				</script>";
			}
		}
		else
		{
			echo 'No tiene permisos para acceder';
		}
	}
	
	/*
	*	Nombre: verificar_motoristas
	*	Objetivo: Funci�n para conocer el motorista que se ha de asignar a la misi�n oficial
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function verificar_motoristas($id_vehiculo,$id_solicitud_actual)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),65);
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
		if($data['id_permiso']!=NULL) {
			if($data['id_permiso']>=2) {
				$datos_actual=$this->transporte_model->consultar_fecha_solicitud($id_solicitud_actual);
				
				foreach($datos_actual as $da) {
					$fecha==$da->fecha;
					$salida==$da->salida;
					$entrada==$da->entrada;
				}
				
				$vehiculo_mision_local=$this->transporte_model->vehiculo_en_mision_local($fecha,$salida,$entrada,$id_vehiculo);
				
				if($vehiculo_mision_local!=0) { ///el vehiculo se encuentra en mision local
					echo "El veh�culo se encuentra reservado para esta hora";
				}
				
				if($id_seccion['id_seccion']==52 || $id_seccion['id_seccion']==53 || $id_seccion['id_seccion']==54 || $id_seccion['id_seccion']==55 || $id_seccion['id_seccion']==56 || $id_seccion['id_seccion']==57 || $id_seccion['id_seccion']==58 || $id_seccion['id_seccion']==59 || $id_seccion['id_seccion']==60 || $id_seccion['id_seccion']==61 || $id_seccion['id_seccion']==64 || $id_seccion['id_seccion']==65 || $id_seccion['id_seccion']==66)//Oficinas departamentales//
				{
					$motoristas=$this->transporte_model->consultar_motoristas($id_vehiculo,$id_seccion['id_seccion']);
					//////////consulta al motorista asignado al vehiculo.
					////////// y muestra los posibles motoristas de acuerdo a la oficina departamental
				}
				else if($data['id_permiso']==4)
				{
					$motoristas=$this->transporte_model->consultar_motoristas_central($id_vehiculo);
					//////////consulta al motorista asignado al vehiculo.
					////////// y muestra los posibles motoristas de acuerdo a la central
				}
				else if($data['id_permiso']==3)
				{
					$motoristas=$this->transporte_model->consultar_motoristas_nacional($id_vehiculo);
					//////////consulta al motorista asignado al vehiculo.
					////////// y muestra todos los posibles motoristas del mtps
				}
				
				$j=json_encode($motoristas);
				echo $j;
			}
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}

	/*
	*	Nombre: asignar_veh_mot
	*	Objetivo: Funci�n para registrar una asignaci�n de veh�culo con su correspondiente motorista
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	
	function asignar_veh_mot($estado_transaccion=NULL,$accion=NULL)
	{
		$data['permiso']=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),65);
		$empleado=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
		
		$id_empleado=$empleado['id_empleado'];
		
		if($data['permiso']!=NULL) {
			if($data['permiso']>=2) {
				$this->db->trans_start();
				$id_solicitud=$this->input->post('id_solicitud');//id_solicitud
				$id_vehiculo=$this->input->post('vehiculo'); //id_vehiculo
				$id_motorista=$this->input->post('motorista'); //estado de la solicitud
				$estado=$this->input->post('resp');//futuro estado de la solicitud
				$fecha_m=date('Y-m-d H:i:s');
				$nr=$this->session->userdata('nr'); //NR del usuario Logueado
				$observaciones=$this->input->post('observacion');//observaci�n, si es que hay
				
				if($estado==3) {
					$this->transporte_model->asignar_veh_mot($id_solicitud,$id_motorista,$id_vehiculo, $estado, $fecha_m,$nr, $id_empleado);
					
					if($observaciones!="") $this->transporte_model->insertar_descripcion($id_solicitud,$observaciones,3);
					$this->db->trans_complete();
					$tr=($this->db->trans_status()===FALSE)?0:1;

					if($tr==1)
					{
						/********************************************FUNCI�N DE BIT�CORA************************************************/
						/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						$id_user=$this->session->userdata('id_usuario');
						$user=$this->session->userdata('usuario');
						$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." asign� veh�culo/motorista, correspondiente a la solicitud de transporte con ID ".$id_solicitud,4);
						/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						enviar_correo_automatico_usuarios($id_solicitud);
					}
					
					ir_a("index.php/transporte/asignar_vehiculo_motorista/".$tr."/".$estado);
				}
				else if($estado==0)
				{
					$this->transporte_model->nasignar_veh_mot($id_solicitud, $estado, $fecha_m, $id_empleado);
					if($observaciones!="") $this->transporte_model->insertar_descripcion($id_solicitud,$observaciones,3);
					$this->db->trans_complete();
					$tr=($this->db->trans_status()===FALSE)?0:1;
						if($tr==1)
						{
							/********************************************FUNCI�N DE BIT�CORA************************************************/
							/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							$id_user=$this->session->userdata('id_usuario');
							$user=$this->session->userdata('usuario');
							$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." deneg� la asignaci�n de veh�culo/motorista, correspondiente a la solicitud de transporte con ID ".$id_solicitud,4);
							/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							enviar_correo_automatico_usuarios($id_solicitud);
						}
						
					ir_a("index.php/transporte/asignar_vehiculo_motorista/".$tr."/".$estado);
				}
				else {
					ir_a("index.php/transporte/asignar_vehiculo_motorista/0/0");
				}
			}
		}
		else {
			echo 'No tiene permisos para acceder';
		}	
	}


	/*
	*	Nombre: buscar_info_adicional
	*	Objetivo: Mostrar la informacion del puesto del empleado que necesita el transporte
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function buscar_info_adicional()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),65); /*Verificacion de permiso para crear solicitudes*/
		
		if($data['id_permiso']!=NULL) {
			$id_empleado=$this->input->post('id_empleado');
			$data=$this->transporte_model->info_adicional($id_empleado);
			if($data['nr']!='0') {
				$json =array(
					'estado'=>1,
					'nr'=>$data['nr'],
					'id_seccion'=>$data['id_seccion'],
					'funcional'=>$data['funcional'],
					'nominal'=>$data['nominal'],
					'nivel_1'=>$data['nivel_1'],
					'nivel_2'=>$data['nivel_2'],
					'nivel_3'=>$data['nivel_3']
				);
			}
			else {
				$json =array(
					'estado'=>0
				);
			}
			echo json_encode($json);
		}
		else {
			$json =array(
				'estado'=>0
			);
			echo json_encode($json);
		}
	}
	
	/*
	*	Nombre: guardar_solicitud
	*	Objetivo: Guardar el formulario de solicitud de transporte
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/


	function guardar_solicitud()
	{	
		

		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),65); /*Verificacion de permiso para crear solicitudes*/
		
		if($data['id_permiso']!=NULL)
		{
			if(verificarform($_POST)){  //para que no se vuelva a ejecutar la solicitud
					$this->db->trans_start();
					$modifica_solicitud=false;
					$id_solicitud_old=$this->input->post('id_solicitud_old');
					$estado=1;
					if($id_solicitud_old!="")
					{
						$modifica_solicitud=true;				

						$estado= $this->transporte_model->consultar_estado($id_solicitud_old);
						$estado=$estado[0]['estado'];

						if($estado==0)
						{
							$this->transporte_model->eliminar_solicitud($id_solicitud_old,FALSE);
							$id_solicitud_old="DEFAULT";
						}
						else
						{
							$this->transporte_model->eliminar_solicitud($id_solicitud_old,TRUE);
						}
					}
					else
					{
							$id_solicitud_old="DEFAULT";
					}
					
					$fec=str_replace("/","-",$this->input->post('fecha_mision'));
					$fecha_solicitud_transporte=date('Y-m-d');
					$id_empleado_solicitante=(int)$this->input->post('nombre');
					$fecha_mision=date("Y-m-d", strtotime($fec));
					$hora_salida=date("H:i:s", strtotime($this->input->post('hora_salida')));
					$hora_entrada=date("H:i:s", strtotime($this->input->post('hora_regreso')));
					$correo_cc=$this->input->post('correo_cc');
						if($this->input->post('requiere_motorista')!="")
							$requiere_motorista=$this->input->post('requiere_motorista');
						else
							$requiere_motorista=0;
						$observaciones=$this->input->post('observaciones');
						$acompanante=$this->input->post('acompanantes2');
						$id_usuario_crea=$this->session->userdata('id_usuario');
						$fecha_creacion=date('Y-m-d H:i:s');
						
						
						
						$formuInfo = array(
							'id_solicitud_transporte'=> $id_solicitud_old,
							'fecha_solicitud_transporte'=>$fecha_solicitud_transporte,
							'id_empleado_solicitante'=>$id_empleado_solicitante,
							'fecha_mision'=>$fecha_mision,
							'hora_salida'=>$hora_salida,
							'hora_entrada'=>$hora_entrada,
							'correo_cc'=>$correo_cc,
							'requiere_motorista'=>$requiere_motorista,
							'acompanante'=>$acompanante,
							'id_usuario_crea'=>$id_usuario_crea,
							'fecha_creacion'=>$fecha_creacion,
							'estado_solicitud_transporte'=>$estado
						);
							
						$id_solicitud_transporte=$this->transporte_model->guardar_solicitud($formuInfo); /*Guardando la solicitud*/
						$acompanantes=$this->input->post('acompanantes');
						for($i=0;$i<count($acompanantes);$i++)
						{
							$formuInfo = array(
								'id_solicitud_transporte'=>$id_solicitud_transporte,
								'id_empleado'=>$acompanantes[$i]
							);
							$this->transporte_model->guardar_acompanantes($formuInfo); /*Guardando acompa�antes*/
						}
						$destinos=$this->input->post('values');
						for($i=0;$i<count($destinos);$i++)
						{
							$campos=explode("**",$destinos[$i]);
							if(isset($campos[1]))
							{
								$formuInfo = array(
									'id_solicitud_transporte'=>$id_solicitud_transporte,
									'id_municipio'=>$campos[0],
									'lugar_destino'=>$campos[1],
									'direccion_destino'=>$campos[2],
									'mision_encomendada'=>$campos[3]

								);
						
								$this->transporte_model->guardar_destinos($formuInfo); /*Guardando destinos*/
							}
						}
					
					if($observaciones!="")
						$this->transporte_model->insertar_descripcion($id_solicitud_transporte,$observaciones, 1); /*Guardando observaciones*/
					
					$id_modul=66;
					$band=$this->usuario_model->get_rol(APROBADOR_AUTOMATICO,$id_usuario_crea);
					if($band==true)/*Si el usuario posee el rol de aprobador autom�tico se aprobar� la solicitud*/
					{
						$id_modul=68;
						if($this->transporte_model->aprobar($id_solicitud_transporte,2, $id_usuario_crea))
						{
							if($correo_cc!="") enviar_correo_cc($id_solicitud_transporte,$id_usuario_crea,$correo_cc,1);
							else echo "No se envi� ning�n correo de copia";
						}
					}
					else if($correo_cc!="") enviar_correo_cc($id_solicitud_transporte,$id_usuario_crea,$correo_cc,2);
					
					$this->db->trans_complete();
					$tr=($this->db->trans_status()===FALSE)?0:1;
					if($tr)
					{
						enviar_correo_automatico_administracion($id_solicitud_transporte,$id_modul);
					}
					if($id_solicitud_old!="")
					{
						
						/********************************************FUNCI�N DE BIT�CORA************************************************/
						/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						$id_user=$this->session->userdata('id_usuario');
						$user=$this->session->userdata('usuario');
						if($modifica_solicitud==true)/*Si est� modificando la solicitud*/
							$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." modific� la solicitud de transporte con ID ".$_POST['id_solicitud_old'],4);
						else/*Si est� creando la solicitud*/
							$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." cre� una solicitud de transporte para ".$_POST['nombre_input'],3);
						/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
						ir_a('index.php/transporte/ver_solicitudes/'.$tr.'/1');
					}
					else
					{
						ir_a('index.php/transporte/solicitud/'.$tr);
					}

			}else{

					echo "Ya fue procesado";
				}
		}
		else
		{
			echo "No tiene permisos para acceder";
		}
	}
	/*
	*	Nombre: control de entradas y salidas
	*	Objetivo: Mostrar la salida o ingreso de un vehiculo
	*	Hecha por: Jhonatan
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Falta mostrar datos segun el permiso que posea
	*/
	function control_salidas_entradas($estado_transaccion=NULL,$accion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),70);
		$data['id_modulo']=70;
		if(isset($data['id_permiso'])&&$data['id_permiso']>1) {
				$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
				$id_seccion_val=$id_seccion['id_seccion'];
				switch ($data['id_permiso']) {
					case 2:
						$data['datos']=$this->transporte_model->salidas_entradas_vehiculos_seccion($id_seccion_val);
						print_r($id_seccion_val.$data['datos']);
						break;
					case 3:
						$data['datos']=$this->transporte_model->salidas_entradas_vehiculos();
						break;
					case 4:
							$departamental=$this->transporte_model->is_departamental($id_seccion_val);

						if($departamental){
							$data['datos']=$this->transporte_model->salidas_entradas_vehiculos_seccion($id_seccion_val);
								
						}else{/// para san salvador
								
							$data['datos']=$this->transporte_model->salidas_entradas_vehiculos_SanSalvador();
						}

						break;
				}
				
			$data['estado_transaccion']=$estado_transaccion;
			if($accion==3)
				$data['accion']="salida";
			if($accion==4)
				$data['accion']="entrada";	 
			pantalla("transporte/despacho",$data);	
		}
		else 
		{
			echo "No tiene permisos para acceder";
		}

	}
	/*
	*	Nombre: Cargar Accesorios
	*	Objetivo: cargar una lista de cheakbox para selccionar los accesorios que un vehiculo lleva
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	�ltima Modificaci�n: 10/05/2014
	*	Observaciones: Falta mostrar datos segun el permiso que posea
	*/
	function accesoriosABordo($id_solicitud_transporte,$estado)
	{
		if ($estado==3) {//en caso de salida se puede seleccionar todos los accesorios
			$data['accesorios']=$this->transporte_model->accesorios();					
		} else {
			if ($estado==4) {//si viene de regrese unicamente muestra los accesorios con los que salio
					$data['accesorios']=$this->transporte_model->accesoriosABordo($id_solicitud_transporte);					
				}	
		}
		

			$this->load->view("transporte/accesorios",$data);
	}

	/*
	*	Nombre: guardar_despacho
	*	Objetivo: Registrar la salida o ingreso de un vehiculo
	*	Hecha por: Jhonatan
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function guardar_despacho()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),70);
		
		if($data['id_permiso']!=NULL)
		{
			$this->db->trans_start();
			$estado=$this->input->post('estado');
			$id=$this->input->post('id');
			$km=$this->input->post('km');
			$gas=$this->input->post('gas');
			$hora=date("H:i:s", strtotime($this->input->post('hora')));
			$descrip=$this->input->post('observacion'); //Observacion
			$imprimir=$this->input->post('resp'); //estado de la solicitud


			/*remuevo de post los datos para que solo queden los accesorios*/

		
			$acces=$_POST;
			unset($acces['observacion']);
			unset($acces['resp']);
			unset($acces['estado']);
			unset($acces['gas']);
			unset($acces['id']);
			unset($acces['km']);
			unset($acces['hora']);

			$estado= $this->transporte_model->consultar_estado($id);
			$estado=$estado[0]['estado'];
			
			if($estado==3)
			{
				$this->transporte_model->salida_vehiculo($id, $km,$hora,$acces,$gas);			

				$diferencia= $this->transporte_model->consultar_direfencia($id, $gas);
				if($diferencia[0]['diferencia']>=10)
				{
					//bitacora de comparacion de gas con el que salio y con cuanto se dejo en caso de ser sospechoso			
					$this->transporte_model->insertar_bitacora_combustible($id, $diferencia[0]['diferencia']);
					echo $diferencia[0]['diferencia'];
				}
				if($descrip!="")
				{
					$this->transporte_model->insertar_descripcion($id,$descrip,3);
				}

			}
			elseif($estado==4)
			{
				$this->transporte_model->regreso_vehiculo($id, $km, $hora, $gas,$acces);	
				$this->transporte_model->update_combustible($id, $gas);
				if($descrip!="")
				{
					$this->transporte_model->insertar_descripcion($id,$descrip,3);
				}
			}


			
			/********************************************FUNCI�N DE BIT�CORA************************************************/
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$id_user=$this->session->userdata('id_usuario');
			$user=$this->session->userdata('usuario');
			if($estado==3)/*Si va de salida*/
				$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." registr� la salida a misi�n oficial correspondiente a la solicitud de transporte con ID ".$id,3);
			elseif($estado==4)/*Si viene de regreso*/
				$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." registr� el regreso(entrada) de misi�n oficial correspondiente a la solicitud de transporte con ID ".$id,3);
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$this->db->trans_complete();


			if($imprimir==1)
			{
				nuevaVentana('index.php/transporte/solicitud_pdf/'.$id);	
			}
			
			$tr=($this->db->trans_status()===FALSE)?0:1;

			ir_a('index.php/transporte/control_salidas_entradas/'.$tr."/".$estado);	
				
		}
		else
		{
			echo "No tiene permisos para acceder";
		}
	}
		
	/*
	*	Nombre: infoSolicitud
	*	Objetivo: Ver datos de interes para el encargado de despacho sobre la mision
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	�ltima Modificaci�n: 23/03/2014
	*	Observaciones: Falta llevar el control de permiso para ver esta informacion
	*/
	function infoSolicitud($id)
	{		
		$d=$this->transporte_model->infoSolicitud($id);	
		$j=json_encode($d);
		echo $j;
	}
	
	/*
	*	Nombre: kilometraje
	*	Objetivo: Extraer el kilometraje recorrido de un vehiculo
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	�ltima Modificaci�n: 26/03/2014
	*	Observaciones: Falta llevar el control de permiso para ver esta informacion
	*/
	function kilometraje($id)
	{
		$d=$this->transporte_model->kilometraje($id);	
		$j=json_encode($d);
		echo $j;				
	}
	
	/*
	*	Nombre: ver_solicitudes
	*	Objetivo: Ver el estado actual de las solicitudes. Permite editar o eliminar solicitudes
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function ver_solicitudes($estado_transaccion=NULL, $tipo=NULL)
	{ 

							
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),67);
		$data['id_modulo']=67;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) {
				case 1:
					$empleado=$this->transporte_model->consultar_empleado($_SESSION['nr']);
					$data['solicitudes']=$this->transporte_model->buscar_solicitudes($empleado[0]['NR'], 1);
					break;
				case 2:
					$seccion=$this->transporte_model->consultar_seccion_usuario($_SESSION['nr']);
					$data['solicitudes']=$this->transporte_model->buscar_solicitudes(NULL, 1, $seccion['id_seccion']);
					break;
				case 3:
					$data['solicitudes']=$this->transporte_model->buscar_solicitudes(NULL,1);
					break;
				case 4:
					$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
					if($id_seccion['id_seccion']==52 || $id_seccion['id_seccion']==53 || $id_seccion['id_seccion']==54 || $id_seccion['id_seccion']==55 || $id_seccion['id_seccion']==56 || $id_seccion['id_seccion']==57 || $id_seccion['id_seccion']==58 || $id_seccion['id_seccion']==59 || $id_seccion['id_seccion']==60 || $id_seccion['id_seccion']==61 || $id_seccion['id_seccion']==64 || $id_seccion['id_seccion']==65 || $id_seccion['id_seccion']==66) {
						$data['solicitudes']=$this->transporte_model->buscar_solicitudes(NULL, 1, $id_seccion['id_seccion']);
					}
					else {
						$data['solicitudes']=$this->transporte_model->buscar_solicitudes_depto(0);	
					}
					break;
			}
			if($tipo!=NULL)
			{
				switch($tipo)
				{
					case 1: $msj = 'La solicitud de transporte ha sido creada con �xito'; break;
					case 2: $msj = 'La solicitud de transporte ha sido eliminada �xitosamente'; break;
				}
				
				$data['msj']=$msj;
			}
			$data['estado_transaccion']=$estado_transaccion;
			
			pantalla("transporte/ver_solicitudes",$data);	
		}
		else {
			echo "No tiene permisos para acceder";
		}
	}
	
	/*
	*	Nombre: eliminar_solicitud
	*	Objetivo: Elimina (desactiva) una solicitud de transporte
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function eliminar_solicitud($id_solicitud=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),67);
		
		if($data['id_permiso']!=NULL) {
			$this->db->trans_start();
			$this->transporte_model->eliminar_solicitud($id_solicitud);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			if($tr==1)
			{
				/********************************************FUNCI�N DE BIT�CORA************************************************/
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$id_user=$this->session->userdata('id_usuario');
				$user=$this->session->userdata('usuario');
				$this->seguridad_model->bitacora(SISTEMA,$id_user,"El usuario ".$user." elimin� (desactiv�) la solicitud de transporte con ID ".$id_solicitud,5);
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
			redirect('index.php/transporte/ver_solicitudes/'.$tr.'/2');
		}
		else {
			echo "No tiene permisos para acceder";
		}
	}
	
	/*
	*	Nombre: reporte_solicitud
	*	Objetivo: Muestra solicitudes que ya tienen asignado vehiculo y motorista. Permite exportar a pdf
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	�ltima Modificaci�n: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function reporte_solicitud()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),72);
		$data['id_modulo']=72;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) {
				case 1:
					$empleado=$this->transporte_model->consultar_empleado($this->session->userdata('nr'));
					$data['solicitudes']=$this->transporte_model->buscar_solicitudes($empleado[0]['NR'],3,NULL);
					break;
				case 2:
					$seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
					$data['solicitudes']=$this->transporte_model->buscar_solicitudes(NULL,3,$seccion['id_seccion']);
					break;
				case 3:
					$data['solicitudes']=$this->transporte_model->buscar_solicitudes(NULL,3,NULL);
					break;
				case 4:
					$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
					if($id_seccion['id_seccion']==52 || $id_seccion['id_seccion']==53 || $id_seccion['id_seccion']==54 || $id_seccion['id_seccion']==55 || $id_seccion['id_seccion']==56 || $id_seccion['id_seccion']==57 || $id_seccion['id_seccion']==58 || $id_seccion['id_seccion']==59 || $id_seccion['id_seccion']==60 || $id_seccion['id_seccion']==61 || $id_seccion['id_seccion']==64 || $id_seccion['id_seccion']==65 || $id_seccion['id_seccion']==66) {
						$data['solicitudes']=$this->transporte_model->buscar_solicitudes(NULL, 3, $id_seccion['id_seccion']);
					}
					else {
						$data['solicitudes']=$this->transporte_model->buscar_solicitudes_depto(3);
					}
					break;
			}
			pantalla("transporte/reporte_solicitudes",$data);	
		}
		else {
			echo "No tiene permisos para acceder";
		}
	}
	
	/*
	*	Nombre: solicitud_pdf
	*	Objetivo: Genera una archivo pdf de una solicitud
	*	Hecha por: Leonel
	*	Modificada por: Oscar
	*	Ultima Modificacion: 03/07/2014
	*	Observaciones: Ninguna
	*/
	function solicitud_pdf($id) 
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),72);
		if($data['id_permiso']!=NULL) {
			$seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
			$data['id_seccion']=$seccion['id_seccion'];
			$data['info_solicitud']=$this->transporte_model->consultar_solicitud($id);
			$id_solicitud_transporte=$data['info_solicitud']['id_solicitud_transporte'];
			$id_empleado_solicitante=$data['info_solicitud']['id_empleado_solicitante'];
			$id_empleado_autoriza=$data['info_solicitud']['id_empleado_autoriza'];
			$data['info_empleado']=$this->transporte_model->info_adicional($id_empleado_solicitante);
			$data['info_empleado2']=$this->transporte_model->info_adicional($id_empleado_autoriza);
			$data['acompanantes']=$this->transporte_model->acompanantes_internos($id_solicitud_transporte);
			$data['destinos']=$this->transporte_model->destinos($id_solicitud_transporte);
			$data['salida_entrada_real']=$this->transporte_model->datos_salida_entrada_real($id_solicitud_transporte);
			$data['motorista_vehiculo']=$this->transporte_model->datos_motorista_vehiculo($id_solicitud_transporte);
			$data['info_empleado3']=$this->transporte_model->info_adicional($data['motorista_vehiculo']['id_empleado_asigna']);
			$data['observaciones']=$this->transporte_model->observaciones($id_solicitud_transporte);
			/*$this->load->view('transporte/solicitud_pdf.php',$data);*/
			
			$this->mpdf = new mPDF('utf-8','letter',0, '', 15, 10, 10, 10, 9, 9); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
			$stylesheet = file_get_contents('css/pdf/solicitud.css'); /*Selecionamos la hoja de estilo del pdf*/
			$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
			$data['nombre'] = "Renatto NL";
			$html = $this->load->view('transporte/solicitud_pdf.php', $data, true); /*Seleccionamos la vista que se convertir� en pdf*/
			$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/
			if(count($data['destinos'])>1) { /*si la solicitud tiene varios detinos tenemos que crear otra hoja en el pdf y escribirlos all�*/
				$this->mpdf->AddPage();
				$html = $this->load->view('transporte/reverso_solicitud_pdf.php', $data, true);
				$this->mpdf->WriteHTML($html,2);
			}
			$this->mpdf->Output(); /*Salida del pdf*/
		}
		else {
			echo "No tiene permisos para acceder";
		}
	}
	
	/*
	*	Nombre: informes_solicitudes
	*	Objetivo: Genera la vista de informes de solicitudes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Ultima Modificacion: 25/09/2014
	*	Observaciones: Ninguna
	*/
	function informes_solicitudes() 
	{
		$data['empleado']=$this->transporte_model->consultar_empleados();
		$data['motorista']=$this->transporte_model->consultar_motoristas2();
		$data['seccion']=$this->transporte_model->consultar_secciones();
		$data['vehiculo']=$this->transporte_model->consultar_vehiculos();
		$data['usuario']=$this->transporte_model->usuario_sitcom();
		pantalla('transporte/informes_solicitudes',$data);
	}
	
	/*
	*	Nombre: informes_json
	*	Objetivo: filtra los datos de informes de solicitudes para generar la tabla
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Ultima Modificacion: 26/09/2014
	*	Observaciones: Ninguna
	*/
	function informes_json() 
	{
		$bnd=true;
		$id_empleado_solicitante=$this->input->post('id_empleado');
		$id_usuario=$this->input->post('id_usuario');
		$id_seccion=$this->input->post('id_seccion');
		$estado_solicitud=$this->input->post('estado_solicitud');
		$motorista=$this->input->post('motorista');
		$id_vehiculo=$this->input->post('id_vehiculo');
		
		if($_POST['fecha_final']!="" && $_POST['fecha_inicial']!="")
		{
			$fecha_inicial=date("Y-m-d", strtotime($this->input->post('fecha_inicial')));
			$fecha_final=date("Y-m-d", strtotime($this->input->post('fecha_final')));
		}
		else
		{
			$fecha_final=NULL;
			$fecha_inicial=NULL;
		}
		
		if($_POST['hora_final']!="" && $_POST['hora_inicial']!="")
		{
			$hora_inicial=date("H:i:s", strtotime($this->input->post('hora_inicial')));
			$hora_final=date("H:i:s", strtotime($this->input->post('hora_final')));			
		}
		else
		{
			$hora_final=NULL;
			$hora_inicial=NULL;
		}
		
		/*if(($estado_solicitud==1 || $estado_solicitud==2 || $estado_solicitud==3) && ($fecha_inicial!=NULL && $fecha_final!=NULL) && ($hora_inicial!=NULL && $hora_final!=NULL))
		{
			$fecha1=date("Y-m-d H:i:s", strtotime($fecha_incial." ".$hora_inicial));
			$fecha2=date("Y-m-d H:i:s", strtotime($fecha_final." ".$hora_final));
		}
		elseif($fecha_inicial!=NULL && $fecha_final!=NULL && $hora_inicial!=NULL && $hora_final!=NULL)
		{
			$fecha1=$fecha_incial;
			$fecha2=date("Y-m-d H:i:s", strtotime($fecha_final." ".$hora_final));
		}*/
		$data=$this->transporte_model->filtro_informes($id_empleado_solicitante,$id_usuario,$id_seccion,$estado_solicitud,$motorista,$id_vehiculo,$fecha_inicial,$fecha_final,$hora_inicial,$hora_final);
		pantalla('transporte/informes_solicitudes_pdf',$data);
	}
	
	/*
	*	Nombre: filtrar
	*	Objetivo: filtra los datos de informes de solicitudes para generar el pdf
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Ultima Modificacion: 25/09/2014
	*	Observaciones: Recibe como parametro a $_POST que no puede ser reasignada
	*/
	function filtrar() 
	{
		$data['datos']=$this->transporte_model->filtro_informes($_POST);
		pantalla('transporte/informes_solicitudes_pdf',$data);
	}
	
function prueba($id_solicitud_transporte=NULL) 
	{
		$data['solicitud']=$this->transporte_model->consultar_solicitud($id_solicitud_transporte);
		$data['id_solicitud_transporte']=$id_solicitud_transporte;
		$data['nombre']="Juan Francisco Abrego";
		$data['id_usuario']=12;
		$data['a']=$this->transporte_model->acompanantes_internos($id_solicitud_transporte);
		$data['f']=$this->transporte_model->destinos($id_solicitud_transporte);
		$html=$this->load->view('transporte/correo_aprobacion',$data,true);
		echo $html;

	}
function enviar_solicitud($id_solicitud_transporte)
{
	enviar_correo_automatico_administracion($id_solicitud_transporte,66);		
}

	public function prueba_correo($id_solicitud_transporte) {
		enviar_correo_automatico_usuarios($id_solicitud_transporte);
	}
	
}
?>