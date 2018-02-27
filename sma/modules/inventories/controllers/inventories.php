<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventories extends MX_Controller
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
    | MODULE: 			Inventories
    | -----------------------------------------------------
    | This is inventory module controller file.
    | -----------------------------------------------------
    */


    function __construct()
    {
        parent::__construct();

        // check if user logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('module=auth&view=login');
        }
        $this->load->library('form_validation');
        $this->load->model('inventories_model');

    }
    /* -------------------------------------------------------------------------------------------------------------------------------- */
//index or inventories page

    function index()
    {
//	   echo 'hello world';

        if ($this->ion_auth->in_group('viewer')) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        if ($this->input->get('search_term')) {
            $data['search_term'] = $this->input->get('search_term');
        } else {
            $data['search_term'] = false;
        }

        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        $data['success_message'] = $this->session->flashdata('success_message');

        $data['warehouses'] = $this->inventories_model->getAllWarehouses();
        $meta['page_title'] = 'Purchases Requisition';
        $data['page_title'] = 'Purchases Requisition';
        $this->load->view('commons/header', $meta);
        $this->load->view('content', $data);
        $this->load->view('commons/footer');
    }


    function po_content()
    {
//	   echo 'hello world';

        if ($this->ion_auth->in_group('salesman', 'viewer')) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        if ($this->input->get('search_term')) {
            $data['search_term'] = $this->input->get('search_term');
        } else {
            $data['search_term'] = false;
        }

        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        $data['success_message'] = $this->session->flashdata('success_message');

        $data['warehouses'] = $this->inventories_model->getAllWarehouses();
        $meta['page_title'] = $this->lang->line("purchase_orders");
        $data['page_title'] = $this->lang->line("purchase_orders");
        $this->load->view('commons/header', $meta);
        $this->load->view('po_content', $data);
        $this->load->view('commons/footer');
    }


    function mrr_list()
    {
        if ($this->ion_auth->in_group('viewer')) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        if ($this->input->get('search_term')) {
            $data['search_term'] = $this->input->get('search_term');
        } else {
            $data['search_term'] = false;
        }

        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        $data['success_message'] = $this->session->flashdata('success_message');

        $data['warehouses'] = $this->inventories_model->getAllWarehouses();
        $meta['page_title'] = $this->lang->line("purchase_orders");
        $data['page_title'] = $this->lang->line("purchase_orders");
        $this->load->view('commons/header', $meta);
        $this->load->view('mrr_list', $data);
        $this->load->view('commons/footer');
    }

    function getdatatableajax()
    {

        if ($this->input->get('search_term')) {
            $search_term = $this->input->get('search_term');
        } else {
            $search_term = false;
        }


        $userHasAuthority = $this->ion_auth->in_group(array('owner', 'checker'));
        $userDeleteHasAuthority = $this->ion_auth->in_group(array('salesman'));
        $this->load->library('datatables');


        $this->datatables
            ->select("purchases.id as id, purchases.date, purchases.reference_no, purchases.supplier_name, COALESCE(purchases.inv_total, 0), COALESCE(purchases.total_tax, 0), purchases.total,  CASE WHEN purchases.checked = 1 THEN 'PO Done' else '' END AS approved", FALSE)
            ->from('purchases')
            ->join("make_purchases", 'purchases.id = make_purchases.purchase_id', 'left')
            ->where('make_purchases.checked IS NULL', NULL);
//
//        $this->datatables->add_column("Actions",
//            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory_po&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_inventory") . "' class='tip'><i class='icon-fullscreen'></i></a>&nbsp;<a href='index.php?module=inventories&amp;view=edit&amp;id=$1' title='Process' class='tip'><i class='icon-list'></i></a>
//			 &nbsp;<a href='index.php?module=inventories&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_inventory') . "')\" title='" . $this->lang->line("delete_inventory") . "' class='tip'><i class='icon-trash'></i></a>&nbsp; </center>", "id")
//            ->unset_column('id');


        if ($userHasAuthority) {
            $this->datatables->add_column("Actions",
                "<center><center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory_pr&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='view PR' class='tip'><i class='icon-fullscreen'></i></a><a href='index.php?module=inventories&amp;view=edit_requisition&amp;id=$1' title='Process' class='tip'><i class='icon-list'></i></a>
			 &nbsp;<a href='index.php?module=inventories&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_inventory') . "')\" title='" . $this->lang->line("delete_inventory") . "' class='tip'><i class='icon-trash'></i></a>&nbsp; </center>", "id")
                ->unset_column('id');
        } elseif($userDeleteHasAuthority) {
            $this->datatables->add_column("Actions",
                "<center><center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory_pr&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='view PR' class='tip'><i class='icon-fullscreen'></i></a><a href='index.php?module=inventories&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_inventory') . "')\" title='" . $this->lang->line("delete_inventory") . "' class='tip'><i class='icon-trash'></i></a>&nbsp; </center>", "id")
                ->unset_column('id');
        }
        else{
            $this->datatables->add_column("Actions", "")->unset_column('id');
        }

        echo $this->datatables->generate();

    }


    function getpodatatableajax()
    {

        if ($this->input->get('search_term')) {
            $search_term = $this->input->get('search_term');
        } else {
            $search_term = false;
        }
        $this->load->library('datatables');

        $this->datatables
            ->select("make_purchases.id as id, date, reference_no, supplier_name, total,CASE WHEN approved = '1' THEN 'Approved' WHEN verify_status = '1' THEN 'Verified'  WHEN checked = '1' THEN 'Checked' END AS approved,CASE WHEN mr_status = '1' THEN 'Done' END AS mrrApproved", FALSE)
            ->from('make_purchases')
            ->where("checked", 1);

//        $this->datatables->add_column("Actions",
//            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory_po&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_workOrder") . "' class='tip'><i class='icon-fullscreen'></i></a>&nbsp; <a href='index.php?module=inventories&view=pdf_purchase&id=$1' title='Work Order' class='tip'><i class='icon-download'></i></a>
//			  &nbsp;<a href='index.php?module=inventories&amp;view=make_mrr&amp;id=$1' title='Make MRR' class='tip'><i class='icon-adjust'></i></a>&nbsp;<a href='index.php?module=inventories&amp;view=edit&amp;id=$1' title='" . $this->lang->line("edit_order") . "' class='tip'><i class='icon-edit'></i></a> <a href='index.php?module=inventories&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_inventory') . "')\" title='" . $this->lang->line("delete_inventory") . "' class='tip'><i class='icon-trash'></i></a>&nbsp; <input type='checkbox' name='chk[]' value='$1' /> </center>", "id")
//            ->unset_column('id');


        $this->datatables->add_column("Actions",
            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory_po&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_workOrder") . "' class='tip'><i class='icon-fullscreen'></i></a>&nbsp; <a href='index.php?module=inventories&view=pdf_purchase&id=$1' title='Work Order' class='tip'><i class='icon-download'></i></a>
			  &nbsp;<a href='index.php?module=inventories&amp;view=make_mrr&amp;id=$1' title='Make MRR' class='tip'><i class='icon-adjust'></i></a>&nbsp;<a href='index.php?module=inventories&amp;view=edit&amp;id=$1' title='" . $this->lang->line("edit_order") . "' class='tip'><i class='icon-edit'></i></a> &nbsp; <input type='checkbox' name='chk[]' value='$1' /> </center>", "id")
            ->unset_column('id');


        echo $this->datatables->generate();

    }

    function getmrrdatatableajax()
    {

        if ($this->input->get('search_term')) {
            $search_term = $this->input->get('search_term');
        } else {
            $search_term = false;
        }
        $this->load->library('datatables');
        $this->datatables
            ->select("make_purchases.id as id, mr_date, reference_no, supplier_name, sum(COALESCE(make_mrr.inv_val, 0)), COALESCE(total_tax, 0), sum(COALESCE(make_mrr.inv_val, 0)) as total, case mr_status when '1' Then 'Approved' END approved", FALSE)
            ->from('make_purchases')
            ->join("make_mrr", 'make_purchases.id = make_mrr.make_purchase_id', 'left')
            ->group_by("make_purchases.id")
            ->where("make_purchases.mr_status", "1");
        $this->datatables->add_column("Actions",
            // omit mrr approve
//            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_workOrder") . "' class='tip'><i class='icon-fullscreen'></i></a> <a href='index.php?module=inventories&amp;view=make_mrr&amp;id=$1' title='Process' class='tip'><i class='icon-ban-circle'></i></a>&nbsp;<a href='index.php?module=inventories&view=pdf_mrr&id=$1' title='MRR Order' class='tip'><i class='icon-download'></i></a></center>", "id")
            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_workOrder") . "' class='tip'><i class='icon-fullscreen'></i></a>&nbsp;<a href='index.php?module=inventories&view=pdf_mrr&id=$1' title='MRR Order' class='tip'><i class='icon-download'></i></a></center>", "id")
            ->unset_column('id')->unset_column('checked');


        echo $this->datatables->generate();

    }


    function verify_check()
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner', 'checker'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $purchase_id = $this->input->get('id');
        $this->inventories_model->updateCheck($purchase_id);
        $this->session->set_flashdata('success_message', 'Order Checked Successful');
        redirect("module=inventories&view=edit&id=" . $purchase_id, 'refresh');
    }

    function verify_approve()
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner', 'approver'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $this->form_validation->set_rules('chk', $this->lang->line("po_selection"), 'required');

        if ($this->form_validation->run() == true) {
            $not_verify = [];
            $All_ready_approved = [];
            $purchase_id = $this->input->post('chk');


//             verify and approve
//            foreach ($purchase_id as $idvalue) {
//                $p_data = $this->inventories_model->getPurchaseId($idvalue);
//                if ($p_data->approved == 1) {
//                    $All_ready_approved[] = $p_data->reference_no;
//                } else {
//                    if ($p_data->verify_status == 1) {
//
//                        $this->inventories_model->updateApprovePO($idvalue);
//
//                    } else {
//
//                        $not_verify[] = $p_data->reference_no;
//                    }
//
//                }
//            }


            foreach ($purchase_id as $idvalue) {
                $p_data = $this->inventories_model->getPurchaseId($idvalue);
                if ($p_data->approved == 1) {
                    $All_ready_approved[] = $p_data->reference_no;
                } else {
//                    if ($p_data->verify_status == 1) {

                        $this->inventories_model->updateApprovePO($idvalue);

//                    } else {
//
//                        $not_verify[] = $p_data->reference_no;
//                    }

                }
            }

            if (count($All_ready_approved) > 0) {
                $ready_approved = implode(',', $All_ready_approved);
                $this->session->set_flashdata('message', "Following PO already are approved." . $ready_approved);
                $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
                redirect('module=inventories&view=po_content', 'refresh');
            }


            if (count($not_verify) > 0) {
                $not_v_req = implode(',', $not_verify);
                $this->session->set_flashdata('message', "Following Requisition are not verify yet." . $not_v_req);
                $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
                redirect('module=inventories&view=po_content', 'refresh');
            }


            $this->session->set_flashdata('success_message', 'Order Approved Successful');
            redirect("module=inventories&view=po_content", 'refresh');
        } else {
            $this->session->set_flashdata('message', "Please select require PO");
            redirect("module=inventories&view=po_content", 'refresh');
        }
    }


    function verify_verify_new()
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner', 'verify'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }
        $not_verify = [];
        $All_ready_approved = [];
        $purchase_id = $this->input->post('chk');

        $this->form_validation->set_rules('chk', $this->lang->line("po_selection"), 'required');

        if ($this->form_validation->run() == true) {
            foreach ($purchase_id as $idvalue) {
                $p_data = $this->inventories_model->getPurchaseId($idvalue);

                if ($p_data->approved == 1) {
                    $All_ready_approved[] = $p_data->reference_no;
                } else {
                    if ($p_data->checked == 1) {

                        $this->inventories_model->updateVerifyPO($idvalue);

                    } else {

                        $not_verify[] = $p_data->reference_no;
                    }

                }
            }


            if (count($All_ready_approved) > 0) {
                $ready_approved = implode(',', $All_ready_approved);
                $this->session->set_flashdata('message', "Following PO already are approved." . $ready_approved);
                $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
                redirect("module=inventories&view=po_content", 'refresh');
            }


            if (count($not_verify) > 0) {
                $not_v_req = implode(',', $not_verify);
                $this->session->set_flashdata('message', "Following Requisition are not Checked yet." . $not_v_req);
                $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
                redirect("module=inventories&view=po_content", 'refresh');
            }


            $this->session->set_flashdata('success_message', 'Order Verify Successful');
            redirect("module=inventories&view=po_content", 'refresh');
        } else {
            $this->session->set_flashdata('message', "Please select require PO");
            redirect("module=inventories&view=po_content", 'refresh');
        }
    }


    function mrr_verify_approve()
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner', 'approver'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $purchase_id = $this->input->get('id');
        $this->inventories_model->updateMrrApprove($purchase_id);
        $this->session->set_flashdata('success_message', 'MRR Approved Successfully');
        redirect("module=inventories&view=mrr_list&id=" . $purchase_id, 'refresh');
    }


    function verify_verify($inv_id = '')
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner', 'verify'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $purchase_id = $this->input->get('id') ? $this->input->get('id') : $inv_id;
        $this->inventories_model->updateVerify($purchase_id);

        $this->session->set_flashdata('success_message', 'Order Verify Successful');
        redirect("module=inventories&view=po_content");
    }

    function cancel_requisition()
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $purchase_id = $this->input->get('id');
        $this->inventories_model->cancelRequisition($purchase_id);
        $this->session->set_flashdata('success_message', 'Order Cancel Successfully');
        redirect("module=inventories&view=edit&id=" . $purchase_id, 'refresh');
    }


    function cancel_mrr()
    {
        if (!$this->ion_auth->in_group(array('admin', 'owner'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $purchase_id = $this->input->get('id');
        $this->inventories_model->cancelMrr($purchase_id);
        $this->session->set_flashdata('success_message', 'MRR Cancel Successfully');
        redirect("module=inventories&view=mrr_list&id=" . $purchase_id, 'refresh');
    }

    function warehouse($warehouse = DEFAULT_WAREHOUSE)
    {
        if ($this->input->get('warehouse_id')) {
            $warehouse = $this->input->get('warehouse_id');
        }

        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        $data['warehouses'] = $this->inventories_model->getAllWarehouses();
        $data['warehouse_id'] = $warehouse;

        $meta['page_title'] = $this->lang->line("purchase_orders");
        $data['page_title'] = $this->lang->line("purchase_orders");
        $this->load->view('commons/header', $meta);
        $this->load->view('warehouse', $data);
        $this->load->view('commons/footer');
    }

    function getwhinv($warehouse_id = NULL)
    {
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id');
        }

        $this->load->library('datatables');
        $this->datatables
            ->select("id, date, reference_no, supplier_name, total")
            ->from('purchases')
            ->where('warehouse_id', $warehouse_id)
            ->add_column("Actions",
                "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_workOrder") . "' class='tip'><i class='icon-fullscreen'></i></a> <a href='index.php?module=inventories&view=pdf&id=$1' title='" . $this->lang->line("download_pdf") . "' class='tip'><i class='icon-file'></i></a> <a href='index.php?module=inventories&view=pdf_purchase&id=$1' title='Purchase Order' class='tip'><i class='icon-file'></i></a> <a href='index.php?module=inventories&view=email_inventory&id=$1' title='" . $this->lang->line("email_inventory") . "' class='tip'><i class='icon-envelope'></i></a> <a href='index.php?module=inventories&amp;view=edit&amp;id=$1' title='" . $this->lang->line("edit_inventory") . "' class='tip'><i class='icon-edit'></i></a> <a href='index.php?module=inventories&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_inventory') . "')\" title='" . $this->lang->line("delete_inventory") . "' class='tip'><i class='icon-trash'></i></a></center>", "id")
            ->unset_column('id');


        echo $this->datatables->generate();

    }
    /* -------------------------------------------------------------------------------------------------------------------------------- */
//view MRR as html page

    function view_inventory($purchase_id = NULL)
    {
        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));


        $inv = $this->inventories_model->getInventoryFromPOByPurchaseID($purchase_id);
        $data['rows'] = $this->inventories_model->getMakeMrrInfo($purchase_id);
        $supplier_id = $data['rows'][0]->supplier_id;
        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);

        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = $this->lang->line("inventory");
        $this->load->view('view_inventory', $data);

    }

    function view_inventory_po($purchase_id = NULL)
    {
        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

        $inv = $this->inventories_model->getInventoryFromPOByPurchaseID($purchase_id);
        $data['rows'] = $this->inventories_model->getAllpoInventoryItems($purchase_id);
        $supplier_id = $data['rows'][0]->supplier_id;
        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);


        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = $this->lang->line("inventory");

        $this->load->view('view_po', $data);

    }


    function view_inventory_pr($purchase_id = NULL)
    {
        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

        $inv = $this->inventories_model->getInventoryByPurchaseID($purchase_id);
        $data['rows'] = $this->inventories_model->getAllRequisitionInventoryItems($purchase_id);
//        $supplier_id = $data['rows'][0]->supplier_id;
//        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);


        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = 'Requisition Details';

        $this->load->view('view_pr', $data);

    }


    /* -------------------------------------------------------------------------------------------------------------------------------- */
