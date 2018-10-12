﻿<?php
	define("INGRESO",69);
	define("REQUISICION",81);
	define("AUTORIZAR",86);
	define("ENTREGAR",87);
	define("CONSUMO",82);
	define("ASIGNACION",88);
	define("ESTADO",111);
	define("VER_REQUISICIONES",89);
	define("CONSUMO_S",114);	//consumo vr asignacion
	define("CONSUMO_H",123);	//consumo historico
	define("CONSUMO_V",115);	//consumo por vehiculo
	define("LIQUIDACION",138); 
	define("REQUISICION_PDF",VER_REQUISICIONES); ///no esta 
	define("HERRAMIENTA",115); ///son herramientas y otros posibles consumidores de combustibles
	define("ONLY_SOURCE",1); ///hay que cambiar a 0 si los vales de banco mundial y goes se trabajaran como uno solo
	define("ADMIN_FACTURA",137); 
	define("GASOLINERA",140); 
	define("FUENTE_FONDO",119); 
	define("ACTIVAR_COMBUSTIBLE",205);
	define("PLANTA", 414);
	define ("SISTEMA","5"); //ID del sistema

class Vales extends CI_Controller
{

    function Vales()
	{
		//error_reporting(0);
        parent::__construct();
		date_default_timezone_set('America/El_Salvador');
		$this->load->model('vales_model');
		$this->load->model('transporte_model');
		$this->load->model('seguridad_model');
		$this->load->library("mpdf");
    	if(!$this->session->userdata('id_usuario')){
			redirect('index.php/sessiones');
		}
	#error_reporting(E_ALL);
	#ini_set('display_errors','1');
	
		
    }
	
	function index()
	{

		$this->ingreso_consumo();
  	}

	/*
	*	Nombre: ingreso_vales
	*	Objetivo: Carga la vista para el ingreso de vales de combustible
	*	Hecha por: Leonel
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function ingreso_vales($estado_transaccion=NULL, $insertado=NULL) 
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),INGRESO); 
		$data['id_modulo']=INGRESO;
		
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) {
				case 1:

				case 2:
				case 3:
			}
			$data['gasolineras']=$this->vales_model->consultar_gasolineras();
			$data['estado_transaccion']=$estado_transaccion;
			$data['accion']=$insertado;
			$data['fuente_fondo']=$this->transporte_model->consultar_fuente_fondo();
			pantalla("vales/ingreso",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}	
	}
	
	/*
	*	Nombre: guardar_vales
	*	Objetivo: guarda los datos de los vales de combustible
	*	Hecha por: Leonel
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function guardar_vales() 
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),INGRESO); /*Verificacion de permiso para crear solicitudes*/
		
		if($data['id_permiso']!=NULL) {
			$this->db->trans_start();
			$fec=str_replace("/","-",$this->input->post('fecha_recibido'));
			$fecha_recibido=date("Y-m-d", strtotime($fec));
			$inicial=$this->input->post('inicial');
			$cantidad_restante=$this->input->post('cantidad');
			$tipo_vehiculo=$this->input->post('tipo_vehiculo');
			$valor_nominal=$this->input->post('valor_nominal');
			$id_gasolinera=$this->input->post('id_gasolinera');
			$final=$this->input->post('inicial')+$this->input->post('cantidad')-1;
			$id_usuario_crea=$this->session->userdata('id_usuario'.'/'.$insertado);
			$fecha_creacion=date('Y-m-d H:i:s');
		
			$formuInfo = array(
				'inicial'=>$inicial,
				'final'=>$final,
				'valor_nominal'=>$valor_nominal,
				'tipo_vehiculo'=>$tipo_vehiculo,
				'id_gasolinera'=>$id_gasolinera,
				'id_usuario_crea'=>$id_usuario_crea,
				'fecha_creacion'=>$fecha_creacion,
				'fecha_recibido'=>$fecha_recibido,
				'cantidad_restante'=>$cantidad_restante
			);
		
		$insertado=0;
			if(sizeof($this->vales_model->verificarRepetidos($inicial,$final))==0){
					$id_vale= $this->vales_model->guardar_vales($formuInfo);
					$insertado=1;
					bitacora("Se ingresó la serie de vales $inicial - $final con ID ".$id_vale,3);
			}

			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/ingreso_vales/'.$tr.'/'.$insertado);
		}
		else {
			echo "No tiene permisos para acceder";
		}
	}
	
	/*
	*	Nombre: ingreso_requisicion
	*	Objetivo: Cargar la vista de la requisicion (solicitud) de combustible
	*	Hecha por: Leonel
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function ingreso_requisicion($estado_transaccion=NULL, $accion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/requicision';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		$data['m']=$this->vales_model->meses_requisicion();
		$data['id_modulo']=REQUISICION;
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					$data['vehiculos']=$this->vales_model->vehiculos($id_seccion['id_seccion']); //verificaciones que tenga vehiculo
					$data['otros']=$this->vales_model->otros($id_seccion['id_seccion']);			///o que tenga otros dispositivos

					if(sizeof($data['vehiculos'])+sizeof($data['otros'])==0) {
						$url.='Error';	
					}
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->consultar_oficinas();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental

					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
						$data['vehiculos']=$this->vales_model->vehiculos($id_seccion['id_seccion']);
						$data['otros']=$this->vales_model->otros($id_seccion['id_seccion']);

					if(sizeof($data['vehiculos'])+sizeof($data['otros'])==0) {
							$url.='error';	
						}
					}
					else {//san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas_san_salvador();
						$data['fuente']=$this->vales_model->consultar_fuente_fondo_san_salvador();
					}
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			$data['accion']=$accion;
		
			//echo"<pre>"; print_r($data); echo "</pre>";
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder ';
		}
	}



	/*
	*	Nombre: requisicion_modificar
	*	Objetivo: cargar la vista para modificar una requisicion
	*	Hecha por: Leonel
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/10/2014
	*	Observaciones: Ninguna.
	*/
	function requisicion_modificar($id_requisicion=NULL, $estado_transaccion=NULL, $accion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/requicision_modificar';
		$data['id_modulo']=REQUISICION;
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		$data['m']=$this->vales_model->meses_requisicion();


		$data['requisicion']=$this->vales_model->consultar_requisicion($id_requisicion);
		$data['requisicion']=$data['requisicion'][0];
		$data['req_veh']=$this->vales_model->info_requisicion_vehiculos($id_requisicion);

		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					$data['vehiculos']=$this->vales_model->vehiculos($id_seccion['id_seccion']);
					if(sizeof($data['vehiculos'])==0) {
						$url.='Error';	
					}
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->consultar_oficinas();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
						$data['vehiculos']=$this->vales_model->vehiculos($id_seccion['id_seccion']);
						if(sizeof($data['vehiculos'])==0){
							$url.='error';	
						}
					}
					else {//san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas_san_salvador();
						$data['fuente']=$this->vales_model->consultar_fuente_fondo_san_salvador();
					}
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			$data['accion']=$accion;
		//	echo "<br>  id seccion ".$id_seccion['id_seccion']." permiso ".$data['id_permiso'];
			//echo"<pre>"; print_r($data);  echo "</pre>";
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder ';
		}
	}	
	
	/*
	*	Nombre: consultar_refuerzo
	*	Objetivo: Verificar que la seccion no tenga requisiciones para un mismo mes
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 22/07/2014
	*	Observaciones: Ninguna.
	*/
	

