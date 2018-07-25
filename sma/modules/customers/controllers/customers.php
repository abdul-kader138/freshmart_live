<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends MX_Controller
{

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
    | MODULE: 			Customers
    | -----------------------------------------------------
    | This is customers module controller file.
    | -----------------------------------------------------
    */


    function __construct()
    {
        parent::__construct();

        // check if user logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
        }

        $this->load->library('form_validation');
        $this->load->model('customers_model');


    }

    function index()
    {

        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        $data['success_message'] = $this->session->flashdata('success_message');

        $meta['page_title'] = $this->lang->line("customers");
        $data['page_title'] = $this->lang->line("customers");
        $this->load->view('commons/header', $meta);
        $this->load->view('content', $data);
        $this->load->view('commons/footer');
    }

    function getdatatableajax()
    {

        $userHasAuthority = $this->ion_auth->in_group(array('admin', 'owner'));
        $this->load->library('datatables');
        if ($userHasAuthority) {
            $this->datatables
                ->select("id, name, company, phone, email, cf4, cf1, credit_limit")
                ->from("customers")
                ->add_column("Actions",
                    "<center>			<a class=\"tip\" title='" . $this->lang->line("edit_customer") . "' href='index.php?module=customers&amp;view=edit&amp;id=$1'><i class=\"icon-edit\"></i></a>
							    <a class=\"tip\" title='" . $this->lang->line("delete_customer") . "' href='index.php?module=customers&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_customer') . "')\"><i class=\"icon-remove\"></i></a></center>", "id")
                ->unset_column('id');

        } else {
            $this->datatables
                ->select("id, name, company, phone, email, cf4, cf1, cf2")
                ->from("customers")
                ->add_column("Actions",
                    "")
                ->unset_column('id');

        }
        echo $this->datatables->generate();

    }

    function add()
    {
        $groups = array('owner', 'admin', 'salesman');
        if (!$this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }


        //validate form input
        $this->form_validation->set_rules('name', $this->lang->line("name"), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'required|valid_email');
        $this->form_validation->set_rules('company', $this->lang->line("company"), 'required|xss_clean');

        $this->form_validation->set_rules('cf2', $this->lang->line("ccf5"), 'xss_clean');
        $this->form_validation->set_rules('cf6', $this->lang->line("ccf6"), 'xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line("address"), 'required|xss_clean');

        $this->form_validation->set_rules('cf1', 'Discount set is required', 'required|xss_clean');
        $this->form_validation->set_rules('credit_limit', 'Credit Limit is required', 'required|xss_clean');
        $this->form_validation->set_rules('cf5', 'Discount amount is required', 'required|xss_clean');
        $this->form_validation->set_rules('cf3', 'Customer Group is required ', 'required|xss_clean');
        $this->form_validation->set_rules('cf4', 'Customer Group', 'required|xss_clean');

        $this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required|xss_clean|min_length[9]|max_length[16]');

        $getDiscount = $this->customers_model->getDiscountByID($this->input->post('cf5'));


        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'company' => $this->input->post('company'),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $getDiscount->discount,
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'credit_limit' => $this->input->post('credit_limit')
            );
        }

        if ($this->form_validation->run() == true && $this->customers_model->addCustomer($data)) { //check to see if we are creating the customer
            //redirect them back to the admin page
            $this->session->set_flashdata('success_message', $this->lang->line("customer_added"));
            redirect("module=customers", 'refresh');
        } else { //display the create customer form
            //set the flash data error message if there is one
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

            $data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('name'),
            );
            $data['email'] = array('name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'value' => $this->form_validation->set_value('email'),
            );
            $data['company'] = array('name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'value' => $this->form_validation->set_value('company'),
            );
            $data['cui'] = array('name' => 'cui',
                'id' => 'cui',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cui', '-'),
            );
            $data['reg'] = array('name' => 'reg',
                'id' => 'reg',
                'type' => 'text',
                'value' => $this->form_validation->set_value('reg', '-'),
            );
            $data['cnp'] = array('name' => 'cnp',
                'id' => 'cnp',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cnp', '-'),
            );
            $data['serie'] = array('name' => 'serie',
                'id' => 'serie',
                'type' => 'text',
                'value' => $this->form_validation->set_value('serie', '-'),
            );
            $data['account_no'] = array('name' => 'account_no',
                'id' => 'account_no',
                'type' => 'text',
                'value' => $this->form_validation->set_value('account_no', '-'),
            );
            $data['bank'] = array('name' => 'bank',
                'id' => 'bank',
                'type' => 'text',
                'value' => $this->form_validation->set_value('bank', '-'),
            );
            $data['address'] = array('name' => 'address',
                'id' => 'address',
                'type' => 'text',
                'value' => $this->form_validation->set_value('address'),
            );
            $data['cf1'] = array('name' => 'cf1',
                'id' => 'cf1',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cf1'),
            );
            $data['cf2'] = array('name' => 'cf2',
                'id' => 'cf2',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cf2'),
            );
            $data['cf3'] = array('name' => 'cf3',
                'id' => 'cf3',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cf3'),
            );
            $data['cf4'] = array('name' => 'cf3',
                'id' => 'cf3',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cf3'),
            );
            $data['phone'] = array('name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone'),
            );


            $meta['page_title'] = $this->lang->line("new_customer");
            $data['page_title'] = $this->lang->line("new_customer");
            $data['discounts'] = $this->customers_model->getAllDiscounts();
            $this->load->view('commons/header', $meta);
            $this->load->view('add', $data);
            $this->load->view('commons/footer');

        }
    }

    function edit($id = NULL)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $groups = array('owner', 'admin', 'salesman');
        if (!$this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }


        //validate form input
        $this->form_validation->set_rules('name', $this->lang->line("name"), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'required|valid_email');
        $this->form_validation->set_rules('company', $this->lang->line("company"), 'required|xss_clean');
        $this->form_validation->set_rules('cf2', $this->lang->line("ccf5"), 'xss_clean');
        $this->form_validation->set_rules('cf6', $this->lang->line("ccf6"), 'xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line("address"), 'required|xss_clean');

        $this->form_validation->set_rules('cf1', 'Discount set is required', 'required|xss_clean');
        $this->form_validation->set_rules('credit_limit', 'Credit Limit is required', 'required|xss_clean');
        $this->form_validation->set_rules('cf5', 'Discount amount is required', 'required|xss_clean');
        $this->form_validation->set_rules('cf3', 'Customer Group is required ', 'required|xss_clean');
        $this->form_validation->set_rules('cf4', 'Customer Group', 'required|xss_clean');

        $this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required|xss_clean|min_length[9]|max_length[16]');

        $getDiscount = $this->customers_model->getDiscountByID($this->input->post('cf5'));

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'company' => $this->input->post('company'),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $getDiscount->discount,
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'credit_limit' => $this->input->post('credit_limit')
            );
        }

        if ($this->form_validation->run() == true && $this->customers_model->updateCustomer($id, $data)) {
            $this->session->set_flashdata('success_message', $this->lang->line("customer_updated"));
            redirect("module=customers", 'refresh');
        } else {
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

            $data['customer'] = $this->customers_model->getCustomerByID($id);
            $data['discounts'] = $this->customers_model->getAllDiscounts();

            $meta['page_title'] = $this->lang->line("update_customer");
            $data['id'] = $id;
            $data['page_title'] = $this->lang->line("update_customer");
            $this->load->view('commons/header', $meta);
            $this->load->view('edit', $data);
            $this->load->view('commons/footer');

        }
    }


    function add_by_csv()
    {

        $groups = array('owner', 'admin');
        if (!$this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=products', 'refresh');
        }

        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('message', $this->lang->line("disabled_in_demo"));
                redirect('module=home', 'refresh');
            }

            if (isset($_FILES["userfile"])) /*if($_FILES['userfile']['size'] > 0)*/ {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '200';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    redirect("module=suppliers&view=add_by_csv", 'refresh');
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('name', 'email', 'phone', 'company', 'address', 'city', 'state', 'postal_code', 'country');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv) {
                    if ($this->customers_model->getCustomerByEmail($csv['email'])) {
                        $this->session->set_flashdata('message', $this->lang->line("check_customer_email") . " (" . $csv['email'] . "). " . $this->lang->line("customer_already_exist") . " " . $this->lang->line("line_no") . " " . $rw);
                        redirect("module=customers&view=add_by_csv", 'refresh');
                    }
                    $rw++;
                }
            }

            $final = $this->mres($final);
            //$data['final'] = $final;
        }

        if ($this->form_validation->run() == true && $this->customers_model->add_customers($final)) {
            $this->session->set_flashdata('success_message', $this->lang->line("customers_added"));
            redirect('module=customers', 'refresh');
        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

            $data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $meta['page_title'] = $this->lang->line("add_customers_by_csv");
            $data['page_title'] = $this->lang->line("add_customers_by_csv");
            $this->load->view('commons/header', $meta);
            $this->load->view('add_by_csv', $data);
            $this->load->view('commons/footer');

        }

    }

    function delete($id = NULL)
    {
        if (DEMO) {
            $this->session->set_flashdata('message', $this->lang->line("disabled_in_demo"));
            redirect('module=home', 'refresh');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if (!$this->ion_auth->in_group('owner')) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        if ($this->customers_model->deleteCustomer($id)) {
            $this->session->set_flashdata('success_message', $this->lang->line("customer_deleted"));
            redirect("module=customers", 'refresh');
        }

    }

    function mres($q)
    {
        if (is_array($q))
            foreach ($q as $k => $v)
                $q[$k] = $this->mres($v); //recursive
        elseif (is_string($q))
            $q = mysql_real_escape_string($q);
        return $q;
    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);

//        var_dump($term);
        if (strlen($term) < 2) break;
        $rows = $this->customers_model->getCustomerNames($term);

        $json_array = array();
        foreach ($rows as $row)
            $json_array[$row->id] = $row->name;
        //array_push($json_array, $row->name);

//        echo $term;
        echo json_encode($json_array);
    }


}