//generate pdf and force to download

    function pdf()
    {
        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

        $data['rows'] = $this->inventories_model->getAllInventoryItems($purchase_id);

        $inv = $this->inventories_model->getInventoryByPurchaseID($purchase_id);
        $supplier_id = $inv->supplier_id;
        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);

        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = $this->lang->line("inventory");


//        $html = $this->load->view('view_inventory', $data, TRUE);


//        $this->load->library('MPDF/mpdf');
//
//        $mpdf = new mPDF('utf-8', 'A4', '12', '', 10, 10, 10, 10, 9, 9);
//        $mpdf->useOnlyCoreFonts = true;
//        $mpdf->SetProtection(array('print'));
//        $mpdf->SetTitle(SITE_NAME);
//        $mpdf->SetAuthor(SITE_NAME);
//        $mpdf->SetCreator(SITE_NAME);
//        $mpdf->SetDisplayMode('fullpage');
//        $mpdf->SetAutoFont();
//        $stylesheet = file_get_contents('assets/css/bootstrap-' . THEME . '.css');
//        $mpdf->WriteHTML($stylesheet, 1);
//
//        $search = array("<div class=\"row-fluid\">", "<div class=\"span6\">");
//        $replace = array("<div style='width: 100%;'>", "<div style='width: 48%; float: left;'>");
//        $html = str_replace($search, $replace, $html);
//
//
//        $name = $this->lang->line("inventory") . "-" . $inv->id . ".pdf";
//
//        $mpdf->WriteHTML($html);
//
//        $mpdf->Output($name, 'D');

        exit;

    }


    //generate pdf and force to download

    function pdf_purchase()
    {

        if (!$this->ion_auth->in_group(array('admin', 'owner', 'approver', 'checker'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=inventories&view=po_content', 'refresh');
        }

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

        $data['rows'] = $this->inventories_model->getAllpoInventoryItems($purchase_id);

        // pull from purchase table
        $inv = $this->inventories_model->getWorkorderByPurchaseID($purchase_id);
        $supplier_id = $inv->supplier_id;
        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);

        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = $this->lang->line("inventory");
        if (!$inv->approved) {
            $this->session->set_flashdata('message', "This Purchese Order Not Approve Yet");
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=inventories&view=po_content', 'refresh');
        }

        $html = $this->load->view('view_purchase', $data, TRUE);


        $this->load->library('MPDF/mpdf');

        $mpdf = new mPDF('utf-8', 'A4', '12', '', 10, 10, 10, 10, 9, 9);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(SITE_NAME);
        $mpdf->SetAuthor(SITE_NAME);
        $mpdf->SetCreator(SITE_NAME);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetAutoFont();
        $stylesheet = file_get_contents('assets/css/bootstrap-' . THEME . '.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $search = array("<div class=\"row-fluid\">", "<div class=\"span6\">");
        $replace = array("<div style='width: 100%;'>", "<div style='width: 48%; float: left;'>");
        $html = str_replace($search, $replace, $html);


        $name = $this->lang->line("inventory") . "-" . $inv->id . ".pdf";

        $mpdf->WriteHTML($html);

        $mpdf->Output($name, 'D');

        exit;

    }


    function pdf_mrr()
    {

        if (!$this->ion_auth->in_group(array('admin', 'owner', 'checker','salesman'))) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=inventories&view=mrr_list', 'refresh');
        }

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));


        $inv = $this->inventories_model->getInventoryFromPOByPurchaseID($purchase_id);
        $data['rows'] = $this->inventories_model->getMakeMrrInfo($purchase_id);
        $supplier_id = $data['rows'][0]->supplier_id;
        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);

