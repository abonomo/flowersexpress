<?php

/*
	Description: Backend Shopping Cart for Sales Orders (and their Sales Order Components)
	Originally Written: Max Vujovic, May 8, 2009
	Revision Notes: -
*/

include_once('framework.php');

//1 sales order cart per employee
class SalesOrder
{
	public static $SPECIAL_SEARCH_WORD = 'special';	//word add to search_words field when order is special
	
	private $m_is_cart;
	private $m_id;	//sales order id (the cart's id)

	//if id is not taken in, defaults to shopping cart, otherwise edits an existing order
	public function __construct($the_id)
	{
		//existing order id passed in
		if($the_id != '')
		{
			//determine if cart or not from database
			$cart_info_res = DB::get_result_fq('
				SELECT is_cart
				FROM sales_orders
				WHERE id=\'' . $the_id . '\'
			');			
			
			//id not found, so instead load the shopping cart
			if(!DB::is_unique_result($cart_info_res))
			{
				$this->load_cart();
			}
			//id and is_cart found
			else
			{
				//store is_cart status and id
				$this->m_is_cart = (DB::get_field_fr($cart_info_res) != 0);
				$this->m_id = $the_id;
			}
		}
		//is shopping cart
		else
		{
			$this->load_cart();
		}
	}
	
	private function load_cart()
	{
		$this->m_is_cart = true;
		
		//make a new cart or fetch the old one associated with this employee
		$cart_info = DB::get_all_rows_fq('
			SELECT id
			FROM sales_orders
			WHERE created_employee_id=\'' . LoginManager::get_id() . '\'
			AND is_cart=1
		');

		//cart exists (should only be one, maybe more someday)
		if(count($cart_info) >= 1)
		{
			$this->m_id = $cart_info[0]['id'];
		}
		//no cart exists, so make one
		else
		{
			//insert a new cart only if there isn't one already (check again within query to avoid concurrency issues)
			//give the cart/order an employee "owner"
			DB::send_query('
				INSERT INTO sales_orders 
				(is_cart, created_employee_id) 
				SELECT 1, \'' . LoginManager::get_id() . '\'
				FROM DUAL
				WHERE NOT EXISTS
				(SELECT 1
				FROM sales_orders
				WHERE
				created_employee_id=\'' . LoginManager::get_id() . '\' 
				AND is_cart=1)
			');
			
			//if the query didn't do anything, something screwy is going on, go to error
			if(mysql_affected_rows() <= 0)
			{
				die('Error: Concurrent cart creation detected.');
			}
			
			//get the newly inserted cart id
			$this->m_id = DB::get_field_fq('SELECT LAST_INSERT_ID()');
		}	
	}
	
	public function get_id()
	{
		return $this->m_id;
	}
	
	public function is_cart()
	{
		return $this->m_is_cart;
	}
	
	public function get_all_components()
	{
		//decide here what fields per sales order component you want to get back to display them
		//LEFT OUTER JOIN used so that rows show up even if there the corresponding product or purchase component got deleted
		//missing field values will be MySQL NULL type, use COALESCE to make them into something usable in PHP
		return DB::get_all_rows_fq('
			SELECT
			sales_order_comps.id,
			sales_order_comps.quantity_ordered, 
			sales_order_comps.total_cost,
			purchases.icode,
			suppliers.icode,
			suppliers.company_name,
			products.icode,
			products.name
			FROM sales_order_comps 
			LEFT OUTER JOIN purchase_comps ON sales_order_comps.purchase_comp_id = purchase_comps.id
			LEFT OUTER JOIN purchases ON purchase_comps.purchase_id = purchases.id
			LEFT OUTER JOIN products ON purchase_comps.product_id = products.id
			LEFT OUTER JOIN suppliers ON purchases.supplier_id = suppliers.id
			WHERE sales_orders_comps.sales_order_id=\'' . $this->m_id . '\'
		');
	}
	
	public function get_order_info()
	{
		return DB::get_single_row_fq('
			SELECT
			sales_orders.*,
			COALESCE(customers.icode, \'None\') AS customer_icode,
			COALESCE(customers.company_name, \'None\') AS customer_company_name,
			COALESCE(shippers.icode, \'None\') AS shipper_icode,
			COALESCE(shippers.company_name, \'None\') AS shipper_company_name
			FROM sales_orders
			LEFT OUTER JOIN customers ON sales_orders.customer_id = customers.id
			LEFT OUTER JOIN shippers ON sales_orders.shipper_id = shippers.id
			WHERE sales_orders.id=\'' . $this->m_id . '\'
		');
	}

	public function add_component($purchase_comp_id, $quantity_ordered, $total_cost)
	{
		DB::send_query('
			INSERT INTO sales_order_comps (
				sales_order_id, 
				purchase_comp_id, 
				quantity_ordered, 
				total_cost
			)
			VALUES (
				\'' . $this->m_id . '\',
				\'' . $purchase_comp_id . '\',
				\'' . $quantity_ordered . '\',
				\'' . $total_cost . '\'
			)
		');
	}
	
	public function remove_component($sales_order_comp_id)
	{
		DB::send_query('
			DELETE FROM sales_order_comps 
			WHERE id=\'' . $sales_order_comp_id . '\'
		');
	}
	
	public function remove_all_components()
	{
		DB::send_query('
			DELETE FROM sales_order_comps
			WHERE sales_order_id=\'' . $this->m_id . '\'
		');
	}
	
	public function reset()
	{
		//remove components	
		$this->remove_all_components();
		
		//reset sales order (or cart) to default and initial values
		//NOTE: is_cart=1 taken out because this could be editting an existing order
		DB::send_query('
			UPDATE sales_orders SET
			icode=DEFAULT,
			notes=DEFAULT,
			customer_id=DEFAULT,
			shipper_id=DEFAULT,
			shipment_details=DEFAULT,
			special=DEFAULT,
			order_date=NOW(),
			delivery_date=NOW(),
			price=DEFAULT,
			currency=DEFAULT,
			created_employee_id=\'' . LoginManager::get_id() . '\',
			updated_employee_id=DEFAULT,
			created_date=NOW(),
			updated_date=NOW(),
			search_words=DEFAULT,
			trash_flag=DEFAULT
			WHERE id=\'' . $this->m_id . '\'
		');
	}
	
	//has no components?
	public function is_empty()
	{
		return DB::get_field_fq('
			SELECT (COUNT(*) <= 0) 
			FROM sales_order_comps 
			WHERE sales_order_id=\'' . $this->m_id . '\'
		');
	}
	
	public function become_order()
	{
		/*
		//get the all of the fields for making the search words field
		$order_info = $this->get_order_info();
	
		//generate the search words field (real searches should use this field and also corresponding customer, etc. table joined search words fields)
		$search_words = DB::encode_small_words_store(
			$order_info['icode'] . ' ' .
			$order_info['notes'] . ' ' .
			$order_info['shipment_details'] . ' ' .
			(($order_info['special'] != 0) ? self::$SPECIAL_SEARCH_WORD : '') . ' ' .
			$order_info['price'] . ' ' .
			$order_info['currency']
		);
		
	
		//just change the is_cart flag from 1 to 0, and you have yourself an order, also fill in some additional fields automatically
		DB::send_query('
			UPDATE sales_orders SET
			is_cart=0,
			updated_employee_id=\'' . LoginManager::get_id() .'\',
			created_date=NOW(),
			updated_date=NOW(),
			search_words=\'' . $search_words .'\'
			WHERE id=\'' . $this->m_id . '\'
		');
		*/
		
		//just change the is_cart flag from 1 to 0, and you have yourself an order, also fill in some additional fields automatically
		DB::send_query('
			UPDATE sales_orders SET
			is_cart=0,
			updated_employee_id=\'' . LoginManager::get_id() .'\',
			created_date=NOW(),
			updated_date=NOW()
			WHERE id=\'' . $this->m_id . '\'
		');		
	}
}



/*
//*** TESTING/EXAMPLE USAGE... ***

session_start();

DB::connect();

LoginManager::assert_auth_level(LoginManager::$AUTH_READ_WRITE);

$soc = new SalesOrderCart();
//$soc->add_component(2, 40, 580.49);
//$soc->remove_component(6);
//$components = $soc->get_all_components();
//echo serialize($components);
//$soc->become_order();
//echo $soc->is_empty();
//$soc->reset();
//echo $soc->is_empty();
//echo serialize($soc->get_order_info());
*/

?>