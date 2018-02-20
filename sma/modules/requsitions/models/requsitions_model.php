<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
| -----------------------------------------------------
| PRODUCT NAME: 	STOCK MANAGER ADVANCE 
| -----------------------------------------------------
| AUTHER:			MIAN SALEEM 
| -----------------------------------------------------
| EMAIL:			saleem@tecdiary.com 
| -----------------------------------------------------
| COPYRIGHTS:		RESERVED BY TECDIARY IT SOLUTIONS
| -----------------------------------------------------
| WEBSITE:			http://tecdiary.net
| -----------------------------------------------------
|
| MODULE: 			Requsitions
| -----------------------------------------------------
| This is inventories module's model file.
| -----------------------------------------------------
*/


class Requsitions_model extends CI_Model
{
	
	
	public function __construct()
	{
		parent::__construct();

	}
	
	public function getAllSuppliers() 
	{
		$this->db->select('id, name, company')->from('suppliers');
		$q = $this->db->get();
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}

	public function getAllTaxRates() 
	{
		$q = $this->db->get('tax_rates');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
	}
	
	public function getTaxRateByID($id) 
	{

		$q = $this->db->get_where('tax_rates', array('id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function getSupplierByID($id) 
	{

		$q = $this->db->get_where('suppliers', array('id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function getAllProducts() 
	{
		$q = $this->db->get('products');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
		public function getAllInventoryIAlerttems() 
	{
	
	
		$q = $this->db->query("SELECT * FROM products WHERE alert_quantity > quantity AND track_quantity=1");
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
		
			  
	}
	
	public function getProductByID($id) 
	{

		$q = $this->db->get_where('products', array('id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function getNextAI() 
	{
		$this->db->select_max('id');
		$q = $this->db->get('requsitions');
		if( $q->num_rows() > 0 )
		  {
			$row = $q->row();
			//return QUOTE_REF."-".date('Y')."-".sprintf("%03s", $row->id+1);
			return PURCHASE_REF."-".sprintf("%04s", $row->id+1);
		  } 
				
			return FALSE;

	}
	
	public function getProductsByCode($code) 
	{
		$this->db->select('*')->from('products')->like('code', $code, 'both');
		$q = $this->db->get();
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
	
	public function getProductByCode($code) 
	{

		$q = $this->db->get_where('products', array('code' => $code), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	public function getProductNames($term)
    {
	   	$this->db->select('name')->limit('10');
	    $this->db->like('name', $term, 'both');
   		$q = $this->db->get('products');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data; 
		}
    }
	
	public function getProductByName($name)
	{

		$q = $this->db->get_where('products', array('name' => $name), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function updateProductQuantity($product_id, $quantity, $warehouse_id, $product_cost)
	{

		// update the product with new details
		if($this->updatePrice($product_id, $product_cost) && $this->addQuantity($product_id, $warehouse_id, $quantity))
		{
			return true;
		} 
			
			return false;
	}
	
	public function calculateAndUpdateQuantity($item_id, $product_id, $quantity, $warehouse_id, $product_cost)
	{

		// update the product with new details
		if($this->updatePrice($product_id, $product_cost) && $this->calculateAndAddQuantity($item_id, $product_id, $warehouse_id, $quantity))
		{
			return true;
		} 
			
			return false;
	}
	
	public function calculateAndAddQuantity($item_id, $product_id, $warehouse_id, $quantity) 
	{

		
		//check if entry exist then update else inster
		if($this->getProductQuantity($product_id, $warehouse_id)) {
			
		//get product details to calculate quantity
		$quantity_details = $this->getProductQuantity($product_id, $warehouse_id);
		$product_quantity = $quantity_details['quantity'];
		$item_details = $this->getItemByID($item_id);
		$item_quantity = $item_details->quantity;
		$after_quantity = $product_quantity - $item_quantity;
		$new_quantity = $after_quantity + $quantity;
		
					
			if($this->updateQuantity($product_id, $warehouse_id, $new_quantity)){
				return TRUE;
			}
			
		} else {
						
			if($this->insertQuantity($product_id, $warehouse_id, $quantity)){
				return TRUE;
			}
		}
		
		return FALSE;

	}
	
	public function addQuantity($product_id, $warehouse_id, $quantity) 
	{

		//check if entry exist then update else inster
		if($this->getProductQuantity($product_id, $warehouse_id)) {
			
		$warehouse_quantity = $this->getProductQuantity($product_id, $warehouse_id);	
		$old_quantity = $warehouse_quantity['quantity'];		
		$new_quantity = $old_quantity + $quantity;
					
			if($this->updateQuantity($product_id, $warehouse_id, $new_quantity)){
				return TRUE;
			}
			
		} else {
						
			if($this->insertQuantity($product_id, $warehouse_id, $quantity)){
				return TRUE;
			}
		}
		
		return FALSE;

	}
	
	public function insertQuantity($product_id, $warehouse_id, $quantity)
	{	

			// Product data
			$productData = array(
				'product_id'	     		=> $product_id,
				'warehouse_id'   			=> $warehouse_id,
				'quantity' 					=> $quantity
			);

		if($this->db->insert('warehouses_products', $productData)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	public function updateQuantity($product_id, $warehouse_id, $quantity)
	{	

			$productData = array(
				'quantity'	     			=> $quantity
			);
		
		//$this->db->where('product_id', $id);		
		if($this->db->update('warehouses_products', $productData, array('product_id' => $product_id, 'warehouse_id' => $warehouse_id))) {
			return true;
		} else {
			return false;
		}
	}
	public function getProductQuantity($product_id, $warehouse) 
	{
		$q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1); 
		
		  if( $q->num_rows() > 0 )
		  {
			return $q->row_array(); //$q->row();
		  } 
		
		  return FALSE;
		
	}
	
	
	public function updatePrice($id, $unit_price)
	{
		
		// Product data 
		$productData = array(
		    'cost'   			=> $unit_price
		);
		
		$this->db->where('id', $id);
		if($this->db->update('products', $productData)) {
			return true;
		} 
			
			return false;

	}
	
	public function getAllInventories() 
	{
		$q = $this->db->get('requsitions');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
	public function inventories_count() {
        return $this->db->count_all("requsitions");
    }

    public function fetch_inventories($limit, $start) {
        $this->db->limit($limit, $start);
		$this->db->order_by("id", "desc"); 
        $query = $this->db->get("requsitions");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
   }
	
	public function getAllInventoryItems($purchase_id) 
	{
		$this->db->order_by('id', 'asc');
		$q = $this->db->get_where('requsition_items', array('purchase_id' => $purchase_id));
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	public function getInventoryByID($id)
	{

		$q = $this->db->get_where('requsitions', array('id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function getItemByID($id)
	{

		$q = $this->db->get_where('requsition_items', array('id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function getInventoryByPurchaseID($purchase_id)
	{

		$q = $this->db->get_where('requsitions', array('id' => $purchase_id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	
		public function getWorkorderByPurchaseID($purchase_id)
	{

		$q = $this->db->query("SELECT p.*, u.username as chk_name, u1.username as app_name, u2.username as verify_name FROM requsitions as p LEFT JOIN users as u ON p.`checked_by` = u.id LEFT JOIN users 
		as u1 ON p.`approved_by` = u1.id LEFT JOIN users as u2 ON p.`verify_by` = u2.id WHERE p.id='$purchase_id'");
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function npQTY($product_id, $quantity) {
		$prD = $this->getProductByID($product_id);
		$nQTY = $prD->quantity + $quantity;
		$this->db->update('products', array('quantity' => $nQTY), array('id' => $product_id));
	}
	
	public function addPurchase($pdata, $items = array(), $warehouse_id)
	{
				
		// purchase data
		$purchseData = array(
			'reference_no'			=> $pdata['reference_no'],
			'warehouse_id'			=> $warehouse_id,
		    'supplier_id'			=> $pdata['supplier_id'],
			'supplier_name'			=> $pdata['supplier_name'],
			'date'					=> $pdata['date'],
			'note'	  	 			=> $pdata['note'],
			'total_tax'	  	 			=> $pdata['total_tax'],
			'inv_total'				=> $pdata['inv_total'],
			'total'					=> $pdata['total'],
			'user'					=> USER_NAME
		);

		if($this->db->insert('requsitions', $purchseData)) {
			$purchase_id = $this->db->insert_id();
			
			foreach($items as $data){
				$this->npQTY($data['product_id'], $data['quantity']);
				//$this->updateProductQuantity($data['product_id'], $data['quantity'], $warehouse_id, $data['unit_price']);
			}
		
			$addOn = array('purchase_id' => $purchase_id);
					end($addOn);
					foreach ( $items as &$var ) {
						$var = array_merge($addOn, $var);
			}
				
			if($this->db->insert_batch('requsition_items', $items)) {
				return true;
			}
		}
		return false;
	}
	
	public function upQTY($product_id, $quantity) {
		$prD = $this->getProductByID($product_id);
		$nQTY = $prD->quantity - $quantity;
		$this->db->update('products', array('quantity' => $nQTY), array('id' => $product_id));
	}
	
	public function updatePurchase($id, $pdata, $items = array(), $warehouse_id)
	{
		
		$old_items = $this->getAllInventoryItems($id);
		$old_inv = $this->getInventoryByID($id);
		foreach($old_items as $data){
			$item_id = $data->id;
			$item_details = $this->getItemByID($item_id);
			$item_qiantity = $item_details->quantity;
			$product_id = $data->product_id;
			$pr_qty_details = $this->getProductQuantity($product_id, $old_inv->warehouse_id);
			$pr_qty = $pr_qty_details['quantity'];
			$qty = $pr_qty - $item_qiantity;
			
			$this->updateQuantity($product_id, $old_inv->warehouse_id, $qty);
			$this->upQTY($product_id, $item_qiantity);
			
		}
		
		$purchseData = array(
			'reference_no'			=> $pdata['reference_no'],
			'warehouse_id'			=> $warehouse_id,
		    'supplier_id'			=> $pdata['supplier_id'],
			'supplier_name'			=> $pdata['supplier_name'],
			'date'					=> $pdata['date'],
			'note'	  	 			=> $pdata['note'],
			'total_tax'	  	 		=> $pdata['total_tax'],
			'inv_total'				=> $pdata['inv_total'],
			'total'					=> $pdata['total'],
			'updated_by'			=> USER_NAME
		);


		$this->db->where('id', $id);
		if($this->db->update('requsitions', $purchseData) && $this->db->delete('requsition_items', array('purchase_id' => $id))) {
			
			foreach($items as $data){
				$this->npQTY($data['product_id'], $data['quantity']);
				//$this->updateProductQuantity($data['product_id'], $data['quantity'], $warehouse_id, $data['unit_price']);
			}
						
			$addOn = array('purchase_id' => $id);
				end($addOn);
				foreach ( $items as &$var ) {
						$var = array_merge($addOn, $var);
				}
		
		
			if($this->db->insert_batch('requsition_items', $items)) {
				return true;
			}
		

	}
	
		return false;
	}


    public function updateMrr($id, $pdata, $items = array(), $warehouse_id)
	{
		
		$purchseData = array(
			'mr_reference_no'		=> $pdata['mr_reference_no'],
			'mr_date'				=> $pdata['date'],
			'mr_entry_date'			=> date("Y-m-d"),
			'mr_status'				=> $pdata['status'],
			'mr_entry_by'			=> USER_ID
		);


		$this->db->where('id', $id);
		if($this->db->update('requsitions', $purchseData) && $this->db->delete('requsition_items', array('purchase_id' => $id))) {
			
			foreach($items as $data){
				$this->npQTY($data['product_id'], $data['quantity']);
				//$this->updateProductQuantity($data['product_id'], $data['quantity'], $warehouse_id, $data['unit_price']);
			}
						
			$addOn = array('purchase_id' => $id);
				end($addOn);
				foreach ( $items as &$var ) {
						$var = array_merge($addOn, $var);
				}
		
		
			if($this->db->insert_batch('requsition_items', $items)) {
				return true;
			}
		

	}
	
		return false;
	}

	public function updateCheck($purchase_id){
		
		$data = array('checked' => 1, 'checked_by' => USER_ID, 'checked_at' => date('Y-m-d H:i:s'));
		$this->db->where('id', $purchase_id);
		if($this->db->update('requsitions', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function updateApprove($purchase_id){
		$data = array('approved' => 1, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
		$this->db->where('id', $purchase_id);
		if($this->db->update('requsitions', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
		public function updateMrrApprove($purchase_id){
		$data = array('mr_status' => 2, 'mr_approve_by' => USER_ID, 'mr_approve_date' => date('Y-m-d H:i:s'));
		$this->db->where('id', $purchase_id);
		if($this->db->update('requsitions', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function updateVerify($purchase_id){
		$data = array('verify_status' => 1, 'verify_by' => USER_ID, 'verify_at' => date('Y-m-d H:i:s'));
		$this->db->where('id', $purchase_id);
		if($this->db->update('requsitions', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function cancelRequisition($purchase_id){
		$data = array('approved' => 2, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
		$this->db->where('id', $purchase_id);
		if($this->db->update('requsitions', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
		public function cancelMrr($purchase_id){
		$data = array('approved' => 2, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
		$data = array('mr_status' => 3);
		$this->db->where('id', $purchase_id);
		if($this->db->update('requsitions', $data)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	public function getAllWarehouses() 
	{
		$q = $this->db->get('warehouses');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
	public function deleteInventory($id) 
	{
		$inv = $this->getInventoryByID($id);
		$warehouse_id = $inv->warehouse_id;
		$items = $this->getAllInventoryItems($id);
		
		foreach($items as $item) {
			$product_id = $item->product_id;
			$item_details = $this->getProductQuantity($product_id, $warehouse_id);
			$pr_quantity = $item_details['quantity'];
			$inv_quantity = $item->quantity;
			$new_quantity = $pr_quantity - $inv_quantity;
			
			$this->updateQuantity($product_id, $warehouse_id, $new_quantity);
                        $this->upQTY($product_id, $item->quantity);
		}
		
		if($this->db->delete('requsition_items', array('purchase_id' => $id)) && $this->db->delete('requsitions', array('id' => $id))) {
			return true;
		}
	return FALSE;
	}
	
	public function getWarehouseProductQuantity($warehouse_id, $product_id)
	{

		$q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
        
        public function getProductCodes($term)
    {
	   	$this->db->select('code');
	    $this->db->like('code', $term, 'both')->limit('10');
   		$q = $this->db->get('products');
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data; 
		}
    }
	
	
}