//        $data['rows'] = $this->inventories_model->getAllInventoryItems($purchase_id);
//
//        $inv = $this->inventories_model->getWorkorderByPurchaseID($purchase_id);
//        $supplier_id = $inv->supplier_id;
//        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);

        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = $this->lang->line("inventory");
//      /  if ($inv->mr_status != 2) {
//            $this->session->set_flashdata('message', "This MRR Order Not Approve Yet");
//            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
//            redirect('module=inventories', 'refresh');
//        }

        $html = $this->load->view('view_mrr', $data, TRUE);


        $this->load->library('MPDF/mpdf');

        $mpdf = new mPDF('utf-8', 'A4', '12', '', 10, 10, 10, 10, 9, 9);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(SITE_NAME);
        $mpdf->SetAuthor(SITE_NAME);
        $mpdf->SetCreator(SITE_NAME);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetAutoFont();
        $stylesheet = file_get_contents('assets/css/bootstrap-' . THEME . '.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $search = array("<div class=\"row-fluid\">", "<div class=\"span6\">");
        $replace = array("<div style='width: 100%;'>", "<div style='width: 48%; float: left;'>");
        $html = str_replace($search, $replace, $html);


        $name = $this->lang->line("inventory") . "-" . $inv->id . ".pdf";

        $mpdf->WriteHTML($html);

        $mpdf->Output($name, 'D');

        exit;

    }



    /* -------------------------------------------------------------------------------------------------------------------------------- */