function consultar_refuerzo($id_seccion, $id_fuente_fondo, $mes)
{

		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
	
		echo json_encode($this->vales_model->consultar_refuerzo($id_seccion, $id_fuente_fondo, $mes));	
		}else{
			echo 'No tiene permisos para acceder ';
		}
}
	/*
	*	Nombre: guardar_requisicion
	*	Objetivo: Guardar la requisicion de una seccion
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 25/05/2014
	*	Observaciones: Ninguna.
	*/
	function guardar_requisicion()
	{

		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
		
			if($_POST['refuerzo']==1){
				$_POST['asignado']=0;
				$_POST['restante']=0;
			}else{			
				$temp= $this->vales_model->asignaciones($_POST['id_seccion'],$_POST['id_fuente_fondo']);			
				$_POST['asignado']=$temp[0][cantidad];

			}
			$this->db->trans_start();

			if(verificarform($_POST)){

					$id_usuario=$this->session->userdata('id_usuario');
					$id_empleado_solicitante=$this->vales_model->get_id_empleado($this->session->userdata('nr')); 
					$id_requisicion=$this->vales_model->guardar_requisicion($_POST, $id_usuario, $id_empleado_solicitante);
					$vehiculos=$this->input->post('values');

					if (isset($_POST['values'])) {			
							
							for($i=0;$i<count($vehiculos);$i++) {		
									$this->vales_model->guardar_req_veh($vehiculos[$i],NULL, $id_requisicion);
						}
					}
					
					$vehiculos=$this->input->post('values2');

					if (isset($_POST['values2'])) {			
						for($i=0;$i<count($vehiculos);$i++) {
							$this->vales_model->guardar_req_veh(NULL,$vehiculos[$i], $id_requisicion);
						}
					}

					if($_POST['refuerzo']!=1){
						//proceso para guardar las series de vales sobrantes
						$sobraInfo= array('bandera' => 1,
						  'id_fuente_fondo'=>$_POST[id_fuente_fondo],
						  'id_seccion'=>$_POST[id_seccion],
						  'mes'=>restar_mes($_POST[mes],1) );

						$r=$this->vales_model->simular_buscar_requisicion_vale($sobraInfo,1);
						$this->vales_model->insertar_sobrante($r, $sobraInfo);
						//fin del proceso de sobrantes
					}
					$proce=1;

					deleteform($_POST);
					bitacora("Se guardó una requicisión  con id_requisicion ".$id_requisicion,3);
				}else{

					$proce=0;

				}
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/ingreso_requisicion/'.$tr.'/'.$proce);		
		}else{
			echo 'No tiene permisos para acceder ';	
		}
	}
		/*
	*	Nombre: guardar_requisicion
	*	Objetivo: Guardar la requisicion de una seccion
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 25/05/2014
	*	Observaciones: Ninguna.
	*/
	function actualizar_requisicion()
	{

		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
		
			$this->db->trans_start();

			if(verificarform($_POST)){

					
					$id_usuario=$this->session->userdata('id_usuario');
					$id_empleado_solicitante=$this->vales_model->get_id_empleado($this->session->userdata('nr')); 
					$id_requisicion=$this->vales_model->actualizar_requisicion($_POST, $id_usuario, $id_empleado_solicitante);
					
					$vehiculos=$this->input->post('values');

					if (isset($_POST['values'])) {			
							
							for($i=0;$i<count($vehiculos);$i++) {		
									$this->vales_model->guardar_req_veh($vehiculos[$i],NULL, $id_requisicion);
						}
					}
					
					$vehiculos=$this->input->post('values2');

					if (isset($_POST['values2'])) {			
						for($i=0;$i<count($vehiculos);$i++) {
							$this->vales_model->guardar_req_veh(NULL,$vehiculos[$i], $id_requisicion);
						}
					}
					$proce=1;
					bitacora("Se modificó una requicisión  con id_requisicion ".$id_requisicion,4);
					deleteform($_POST);
				}else{

					$proce=0;

				}
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/requisicion_modificar/'.$_POST['id_requisicion'].'/'.$tr.'/'.$proce);		
		}else{
			echo 'No tiene permisos para acceder ';	
		}
	}

	/*
	*	Nombre: vehiculos
	*	Objetivo: Cargar por ajax los vehiculos de una seccion y fuente de fonodo segun selccione el usuario 
	*	en la pantalla de ingrese de requisicion de combustible 
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 21/05/2014
	*	Observaciones: Ninguna.
	*/
	function vehiculos($id_seccion=NULL, $id_fuente_fondo = NULL,$id_requisicion=NULL)
	{

		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
		
			if ($id_requisicion==NULL) {
				$data['vehiculos']=$this->vales_model->vehiculos($id_seccion, $id_fuente_fondo);
			}else{
				$data['vehiculos']=$this->vales_model->vehiculos_req($id_seccion, $id_fuente_fondo,$id_requisicion);
			}		

			$this->load->view("vales/vehiculos",$data);		
		}else{
			echo 'No tiene permisos para acceder ';		
		}
	}	
	/*
	*	Nombre: Otros
	*	Objetivo: Cargar por ajax herramientas u otros articulos consumidores de combustible de una seccion y fuente de fonodo segun selccione el usuario 
	*	en la pantalla de ingrese de requisicion de combustible 
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 21/05/2014
	*	Observaciones: Ninguna.
	*/
	function CargarOtros($id_seccion=NULL, $id_fuente_fondo = NULL,$id_requisicion=NULL)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {		
			if ($id_requisicion==NULL) {
					$data['otros']=$this->vales_model->otros($id_seccion, $id_fuente_fondo);		
			}else{
					$data['otros']=$this->vales_model->otros_req($id_seccion, $id_fuente_fondo,$id_requisicion);				
			}		

			$this->load->view("vales/otros",$data);		
		}else{
			echo 'No tiene permisos para acceder ';		
		}
	}
	
/*
	*	Nombre: consultar_consumo
	*	Objetivo: Verificar el consumo de una seccion para hacer la peticion y lo compara con la cantidad asignada
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 1/07/2014
	*	Observaciones: Ninguna.
	*/
	function consultar_consumo($id_seccion, $id_fuente_fondo)
	{
		
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION); /*Verificacion de permiso para crear requisiciones*/
			if($data['id_permiso']!=NULL) {		
			
				$a=$this->vales_model->consultar_consumo($id_seccion, $id_fuente_fondo);
				$a=$a[0];
				$b=$this->vales_model->asignaciones($id_seccion, $id_fuente_fondo);
				$b=$b[0];
				
				$c = array('peticion' => $b['cantidad']-$a['suma'],
							'asignado'=>$b['cantidad'],
							'restante'=>$a['suma']);
					echo json_encode($c);
			}else{
				echo 'No tiene permisos para acceder ';		
			}


	}
	/*
	*	Nombre: requisiciones_pdf
	*	Objetivo: Cargar el PDF 
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/06/2014
	*	Observaciones: Ninguna.
	*/
	

	function requisicion_pdf($id)
	{

		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),REQUISICION_PDF); /*Verificacion de permiso para imprimir requisiciones*/
			if($data['id_permiso']!=NULL) {		
				
				$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
				$datos['e']=$this->vales_model->vales_entregados($id);
				$datos['d']=$this->vales_model->info_requisicion($id);;
				$datos['f']=$this->vales_model->info_requisicion_vehiculos($id);
				//$datos['info_empleado']=$this->vales_model->info_seccion($this->vales_model->id_seccion_requisicion($id));
				$datos['id']=$id;
				
				$html =$this->load->view("vales/requisicion_pdf",$datos, true);
				
				$this->mpdf->mPDF('utf-8','letter',0, '', 4, 4, 6, 6, 9, 9); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
				$stylesheet = file_get_contents('css/style-base.css'); /*Selecionamos la hoja de estilo del pdf*/
				$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
				$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/

				$this->mpdf->Output(); /*Salida del pdf*/	
				//echo"<pre>"; print_r($datos); echo "</pre>";
					
				}else{
				echo 'No tiene permisos para acceder ';		
			}
	}

	/*
	*	Nombre: visto bueno
	*	Objetivo: Aprobar y asignar los vales a entregar a la oficina, o en su defecto rechazar la peticion
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function autorizacion($estado_transaccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),AUTORIZAR); /*Verificacion de permiso para autorizacion*/
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		$data['id_modulo']=AUTORIZAR;
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['datos']=$this->vales_model->consultar_requisiciones($id_seccion['id_seccion'], 1);
					break;
				case 3://administrador
					$data['datos']=$this->vales_model->consultar_requisiciones(NULL,1);					
					break;
				case 4: //departamental					
						if($this->vales_model->is_departamental($id_seccion['id_seccion'])){// fuera de san salvador
							$data['datos']=$this->vales_model->consultar_requisiciones($id_seccion['id_seccion'], 1);
						}
						else{//san salvador
							$data['datos']=$this->vales_model->consultar_requisiciones_san_salvador(1);
						}
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			//print_r($data);
			pantalla("vales/autorizacion",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}


	/*
	*	Nombre: dialogo_ autorizacion 
	*	Objetivo: Cargar el cuadro de dialogo mediante ajax en la pantalla de autorizacion de vales de combustible
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function dialogo_autorizacion($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),AUTORIZAR);
		if(isset($data['id_permiso'])&& $data['id_permiso']>=2 ) {
			$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
			$datos['d']=$this->vales_model->info_requisicion($id);
			$datos['f']=$this->vales_model->info_requisicion_vehiculos($id);
			$datos['v']=$this->vales_model->info_vales($id);
			$datos['id']=$id;
			
		
			$this->load->view('vales/dialogo_autorizacion',$datos);
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}

	/*
	*	Nombre: guardar_autorizacion
	*	Objetivo: Guardar la informacion o respuesta del usuario en visto bueno
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Utilizo el arreglo POST para faciclitar la transferencia de datos al modelo
	*/
	function guardar_autorizacion()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),AUTORIZAR);
		$id_empleado=$this->vales_model->get_id_empleado($this->session->userdata('nr')); 
		$_POST['id_usuario']=$this->session->userdata('id_usuario');
		$_POST['id_empleado']=$id_empleado;
		
		if($data['id_permiso']!=NULL) {
			$this->db->trans_start();

			$this->vales_model->guardar_visto_bueno($_POST);
			$id_requisicion=$_POST['ids'];
			$resp=$_POST['resp'];
			if ($resp==2) {
				bitacora("Se autorizó una requicisión  con id_requisicion ".$id_requisicion,4);
			} else {
				bitacora("Se denegó una requicisión  con id_requisicion ".$id_requisicion,4);
			}
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/autorizacion/'.$tr);		
		}
		else{
			echo 'No tiene permisos para acceder';
		}
	}

	
	/*
	*	Nombre: ver requiciciones de combustible 
	*	Objetivo: Aprobar y asignar los vales a entregar a la oficina, o en su defecto rechazar la peticion
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/



	function ver_requisiciones($estado_transaccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VER_REQUISICIONES); /*Verificacion de permiso para crear requisiciones*/
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['datos']=$this->vales_model->consultar_requisiciones($id_seccion['id_seccion']);
					break;
				case 3://administrador
					$data['datos']=$this->vales_model->consultar_requisiciones();					
					break;
				case 4: //departamental
						if($this->vales_model->is_departamental($id_seccion['id_seccion'])){// fuera de san salvador
							$data['datos']=$this->vales_model->consultar_requisiciones($id_seccion['id_seccion']);
						}
						else{//san salvador
							$data['datos']=$this->vales_model->consultar_requisiciones_san_salvador();
						}
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			//print_r($data);
			$data['id_modulo']=VER_REQUISICIONES;
			pantalla("vales/ver_requisiciones",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}
	
	/*
	*	Nombre: dialogo detalle
	*	Objetivo: 	Guardar la informacion o respuesta del usuario en visto bueno
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Utilizo el arreglo POST para faciclitar la transferencia de datos al modelo
	*/
	function dialogo_detalles($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VER_REQUISICIONES);
		if(isset($data['id_permiso'])&& $data['id_permiso']>=2 ) {

			$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
			$datos['e']=$this->vales_model->vales_entregados($id);
			$datos['d']=$this->vales_model->info_requisicion($id);;
			$datos['f']=$this->vales_model->info_requisicion_vehiculos($id);
			$datos['id']=$id;
			//echo "<pre>";	print_r($datos);echo "</pre>";
			$this->load->view('vales/dialogo_detalles',$datos);
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}
	


