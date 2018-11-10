<?php
	define("INGRESO_PLANTA", 430);
	define("CONSUMO_PLANTA", 431);
    define("USO", 432);
    define("BODEGA",117);
	define ("SISTEMA","5"); //ID del sistema

class Uso extends CI_Controller {

    function __construct() {
        parent::__construct();
		date_default_timezone_set('America/El_Salvador');
        $this->load->model('vales_model');
        $this->load->model('transporte_model');
		$this->load->model('seguridad_model');
    	if(!$this->session->userdata('id_usuario')){
			redirect('index.php/sessiones');
		}
    }



	public function uso($estado_transaccion=NULL,$tipo=NULL) {
		$data=$this->seguridad_model->consultar_permiso($this->session->userdata('id_usuario'), USO);
        $data['id_modulo']=USO;
        
        if($data['id_permiso']!=NULL) {
			if($estado_transaccion!=NULL) {
				$data['estado_transaccion']=$estado_transaccion;
				if($tipo!=NULL && $estado_transaccion==1) {
					switch($tipo) {
						case 1: $data['mensaje']='Se ha registrado un nuevo uso éxitosamente';
								break;
						case 2: $data['mensaje']='Se ha modificado la información del uso éxitosamente';
								break;
					}
				}
			}
			$data['usos']=$this->db->get('tcm_seccion_adicional');
			pantalla('uso/uso',$data);
		}
		else {
			echo 'No tiene permisos para acceder';
		}
    }
      
	function ingreso_vales($estado_transaccion=NULL, $insertado=NULL) {
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
    
}

?>