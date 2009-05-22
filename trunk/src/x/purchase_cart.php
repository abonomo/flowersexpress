<?php

include_once('framework.php');

//1 sales purchase cart per employee
class Purchase
{
	public static $IN_WAREHOUSE_SEARCH_WORD = 'warehouse';	//word add to search_words field when purchase is special
	
	private $m_is_cart;
	private $m_id;	//sales purchase id (the cart's id)

	//if id is not taken in, defaults to shopping cart, otherwise edits an existing purchase
	public function __construct($the_id)
	{
		//existing purchase id passed in
		if($the_id != '')
		{
			//determine if cart or not from database
			$cart_info_res = DB::get_result_fq('
				SELECT is_cart
				FROM purchases
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
			FROM purchases
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
			//give the cart/purchase an employee "owner"
			DB::send_query('
				INSERT INTO purchases 
				(is_cart, created_employee_id) 
				SELECT 1, \'' . LoginManager::get_id() . '\'
				FROM DUAL
				WHERE NOT EXISTS
				(SELECT 1
				FROM purchases
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
	
	public function get_purchase_info()
	{
		return DB::get_single_row_fq('
			SELECT
			purchases.*,
			COALESCE(suppliers.icode, \'None\') AS supplier_icode,
			COALESCE(suppliers.company_name, \'None\') AS supplier_company_name,
			COALESCE(shippers.icode, \'None\') AS shipper_icode,
			COALESCE(shippers.company_name, \'None\') AS shipper_company_name
			FROM purchases
			LEFT OUTER JOIN suppliers ON purchases.supplier_id = suppliers.id
			LEFT OUTER JOIN shippers ON purchases.shipper_id = shippers.id
			WHERE purchases.id=\'' . $this->m_id . '\'
		');
	}

	//TODO: change
	public function add_component($purchase_comp_id, $quantity_ordered, $total_cost)
	{
		DB::send_query('
			INSERT INTO purchase_comps (
				purchase_id, 
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
	
	public function remove_component($purchase_comp_id)
	{
		DB::send_query('
			DELETE FROM purchase_comps 
			WHERE id=\'' . $purchase_comp_id . '\'
		');
	}
	
	public function remove_all_components()
	{
		DB::send_query('
			DELETE FROM purchase_comps
			WHERE purchase_id=\'' . $this->m_id . '\'
		');
	}
	
	public function reset()
	{
		//remove components	
		$this->remove_all_components();
		
		//reset sales purchase (or cart) to default and initial values
		//NOTE: is_cart=1 taken out because this could be editting an existing purchase
		DB::send_query('
			UPDATE purchases SET
			icode=DEFAULT,
			notes=DEFAULT,
			supplier_id=DEFAULT,
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
			FROM purchase_comps 
			WHERE purchase_id=\'' . $this->m_id . '\'
		');
	}
	
	public function become_purchase()
	{	
		//just change the is_cart flag from 1 to 0, and you have yourself an purchase, also fill in some additional fields automatically
		DB::send_query('
			UPDATE purchases SET
			is_cart=0,
			updated_employee_id=\'' . LoginManager::get_id() .'\',
			created_date=NOW(),
			updated_date=NOW()
			WHERE id=\'' . $this->m_id . '\'
		');		
	}
}

?>