/*
	*	Nombre: Asignaciones de vales a cada seccion
	*	Objetivo: Permite modificar, crear o elimnar las asignaciones mensuales de vales a las diferentes secciones
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function asignaciones($estado_transaccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
		
			$data['datos']=$this->vales_model->asignaciones();
			$data['estado_transaccion']=$estado_transaccion;
			//print_r($data);
			$data['id_modulo']=ASIGNACION;
			pantalla("vales/asignaciones",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	/*
	*	Nombre: dialogoM_asignacion 
	*	Objetivo: 	Permite modificar las asignaciones
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Utilizo el arreglo POST para faciclitar la transferencia de datos al modelo
	*/
	

	
	function dialogoM_asignacion($id_seccion,$id_fuente_fondo)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$data['d']=$this->vales_model->asignaciones($id_seccion,$id_fuente_fondo);
			$this->load->view("vales/dialogoM_asignacion",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	/*
	*	Nombre: modificar_asignacion
	*	Objetivo: 	modifica la asiganacion en la base de datos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/


	function modificar_asignacion()
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$this->db->trans_start();
			$this->vales_model->modificar_asignaciones($_POST);
			$id_seccion=$_POST['id_seccion'];
			$id_fuente_fondo=$_POST['id_fuente_fondo'];
			bitacora("Se modificó la asignación de vales para la sección con id_seccion ".$id_seccion." con id_fuente_fondo ".$id_fuente_fondo,4);

			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/asignaciones/'.$tr);		
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}

	/*
	*	Nombre: dialogoN_asignacion
	*	Objetivo: 	Permite insertar una nueva asignación
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/
	

	function dialogoN_asignacion()
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$data['oficinas']=$this->vales_model->consultar_todas_oficinas();
			$data['fuente']=$this->vales_model->consultar_fuente_fondo();		
			$this->load->view("vales/dialogoN_asignacion",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	/*
	*	Nombre: Insertar_asignacion
	*	Objetivo: 	guarda la asiganacion en la base de datos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/

	function insertar_asignacion()
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$this->db->trans_start();
			$this->vales_model->insertar_asignaciones($_POST);		
			$id_seccion=$_POST['id_seccion'];
			$id_fuente_fondo=$_POST['id_fuente_fondo'];
			bitacora("Se añadió una nueva asignación de vales para la sección con id_seccion ".$id_seccion." con id_fuente_fondo ".$id_fuente_fondo,3);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/asignaciones/'.$tr);		
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}

	/*
	*	Nombre: eliminar_asignacion
	*	Objetivo: 	eliminar la asiganacion en la base de datos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/

	function eliminar_asignacion($id_seccion, $id_fuente_fondo)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$this->db->trans_start();
			$this->vales_model->eliminar_asignaciones($id_seccion, $id_fuente_fondo);		
			bitacora("Se eliminó la asignación de vales para la sección con id_seccion ".$id_seccion." con id_fuente_fondo ".$id_fuente_fondo,3);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/asignaciones/'.$tr);		
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}


	/*
	*	Nombre: ingreso_consumo
	*	Objetivo: Carga la vista para el ingreso de comsumo de vales de combustible por vehiculo.
	*	Hecha por: Leonel
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/


	function ingreso_consumo($estado_transaccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO); 
		
		if($data['id_permiso']!=NULL) {
			
			$this->vales_model->Desactivar_combustible_para_todos_automatico();///////////desactivacion automatica de combustible


			$data['fuente']=$this->vales_model->consultar_fuente_fondo();
			$data['gasolineras']=$this->vales_model->consultar_gasolineras();
			$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
			
	
		switch ($data['id_permiso']) {
				case 2: //seccion
					$data['seccion']=$id_seccion['id_seccion'];
					$data['seccionN']=$this->vales_model->consultar_seccion($id_seccion['id_seccion']);
					$data['seccionN']=$data['seccionN'][0]['nombre'];
					break;
				case 3: //administrador nacional
					$data['seccion']=$this->vales_model->secciones_vales();
					break;
				case 4: //administrador departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['seccion']=$id_seccion['id_seccion'];
						$data['seccionN']=$this->vales_model->consultar_seccion($id_seccion['id_seccion']);
						$data['seccionN']=$data['seccionN'][0]['nombre'];

					}else{
						$data['seccion']=$this->vales_model->secciones_vales(TRUE);
					}
					break;
			}

			$data['estado_transaccion']=$estado_transaccion;
			$data['id_modulo']=CONSUMO;
			pantalla("vales/consumo",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}	
	}
	
	/*
	*	Nombre: vehiculos_consumo
	*	Objetivo: Cargar por ajax los vehiculos de una seccion segun la gasolinera que se seleccione  
	*	en la pantalla de ingrese de requisicion de combustible 
	*	Hecha por: Leonel
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function vehiculos_consumo($id_gasolinera = NULL, $fecha_factura_dia = NULL, $fecha_factura_mes = NULL, $fecha_factura_anio = NULL, $seccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO); 
		
		if($data['id_permiso']!=NULL) {
			//$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));			
			$id_seccion['id_seccion']=$seccion;
			$fecha_factura=$fecha_factura_anio."-".$fecha_factura_mes."-".$fecha_factura_dia;
			$fecha_factura2=$fecha_factura_anio."".$fecha_factura_mes;

//nueva adaptacion para que funcione consumo por seccion
			$data['vehiculos']=$this->vales_model->consultar_vehiculos_seccion($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura2);
			$data['vales']=$this->vales_model->consultar_vales_seccion($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura2);

//antigua forma
			/*switch($data['id_permiso']) {
				case 1:
					break;
				case 2:
					$data['vehiculos']=$this->vales_model->consultar_vehiculos_seccion($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura);
					$data['vales']=$this->vales_model->consultar_vales_seccion($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura);
					break;
				case 3:
				case 4:
					if(!$this->vales_model->es_san_salvador($id_seccion['id_seccion'])) {
						$data['vehiculos']=$this->vales_model->consultar_vehiculos_seccion($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura);
						$data['vales']=$this->vales_model->consultar_vales_seccion($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura);
					}
					else {
						$data['vehiculos']=$this->vales_model->consultar_vehiculos_seccion(NULL,$id_gasolinera,$fecha_factura);
						$data['vales']=$this->vales_model->consultar_vales_seccion(NULL,$id_gasolinera,$fecha_factura);
					}
					break;
			}
			echo "<pre>";
			print_r($data);
			echo "</pre>";
			*/
			$this->load->view("vales/vehiculos_consumo",$data);	

		}
		else {
			echo 'No tiene permisos para acceder';
		}	
	}
		/*
	*	Nombre: tipo de combustible
	*	Objetivo: Enviar obligaicon de los precios de combustible obligatorios
	*	en la pantalla de ingrese de requisicion de combustible 
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 08/01/2015
	*	Observaciones: Ninguna.
	*/
	function tipo_combustible($id_gasolinera = NULL, $fecha_factura_dia = NULL, $fecha_factura_mes = NULL, $fecha_factura_anio = NULL, $seccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO); 
		
		if($data['id_permiso']!=NULL) {
			//$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));			
			$id_seccion['id_seccion']=$seccion;
			$fecha_factura=$fecha_factura_anio."-".$fecha_factura_mes."-".$fecha_factura_dia;
			$fecha_factura2=$fecha_factura_anio."".$fecha_factura_mes;
		echo json_encode($this->vales_model->consultar_tipos_combustible($id_seccion['id_seccion'],$id_gasolinera,$fecha_factura2));


		}
		else {
			echo 'No tiene permisos para acceder';
		}	
	}
	
	/*
	*	Nombre: guardar_consumo
	*	Objetivo: Guardar formulario del consumo de vales de combustible
	*	Hecha por: Leonel
	*	Modificada por: Leonel
	*	Última Modificación: 07/07/2014
	*	Observaciones: Falta la creacion de la tabla "configuraciones" para saber si los vales se pueden "mezclar".
	*/
	
