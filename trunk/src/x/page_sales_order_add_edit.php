<?php

require_once('framework.php');

class PageSalesOrderAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_sales_order_add_edit.php';
	
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
		$this->f_mode = IO::get_input_sl_g('f_mode','string');	
		$this->f_action = IO::get_input_sl_g('f_action','string');
	
		//if submitting in ADD or EDIT mode, get fields from form
		if($this->f_action == 'submit')
		{
			//if submitting in EDIT mode, additionally get the sales order id to edit
			if($this->f_mode == 'edit' || $this->f_mode == 'delete')
			{
				//get id of sales order to edit
				$this->f_id = IO::get_input_sl_g('f_id','string');
			}
		
			$this->f_icode 				= IO::get_input_sl_p('f_icode','string');
			$this->f_customer_id 		= IO::get_input_sl_p('f_customer_id','integer');
			$this->f_shipper_id 		= IO::get_input_sl_p('f_shipper_id','integer');
			$this->f_shipment_details 	= IO::get_input_ml_p('f_shipment_details','string');
			$this->f_special 			= IO::get_input_sl_p('f_special','integer');
			$this->f_order_date 		= date("Y-m-d H:i:s",strtotime(IO::get_input_sl_p('f_order_date','string')));
			$this->f_delivery_date 		= date("Y-m-d H:i:s",strtotime(IO::get_input_sl_p('f_delivery_date','string')));
			$this->f_price 				= IO::get_input_sl_p('f_price','float');
			$this->f_currency 			= IO::get_input_sl_p('f_currency','string');
			$this->f_notes 				= IO::get_input_ml_p('f_notes','string');
		}
		//if NOT submitting, but in EDIT mode, fill the fields from database data
		else if($this->f_mode == 'edit')
		{
			//get id of customer to edit
			$this->f_id = IO::get_input_sl_g('f_id','integer');
		
			//get values from database
			$sales_order_info = DB::get_single_row_fq('
				SELECT icode, customer_id, shipper_id, shipment_details, special, order_date, delivery_date, price, currency, notes
				FROM sales_orders WHERE id=\'' . $this->f_id . '\''
			);
			
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
		//if NOT submitting, and in ADD mode, do nothing (empty textboxes)
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
				$this->f_special . ' ' .
				$this->f_order_date . ' ' .
				$this->f_delivery_date . ' ' .
				$this->f_price . ' ' .
				$this->f_currency . ' ' .
				$this->f_notes			
			);
		
			//edit mode submit
			if($this->f_mode == 'edit')
			{
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
			else if($this->f_mode == 'delete')
			{
				//update trash_flag
				DB::send_query('
				UPDATE sales_orders SET
				trash_flag = 1
				WHERE id=\'' . $this->f_id . '\'
				');
				echo '
				UPDATE sales_orders SET
				trash_flag = 1
				WHERE id=\'' . $this->f_id . '\'
				';
			}
			//add mode submit
			else
			{
				//insert
				DB::send_query('
				INSERT INTO sales_orders (
					icode, 
					customer_id, 
					shipper_id, 
					shipment_details, 
					special, 
					order_date, 
					delivery_date, 
					price, 
					currency, 
					notes, 
					created_employee_id, 
					updated_employee_id, 
					created_date, 
					updated_date, 
					search_words) 
				VALUES (
				\'' . $this->f_icode . '\',
				\'' . $this->f_customer_id . '\',
				\'' . $this->f_shipper_id . '\',
				\'' . $this->f_shipment_details . '\',
				\'' . $this->f_special . '\',
				\'' . $this->f_order_date . '\',
				\'' . $this->f_delivery_date . '\',
				\'' . $this->f_price . '\',
				\'' . $this->f_currency . '\',
				\'' . $this->f_notes . '\',
				\'' . LoginManager::get_id() . '\',
				\'' . LoginManager::get_id() . '\',
				NOW(),
				NOW(),
				\'' . $search_words . '\'
				)
				');
				
				//fetch the id of the new row
				$this->f_id = DB::get_field_fq('SELECT LAST_INSERT_ID()');
			}
			
			//successful insert or update
			IO::navigate_to('page_sales_order_view.php?f_id=' . $this->f_id);
		}
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
				<td class="text_label">ID Code: </td>
				<td class="form_input"><input type="text" name="f_icode" class="textbox" value="' . IO::prepout_sl($this->f_icode, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Customer ID: </td>
				<td class="form_input"><input type="text" name="f_customer_id" class="textbox" value="' . IO::prepout_sl($this->f_customer_id, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Shipper ID: </td>
				<td class="form_input"><input type="text" name="f_shipper_id" class="textbox" value="' . IO::prepout_sl($this->f_shipper_id, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Shipment Details: </td>
				<td class="form_input"><textarea name="f_shipment_details" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_shipment_details) . '</textarea></td>
			</tr>
			
			<tr>
				<td class="text_label">Special: </td>
				<td class="form_input"><input type="checkbox" name="f_special"');
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
		
		
		<table width="100%">
			<tr>
				<td class="text_label"> </td>
				<td><input type="submit" name="Submit" value="Save" class="button"></form></td>');
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
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_sales_order_add_edit = new PageSalesOrderAddEdit();
$page_sales_order_add_edit->run();

?>