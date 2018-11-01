<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jhonatan Flores
 * @link		http://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Form Declaration
 *
 * Creates the opening portion of the form.
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */
	define('ID_SISTEMA', 5);

	function pantalla($vista, $data=NULL) 
	{
		$CI =& get_instance();

		
		$data['usuario']=$CI->session->userdata('usuario');
		$data['nick']=$CI->session->userdata('usuario');
		$data['nombre']=$CI->session->userdata('nombre');
		$data['menus']=$CI->seguridad_model->buscar_menus($CI->session->userdata('id_usuario'));

////para ayuda

		$descripcion_ayuda="";
		$pasos="";
		$problemas="";
		if($data['id_modulo']!=NULL){
			$id_modulo=$data['id_modulo'];
			$descripcion_ayuda=$CI->usuario_model->get_ayuda($id_modulo);
			$pasos=$CI->usuario_model->get_ayuda_pasos($id_modulo);
			$problemas=$CI->usuario_model->get_ayuda_problemas($id_modulo);

		}

		$data['descripcion_ayuda']=$descripcion_ayuda;
		$data['pasos']=$pasos;
		$data['problemas']=$problemas;
	
	//fin para ayuda
	// echo "<pre>"; print_r($data);echo "</pre>";
	$CI->load->view('encabezado',$data);
	$CI->load->view($vista);	
	$CI->load->view('piePagina');
	}

	function ir_a($url){

		/*
	echo'<script language="JavaScript" type="text/javascript">
				var pagina="'.base_url().$url.'"
				function redireccionar() 
				{
				location.href=pagina
				} 
				setTimeout ("redireccionar()", 600);	
				
				</script>'; */

	echo'<script language="JavaScript" type="text/javascript">
				var pagina="'.base_url().$url.'"
				window.location.href=pagina
				</script>'; 
		
		}	

	function nuevaVentana($url){
		echo'<script language="JavaScript" type="text/javascript">
				var pagina2="'.base_url().$url.'"
				function nuevaVentana() 
				{
				window.open(pagina2,"_blank");
				} 
				setTimeout ("nuevaVentana()", 300);
				
				</script>';
		
		}	

	/*function enviar_correo($correo=array(),$title,$message) */
	function enviar_correo($correo,$title,$message)
	{

		$title= utf8_decode($title);
		$message= utf8_decode($message);
		$CI =& get_instance();
		$CI->load->library("phpmailer");
		
		$mail = $CI->phpmailer->load();
		$mail->SMTPSecure = "ssl";
		$mail->Host = "mtrabajo.mtps.gob.sv";
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPDebug = 4;
		
		$mail->Port = 465;
		$mail->Username = "departamento.transporte@mtps.gob.sv";
		$mail->Password = "j/82}k8AXmI&89e.-0?323";
		$mail->From = "departamento.transporte@mtps.gob.sv";
		$mail->FromName = "Departamento de Transporte";
		$mail->IsHTML(true);
		$mail->Timeout = 1000;
		/*for($i=0;$i<count($correo);$i++)
			$mail->AddAddress( $correo[$i] );*/
		$mail->AddAddress( $correo );
		$mail->ContentType = "text/html";
		$mail->Subject = $title;
		$mail->Body = $message;
		$r=$mail->Send();
		if(!$r) {
			echo "Error al enviar: " .$mail->ErrorInfo;
			} else {
			echo "Mensaje enviado!";
		}
		// return $r;
	}
	
	/*function enviar_correo($correo=array(),$title,$message) */
	function enviar_correo_cc($id_solicitud,$id_usuario,$correo,$aux) 
	{
		$CI =& get_instance();
		$CI->load->model('usuario_model');
		$CI->load->model('transporte_model');
		$datos=$CI->usuario_model->info_usuario($id_usuario);
		$solicitud=$CI->transporte_model->consultar_solicitud($id_solicitud);
		/*$mod=$CI->usuario_model->datos_modulo($id_modulo);
		$url="Presione <a href='".base_url()."index.php/".$mod['url_modulo']."'>aqui <a/> para verla en el sistema";*/
		$data['a']=$CI->transporte_model->acompanantes_internos($id_solicitud);
		$data['f']=$CI->transporte_model->destinos($id_solicitud);
		$data['observaciones']=$CI->transporte_model->observaciones($id_solicitud);
		for($i=0;$i<count($datos);$i++) {
			$nombre=ucwords($datos[$i]['nombre']);
			$id_usuario=$datos[$i]['id_usuario'];
			
			$titulo="SOLICITUD DE TRANSPORTE N°".$id_solicitud;
			$data['id_solicitud_transporte']=$id_solicitud;
			$data['nombre']=$nombre;
			$data['solicitud']=$solicitud;
			$data['id_usuario']=$id_usuario;
			$data['bandera']=$aux;
			$mensaje=$CI->load->view('transporte/correo_cc',$data,true);
			//echo $mensaje;			
		}
		$r=enviar_correo($correo,$titulo,$mensaje);
	}
	
	
	function enviar_por_gmail($correo,$title,$message) 
	{

			$CI =& get_instance();
			$CI->load->library("phpmailer");		
			$mail = new PHPMailer();
			//indico a la clase que use SMTP
			$mail->isSMTP();
			//permite modo debug para ver mensajes de las cosas que van ocurriendo 2 para desactivar  1
			$mail->SMTPDebug = 1;
			//Debo de hacer autenticación SMTP
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			//indico el servidor de Gmail para SMTP
			$mail->Host = "smtp.gmail.com";
			//indico el puerto que usa Gmail
			$mail->Port = 465;
			//indico un usuario / clave de un usuario de gmail
			$mail->Username = "informatica.mtps@gmail.com";
			$mail->Password = "wsx!@#mko";
			$mail->From = "informatica.mtps@gmail.com";
			$mail->FromName = "Informatica MTPS";
			$mail->AddReplyTo("informatica.mtps@gmail.com","Informatica mtps");
			$mail->Subject = $title;
			$mail->MsgHTML($message);
			//indico destinatario
			$mail->AddAddress( $correo);
			$r=$mail->Send();
				if(!$r) {
					//echo "Error al enviar: " .$mail->ErrorInfo;
					} else {
					//echo "Mensaje enviado!";
				}
				return $r;
	}
	
	function enviar_correo_automatico_administracion($id_solicitud_transporte=NULL, $id_modulo=NULL)
	{
		$CI =& get_instance();
		$CI->load->model('usuario_model');
		$CI->load->model('transporte_model');
		$datos=$CI->usuario_model->buscar_correos($id_solicitud_transporte, $id_modulo);
		$solicitud=$CI->transporte_model->consultar_solicitud($id_solicitud_transporte);
		$mod=$CI->usuario_model->datos_modulo($id_modulo);
		$url="<p style='font-family: arial,verdana,sans-serif;'>Ingrese <a href='".base_url()."index.php/".$mod['url_modulo']."' title='Visualizar en el sistema'>aquí</a> para visualizar la solicitud en el sistema.</p>";
		$data['a']=$CI->transporte_model->acompanantes_internos($id_solicitud_transporte);
		$data['f']=$CI->transporte_model->destinos($id_solicitud_transporte);
		$data['observaciones']=$CI->transporte_model->observaciones($id_solicitud_transporte);
		
		for($i=0;$i<count($datos);$i++) {
			$nombre=ucwords($datos[$i]['nombre']);
			$correo=ucwords($datos[$i]['correo']);
			$id_usuario=$datos[$i]['id_usuario'];
			
			$nominal=ucwords($datos[$i]['nominal']);
			$mensaje="<div style='background-color:#F5F6F8; padding: 15px;'><div style='background-color: #FFFFFF; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 4px; border: 1px solid #e1e1e1;'>
					<h2 style='font-family: arial,verdana,sans-serif; margin-top: 0;'>¡Bienvenida/o al Sistema de Transporte!</h2>
					<p style='font-family: arial,verdana,sans-serif;'>Estimada/o $nombre:</p>";
			switch($id_modulo){
				case 66:
					$titulo="SOLICITUD DE TRANSPORTE N°".$id_solicitud_transporte;
					$data['id_solicitud_transporte']=$id_solicitud_transporte;
					$data['nombre']=$nombre;
					$data['solicitud']=$solicitud;
					$data['id_usuario']=$id_usuario;
					$mensaje=$CI->load->view('transporte/correo_aprobacion',$data,true);
					
					break;
				case 68:
					
					$titulo="SOLICITUD DE TRANSPORTE N°".$id_solicitud_transporte;
					$mensaje.="requiere asignaci&oacute;n de veh&iacute;culo/motorista. ".$url."<br><br>Departamento de Transporte.";
					$mensaje.="<p style='font-family: arial,verdana,sans-serif;'>Su solicitud No $id_solicitud_transporte para el ".$datos['fecha_mision']." en el horario de ".$solicitud['hora_salida']." a ".$solicitud['hora_entrada']." requiere asignaci&oacute;n de veh&iacute;culo/motorista.</p>".$url;
					break;
				default:
					$titulo="X";
					$mensaje="X";
			}
			$r=enviar_correo($correo,$titulo,$mensaje);
			
		}
	}
	
	function enviar_correo_automatico_usuarios($id_solicitud_transporte=NULL) 
	{
		$CI =& get_instance();
		$CI->load->model('usuario_model');
		$CI->load->model('transporte_model');
		$datos=$CI->usuario_model->buscar_correo($id_solicitud_transporte);
		$solicitud=$CI->transporte_model->consultar_solicitud($id_solicitud_transporte);
		$nombre=ucwords($datos['nombre']);
		$correo=ucwords($datos['correo']);
		
		
		$nominal=ucwords($datos['nominal']);
		$mensaje="<div style='background-color:#F5F6F8; padding: 15px;'><div style='background-color: #FFFFFF; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 4px; border: 1px solid #e1e1e1;'>
					<h2 style='font-family: arial,verdana,sans-serif; margin-top: 0;'>¡Bienvenida/o al Sistema de Transporte!</h2>
					<p style='font-family: arial,verdana,sans-serif;'>Estimada/o $nombre:</p>";
		switch($datos['estado']){
			case 0:	
				$titulo="SOLICITUD DE TRANSPORTE N°".$id_solicitud_transporte;
				$mensaje.="<p style='font-family: arial,verdana,sans-serif;'>Su solicitud No $id_solicitud_transporte para el ".$datos['fecha_mision']." en el horario de ".$solicitud['hora_salida']." a ".$solicitud['hora_entrada']." ha sido aprobada.</p>
							Puede que se deba a uno de los siguientes motivos: ".$datos['observacion'];
				break;
			case 2:
				$titulo="SOLICITUD DE TRANSPORTE N°".$id_solicitud_transporte;
				$mensaje.="<p style='font-family: arial,verdana,sans-serif;'>Su solicitud No $id_solicitud_transporte para el ".$datos['fecha_mision']." en el horario de ".$solicitud['hora_salida']." a ".$solicitud['hora_entrada']." ha sido aprobada.</p>";
				break;
			case 3:
				$d=$CI->transporte_model->datos_motorista_vehiculo($id_solicitud_transporte);
				$titulo="SOLICITUD DE TRANSPORTE N°".$id_solicitud_transporte;
				$mensaje.= "<p style='font-family: arial,verdana,sans-serif;'>Su solicitud No $id_solicitud_transporte para el ".$datos['fecha_mision']." en el horario de ".$solicitud['hora_salida']." a ".$solicitud['hora_entrada']." ha sido asignada al vehículo con placa ". $d['placa'] .".</p>";
				break;
			default:
				$titulo="Y";
				$mensaje="Y";
		}

		$mensaje .= "<br><br>
					<p style='font-family: arial,verdana,sans-serif;'>Atentamente,</p>
					<p style='font-family: arial,verdana,sans-serif;'>Departamento de Transporte</p>
					<p style='font-family: arial,verdana,sans-serif;'>Dirección Administrativa</p>
					<img style='height: 106px; width: 173px;' src=".base_url()."'img/logo_izquierdo.jpg'>
		";
		$r=enviar_correo($correo,$titulo,$mensaje);
	}
	
	function alerta($msj,$url){
		echo'
	<link href="'.base_url().'css/default.css" rel="stylesheet" type="text/css" />
		<link href="'.base_url().'css/component.css" rel="stylesheet" type="text/css" />
        <link href="'.base_url().'css/kendo.common.min.css" rel="stylesheet" type="text/css" />
        <link href="'.base_url().'css/kendo.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="'.base_url().'css/kendo.dataviz.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="'.base_url().'css/tooltipster.css" rel="stylesheet" type="text/css" />
		<link href="'.base_url().'css/alertify.core.css" rel="stylesheet" />
		<link href="'.base_url().'css/alertify.default.css" rel="stylesheet" />
        <link href="'.base_url().'css/style-base.css" rel="stylesheet" type="text/css" />
        <script src="'.base_url().'js/jquery-1.8.2.js"></script>
        <!--<script src="'.base_url().'js/jquery-ui-1.9.0.custom.js"></script>-->
		<script src="'.base_url().'js/classie.js"></script>
        <script src="'.base_url().'js/kendo.all.min.js" type="text/javascript"></script>
        <script src="'.base_url().'js/jquery.tooltipster.js" type="text/javascript"></script>
        <script src="'.base_url().'js/jquery.leanModal.min.js" type="text/javascript"></script>
        <script src="'.base_url().'js/waypoints.min.js"></script>
        <script src="'.base_url().'js/alertify.js" type="text/javascript"></script>
		<script language="JavaScript" type="text/javascript">
		
				var pagina="'.base_url().$url.'"
				function redireccionar() 
				{
				alertify.alert("'.$msj.'");
				setTimeout("partB()",2000)
				} 
				function partB() 
				{
				location.href=pagina
				} 
				setTimeout ("redireccionar()",1000);			
		</script>';
		
		}	
 function deleteform($item)
  {
  	$array=$_SESSION['form'];
  	$item=$item['keyform'];

  	foreach ($array as $key => $value) {
  		if($value==$item){
  			unset($_SESSION['form'][$key]);
  		}

  	}
  }

 function verificarform($item)
  {
  	$array=$_SESSION['form'];
  	$item=$item['keyform'];
  	$ban=false;
  	foreach ($array as $key => $value) {
  		if($value==$item){
  			$ban=true;
  		}

  	}
  	return $ban;
  }