function guardar_consumo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO); 
		
		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();
			//$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
					$id_seccion=$this->input->post('id_seccion');
					$id_gasolinera=$this->input->post('id_gasolinera');
					$fec=str_replace("/","-",$this->input->post('fecha_factura'));
					$fecha_factura=date("Y-m-d", strtotime($fec));
					$numero_factura=$this->input->post('numero_factura');
					$valor_super=$this->input->post('valor_super');
					$valor_regular=$this->input->post('valor_regular');
					$valor_diesel=$this->input->post('valor_diesel');
					$id_herramienta= $this->input->post('id_herramienta');
					$id_vehiculo=$this->input->post('id_vehiculo');
					$actividad_consumo=$this->input->post('actividad_consumo');
					$tip_gas_bruto=$this->input->post('tip_gas');
					$cantidad_consumo=$this->input->post('cantidad_consumo');
					$year =date("Y", strtotime($fec));

					$verificar2= $this->vales_model->factura_repetida($numero_factura, $id_seccion, $year);
					
					if(verificarform($_POST) && $verificar2){ //se verifica que no sea el mismo formulario  por un F5 y ademas se verifica que la factura no este ingresada en el sistema
					
					
					//esto se agrego debido a que no siempre se pedira el tipo de combustible
					if ($valor_diesel=="") {
						$valor_diesel=0;
					}
					if ($valor_super=="") {
						$valor_super=0;
					}
					if ($valor_regular=="") {
						$valor_regular=0;
					}
					//esto fin de inicializacion de valores de combustibles
					if(count($tip_gas_bruto)>count($id_vehiculo)) {
						for($x=1;$x<count($tip_gas_bruto);$x++) {
							if($x%2!=0)
								$tip_gas[]=$tip_gas_bruto[$x];
						}
					}
					else
						$tip_gas=$tip_gas_bruto;
					
					/*echo "<br><pre>";
					print_r($tip_gas);
					echo "</pre>";*/
					
					$formuInfo = array(
						'fecha_factura'=>$fecha_factura,
						'numero_factura'=>$numero_factura,
						'valor_super'=>$valor_super,
						'valor_regular'=>$valor_regular,
						'valor_diesel'=>$valor_diesel,
						'id_usuario_crea'=>$this->session->userdata('id_usuario')
					);
				 $id_consumo=$this->vales_model->guardar_factura($formuInfo); //descomentar
					
					/*$bandera=$this->vales_model->mezclar_tipo_vehiculo();*/
					$bandera=ONLY_SOURCE;
					
					for($i=0;$i<count($id_vehiculo);$i++){
						$val=explode("**",$id_vehiculo[$i]);
						$id_veh=$val[0];
						$id_requisicion_vale=$val[1];
						$valor_vale=$val[2];
						$tipo_vehiculo=$val[3];

						if($tip_gas[$i]!="" && $cantidad_consumo[$i]!="") {
							$formuInfo = array(
								'id_consumo'=>$id_consumo,
								'id_vehiculo'=>$id_veh,
								'actividad_consumo'=>$actividad_consumo[$i],
								'tip_gas'=>$tip_gas[$i],
								'id_requisicion_vale'=>$id_requisicion_vale,
								'cantidad'=>$cantidad_consumo[$i],
								'id_gasolinera'=>$id_gasolinera,
								'recibido'=>1,
								'tipo_vehiculo'=>$tipo_vehiculo,
								'bandera'=>$bandera,
								'id_seccion'=>$id_seccion,
								'id_herramienta'=>$id_herramienta[$i]
							);
						$this->vales_model->buscar_requisicion_vale($formuInfo); // descomentar esto
						}
					}
					bitacora("Se registró consumo de combustible, con id_consumo ".$id_consumo." correspondiente a la factura numero $numero_factura",3);
					deleteform($_POST);
				}else{

					echo "Ya fue procesado";
				}

			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/ingreso_consumo/'.$tr);

		}
		else {
			echo 'No tiene permisos para acceder';
		}	
	}

	



	/*	
	*	Nombre: vales a consumir
	*	Objetivo: Mostrar la informacion de los vales que se consumiran
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 03/09/2014
	*	Observaciones: Ninguna.
	*/
	function vales_a_consumir()
	{



	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO); 
		
		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();

			$id_seccion=$this->input->post('id_seccion');
			$id_gasolinera=$this->input->post('id_gasolinera');
			$bandera=ONLY_SOURCE;
	
			$formuInfo = array(
						'id_gasolinera'=>$id_gasolinera,
						'bandera'=>$bandera,
						'id_seccion'=>$id_seccion
						);
				echo json_encode($this->vales_model->simular_buscar_requisicion_vale($formuInfo)); 
	
			$this->db->trans_complete();
		}else {
			//error de permisos
		}	
	
	}
	/*	
	*	Nombre: entrega de vales
	*	Objetivo: Mostrar la informacion necesaria para entregar los vales
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/


function entrega($estado_transaccion=NULL, $accion=NULL)
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ENTREGAR); 
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
		
		$data['datos']=$this->vales_model->consultar_requisiciones(NULL,2);											
		$data['estado_transaccion']=$estado_transaccion;
		$data['accion']=$accion;
		$data['id_modulo']=ENTREGAR;
			pantalla("vales/entrega",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	
}
	/*
	*	Nombre: dialogo entrega vales
	*	Objetivo: Mostrar  informacion mas detallada de la entrega de vales
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Ninguna.
	*/
	function dialogo_entrega($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ENTREGAR);
		if(isset($data['id_permiso'])&& $data['id_permiso']>=2 ) {
			$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));
			
			$t=$this->vales_model->info_correlativo();
			//$datos['b']=$this->vales_model->info_requisicion($id);
			$datos['c']=$t[0];
			$datos['d']=$this->vales_model->info_requisicion($id);
			$datos['e']=$this->vales_model->vales_a_entregar($id);
			$datos['f']=$this->vales_model->info_requisicion_vehiculos($id);
			$datos['id']=$id;


			$this->load->view('vales/dialogo_entrega',$datos);
		}
		else {
			echo 'No tiene permisos para acceder';
		}
	}
	/*
	*	Nombre: guardar_entrega
	*	Objetivo: Guardar el registro de entrega
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Utilizo el arreglo POST para faciclitar la transferencia de datos al modelo
	*/
	function guardar_entrega()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ENTREGAR);
		$id_empleado=$this->vales_model->get_id_empleado($this->session->userdata('nr')); 
		$_POST['id_usuario']=$this->session->userdata('id_usuario');
		$_POST['id_empleado']=$id_empleado;		
		if($data['id_permiso']!=NULL) {
			$this->db->trans_start();
			
			if(verificarform($_POST)){


					$this->vales_model->guardar_entrega($_POST);
					$req=(array)$this->vales_model->info_requisicion($_POST['ids']);
					$val=$this->vales_model->buscar_vales($_POST['ids'],$req[0]->id_fuente_fondo,$_POST['asignar']);
					$id_requisicion=$_POST['ids'];
					bitacora("Se entregarón los vales autorizados a la requsición con id_requisicion ".$id_requisicion,4);
					$this->db->trans_complete();
					deleteform($_POST);
					$proce=1;
				}else{
					$proce=0;
					
				}

			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/entrega/'.$tr.'/'.$proce);		
		}
		else{
			echo 'No tiene permisos para acceder';
		}
	}

	function vales_a_entregar($id){
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ENTREGAR);
		if($data['id_permiso']!=NULL) {			
				$this->vales_model->vales_a_entregar($id);
		}else{
			echo 'No tiene permisos para acceder';
		}

	}

		/*
	*	Nombre: reporte_consumo
	*	Objetivo: Mostrar un reporte personalizado al usuario
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 10/07/2014
	*	Observaciones: Ninguna
	*/

		function reporte_consumo()
	{
$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_S); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/reporte_consumo';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->consultar_oficinas();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					}
				
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			/*echo "<br>  id seccion ".$id_seccion['id_seccion']." permiso ".$data['id_permiso'];
			print_r($data['oficinas']);  */
			$data['id_modulo']=CONSUMO_S;
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}
		
	}
		/*
	*	Nombre: consumo_json
	*	Objetivo: Proporiciona los datos segun los parametros recibidos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 10/07/2014
	*	Observaciones: Ninguna
	*/

	function consumo_json()
	{		

			$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_S); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {

			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			$agrupar = $this->input->post('agrupar');
			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));
			}else{
					$fecha_inicio=NULL;
					$fecha_fin=NULL;
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}


			$data=$this->vales_model->consumo_seccion_fuente($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin, $agrupar);
			echo json_encode($data);
		}else {
			echo 'No tiene permisos para acceder';
		}
			
	}

		/*
	*	Nombre: consumo_pdf
	*	Objetivo: imprimir reporte segun parametros recibidos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 20/07/2014
	*	Observaciones: Ninguna
	*/

	function consumo_pdf()
	{		


	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_S); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
				///Preparacion de datos
			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			$agrupar = $this->input->post('agrupar');
			$data['color1']=$this->input->post('color1');
			$data['color2']=$this->input->post('color2');
		
			
		//para formar mensaje
			$f="Consumo de vales de combustible ";


			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));


					$f.="del ".$fecha_inicio." al ".$fecha_fin;
			}else{
					$f.="del ".date('01-m-Y')." al ".date('d-m-Y'); 
					$fecha_inicio=NULL;
					$fecha_fin=NULL;
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}else{

				$f.=" en ".$_POST['id_seccion_input'];
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}else{
				$f.=" con fuente de fondo ".$_POST['id_fuente_fondo_input'];	
			}
			$data['f']=$f;

			$aux= $this->vales_model->consumo_seccion_fuente($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin, $agrupar);
			$data['j']=json_encode($aux);
			

			//print_r($data);
			$this->load->view('vales/consumo_pdf',$data);

		}else {
			echo 'No tiene permisos para acceder';
		}

	}

	
	public function consumo_xls()
	{
		$this->load->view('vales/consumo_xls');
	}


	/*
	*	Nombre: estado
	*	Objetivo: Mostrar un informe de los vales disponibles 
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 10/07/2014
	*	Observaciones: Ninguna
	*/
	function estado2()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ESTADO); /*Verificacion de permisos*/
		$url='vales/estado';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		

		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['e']=$this->vales_model->vales_de_seccion($id_seccion['id_seccion']);					
					$data['s']=FALSE;
					$data['g']=FALSE;					
					break;
				case 3://administrador
					$data['ne']=$this->vales_model->vales_sin_entregar();
					$data['e']=$this->vales_model->vales_de_seccion();
					$data['s']=TRUE;
					$data['g']=TRUE;

					break;
				case 4: //departamental
					$data['g']=FALSE;

					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['e']=$this->vales_model->vales_de_seccion($id_seccion['id_seccion']);
						$data['s']=FALSE;
					}else{// en san salvador
						$data['e']=$this->vales_model->vales_san_salvador();
						$data['s']=TRUE;
					}				
					break;
			}
			$data['id_modulo']=ESTADO;
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}

	}


