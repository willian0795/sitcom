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

  public function insertar_secciones_adicionales($post) {
    
    $this->db->insert('tcm_seccion_adicional', $post);

  }

  public function consultar_seccion_adicional($id_uso) {
    $this->db->from('tcm_seccion_adicional')
              ->where('id_seccion_adicional', $id_uso);

    $query = $this->db->get();

    if($query->num_rows() > 0) return $query->row_array();
		else return false;
  }

  public function modificar_seccion_adicional($post) {
    $this->db->where('id_seccion_adicional', $post['id_seccion_adicional']);

    $this->db->update('tcm_seccion_adicional', $post);

  }

}

?>