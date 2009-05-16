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
	
		$this->get_verify_process_input();
	
		//$this->get_input(); 
		//$this->verify_input();
		//$this->process_input();
		
		$this->show_output();
	}
	
	private function get_verify_process_input()
	{
		//get action
		$this->f_action = IO::get_input_sl_pg('f_action','string');

		
		//get id of sales order to edit either from GET or from shopping cart		
		$this->f_id = IO::get_input_sl_pg('f_id','integer');
		$this->m_sales_order = new SalesOrder($this->f_id);
		//sales order has final word on what order this is, might have defaulted to the shopping cart if f_id was bogus
		$this->f_id = $this->m_sales_order->get_id();
	
	
		//cart actions, after any of these actions, page is redisplayed unless an IO::navigate_to command was issued
		if($this->m_sales_order->is_cart())
		{
			if($this->f_action == 'empty')
			{
				$this->get_save_restore();	
				$this->m_sales_order->remove_all_components();
			}
			else if($this->f_action == 'reset')
			{	
				$this->m_sales_order->reset();
				$this->get_input_from_db();
			}
			else if($this->f_action == 'save')
			{	
				$this->get_save_restore();
			}
			else if($this->f_action == 'finish')
			{	
				$this->get_input_from_form();
				$this->action_save();
				$this->m_sales_order->become_order();
				IO::navigate_to('page_sales_order_view.php?f_id=' . $this->f_id);				
			}
			else if($this->f_action == 'saveredirect')
			{
				$this->get_input_from_form();
				$this->action_save();	
				//TODO: IO::navigate_to('page_sales_order_view.php?f_id=' . $this->f_id);				
			}
			else if($this->f_action == 'removecomp')
			{
				$this->get_save_restore();			
				//TODO
			}
			//just display
			else
			{
				$this->get_input_from_db();
			}
		}
		//existing order actions
		else
		{
			if($this->f_action == 'save')
			{	
				$this->get_save_restore();
			}
			else if($this->f_action == 'saveredirect')
			{
				$this->get_input_from_form();
				$this->action_save();
				//TODO: IO::navigate_to('page_sales_order_view.php?f_id=' . $this->f_id);	
			}
			else if($this->f_action == 'removecomp')
			{
				$this->get_save_restore();
				//TODO
			}			
			//just display			
			else
			{
				$this->get_input_from_db();
			}			
		}
	}
	
	private function get_save_restore()
	{
		$this->get_input_from_form();
		$this->action_save();
		$this->get_input_from_db();		
	}
	
	private function get_input_from_form()
	{
		$this->f_icode 				= IO::get_input_sl_pg('f_icode','string');
		$this->f_customer_id 		= IO::get_input_sl_pg('f_customer_id','integer');
		$this->f_shipper_id 		= IO::get_input_sl_pg('f_shipper_id','integer');
		$this->f_shipment_details 	= IO::get_input_ml_pg('f_shipment_details','string');
		$this->f_special 			= IO::get_input_sl_pg('f_special','integer');
		$this->f_order_date 		= IO::get_input_sl_pg('f_order_date','string'); //date("Y-m-d H:i:s",strtotime(IO::get_input_sl_pg('f_order_date','string')));
		$this->f_delivery_date 		= IO::get_input_sl_pg('f_delivery_date','string'); //date("Y-m-d H:i:s",strtotime(IO::get_input_sl_pg('f_delivery_date','string')));
		$this->f_price 				= IO::get_input_sl_pg('f_price','float');
		$this->f_currency 			= IO::get_input_sl_pg('f_currency','string');
		$this->f_notes 				= IO::get_input_ml_pg('f_notes','string');		
	}

	private function get_input_from_db()
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
		
		$this->m_customer_text = $sales_order_info['customer_icode'] . ' : ' . $sales_order_info['customer_company_name'];
		$this->m_shipper_text = $sales_order_info['shipper_icode'] . ' : ' . $sales_order_info['shipper_company_name'];			
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function action_save()
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
		$the_order_date = ($this->f_order_date == '' ? 'NOW()' : '\'' . $this->f_order_date . '\'');
		$the_delivery_date = ($this->f_delivery_date == '' ? 'NOW()' : '\'' . $this->f_delivery_date . '\'');
		
		//echo 'here:' . $this->f_special;
		//$this->f_special == ('on' ?  1 : 0);
		
		DB::send_query('
		UPDATE sales_orders SET
		icode=\'' . $this->f_icode . '\',
		customer_id=\'' . $this->f_customer_id . '\',
		shipper_id=\'' . $this->f_shipper_id . '\',
		shipment_details=\'' . $this->f_shipment_details . '\',
		special=\'' . $this->f_special . '\',
		order_date=' . $the_order_date . ',
		delivery_date=' . $the_delivery_date . ',
		price=\'' . $this->f_price . '\',
		currency=\'' . $this->f_currency . '\',
		notes=\'' . $this->f_notes . '\',
		updated_date=NOW(),
		updated_employee_id=\'' . LoginManager::get_id() . '\',
		search_words=\'' . $search_words . '\'
		WHERE id=\'' . $this->f_id . '\'
		');
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SALES_ORDERS);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<form name="form_sales_order" method="post" action="page_sales_order_add_edit.php">
		<input name="f_action" type="hidden" value="">
		<input name="f_id" type="hidden" value="">
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
				<td class="text_label" width="25%">Order ID Code: </td>
				<td class="form_input"><input type="text" name="f_icode" class="textbox" value="' . IO::prepout_sl($this->f_icode, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Customer: </td>
				<td class="form_input">
					<input type="text" size="48" disabled="true" name="f_customer_text" class="textbox" value="' . $this->m_customer_text . '">
					<input type="hidden" name="f_customer_id" class="textbox" value="' . IO::prepout_sl($this->f_customer_id, false) . '">
					<input type="button" value="Select New" onclick="document.location=\'page_customer_list.php?f_mode=select\'">
				</td>
			
			</tr>
			
			<tr>
				<td class="text_label">Shipper: </td>
				<td class="form_input">
					<input type="text" size="48" disabled="true" name="f_shipper_text" class="textbox" value="' . $this->m_shipper_text . '">
					<input type="hidden" name="f_shipper_id" class="textbox" value="' . IO::prepout_sl($this->f_shipper_id, false) . '">
					<input type="button" value="Select New" onclick="document.location=\'page_shipper_list.php?f_mode=select\'">
				</td>
			</tr>
			
			<tr>
				<td class="text_label">Shipment Details: </td>
				<td class="form_input"><textarea name="f_shipment_details" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_shipment_details) . '</textarea></td>
			</tr>
			
			<tr>
				<td class="text_label">Special: </td>
				<td class="form_input"><input type="checkbox" name="f_special" value="1"
			');
				if($this->f_special != 0)
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
		
		//sales order component title
		echo ('
		<table width="100%">
			<tr>
				<td width="25%"></td>
				<td align="" class="text_title">
					<br>
					Sales Order Contents:
				</td>
			</tr>
		</table>
		');
		
		//sales order component list
		$obj_sales_order_comp_list = new ObjSalesOrderCompList();
		$obj_sales_order_comp_list->display('delete', $this->m_comp_info_arr);
		
		//buttons if is cart
		if( $this->m_sales_order->is_cart() )
		{		
			echo('
			<br><br><br>
			<table width="100%">
				<tr>
					<td align="left">
						<input type="submit" name="submit" value="Empty Contents" class="button" onclick="form_sales_order.f_action.value=\'empty\'; form_sales_order.f_id.value=\'' . $this->f_id . '\';">
						<br>
						<input type="submit" name="submit" value="Reset All" class="button" onclick="form_sales_order.f_action.value=\'reset\'; form_sales_order.f_id.value=\'' . $this->f_id . '\';">
					</td>					
					<td align="right">
						<input type="submit" name="submit" value="Save Progress" class="button" onclick="form_sales_order.f_action.value=\'save\'; form_sales_order.f_id.value=\'' . $this->f_id . '\';">
						<br>
						<input type="submit" name="submit" value="Complete Order" class="button" onclick="form_sales_order.f_action.value=\'finish\'; form_sales_order.f_id.value=\'' . $this->f_id . '\';">
					</td>
				</tr>
			</table>
			</form>
			');
		}
		//buttons if is existing sales order
		else
		{
			echo('
			<br><br><br>
			<table width="100%">
				<tr>
					<td align="left">
						<input type="submit" name="submit" value="Delete Order" onclick="document.location=\'page_sales_order_add_edit.php?f_action=submit&f_mode=delete&f_id=' . IO::prepout_url($this->f_id) . '\'">
					</td>					
					<td align="right">
						<input type="submit" name="submit" value="Save Changes" class="button" onclick="form_sales_order.f_action.value=\'save\'; form_sales_order.f_id.value=\'' . $this->f_id . '\';">
					</td>
				</tr>
			</table>
			</form>
			');
		}		
		
		//outer area bottom
		ObjOuterArea::echo_bottom(false);
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_sales_order_add_edit = new PageSalesOrderAddEdit();
$page_sales_order_add_edit->run();

?>