/*
	*	Nombre: Herramientas de cada seccion
	*	Objetivo: Permite modificar, crear o elimnar las herramientas
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 12/07/2014
	*	Observaciones: Ninguna.
	*/
	function herramientas($estado_transaccion=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HERRAMIENTA); /*Verificacion de permiso gestionar herramientas*/
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
		
			$data['datos']=$this->vales_model->herramientas();
			$data['estado_transaccion']=$estado_transaccion;
			//print_r($data);
			$data['id_modulo']=HERRAMIENTA;
			pantalla("vales/herramientas",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	/*
	*	Nombre: dialogoM_herramienta
	*	Objetivo: 	Permite modificar las asignaciones
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*	Observaciones: Utilizo el arreglo POST para faciclitar la transferencia de datos al modelo
	*/
	

	
	function dialogoM_herramienta($id_herramienta)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HERRAMIENTA); /*Verificacion de permiso gestionar herramientas*/
		if($data['id_permiso']!=NULL) {

			$data['oficinas']=$this->vales_model->consultar_oficinas();
			$data['fuente']=$this->vales_model->consultar_fuente_fondo();		
			$data['d']=$this->vales_model->consultar_herramientas($id_herramienta);		
			$this->load->view("vales/dialogoM_herramienta",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	/*
	*	Nombre: modificar_herramienta
	*	Objetivo: 	modifica la herramienta en la base de datos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/


	function modificar_herramienta()
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HERRAMIENTA); /*Verificacion de permiso gestionar herramientas*/
		if($data['id_permiso']!=NULL) {
		
            $this->db->trans_start();
$this->vales_model->modificar_herramienta($_POST);
$id_herramienta=$_POST['id'];             
bitacora("Se modificó la herramienta con id_herramienta  ".$id_herramienta,4);
$this->db->trans_complete();
$tr=($this->db->trans_status()===FALSE)?0:1;
ir_a('index.php/vales/herramientas/'.$tr);               }else{
echo 'No tiene permisos para acceder';         }            }

	/*
	*	Nombre: dialogoN_herramienta
	*	Objetivo: 	Permite insertar una nueva asignación
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/
	

	function dialogoN_herramienta()
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HERRAMIENTA); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$data['oficinas']=$this->vales_model->consultar_oficinas();
			$data['fuente']=$this->vales_model->consultar_fuente_fondo();		
			$this->load->view("vales/dialogoN_herramienta",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	/*
	*	Nombre: Insertar_asignacion
	*	Objetivo: 	guarda la asiganacion en la base de datos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/

	function insertar_herramienta()
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HERRAMIENTA); /*Verificacion de permiso gestionar herramientas*/
		if($data['id_permiso']!=NULL) {
		
			$this->db->trans_start();
			$id_herramienta=$this->vales_model->insertar_herramientas($_POST);		
			bitacora("Se insertó la herramienta con id_herramienta  ".$id_herramienta,3);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/herramientas/'.$tr);		
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}

	function seccion_vale($id_fuente_fondo)
	{
		echo json_encode($data['oficinas']=$this->vales_model->consultar_oficinas_fuente($id_fuente_fondo));

	}

	/*
	*	Nombre: eliminar_herramienta
	*	Objetivo: 	eliminar la asiganacion en la base de datos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 07/07/2014
	*/

	function eliminar_herramienta($id_herramienta)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HERRAMIENTA); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$this->db->trans_start();
			$this->vales_model->eliminar_herramienta($id_herramienta);
			bitacora("Se eliminó la herramienta con id_herramienta  ".$id_herramienta,5);		
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/herramientas/'.$tr);		
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}


			/*
	*	Nombre: reporte_historico
	*	Objetivo: Mostrar un reporte personalizado al usuario
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 27/07/2014
	*	Observaciones: Ninguna
	*/

		function reporte_historico()
	{
$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_H); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/consumo_historico';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->consultar_oficinas();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);					
					}
				
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			/*echo "<br>  id seccion ".$id_seccion['id_seccion']." permiso ".$data['id_permiso'];
			print_r($data['oficinas']);  */
			$data['id_modulo']=CONSUMO_H;
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}
		
	}
		/*
	*	Nombre: consumo_json
	*	Objetivo: Proporiciona los datos segun los parametros recibidos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 27/07/2014
	*	Observaciones: Ninguna
	*/

	function historico_json()
	{		

			$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_H); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {

			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			
			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));
			}else{
					$fecha_inicio=NULL;
					$fecha_fin=NULL;
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}
			$agrupar=$this->input->post('agrupar');

			$data=$this->vales_model->consumo_seccion_fuente_d($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin, $agrupar);
			echo json_encode($data);
		}else {
			echo 'No tiene permisos para acceder';
		}
			
	}

		/*
	*	Nombre: consumo_pdf
	*	Objetivo: imprimir reporte segun parametros recibidos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 20/07/2014
	*	Observaciones: Ninguna
	*/

	function historico_pdf()
	{		


	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_H); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
				///Preparacion de datos
			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			$data['color1']=$this->input->post('color1');

			
					//para formar mensaje
			$f="Consumo de vales de combustible ";


			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));


					$f.="del ".$this->input->post('start')." al ".$this->input->post('end');
			}else{
						$f.="del ".date('01-m-Y')." al ".date('d-m-Y'); 
					$fecha_inicio=NULL;
					$fecha_fin=NULL;
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}else{

				$f.=" en ".$_POST['id_seccion_input'];
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}else{
				$f.=" con fuente de fondo ".$_POST['id_fuente_fondo_input'];	
			}
			$data['f']=$f;

			$agrupar=$this->input->post('agrupar');
			$aux=$this->vales_model->consumo_seccion_fuente_d($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin, $agrupar);
			$data['j']=json_encode($aux);
			
		
			//print_r($_POST);
			$this->load->view('vales/historico_pdf',$data);

		}else {
			echo 'No tiene permisos para acceder';
		}

	}
		/*
	*	Nombre: repotyr_vehiculos
	*	Objetivo: reporte de vehiculos con su consumo, las dos funciones que le siguen hacen lo mismo pero en formatos de informacion diferente
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 20/08/2014
	*	Observaciones: Ninguna
	*/

	function reporte_vehiculo()
	{


	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_V); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
				///Preparacion de datos
	
		switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->consultar_oficinas();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);					
					}
				
					break;
			}
	

		$data['id_modulo']=CONSUMO_V;
		pantalla('vales/reporte_vehiculos',$data);


		}else {
			echo 'No tiene permisos para acceder';
		}		
	}
	
	function reporte_vehiculo_json($op=1)
	{


	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_V); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
				///Preparacion de datos


			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			
			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));
			}else{
					$fecha_fin=date('Y-m-').getUltimoDiaMes();
					$fecha_inicio=date('Y-m-1');
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}			

			if ($op==1) {
				$data=$this->vales_model->consumo_vehiculo($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin);
			}elseif ($op==2) {
				$data=$this->vales_model->consumo_herramientas($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin);
			}

			echo json_encode($data);	

		}else {
			echo 'No tiene permisos para acceder';
		}		
	}
	
