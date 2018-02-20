<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shelfs extends MX_Controller {

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
| MODULE: 			shelfs
| -----------------------------------------------------
| This is shelfs module controller file.
| -----------------------------------------------------
*/

	 
	function __construct()
	{
		parent::__construct();
		
		// check if user logged in 
		if (!$this->ion_auth->logged_in())
	  	{
			redirect('auth/login');
	  	}
		
		$this->load->library('form_validation');
		$this->load->model('shelfs_model');
		$groups = array('owner', 'admin');
		if (!$this->ion_auth->in_group($groups))
		{
			$this->session->set_flashdata('message', $this->lang->line("access_denied"));
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
			redirect('module=shelfs', 'refresh');
		}

	}
	
   function index()
   {
	   
	  $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
	  $data['success_message'] = $this->session->flashdata('success_message');
	  	
      $meta['page_title'] = $this->lang->line("shelfs");
	  $data['page_title'] = $this->lang->line("shelfs");
      $this->load->view('commons/header', $meta);
      $this->load->view('index', $data);
      $this->load->view('commons/footer');
   }
   
   function getdatatableajax()
   {
 
	   $this->load->library('datatables');
	   $this->datatables
			->select("id, code, name")
			->from("shelfs")
			
			->add_column("Actions", 
			"<center><a href='index.php?module=shelfs&amp;view=edit&amp;id=$1' class='tip' title='".$this->lang->line("edit_shelf")."'><i class=\"icon-edit\"></i></a> <a href='index.php?module=shelfs&amp;view=delete&amp;id=$1' onClick=\"return confirm('". $this->lang->line('alert_x_shelf') ."')\" class='tip' title='".$this->lang->line("delete_shelf")."'><i class=\"icon-remove\"></i></a></center>", "id");
		
	   echo $this->datatables->generate();

   }
	
	function add()
	{

		//validate form input
		$this->form_validation->set_rules('code', $this->lang->line("shelf_code"), 'trim|is_unique[shelfs.code]|required|xss_clean');
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required|min_length[3]|xss_clean');
	
		if ($this->form_validation->run() == true)
		{
			$name = strtolower($this->input->post('name'));
			$code = $this->input->post('code');
			
		}
		
		if ( $this->form_validation->run() == true && $this->shelfs_model->addshelf($name, $code))
		{ //check to see if we are creating the customer
			//redirect them back to the admin page
			$this->session->set_flashdata('success_message', $this->lang->line("shelf_added"));
			redirect("module=shelfs", 'refresh');
		}
		else
		{ //display the create customer form
			//set the flash data error message if there is one
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

			$data['name'] = array('name' => 'name',
				'id' => 'name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('name'),
			);
			$data['code'] = array('name' => 'code',
				'id' => 'code',
				'type' => 'text',
				'value' => $this->form_validation->set_value('code'),
			);
			
		
		$meta['page_title'] = $this->lang->line("new_shelf");
		$data['page_title'] = $this->lang->line("new_shelf");
		$this->load->view('commons/header', $meta);
		$this->load->view('add', $data);
		$this->load->view('commons/footer');
		
		}
	}
	
	function edit($id = NULL)
	{
		if($this->input->get('id')) { $id = $this->input->get('id'); }

		//validate form input
		$this->form_validation->set_rules('code', $this->lang->line("shelf_code"), 'trim|required|xss_clean');
		$pr_details = $this->shelfs_model->getshelfByID($id);
			if ($this->input->post('code') != $pr_details->code) {
				$this->form_validation->set_rules('code', $this->lang->line("shelf_code"), 'is_unique[shelfs.code]');
			}
		$this->form_validation->set_rules('name', $this->lang->line("shelf_name"), 'required|min_length[3]|xss_clean');
		
		if ($this->form_validation->run() == true)
		{

			$data = array('code' => $this->input->post('code'),
				'name' => $this->input->post('name')
				
			);
		}
		
		if ( $this->form_validation->run() == true && $this->shelfs_model->updateshelf($id, $data))
		{ //check to see if we are updateing the customer
			//redirect them back to the admin page
			$this->session->set_flashdata('success_message', $this->lang->line("shelf_updated"));
			redirect("module=shelfs", 'refresh');
		}
		else
		{ //display the update form
			//set the flash data error message if there is one
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

			$data['name'] = array('name' => 'name',
				'id' => 'name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('name'),
			);
			$data['code'] = array('name' => 'code',
				'id' => 'code',
				'type' => 'text',
				'value' => $this->form_validation->set_value('code'),
			);
			
			
		$data['shelf'] = $this->shelfs_model->getshelfByID($id);
		
		$meta['page_title'] = $this->lang->line("update_shelf");
		$data['id'] = $id;
		$data['page_title'] = $this->lang->line("update_shelf");
		$this->load->view('commons/header', $meta);
		$this->load->view('edit', $data);
		$this->load->view('commons/footer');
		
		}
	}
	
	function delete($id = NULL)
	{
		if (DEMO) {
			$this->session->set_flashdata('message', $this->lang->line("disabled_in_demo"));
			redirect('module=home', 'refresh');
		}
		
		if($this->input->get('id')) { $id = $this->input->get('id'); }
		if (!$this->ion_auth->in_group('owner'))
		{
			$this->session->set_flashdata('message', $this->lang->line("access_denied"));
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
			redirect('module=shelfs', 'refresh');
		}
		
		if($this->shelfs_model->getRackByShelfID($id)) {
			$this->session->set_flashdata('message', $this->lang->line("Shelf Has Rack"));
			redirect("module=shelfs", 'refresh');
		}
		
		if ( $this->shelfs_model->deleteshelf($id) )
		{ //check to see if we are deleting the customer
			//redirect them back to the admin page
			$this->session->set_flashdata('success_message', $this->lang->line("shelf_deleted"));
			redirect("module=shelfs", 'refresh');
		}
		
	}
	
	

function racks()
   {
	   
	  if($this->input->get('shelf_id')) { $data['shelf_id'] = $this->input->get('shelf_id'); } else { $data['shelf_id'] = NULL; }
	  
	  $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
	  $data['success_message'] = $this->session->flashdata('success_message');
	  	
      $meta['page_title'] = "Racks";
	  $data['page_title'] = "Racks";
      $this->load->view('commons/header', $meta);
      $this->load->view('racks', $data);
      $this->load->view('commons/footer');
   }
   
   function getrack()
   {
 		if($this->input->get('shelf_id')) { $shelf_id = $this->input->get('shelf_id'); } else { $shelf_id = NULL; } 
		
	   $this->load->library('datatables');
	   $this->datatables
			->select("racks.rack_id as id, racks.code as rcode, racks.name as cname, shelfs.name as sname")
			->from("racks")
			->join('shelfs', 'shelfs.id = racks.shelf_id', 'left')
			->group_by('racks.rack_id');
			
		if($shelf_id) { $this->datatables->where('shelfs_id', $shelf_id); }	
			
		$this->datatables->add_column("Actions", 
			"<center>			<a href='index.php?module=shelfs&amp;view=edit_rack&amp;id=$1' class='tip' title='Edit Rack'><i class=\"icon-edit\"></i></a>
							    <a href='index.php?module=shelfs&amp;view=delete_rack&amp;id=$1' onClick=\"return confirm('delete Rack')\" class='tip' title='Delete Rack'><i class=\"icon-remove\"></i></a></center>", "id")
			->unset_column('id');
		
		
	   echo $this->datatables->generate();

   }
   
	function add_rack()
	{
		

		//validate form input
		$this->form_validation->set_rules('category', $this->lang->line("shelf"), 'required|xss_clean');
		$this->form_validation->set_rules('code', $this->lang->line("rack_code"), 'trim|is_unique[shelfs.code]|is_unique[racks.code]|required|xss_clean');
		$this->form_validation->set_rules('name', $this->lang->line("asdasda_name"), 'required|min_length[3]|xss_clean');
	
		if ($this->form_validation->run() == true)
		{
			$name = strtolower($this->input->post('name'));
			$code = $this->input->post('code');
			$shelf = $this->input->post('category');
			
		}
		
		if ( $this->form_validation->run() == true && $this->shelfs_model->addRack($shelf, $name, $code))
		{ //check to see if we are creating the customer
			//redirect them back to the admin page
			$this->session->set_flashdata('success_message', $this->lang->line("rack_added"));
			redirect("module=shelfs&view=racks", 'refresh');
		}
		else
		{ //display the create customer form
			//set the flash data error message if there is one
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

			$data['name'] = array('name' => 'name',
				'id' => 'name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('name'),
			);
			$data['code'] = array('name' => 'code',
				'id' => 'code',
				'type' => 'text',
				'value' => $this->form_validation->set_value('code'),
			);
			
		$data['categories'] = $this->shelfs_model->getAllShelfs();
		$meta['page_title'] = $this->lang->line("add_rack");
		$data['page_title'] = $this->lang->line("add_rack");
		$this->load->view('commons/header', $meta);
		$this->load->view('add_rack', $data);
		$this->load->view('commons/footer');
		
		}
	}
	
	function edit_rack($id = NULL)
	{
		if($this->input->get('id')) { $id = $this->input->get('id'); }

		//validate form input
		$this->form_validation->set_rules('category', $this->lang->line("shelf"), 'required|xss_clean');
		$this->form_validation->set_rules('code', $this->lang->line("rack_code"), 'trim|required|xss_clean');
		$pr_details = $this->shelfs_model->getRackByID($id);
			if ($this->input->post('code') != $pr_details->code) {
				$this->form_validation->set_rules('code', $this->lang->line("Rack Code"), 'is_unique[categories.code]');
			}
		$this->form_validation->set_rules('name', $this->lang->line("Rack Name"), 'required|min_length[3]|xss_clean');
		
		if ($this->form_validation->run() == true)
		{

			$data = array(
				'shelf_id' => $this->input->post('category'),
				'code' => $this->input->post('code'),
				'name' => $this->input->post('name')
				
			);
		}
		
		if ( $this->form_validation->run() == true && $this->shelfs_model->updateRack($id, $data))
		{ //check to see if we are updateing the customer
			//redirect them back to the admin page
			$this->session->set_flashdata('success_message', $this->lang->line("Rack Update"));
			redirect("module=shelfs&view=racks", 'refresh');
		}
		else
		{ //display the update form
			//set the flash data error message if there is one
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

			$data['name'] = array('name' => 'name',
				'id' => 'name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('name'),
			);
			$data['code'] = array('name' => 'code',
				'id' => 'code',
				'type' => 'text',
				'value' => $this->form_validation->set_value('code'),
			);
			
			
		$data['rack'] = $this->shelfs_model->getRackByID($id);
		$data['categories'] = $this->shelfs_model->getAllShelfs();
		$meta['page_title'] = $this->lang->line("Update Rack");
		$data['id'] = $id;
		$data['page_title'] = "Update Rack";
		$this->load->view('commons/header', $meta);
		$this->load->view('edit_rack', $data);
		$this->load->view('commons/footer');
		
		}
	}
	
	function delete_rack($id = NULL)
	{
		if (DEMO) {
			$this->session->set_flashdata('message', $this->lang->line("disabled_in_demo"));
			redirect('module=home', 'refresh');
		}
		
		if($this->input->get('id')) { $id = $this->input->get('id'); }
		if (!$this->ion_auth->in_group('owner'))
		{
			$this->session->set_flashdata('message', $this->lang->line("access_denied"));
			$data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
			redirect('module=shelfs&view=racks', 'refresh');
		}
		
		if ( $this->shelfs_model->deleteRack($id) )
		{  
			$this->session->set_flashdata('success_message', $this->lang->line("Rack Deleted"));
			redirect("module=shelfs&view=racks", 'refresh');
		}
		
	}
	

   
   
	
	
	
	
	
}

/* End of file shelfs.php */ 
/* Location: ./sma/modules/shelfs/controllers/shelfs.php */