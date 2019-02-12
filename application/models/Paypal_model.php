<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Paypal_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/* This function create new Service. */

	public function create($array) {
        $this->db->set($array)->insert('Payments');
		$id = $this->db->insert_id();
		return $id;
	}

	public function getID(){
		$query = $this->db->select('IFNULL(MAX(idVentas), 0) AS id')
		->from('Ventas')->get();
		if($query->num_rows()>0){
			foreach ($query->result() as $id) {
				return  $id->id+1;
			}
		}
		return null;
	}

	public function insertarVenta($array){
		$this->db->set($array)->insert('Ventas');
		$id = $this->db->insert_id();
		return $id;
	}

	public function insertarProductosVendidos($array){
		try{
			$query = $this->db->insert_batch('Productos_Vendidos', $array);
			return true;
		}catch (Exception $e) {
			return false;
		}
	}

	
	public function getPaypalPayment($idPayment){
		$query = $this->db->select('
		Ventas.idVentas, 
		Payments.Total, 
		Payments.PayerMail, 
		Payments.Payment_state, 
		Payments.CreateTime, 
		Productos_Vendidos.idProductos, 
		Productos_Vendidos.Cantidad, 
		Productos_Vendidos.Equipos')
		->from('Ventas')
		->join('Payments', 'Payments.payment_id = Ventas.idPayment', 'RIGHT')
		->join('Productos_Vendidos', 'Productos_Vendidos.idVentas = Ventas.idVentas', 'RIGHT')
		->where('Ventas.idVentas', $idPayment)
		->get();
		if($query->num_rows()>0){
			return $query->result_array();
		}
		return null;
	}

}