function reporte_vehiculo_pdf()
	{


			$id_seccion=$this->input->post('id_seccion');

			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			$salida=$this->input->post('salida');
			
					//para formar mensaje
			$f="Consumo de vales de combustible aplicacados a vehiculos";


			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));


					$f.=" del ".$_POST['start']." al ".$_POST['end'];
			}else{
					$f.=" del ".date('01-m-Y')." hasta  el ".getUltimoDiaMes().date('-m-Y'); 
					$fecha_fin=date('Y-m-').getUltimoDiaMes();
					$fecha_inicio=date('Y-m-1');
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}else{

				$f.=" en ".$_POST['id_seccion_input'];
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}else{
				$f.=" con fuente de fondo ".$_POST['id_fuente_fondo_input'];	
			}
			$data['f']=$f;			
			$data['datos']=$this->vales_model->consumo_vehiculo($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin);
			$data['herramientas']=$this->vales_model->consumo_herramientas($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin);
		if ($salida==2) { //excel


			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=vehiculos.xls");
			header("Pragma: no-cache");
			header("Expires: 0");
			$data['base']=true;
			$html = $this->load->view('vales/vehiculos_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
			echo $html;
		}elseif ($salida==1) { //PDF
			$data['base']=false;
			$this->mpdf->mPDF('utf-8','letter',0, '', 4, 4, 6, 6, 9, 9); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
			$stylesheet = file_get_contents('css/style-base.css'); /*Selecionamos la hoja de estilo del pdf*/
			$html = $this->load->view('vales/vehiculos_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
			$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
			$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/
			$this->mpdf->Output(); /*Salida del pdf*/	
			# code...
		}else{ //HTML
			$data['base']=true;
			$html = $this->load->view('vales/vehiculos_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
			echo $html;
		}

	
	}

			/*
	*	Nombre: asingacion de vehiculos
	*	Objetivo: esta funcion junto con las que le siguen son para darle mantenimiento a la asignacion de vehiculos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 20/08/2014
	*	Observaciones: Ninguna
	*/



function asignacion_vehiculo()
{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
		
			$data['datos']=$this->vales_model->asignaciones_vehiculo();
			$data['estado_transaccion']=$estado_transaccion;
			//print_r($data);
			$data['id_modulo']=ASIGNACION;
			pantalla("vales/asignaciones_vehiculo",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}	
}
	function dialogo_asignacion_vehiculo($id_vehiculo = NULL)
	{


	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ASIGNACION); /*Verificacion de permiso gestionar asignaciones*/
		if($data['id_permiso']!=NULL) {
		
			$data['d']=$this->vales_model->consultar_datos_vehiculos($id_vehiculo);
		
			$data['oficinas']=$this->vales_model->consultar_oficinas_fuente( $data['d'][0]['id_fuente_fondo']);
		$this->load->view("vales/dialogo_asignacion_vehiculo",$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}		
	}
	function modificar_asignacion_vehiculo()
	{
			$this->db->trans_start();
			$this->vales_model->seccion_asignada($_POST);	
			$id_vehiculo=$_POST['id_vehiculo'];
			bitacora("Se modificó la asignación del vehículo con id_vehiculo  ".$id_vehiculo,4);	
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/asignacion_vehiculo/'.$tr);		
	}


	/*
	*	Nombre: estado
	*	Objetivo: permite ver detalle a detalle, el destino de cada vale
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 20/08/2014
	*	Observaciones: Ninguna
	*/


	function estado()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ESTADO); /*Verificacion de permisos*/
		$url='vales/estado2';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		

		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['e']=$this->vales_model->vales_de_seccion($id_seccion['id_seccion']);					
					$data['s']=FALSE;
					$data['g']=FALSE;					
					break;
				case 3://administrador
					$data['ne']=$this->vales_model->vales_sin_entregar();
			
					$data['e']=$this->vales_model->vales_de_seccion();
					$data['s']=TRUE;
					$data['g']=TRUE;

					break;
				case 4: //departamental
					$data['g']=FALSE;

					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['e']=$this->vales_model->vales_de_seccion($id_seccion['id_seccion']);
						$data['s']=FALSE;
					}else{// en san salvador
						$data['e']=$this->vales_model->vales_san_salvador();
						$data['s']=TRUE;
					}				
					break;
			}
			$data['id_modulo']=ESTADO;
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}

	}

	function detalleVjson($id_vale=NULL)
	{
		$data['v']=$this->vales_model->detalleV($id_vale);
		echo json_encode($data['v']);
	}
	function detalleRjson($id_requisicion=NULL)
	{
		$data['v']=$this->vales_model->detalleR($id_requisicion);
		echo json_encode($data['v']);
	}
	function detalleFjson($id_requisicion=NULL)
	{
		$data['v']=$this->vales_model->detalleF($id_requisicion);
		echo json_encode($data['v']);
	}

		/*
	*	Nombre: reporte_asignacion
	*	Objetivo: Mostrar un reporte personalizado al usuario
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 8/09/2014
	*	Observaciones: Ninguna
	*/

	function reporte_asignacion()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_S); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/reporte_asignacion';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->consultar_oficinas();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					}
				
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			/*echo "<br>  id seccion ".$id_seccion['id_seccion']." permiso ".$data['id_permiso'];
			print_r($data['oficinas']);  */
			$data['id_modulo']=CONSUMO_S;
			pantalla($url,$data);	
		}
		else {
			echo 'No tiene permisos para acceder';
		}
		
	}
		/*
	*	Nombre: consumo_json
	*	Objetivo: Proporiciona los datos segun los parametros recibidos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 10/07/2014
	*	Observaciones: Ninguna
	*/

	function asignacion_json()
	{		

			$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_S); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {

			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			$agrupar = $this->input->post('agrupar');
			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));
			}else{
					$fecha_inicio=NULL;
					$fecha_fin=NULL;
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo=NULL;
			}


			$data=$this->vales_model->asignacion_reporte($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin, $agrupar);
			echo json_encode($data);
		}else {
			echo 'No tiene permisos para acceder';
		}
			
	}

		/*
	*	Nombre: consumo_pdf
	*	Objetivo: imprimir reporte segun parametros recibidos
	*	Hecha por: Jhonatan
	*	Modificada por: Jhonatan
	*	Última Modificación: 20/07/2014
	*	Observaciones: Ninguna
	*/

	function asignacion_pdf()
	{		


	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),CONSUMO_S); /*Verificacion de permiso para crear requisiciones*/
		if($data['id_permiso']!=NULL) {
				///Preparacion de datos
			$id_seccion=$this->input->post('id_seccion');
			$id_fuente_fondo=$this->input->post('id_fuente_fondo');
			$agrupar = $this->input->post('agrupar');
			$data['color1']=$this->input->post('color1');
			$data['color2']=$this->input->post('color2');
		
			
		//para formar mensaje
			$f="Consumo de vales de combustible ";


			if($_POST['start']!="" && $_POST['end']!=""){
					$fecha_inicio=$this->input->post('start');
					$fecha_fin=$this->input->post('end');
					$fecha_inicio=date("Y-m-d", strtotime($fecha_inicio));
					$fecha_fin=date("Y-m-d", strtotime($fecha_fin));


					$f.="del ".$fecha_inicio." al ".$fecha_fin;
			}else{
					$f.="del ".date('01-m-Y')." al ".date('d-m-Y'); 
					$fecha_inicio=NULL;
					$fecha_fin=NULL;
			}
			if($id_seccion==0){
				$id_seccion=NULL;
			}else{

				$f.=" en ".$_POST['id_seccion_input'];
			}
			if($id_fuente_fondo==0){
				$id_fuente_fondo= NULL;
			}else{
				$f.=" con fuente de fondo ".$_POST['id_fuente_fondo_input'];	
			}
			$data['f']=$f;

			$aux= $this->vales_model->asignacion_reporte($id_seccion, $id_fuente_fondo, $fecha_inicio, $fecha_fin, $agrupar);
			$data['j']=json_encode($aux);
			

			$this->load->view('vales/asignacion_pdf',$data);

		}else {
			echo 'No tiene permisos para acceder';
		}

	}

