<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
| -----------------------------------------------------
| PRODUCT NAME: 	Trip Manager
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
| MODULE: 			Categories
| -----------------------------------------------------
| This is categories module's model file.
| -----------------------------------------------------
*/


class Shelfs_model extends CI_Model
{
	
	
	public function __construct()
	{
		parent::__construct();

	}
	
	public function getAllShelfs() 
	{
		$q = $this->db->get("shelfs");
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
	public function getAllRack() 
	{
		$q = $this->db->get("racks");
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
	public function getRackByShelfID($shelf_id) 
	{
		$q = $this->db->get_where("racks", array('shelf_id' => $shelf_id));
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
		
		return FALSE;
	}

	public function getShelfByID($id) 
	{

		$q = $this->db->get_where("shelfs", array('id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	
	public function getRackByID($id) 
	{

		$q = $this->db->get_where("racks", array('rack_id' => $id), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;

	}
	

	
	public function addShelf($name, $code)
	{

		if($this->db->insert("shelfs", array('code' => $code, 'name' => $name))) {
			return true;
		} else {
			return false;
		}
	}
	
	
		public function addRack($shelf, $name, $code)
	{

		if($this->db->insert("racks", array('shelf_id' => $shelf,'code' => $code, 'name' => $name))) {
			return true;
		} else {
			return false;
		}
	}
	

	
	public function updateShelf($id, $data = array())
	{
		
		
		// Shelf data
		$shelfData = array(
		    'code'	     		=> $data['code'],
		    'name'   			=> $data['name'],

		);
		$this->db->where('id', $id);
		if($this->db->update("shelfs", $shelfData)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	public function updateRack($id, $data = array())
	{
		
		// Rack data
		$racksData = array(
		    'shelf_id'	   	=> $data['shelf_id'],
			'code'	     		=> $data['code'],
		    'name'   			=> $data['name'],

		);
		$this->db->where('rack_id', $id);
		if($this->db->update("racks", $racksData)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	public function deleteShelf($id) 
	{
		if($this->db->delete("shelfs", array('id' => $id))) {
			return true;
		}
	return FALSE;
	}
	
	
	public function deleteRack($id) 
	{
		if($this->db->delete("racks", array('rack_id' => $id))) {
			return true;
		}
	return FALSE;
	}
	


}

/* End of file calegories_model.php */ 
/* Location: ./sma/modules/categories/models/categories_model.php */
