<?php

require_once('framework.php');

class PageSalesOrderView
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_sales_order_view.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_customer_id;
	private $f_shipper_id;
	private $f_shipment_details;
	private $f_special;
	private $f_order_date;
	private $f_delivery_date;
	private $f_price;
	private $f_currency;
	private $f_notes;
	
	private $f_shipper_comp_name;
	private $f_shipper_cont_name;	
	private $f_customer_comp_name;
	private $f_customer_cont_name;
	
	private $f_created_first;
	private $f_created_last;
	private $f_updated_first;
	private $f_updated_last;
	
	private $f_created_date;
	private $f_updated_date;
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->get_output();
		
		$this->show_output();
	}
	
	private function get_input()
	{
		$this->f_id = IO::get_input_sl_g('f_id', 'string');
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function get_output()
	{
					//get values from database
			$sales_order_info = DB::get_single_row_fq('
				SELECT icode, customer_id, shipper_id, shipment_details, special, order_date, delivery_date, price, currency, notes, created_employee_id, updated_employee_id, created_date, updated_date, trash_flag
				FROM sales_orders WHERE id=\'' . $this->f_id . '\''
			);
			
			$this->f_icode 					= $sales_order_info['icode'];
			$this->f_customer_id 			= $sales_order_info['customer_id'];
			$this->f_shipper_id 			= $sales_order_info['shipper_id'];
			$this->f_shipment_details 		= $sales_order_info['shipment_details'];
			$this->f_special 				= $sales_order_info['special'];
			$this->f_order_date 			= date("j. M Y", strtotime($sales_order_info['order_date']));
			$this->f_delivery_date 			= date("j. M Y", strtotime($sales_order_info['delivery_date']));
			$this->f_price 					= $sales_order_info['price'];
			$this->f_currency 				= $sales_order_info['currency'];
			$this->f_notes 					= $sales_order_info['notes'];
			$this->f_created_employee_id	= $sales_order_info['created_employee_id'];
			$this->f_updated_employee_id	= $sales_order_info['updated_employee_id'];
			$this->f_created_date			= date("j. M Y", strtotime($sales_order_info['created_date']));
			$this->f_updated_date			= date("j. M Y", strtotime($sales_order_info['updated_date']));
			$this->f_trash_flag				= $sales_order_info['trash_flag'];
			
			//QUERY for created by user info
			$sales_info_cre = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM employees
				WHERE employees.id=\'' . $this->f_created_employee_id . '\''
			);
			$this->f_created_first 		= $sales_info_cre['first_name'];
			$this->f_created_last 		= $sales_info_cre['last_name'];
			
			//QUERY for Updated by user info
			$sales_info_up = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM sales_orders 
				LEFT OUTER JOIN employees ON sales_orders.updated_employee_id = employees.id
				WHERE sales_orders.id=\'' . $this->f_id . '\''
			);

			$this->f_updated_first 		= $sales_info_up['first_name'];
			$this->f_updated_last 		= $sales_info_up['last_name'];
			
			//QUERY for order's Customer info
			$sales_cust = DB::get_single_row_fq
			('
				SELECT  customers.company_name,
						customers.contact_name
				FROM customers
				WHERE customers.id=\'' . $this->f_customer_id . '\''
			);
			$this->f_customer_comp_name	= $sales_cust['company_name'];
			$this->f_customer_cont_name	= $sales_cust['contact_name'];
			
			//QUERY for order's Shipper info
			$sales_ship = DB::get_single_row_fq
			('
				SELECT  shippers.company_name,
						shippers.contact_name
				FROM shippers
				WHERE shippers.id=\'' . $this->f_shipper_id . '\''
			);
			$this->f_shipper_comp_name	= $sales_ship['company_name'];
			$this->f_shipper_cont_name	= $sales_ship['contact_name'];	
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SALES_ORDERS);
		
		//echo inner area html
		echo ('
		<!-- Title of the page -->
		<form name="sales_order_add_edit" method="post" action="page_sales_order_add_edit.php?f_mode=edit&f_id=' . IO::prepout_url($this->f_id) . '">
		<table width="100%">
			<tr>
				<td width="25%"> </td>
				<td width="75%" class="text_title">
				View Sales Order
				</td>
			</tr>
		</table>
		');
			
		// see if the sales order has been deleted
		if( $this->f_trash_flag == '1' )
		{
			echo '<p>This sales order has been deleted.</p>';
		}
		
		// show details if it hasn't been deleted or if user is an admin
		if( $this->f_trash_flag == '0' or LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN) )
		{
			echo ('
			<!-- View fields of a sales order -->
			<table width="100%">
				<tr>
					<td class="text_label">ID Code: </td>
					<td class="form_input">' . IO::prepout_sl($this->f_icode, false) . '</td>
				</tr>
				
				<tr>
					<td class="text_label">Customer: </td>
					<td class="form_input"><a href="page_customer_view.php?f_id=' . IO::prepout_sl($this->f_customer_id, false) . '">' . IO::prepout_ml_html($this->f_customer_comp_name) . '</a></td>
				</tr>
				
				<tr>
					<td class="text_label">Shipper ID: </td>
					<td class="form_input"><a href="page_shipper_view.php?f_id=' . IO::prepout_sl($this->f_shipper_id, false) . '">' . IO::prepout_ml_html($this->f_shipper_comp_name) . '</a></td>
				</tr>
				
				<tr>
					<td class="text_label">Shipment Details: </td>
					<td class="form_input"><div>' . IO::prepout_ml_textarea($this->f_shipment_details) . '</div></td>
				</tr>
				
				<tr>
					<td class="text_label">Special: </td>
					<td class="form_input">');
					if( IO::prepout_sl($this->f_special, false) == '1')
					{
						echo 'Yes';
					}
					else
					{
						echo 'No';
					}
				echo ('</td>
				</tr>
				
				<tr>
					<td class="text_label">Order Date: </td>
					<td class="form_input">' . IO::prepout_sl($this->f_order_date, false) . '</td>
				</tr>
				
				<tr>
					<td class="text_label">Delivery Date: </td>
					<td class="form_input">' . IO::prepout_sl($this->f_delivery_date, false) . '</td>
				</tr>
				
				<tr>
					<td class="text_label">Price: </td>
					<td class="form_input">' . IO::prepout_sl($this->f_price, false) . '&nbsp;' . IO::prepout_sl($this->f_currency, false) .  '</td>
				</tr>
				
				<tr>
					<td class="text_label">Notes: </td>
					<td class="form_input"><div>' . IO::prepout_ml_textarea($this->f_notes) . '</div></td>
				</tr>
			');
			// the following details are only viewable by admins
			if( LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN ) )
			{
				echo ('
					<tr>
						<td class="text_label">Created Date: </td>
						<td class="form_input">' . IO::prepout_sl($this->f_created_date, false) . '</td>
					</tr>
					
					<tr>
						<td class="text_label">Updated Date: </td>
						<td class="form_input">' . IO::prepout_sl($this->f_updated_date, false) . '</td>
					</tr>
					
					<tr>
						<td class="text_label">Created By: </td>
						<td class="form_input"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_created_employee_id, false) . '">'  . IO::prepout_sl($this->f_created_first, false) . '&nbsp;' . IO::prepout_sl($this->f_created_last, false)  . '</a></td>
					</tr>
					
					<tr>
						<td class="text_label">Updated By: </td>
						<td class="form_input"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_updated_employee_id, false) . '">'  . IO::prepout_sl($this->f_updated_first, false) . '&nbsp;' . IO::prepout_sl($this->f_updated_last, false)  . '</a></td>
					</tr>
				');
			}
			echo ('
				<tr>
					<td class="text_label"> </td>
					<td><input type="submit" name="submit" value="Edit Sales Order"></td>
				</tr>
			</table>
			');
		}

		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_sales_order_view = new PageSalesOrderView();
$page_sales_order_view->run();

?>