//email inventory as html and send pdf as attachment

    function email($purchase_id, $to, $cc = NULL, $bcc = NULL, $from_name, $from, $subject, $note)
    {

        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        $data['rows'] = $this->inventories_model->getAllInventoryItems($purchase_id);
        $inv = $this->inventories_model->getInventoryByPurchaseID($purchase_id);
        $supplier_id = $inv->supplier_id;
        $data['supplier'] = $this->inventories_model->getSupplierByID($supplier_id);
        $data['inv'] = $inv;
        $data['pid'] = $purchase_id;
        $data['page_title'] = $this->lang->line("inventory");

        $html = $this->load->view('view_inventory', $data, TRUE);

        $this->load->library('MPDF/mpdf');

        $mpdf = new mPDF('utf-8', 'A4', '12', '', 10, 10, 10, 10, 9, 9);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(SITE_NAME);
        $mpdf->SetAuthor(SITE_NAME);
        $mpdf->SetCreator(SITE_NAME);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetAutoFont();
        $stylesheet = file_get_contents('assets/css/bootstrap-' . THEME . '.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $search = array("<div class=\"row-fluid\">", "<div class=\"span6\">");
        $replace = array("<div style='width: 100%;'>", "<div style='width: 48%; float: left;'>");
        $html = str_replace($search, $replace, $html);


        $name = $this->lang->line("inventory") . "-" . $inv->id . ".pdf";

        $mpdf->WriteHTML($html);

        $mpdf->Output($name, 'F');

        if ($note) {
            $message = html_entity_decode($note) . "<br><hr>" . $html;
        } else {
            $message = $html;
        }

        $this->load->library('email');

        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);

        $this->email->from($from, $from_name);
        $this->email->to($to);
        if ($cc) {
            $this->email->cc($cc);
        }
        if ($bcc) {
            $this->email->bcc($bcc);
        }

        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->attach($name);

        if ($this->email->send()) {
            // email sent
            unlink($name);
            return true;
        } else {
            //email not sent
            unlink($name);
            //echo $this->email->print_debugger();
            return false;
        }


    }


    function email_inventory($id = NULL)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $groups = array('owner', 'admin');
        if (!$this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        //validate form input
        $this->form_validation->set_rules('to', $this->lang->line("to") . " " . $this->lang->line("email"), 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('subject', $this->lang->line("subject"), 'trim|required|xss_clean');
        $this->form_validation->set_rules('cc', $this->lang->line("cc"), 'trim|xss_clean');
        $this->form_validation->set_rules('bcc', $this->lang->line("bcc"), 'trim|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line("message"), 'trim|xss_clean');

        if ($this->form_validation->run() == true) {
            $to = $this->input->post('to');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = NULL;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = NULL;
            }
            $message = $this->ion_auth->clear_tags($this->input->post('note'));
            $user = $this->ion_auth->user()->row();
            $from_name = $user->first_name . " " . $user->last_name;
            $from = $user->email;
        }

        if ($this->form_validation->run() == true && $this->email($id, $to, $cc, $bcc, $from_name, $from, $subject, $message)) {

            $this->session->set_flashdata('success_message', $this->lang->line("sent"));
            redirect("module=inventories", 'refresh');


        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

            $data['to'] = array('name' => 'to',
                'id' => 'to',
                'type' => 'text',
                'value' => $this->form_validation->set_value('to'),
            );
            $data['subject'] = array('name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject'),
            );
            $data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note'),
            );


            $user = $this->ion_auth->user()->row();
            $data['from_name'] = $user->first_name . " " . $user->last_name;
            $data['from_email'] = $user->email;

            $data['id'] = $id;
            $meta['page_title'] = $this->lang->line("email_inventory");
            $data['page_title'] = $this->lang->line("email_inventory");
            $this->load->view('commons/header', $meta);
            $this->load->view('email', $data);
            $this->load->view('commons/footer');

        }
    }


    /* -------------------------------------------------------------------------------------------------------------------------------- */
//Add new inventory

    function add($alert = NULL)
    {

        //print_r($this->input->post);


        $groups = array('viewer');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        //validate form input
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required|xss_clean');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        $quantity = "quantity";
        $product = "product";
        $unit_cost = "unit_cost";
        $tax_rate = "tax_rate";

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no');
            $date = $this->ion_auth->fsd(trim($this->input->post('date')));
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $supplier_details = $this->inventories_model->getSupplierByID($supplier_id);
            $supplier_name = $supplier_details->name;
            $note = $this->ion_auth->clear_tags($this->input->post('note'));
            $inv_total = 0;
            $inv_total_no_tax = 0;

            for ($i = 1; $i <= 500; $i++) {
                if ($this->input->post($quantity . $i) && $this->input->post($product . $i) && $this->input->post($unit_cost . $i)) {

                    if (TAX1) {
                        $tax_id = $this->input->post($tax_rate . $i);
                        $tax_details = $this->inventories_model->getTaxRateByID($tax_id);
                        $taxRate = $tax_details->rate;
                        $taxType = $tax_details->type;
                        $tax_rate_id[] = $tax_id;

                        if ($taxType == 1 && $taxRate != 0) {
                            $item_tax = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)) * $taxRate / 100);
                            $val_tax[] = $item_tax;
                        } else {
                            $item_tax = $taxRate;
                            $val_tax[] = $item_tax;
                        }

                        if ($taxType == 1) {
                            $tax[] = $taxRate . "%";
                        } else {
                            $tax[] = $taxRate;
                        }
                    } else {
                        $item_tax = 0;
                        $tax_rate_id[] = 0;
                        $val_tax[] = 0;
                        $tax[] = "";
                    }

                    $product_details = $this->inventories_model->getProductByCode($this->input->post($product . $i));
                    $product_id[] = $product_details->id;
                    $product_name[] = $product_details->name;
                    $product_code[] = $product_details->code;

                    $inv_quantity[] = $this->input->post($quantity . $i);
                    //$inv_product_code[] = $this->input->post($product.$i);
                    $inv_unit_cost[] = $this->input->post($unit_cost . $i);

                    $inv_gross_total[] = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));
                    $inv_total_no_tax += (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));


                }
            }

            if (TAX1) {
                $total_tax = array_sum($val_tax);
            } else {
                $total_tax = 0;
            }

            /*	foreach($inv_product_code as $pr_code){
                    $product_details = $this->inventories_model->getProductByCode($pr_code);
                    $product_id[] = $product_details->id;
                    $product_name[] = $product_details->name;
                    $product_code[] = $product_details->code;
                } */

            $keys = array("product_id", "product_code", "product_name", "tax_rate_id", "tax", "quantity", "unit_price", "gross_total", "val_tax");

            $items = array();
            foreach (array_map(null, $product_id, $product_code, $product_name, $tax_rate_id, $tax, $inv_quantity, $inv_unit_cost, $inv_gross_total, $val_tax) as $key => $value) {
                $items[] = array_combine($keys, $value);
            }

            /*	$keys = array("product_id","product_code","product_name","quantity","unit_price", "gross_total");

                    $items = array();
                foreach ( array_map(null, $product_id, $product_code, $product_name, $inv_quantity, $inv_unit_cost, $inv_gross_total) as $key => $value ) {
                    $items[] = array_combine($keys, $value);
                } */

            $inv_total = $inv_total_no_tax + $total_tax;

            $invDetails = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier_name' => $supplier_name,
                'note' => $note,
                'inv_total' => $inv_total_no_tax,
                'total_tax' => $total_tax,
                'total' => $inv_total
            );

            /*print_r($invDetails);
            echo "<hr>";
            print_r($items);
            die();*/
        }


        if ($this->form_validation->run() == true && $this->inventories_model->addPurchase($invDetails, $items, $warehouse_id)) {

            $this->session->set_flashdata('success_message', "Purchase requisition successfully created");
            redirect("module=inventories", 'refresh');

        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

            $data['reference_no'] = array('name' => 'reference_no',
                'id' => 'reference_no',
                'type' => 'text',
                'value' => $this->form_validation->set_value('reference_no'),
            );
            $data['date'] = array('name' => 'date',
                'id' => 'date',
                'type' => 'text',
                'value' => $this->form_validation->set_value('date'),
            );
            $data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'textarea',
                'value' => $this->form_validation->set_value('note'),
            );

            if ($this->input->get('alert')) {
                $alert = $this->input->get('alert');
            }

            if (isset($alert) && $alert != '') {

                $data['inv_products'] = $this->inventories_model->getAllInventoryIAlerttems();

            }


            $data['suppliers'] = $this->inventories_model->getAllSuppliers();
            $data['tax_rates'] = $this->inventories_model->getAllTaxRates();
            $data['warehouses'] = $this->inventories_model->getAllWarehouses();
            $data['rnumber'] = $this->inventories_model->getRQNextAI();
            $meta['page_title'] = $this->lang->line("add_purchase");
            $data['page_title'] = $this->lang->line("add_purchase");
            $this->load->view('commons/header', $meta);
            $this->load->view('add', $data);
            $this->load->view('commons/footer');

        }
    }


    //ADD Count Quantity

    //Add new inventory

    function add_quantity()
    {


        $groups = array('salesman');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        //validate form input

        $this->form_validation->set_rules('product_name', "Product Name Required", 'required|xss_clean');

//        $this->form_validation->set_rules('um', "UM Required", 'required|xss_clean');
        $this->form_validation->set_rules('warehouse', "Warehouse Required", 'required|xss_clean');

        $this->form_validation->set_rules('count_quantity', "Count Quantity Required", 'required|is_natural_no_zero|xss_clean');

        $this->form_validation->set_rules('product_code', "Product Code Required", 'required|xss_clean');


        if ($this->form_validation->run() == true) {
            $warehouse_id = $this->input->post('warehouse');
            $quantity = $this->input->post('count_quantity');
            $product_code = $this->input->post('product_code');

        }


        if (($this->form_validation->run()) == true) {
            if ($this->inventories_model->addCountQuantity($product_code, $quantity, $warehouse_id)) {
                $this->session->set_flashdata('success_message', $this->lang->line("item_count_added"));
                $data['success_message'] = $this->lang->line("item_count_added");
                $data['message'] = validation_errors();
            }
        } else {
            $data['message'] = validation_errors();
        }
//        $data['suppliers'] = $this->inventories_model->getAllSuppliers();
//        $data['tax_rates'] = $this->inventories_model->getAllTaxRates();
        $data['warehouses'] = $this->inventories_model->getAllWarehouses();
//        $data['rnumber'] = $this->inventories_model->getRQNextAI();
        $meta['page_title'] = "Count Variance Quantity";
        $data['page_title'] = "Count Variance Quantity";
        $this->load->view('commons/header', $meta);
        $this->load->view('count_quantity', $data);
        $this->load->view('commons/footer');

    }
    /* -------------------------------------------------------------------------------------------------------------------------------- */
