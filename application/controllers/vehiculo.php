<?php

/*Módulos del área de taller*/

define("VEHICULOS",79);
define("PRESUPUESTO",80);
define("TALLER_INT",116);
define("BODEGA",117);
define("TALLER_EXT",118);

/*reportes*/
define("HOJA_MTTO",144);
define("KARDEX",141);
define("MTTOS",142);
define("HOJA_VEHICULO",143);
define("PRESUPUESTOS",159);

/*Sistema SITCOM*/
define ("SISTEMA","5");

class Vehiculo extends CI_Controller
{
    
    function __construct()
	{
        parent::__construct();
		date_default_timezone_set('America/El_Salvador');
		$this->load->model('transporte_model');
		$this->load->model('seguridad_model');
		$this->load->library("mpdf");
    	if(!$this->session->userdata('id_usuario')){
		 redirect('index.php/sessiones');
		}
		error_reporting(0);
    }
	
	function index()
	{
		echo"ok";
  	}
	
	/*
	*	Nombre: vehiculos
	*	Objetivo: Carga el catálogo de Vehículos y permite la modificación de los datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 15/01/2015
	*	Observaciones: Ninguna
	*/
	function vehiculos($estado_transaccion=NULL,$tipo=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS); 
		$data['id_modulo']=VEHICULOS;
		if($data['id_permiso']!=NULL)
		{
			switch($data['id_permiso'])
			{
				case 3: 
						if($estado_transaccion!=NULL)
						{
							$data['estado_transaccion']=$estado_transaccion;
							
							if($tipo!=NULL)
							{
								switch($tipo)
								{
									case 1: $data['mensaje']="Se ha ingresado la información de un nuevo vehículo éxitosamente";
											break;
									case 2: $data['mensaje']="Se ha modificado la información del vehículo éxitosamente";
											break;
									case 3: $data['mensaje']="Se ha reportado anomalía del vehículo a taller institucional éxitosamente";
											break;
									case 4: $data['mensaje']="Se ha registrado el mantenimiento rutinario al vehículo éxitosamente";
											break;
								}
							}
						}
						break;
			}
			$data['datos']=$this->transporte_model->consultar_vehiculos();
			pantalla('mantenimiento/vehiculos',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: nuevo_vehiculo
	*	Objetivo: Carga la vista para el Registro de un nuevo Vehículo a la Base de Datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/11/2014
	*	Observaciones: Ninguna
	*/
	function nuevo_vehiculo($id_vehiculo=0)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']!=NULL)
		{
			switch($data['id_permiso'])
			{
				case 3: ///Control de Vehículos Nacional
					if($id_vehiculo==0)
					{
						$data['motoristas']=$this->transporte_model->consultar_motoristas2();
						$data['marca']=$this->transporte_model->consultar_marcas();
						$data['modelo']=$this->transporte_model->consultar_modelos();
						$data['clase']=$this->transporte_model->consultar_clases();
						$data['condicion']=$this->transporte_model->consultar_condiciones();
						$data['seccion']=$this->transporte_model->consultar_secciones();
						$data['fuente_fondo']=$this->transporte_model->consultar_fuente_fondo();
						$data['bandera']='false';
					}
					else
					{
						$data['vehiculo_info']=$this->transporte_model->consultar_vehiculo_taller($id_vehiculo);
						$data['motoristas']=$this->transporte_model->consultar_motoristas2();
						$data['marca']=$this->transporte_model->consultar_marcas();
						$data['modelo']=$this->transporte_model->consultar_modelos();
						$data['clase']=$this->transporte_model->consultar_clases();
						$data['condicion']=$this->transporte_model->consultar_condiciones();
						$data['seccion']=$this->transporte_model->consultar_secciones();
						$data['fuente_fondo']=$this->transporte_model->consultar_fuente_fondo();
						$data['bandera']='true';
					}
					break;
			}
			pantalla("mantenimiento/nuevo_vehiculo",$data);
		}
		else
		{
			echo "No tiene permisos para acceder a esta pantalla";
		}
	}
	
	/*
	*	Nombre: control_taller
	*	Objetivo: Carga el catálogo de Vehículos que se encuentran en taller.
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function control_taller($estado_transaccion=NULL,$tipo=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		$data['id_modulo']=TALLER_INT;
		if($data['id_permiso']!=NULL)
		{
			switch($data['id_permiso'])
			{
				case 3: if($estado_transaccion!=NULL)
						{
							$data['estado_transaccion']=$estado_transaccion;
							
							if($tipo!=NULL)
							{
								switch($tipo)
								{
									case 1: $data['mensaje']="Se ha registrado el mantenimiento al vehículo éxitosamente";
											break;
									case 2: $data['mensaje']="Se ha enviado el vehículo a taller externo éxitosamente";
											break;
									case 3: $data['mensaje']="Se ha dado de alta al vehículo éxitosamente";
											break;
									case 4: $data['mensaje']="Se ha ingresado el vehículo a taller interno éxitosamente";
											break;
									case 5: $data['mensaje']="Se ha modificado la información de ingreso al taller del vehículo éxitosamente";
											break;
								}
							}
						}
						$data['ingreso_taller']=$this->transporte_model->vehiculos_taller_interno(0,2);
						break;
			}
			pantalla('mantenimiento/control_taller',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: ventana_mantenimientos
	*	Objetivo: carga la ventana de mantenimientos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 07/01/2015
	*	Observaciones: Ninguna
	*/
	
	function ventana_mantenimientos($id,$id2)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']!=NULL)
		{
			switch($data['id_permiso'])
			{
				case 3:
						$data['vehiculo']=$this->transporte_model->vehiculos_taller_interno(0,NULL,$id);
						$data['vehiculo']=$data['vehiculo'][0];
						$data['revision']=$this->transporte_model->consultar_revisiones2($id);
						$data['mantenimientos']=$this->transporte_model->consultar_mantenimientos($id2,$id);
						break;
			}
			$this->load->view('mantenimiento/ventana_mantenimiento.php',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: controlMtto
	*	Objetivo: carga la vista para ingresar un vehículo al taller del MTPS
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 19/05/2015
	*	Observaciones: Ninguna
	*/
	
	function controlMtto($id_vehiculo=0, $estado_transaccion=NULL, $id_ingreso_taller=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$data['bandera2']='false';
			if($estado_transaccion!=NULL) $data['estado_transaccion']=$estado_transaccion;
			if($id_vehiculo!=0)
			{
				$data['vehiculo']=$this->transporte_model->consultar_vehiculos(1,$id_vehiculo);
				$data['bandera']='true';
				if($id_ingreso_taller!=NULL)
				{
					$data['vehiculos']=$this->transporte_model->consultar_vehiculos(2,NULL,$id_ingreso_taller);
					$data['info']=$this->transporte_model->vehiculos_taller_interno(0,NULL,$id_ingreso_taller);
					$data['info']=$data['info'][0];
					$data['revision2']=$this->transporte_model->consultar_revisiones2($id_ingreso_taller);
					$data['bandera2']='true';
				}
			}
			else
			{
				$data['vehiculos']=$this->transporte_model->consultar_vehiculos(1);
				$data['bandera']='false';
			}
			$data['mecanicos']=$this->transporte_model->mecanicos();
			$data['revision']=$this->transporte_model->consultar_revisiones();
			pantalla('mantenimiento/control_mantenimiento',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_mantenimiento
	*	Objetivo: insertar en la Base de Datos un nuevo registro de ingreso de un vehiculo al taller del MTPS
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function guardar_mantenimiento()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->guardar_mtto($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			if($_POST['placa']!="" && $_POST['placa']!=NULL) $placa = $_POST['placa'];
			else $placa = $_POST['id_vehiculo_input'];
			
			$descripcion="Registró el ingreso del vehículo con número de placa ".$placa." al taller del MTPS en la fecha ".$_POST['fecha_recepcion'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			if($_POST['pantalla']==1) ir_a("index.php/vehiculo/control_taller/".$tr."/4");
			elseif($_POST['pantalla']==2) ir_a("index.php/vehiculo/vehiculos/".$tr."/3");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: modificar_mantenimiento
	*	Objetivo: modifica en la Base de Datos el registro de ingreso de un vehiculo al taller del MTPS
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 20/05/2015
	*	Observaciones: Ninguna
	*/
	
	function modificar_mantenimiento()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->modificar_mtto($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			if($_POST['placa']!="" && $_POST['placa']!=NULL) $placa = $_POST['placa'];
			else $placa = $_POST['id_vehiculo_input'];
			
			$descripcion="Modificó el registro de ingreso al taller del MTPS con ID ".$_POST['id_ingreso_taller'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,4);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/control_taller/".$tr."/5");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: tallerMTPS
	*	Objetivo: carga la vista de Reparación y mantenimiento en taller del MTPS
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 27/01/2014
	*	Observaciones: Ninguna
	*/
	
	function tallerMTPS($id_v)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculos']=$this->transporte_model->vehiculos_taller_interno($id_v,2);
			$data['vehiculos']=$data['vehiculos'][0];
			$data['reparacion']=$this->transporte_model->consultar_reparacion();
			$data['inventario']=$this->transporte_model->inventario();
			$data['mecanicos']=$this->transporte_model->mecanicos();
			
			/*obteniendo la fecha del último mantenimiento*/
			$fecha_min="";
			$aux=$this->transporte_model->consultar_mantenimientos($id_v,$data['vehiculos']['id_ingreso_taller']);
			if(!empty($aux) && $aux!=0)
			{
				$i=0;
				foreach($aux as $a)
				{
					if($i==0) $fecha_min = $a['fecha'];
					
					$i++;
				}
			}
			
			/*obteniendo la última fecha de ingreso a taller externo*/
			$fecha_min2="";
			$aux2=$this->transporte_model->consultar_ingresos_taller_externo($data['vehiculos']['id_ingreso_taller']);
			if(!empty($aux2) && $aux2!=0)
			{
				$i2=0;
				foreach($aux2 as $a2)
				{
					if($i2==0) $fecha_min2 = $a2['fecha_entrega'];
					
					$i2++;
				}
			}
			
			if($fecha_min!="" && $fecha_min2=="") $data['fecha_min']=$fecha_min; //si sólo se han realizado mantenimientos
			elseif($fecha_min2!="" && $fecha_min=="") $data['fecha_min']=$fecha_min2; // si sólo se ha enviado a taller externo
			elseif($fecha_min!="" && $fecha_min2!="") // Si han habido matenimientos e ingresos a taller externo
			{
				if($fecha_min>=$fecha_min2) $data['fecha_min']=$fecha_min; //se compara cual fecha es la mayor o si son iguales
				else $data['fecha_min']=$fecha_min2;
			}
			else $data['fecha_min']=""; // si no se ha hecho nada
			
			pantalla('mantenimiento/taller_MTPS',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: mantenimiento_rutinario
	*	Objetivo: Carga la vista de mantenimiento rutinario
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 16/02/2015
	*	Observaciones: Ninguna
	*/
	
	function mantenimiento_rutinario($id_v)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculos']=$this->transporte_model->vehiculos_taller_interno($id_v,1,NULL,1);
			$data['vehiculos']=$data['vehiculos'][0];
			$data['inventario']=$this->transporte_model->inventario();
			$data['mecanicos']=$this->transporte_model->mecanicos();
			pantalla('mantenimiento/mantenimiento_rutinario',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: control_taller_ext
	*	Objetivo: carga el listado de vehículos que se encuentran en taller externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 12/01/2015
	*	Observaciones: Ninguna
	*/
	
	function control_taller_ext($estado_transaccion=NULL,$tipo=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		$data['id_modulo']=TALLER_EXT;
		if($data['id_permiso']==3)
		{
			if($estado_transaccion!=NULL)
			{
				$data['estado_transaccion']=$estado_transaccion;
				
				if($tipo!=NULL)
				{
					switch($tipo)
					{
						case 1: $data['mensaje']="Se ha ingresado el vehículo al taller externo éxitosamente";
								break;
						case 2: $data['mensaje']="Se ha dado de alta al vehículo éxitosamente";
								break;
						case 3: $data['mensaje']="Se ha modificado la información de ingreso al taller externo del vehículo éxitosamente";
								break;
					}
				}
			}
			$data['taller_externo']=$this->transporte_model->vehiculos_taller_externo(0,3);
			pantalla('mantenimiento/control_taller_ext',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: ingreso_taller_ext
	*	Objetivo: carga la vista de para ingresar vehiculo a taller externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function ingreso_taller_ext($id_vehiculo=0,$estado_transaccion=NULL,$id_ingreso_taller_ext=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		
		if($data['id_permiso']==3)
		{
			if($estado_transaccion!=NULL) $data['estado_transaccion']=$estado_transaccion;
			if($id_vehiculo!=0)
			{
				$data['vehiculo']=$this->transporte_model->consultar_vehiculos(2,$id_vehiculo);
				$data['bandera']='true';
				$data['bandera2']='false';
				
				if($id_ingreso_taller_ext!=NULL)
				{
					$data['vehiculos']=$this->transporte_model->consultar_vehiculos();
					$data['info']=$this->transporte_model->vehiculos_taller_externo(0,NULL,$id_ingreso_taller_ext);
					$data['info']=$data['info'][0];
					$data['bandera2']='true';
				}
				
				/*obteniendo la fecha del último mantenimiento*/
				$fecha_min="";
				$aux=$this->transporte_model->consultar_mantenimientos($id_vehiculo,$data['vehiculo']->id_ingreso_taller);
				if(!empty($aux) && $aux!=0)
				{
					$i=0;
					foreach($aux as $a)
					{
						if($i==0) $fecha_min = $a['fecha'];
						
						$i++;
					}
				}
				
				/*obteniendo la última fecha de ingreso a taller externo*/
				$fecha_min2="";
				$aux2=$this->transporte_model->consultar_ingresos_taller_externo($data['vehiculo']->id_ingreso_taller);
				if(!empty($aux2) && $aux2!=0)
				{
					$i2=0;
					foreach($aux2 as $a2)
					{
						if($i2==0) $fecha_min2 = $a2['fecha_entrega'];
						
						$i2++;
					}
				}
				
				if($fecha_min!="" && $fecha_min2=="") $data['fecha_min']=$fecha_min; //si sólo se han realizado mantenimientos
				elseif($fecha_min2!="" && $fecha_min=="") $data['fecha_min']=$fecha_min2; // si sólo se ha enviado a taller externo
				elseif($fecha_min!="" && $fecha_min2!="") // Si han habido matenimientos e ingresos a taller externo
				{
					if($fecha_min>=$fecha_min2) $data['fecha_min']=$fecha_min; //se compara cual fecha es la mayor o si son iguales
					else $data['fecha_min']=$fecha_min2;
				}
				else $data['fecha_min']=""; // si no se ha hecho nada
			}
			else
			{
				$data['vehiculos']=$this->transporte_model->consultar_vehiculos(2);
				$data['bandera']='false';
			}
			
			$data['talleres']=$this->transporte_model->consultar_taller_ext();
			pantalla('mantenimiento/ingreso_taller_externo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_taller_ext
	*	Objetivo: Inserta en la Base de Datos un nuevo registro de vehiculo en el taller del externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/

	function guardar_taller_ext()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->guardar_taller_ext($_POST);
			
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			if($_POST['placa']!="" && $_POST['placa']!=NULL) $placa = $_POST['placa'];
			else $placa = $_POST['id_vehiculo_input'];
			
			if($_POST['ntaller_ext']!="" && $_POST['ntaller_ext']!=NULL) $taller = $_POST['ntaller_ext'];
			else $taller = $_POST['id_taller_externo_input'];
			
			$descripcion="Registró el ingreso del vehículo con número de placa ".$placa." al taller externo ".$taller." en la fecha ".$_POST['fecha_recepcion'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			if($_POST['pantalla']==1) ir_a("index.php/vehiculo/control_taller_ext/".$tr."/1");
			elseif($_POST['pantalla']==2) ir_a("index.php/vehiculo/control_taller/".$tr."/2");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: modificar_taller_ext
	*	Objetivo: Modifica en la Base de Datos un registro de vehiculo en el taller del externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 27/06/2015
	*	Observaciones: Ninguna
	*/

	function modificar_taller_ext()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->modificar_taller_ext($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			if($_POST['placa']!="" && $_POST['placa']!=NULL) $placa = $_POST['placa'];
			else $placa = $_POST['id_vehiculo_input'];
			
			if($_POST['ntaller_ext']!="" && $_POST['ntaller_ext']!=NULL) $taller = $_POST['ntaller_ext'];
			else $taller = $_POST['id_taller_externo'];
			
			$descripcion="Modificó el registro de ingreso al taller externo con ID ".$_POST['id_ingreso_taller_ext'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,4);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/control_taller_ext/".$tr."/3");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: ventana_taller_ext
	*	Objetivo: carga la ventana de información para un vehículo en taller externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function ventana_taller_ext($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculo']=$this->transporte_model->vehiculos_taller_externo(0,NULL,$id);
			$data['vehiculo']=$data['vehiculo'][0];
			$this->load->view('mantenimiento/ventana_taller_ext.php',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_taller
	*	Objetivo: insertar en la Base de Datos un nuevo registro de mtto. del taller del MTPS
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 22/12/2014
	*	Observaciones: Ninguna
	*/

	function guardar_taller()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->guardar_taller($_POST);
				
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
				
			$descripcion="Guardó un nuevo registro de mantenimiento vehicular en el taller del MTPS, realizado al vehículo con número de placa: ".$_POST['placa']." en la fecha ".$_POST['fecha']." por el mecánico ".$_POST['id_empleado_mtto_input'];
				
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
				
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/control_taller/".$tr."/1");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_mtto_rutinario
	*	Objetivo: Insertar en la Base de Datos un nuevo registro de mtto. rutinario
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 16/01/2015
	*	Observaciones: Ninguna
	*/

	function guardar_mtto_rutinario()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$this->transporte_model->guardar_mtto_rutinario($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			$descripcion="Registró un nuevo mantenimiento rutinario para el vehículo con placa ".$_POST['placa']." realizado por el mecánico ".$_POST['id_empleado_repara_input']." en la fecha ".$_POST['fecha'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/vehiculos/".$tr."/4");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: alta_taller_ext
	*	Objetivo: carga la vista para dar de alta a un vehículo en taller externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function alta_taller_ext($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculo']=$this->transporte_model->vehiculos_taller_externo(0,NULL,$id);
			$data['vehiculo']=$data['vehiculo'][0];
			
			/*Para validar que no gaste más de la cuenta*/
			$presupuesto=$this->transporte_model->presupuesto_activo();
			foreach($presupuesto as $pre)
			{
				$id_pre=$pre['id_presupuesto'];
			}
			
			$data['presupuesto']=$this->transporte_model->presupuesto($id_pre);
			$data['presupuesto']=$data['presupuesto'][0];
			
			pantalla('mantenimiento/alta_taller_ext.php',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: dar_alta_taller_ext
	*	Objetivo: da de alta a un vehículo en taller externo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function dar_alta_taller_ext()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_EXT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->alta_taller_ext($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			$descripcion="Guardó el registro de alta en el taller externo al vehículo con placa ".$_POST['placa']." llevado a cabo en la fecha ".$_POST['fecha_entrega'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/control_taller_ext/".$tr."/2");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: alta_taller_MTPS
	*	Objetivo: carga la vista para dar de alta a un vehículo en taller interno
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function alta_taller_MTPS($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculo']=$this->transporte_model->vehiculos_taller_interno(0,NULL,$id);
			$data['vehiculo']=$data['vehiculo'][0];
			$data['motoristas']=$this->transporte_model->consultar_motoristas2(1);
			
			/*obteniendo la fecha del último mantenimiento*/
			$fecha_min="";
			$aux=$this->transporte_model->consultar_mantenimientos(NULL,$id);
			if(!empty($aux) && $aux!=0)
			{
				$i=0;
				foreach($aux as $a)
				{
					if($i==0) $fecha_min = $a['fecha'];
					
					$i++;
				}
			}
			
			/*obteniendo la última fecha de ingreso a taller externo*/
			$fecha_min2="";
			$aux2=$this->transporte_model->consultar_ingresos_taller_externo($id);
			if(!empty($aux2) && $aux2!=0)
			{
				$i2=0;
				foreach($aux2 as $a2)
				{
					if($i2==0) $fecha_min2 = $a2['fecha_entrega'];
					
					$i2++;
				}
			}
			
			if($fecha_min!="" && $fecha_min2=="") $data['fecha_min']=$fecha_min; //si sólo se han realizado mantenimientos
			elseif($fecha_min2!="" && $fecha_min=="") $data['fecha_min']=$fecha_min2; // si sólo se ha enviado a taller externo
			elseif($fecha_min!="" && $fecha_min2!="") // Si han habido matenimientos e ingresos a taller externo
			{
				if($fecha_min>=$fecha_min2) $data['fecha_min']=$fecha_min; //se compara cual fecha es la mayor o si son iguales
				else $data['fecha_min']=$fecha_min2;
			}
			else $data['fecha_min']=""; // si no se ha hecho nada
				
			pantalla('mantenimiento/alta_taller_MTPS.php',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: dar_alta_taller_MTPS
	*	Objetivo: da de alta a un vehículo en taller interno
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/01/2015
	*	Observaciones: Ninguna
	*/
	
	function dar_alta_taller_MTPS()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),TALLER_INT);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$this->transporte_model->alta_taller_MTPS($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			
			$descripcion="Guardó el registro de alta en el taller del MTPS al vehículo con placa ".$_POST['placa'].",
						  el cual fue entregado a ".$_POST['id_motorista_recibe_input']." en la fecha ".$_POST['fecha_entrega'];
			
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/control_taller/".$tr."/3");
		}
		else echo "No tiene permisos para acceder en esta pantalla";
	}
	
	/*
	*	Nombre: vehiculo_info
	*	Objetivo: carga los datos de los vehiculos para la vista de Reparación y mantenimiento en taller del MTPS
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 22/07/2014
	*	Observaciones: Ninguna
	*/
	
	function vehiculo_info($id_vehiculo,$estado=NULL,$talleres=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']==3)
		{
			if($talleres!=NULL) $vehiculo=$this->transporte_model->consultar_vehiculo_taller2($id_vehiculo,$estado);
			else $vehiculo=$this->transporte_model->consultar_vehiculo_taller($id_vehiculo,$estado);
			
			$j=json_encode($vehiculo);
			echo $j;
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_vehiculo
	*	Objetivo: Registra los datos de un nuevo vehículo en la Base de Datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/11/2014
	*	Observaciones: Ninguna
	*/
	function guardar_vehiculo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$placa=$this->input->post('placa');
			$id_marca=$this->input->post('marca');
			$id_modelo=$this->input->post('modelo');
			$id_clase=$this->input->post('clase');
			$anio=$this->input->post('anio');
			$id_condicion=$this->input->post('condicion');
			$id_seccion=$this->input->post('seccion');
			$id_empleado=$this->input->post('motorista');
			$id_fuente_fondo=$this->input->post('fuente');
			$tipo_combustible=$this->input->post('tipo_combustible');
			
			if($nmarca!=NULL && $nmarca!="") $mark=$nmarca;
			elseif($id_marca!=0)
			{
				
				$marca_existe=$this->transporte_model->consultar_marcas($id_marca);
				foreach($marca_existe as $mar_ex)
				{
					$mark=$mar_ex->nombre;
				}
			}
			
			if($img_df=="si") $imagen="vehiculo.jpg";
			else
			{
				$config['upload_path'] = './fotografias_vehiculos/';
				$config['allowed_types'] = 'gif|jpg|jpeg|png';
				$config['file_name']=$placa;
						
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload())
				{
					echo "<script language='javascript'>
					alert('Debe seleccionar una imagen de formato: .gif | .jpg | .png | .jpeg');
					</script>";
					pantalla('mantenimiento/nuevo_vehiculo');
				}	
				else
				{
					$file_data = $this->upload->data();
					$imagen=$file_data['file_name'];
				}
			}		
			$nmarca=$this->input->post('nmarca');
			$nmodelo=$this->input->post('nmodelo');
			$nclase=$this->input->post('nclase');
			$nfuente=$this->input->post('nfuente');
			
			if($id_marca==0 && $nmarca!="")
			{
				$id_marca=$this->transporte_model->nueva_marca($nmarca);
			}
			if($id_modelo==0 && $nmodelo!="")
			{
				$id_modelo=$this->transporte_model->nuevo_modelo($nmodelo);
			}
			if($id_clase==0 && $nclase!="")
			{
				$id_clase=$this->transporte_model->nueva_clase($nclase);
			}
			if($id_fuente_fondo==0 && $nfuente!="")
			{
				$id_fuente_fondo=$this->transporte_model->nueva_fuente($nfuente);
			}
			if($id_marca!=0 && $id_modelo!=0 && $id_clase!=0 && $id_fuente_fondo!=0)
			{
				$this->transporte_model->registrar_vehiculo($placa,$id_marca,$id_modelo,$id_clase,$anio,$id_condicion,$tipo_combustible,$id_seccion,$id_empleado,$id_fuente_fondo,$imagen);
				
				
				/*Guardando en bitácora*/
				$id_user=$this->session->userdata('id_usuario');
				
				$descripcion="Se registró un nuevo vehículo con número de placa ".$placa.", marca ".ucwords($mark)." y año ".$anio;
				$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
				
				$this->db->trans_complete();
				$tr=($this->db->trans_status()===FALSE)?0:1;
				ir_a("index.php/vehiculo/vehiculos/".$tr."/1");
			}
			else
			{
				ir_a("index.php/vehiculo/vehiculos/0");
			}
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: modificar_vehiculo
	*	Objetivo: modifica los datos de un vehículo en la Base de Datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/11/2014
	*	Observaciones: Ninguna
	*/
	function modificar_vehiculo($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$placa=$this->input->post('placa');
			$id_vehiculo=$this->input->post('id_vehiculo');
			$id_marca=$this->input->post('marca');
			$id_modelo=$this->input->post('modelo');
			$id_clase=$this->input->post('clase');
			$anio=$this->input->post('anio');
			$id_condicion=$this->input->post('condicion');
			$id_seccion=$this->input->post('seccion');
			$id_empleado=$this->input->post('motorista');
			$id_fuente_fondo=$this->input->post('fuente');
			$tipo_combustible=$this->input->post('tipo_combustible');
			$estado=$this->input->post('estado');
			$img_df=$this->input->post('img_df');
			if($img_df=="si") $imagen=$this->input->post('imagen');
			else
			{
				$config['upload_path'] = './fotografias_vehiculos/';
				$config['allowed_types'] = 'gif|jpg|jpeg|png';
				$config['file_name']=$placa;
						
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload())
				{
					echo "<script language='javascript'>
					alert('Debe seleccionar una imagen de formato: .gif | .jpg | .png | .jpeg');
					</script>";
					pantalla('mantenimiento/nuevo_vehiculo/'.$id);
				}	
				else
				{
					$file_data = $this->upload->data();
					$imagen=$file_data['file_name'];
				}
			}		
			$nmarca=$this->input->post('nmarca');
			$nmodelo=$this->input->post('nmodelo');
			$nclase=$this->input->post('nclase');
			$nfuente=$this->input->post('nfuente');
			
			if($id_marca==0 && $nmarca!="")
			{
				$id_marca=$this->transporte_model->nueva_marca($nmarca);
			}
			if($id_modelo==0 && $nmodelo!="")
			{
				$id_modelo=$this->transporte_model->nuevo_modelo($nmodelo);
			}
			if($id_clase==0 && $nclase!="")
			{
				$id_clase=$this->transporte_model->nueva_clase($nclase);
			}
			if($id_fuente_fondo==0 && $nfuente!="")
			{
				$id_fuente_fondo=$this->transporte_model->nueva_fuente($nfuente);
			}
			if($id_marca!=0 && $id_modelo!=0 && $id_clase!=0 && $id_fuente_fondo!=0)
			{
				$this->transporte_model->modificar_vehiculo($placa,$id_marca,$id_modelo,$id_clase,$anio,$id_condicion,$tipo_combustible,$id_seccion,$id_empleado,$id_fuente_fondo,$imagen,$id,$estado);
								
				/*Guardando en bitácora*/
				$id_user=$this->session->userdata('id_usuario');
				$descripcion="Se modificó el registro del vehículo con ID ".$id_vehiculo;
				$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,4);
				
				$this->db->trans_complete();
				$tr=($this->db->trans_status()===FALSE)?0:1;
				ir_a("index.php/vehiculo/vehiculos/".$tr."/2");
			}
			else
			{
				ir_a("index.php/vehiculo/vehiculos/0");
			}
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: dialogo_vehiculo_info
	*	Objetivo: Modificar los datos de un vehículo en la Base de Datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 16/07/2014
	*	Observaciones: Ninguna
	*/
	
	function dialogo_vehiculo_info($id_vehiculo)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']==3)
		{
			$data['datos']=$this->transporte_model->consultar_vehiculo_taller($id_vehiculo);
			$this->load->view('mantenimiento/dialogo_vehiculo_info.php',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}

	/*
	*	Nombre: vehiculos_pdf
	*	Objetivo: llama a la vista de vehiculo_pdf para odservar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 28/07/2014
	*	Observaciones: Ninguna
	*/
	
	function reporte_vehiculos()
	{
		
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']==3)
		{
			$data['motoristas']=$this->transporte_model->consultar_motoristas2();
			$data['marca']=$this->transporte_model->consultar_marcas();
			$data['modelo']=$this->transporte_model->consultar_modelos();
			$data['clase']=$this->transporte_model->consultar_clases();
			$data['condicion']=$this->transporte_model->consultar_condiciones();
			$data['seccion']=$this->transporte_model->consultar_secciones();
			$data['fuente_fondo']=$this->transporte_model->consultar_fuente_fondo();
			pantalla('mantenimiento/reporte_vehiculos',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}

	/*
	*	Nombre: vehiculos_pdf
	*	Objetivo: llama a la vista de vehiculo_pdf para observar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 29/07/2014
	*	Observaciones: Ninguna
	*/
	
	function vehiculos_pdf()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),VEHICULOS);
		
		if($data['id_permiso']==3)
		{
			$data['datos']=$this->transporte_model->filtro_vehiculo($_POST);
			$this->mpdf->mPDF('utf-8','A4-L'); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
			$stylesheet = file_get_contents('css/pdf/solicitud.css'); /*Selecionamos la hoja de estilo del pdf*/
			$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
			$html = $this->load->view('mantenimiento/vehiculos_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
			$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/
			$this->mpdf->Output(); /*Salida del pdf*/
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: presupuestos
	*	Objetivo: llama a la vista de presupuestos para el control de los presupuestos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 15/12/2014
	*	Observaciones: Ninguna
	*/
	
	function presupuestos($estado_transaccion=NULL,$tipo=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		$data['id_modulo']=PRESUPUESTO;
		if($data['id_permiso']==3)
		{
			if($estado_transaccion!=NULL)
			{
				$data['estado_transaccion']=$estado_transaccion;
				if($tipo!=NULL && $estado_transaccion==1)
				{
					switch($tipo)
					{
						case 1: $data['mensaje']='Se ha registrado un nuevo presupuesto éxitosamente';
								break;
						case 2: $data['mensaje']='Se ha modificado la información del presupuesto éxitosamente';
								break;
						case 3: $data['mensaje']='Se ha reforzado el presupuesto éxitosamente';
								break;
					}
				}
			}
			$data['presupuesto']=$this->transporte_model->presupuesto();
			pantalla('mantenimiento/presupuestos',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: nuevo_presupuesto
	*	Objetivo: función que sirve para ingresar un nuevo presupuestos o modificarlo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/11/2014
	*	Observaciones: Ninguna
	*/
	
	function nuevo_presupuesto($id_presupuesto=0)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		
		if($data['id_permiso']==3)
		{
			if($id_presupuesto!=0)
			{
				$data['presupuesto']=$this->transporte_model->presupuesto($id_presupuesto);
				$data['presupuesto']=$data['presupuesto'][0];
				$data['bandera']=true;
			}
			else $data['bandera']=false;
			pantalla('mantenimiento/nuevo_presupuesto',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: ventana_presupuesto_gastos
	*	Objetivo: carga la ventana en donde se miran detalladamente los gastos realizados de un presupuesto específico
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 05/02/2015
	*	Observaciones: Ninguna
	*/
	
	function ventana_presupuesto_gastos($id_presupuesto)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		
		if($data['id_permiso']==3)
		{	
			$data['presupuesto_info']=$this->transporte_model->presupuesto($id_presupuesto);
			$data['presupuesto_info']=$data['presupuesto_info'][0];
			$data['gastos']=$this->transporte_model->gastos($id_presupuesto);
			$data['refuerzos']=$this->transporte_model->refuerzos($id_presupuesto);
			$this->load->view('mantenimiento/ventana_gastos',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_presupuesto
	*	Objetivo: guarda un nuevo registro de presupuesto
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 25/11/2014
	*	Observaciones: Ninguna
	*/
	
	function guardar_presupuesto()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$presupuesto=$_POST['presupuesto'];
			$this->transporte_model->guardar_presupuesto($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			$this->seguridad_model->bitacora(SISTEMA,$id_user,"Creó un nuevo presupuesto con la cantidad de $ ".number_format($presupuesto,2),3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/presupuestos/".$tr."/1");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: modificar_presupuesto
	*	Objetivo: guarda un nuevo registro de presupuesto
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/11/2014
	*	Observaciones: Ninguna
	*/
	
	function modificar_presupuesto()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$id_presupuesto=$_POST['id_presupuesto'];
			$this->transporte_model->modificar_presupuesto($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			$this->seguridad_model->bitacora(SISTEMA,$id_user,"Modificó el registro de presupuesto con ID ".$id_presupuesto,4);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/presupuestos/".$tr."/2");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: nuevo_refuerzo
	*	Objetivo: llama a la vista de nuevo refuerzo para registrarlo en la base de datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 15/12/2014
	*	Observaciones: Ninguna
	*/
	
	function nuevo_refuerzo($id_presupuesto)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		
		if($data['id_permiso']==3)
		{
			$data['id_presupuesto']=$id_presupuesto;
			pantalla('mantenimiento/nuevo_refuerzo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_refuerzo
	*	Objetivo: guarda un nuevo refuerzo en la base de datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 15/12/2014
	*	Observaciones: Ninguna
	*/
	
	function guardar_refuerzo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTO);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$refuerzo=$_POST['refuerzo'];
			$id_presupuesto=$_POST['id_presupuesto'];
			$this->transporte_model->guardar_refuerzo($_POST);
			
			/*Guardando en bitácora*/
			$id_user=$this->session->userdata('id_usuario');
			$this->seguridad_model->bitacora(SISTEMA,$id_user,"Creó un refuerzo con la cantidad de $ ".number_format($refuerzo,2)." para el presupuesto con ID ".$id_presupuesto,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/presupuestos/".$tr."/3");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: nuevo_articulo
	*	Objetivo: llama a la vista de nuevo artículo para registrarlo en la base de datos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 17/12/2014
	*	Observaciones: Si el valor de la variable $id_articulo es diferente de NULL,la función sirve para modificar la información de un artículo
	*/
	
	function nuevo_articulo($id_articulo=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		
		if($data['id_permiso']==3)
		{
			if($id_articulo!=NULL)
			{
				$data['articulo']=$this->transporte_model->inventario($id_articulo);
				$data['articulo']=$data['articulo'][0];
				$data['bandera']='true';
			}
			else $data['bandera']='false';
			
			/*Para validar que no gaste más de la cuenta*/
			$presupuesto=$this->transporte_model->presupuesto_activo();
			foreach($presupuesto as $pre)
			{
				$id_pre=$pre['id_presupuesto'];
			}
			
			$data['presupuesto']=$this->transporte_model->presupuesto($id_pre);
			$data['presupuesto']=$data['presupuesto'][0];
			
			$data['unidades']=$this->transporte_model->UM();
			pantalla('mantenimiento/nuevo_articulo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: cargar_articulo
	*	Objetivo: llama a la vista de cargar_articulo para suplirlo en la bodega
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 17/12/2014
	*	Observaciones:
	*/
	
	function cargar_articulo($id_articulo)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		
		if($data['id_permiso']==3)
		{
			$data['articulo']=$this->transporte_model->inventario($id_articulo);
			$data['articulo']=$data['articulo'][0];
			
			/*Para validar que no gaste más de la cuenta*/
			$presupuesto=$this->transporte_model->presupuesto_activo();
			foreach($presupuesto as $pre)
			{
				$id_pre=$pre['id_presupuesto'];
			}
			
			$data['presupuesto']=$this->transporte_model->presupuesto($id_pre);
			$data['presupuesto']=$data['presupuesto'][0];
			
			pantalla('mantenimiento/cargar_articulo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: guardar_articulo
	*	Objetivo: registra un nuevo artículo en la bodega.
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 16/12/2014
	*	Observaciones: Ninguna
	*/
	
	function guardar_articulo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$_POST['id_usuario']=$this->session->userdata('id_usuario');
			$cantidad=$_POST['cantidad'];
			$this->transporte_model->guardar_articulo($_POST);
			
			/*Guardando en bitácora*/
			$unidad_medida=$this->transporte_model->UM($_POST['id_unidad_medida']);
			foreach($unidad_medida as $UM)
			{
				$unidades=$UM['unidad_medida'];
			}
			
			if($_POST['adquisicion']=="comprado")
			{
				$descripcion="Se ingresó un nuevo registro de artículo a Bodega, a través de la compra de ".$cantidad." ".$unidades." de ".$_POST['nombre']." por un precio total de $ ".number_format($_POST['gasto'],2);
			}
			else
			{
				$descripcion="Se ingresó un nuevo registro de artículo a Bodega, a través de la donación de ".$cantidad." ".$unidades." de ".$_POST['nombre'];
			}
			
			$id_user=$this->session->userdata('id_usuario');
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,3);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/bodega/".$tr."/3");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: modificar_articulo
	*	Objetivo: modifica la información del artículo en la bodega.
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 17/12/2014
	*	Observaciones: Ninguna
	*/
	
	function modificar_articulo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$this->transporte_model->modificar_articulo($_POST);
			
			/*Guardando en bitácora*/
			$unidad_medida=$this->transporte_model->UM($_POST['id_unidad_medida']);
			foreach($unidad_medida as $UM)
			{
				$unidades=$UM['unidad_medida'];
			}
			
			$descripcion="Modificó el registro de artículo en bodega con ID ".$_POST['id_articulo'];
			$id_user=$this->session->userdata('id_usuario');
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,4);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/bodega/".$tr."/4");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: surtir_articulo
	*	Objetivo: surte en bodega más artículos
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 17/12/2014
	*	Observaciones:
	*/
	
	function surtir_articulo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		
		if($data['id_permiso']==3)
		{
			$this->db->trans_start();
			$this->transporte_model->surtir_articulo($_POST);
			
			/*Guardando en bitácora*/
			$unidad_medida=$this->transporte_model->UM($_POST['id_unidad_medida']);
			foreach($unidad_medida as $UM)
			{
				$unidades=$UM['unidad_medida'];
			}
			
			if($_POST['adquisicion']=="comprado")
			{
				$descripcion="Se surtió el artículo: ".$_POST['nombre2'].", a través de la compra de ".$_POST['cantidad']." ".$unidades." por un precio total de $ ".number_format($_POST['gasto'],2);
			}
			else
			{
				$descripcion="Se surtió el artículo: ".$_POST['nombre2'].", a través de la donación de ".$_POST['cantidad']." ".$unidades;
			}
			
			$id_user=$this->session->userdata('id_usuario');
			$this->seguridad_model->bitacora(SISTEMA,$id_user,$descripcion,4);
			
			$this->db->trans_complete();
			$tr=($this->db->trans_status()===FALSE)?0:1;
			ir_a("index.php/vehiculo/bodega/".$tr."/5");
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: ventana_articulo
	*	Objetivo: llama a la ventana_rtículo para mostrar información detallada del artículo
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 17/12/2014
	*	Observaciones:
	*/
	
	function ventana_articulo($id_articulo)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		
		if($data['id_permiso']==3)
		{
			$data['tta']=$this->transporte_model->transaccion_articulo($id_articulo);
			$data['articulo']=$this->transporte_model->inventario($id_articulo);
			$data['articulo']=$data['articulo'][0];
			$this->load->view('mantenimiento/ventana_articulo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: bodega
	*	Objetivo: llama a la vista de bodega para observar el inventario en bodega
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 13/11/2014
	*	Observaciones: Ninguna
	*/
	
	function bodega($estado_transaccion=NULL,$tipo=NULL)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),BODEGA);
		$data['id_modulo']=BODEGA;
		if($data['id_permiso']==3)
		{
			if($estado_transaccion!=NULL)
			{
				$data['estado_transaccion']=$estado_transaccion;
				if($tipo!=NULL && $estado_transaccion==1)
				{
					switch($tipo)
					{
						case 1: $data['mensaje']='Se ha registrado un nuevo artículo a bodega éxitosamente';
								break;
						case 2: $data['mensaje']='Se ha modificado la información del artículo éxitosamente';
								break;
						case 3: $data['mensaje']='Se ha registrado un nuevo material en bodega éxitosamente';
								break;
						case 4: $data['mensaje']='Se ha cargado el material en bodega éxitosamente';
								break;
						case 5: $data['mensaje']='Se ha cargado el artículo en bodega éxitosamente';
								break;
					}
				}
			}
			$data['inventario']=$this->transporte_model->inventario();
			pantalla('mantenimiento/bodega',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: hoja_ingreso_taller
	*	Objetivo: llama a la vista de hoja_ingreso_taller_pdf para observar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 14/01/2015
	*	Observaciones: Ninguna
	*/
	
	function hoja_ingreso_taller($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HOJA_MTTO);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculo']=$this->transporte_model->vehiculos_taller_interno(0,NULL,$id);
			$data['vehiculo']=$data['vehiculo'][0];
			$data['revisiones']=$this->transporte_model->consultar_revisiones();
			$data['revision']=$this->transporte_model->consultar_revisiones2($id);
			
			$this->mpdf->mPDF('utf-8','A4'); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
			$this->mpdf->SetHTMLHeader('
									<table align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td align="right" valign="top"><img src="img/escudo.min.png" width="90px" /></td>
											<td align="center" valign="top" colspan="2" width="450px">
											MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL<br />
											Departamento de Servicios Generales-Mantenimiento<hr align="center" />
											</td>
											<td align="center" valign="middle"><img src="img/mtps_report.jpg" width="110px" /></td>
										</tr>
									</table>
									');
			$this->mpdf->SetHTMLFooter('
									<table align="center">
									<tr><td align="center">HOJA DE CONTROL DE ENCARGADO DE MANTENIMIENTO.-</td></tr>
									<tr><td align="center"><u>=========================================================</u></td></tr>
									</table>
									');
			$stylesheet = file_get_contents('css/pdf/solicitud.css'); /*Selecionamos la hoja de estilo del pdf*/
			$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
			$html = $this->load->view('mantenimiento/hoja_ingreso_taller_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
			$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/
			$this->mpdf->Output(); /*Salida del pdf*/
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: hoja_mtto_preventivo
	*	Objetivo: llama a la vista de hoja_mtto_preventivo para observar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 19/01/2015
	*	Observaciones: Ninguna
	*/
	
	function hoja_mtto_preventivo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HOJA_MTTO);
		$data['id_modulo']=HOJA_MTTO;
		if($data['id_permiso']==3)
		{
			$data['vehiculos']=$this->transporte_model->consultar_vehiculos_ingreso_taller();
			pantalla('mantenimiento/hoja_mtto_preventivo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: kardex_articulo
	*	Objetivo: llama a la vista de kardex_articulo para generar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 27/01/2015
	*	Observaciones: Ninguna
	*/
	
	function kardex_articulo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),KARDEX);
		$data['id_modulo']=KARDEX;
		if($data['id_permiso']==3)
		{
			$data['articulos']=$this->transporte_model->inventario();
			$data['vehiculos']=$this->transporte_model->consultar_vehiculos();
			pantalla('mantenimiento/kardex_articulos',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: kardex_articulo_json
	*	Objetivo: Obtiene la información relacionada al kardex_articulo para generar los informes y estadísticas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 30/01/2015
	*	Observaciones: Ninguna
	*/
	
	function kardex_articulo_json()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),KARDEX);
		
		if($data['id_permiso']==3)
		{
			$data=$this->transporte_model->kardex_articulo($_POST);
			echo json_encode($data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: kardex_pdf
	*	Objetivo: Función que genera el pdf a imprimir de los informes y estadísticas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 10/02/2015
	*	Observaciones: Ninguna
	*/
	
	function kardex_pdf()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),KARDEX);
		
		if($data['id_permiso']==3)
		{
			$aux=$this->transporte_model->kardex_articulo($_POST);
			$data['j']=json_encode($aux);
			$data['id_articulo']=$_POST['id_articulo'];
			$data['id_vehiculo']=$_POST['id_vehiculo'];
			$data['titulo']=$_POST['titulo'];
			$data['subtitulo']=$_POST['subtitulo'];
			$data['vehiculo']=$_POST['id_vehiculo_input'];
			$data['articulo']=$_POST['id_articulo_input'];
			$data['fecha_inicial']=$_POST['fecha_inicial'];
			$data['fecha_final']=$_POST['fecha_final'];
			$this->load->view('mantenimiento/kardex_pdf',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: hoja_control_vehiculo
	*	Objetivo: llama a la vista de hoja_control_vehiculo para generar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 26/01/2015
	*	Observaciones: Ninguna
	*/
	
	function hoja_control_vehiculo()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HOJA_VEHICULO);
		$data['id_modulo']=HOJA_VEHICULO;
		if($data['id_permiso']==3)
		{
			$data['vehiculos']=$this->transporte_model->consultar_mantenimientos2();
			pantalla('mantenimiento/hoja_control_vehiculo',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: hoja_control_vehiculo_pdf
	*	Objetivo: llama a la vista de hoja_control_vehiculo para generar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 26/01/2015
	*	Observaciones: Ninguna
	*/
	
	function hoja_control_vehiculo_pdf($id)
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),HOJA_VEHICULO);
		
		if($data['id_permiso']==3)
		{
			$data['vehiculo']=$this->transporte_model->consultar_mantenimientos2($id);
			$data['vehiculo']=$data['vehiculo'][0];
			$data['reparaciones']=$this->transporte_model->consultar_reparaciones();
			$data['reparacion']=$this->transporte_model->consultar_reparaciones2($id);
			
			$this->mpdf->mPDF('utf-8','A4-L'); /*Creacion de objeto mPDF con configuracion de pagina y margenes*/
			$this->mpdf->SetHTMLHeader('
									<table align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td align="right" valign="top"><img src="img/escudo.min.png" width="70px" /></td>
											<td align="center" valign="top" colspan="2" width="450px">
											<h3>Hoja Control por Vehículo<br />
											Reparación y Mantenimiento en Taller MTPS
											</h3></td>
											<td align="center" valign="middle"><img src="img/mtps_report.jpg" width="90px" /></td>
										</tr>
									</table>
									');
			$stylesheet = file_get_contents('css/pdf/solicitud.css'); /*Selecionamos la hoja de estilo del pdf*/
			$this->mpdf->WriteHTML($stylesheet,1); /*lo escribimos en el pdf*/
			$html = $this->load->view('mantenimiento/hoja_control_vehiculo_pdf', $data, true); /*Seleccionamos la vista que se convertirá en pdf*/
			$this->mpdf->WriteHTML($html,2); /*la escribimos en el pdf*/
			$this->mpdf->Output(); /*Salida del pdf*/
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: reporte_presupuesto
	*	Objetivo: llama a la vista de reporte_presupuesto para generar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 10/02/2015
	*	Observaciones: Ninguna
	*/
	
	function reporte_presupuesto()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTOS);
		$data['id_modulo']=PRESUPUESTOS;
		if($data['id_permiso']==3)
		{
			$data['presupuesto']=$this->transporte_model->presupuesto();
			$data['vehiculos']=$this->transporte_model->consultar_vehiculos();
			pantalla('mantenimiento/reporte_presupuesto',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: reporte_presupuesto_json
	*	Objetivo: Obtiene la información relacionada al reporte_presupuesto para generar los informes y estadísticas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 10/02/2015
	*	Observaciones: Ninguna
	*/
	
	function reporte_presupuesto_json()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTOS);
		
		if($data['id_permiso']==3)
		{
			$data=$this->transporte_model->presupuesto_mtto($_POST);
			echo json_encode($data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: reporte_presupuesto_pdf
	*	Objetivo: Función que genera el pdf a imprimir de los informes y estadísticas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 10/02/2015
	*	Observaciones: Ninguna
	*/
	
	function reporte_presupuesto_pdf()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),PRESUPUESTOS);
		
		if($data['id_permiso']==3)
		{
			$aux=$this->transporte_model->presupuesto_mtto($_POST);
			$data['j']=json_encode($aux);
			$data['mtto']=$_POST['mtto'];
			$data['id_vehiculo']=$_POST['id_vehiculo'];
			$this->load->view('mantenimiento/reporte_presupuesto_pdf',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: reporte_mantenimientos
	*	Objetivo: llama a la vista de reporte_mantenimientos para generar los reportes
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 20/02/2015
	*	Observaciones: Ninguna
	*/
	
	function reporte_mantenimientos()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),MTTOS);
		$data['id_modulo']=MTTOS;
		if($data['id_permiso']==3)
		{
			$data['mecanicos']=$this->transporte_model->mecanicos();
			$data['vehiculos']=$this->transporte_model->consultar_vehiculos();
			pantalla('mantenimiento/reporte_mantenimientos',$data);
		}

	}
	
	/*
	*	Nombre: reporte_mantenimientos_json
	*	Objetivo: Obtiene la información relacionada al reporte_mantenimiento para generar los informes y estadísticas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 20/02/2015
	*	Observaciones: Ninguna
	*/
	
	function reporte_mantenimientos_json()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),MTTOS);
		
		if($data['id_permiso']==3)
		{
			$data=$this->transporte_model->mantenimientos($_POST);
			echo json_encode($data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
	
	/*
	*	Nombre: reporte_mantenimientos_pdf
	*	Objetivo: Función que genera el pdf a imprimir de los informes y estadísticas
	*	Hecha por: Oscar
	*	Modificada por: Oscar
	*	Última Modificación: 04/03/2015
	*	Observaciones: Ninguna
	*/
	
	function reporte_mantenimientos_pdf()
	{
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'),MTTOS);
		
		if($data['id_permiso']==3)
		{
			$aux=$this->transporte_model->mantenimientos($_POST);
			$data['j']=json_encode($aux);
			
			$data['titulo']=$_POST['titulo'];
			$data['subtitulo']=$_POST['subtitulo'];
			
			$data['mecanico']=$_POST['mecanico'];
			$data['id_vehiculo']=$_POST['id_vehiculo'];
			
			$data['nombre_mecanico']=$_POST['mecanico_input'];
			$data['vehiculo']=$_POST['id_vehiculo_input'];
			
			$data['fecha_inicial']=$_POST['fecha_inicial'];
			$data['fecha_final']=$_POST['fecha_final'];
			
			$this->load->view('mantenimiento/reporte_mantenimientos_pdf',$data);
		}
		else echo "No tiene permisos para acceder a esta pantalla";
	}
}
?>