function reporte_liquidacion()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),LIQUIDACION); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/reporte_liquidacion';
		$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
		//$data['id_permiso']=$permiso;

		$data['meses']=$this->vales_model->meses_registrados();
		if($data['id_permiso']!=NULL) {
			switch($data['id_permiso']) { /*Busqueda de informacion a mostrar en la pantalla segun el nivel del usuario logueado*/
				case 1:
				case 2://seccion
					$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
					$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					break;
				case 3://administrador
					$data['oficinas']=$this->vales_model->secciones_vales();
					$data['fuente']=$this->vales_model->consultar_fuente_fondo();

					break;
				case 4: //departamental
					if($this->vales_model->is_departamental($id_seccion['id_seccion'])) {// fuera de san salvador
						$data['oficinas']=$this->vales_model->consultar_oficinas($id_seccion['id_seccion']);
						$data['fuente']=$this->vales_model->consultar_fuente_fondo($id_seccion['id_seccion']);
					
					}
				
					break;
			}
			$data['estado_transaccion']=$estado_transaccion;
			/*echo "<br>  id seccion ".$id_seccion['id_seccion']." permiso ".$data['id_permiso'];
			print_r($data['oficinas']);  */
			$data['id_modulo']=LIQUIDACION;
			pantalla($url,$data);	
		}else {
			echo 'No tiene permisos para acceder';
		}
		
	}

function liquidacion_pdf()
{		
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),LIQUIDACION); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {
				//DATOS GENERALES	
				extract($_POST);

				$data['mesn']= $mes_input;
				$mes1=restar_mes($mes,1); //le resta un mes
				$mes2=restar_mes($mes,-1); //le resta dos meses
				$mes1s=restar_mes($mes,-1); //le suma un mes



				$data['seccion']= $id_seccion_input;
				$data['mesn1']=$this->vales_model->name_mes($mes1);
				$data['mesn2']=$this->vales_model->name_mes($mes2);
				$data['base']=($salida==2);
				
					//DEFINICION DE TIPO DE REPORTE
				$html="";
			if($id_seccion==0){ //liquidacion general

				$data['l']=$this->vales_model->liquidacion_mensual2($mes, $id_fuente_fondo);	
				$html = $this->load->view('vales/liquidacion_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
				$this->mpdf->mPDF('utf-8','letter-L',0, '', 4, 4, 6, 6, 9, 9); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
			}else{ //liquidacion por seccion
					//$mes2=$mes1;
					//$mes1=$mes;				
				$req=$this->vales_model->cantidades_requisiciones($id_seccion,$mes, $id_fuente_fondo);
				$req=$req[0];
////temp
				$data['mesn1']=$this->vales_model->name_mes($mes1);
				$data['mesn2']=$this->vales_model->name_mes($mes2);
				$data['mesn1s']=$this->vales_model->name_mes($mes1s);
//end temp

				$data['s_entregada']=$this->vales_model->cantidad_entregada($id_seccion,$mes, $id_fuente_fondo);			
				//$data['s_sobrante']=$this->vales_model->cantidad_sobrante($id_seccion,$mes2, $id_fuente_fondo);			
				$data['s_sobrante']=$this->vales_model->cantidad_sobrante2($id_seccion,$mes, $id_fuente_fondo);			
				$a_consumir= array_merge($data['s_sobrante'],$data['s_entregada']);
				$data['consumo_clase']=$this->vales_model->consumo_clase($id_seccion,$mes, $id_fuente_fondo);
				if (sizeof($data['consumo_clase'])>12) { //para no hacer una lista demasiado grande de vehiculos
					$data['consumo_clase']=$this->vales_model->consumo_clase($id_seccion,$mes, $id_fuente_fondo,true);
				}
				$temp=$this->vales_model->simular_consumo_liquidacion($a_consumir, $data['consumo_clase']);
				$data['s_consumida']=$temp['c'];
				$data['s_sobrante2']=$temp['s'];
				$data['vacio']=" <tr><td>--</td><td>--</td><td>--</td></tr>"; 
				$data['asignado']=$this->vales_model->consultar_asignacion($id_seccion, $mesn, $id_fuente_fondo);
//				echo "<pre>"; 		print_r($data);		echo "</pre>"; 

				$html = $this->load->view('vales/liquidacion_seccion_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
				$this->mpdf->mPDF('utf-8','letter',0, '', 4, 4, 6, 6, 9, 9); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/

			}

			if ($salida==2) { //excel

				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=liquidacion.xls");
				header("Pragma: no-cache");
				header("Expires: 0");
				echo $html;
			}elseif ($salida==1) { //PDF

				$stylesheet = file_get_contents('css/style-base.css'); /*Selecionamos la hoja de estilo del pdf*/
				$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
				$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/

				$this->mpdf->Output(); /*Salida del pdf*/	
				# code...
			}else{ //HTML
				$data['base']=true;
				$html = $this->load->view('vales/vehiculos_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
				echo $html;
			}
		}else{
			echo 'No tiene permisos para acceder';	
		}


}

function ver_facturas($estado_transaccion=NULL)
{

	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ADMIN_FACTURA); /*Verificacion de permiso */		
	$id_seccion=$this->transporte_model->consultar_seccion_usuario($this->session->userdata('nr'));	
	if($data['id_permiso']!=NULL) {
		switch ($data['id_permiso']) {
			case 1:
			case 2: //seccion
					
				$data['d']=$this->vales_model->consultar_facturas($id_seccion['id_seccion']);
				break;
			case 4:  //departamental
				$data['d']=$this->vales_model->consultar_facturas($id_seccion['id_seccion']);
				break;
			case 3: //nacional
				$data['d']=$this->vales_model->consultar_facturas();
				break;
			
			default:
				# code...
				break;
		}
		$data['estado_transaccion']=$estado_transaccion;
		$data['id_modulo']=ADMIN_FACTURA;
		pantalla('vales/ver_facturas',$data);

	}else{
			echo 'No tiene permisos para acceder';	
	}


}
function eliminar_factura($id_consumo=NULL)
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ADMIN_FACTURA); /*Verificacion de permiso */		
	if($data['id_permiso']!=NULL) {
		$this->db->trans_start();	
			
			$this->vales_model->copiar_factura($id_consumo);		//se hace una copia de seguridad de la factura
			$this->vales_model->eliminar_factura($id_consumo);
			bitacora("Se eliminó la factura con id_consumo ".$id_consumo,5);
		
		$this->db->trans_complete();
		$tr=($this->db->trans_status()===FALSE)?0:1;
		ir_a('index.php/vales/ver_facturas/'.$tr);
	}else{
			echo 'No tiene permisos para acceder';	
	}	
}

