<?php

//NOTES:
//technically, we're always in EDIT mode, so there is no "f_mode=edit" necessary
//we're either editting the user's shopping cart if no f_id was passed in
//or we're editting an purchase if an f_id was passed in, but still through the shopping cart class
//the shopping cart class takes care of the creation of a shopping cart (an purchase with is_cart=1)

require_once('framework.php');
require_once('obj_comp_lists.php');
require_once('purchase_cart.php');
require_once('our_time.php');

class PagePurchaseAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_purchase_add_edit.php';
	
	//*** MEMBERS ***
	private $m_comp_info_arr;
	private $m_purchase;
	private $obj_purchase_comp_list;
		
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_supplier_id;
	private $f_shipper_id;
	private $f_shipment_details;
	private $f_in_warehouse;
	private $f_order_date;
	private $f_delivery_date;
	private $f_price;
	private $f_notes;
	
	private $f_comp_id;
	private $f_quantity;
	private $f_total_cost;	
	
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

		
		//get id of purchase to edit either from GET or from shopping cart		
		$this->f_id = IO::get_input_sl_pg('f_id','integer');
		$this->m_purchase = new Purchase($this->f_id);
		//purchase has final word on what purchase this is, might have defaulted to the shopping cart if f_id was bogus
		$this->f_id = $this->m_purchase->get_id();
	
	
		//cart actions, after any of these actions, page is redisplayed unless an IO::navigate_to command was issued
		if($this->m_purchase->is_cart())
		{
			if($this->f_action == 'empty')
			{
				$this->get_save_restore();	
				$this->m_purchase->remove_all_components();
			}
			else if($this->f_action == 'reset')
			{	
				$this->m_purchase->reset();
				$this->get_input_from_db();
			}
			else if($this->f_action == 'save')
			{	
				$this->get_save_restore();
			}
			else if($this->f_action == 'savesupplier')
			{	
				$this->get_input_from_db();
				$this->f_supplier_id = IO::get_input_sl_pg('f_supplier_id','integer');
				$this->action_save();
				$this->get_input_from_db();
			}
			else if($this->f_action == 'saveshipper')
			{	
				$this->get_input_from_db();
				$this->f_shipper_id = IO::get_input_sl_pg('f_shipper_id','integer');	
				$this->action_save();
				$this->get_input_from_db();
			}
			else if($this->f_action == 'finish')
			{	
				$this->get_input_from_form();
				$this->action_save();
				$this->m_purchase->become_purchase();
				IO::navigate_to('page_purchase_view.php?f_id=' . $this->f_id);				
			}
			else if($this->f_action == 'selectsupplier')
			{
				$this->save_and_redirect('page_supplier_list.php?f_action_box_param=' . $this->f_id . '&f_mode=select');				
			}
			else if($this->f_action == 'selectshipper')
			{
				$this->save_and_redirect('page_shipper_list.php?f_action_box_param=' . $this->f_id . '&f_mode=selectforpurchase');				
			}
			//TODO: properly
			else if($this->f_action == 'removecomp')
			{
				$this->get_save_restore();
				$this->f_comp_id = IO::get_input_sl_pg('f_comp_id','integer');
				$this->m_purchase->remove_component($this->f_comp_id);
			}
			else if($this->f_action == 'gotoaddcomp')
			{
				$new_comp_id = $this->make_new_component();
				$this->save_and_redirect('page_purchase_comp_add_edit.php?f_id=' . $new_comp_id . '&f_action_box_param=' . $this->f_id); 
			}		
			else if($this->f_action == 'gotoeditcomp')
			{
				$this->f_comp_id = IO::get_input_sl_pg('f_comp_id','integer');			
				$this->save_and_redirect('page_purchase_comp_add_edit.php?f_id=' . $this->f_comp_id . '&f_action_box_param=' . $this->f_id); 
			}				
			//just display
			else
			{
				$this->get_input_from_db();
			}
		}
		//existing purchase actions
		else
		{
			if($this->f_action == 'save')
			{	
				$this->get_save_restore();
				IO::navigate_to('page_purchase_view.php?f_id=' . $this->f_id);	
			}
			else if($this->f_action == 'savesupplier')
			{	
				$this->get_input_from_db();
				$this->f_supplier_id = IO::get_input_sl_pg('f_supplier_id','integer');
				$this->action_save();
				$this->get_input_from_db();
				//IO::navigate_to('page_purchase_add_edit.php?f_id=' . $this->f_id);	
			}
			else if($this->f_action == 'saveshipper')
			{	
				$this->get_input_from_db();
				$this->f_shipper_id = IO::get_input_sl_pg('f_shipper_id','integer');
				$this->action_save();
				$this->get_input_from_db();
				//IO::navigate_to('page_purchase_add_edit.php?f_id=' . $this->f_id);	
			}			
			else if($this->f_action == 'selectsupplier')
			{
				$this->save_and_redirect('page_supplier_list.php?f_action_box_param=' . $this->f_id . '&f_mode=select');				
			}
			else if($this->f_action == 'selectshipper')
			{
				$this->save_and_redirect('page_shipper_list.php?f_action_box_param=' . $this->f_id . '&f_mode=selectforpurchase');				
			}
			//TODO: properly
			else if($this->f_action == 'removecomp')
			{
				$this->get_save_restore();
				$this->f_comp_id = IO::get_input_sl_pg('f_comp_id','integer');
				$this->m_purchase->remove_component($this->f_comp_id);
			}
			else if($this->f_action == 'gotoaddcomp')
			{
				$new_comp_id = $this->make_new_component();
				$this->save_and_redirect('page_purchase_comp_add_edit.php?f_id=' . $new_comp_id . '&f_action_box_param=' . $this->f_id); 
			}	
			else if($this->f_action == 'gotoeditcomp')
			{
				$this->f_comp_id = IO::get_input_sl_pg('f_comp_id','integer');			
				$this->save_and_redirect('page_purchase_comp_add_edit.php?f_id=' . $this->f_comp_id . '&f_action_box_param=' . $this->f_id); 
			}				
			//just display			
			else
			{
				$this->get_input_from_db();
			}			
		}

		//BUG FIX: avoids double addition/postback question on back button		
		if($this->f_action != '') IO::navigate_to('page_purchase_add_edit.php?f_id=' . $this->f_id);	
		
		//get purchase components for later listing
		$this->get_purchase_comps();
	}
	
	private function make_new_component()
	{
		DB::send_query('INSERT INTO purchase_comps (purchase_id) VALUES (\'' . $this->f_id . '\')');
		return DB::get_field_fq('SELECT LAST_INSERT_ID()');
	}
	
	private function get_purchase_comps()
	{
		//make list object
		$this->m_obj_purchase_comp_list = new ObjEditPurchaseCompList();
	
		$this->m_comp_info_arr = DB::get_all_rows_fq(
			'SELECT ' . 
			$this->m_obj_purchase_comp_list->get_needed_fields() . 
			'FROM purchase_comps' .
			$this->m_obj_purchase_comp_list->get_needed_joins() .
			'WHERE purchase_comps.purchase_id=\'' . $this->f_id . '\''
		);
		
		//TESTING:
		//echo count($this->m_comp_info_arr);
	}
	
	private function save_and_redirect($to_where)
	{
		$this->get_input_from_form();
		$this->action_save();
		IO::navigate_to($to_where);	
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
		$this->f_supplier_id 		= IO::get_input_sl_pg('f_supplier_id','integer');
		$this->f_shipper_id 		= IO::get_input_sl_pg('f_shipper_id','integer');
		$this->f_shipment_details 	= IO::get_input_ml_pg('f_shipment_details','string');
		$this->f_in_warehouse 			= IO::get_input_sl_pg('f_in_warehouse','integer');
		$this->f_order_date 		= IO::get_input_sl_pg('f_order_date','string');
		$this->f_delivery_date 		= IO::get_input_sl_pg('f_delivery_date','string'); 
		$this->f_price 				= IO::get_input_sl_pg('f_price','float');
		$this->f_notes 				= IO::get_input_ml_pg('f_notes','string');

		$this->f_order_date = OurTime::js_to_datetime($this->f_order_date, 0);
		$this->f_delivery_date = OurTime::js_to_datetime($this->f_delivery_date, 0);
	}

	private function get_input_from_db()
	{
		//get values from database
		$purchase_info = $this->m_purchase->get_purchase_info();
		
		$this->f_icode 				= $purchase_info['icode'];
		$this->f_supplier_id 		= $purchase_info['supplier_id'];
		$this->f_shipper_id 		= $purchase_info['shipper_id'];
		$this->f_shipment_details 	= $purchase_info['shipment_details'];
		$this->f_in_warehouse 		= $purchase_info['in_warehouse'];
		$this->f_order_date 		= OurTime::datetime_to_js($purchase_info['order_date']);
		$this->f_delivery_date 		= OurTime::datetime_to_js($purchase_info['delivery_date']);
		$this->f_price 				= $purchase_info['price'];
		$this->f_notes 				= $purchase_info['notes'];
		
		$this->m_supplier_text = IO::prepout_sl($purchase_info['supplier_icode'], 20) . ' : ' . IO::prepout_sl($purchase_info['supplier_company_name'], 30);
		$this->m_shipper_text = IO::prepout_sl($purchase_info['shipper_icode'], 20) . ' : ' . IO::prepout_sl($purchase_info['shipper_company_name'], 30);			
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
			$this->f_supplier_id . ' ' .
			$this->f_shipper_id . ' ' .
			$this->f_shipment_details . ' ' .
			(($this->f_in_warehouse != 0) ? Purchase::$IN_WAREHOUSE_SEARCH_WORD : '') . ' ' .
			$this->f_order_date . ' ' .
			$this->f_delivery_date . ' ' .
			$this->f_price . ' ' .
			$this->f_notes			
		);
	
		//update database
		$the_order_date = ($this->f_order_date == '' ? 'NOW()' : '\'' . $this->f_order_date . '\'');
		$the_delivery_date = ($this->f_delivery_date == '' ? 'NOW()' : '\'' . $this->f_delivery_date . '\'');
		
		DB::send_query('
		UPDATE purchases SET
		icode=\'' . $this->f_icode . '\',
		supplier_id=\'' . $this->f_supplier_id . '\',
		shipper_id=\'' . $this->f_shipper_id . '\',
		shipment_details=\'' . $this->f_shipment_details . '\',
		in_warehouse=\'' . $this->f_in_warehouse . '\',
		order_date=' . $the_order_date . ',
		delivery_date=' . $the_delivery_date . ',
		price=\'' . $this->f_price . '\',
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PURCHASES);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<form name="form_purchase" method="post" action="page_purchase_add_edit.php">
		<input name="f_action" type="hidden" value="">
		<input name="f_id" type="hidden" value="">
		<input name="f_comp_id" type="hidden" value="">
		<table width="100%">
			<tr>
				<td width="25%"> </td>
				<td width="75%" class="text_title">
				' . ($this->m_purchase->is_cart() ? 'Create New' : 'Edit Existing') . ' Purchase
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
				<td class="text_label" width="25%">Purchase ID Code: </td>
				<td class="form_input"><input type="text" name="f_icode" class="textbox" value="' . IO::prepout_sl($this->f_icode, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Supplier: </td>
				<td class="form_input">
					<input type="text" size="48" disabled="true" name="f_supplier_text" class="textbox" value="' . $this->m_supplier_text . '">
					<input type="hidden" name="f_supplier_id" class="textbox" value="' . IO::prepout_sl($this->f_supplier_id, false) . '">
					<input type="submit" class="button" value="Select New" onclick="form_purchase.f_action.value=\'selectsupplier\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
				</td>
			
			</tr>
			
			<tr>
				<td class="text_label">Shipper: </td>
				<td class="form_input">
					<input type="text" size="48" disabled="true" name="f_shipper_text" class="textbox" value="' . $this->m_shipper_text . '">
					<input type="hidden" name="f_shipper_id" class="textbox" value="' . IO::prepout_sl($this->f_shipper_id, false) . '">
					<input type="submit" class="button" value="Select New" onclick="form_purchase.f_action.value=\'selectshipper\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
				</td>
			</tr>
			
			<tr>
				<td class="text_label">Shipment Details: </td>
				<td class="form_input"><textarea name="f_shipment_details" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_shipment_details) . '</textarea></td>
			</tr>
			
			<tr>
				<td class="text_label">In Warehouse: </td>
				<td class="form_input"><input type="checkbox" name="f_in_warehouse" value="1"
			');
				if($this->f_in_warehouse != 0)
				{
					echo ' checked';
				}
			echo ('></td>
			</tr>
			
			<tr>
				<td class="text_label">Bought Date: </td>
				<td>
					<input name="f_order_date" class="textbox" type="text" value="' . IO::prepout_sl($this->f_order_date, false) . '" /> <a href="#" onclick="calord.select(document.form_purchase.f_order_date, \'datexx\', \'MM/dd/yyyy\'); return false; " name="datexx" id="datexx">Select Date</a>
					<div id="orderCal" style="position:absolute;visibility:hidden;"></div><br>
				</td>
			</tr>
			
			<tr>
				<td class="text_label">Delivery Date: </td>
				<td>
					<input name="f_delivery_date" class="textbox" type="text" value="' . IO::prepout_sl($this->f_delivery_date, false) . '" /> <a href="#" onclick="caldel.select(document.form_purchase.f_delivery_date, \'datexx\', \'MM/dd/yyyy\'); return false; " name="datexx" id="datexx">Select Date</a>
					<div id="deliveryCal" style="position:absolute;visibility:hidden;"></div><br>
				</td>
			</tr>
			
			<tr>
				<td class="text_label">Price: </td>
				<td class="form_input"><input type="text" name="f_price" class="textbox" value="' . IO::prepout_sl($this->f_price, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Notes: </td>
				<td class="form_input"><textarea name="f_notes" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_notes) . '</textarea></td>
			</tr>

		</table>
		<br>
		');
		
		//purchase component title
		echo ('
		<table width="100%">
			<tr>
				<td align="left" width="25%" valign="top"><input type="button" class="button" value="Add New Item" onclick="form_purchase.f_action.value=\'gotoaddcomp\'; form_purchase.f_id.value=\'' . $this->f_id . '\'; form_purchase.submit();"></td>
				<td align="left" valign="top" class="text_title">
					Purchase Contents:
				</td>
			</tr>
		</table>
		');
		
		//purchase component list
		//echo('<div align="center">');
			//TODO:
			$this->m_obj_purchase_comp_list->display('delete', $this->m_comp_info_arr);
		//echo('</div>');
		
		//buttons if is cart
		if( $this->m_purchase->is_cart() )
		{		
			echo('
			<br><br><br>
			<table width="100%">
				<tr>
					<td align="left">
						<input type="submit" name="f_submit_btn" value="Empty Contents" class="button" onclick="form_purchase.f_action.value=\'empty\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
						<br>
						<input type="submit" name="f_submit_btn" value="Reset All" class="button" onclick="form_purchase.f_action.value=\'reset\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
					</td>					
					<td align="right">
						<input type="submit" name="f_submit_btn" value="Save Progress" class="button" onclick="form_purchase.f_action.value=\'save\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
						<br>
						<input type="submit" name="f_submit_btn" value="Complete Purchase" class="button" onclick="form_purchase.f_action.value=\'finish\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
					</td>
				</tr>
			</table>
			</form>
			');
		}
		//buttons if is existing purchase
		else
		{
			echo('
			<br>
			<table width="100%">
				<tr>
					<td align="left">
						<input type="button" name="f_submit_btn" value="Delete Purchase" onclick="document.location=\'page_purchase_delete.php?f_id=' . IO::prepout_url($this->f_id) . '\';">
					</td>					
					<td align="right">
						<input type="submit" name="f_submit_btn" value="Save Changes" class="button" onclick="form_purchase.f_action.value=\'save\'; form_purchase.f_id.value=\'' . $this->f_id . '\';">
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
$page_purchase_add_edit = new PagePurchaseAddEdit();
$page_purchase_add_edit->run();

?>
