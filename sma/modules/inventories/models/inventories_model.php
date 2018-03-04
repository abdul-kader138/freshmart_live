<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


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
| This is inventories module's model file.
| -----------------------------------------------------
*/


class Inventories_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();

    }

    public function getAllSuppliers()
    {
        $this->db->select('id, name, company')->from('suppliers');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getTaxRateByID($id)
    {

        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getAllPurchaseInfo($id)
    {

        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;

    }

    public function getSupplierByID($id)
    {

        $q = $this->db->get_where('suppliers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getAllProducts()
    {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllInventoryIAlerttems()
    {


        $q = $this->db->query("SELECT * FROM products WHERE alert_quantity > quantity AND track_quantity=1");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }


    }

    public function getProductByID($id)
    {

        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getNextAI()
    {
        $this->db->select_max('id');
        $q = $this->db->get('make_purchases');
        if ($q->num_rows() > 0) {
            $row = $q->row();
            //return QUOTE_REF."-".date('Y')."-".sprintf("%03s", $row->id+1);
            return PURCHASE_REF . "-" . sprintf("%04s", $row->id + 1);
        }

        return FALSE;

    }

    public function getRQNextAI()
    {
        $this->db->select_max('id');
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            $row = $q->row();
            return "RQ" . "-" . sprintf("%04s", $row->id + 1);
        }

        return FALSE;

    }

    public function getProductsByCode($code)
    {
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }


    public function getProductByCode($code)
    {

        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }



    public function getProductByCodeForScan($code)
    {

        //$q = $this->db->get_where('products', array('code' => $code), 1);
        $q = $this->db->query("SELECT * FROM products LEFT JOIN promotion ON products.discount_id=promotion.promo_id  WHERE code LIKE '%{$code}%' OR cf4 LIKE  '%{$code}%' OR name LIKE  '%{$code}%' Limit 1");
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getProductNames($term)
    {
        $this->db->select('name')->limit('10');
        $this->db->like('name', $term, 'both');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getProductByName($name)
    {

        $q = $this->db->get_where('products', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function getProductByNameFromWh($name,$wh_id)
    {

        $q = $this->db->get_where('products', array('name' => trim($name)), 1);
        if ($q->num_rows() > 0) {
            $product= $q->row();
            $wh_item = $this->db->get_where('warehouses_products', array('product_id' => $product->id,'warehouse_id' => $wh_id), 1);
            if ($wh_item->num_rows() > 0) {
                return $wh_item->row();
            }
            return true;

        }

        return FALSE;

    }


    public function updateProductQuantity($product_id, $quantity, $warehouse_id, $product_cost)
    {

        // update the product with new details
        if ($this->updatePrice($product_id, $product_cost) && $this->addQuantity($product_id, $warehouse_id, $quantity)) {
            return true;
        }

        return false;
    }

    public function calculateAndUpdateQuantity($item_id, $product_id, $quantity, $warehouse_id, $product_cost)
    {

        // update the product with new details
        if ($this->updatePrice($product_id, $product_cost) && $this->calculateAndAddQuantity($item_id, $product_id, $warehouse_id, $quantity)) {
            return true;
        }

        return false;
    }

    public function calculateAndAddQuantity($item_id, $product_id, $warehouse_id, $quantity)
    {


        //check if entry exist then update else inster
        if ($this->getProductQuantity($product_id, $warehouse_id)) {

            //get product details to calculate quantity
            $quantity_details = $this->getProductQuantity($product_id, $warehouse_id);
            $product_quantity = $quantity_details['quantity'];
            $item_details = $this->getItemByID($item_id);
            $item_quantity = $item_details->quantity;
            $after_quantity = $product_quantity - $item_quantity;
            $new_quantity = $after_quantity + $quantity;


            if ($this->updateQuantity($product_id, $warehouse_id, $new_quantity)) {
                return TRUE;
            }

        } else {

            if ($this->insertQuantity($product_id, $warehouse_id, $quantity)) {
                return TRUE;
            }
        }

        return FALSE;

    }

    public function addQuantity($product_id, $warehouse_id, $quantity)
    {

        //check if entry exist then update else inster
        if ($this->getProductQuantity($product_id, $warehouse_id)) {

            $warehouse_quantity = $this->getProductQuantity($product_id, $warehouse_id);
            $old_quantity = $warehouse_quantity['quantity'];
            $new_quantity = $old_quantity + $quantity;

            if ($this->updateQuantity($product_id, $warehouse_id, $new_quantity)) {
                return TRUE;
            }

        } else {

            if ($this->insertQuantity($product_id, $warehouse_id, $quantity)) {
                return TRUE;
            }
        }

        return FALSE;

    }

    public function insertQuantity($product_id, $warehouse_id, $quantity)
    {

        // Product data
        $productData = array(
            'product_id' => $product_id,
            'warehouse_id' => $warehouse_id,
            'quantity' => $quantity
        );

        if ($this->db->insert('warehouses_products', $productData)) {
            return true;
        } else {
            return false;
        }
    }


    public function addCountQuantity($code, $quantity,$warehouseId)
    {
        $data = $this->getProductByCode($code);
        $warehouse_data=$this->getProductQuantity($data->id,$warehouseId);
        // Product data
        $productData = array(
            'product_id' => $data->id,
            'count_quantity' => $quantity,
            'actual_quantity' => $warehouse_data['quantity'],
            'warehouse_id' => $warehouseId,
            'user_id' => USER_ID
        );
//
        if ($this->db->insert('count_products', $productData)) {
            return true;
        } else {
            return false;
        }
    }


    public function updateQuantity($product_id, $warehouse_id, $quantity)
    {

        $productData = array(
            'quantity' => $quantity
        );

        //$this->db->where('product_id', $id);
        if ($this->db->update('warehouses_products', $productData, array('product_id' => $product_id, 'warehouse_id' => $warehouse_id))) {
            return true;
        } else {
            return false;
        }
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1);

        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }

        return FALSE;

    }


    public function updatePrice($id, $unit_price)
    {

        // Product data
        $productData = array(
            'cost' => $unit_price
        );

        $this->db->where('id', $id);
        if ($this->db->update('products', $productData)) {
            return true;
        }

        return false;

    }

    public function getAllInventories()
    {
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function inventories_count()
    {
        return $this->db->count_all("purchases");
    }

    public function fetch_inventories($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc");
        $query = $this->db->get("purchases");

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
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }



    public function getAllInventoryItemsFromMrr($purchase_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_items', array('make_purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }


    public function getAllpoInventoryItems($purchase_id)

    {

        $this->db->order_by('id', 'asc');
        $p = $this->db->get_where('make_purchases', array('id' => $purchase_id), 1);
        if ($p->num_rows() > 0) {
            $data = $p->row();
            $q = $this->db->get_where('purchase_items', array('purchase_id' => $data->purchase_id, 'make_purchase_id' => $purchase_id));
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $value[] = $row;
                }
                return $value;
            }
        }
    }


    public function getAllRequisitionInventoryItems($purchase_id)

    {

        $this->db->order_by('id', 'asc');
        $p = $this->db->get_where('purchases', array('id' => $purchase_id), 1);
        if ($p->num_rows() > 0) {
            $data = $p->row();
            $q = $this->db->get_where('purchase_items', array('purchase_id' => $data->id));
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $value[] = $row;
                }
                return $value;
            }
        }
    }

    public function getInventoryByID($id)
    {

        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function getPoInventoryByID($id)
    {

        $data = null;
        $p = $this->db->get_where('make_purchases', array('id' => $id), 1);
//        $p = $this->db->get_where('make_purchases', array('id' => $id), 1);
        if ($p->num_rows() > 0) {
            $data = $p->row();
            $q = $this->db->get_where('purchases', array('id' => $data->purchase_id));
            if ($q->num_rows() > 0) {

                return $q->row();
            }
        }
    }


    public function getRequisitionInventoryByID($id)
    {
            $q = $this->db->get_where('purchases', array('id' => $id));
            if ($q->num_rows() > 0) {

                return $q->row();
            }
    }

    public function getPurchasesById($id)
    {

        $data = null;
//        $p = $this->db->get_where('make_purchases', array('id' => $id), 1);
        $p = $this->db->get_where('make_purchases', array('purchase_id' => $id), 1);
        if ($p->num_rows() > 0) {
            return $p->row();
        }
        return FALSE;
    }


    public function getPurchaseId($id)
    {

        $data = null;
//        $p = $this->db->get_where('make_purchases', array('id' => $id), 1);
        $p = $this->db->get_where('make_purchases', array('id' => $id), 1);
        if ($p->num_rows() > 0) {
            return $p->row();
        }
        return FALSE;
    }

    public function getmakePurchaseInventoryByID($id)
    {

        $q = $this->db->get_where('make_purchases', array('purchase_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function checkedByStatus($id)
    {

        $q = $this->db->get_where('make_purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getmakePurchaseForVerifyInventoryByID($id)
    {

        $q = $this->db->get_where('make_purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getItemByID($id)
    {

        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function getItemByProductId($id)
    {

        $q = $this->db->get_where('purchase_items', array('product_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function getInventoryByPurchaseID($purchase_id)
    {

        $q = $this->db->get_where('purchases', array('id' => $purchase_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getInventoryFromPOByPurchaseID($purchase_id)
    {

        $q = $this->db->get_where('make_purchases', array('id' => $purchase_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function getMakeMrrInfoByPurchasedId($purchase_id)
    {

        $q = $this->db->get_where('make_mrr', array('make_purchase_id' => $purchase_id),1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }


    public function getMakeMrrInfo($purchase_id)
    {

        $q = $this->db->get_where('make_mrr', array('make_purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

    }

    public function getInventoryFromPOByMakePurchaseID($purchase_id)
    {
        $data = null;
        $p = $this->db->get_where('make_purchases', array('id' => $purchase_id), 1);
        if ($p->num_rows() > 0) {
            $data = $p->row();
            $q = $this->db->get_where('purchases', array('id' => $data->purchase_id));
            if ($q->num_rows() > 0) {

                return $q->row();
            }
        }
        return FALSE;
    }


    public function getWorkorderByPurchaseID($purchase_id)
    {

        $q = $this->db->query("SELECT p.*, u.username as chk_name, u1.username as app_name, u2.username as verify_name FROM make_purchases as p LEFT JOIN users as u ON p.`checked_by` = u.id LEFT JOIN users
		as u1 ON p.`approved_by` = u1.id LEFT JOIN users as u2 ON p.`verify_by` = u2.id WHERE p.id='$purchase_id'");
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function npQTY($product_id, $quantity)
    {
        $prD = $this->getProductByID($product_id);
        $nQTY = $prD->quantity + $quantity;
        $this->db->update('products', array('quantity' => $nQTY), array('id' => $product_id));
    }

    public function addPurchase($pdata, $items = array(), $warehouse_id)
    {

        // purchase data
        $purchseData = array(
            'reference_no' => $pdata['reference_no'],
            'warehouse_id' => $warehouse_id,
            'supplier_id' => $pdata['supplier_id'],
            'supplier_name' => $pdata['supplier_name'],
            'date' => $pdata['date'],
            'note' => $pdata['note'],
            'total_tax' => $pdata['total_tax'],
            'inv_total' => $pdata['inv_total'],
            'total' => $pdata['total'],
            'user' => USER_NAME
        );

        if ($this->db->insert('purchases', $purchseData)) {
            $purchase_id = $this->db->insert_id();

//            foreach ($items as $data) {
//                $this->npQTY($data['product_id'], $data['quantity']);
//                $this->updateProductQuantity($data['product_id'], $data['quantity'], $warehouse_id, $data['unit_price']);
//            }

            $addOn = array('purchase_id' => $purchase_id);
            end($addOn);
            foreach ($items as &$var) {
                $var = array_merge($addOn, $var);
            }

            if ($this->db->insert_batch('purchase_items', $items)) {
                return true;
            }
        }
        return false;
    }


    public function makePurchaseOrder($id, $pdata, $items = array(), $warehouse_id)
    {


        $getInventory = $this->getInventoryByID($id);

        $templevel = 0;

        $newkey = 0;

        $grouparr[$templevel] = "";

        foreach ($items as $key => $val) {

            if ($templevel == $val['supplier_id']) {
                $grouparr[$templevel][$newkey] = $val;
            } else {
                $grouparr[$val['supplier_id']][$newkey] = $val;
            }
            $newkey++;
        }

        foreach ($grouparr as $key => $value) {

            if (is_array($value)) {
                $inv_total = array_sum(array_map(function ($item) {
                    return $item['gross_total'];
                }, $value));

                $tax_total = array_sum(array_map(function ($item) {
                    return $item['val_tax'];
                }, $value));

                $supplier_details = $this->getSupplierByID($key);

                // purchase data
                $purchseData = array(
                    'reference_no' => $this->getNextAI(),
                    'warehouse_id' => $warehouse_id,
                    'supplier_id' => $key,
                    'supplier_name' => $supplier_details->company,
                    'date' => $pdata['date'],
                    'note' => $pdata['note'],
                    'total_tax' => $tax_total,
                    'inv_total' => $inv_total,
                    'total' => $inv_total + $tax_total,
                    'checked' => 1,
                    'checked_by' => USER_ID,
                    'user' => $getInventory->user,
                    'checked_at' => date('Y-m-d H:i:s'),
                    'purchase_id' => $id

                );


                if ($this->db->insert('make_purchases', $purchseData)) {

                    $make_purchase_id = $this->db->insert_id();

                    $this->db->update('purchases', array('checked' => 1, 'checked_by' => USER_ID, 'checked_at' => date('Y-m-d H:i:s'),'warehouse_id'=>$warehouse_id), array('id' => $id));


                    foreach ($value as $data) {

                       $this->db->update('purchase_items', array('make_purchase_id' => $make_purchase_id, 'supplier_id' => $key,'unit_price'=>$data['unit_price'],'quantity'=>$data['quantity'],'gross_total'=>($data['quantity']*$data['unit_price'])), array('id' => $data['p_item_id']));
					}

                }

            }


        }


        return true;
    }

    public function upQTY($product_id, $quantity)
    {
        $prD = $this->getProductByID($product_id);
        $nQTY = $prD->quantity - $quantity;
        $this->db->update('products', array('quantity' => $nQTY), array('id' => $product_id));
    }

    public function updatePurchase($id, $pdata, $items = array(), $warehouse_id)
    {
//
        $templevel = 0;

        $newkey = 0;

        $grouparr[$templevel] = "";

        foreach ($items as $key => $val) {

            if ($templevel == $val['supplier_id']) {
                $grouparr[$templevel][$newkey] = $val;
            } else {
                $grouparr[$val['supplier_id']][$newkey] = $val;
            }
            $newkey++;
        }


        $old_items = $this->getAllInventoryItems($id);
        $old_inv = $this->getInventoryByID($id);
        foreach ($old_items as $data) {
            $item_id = $data->id;
            $item_details = $this->getItemByID($item_id);
            $item_qiantity = $item_details->quantity;
            $product_id = $data->product_id;
            $pr_qty_details = $this->getProductQuantity($product_id, $old_inv->warehouse_id);
            $pr_qty = $pr_qty_details['quantity'];
            $qty = $pr_qty - $item_qiantity;
        }

        $purchseData = array(
            'reference_no' => $pdata['reference_no'],
            'warehouse_id' => $warehouse_id,
            'supplier_id' => $pdata['supplier_id'],
            'supplier_name' => $pdata['supplier_name'],
            'date' => $pdata['date'],
            'note' => $pdata['note'],
            'total_tax' => $pdata['total_tax'],
            'inv_total' => $pdata['inv_total'],
            'total' => $pdata['total'],
            'updated_by' => USER_NAME
        );


        $this->db->where('id', $id);
        if ($this->db->update('purchases', $purchseData) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {

//            foreach ($items as $data) {
//                $this->npQTY($data['product_id'], $data['quantity']);
                //$this->updateProductQuantity($data['product_id'], $data['quantity'], $warehouse_id, $data['unit_price']);
//            }

            $addOn = array('purchase_id' => $id);
            end($addOn);
            foreach ($items as &$var) {
                $var = array_merge($addOn, $var);
            }


            if ($this->db->insert_batch('purchase_items', $items)) {
                return true;
            }


        }

        return false;
    }


    public function updatePurchaseOrder($id, $p_data, $items = array(), $warehouse_id, $p_id)
    {
        $templevel = 0;
        $newkey = 0;
        $grouparr[$templevel] = "";

        foreach ($items as $key => $val) {

            if ($templevel == $val['supplier_id']) {
                $grouparr[$templevel][$newkey] = $val;
            } else {
                $grouparr[$val['supplier_id']][$newkey] = $val;
            }
            $newkey++;
        }


        $old_items = $this->getAllInventoryItems($id);
        $old_inv = $this->getInventoryByID($id);
        foreach ($old_items as $data) {
            $item_id = $data->id;
            $item_details = $this->getItemByID($item_id);
            $item_qiantity = $item_details->quantity;
            $product_id = $data->product_id;
            $pr_qty_details = $this->getProductQuantity($product_id, $old_inv->warehouse_id);
            $pr_qty = $pr_qty_details['quantity'];
            $qty = $pr_qty - $item_qiantity;
        }


        $purchseData = array(
            'reference_no' => $p_data['reference_no'],
            'warehouse_id' => $warehouse_id,
            'date' => $p_data['date'],
            'note' => $p_data['note'],
            'total_tax' => $p_data['total_tax'],
            'inv_total' => $p_data['inv_total'],
            'total' => $p_data['total'],
            'updated_by' => USER_NAME
        );


        $condition = array('id' => $p_id);
        $this->db->where($condition);
        if ($this->db->update('purchases', $purchseData)) {


            $addOn = array('purchase_id' => $p_id);
            end($addOn);


            // update info to make_purchase
            foreach ($items as &$var) {
                $params = array(
                    'purchase_id' => $p_id,
                    'make_purchase_id' => $id,
                    'product_id' => $var['product_id']
                );
                $var = array_merge($addOn, $var);
                unset($var['p_item_id']);
                $supplierInfo = $this->getSupplierByID($var['supplier_id']);

                $make_purchase_data = array(
                    'warehouse_id' => $warehouse_id,
                    'supplier_id' => $supplierInfo->id,
                    'supplier_name' => $supplierInfo->company,
                    'date' => $p_data['date'],
                    'note' => $p_data['note'],
                    'total_tax' => $p_data['total_tax'],
                    'inv_total' => $p_data['inv_total'],
                    'total' => $p_data['total'],
                    'updated_by' => USER_NAME
                );


                $this->db->update('purchase_items', $var, $params);

                // update info to make_purchase
                $this->db->update('make_purchases', $make_purchase_data, array('id' => $id));

            }
            return true;
        }
        return false;
    }


    public function updateMrr($id, $pdata, $items = array(), $warehouse_id, $obj)

    {
        $purchseData = array(
            'mr_reference_no' => $pdata['mr_reference_no'],
            'mr_date' => $pdata['date'],
            'mr_entry_date' => date("Y-m-d"),
            'mr_status' => $pdata['status'],
            'mr_entry_by' => USER_ID
        );


        $ifMrrNotExists = $this->getMakeMrrInfoByPurchasedId($obj['purchase_id']);
        if (!$ifMrrNotExists) {
            $this->db->where('id', $id);
            if ($this->db->update('make_purchases', $purchseData)) {
                foreach ($obj as $data) {
                    $this->npQTY($data['purchase_item_id'], $data['received_qty']);
                    $this->updateProductQuantity($data['purchase_item_id'], $data['received_qty'], $warehouse_id, $data['price']);
                }
                $this->db->insert_batch('make_mrr', $obj);
                return true;
            } else {
                return false;
            }
            return false;
        }
    }

    public function updateCheck($purchase_id)
    {

        $data = array('checked' => 1, 'checked_by' => USER_ID, 'checked_at' => date('Y-m-d H:i:s'));
        $this->db->where('id', $purchase_id);
        if ($this->db->update('purchases', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateApprovePO($purchase_id)
    {
        $data = array('approved' => 1, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
        $p_data = $this->getPurchaseId($purchase_id);

        $this->db->where('id', $purchase_id);
        if ($this->db->update('make_purchases', $data)) {
//            $this->db->where('id', $p_data->purchase_id);
//            $this->db->update('purchases', $data);
            return true;
        } else {
            return false;
        }
    }


    public function updateApprove($purchase_id)
    {

        $getInventory = $this->getmakePurchaseInventoryByID($id);

        $data = array('approved' => 1, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
        $this->db->where('id', $purchase_id);
        if ($this->db->update('make_purchases', $data)) {
            $this->db->update('purchases', $data, array('id' => $getInventory->purchase_id));

            return true;
        } else {
            return false;
        }
    }

    public function updateMrrApprove($purchase_id)
    {
        $data = array('mr_status' => 2, 'mr_approve_by' => USER_ID, 'mr_approve_date' => date('Y-m-d H:i:s'));
        $this->db->where('id', $purchase_id);
        if ($this->db->update('make_purchases', $data)) {

            $mrrData=$this->db->getMakeMrrInfo($purchase_id);
                $obj=array(
                    "approved_by"=>USER_ID,
                    "approved_date"=>date('Y-m-d H:i:s')
                );
                $condition=array(
                    "make_purchase_id"=>$purchase_id,
                );
                $condition=array("make_purchase_id"=>$purchase_id);
                $this->db->update('make_mrr', $obj,$condition);
            return true;
        } else {
            return false;
        }
    }

    public function updateVerifyPO($purchase_id)
    {

        $data = array('verify_status' => 1, 'verify_by' => USER_ID, 'verify_at' => date('Y-m-d H:i:s'));
//        $p_data = $this->inventories_model->getPurchaseId($purchase_id);

        $this->db->where('id', $purchase_id);
        if ($this->db->update('make_purchases', $data)) {
//            $this->db->where('id', $p_data->purchase_id);
//            $this->db->update('purchases', $data);
            return true;
        } else {
            return false;
        }
    }


    public function updateVerify($purchase_id)
    {

        $data = array('verify_status' => 1, 'verify_by' => USER_ID, 'verify_at' => date('Y-m-d H:i:s'));

        $getInventory = $this->getmakePurchaseInventoryByID($purchase_id);

        $this->db->where('id', $purchase_id);
        if ($this->db->update('make_purchases', $data)) {
            $this->db->update('purchases', $data, array('id' => $getInventory->purchase_id));

            return true;
        } else {
            return false;
        }
    }

    public function cancelRequisition($purchase_id)
    {
        $data = array('approved' => 2, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
        $this->db->where('id', $purchase_id);
        if ($this->db->update('purchases', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function cancelMrr($purchase_id)
    {
        $data = array('approved' => 2, 'approved_by' => USER_ID, 'approved_at' => date('Y-m-d H:i:s'));
        $data = array('mr_status' => 3);
        $this->db->where('id', $purchase_id);
        if ($this->db->update('make_purchases', $data)) {
            return true;
        } else {
            return false;
        }
    }


    public function getAllWarehouses()
    {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
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

        foreach ($items as $item) {
            $product_id = $item->product_id;
//            $item_details = $this->getProductQuantity($product_id, $warehouse_id);
//            $pr_quantity = $item_details['quantity'];
//            $inv_quantity = $item->quantity;
//            $new_quantity = $pr_quantity - $inv_quantity;
//
//            $this->updateQuantity($product_id, $warehouse_id, $new_quantity);
//            $this->upQTY($product_id, $item->quantity);
        }

        if ($this->db->delete('purchase_items', array('purchase_id' => $id,'make_purchase_id'=>0)) && $this->db->delete('purchases', array('id' => $id, "checked"=>0))) {
            return true;
        }
        return FALSE;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {

        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;

    }

    public function getProductCodes($term)
    {
        $this->db->select('code');
        $this->db->like('code', $term, 'both')->limit('10');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }


}