function dialogo_factura($id_consumo=NULL)
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ADMIN_FACTURA); /*Verificacion de permiso */		
	if($data['id_permiso']!=NULL) {
		$data['d']=$this->vales_model->detalleF($id_consumo);
		$data['g']=$this->vales_model->info_factura($id_consumo);
		$data['g']=$data['g'][0];
		//echo "<pre>"; print_r($data); echo "</pre>";
		$this->load->view('vales/dialogo_factura',$data);


	}else{
			echo 'No tiene permisos para acceder';	
	}	
}


	function gasolineras($estado_transaccion=NULL,$accion=NULL)
	{

	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),GASOLINERA); /*Verificacion de permiso */		

		if($data['id_permiso']!=NULL) {
		$data['estado_transaccion']=$estado_transaccion;
			if($accion==0)
				$data['accion']="elimina";
			if($accion==1)
				$data['accion']="actualiza";
			if($accion==2)
				$data['accion']="guarda";

			$data['titulo']="Gasolinera";
			$data['d']=$this->vales_model->consultar_gasolineras();
			$data['id_modulo']=GASOLINERA;
			pantalla('vales/gasolineras',$data);	
		
		}else {
			echo 'No tiene permisos para acceder';
		}	
	}

function modificar_gasolinera($id)
	{

$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),GASOLINERA); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {

			$data['d']=$this->vales_model->consultar_gasolineras($id);

			$this->load->view('vales/DM_gasolinera',$data);
		}else {
			echo 'No tiene permisos para acceder';
		}	
	}

function actualizar_gasolinera()
{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),GASOLINERA); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {
		//	print_r($_POST);
			$this->db->trans_start();			
			$this->vales_model->actualizar_gasolinera($_POST);			
			$id_gasolinera=$_POST['id'];
			bitacora("Se modificó gasolinera con id_gasolinera ".$id_gasolinera,4);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/gasolineras/'.$tr.'/1');
		}else {
			echo 'No tiene permisos para acceder';
		}	

}
function nuevo_gasolinera()
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),GASOLINERA); /*Verificacion de permiso */		

		if($data['id_permiso']!=NULL) {

			$this->load->view('vales/DN_gasolinera');
		}else {
			echo 'No tiene permisos para acceder';
		}	
}
function guardar_gasolinera()
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),GASOLINERA); /*Verificacion de permiso */		

		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();			
			$id_gasolinera=$this->vales_model->insertar_gasolinera($_POST);
			bitacora("Se guardó la gasolinera con id_gasolinera ".$id_gasolinera,3);			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/gasolineras/'.$tr.'/2');	
		}else {
			echo 'No tiene permisos para acceder';
		}	
}
function eliminar_gasolinera($id)
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),GASOLINERA); /*Verificacion de permiso */		

		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();			
			$this->vales_model->eliminar_gasolinera($id);			
			bitacora("Se desabilitó la gasolinera con id_gasolinera ".$id,5);			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/gasolineras/'.$tr.'/3');	
		}else {
			echo 'No tiene permisos para acceder';
		}	
}

function fuente_fondos($estado_transaccion=NULL,$accion=NULL)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),FUENTE_FONDO); /*Verificacion de permiso */		
		
		if($data['id_permiso']!=NULL) {
		$data['estado_transaccion']=$estado_transaccion;
			if($accion==0)
				$data['accion']="elimina";
			if($accion==1)
				$data['accion']="actualiza";
			if($accion==2)
				$data['accion']="guarda";

			$data['titulo']="fuente de fondo";
			$data['d']=$this->vales_model->consultar_fuente_fondo2();
			$data['id_modulo']=FUENTE_FONDO;
			pantalla('vales/fuente_fondos',$data);	
		
		}else {
			echo 'No tiene permisos para acceder';
		}	
	}
function actualizar_fuente_fondo()
{	
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),FUENTE_FONDO); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();			
			$this->vales_model->actualizar_fuente_fondo($_POST);
			$id_fuente_fondo = $_POST['id'];
			bitacora("Se modificó la fuente de fondo con id_fuente_fondo ".$id_fuente_fondo,4);			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/fuente_fondos/'.$tr.'/1');
		}else {
			echo 'No tiene permisos para acceder';
		}	

}
function modificar_fuente_fondo($id)
	{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),FUENTE_FONDO); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {

			$data['d']=$this->vales_model->consultar_fuente_fondo2($id);
	
			$this->load->view('vales/DM_fuente_fondo',$data);
		}else {
			echo 'No tiene permisos para acceder';
		}	
	}
function nuevo_fuente_fondo()
{		
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),FUENTE_FONDO); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {

			$this->load->view('vales/DN_fuente_fondo');
		}else {
			echo 'No tiene permisos para acceder';
		}	
}
function guardar_fuente_fondo()
{
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),FUENTE_FONDO); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();			
			$id_fuente_fondo=$this->vales_model->insertar_fuente_fondo($_POST);

			bitacora("Se guardó la fuente de fondo con id_fuente_fondo ".$id_fuente_fondo,3);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/fuente_fondos/'.$tr.'/2');	
		}else {
		echo 'No tiene permisos para acceder';
	}	
}
function eliminar_fuente_fondo($id)
{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),FUENTE_FONDO); /*Verificacion de permiso */		
		if($data['id_permiso']!=NULL) {

			$this->db->trans_start();			
			$this->vales_model->eliminar_fuente_fondo($id);			
			bitacora("Se desabilitó la fuente de fondo con id_fuente_fondo ".$id,5);
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/vales/fuente_fondos/'.$tr.'/3');	
		}else {
			echo 'No tiene permisos para acceder';
		}	
}

function D_activar_combustible()
{
	
	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ACTIVAR_COMBUSTIBLE); /*Verificacion de permiso */		
	if($data['id_permiso']!=NULL) {

		$data['estado']= $this->vales_model->Combustible_para_todos();
		$this->load->view('vales/D_activar_combustible',$data);
	}else {
		echo 'No tiene permisos para acceder';
	}	
}
function Combustible_para_todos()
{

	$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),ACTIVAR_COMBUSTIBLE); /*Verificacion de permiso */		
	if($data['id_permiso']!=NULL) {
			
			extract($_POST);
			$estado=(isset($estado) && $estado==1)?1:2;
			$this->vales_model->Combustible_para_todos($estado);

			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a('index.php/inicio');			
	}else {
		echo 'No tiene permisos para acceder';
	}
}

/*
	*	Nombre: ingreso_requisicion_plan
	*	Objetivo: Cargar la vista de la requisicion plan
	*	Hecha por: Alberto
	*	Modificada por: Alberto
	*	Última Modificación: 12/10/2018
	*	Observaciones: Ninguna.
	*/
	function ingreso_requisicion_planta() {
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'), PLANTA); /*Verificacion de permiso para crear requisiciones*/
		$url='vales/requicision_planta';
		
		$data['id_modulo']=PLANTA;
		
		if($data['id_permiso']!=NULL) {
			
			$data['fuente']=$this->vales_model->consultar_fuente_fondo();
			$data['estado_transaccion']=$estado_transaccion;
			$data['accion']=$accion;

			pantalla($url, $data);	
		}
		else {
			echo 'No tiene permisos para acceder ';
		}
	}

	public function obtener_numeracion_vales() {
		$numeros = $this->vales_model->obtener_numeracion_vales($this->input->post('cantidad'), $this->input->post('fuente'));
		
		if ($numeros) {
			echo json_encode($numeros->result_array());
		}
	}

}

?>