function randomkey($length=16)
      {
        $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";

        for($i=0;$i<$length;$i++)
            $key .= $pattern{rand(0,35)};

       $tem=$_SESSION['form'];
       $tem[]=$key;
       $_SESSION['form']=$tem;

       return $key;
      }  

function llaveform(){
	echo '<input value="'.randomkey().'" type="hidden" name="keyform"/>';
}

function restar_mes($mes=NULL,$rest=NULL)
{	$arr2 = str_split($mes, 4); //divide en 4 digitos, cada arreglo
	$a=$arr2[0];
	$m=$arr2[1];

	if($rest>0){
		
		while ( $rest>=12) {
			$a--; $rest-=12;
		}
		
		$m=$m-$rest;
		//echo $m;
		if($m<0){
			//$m=13-$rest;
			$m=12-(-$m);
			$a--;
		}	
		if($m==0){
			$m=12;
			$a--;
		}
	}else{

		while ( $rest<=-12) {
			$a++; $rest+=12;
		}
		
		$m=$m-$rest;
		//echo $m;
				
		if($m>12){
			//$m=13-$rest;
			$m-=12;
			$a++;
		}	
		if($m==0){
			$m=1;
			$a++;
		}

	}

	if($m<10){ ///para que se mantenga el formato de dos digitos en mes
		$m="0".$m;
	}
	$nmes=$a.$m;	
	return $nmes;
}

function getUltimoDiaMes($elAnio=NULL,$elMes=NULL) {
		if ($elMes==NULL) {
			$elMes=date('m');
		}
		if ($elAnio==NULL) {
			$elAnio=date('Y');
		}
  return date("d",(mktime(0,0,0,$elMes+1,1,$elAnio)-1));
}

	function bitacora($descripcion='',$id_accion)
	{
		$CI =& get_instance();
		$id_usuario=$CI->session->userdata('id_usuario');		
		$CI->seguridad_model->bitacora(ID_SISTEMA,$id_usuario,$descripcion,$id_accion);

	}
function getID_SISTEMA()
{
	return ID_SISTEMA;
}

/* End of file tools_helper.php */
/* Location: ./system/helpers/form_helper.php */
