<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MX_Controller
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
    | MODULE: 			REPORTS
    | -----------------------------------------------------
    | This is reports module controller file.
    | -----------------------------------------------------
    */


    function __construct()
    {
        parent::__construct();

        // check if user logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('module=auth&view=login');
        }

        $this->load->model('reports_model');

    }

    function index()
    {

        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        $meta['page_title'] = $this->lang->line("reports");
        $data['page_title'] = $this->lang->line("reports");
        $this->load->view('commons/header', $meta);
        $this->load->view('content', $data);
        $this->load->view('commons/footer');
    }

    function products($alerts = "alerts")
    {


        //$data['n'] = $this->reports_model->get_total_alerts();
        $meta['page_title'] = "Stock Reports From";
        $data['page_title'] = "Stock Reports From";
        $this->load->view('commons/header', $meta);
        $this->load->view('alerts', $data);
        $this->load->view('commons/footer');
    }

    function getProductAlerts()
    {


//        $sp = "( select wp.product_id,sum(wp.quantity) quantity from warehouses_products wp group by product_id) pAlert";
        $sp = "( SELECT purchase_items.product_code,purchases.checked,purchases.reference_no, make_purchases.reference_no as ref,make_purchases.mr_status from purchase_items inner join purchases on purchases.id=purchase_items.purchase_id left join make_purchases on make_purchases.purchase_id=purchases.id where purchases.checked in (0,1) and make_purchases.mr_status=0) pAlert";

        $this->load->library('datatables');

        $this->datatables
            ->select('p.id as product_id, p.image as image, p.code as code, p.name as name, p.unit, p.price, p.quantity, p.alert_quantity, CASE WHEN pAlert.checked=0 then pAlert.reference_no  WHEN pAlert.checked=1 then pAlert.ref else  " " END as status ',false)
//            ->select('p.id as product_id, p.image as image, p.code as code, p.name as name, p.unit, p.price, p.quantity, p.alert_quantity, " " as status ',false)
            ->from('products p')
            ->join($sp,'p.code=pAlert.product_code','left')
            ->where('p.quantity <=  p.alert_quantity',  NULL,false)
            ->where('p.track_quantity', 1);

//        $this->datatables->add_column("Actions",
//            "<center><a id='$4 - $3' href='index.php?module=products&view=gen_barcode&code=$3&height=200' title='" . $this->lang->line("view_barcode") . "' class='barcode tip'><i class='icon-barcode'></i></a>
//			<a href='#' onClick=\"MyWindow=window.open('index.php?module=products&view=product_details&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=500'); return false;\" class='tip' title='" . $this->lang->line("product_details") . "'><i class='icon-fullscreen'></i></a>
//			<a class='image tip' id='$4 - $3' href='" . $this->config->base_url() . "uploads/$2' title='" . $this->lang->line("view_image") . "'><i class='icon-picture'></i></a>
//			<a href='index.php?module=products&amp;view=edit&amp;id=$1' class='tip' title='" . $this->lang->line("edit_product") . "'><i class='icon-edit'></i></a>
//			<a href='index.php?module=products&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_product') . "')\" class='tip' title='" . $this->lang->line("delete_product") . "'><i class='icon-trash'></i></a></center>", "productid, image, code, name");


        $this->datatables->add_column("Actions",
            "<center><a id='$4 - $3' href='index.php?module=products&view=gen_barcode&code=$3&height=200' title='" . $this->lang->line("view_barcode") . "' class='barcode tip'><i class='icon-barcode'></i></a>
			<a href='#' onClick=\"MyWindow=window.open('index.php?module=products&view=product_details&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=500'); return false;\" class='tip' title='" . $this->lang->line("product_details") . "'><i class='icon-fullscreen'></i></a>
			<a class='image tip' id='$4 - $3' href='" . $this->config->base_url() . "uploads/$2' title='" . $this->lang->line("view_image") . "'><i class='icon-picture'></i></a>
			</center>", "product_id, image, code, name");

        $this->datatables->unset_column('product_id');
        $this->datatables->unset_column('image');

        echo $this->datatables->generate();

//        echo $sp;
    }

    function overview()
    {

        $data['monthly_sales'] = $this->reports_model->getChartData();
        $data['stock'] = $this->reports_model->getStockValue();
        $meta['page_title'] = $this->lang->line("stock_chart");
        $data['page_title'] = $this->lang->line("stock_chart");
        $this->load->view('commons/header', $meta);
        $this->load->view('chart', $data);
        $this->load->view('commons/footer');
    }

    function warehouse_stock()
    {
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = DEFAULT_WAREHOUSE;
        }

        $data['stock'] = $this->reports_model->getWarehouseStockValue($warehouse);
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $data['warehouse_id'] = $warehouse;
        $meta['page_title'] = $this->lang->line("warehouse_stock_value");
        $data['page_title'] = $this->lang->line("stock_value");
        $this->load->view('commons/header', $meta);
        $this->load->view('stock', $data);
        $this->load->view('commons/footer');
    }


    function sales()
    {
        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        $data['users'] = $this->reports_model->getAllUsers();
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $data['customers'] = $this->reports_model->getAllCustomers();
        $data['billers'] = $this->reports_model->getAllBillers();

        $meta['page_title'] = $this->lang->line("sale_reports");
        $data['page_title'] = $this->lang->line("sale_reports");
        $this->load->view('commons/header', $meta);
        $this->load->view('sales', $data);
        $this->load->view('commons/footer');
    }
	
	function customer_sales()
    {
        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        $data['users'] = $this->reports_model->getAllUsers();
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $data['customers'] = $this->reports_model->getAllCustomers();
        $data['billers'] = $this->reports_model->getAllBillers();

        $meta['page_title'] = $this->lang->line("sale_report_customer");
        $data['page_title'] = $this->lang->line("sale_report_customer");
        $this->load->view('commons/header', $meta);
        $this->load->view('customer_sales', $data);
        $this->load->view('commons/footer');
    }

    function sales_margin()
    {
        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $data['categories'] = $this->reports_model->getAllCategories();

        $meta['page_title'] = $this->lang->line("sale_margin_report");
        $data['page_title'] = $this->lang->line("sale_margin_report");
        $this->load->view('commons/header', $meta);
        $this->load->view('sales_margin', $data);
        $this->load->view('commons/footer');
    }


    function getSales()
    {
        //if($this->input->get('name')){ $name = $this->input->get('name'); } else { $name = NULL; }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('paid_by')) {
            $paid_by = $this->input->get('paid_by');
        } else {
            $paid_by = NULL;
        }
        if ($start_date) {
            $start_date = $this->ion_auth->fsd($start_date);
            $end_date = $this->ion_auth->fsd($end_date);
        }
		
		
		
        $sr = "( select sir.sales_id, sum((COALESCE( sir.return_qty, 0 )* COALESCE( sir.price, 0 ))) as return_val  from sales_item_return sir where sir.warehouse_id='{$warehouse}' group by sir.sales_id,sir.product_id) sReturn";

        $this->load->library('datatables');
        $this->datatables
            ->select("sales.id as sid,date, reference_no, biller_name, customer_name, GROUP_CONCAT(CONCAT(sale_items.product_name, ' (Qty-', sale_items.quantity, ' ,Price-', sale_items.unit_price, ')') SEPARATOR ', <br>') as iname, (total +sum( (COALESCE( discount_val, 0)))) as gTotal ,total_tax, total_tax2,return_ref,(COALESCE((return_amount), 0)) as return_val ,sum( (COALESCE( discount_val, 0))) as discount, (total -(COALESCE(return_amount, 0)))  as total_val", FALSE)
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
            ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
            ->join($sr, 'sales.id=sReturn.sales_id', 'left')
            ->group_by('sales.id,sales.reference_no');


       
        if ($user) {
            $this->datatables->like('sales.user', $user);
        }
        //if($name) { $this->datatables->like('sale_items.product_name', $name, 'both'); }
        if ($biller) {
            $this->datatables->like('sales.biller_id', $biller);
        }
        if ($customer) {
            $this->datatables->like('sales.customer_id', $customer);
        }
        if ($warehouse) {
            $this->datatables->like('sales.warehouse_id', $warehouse);
        }
        if ($paid_by) {
            $this->datatables->where('sales.paid_by = ', $paid_by);
        }
        if ($reference_no) {
            $this->datatables->like('sales.reference_no', $reference_no, 'both');
        }
        if ($start_date) {
            $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        /*$this->datatables->add_column("Actions",
            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=sales&view=view_invoice&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='".$this->lang->line("view_invoice")."' class='tip'><i class='icon-fullscreen'></i></a>
            <a href='index.php?module=sales&view=pdf&id=$1' title='".$this->lang->line("download_pdf")."' class='tip'><i class='icon-file'></i></a>
            <a href='index.php?module=sales&view=email_invoice&id=$1' title='".$this->lang->line("email_invoice")."' class='tip'><i class='icon-envelope'></i></a>
            <a href='index.php?module=sales&amp;view=edit&amp;id=$1' title='".$this->lang->line("edit_invoice")."' class='tip'><i class='icon-edit'></i></a>
            <a href='index.php?module=sales&amp;view=delete&amp;id=$1' onClick=\"return confirm('". $this->lang->line('alert_x_invoice') ."')\" title='".$this->lang->line("delete_invoice")."' class='tip'><i class='icon-trash'></i></a></center>", "sid");*/

        $this->datatables->unset_column('sid');


        echo $this->datatables->generate();
    }
	
	
	
	
	function getSalesByCustomer()
    {
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('paid_by')) {
            $paid_by = $this->input->get('paid_by');
        } else {
            $paid_by = NULL;
        }
        if ($start_date) {
            $start_date = $this->ion_auth->fsd($start_date);
            $end_date = $this->ion_auth->fsd($end_date);
        }

        $sr = "( SELECT sale_items.sale_id,sale_items.product_id,  (COALESCE((COALESCE( sales_item_return.return_qty, 0 )* COALESCE( sales_item_return.price, 0 )),0))  as return_val FROM `sale_items` inner join sales_item_return on sale_items.sale_id=sales_item_return.sales_id and sale_items.product_id=sales_item_return.product_id and  sale_items.id=sales_item_return.sales_item_id where sales_item_return.warehouse_id='{$warehouse}' group by sale_items.product_id,sale_items.id) sReturn";


        $this->load->library('datatables');
        $this->datatables
            ->select("sale_items.product_id as pid,sales.customer_name,sale_items.product_code,sale_items.product_name,sale_items.product_unit,sum(sale_items.quantity),sale_items.unit_price,sum((COALESCE( sale_items.unit_price, 0))*(COALESCE( sale_items.quantity, 0))) as val, COALESCE( sale_items.discount_val, 0) as discount_value,COALESCE( sum(sReturn.return_val), 0) as return_value, (COALESCE(sum((COALESCE( sale_items.unit_price, 0))*(COALESCE( sale_items.quantity, 0))) -COALESCE( sReturn.return_val, 0) - COALESCE( sale_items.discount_val, 0),0)) as gross_total", FALSE)
            ->from('sales')
            ->join('sale_items', 'sales.id=sale_items.sale_id', 'left')
            ->join($sr, 'sale_items.sale_id=sReturn.sale_id and  sale_items.product_id=sReturn.product_id', 'left')
            ->group_by('sale_items.product_id','sale_items.sale_id');



        if ($customer) {
            $this->datatables->like('sales.customer_id', $customer);
        }
        if ($warehouse) {
            $this->datatables->like('sales.warehouse_id', $warehouse);
        }
        if ($start_date) {
            $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }
        /*$this->datatables->add_column("Actions",
            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=sales&view=view_invoice&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='".$this->lang->line("view_invoice")."' class='tip'><i class='icon-fullscreen'></i></a>
            <a href='index.php?module=sales&view=pdf&id=$1' title='".$this->lang->line("download_pdf")."' class='tip'><i class='icon-file'></i></a>
            <a href='index.php?module=sales&view=email_invoice&id=$1' title='".$this->lang->line("email_invoice")."' class='tip'><i class='icon-envelope'></i></a>
            <a href='index.php?module=sales&amp;view=edit&amp;id=$1' title='".$this->lang->line("edit_invoice")."' class='tip'><i class='icon-edit'></i></a>
            <a href='index.php?module=sales&amp;view=delete&amp;id=$1' onClick=\"return confirm('". $this->lang->line('alert_x_invoice') ."')\" title='".$this->lang->line("delete_invoice")."' class='tip'><i class='icon-trash'></i></a></center>", "sid");*/

        $this->datatables->unset_column('pid');


        echo $this->datatables->generate();
    }


    function getSalesMarginByCategory()
    {

        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }

        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('category')) {
            $category = $this->input->get('category');
        } else {
            $category = NULL;
        }
        if ($start_date) {
            $start_date = $this->ion_auth->fsd($start_date);
            $end_date = $this->ion_auth->fsd($end_date);
        }



        $this->load->library('datatables');
        $this->datatables
            ->select("sale_items.product_id as pid,categories.name,sale_items.product_code,sale_items.product_name,sale_items.product_unit,sum(sale_items.quantity) as qty,sale_items.unit_price,products.cost,sum((COALESCE( sale_items.unit_price, 0))*(COALESCE( sale_items.quantity, 0))) as val, sum((COALESCE( products.cost, 0))*(COALESCE( sale_items.quantity, 0))) as val1, (COALESCE(sum((COALESCE( sale_items.unit_price, 0))*(COALESCE( sale_items.quantity, 0))) -sum((COALESCE(products.cost, 0))*(COALESCE( sale_items.quantity, 0))),0)) as differ", FALSE)
            ->from('sales')
            ->join('sale_items', 'sales.id=sale_items.sale_id', 'left')
            ->join('products', 'sale_items.product_code=products.code', 'left')
            ->join('categories', 'products.category_id=categories.id', 'left')
            ->group_by('sale_items.product_id');




        if ($warehouse) {
            $this->datatables->like('sales.warehouse_id', $warehouse);
        }
        if ($start_date) {
            $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }
        if ($category) {
            $this->datatables->like('products.category_id', $category);
        }
        /*$this->datatables->add_column("Actions",
            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=sales&view=view_invoice&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='".$this->lang->line("view_invoice")."' class='tip'><i class='icon-fullscreen'></i></a>
            <a href='index.php?module=sales&view=pdf&id=$1' title='".$this->lang->line("download_pdf")."' class='tip'><i class='icon-file'></i></a>
            <a href='index.php?module=sales&view=email_invoice&id=$1' title='".$this->lang->line("email_invoice")."' class='tip'><i class='icon-envelope'></i></a>
            <a href='index.php?module=sales&amp;view=edit&amp;id=$1' title='".$this->lang->line("edit_invoice")."' class='tip'><i class='icon-edit'></i></a>
            <a href='index.php?module=sales&amp;view=delete&amp;id=$1' onClick=\"return confirm('". $this->lang->line('alert_x_invoice') ."')\" title='".$this->lang->line("delete_invoice")."' class='tip'><i class='icon-trash'></i></a></center>", "sid");*/

        $this->datatables->unset_column('pid');


