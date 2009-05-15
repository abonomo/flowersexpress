<?php

//TODO: a 'saveandredirect' mode (that doesn't try to make this an order if its a cart, and then redirects)

//NOTES:
//technically, we're always in EDIT mode, so there is no "f_mode=edit" necessary
//we're either editting the user's shopping cart if no f_id was passed in
//or we're editting an order if an f_id was passed in, but still through the shopping cart class
//the shopping cart class takes care of the creation of a shopping cart (an order with is_cart=1)

require_once('framework.php');
require_once('obj_comp_lists.php');
require_once('sales_order_cart.php');

class PageSalesOrderAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_sales_order_add_edit.php';
	
	//*** MEMBERS ***
	private $m_comp_info_arr;
	private $m_sales_order;
		
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
	
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_WRITE);
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output();
	}
	
	private function get_input()
	{
		//get action
		$this->f_action = IO::get_input_sl_g('f_action','string');

		//get id of sales order to edit either from GET or from shopping cart		
		if(IO::input_exists_g('f_id'))
		{
			$this->f_id = IO::get_input_sl_g('f_id','integer');
			$this->m_sales_order = new SalesOrder($this->f_id);
		}
		else
		{
			$this->m_sales_order = new SalesOrder();
		}
	
		//if submitting in ADD or EDIT mode, get fields from form
		if($this->f_action == 'submit')
		{
			$this->f_icode 				= IO::get_input_sl_pg('f_icode','string');
			$this->f_customer_id 		= IO::get_input_sl_pg('f_customer_id','integer');
			$this->f_shipper_id 		= IO::get_input_sl_pg('f_shipper_id','integer');
			$this->f_shipment_details 	= IO::get_input_ml_pg('f_shipment_details','string');
			$this->f_special 			= IO::get_input_sl_pg('f_special','integer');
			$this->f_order_date 		= date("Y-m-d H:i:s",strtotime(IO::get_input_sl_pg('f_order_date','string')));
			$this->f_delivery_date 		= date("Y-m-d H:i:s",strtotime(IO::get_input_sl_pg('f_delivery_date','string')));
			$this->f_price 				= IO::get_input_sl_pg('f_price','float');
			$this->f_currency 			= IO::get_input_sl_pg('f_currency','string');
			$this->f_notes 				= IO::get_input_ml_pg('f_notes','string');
		}
		//if NOT submitting fill the fields from database data
		else
		{	
			//get values from database
			$sales_order_info = $this->m_sales_order->get_order_info();
			
			$this->f_icode 				= $sales_order_info['icode'];
			$this->f_customer_id 		= $sales_order_info['customer_id'];
			$this->f_shipper_id 		= $sales_order_info['shipper_id'];
			$this->f_shipment_details 	= $sales_order_info['shipment_details'];
			$this->f_special 			= $sales_order_info['special'];
			$this->f_order_date 		= $sales_order_info['order_date'];
			$this->f_delivery_date 		= $sales_order_info['delivery_date'];
			$this->f_price 				= $sales_order_info['price'];
			$this->f_currency 			= $sales_order_info['currency'];
			$this->f_notes 				= $sales_order_info['notes'];
		}
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function process_input()
	{
		if($this->f_action == 'submit')
		{
			//make the search words field
			$search_words = DB::encode_small_words_store(
				$this->f_icode . ' ' .
				$this->f_customer_id . ' ' .
				$this->f_shipper_id . ' ' .
				$this->f_shipment_details . ' ' .
				(($this->f_special != 0) ? SalesOrder::$SPECIAL_SEARCH_WORD : '') . ' ' .
				$this->f_order_date . ' ' .
				$this->f_delivery_date . ' ' .
				$this->f_price . ' ' .
				$this->f_currency . ' ' .
				$this->f_notes			
			);
		
			//update database
			DB::send_query('
			UPDATE sales_orders SET
			icode=\'' . $this->f_icode . '\',
			customer_id=\'' . $this->f_customer_id . '\',
			shipper_id=\'' . $this->f_shipper_id . '\',
			shipment_details=\'' . $this->f_shipment_details . '\',
			special=\'' . $this->f_special . '\',
			order_date=\'' . $this->f_order_date . '\',
			delivery_date=\'' . $this->f_delivery_date . '\',
			price=\'' . $this->f_address_line_1 . '\',
			currency=\'' . $this->f_currency . '\',
			notes=\'' . $this->f_notes . '\',
			updated_date=NOW(),
			updated_employee_id=\'' . LoginManager::get_id() . '\',
			search_words=\'' . $search_words . '\'
			WHERE id=\'' . $this->f_id . '\'
			');
		}
		
		//become sales order if cart
		if($this->m_sales_order->is_cart())
		{
			$this->m_sales_order->become_order();
			$this->f_id = $this->m_sales_order->get_id();
		}

		//successful insert or update
		IO::navigate_to('page_sales_order_view.php?f_id=' . $this->f_id);		
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SALES_ORDERS);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<form name="sales_order_add_edit" method="post" action="page_sales_order_add_edit.php?f_action=submit&f_mode=' . IO::prepout_url($this->f_mode) . (($this->f_mode == 'edit') ? ('&f_id=' . IO::prepout_url($this->f_id)) : '') . '">
		<table width="100%">
			<tr>
				<td width="25%"> </td>
				<td width="75%" class="text_title">
				Add New Sales Order
				</td>
			</tr>
		</table>
		');

		if($err_msg != '')
		{
			echo('
			<!-- Error message -->
			<table width="100%">
				<tr>
					<td class="text_label"></td>
					<td class="text_error">' . $err_msg . '</td>
				</tr>
			</table>
			');
		}
		
		echo ('
		<!-- Input form fields -->
		<table width="100%">
			<tr>
				<td class="text_label" width="25%">ID Code: </td>
				<td class="form_input"><input type="text" name="f_icode" class="textbox" value="' . IO::prepout_sl($this->f_icode, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Customer: </td>
				<td class="form_input">
					<input type="text" disabled="true" name="f_customer_text" class="textbox" value="' . IO::prepout_sl($this->f_customer_text, false) . '">
					<input type="hidden" name="f_customer_id" class="textbox" value="' . IO::prepout_sl($this->f_customer_id, false) . '">
					<input type="button" value="Find" onclick="document.location=\'page_customer_list.php?f_mode=select\'">
				</td>
			
			</tr>
			
			<tr>
				<td class="text_label">Shipper: </td>
				<td class="form_input">
					<input type="text" disabled="true" name="f_shipper_text" class="textbox" value="' . IO::prepout_sl($this->f_shipper_text, false) . '">
					<input type="hidden" name="f_shipper_id" class="textbox" value="' . IO::prepout_sl($this->f_shipper_id, false) . '">
					<input type="button" value="Find" onclick="document.location=\'page_shipper_list.php?f_mode=select\'">
				</td>
			</tr>
			
			<tr>
				<td class="text_label">Shipment Details: </td>
				<td class="form_input"><textarea name="f_shipment_details" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_shipment_details) . '</textarea></td>
			</tr>
			
			<tr>
				<td class="text_label">Special: </td>
				<td class="form_input"><input type="checkbox" name="f_special"
			');
				if( IO::prepout_sl($this->f_special, false) == '1')
				{
					echo ' checked';
				}
			echo ('></td>
			</tr>
			
			<tr>
				<td class="text_label">Order Date: </td>
				<td class="form_input"><input type="text" name="f_order_date" class="textbox" value="' . IO::prepout_sl($this->f_order_date, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Delivery Date: </td>
				<td class="form_input"><input type="text" name="f_delivery_date" class="textbox" value="' . IO::prepout_sl($this->f_delivery_date, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Price: </td>
				<td class="form_input"><input type="text" name="f_price" class="textbox" value="' . IO::prepout_sl($this->f_price, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Currency: </td>
				<td class="form_input"><input type="text" name="f_currency" class="textbox" value="' . IO::prepout_sl($this->f_currency, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Notes: </td>
				<td class="form_input"><textarea name="f_notes" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_notes) . '</textarea></td>
			</tr>

		</table>
		');
		
		//buttons
		echo('
		<table width="100%">
			<tr>
				<td class="text_label" width="25%"> </td>
				<td><input type="submit" name="Submit" value="Finish Order" class="button"></form></td>
		');
		
		if( $this->f_mode == 'edit' )
		{
			echo ('
				<td align="right">
					<form name="sales_order_delete" method="post" action="page_sales_order_add_edit.php?f_action=submit&f_mode=delete&f_id=' . IO::prepout_url($this->f_id) . '">
					<input type="submit" name="submit" value="Delete Order">
					</form>
				</td>');
		}
		
		echo ('
			</tr>
		</table>
		');
		
		//sales order component title
		echo ('
		<table width="100%">
			<tr>
				<td width="25%"></td>
				<td align="" class="text_title"><br>
					Sales Order Contents:
				</td>
			</tr>
		</table>
		');
		
		//sales order component list
		$obj_sales_order_comp_list = new ObjSalesOrderCompList();
		$obj_sales_order_comp_list->display('delete', $this->m_comp_info_arr);
		
		//outer area bottom
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_sales_order_add_edit = new PageSalesOrderAddEdit();
$page_sales_order_add_edit->run();

?>