//Edit inventory

    function edit($id = NULL)
    {

        // console . log($id);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $groups = array('viewer');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $getPurchaseId = $this->inventories_model->getPurchaseId($id);
        $All_ready_approved["id"] = null;
        if ($getPurchaseId->approved === "1") $All_ready_approved["id"] = $getPurchaseId->reference_no;
        if ($All_ready_approved["id"] != "" && $All_ready_approved["id"] != null) {
            $ready_approved = implode(',', $All_ready_approved);
            $this->session->set_flashdata('message', "Following PO already are approved." . $ready_approved);
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=inventories&view=po_content', 'refresh');
        }


//
//     //   validate form input
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required|xss_clean');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        $quantity = "quantity";
        $product = "product";
        $unit_cost = "unit_cost";
        $tax_rate = "tax_rate";
        $supplier_item = "supplier_item";
        $item_id = "item_id";
        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no');
            $date = $this->ion_auth->fsd(trim($this->input->post('date')));
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $supplier_details = $this->inventories_model->getSupplierByID($supplier_id);
            $supplier_name = $supplier_details->name;
            $note = $this->ion_auth->clear_tags($this->input->post('note'));
            $inv_total = 0;
            $inv_total_no_tax = 0;

            for ($i = 1; $i <= 500; $i++) {
                if ($this->input->post($quantity . $i) && $this->input->post($product . $i) && $this->input->post($unit_cost . $i)) {

                    if (TAX1) {
                        $tax_id = $this->input->post($tax_rate . $i);
                        $tax_details = $this->inventories_model->getTaxRateByID($tax_id);
                        $taxRate = $tax_details->rate;
                        $taxType = $tax_details->type;
                        $tax_rate_id[] = $tax_id;

                        if ($taxType == 1 && $taxRate != 0) {
                            $item_tax = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)) * $taxRate / 100);
                            $val_tax[] = $item_tax;
                        } else {
                            $item_tax = $taxRate;
                            $val_tax[] = $item_tax;
                        }

                        if ($taxType == 1) {
                            $tax[] = $taxRate . "%";
                        } else {
                            $tax[] = $taxRate;
                        }
                    } else {
                        $item_tax = 0;
                        $tax_rate_id[] = 0;
                        $val_tax[] = 0;
                        $tax[] = "";
                    }

                    $product_details = $this->inventories_model->getProductByCode($this->input->post($product . $i));
                    $product_id[] = $product_details->id;
                    $product_name[] = $product_details->name;
                    $product_code[] = $product_details->code;

                    $inv_quantity[] = $this->input->post($quantity . $i);
                    $p_item_id[] = $this->input->post($item_id . $i);
                    //$inv_product_code[] = $this->input->post($product.$i);
                    $inv_unit_cost[] = $this->input->post($unit_cost . $i);
                    $inv_supplier_item[] = $this->input->post($supplier_item . $i);
                    $inv_gross_total[] = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));
                    $inv_total_no_tax += (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));



                }
            }

            if (TAX1) {
                $total_tax = array_sum($val_tax);
            } else {
                $total_tax = 0;
            }

            $keys = array("product_id", "product_code", "product_name", "tax_rate_id", "tax", "quantity", "unit_price", "gross_total", "val_tax", "supplier_id", "p_item_id");

            $items = array();
            foreach (array_map(null, $product_id, $product_code, $product_name, $tax_rate_id, $tax, $inv_quantity, $inv_unit_cost, $inv_gross_total, $val_tax, $inv_supplier_item, $p_item_id) as $key => $value) {
                $items[] = array_combine($keys, $value);
            }


            $inv_total = $inv_total_no_tax + $total_tax;

            $invDetails = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier_name' => $supplier_name,
                'note' => $note,
                'inv_total' => $inv_total_no_tax,
                'total_tax' => $total_tax,
                'total' => $inv_total
            );

        }

        $checkedBy = $this->inventories_model->checkedByStatus($id);

        if ($this->form_validation->run() == true && $checkedBy->checked == 1 && $this->inventories_model->updatePurchaseOrder($id, $invDetails, $items, $warehouse_id, $checkedBy->purchase_id)) {

            $this->session->set_flashdata('success_message', $this->lang->line("purchase_updated"));
            redirect("module=inventories&view=po_content", 'refresh');

        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            $data['success_message'] = $this->session->flashdata('success_message');

            $data['suppliers'] = $this->inventories_model->getAllSuppliers();
            $data['products'] = $this->inventories_model->getAllProducts();


            $data['tax_rates'] = $this->inventories_model->getAllTaxRates();
            $data['warehouses'] = $this->inventories_model->getAllWarehouses();


            $poInfo = $this->inventories_model->getPoInventoryByID($id);


            $data['inv'] = $poInfo;
            $data['inv_products'] = $this->inventories_model->getAllpoInventoryItems($id);


            $data['id'] = $id;
            $meta['page_title'] = $this->lang->line("update_purchase");
            $data['page_title'] = $this->lang->line("update_purchase");
            $this->load->view('commons/header', $meta);
            $this->load->view('edit', $data);
            $this->load->view('commons/footer');

        }
    }


    function edit_requisition($id = NULL)
    {

        // console . log($id);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $groups = array('viewer');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $getPurchaseId = $this->inventories_model->getPurchasesById($id);
        $All_ready_approved["id"] = null;
        if ($getPurchaseId->approved === "1") $All_ready_approved["id"] = $getPurchaseId->reference_no;
        if ($All_ready_approved["id"] != "" && $All_ready_approved["id"] != null) {
            $ready_approved = implode(',', $All_ready_approved);
            $this->session->set_flashdata('message', "Following PO already are approved." . $ready_approved);
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=inventories&view=po_content', 'refresh');
        }


//
//     //   validate form input
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required|xss_clean');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        $quantity = "quantity";
        $product = "product";
        $unit_cost = "unit_cost";
        $tax_rate = "tax_rate";
        $supplier_item = "supplier_item";
        $item_id = "item_id";
        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no');
            $date = $this->ion_auth->fsd(trim($this->input->post('date')));
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $supplier_details = $this->inventories_model->getSupplierByID($supplier_id);
            $supplier_name = $supplier_details->name;
            $note = $this->ion_auth->clear_tags($this->input->post('note'));
            $inv_total = 0;
            $inv_total_no_tax = 0;

            for ($i = 1; $i <= 500; $i++) {
                if ($this->input->post($quantity . $i) && $this->input->post($product . $i) && $this->input->post($unit_cost . $i)) {

                    if (TAX1) {
                        $tax_id = $this->input->post($tax_rate . $i);
                        $tax_details = $this->inventories_model->getTaxRateByID($tax_id);
                        $taxRate = $tax_details->rate;
                        $taxType = $tax_details->type;
                        $tax_rate_id[] = $tax_id;

                        if ($taxType == 1 && $taxRate != 0) {
                            $item_tax = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)) * $taxRate / 100);
                            $val_tax[] = $item_tax;
                        } else {
                            $item_tax = $taxRate;
                            $val_tax[] = $item_tax;
                        }

                        if ($taxType == 1) {
                            $tax[] = $taxRate . "%";
                        } else {
                            $tax[] = $taxRate;
                        }
                    } else {
                        $item_tax = 0;
                        $tax_rate_id[] = 0;
                        $val_tax[] = 0;
                        $tax[] = "";
                    }

                    $product_details = $this->inventories_model->getProductByCode($this->input->post($product . $i));
                    $product_id[] = $product_details->id;
                    $product_name[] = $product_details->name;
                    $product_code[] = $product_details->code;

                    $inv_quantity[] = $this->input->post($quantity . $i);
                    $p_item_id[] = $this->input->post($item_id . $i);
                    //$inv_product_code[] = $this->input->post($product.$i);
                    $inv_unit_cost[] = $this->input->post($unit_cost . $i);
                    $inv_supplier_item[] = $this->input->post($supplier_item . $i);
                    $inv_gross_total[] = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));
                    $inv_total_no_tax += (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));
                }
            }

            if (TAX1) {
                $total_tax = array_sum($val_tax);
            } else {
                $total_tax = 0;
            }

            $keys = array("product_id", "product_code", "product_name", "tax_rate_id", "tax", "quantity", "unit_price", "gross_total", "val_tax", "supplier_id", "p_item_id");

            $items = array();
            foreach (array_map(null, $product_id, $product_code, $product_name, $tax_rate_id, $tax, $inv_quantity, $inv_unit_cost, $inv_gross_total, $val_tax, $inv_supplier_item, $p_item_id) as $key => $value) {
                $items[] = array_combine($keys, $value);
            }


            $inv_total = $inv_total_no_tax + $total_tax;

            $invDetails = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier_name' => $supplier_name,
                'note' => $note,
                'inv_total' => $inv_total_no_tax,
                'total_tax' => $total_tax,
                'total' => $inv_total
            );

        }

        $checkedBy = $this->inventories_model->checkedByStatus($id);

        if ($this->form_validation->run() == true && $checkedBy->checked == 0 && $this->inventories_model->makePurchaseOrder($id, $invDetails, $items, $warehouse_id)) {
            $this->session->set_flashdata('success_message', $this->lang->line("purchase_updated"));
            redirect("module=inventories&view=po_content", 'refresh');
        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            $data['success_message'] = $this->session->flashdata('success_message');

            $data['suppliers'] = $this->inventories_model->getAllSuppliers();
            $data['products'] = $this->inventories_model->getAllProducts();


            $data['tax_rates'] = $this->inventories_model->getAllTaxRates();
            $data['warehouses'] = $this->inventories_model->getAllWarehouses();

            $data['inv'] = $this->inventories_model->getInventoryByID($id);
            $data['inv_products'] = $this->inventories_model->getAllInventoryItems($id);
            $data['id'] = $id;
            $meta['page_title'] = $this->lang->line("update_purchase");
            $data['page_title'] = $this->lang->line("update_purchase");
            $this->load->view('commons/header', $meta);
            $this->load->view('edit_requisition', $data);
            $this->load->view('commons/footer');

        }
    }

