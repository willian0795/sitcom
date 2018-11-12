<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seccion_adicional_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  public function consultar_secciones_adicionales() {
    
    $query = $this->db->get('tcm_seccion_adicional');

    if($query->num_rows() > 0) return $query->result_array();
		else return false;

  }

}

?>