//        echo $end_date;
        echo $this->datatables->generate();
    }



    function purchases()
    {
        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        $data['users'] = $this->reports_model->getAllUsers();
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $data['suppliers'] = $this->reports_model->getAllSuppliers();

        $meta['page_title'] = $this->lang->line("purchase_reports");
        $data['page_title'] = $this->lang->line("purchase_reports");
        $this->load->view('commons/header', $meta);
        $this->load->view('purchases', $data);
        $this->load->view('commons/footer');
    }

    function getPurchases()
    {
        //if($this->input->get('name')){ $name = $this->input->get('name'); } else { $name = NULL; }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('supplier')) {
            $supplier = $this->input->get('supplier');
        } else {
            $supplier = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse_id = $this->input->get('warehouse');
        } else {
            $warehouse_id = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }

        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }

        if ($start_date) {
            $start_date = $this->ion_auth->fsd($start_date);
            $end_date = $this->ion_auth->fsd($end_date);
        }




        $pp = "(SELECT mm.purchase_id,mm.make_purchase_id, mm.mrr_date,mm.id, mm.received_qty purchasedQty, mm.inv_val  purchasedVal from purchase_items p JOIN make_mrr mm on mm.purchase_id=p.purchase_id and mm.make_purchase_id=p.make_purchase_id where mm.mrr_date between
        '{$start_date}' and '{$end_date}' and mm.wh_id='{$warehouse_id}'
                      group by mm.make_purchase_id,mm.mrr_date) PCosts";

        $mp = "(select mp.id,mp.supplier_name,mp.reference_no,pi.product_name,mp.purchase_id,pi.quantity,mp.warehouse_id,mp.supplier_id,mp.inv_total from make_purchases mp inner join purchase_items pi on pi.make_purchase_id=mp.id  WHERE date BETWEEN
         '{$start_date}' and '{$end_date}' and mp.warehouse_id='{$warehouse_id}'
                         group by pi.make_purchase_id) mPurchase";


        $this->load->library('datatables');
        $this->datatables
            ->select("make_purchases.id as id,mPurchase.reference_no, date,  warehouses.name as wname, mPurchase.supplier_name, GROUP_CONCAT(CONCAT(mPurchase.product_name, ' (', mPurchase.quantity, ')') SEPARATOR ', <br>') as iname, COALESCE(mPurchase.inv_total, 0),PCosts.mrr_date, COALESCE(PCosts.purchasedQty, 0), COALESCE(PCosts.purchasedVal, 0)", FALSE)
            ->from('make_purchases')
            ->join($pp, 'PCosts.make_purchase_id=make_purchases.id', 'inner')
            ->join($mp, 'mPurchase.id=make_purchases.id', 'inner')
            ->join('warehouses', 'warehouses.id=make_purchases.warehouse_id', 'inner')
            ->group_by('make_purchases.supplier_id')
            ->group_by('make_purchases.date');

        if ($supplier) {
            $this->datatables->like('make_purchases.supplier_id', $supplier);
        }
        if ($warehouse_id) {
            $this->datatables->like('make_purchases.warehouse_id', $warehouse_id);
        }
        if ($reference_no) {
            $this->datatables->like('make_purchases.reference_no', $reference_no, 'both');
        }


        $this->datatables->add_column("Actions",
            "<center><a href='#' onClick=\"MyWindow=window.open('index.php?module=inventories&view=view_inventory&id=$1', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600'); return false;\" title='" . $this->lang->line("view_inventory") . "' class='tip'><i class='icon-fullscreen'></i></a> <a href='index.php?module=inventories&view=pdf&id=$1' title='" . $this->lang->line("download_pdf") . "' class='tip'><i class='icon-file'></i></a> <a href='index.php?module=inventories&view=email_inventory&id=$1' title='" . $this->lang->line("email_inventory") . "' class='tip'><i class='icon-envelope'></i></a> <a href='index.php?module=inventories&amp;view=edit&amp;id=$1' title='" . $this->lang->line("edit_inventory") . "' class='tip'><i class='icon-edit'></i></a> <a href='index.php?module=inventories&amp;view=delete&amp;id=$1' onClick=\"return confirm('" . $this->lang->line('alert_x_inventory') . "')\" title='" . $this->lang->line("delete_inventory") . "' class='tip'><i class='icon-trash'></i></a></center>", "id")
            ->unset_column('id');

        echo $this->datatables->generate();
    }

    function daily_sales()
    {
        if ($this->input->get('year')) {
            $year = $this->input->get('year');
        } else {
            $year = date('Y');
        }
        if ($this->input->get('month')) {
            $month = $this->input->get('month');
        } else {
            $month = date('m');
        }

        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        $config['translated_day_names'] = array($this->lang->line("sunday"), $this->lang->line("monday"), $this->lang->line("tuesday"), $this->lang->line("wednesday"), $this->lang->line("thursday"), $this->lang->line("friday"), $this->lang->line("saturday"));
        $config['translated_month_names'] = array('01' => $this->lang->line("january"), '02' => $this->lang->line("february"), '03' => $this->lang->line("march"), '04' => $this->lang->line("april"), '05' => $this->lang->line("may"), '06' => $this->lang->line("june"), '07' => $this->lang->line("july"), '08' => $this->lang->line("august"), '09' => $this->lang->line("september"), '10' => $this->lang->line("october"), '11' => $this->lang->line("november"), '12' => $this->lang->line("december"));

        $config['template'] = '

   			{table_open}<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">{/table_open}
			
			{heading_row_start}<tr>{/heading_row_start}
			
			{heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
			{heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
			{heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
			
			{heading_row_end}</tr>{/heading_row_end}
			
			{week_row_start}<tr>{/week_row_start}
			{week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
			{week_row_end}</tr>{/week_row_end}
			
			{cal_row_start}<tr class="days">{/cal_row_start}
			{cal_cell_start}<td class="day">{/cal_cell_start}
			
			{cal_cell_content}
				<div class="day_num">{day}</div>
				<div class="content">{content}</div>
			{/cal_cell_content}
			{cal_cell_content_today}
				<div class="day_num highlight">{day}</div>
				<div class="content">{content}</div>
			{/cal_cell_content_today}
			
			{cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
			{cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
			
			{cal_cell_blank}&nbsp;{/cal_cell_blank}
			
			{cal_cell_end}</td>{/cal_cell_end}
			{cal_row_end}</tr>{/cal_row_end}
			
			{table_close}</table>{/table_close}
';


        $this->load->library('daily_cal', $config);

        $sales = $this->reports_model->getDailySales($year, $month);

        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        if (!empty($sales)) {
          foreach ($sales as $sale) {
        $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . $this->lang->line("discount") . "</td><td>" . $this->ion_auth->formatMoney($sale->discount) . "</td></tr><tr><td>" . $this->lang->line("return") . "</td><td>" . $this->ion_auth->formatMoney($sale->return_quantity) . "</td></tr><tr><td>" . $this->lang->line("tax1") . "</td><td>" . $this->ion_auth->formatMoney($sale->tax1) . "</td></tr><tr><td>" . $this->lang->line("tax2") . "</td><td>" . $this->ion_auth->formatMoney($sale->tax2) . "</td></tr><tr><td>" . $this->lang->line("total") . "</td><td>" . $this->ion_auth->formatMoney($sale->total) . "</td></tr></table>";
        }





            /*for ($i = 1; $i <= $num; $i++){

                       if(isset($cal_data[$i])) {
                        $daily_sale[$i] = $cal_data[$i];
                    } else {
                        $daily_sale[$i] = $this->lang->line('no_sale');
                    }

            }


            } else {
                for($i=1; $i<=$num; $i++) {
                $daily_sale[$i] = $this->lang->line('no_sale');
            }*/
        } else {
            $daily_sale = array();
        }

        $data['calender'] = $this->daily_cal->generate($year, $month, $daily_sale);


        $meta['page_title'] = $this->lang->line("daily_sales");
        $data['page_title'] = $this->lang->line("daily_sales");
        $this->load->view('commons/header', $meta);
        $this->load->view('daily', $data);
        $this->load->view('commons/footer');
    }


    function monthly_sales()
    {
        if ($this->input->get('year')) {
            $year = $this->input->get('year');
        } else {
            $year = date('Y');
        }

        $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        $data['year'] = $year;

        $data['sales'] = $this->reports_model->getMonthlySales($year);

        $meta['page_title'] = $this->lang->line("monthly_sales");
        $data['page_title'] = $this->lang->line("monthly_sales");
        $this->load->view('commons/header', $meta);
        $this->load->view('monthly', $data);
        $this->load->view('commons/footer');
    }

    function custom_products()
    {
        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }

        $meta['page_title'] = $this->lang->line("reports") . " " . $dt;
        $data['products'] = $this->reports_model->getAllProducts();
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $meta['page_title'] = "Stock Reports" . " " . $dt;
        $data['page_title'] = "Stock Reports";
        $this->load->view('commons/header', $meta);
        $this->load->view('products', $data);
        $this->load->view('commons/footer');
    }


    function quantity_variance()
    {

        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        //$meta['page_title'] = $this->lang->line("reports")." ".$dt;
        $data['products'] = $this->reports_model->getAllProducts();
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $meta['page_title'] = "Stock Count Variance Report" . " " . $dt;
        $data['page_title'] = "Stock Count Variance Report";
        $this->load->view('commons/header', $meta);
        $this->load->view('count_stock', $data);
        $this->load->view('commons/footer');
    }

    function opening_stock()
    {

        $data['message'] = (validation_errors() ? validation_errors() : $this->session->flashdata('message'));
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        //$meta['page_title'] = $this->lang->line("reports")." ".$dt;
        $data['products'] = $this->reports_model->getAllProducts();
        $data['warehouses'] = $this->reports_model->getAllWarehouses();
        $meta['page_title'] = "Opening Stock & Closing Stock Report" . " " . $dt;
        $data['page_title'] = "Opening Stock & Closing Stock Report";
        $this->load->view('commons/header', $meta);
        $this->load->view('opening_stock', $data);
        $this->load->view('commons/footer');
    }


    function getOpening()
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }


        if ($this->input->get('warehouse')) {
            $warehouse_id = $this->input->get('warehouse');
        }


        if ($start_date) {
            $var1 = $start_date;
            $var2 = $end_date;
            $date1 = str_replace('/', '-', $var1);
            $date2 = str_replace('/', '-', $var2);
            $new_date = date('Y-m-d', strtotime($date1));
            $newE_date = date('Y-m-d', strtotime($date2));
            $s_date = $new_date . ' 00:00:00';
            $e_date = $newE_date . ' 23:59:59';


            $tr_s_date = $this->ion_auth->fsd($start_date);
            $tr_e_date = $this->ion_auth->fsd($end_date);

            // get All purchase Data

            $pp = "(SELECT mm.purchase_item_id, SUM( mm.received_qty ) purchasedQty from purchases p JOIN make_mrr mm on p.id = mm.purchase_id where
                         mm.mrr_date  between '{$s_date}' and '{$e_date}'and mm.wh_id='{$warehouse_id}'
                         group by mm.purchase_item_id ) PCosts";

            // get All Sales Data
            $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.gross_total ) totalSale from sales s JOIN sale_items si on s.id = si.sale_id where
                       s.date between '{$tr_s_date}' and '{$tr_e_date}' and s.warehouse_id='{$warehouse_id}'
                       group by si.product_id ) PSales";

            // get All Sales return Data
            $sr = "( SELECT sir.product_id,sum(sir.return_qty) as return_qty FROM `sale_items` si inner join sales_item_return sir on si.product_id=sir.product_id  and si.sale_id=sir.sales_id where
            sir.return_date BETWEEN '{$s_date}' and '{$e_date}' and sir.warehouse_id='{$warehouse_id}' group by sir.product_id ) SalesReturn";


            // get All Adjustment Data
            $ad_qty = "( SELECT  ap.product_id,ap.warehouse_id,sum(ap.adjust_qty_add) addQty, sum(ap.adjust_qty_remove) removeQty from adjustment_products ap where
                 ap.adjustment_date between '{$s_date}' and '{$e_date}'  and ap.warehouse_id='{$warehouse_id}' group by ap.product_id, ap.warehouse_id  ) adPro";


            // get All Transfer Data
            $tr_remove = "(SELECT ti.product_id,sum(quantity) qty FROM transfers t inner join transfer_items ti on t.id=ti.transfer_id where t.date BETWEEN '{$tr_s_date}'
             and '{$tr_e_date}' and t.from_warehouse_id='{$warehouse_id}' group by t.from_warehouse_id,ti.product_id,ti.quantity) trRemove";

            $tr_add = "(SELECT ti.product_id,sum(quantity) qty FROM transfers t inner join transfer_items ti on t.id=ti.transfer_id where t.date BETWEEN '{$tr_s_date}'
             and '{$tr_e_date}' and t.to_warehouse_id='{$warehouse_id}' group by t.from_warehouse_id,ti.product_id,ti.quantity) trAdd";
        } else {

            // get All purchase Data
            $pp = "( SELECT mm.purchase_item_id, SUM(mm.received_qty ) purchasedQty from make_mrr mm where  mm.wh_id='{$warehouse_id}' group by mm.purchase_item_id ) PCosts";

            // get All Sales Data
            $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.gross_total ) totalSale from sale_items si inner join sales s on s.id = si.sale_id where s.warehouse_id='{$warehouse_id}' group by si.product_id ) PSales";

            // get All Sales return Data
            $sr = "( SELECT sir.product_id,sum(sir.return_qty) as return_qty FROM `sale_items` si inner join sales_item_return sir on si.product_id=sir.product_id and si.sale_id=sir.sales_id where sir.warehouse_id='{$warehouse_id}' group by sir.product_id) SalesReturn";


            // get All Adjustment Data
            $ad_qty = "( SELECT  ap.product_id,ap.warehouse_id,sum(ap.adjust_qty_add) addQty, sum(ap.adjust_qty_remove) removeQty from adjustment_products ap where ap.warehouse_id='{$warehouse_id}' group by ap.product_id, ap.warehouse_id) adPro";

            // get All Transfer Data
            $tr_remove = "(SELECT ti.product_id,sum(quantity) qty FROM transfers t inner join transfer_items ti on t.id=ti.transfer_id
            where t.from_warehouse_id='{$warehouse_id}' group by t.from_warehouse_id,ti.product_id,ti.quantity) trRemove";
            $tr_add = "(SELECT ti.product_id,sum(quantity) qty FROM transfers t inner join transfer_items ti on t.id=ti.transfer_id
            where t.to_warehouse_id='{$warehouse_id}' group by t.from_warehouse_id,ti.product_id,ti.quantity) trAdd";

        }


        // Get Ware House Quantity
        $wps = "(SELECT wp.quantity, wp.warehouse_id,wp.product_id from warehouses_products wp where wp.warehouse_id='{$warehouse_id}') wProducts";


        // pull all main data
        $this->load->library('datatables');
        if ($product) {
            $this->datatables->where('p.id', $product);
        }
        $this->datatables
            ->select("p.code, p.name, p.unit,
                (COALESCE( wProducts.quantity,0)+ COALESCE( adPro.removeQty, 0 )- COALESCE( adPro.addQty, 0 ) - COALESCE( SalesReturn.return_qty, 0 ) + COALESCE( trRemove.qty, 0 ) - COALESCE( trAdd.qty, 0 ) - COALESCE( PCosts.purchasedQty, 0 ) + COALESCE( PSales.soldQty, 0 ) ) as quantity,
                COALESCE( PCosts.purchasedQty, 0 ) as PurchasedQty,
                COALESCE( PSales.soldQty, 0 ) as SoldQty,
                COALESCE( SalesReturn.return_qty, 0 ) as SoldReturnQty,
                COALESCE( adPro.addQty, 0 ) as addQty,COALESCE( adPro.removeQty, 0 ) as removeQty,
                COALESCE( trAdd.qty, 0 ) as trAddQty,
                COALESCE( trRemove.qty, 0 ) as trRmvQty,
                COALESCE( wProducts.quantity) as CloseingQnt", FALSE)
            ->from('products p', FALSE)
            ->join($sp, 'p.id = PSales.product_id', 'left')
            ->join($sr, 'p.id = SalesReturn.product_id', 'left')
            ->join($pp, 'p.id = PCosts.purchase_item_id', 'left')
            ->join($wps, 'p.id = wProducts.product_id', 'inner')
            ->join($ad_qty, 'p.id = adPro.product_id', 'left')
            ->join($tr_add, 'p.id = trAdd.product_id', 'left')
            ->join($tr_remove, 'p.id = trRemove.product_id', 'left');

        if ($product) {
            $this->datatables->where('p.id', $product);
        }
        echo $this->datatables->generate();
    }

    function getCP()
    {
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }

        if ($this->input->get('warehouse')) {
            $warehouse_id = $this->input->get('warehouse');
        }

        if ($start_date) {
            $start_date = $this->ion_auth->fsd($start_date);
            $end_date = $this->ion_auth->fsd($end_date);

            $pp = "(SELECT mm.purchase_item_id, SUM( mm.received_qty ) purchasedQty from purchases p JOIN make_mrr mm on p.id = mm.purchase_id where
                         mm.mrr_date  between '{$start_date}' and '{$end_date}' and mm.wh_id='{$warehouse_id}'
                         group by mm.purchase_item_id ) PCosts";

            $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.gross_total ) totalSale from sales s JOIN sale_items si on s.id = si.sale_id where
                       s.date between '{$start_date}' and '{$end_date}' and s.warehouse_id='{$warehouse_id}'
                       group by si.product_id ) PSales";
        } else {
            $pp = "( SELECT mm.purchase_item_id, SUM(mm.received_qty ) purchasedQty from make_mrr mm and mm.warehouse_id='{$warehouse_id}' group by mm.purchase_item_id ) PCosts";
            $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.gross_total ) totalSale from sale_items si inner join sales s on s.id = si.sale_id where s.warehouse_id='{$warehouse_id}' group by si.product_id ) PSales";
        }

        $wh_qty = "(SELECT wp.quantity, wp.warehouse_id,wp.product_id from warehouses_products wp where wp.warehouse_id='{$warehouse_id}') wProducts";

        $this->load->library('datatables');
        $this->datatables
            ->select("p.code, p.name,p.unit,
                COALESCE( PCosts.purchasedQty, 0 ) as PurchasedQty,
                COALESCE( PSales.soldQty, 0 ) as SoldQty,
                  wProducts.quantity as quantity,
                  p.cost,
                COALESCE( wProducts.quantity*p.cost, 0 ) as TotalSales", FALSE)
            ->from('products p', FALSE)
            ->join($wh_qty, 'p.id = wProducts.product_id', 'inner')
            ->join($sp, 'p.id = PSales.product_id', 'left')
            ->join($pp, 'p.id = PCosts.purchase_item_id', 'left')
			->where('p.quantity > 0',null);

        if ($product) {
            $this->datatables->where('p.id', $product);
        }

        echo $this->datatables->generate();

    }

    function getVariance()
    {

        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }

        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
            $start_date = $this->ion_auth->fsd($start_date);
        } else {
            $start_date = NULL;
        }


        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
            $end_date = $this->ion_auth->fsd($end_date);
        } else {
            $end_date = NULL;
        }
        if ($this->input->get('warehouse')) {
            $wh = $this->input->get('warehouse');
        }

        if ($start_date) {
            $var1 = $start_date;
            $var2 = $end_date;
            $sDate = $var1 . ' 00:00:00';
            $eDate = $var2 . ' 23:59:59';
        } else {
            $start_date = NULL;
            $sDate = Null;
            $eDate = Null;
        }

        if ($product != NULL & $start_date != NULL) {

            $wp = "(select w.code,c.created_at,c.count_quantity,wp.quantity,c.product_id
             FROM warehouses_products wp inner join warehouses w right join count_products c on w.id=wp.warehouse_id
             and c.product_id=wp.product_id and c.warehouse_id=wp.warehouse_id where c.created_at
             between '{$sDate}' and '{$eDate}' and c.product_id='{$product}' and c.warehouse_id='{$wh}') pd";
        } elseif ($product != NULL & $start_date == NULL) {
            $wp = "(select w.code,c.created_at,c.count_quantity,c.actual_quantity,wp.quantity,c.product_id
             FROM warehouses_products wp inner join warehouses w right join count_products c on w.id=wp.warehouse_id
             and c.product_id=wp.product_id and c.warehouse_id=wp.warehouse_id where c.product_id='{$product}'
             and c.warehouse_id='{$wh}') pd";
        } elseif ($product == NULL & $start_date != NULL) {
            $wp = "(select w.code,c.created_at,c.count_quantity,c.actual_quantity,wp.quantity,c.product_id
             FROM warehouses_products wp inner join warehouses w right join count_products c on w.id=wp.warehouse_id
             and c.product_id=wp.product_id and c.warehouse_id=wp.warehouse_id where c.created_at
             between '{$sDate}' and '{$eDate}' and c.warehouse_id='{$wh}') pd";
        } else {
            $wp = "(select w.code,c.created_at,c.count_quantity,c.actual_quantity,wp.quantity,c.product_id
             FROM warehouses_products wp inner join warehouses w right join count_products c on w.id=wp.warehouse_id
             and c.product_id=wp.product_id and c.warehouse_id=wp.warehouse_id and c.warehouse_id='{$wh}') pd";
        }


       
        $this->load->library('datatables');
        $this->datatables
            ->select("p.code, p.name,pd.code as c ,pd.created_at as count_date, p.unit, pd.count_quantity as cun_quantity, COALESCE( pd.actual_quantity, 0 ) as quantity,
            (COALESCE( pd.actual_quantity,0) - COALESCE( pd.count_quantity, 0 )) as variance", FALSE)
            ->from('products p', false)
            ->join($wp, 'p.id = pd.product_id', 'right');

        echo $this->datatables->generate();
//        echo $wp;


    }

}

