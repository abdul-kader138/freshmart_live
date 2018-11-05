<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Home_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

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
	
	public function getProductsQuantity($product_id, $warehouse = DEFAULT_WAREHOUSE) 
	{
		$q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row_array();
		  } 
		
		  return FALSE;
		
	}
	
	function get_calendar_data($year, $month) {
		
		$query = $this->db->select('date, data')->from('calendar')
			->like('date', "$year-$month", 'after')->get();
			
		$cal_data = array();
		
		foreach ($query->result() as $row) {
			$day = (int)substr($row->date,8,2);
			$cal_data[$day] = str_replace("|", "<br>", html_entity_decode($row->data));
		}
		
		return $cal_data;
		
	}
	
	public function updateComment($comment)
	{
			if($this->db->update('comment', array('comment' => $comment))) {
			return true;
		}
		return false;
	}
	
	public function getComment()
	{
		$q = $this->db->get('comment'); 
		  if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;
	}
	
	public function getChartData() 
	{
		$myQuery = "SELECT S.month, COALESCE(S.sales, 0) as sales, COALESCE( P.purchases, 0 ) as purchases, COALESCE(S.tax1, 0) as tax1, COALESCE(S.tax2, 0) as tax2, COALESCE( P.ptax, 0 ) as ptax FROM ( SELECT date_format(date, '%Y-%m') Month, SUM(sales.total) Sales, SUM(total_tax) tax1, SUM(total_tax2) tax2 FROM sales WHERE sales.date >= date_sub( now( ) , INTERVAL 6 MONTH ) GROUP BY date_format(date, '%Y-%m')) S LEFT JOIN ( SELECT date_format(mrr_date, '%Y-%m') Month, SUM(tax_val) ptax, SUM(inv_val) purchases FROM make_mrr GROUP BY date_format(mrr_date, '%Y-%m')) P ON S.Month = P.Month GROUP BY S.Month ORDER BY S.Month";
        $q = $this->db->query($myQuery);
		if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
				
			return $data;
		}
	}
	
	public function getStockValue() 
	{
		$q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select COALESCE(sum(warehouses_products.quantity), 0)*price as by_price, COALESCE(sum(warehouses_products.quantity), 0)*cost as by_cost FROM products JOIN warehouses_products ON warehouses_products.product_id=products.id GROUP BY products.id )a");
		 if( $q->num_rows() > 0 )
		  {
			return $q->row();
		  } 
		
		  return FALSE;
	}
	
	public function topProducts()
	{
	//	$m = date('Y-m');
	//	$this->db->select('product_code, product_name, sum(quantity) as quantity')->order_by('sum(quantity)', 'desc')->limit(10)->group_by('product_code')
	//	->join('sales', 'sales.id=sale_items.sale_id', 'left')->like('sales.date', $m, 'both');
	//	$q = $this->db->get('sale_items');
	//	if($q->num_rows() > 0) {
	//		foreach (($q->result()) as $row) {
	//			$data[] = $row;
	//		}
				
	//		return $data;
	//	}
	//}

        $q=$this->db->query("SELECT sale_items.product_code as code, sale_items.product_name as name, sum(sale_items.quantity*sale_items.unit_price) as val
						FROM sales inner join sale_items on sales.id=sale_items.sale_id
                        WHERE sales.date between '".$this->firstDay()."' and '".date('Y-m-d')."'
						GROUP BY sale_items.product_id order by val DESC LIMIT 10");

//	$m = date('Y-m');
//		$this->db->select('categories.id, categories.name, sum(sale_items.quantity*sale_items.unit_price) as quantity');
//		$this->db->from('sale_items');
//        $this->db->join('sales', 'sales.id=sale_items.sale_id', 'left');
//        $this->db->join('products', 'sale_items.product_code=products.code', 'left');
////        $this->db->join('categories', 'products.category_id=categories.id', 'left');
//        $this->db->like('sales.date', $m, 'both');
//        $this->db->order_by('sum(sale_items.quantity)', 'desc');
//        $this->db->limit(10);
//        $this->db->group_by('categories.name');
//		$q = $this->db->get();
//		if($q->num_rows() > 0) {
//			foreach (($q->result()) as $row) {
//				$data[] = $row;
//			}
//
//			return $data;
//		}


        if($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}

			return $data;
		}
//        if( $q->num_rows() > 0 )
//        {
//            return $q->row();
//        }

        return FALSE;
}


    public function topCategory()
    {


        $q=$this->db->query("SELECT categories.name as code, sum(sale_items.quantity*sale_items.unit_price) as val FROM sales inner join sale_items on sales.id=sale_items.sale_id inner join products on sale_items.product_code=products.code INNER join categories on products.category_id=categories.id WHERE sales.date between '".$this->firstDay()."' and '".date('Y-m-d')."' GROUP BY products.category_id order by val DESC LIMIT 10");


        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }


    public function topCategoryMargin()
    {


        $q=$this->db->query("SELECT categories.name as code, (sum(sale_items.quantity*sale_items.unit_price)- sum(sale_items.quantity*products.cost) ) as val
						FROM sales inner join sale_items on sales.id=sale_items.sale_id inner join products on sale_items.product_code=products.code INNER join categories on products.category_id=categories.id
                        WHERE sales.date between '".$this->firstDay()."' and '".date('Y-m-d')."'
						GROUP BY products.category_id order by val DESC LIMIT 10");


        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getMonthlySaleMargin()
    {
        $myQuery = "SELECT S.month,
                       COALESCE(S.sales, 0) as sales,
                       COALESCE( P.purchases, 0 ) as purchases,
                       COALESCE(S.tax1, 0) as tax1,
                       COALESCE(S.tax2, 0) as tax2,
                       COALESCE( P.ptax, 0 ) as ptax
                    FROM (  SELECT  date_format(date, '%Y-%m') Month,
                                (SUM(sale_items.quantity*sale_items.unit_price) -SUM(sale_items.quantity*products.cost)) Sales,
                                SUM(total_tax) tax1,
                                SUM(total_tax2) tax2
                        FROM sales inner join sale_items on sales.id=sale_items.sale_id inner join products on products.code=sale_items.product_code
                        WHERE sales.date >= date_sub( now( ) , INTERVAL 6 MONTH )
                        GROUP BY date_format(date, '%Y-%m')) S
                    LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                                    SUM(total_tax) ptax,
                                    SUM(total) purchases
                            FROM purchases
                            GROUP BY date_format(date, '%Y-%m')) P
                    ON S.Month = P.Month
                    GROUP BY S.Month
                    ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }



    function firstDay($month = '', $year = '')
    {
        if (empty($month)) {
            $month = date('m');
        }
        if (empty($year)) {
            $year = date('Y');
        }
        $result = strtotime("{$year}-{$month}-01");
        return date('Y-m-d', $result);
    }

    public function topNonMovementCategory()
    {


        $q=$this->db->query("select sum(va) as val, name from (select categories.name,products.name as n,products.quantity, products.cost, (products.quantity * products.cost) as va from products inner join categories on products.category_id=categories.id where products.quantity!=0 and products.id NOT IN (select product_id from sale_items)) as a GROUP by name order by val DESC LIMIT 10");


        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function topPurchaseCategory()
    {


        $q=$this->db->query("select a.name, sum(a.val) as val from (select categories.name,make_mrr.purchase_item_name, make_mrr.received_qty,products.price, make_mrr.inv_val as val from make_mrr inner join products on make_mrr.purchase_item_id=products.id  INNER join categories on products.category_id=categories.id where mrr_date between '".$this->firstDay()."' and '".date('Y-m-d')."') as a GROUP by a.name order by a.val DESC LIMIT 10");

        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

}

/* End of file home_model.php */ 
/* Location: ./sma/modules/home/models/home_model.php */