//
    function make_purchase_order($id = NULL)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $groups = array('salesman', 'viewer');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        //validate form input
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required|xss_clean');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');

        $quantity = "quantity";
        $product = "product";
        $unit_cost = "unit_cost";
        $tax_rate = "tax_rate";
        $supplier_item = "supplier_item";

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no');
            $date = $this->ion_auth->fsd(trim($this->input->post('date')));
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $supplier_details = $this->inventories_model->getSupplierByID($supplier_id);
            $supplier_name = $supplier_details->name;
            $note = $this->ion_auth->clear_tags($this->input->post('note'));
            $inv_total = 0;
            $inv_total_no_tax = 0;

            for ($i = 1; $i <= 500; $i++) {
                if ($this->input->post($quantity . $i) && $this->input->post($product . $i) && $this->input->post($unit_cost . $i)) {

                    if (TAX1) {
                        $tax_id = $this->input->post($tax_rate . $i);
                        $tax_details = $this->inventories_model->getTaxRateByID($tax_id);
                        $taxRate = $tax_details->rate;
                        $taxType = $tax_details->type;
                        $tax_rate_id[] = $tax_id;

                        if ($taxType == 1 && $taxRate != 0) {
                            $item_tax = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)) * $taxRate / 100);
                            $val_tax[] = $item_tax;
                        } else {
                            $item_tax = $taxRate;
                            $val_tax[] = $item_tax;
                        }

                        if ($taxType == 1) {
                            $tax[] = $taxRate . "%";
                        } else {
                            $tax[] = $taxRate;
                        }
                    } else {
                        $item_tax = 0;
                        $tax_rate_id[] = 0;
                        $val_tax[] = 0;
                        $tax[] = "";
                    }

                    $product_details = $this->inventories_model->getProductByCode($this->input->post($product . $i));
                    $product_id[] = $product_details->id;
                    $product_name[] = $product_details->name;
                    $product_code[] = $product_details->code;

                    $inv_quantity[] = $this->input->post($quantity . $i);
                    //$inv_product_code[] = $this->input->post($product.$i);
                    $inv_unit_cost[] = $this->input->post($unit_cost . $i);
                    $inv_supplier_item[] = $this->input->post($supplier_item . $i);

                    $supplier_id_ar = $this->input->post($supplier_item . $i);
                    $supplier_details_ar = $this->inventories_model->getSupplierByID($supplier_id_ar);
                    $supplier_name_ar[] = $supplier_details_ar->name;

                    $inv_gross_total[] = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));
                    $inv_total_no_tax += (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));

                }
            }

            if (TAX1) {
                $total_tax = array_sum($val_tax);
            } else {
                $total_tax = 0;
            }

            $keys = array("product_id", "product_code", "product_name", "tax_rate_id", "tax", "quantity", "unit_price", "gross_total", "val_tax", "supplier_id");

            $items = array();
            foreach (array_map(null, $product_id, $product_code, $product_name, $tax_rate_id, $tax, $inv_quantity, $inv_unit_cost, $inv_gross_total, $val_tax, $inv_supplier_item) as $key => $value) {
                $items[] = array_combine($keys, $value);
            }


            $inv_total = $inv_total_no_tax + $total_tax;

            $invDetails = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier_name' => $supplier_name,
                'note' => $note,
                'inv_total' => $inv_total_no_tax,
                'total_tax' => $total_tax,
                'total' => $inv_total
            );

        }


        if ($this->form_validation->run() == true && $this->inventories_model->updatePurchase($id, $invDetails, $items, $warehouse_id)) {

            $this->session->set_flashdata('success_message', $this->lang->line("purchase_updated"));
            redirect("module=inventories", 'refresh');

        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            $data['success_message'] = $this->session->flashdata('success_message');

            $data['suppliers'] = $this->inventories_model->getAllSuppliers();
            $data['products'] = $this->inventories_model->getAllProducts();
            $data['tax_rates'] = $this->inventories_model->getAllTaxRates();
            $data['warehouses'] = $this->inventories_model->getAllWarehouses();
            $data['inv'] = $this->inventories_model->getInventoryByID($id);
            $data['inv_products'] = $this->inventories_model->getAllInventoryItems($id);

            if ($data['inv']->approved) {
                $this->session->set_flashdata('message', "This Purchese Order Already Approved. ");
                $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
                redirect('module=inventories', 'refresh');
            }


            $data['id'] = $id;
            $meta['page_title'] = $this->lang->line("update_purchase");
            $data['page_title'] = $this->lang->line("update_purchase");
            $this->load->view('commons/header', $meta);
            $this->load->view('edit', $data);
            $this->load->view('commons/footer');

        }
    }


    function make_mrr($id = NULL)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $groups = array('viewer', 'purchaser', 'verify', 'approver');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        $getPurchaseRef = $this->inventories_model->getPurchaseId($id);
        $All_ready_approved["id"] = null;
        if ($getPurchaseRef->mr_status === '1') $All_ready_approved["id"] = $getPurchaseRef->reference_no;
        if ($All_ready_approved["id"] != "" && $All_ready_approved["id"] != null) {
            $ready_approved = implode(',', $All_ready_approved);
            $this->session->set_flashdata('message', "MRR already created for following PO (" . $ready_approved . ").");
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect("module=inventories&view=mrr_list", 'refresh');
        }

        //validate form input
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('mr_reference_no', $this->lang->line("ref_no"), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required|xss_clean');
        $ar = array();
        $cr = array();

        //Quantity  validation rules
        for ($i = 1; $i <= 100; $i++) {
            $val = 0;
            $val = $this->input->post('rquantity' . $i);
            $ar[$i] = $val;
        }

        for ($j = 1; $j <= count($ar); $j++) {
            if ($ar[$j] != false) $cr[$j] = $ar[$j];
        }

        for ($k = 1; $k <= count($cr); $k++) {
            $qty = $cr[$k] + 1;
            $this->form_validation->set_rules('quantity' . $k, 'Quantity', 'required|less_than[' . $qty . ']');
        }

//
        $quantity = "quantity";
        $rquantity = "rquantity";
        $product = "product";
        $unit_cost = "unit_cost";
        $tax_rate = "tax_rate";
        $exp_date = "exp_date";
        $mr_reference[] = null;
        $getPurchaseId[] = null;
        $getSupplierId[] = null;
        $tax_rate_id[] = null;
        $warehouse_id_no[] = null;
        $supplier_id_no[] = null;
        $product_qty[] = null;
        $product_remain_qty[] = null;
        $inv_quantity[] = null;
        $product_remain_qty[] = null;
        $inv_unit_cost[] = null;

        $mrrObj = array();

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no');
            $mr_reference_no = $this->input->post('mr_reference_no');
            $date = $this->ion_auth->fsd(trim($this->input->post('date')));
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $supplier_details = $this->inventories_model->getSupplierByID($supplier_id);
            $supplier_name = $supplier_details->name;
            $inv_total = 0;
            $inv_total_no_tax = 0;
            for ($i = 1; $i <= 500; $i++) {
                if ($this->input->post($quantity . $i) && $this->input->post($product . $i) && $this->input->post($unit_cost . $i)) {
                    $product_details = $this->inventories_model->getProductByCode($this->input->post($product . $i));
                    $p_items = $this->inventories_model->getItemByProductId($product_details->id);
                    $product_id[] = $product_details->id;
                    $product_name[] = $p_items->product_name;
                    $product_code[] = $p_items->product_code;
                    $product_qty[] = $this->input->post($quantity . $i);
                    $product_pqty = $this->input->post($rquantity . $i);
                    $mr_item_status[] = in_array($product_details->id, $this->input->post('check_product')) ? 1 : 0;

                    $inv_quantity[] = $this->input->post($quantity . $i);
                    $product_remain_qty = ($this->input->post($rquantity . $i) - $this->input->post($quantity . $i));
                    $inv_unit_cost[] = $this->input->post($unit_cost . $i);

                    if($product_details->price < $this->input->post($unit_cost . $i)){
                        $this->session->set_flashdata('message', $this->lang->line("prd_price_less_then_cost") . " (" . $product_details->name . ")");
                        redirect("module=inventories&view=po_content", 'refresh');
                    }

                    if (TAX1) {
                        $tax_id = $this->input->post($tax_rate . $i);
                        $tax_details = $this->inventories_model->getTaxRateByID($tax_id);
                        $getPurchase = $this->inventories_model->checkedByStatus($id);
                        $getPurchaseId[] = $getPurchase->purchase_id;
                        $getSupplierId[] = $supplier_details->id;
                        $taxRate = $tax_details->rate;
                        $taxType = $tax_details->type;
                        $tax_rate_id[] = $tax_id;
                        $mr_reference[] = $mr_reference_no;
                        $warehouse_id_no[] = $warehouse_id;
                        $supplier_id_no[] = $supplier_id;

                        if ($taxType == 1 && $taxRate != 0) {
                            $item_tax = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)) * $taxRate / 100);
                            $val_tax[] = $item_tax;
                        } else {
                            $item_tax = $taxRate;
                            $val_tax[] = $item_tax;
                        }

                        if ($taxType == 1) {
                            $tax[] = $taxRate . "%";
                        } else {
                            $tax[] = $taxRate;
                        }

                        // date Convertion

                        if ($this->input->post($exp_date . $i) != "") {
                            $expDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post($exp_date . $i))));
                        } else {
                            $expDate = date('Y-m-d', strtotime(str_replace('/', '-', "01/01/1990")));
                        }

                        $mrrObj[] = array("make_purchase_id" => $id,
                            "purchase_id" => $getPurchase->purchase_id,
                            "purchase_item_code" => $p_items->product_code,
                            "purchase_item_name" => $p_items->product_name,
                            "purchase_item_id" => $product_details->id,
                            "po_qty" => $product_pqty,
                            "remain_qty" => $product_remain_qty,
                            "received_qty" => $this->input->post($quantity . $i),
                            "price" => $this->input->post($unit_cost . $i),
                            "tax_val" => $item_tax,
                            "tax_id" => $tax_id,
                            "inv_val" => (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i))),
                            "mrr_date" => date('Y-m-d H:i:s'),
                            "mrr_ref" => $mr_reference_no,
                            "supplier_id" => $supplier_id,
                            "wh_id" => $warehouse_id,
                            "created_by" => USER_ID,
                            "exp_date" => $expDate);


                    } else {
                        $item_tax = 0;
                        $tax_rate_id[] = 0;
                        $val_tax[] = 0;
                        $tax[] = "";
                    }

                    $xp_date[] = $expDate;
                    $inv_gross_total[] = (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));
                    $inv_total_no_tax += (($this->input->post($quantity . $i)) * ($this->input->post($unit_cost . $i)));

                }
            }


            $keys = array("product_id", "product_code", "product_name", "tax_rate_id", "tax", "quantity", "unit_price", "gross_total", "val_tax", "mr_item_status", "exp_date");

            $items = array();
            $mrr_items[] = null;
            $inv_total = $inv_total_no_tax + $total_tax;
            foreach (array_map(null, $product_id, $product_code, $product_name, $tax_rate_id, $tax, $product_qty, $inv_unit_cost, $inv_gross_total, $val_tax, $mr_item_status, $xp_date) as $key => $value) {
                $items[] = array_combine($keys, $value);
            }


            $invDetails = array(
                'reference_no' => $reference,
                'mr_reference_no' => $mr_reference_no,
                'date' => $date,
                'status' => 1
            );
        }


        if ($this->form_validation->run() == true && $this->inventories_model->updateMrr($id, $invDetails, $items, $warehouse_id, $mrrObj)) {
            $this->session->set_flashdata('success_message', "MRR create successfully!");
            redirect("module=inventories&view=mrr_list", 'refresh');
        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            $data['success_message'] = $this->session->flashdata('success_message');

            $data['suppliers'] = $this->inventories_model->getAllSuppliers();
            $data['products'] = $this->inventories_model->getAllProducts();
            $data['tax_rates'] = $this->inventories_model->getAllTaxRates();
            $data['warehouses'] = $this->inventories_model->getAllWarehouses();
            $data['inv'] = $this->inventories_model->getInventoryFromPOByPurchaseID($id);
            $data['inv_products'] = $this->inventories_model->getAllpoInventoryItems($id);

            if (!$data['inv']->approved) {
                $this->session->set_flashdata('message', "This Purchese Order Not Approve Yet");
                $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
                redirect('module=inventories&view=po_content', 'refresh');
            }

            $data['id'] = $id;
            $meta['page_title'] = "Material Receiving";
            $data['page_title'] = "Material Receiving";
            $this->load->view('commons/header', $meta);
            $this->load->view('make_mrr', $data);
            $this->load->view('commons/footer');

        }
    }


    /* ----------------------------------------------------------------------------------------------------------- */


    function csv_inventory()
    {
        $groups = array('purchaser', 'salesman', 'viewer');
        if ($this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        //validate form input
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('reference_no', $this->lang->line("ref_no"), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required|xss_clean');
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required|is_natural_no_zero|xss_clean');

        $this->form_validation->set_rules('note', $this->lang->line("note"), 'xss_clean');


        $this->form_validation->set_rules('userfile', $this->lang->line("upload_file"), 'xss_clean');

        $quantity = "quantity";
        $product = "product";
        $unit_price = "unit_price";

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('message', $this->lang->line("disabled_in_demo"));
                redirect('module=home', 'refresh');
            }

            $reference = $this->input->post('reference_no');
            $date = $this->ion_auth->fsd(trim($this->input->post('date')));
            $warehouse_id = $this->input->post('warehouse');
            $supplier_id = $this->input->post('supplier');
            $supplier_details = $this->inventories_model->getSupplierByID($supplier_id);
            $supplier_name = $supplier_details->name;
            $note = $this->ion_auth->clear_tags($this->input->post('note'));
            $inv_total = 0;


            if (isset($_FILES["userfile"])) /*if($_FILES['userfile']['size'] > 0)*/ {

                $this->load->library('upload_photo');

                //Set the config
                $config['upload_path'] = 'assets/uploads/csv/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '200';
                $config['overwrite'] = TRUE;

                //Initialize
                $this->upload_photo->initialize($config);

                if (!$this->upload_photo->do_upload()) {

                    //echo the errors
                    $error = $this->upload_photo->display_errors();
                    $this->session->set_flashdata('message', $error);
                    redirect("module=inventories&view=csv_inventory", 'refresh');
                }

                //If the upload success
                $csv = $this->upload_photo->file_name;

                $arrResult = array();
                $handle = fopen("assets/uploads/csv/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'quantity', 'unit_price');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {

                    if (!$this->inventories_model->getProductByCode($csv_pr['code'])) {
                        $this->session->set_flashdata('message', $this->lang->line("code_not_found") . " ( " . $csv_pr['code'] . " ). " . $this->lang->line("line_no") . " " . $rw);
                        redirect("module=inventories&view=csv_inventory", 'refresh');
                    }
                    $rw++;


                    $product_details = $this->inventories_model->getProductByCode($csv_pr['code']);
                    $product_id[] = $product_details->id;
                    $product_name[] = $product_details->name;
                    $product_code[] = $product_details->code;

                    $inv_quantity[] = $csv_pr['quantity'];

                    $inv_unit_price[] = $csv_pr['unit_price'];
                    $inv_gross_total[] = ($csv_pr['quantity'] * $csv_pr['unit_price']);
                    $inv_total += ($csv_pr['quantity'] * $csv_pr['unit_price']);

                    if (TAX1) {
                        $tax_id = $product_details->tax_rate ? $product_details->tax_rate : DEFAULT_TAX;
                        $tax_details = $this->inventories_model->getTaxRateByID($tax_id);
                        $taxRate = $tax_details->rate;
                        $taxType = $tax_details->type;
                        $tax_rate_id[] = $tax_id;

                        if ($taxType == 1 && $taxRate != 0) {
                            $item_tax = (($csv_pr['quantity'] * $csv_pr['unit_price']) * $taxRate / 100);
                            $val_tax[] = $item_tax;
                        } else {
                            $item_tax = $taxRate;
                            $val_tax[] = $item_tax;
                        }

                        if ($taxType == 1) {
                            $tax[] = $taxRate . "%";
                        } else {
                            $tax[] = $taxRate;
                        }
                    } else {
                        $item_tax = 0;
                        $tax_rate_id[] = 0;
                        $val_tax[] = 0;
                        $tax[] = "";
                    }
                }

            }

            if (TAX1) {
                $total_tax = array_sum($val_tax);
            } else {
                $total_tax = 0;
            }

            $keys = array("product_id", "product_code", "product_name", "tax_rate_id", "tax", "quantity", "unit_price", "gross_total", "val_tax");

            $items = array();
            foreach (array_map(null, $product_id, $product_code, $product_name, $tax_rate_id, $tax, $inv_quantity, $inv_unit_price, $inv_gross_total, $val_tax) as $key => $value) {
                $items[] = array_combine($keys, $value);
            }


            $gtotal = $inv_total + $total_tax;

            $invDetails = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier_name' => $supplier_name,
                'note' => $note,
                'inv_total' => $inv_total,
                'total_tax' => $total_tax,
                'total' => $gtotal
            );

            $items = $this->mres($items);
        }


        if ($this->form_validation->run() == true && $this->inventories_model->addPurchase($invDetails, $items, $warehouse_id)) {

            $this->session->set_flashdata('success_message', $this->lang->line("purchase_added"));
            redirect("module=inventories", 'refresh');


        } else {

            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));

            $data['reference_no'] = array('name' => 'reference_no',
                'id' => 'reference_no',
                'type' => 'text',
                'value' => $this->form_validation->set_value('reference_no'),
            );
            $data['date'] = array('name' => 'date',
                'id' => 'date',
                'type' => 'text',
                'value' => $this->form_validation->set_value('date'),
            );
            $data['supplier'] = array('name' => 'supplier',
                'id' => 'supplier',
                'type' => 'select',
                'value' => $this->form_validation->set_select('supplier'),
            );
            $data['note'] = array('name' => 'note',
                'id' => 'note',
                'type' => 'textarea',
                'value' => $this->form_validation->set_value('note'),
            );

            //$data['items'] = $items;
            $data['suppliers'] = $this->inventories_model->getAllSuppliers();
            $data['products'] = $this->inventories_model->getAllProducts();
            $data['warehouses'] = $this->inventories_model->getAllWarehouses();
            $data['rnumber'] = $this->inventories_model->getNextAI();

            $meta['page_title'] = $this->lang->line("add_purchase");
            $data['page_title'] = $this->lang->line("add_purchase");
            $this->load->view('commons/header', $meta);
            $this->load->view('csv_inventory', $data);
            $this->load->view('commons/footer');

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

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    function delete($id = NULL)
    {
        if (DEMO) {
            $this->session->set_flashdata('message', $this->lang->line("disabled_in_demo"));
            redirect('module=home', 'refresh');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $groups = array('owner','salesman');
        if (!$this->ion_auth->in_group($groups)) {
            $this->session->set_flashdata('message', $this->lang->line("access_denied"));
            $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
            redirect('module=home', 'refresh');
        }

        if ($this->inventories_model->deleteInventory($id)) {
            $this->session->set_flashdata('success_message', "Purchase requisition deleted successfully.");
            redirect('module=inventories', 'refresh');
        }

    }

    /*---------------------------------------------------------------------------------------------------------- */

    function scan_item()
    {
        if ($this->input->get('code')) {
            $code = $this->input->get('code');
        }

        if ($this->input->get('wh')) {
            $wh = $this->input->get('wh');
        }


        if ($item = $this->inventories_model->getProductByCodeForScan($code)) {
            $itemDetails = $this->inventories_model->getProductByNameFromWh($item->name, $wh);
            $code = $item->code;
            $name = $item->name;
            $cost = $item->cost;
            $product = array('name' => $name, 'code' => $code, 'cost' => $cost, 'tax_rate' => $itemDetails->quantity, "um" => $item->unit);
            $product_tax = $item->tax_rate;
        }


        echo json_encode($product);

    }

    function add_item()
    {
        if ($this->input->get('name')) {
            $name = $this->input->get('name');
        }

        if ($this->input->get('wh')) {
            $wh = $this->input->get('wh');
        }

        if ($item = $this->inventories_model->getProductByName($name)) {

            $itemDetails = $this->inventories_model->getProductByNameFromWh($name, $wh);
            $code = $item->code;
            $cost = $item->cost;
            $product = array('code' => $code, 'cost' => $cost, 'tax_rate' => $itemDetails->quantity, "um" => $item->unit);
//            $product = array('code' => $code, 'cost' => $cost, 'tax_rate' => $itemDetails->quantity);

        }

        echo json_encode($product);

    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);

        if (strlen($term) < 2) die();

        $rows = $this->inventories_model->getProductNames($term);

        $json_array = array();
        foreach ($rows as $row)
            array_push($json_array, $row->name);

        echo json_encode($json_array);
    }

    function formatMoney($number, $fractional = false)
    {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return $number;
    }

    function codeSuggestions()
    {
        $term = $this->input->get('term', TRUE);

        if (strlen($term) < 2) die();

        $rows = $this->inventories_model->getProductCodes($term);

        $json_array = array();
        foreach ($rows as $row)
            array_push($json_array, $row->code);

        echo json_encode($json_array);
    }
}