<?php

//NOTES:
//technically, we're always in EDIT mode, so there is no "f_mode=edit" necessary
//we're either editting the user's shopping cart if no f_id was passed in
//or we're editting an purchase if an f_id was passed in, but still through the shopping cart class
//the shopping cart class takes care of the creation of a shopping cart (an purchase with is_cart=1)

require_once('framework.php');
require_once('obj_comp_lists.php');
require_once('purchase_cart.php');

class PagePurchaseAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_purchase_add_edit.php';
	
	//*** MEMBERS ***	
	private $m_product_text;
	
	private $f_action;
	private $f_id;
	
	private $f_product_id;
	private $f_units;
	private $f_quantity_purchased;
	private $f_quantity_sellable;
	private $f_expiration_date;
	private $f_min_price_per_unit;
	

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
		
		//get id of purchase comp	
		$this->f_id = IO::get_input_sl_pg('f_id','integer');
	
		//actions
		if($this->f_action == 'save')
		{	
			$this->get_input_from_form();
			$this->save_and_redirect('page_purchase_add_edit.php?f_id=' . $this->get_purchase_parent());
		}
		else if($this->f_action == 'selectproduct')
		{	
			$this->get_input_from_form();
			$this->save_and_redirect('page_product_list.php?f_mode=select&f_action_box_param=' . $this->f_id);
		}
		else if($this->f_action == 'saveproduct')
		{	
			$this->get_input_from_db();
			$this->f_product_id = IO::get_input_sl_pg('f_product_id','integer');
			$this->autofill_expiration_date_from_products();			
			$this->action_save();
			$this->get_input_from_db();
		}
		//just display
		else
		{
			$this->get_input_from_db();
		}
	}
	
	private function autofill_expiration_date_from_products()
	{
		$typical_lifespan_days = DB::get_field_fq('
			SELECT products.typical_lifespan_days
			FROM products
			WHERE products.id=\'' . $this->f_product_id . '\'
		');
		
		$this->f_expiration_date = DB::get_field_fq('
			SELECT ADDTIME(NOW(), \'' . $typical_lifespan_days . ' 00:00:00\')			
		');
	}
	
	private function get_purchase_parent()
	{
		return DB::get_field_fq('
			SELECT purchase_comps.purchase_id
			FROM purchase_comps
			WHERE id = \'' . $this->f_id . '\'
		');
	}
	
	private function get_input_from_form()
	{
		$this->f_product_id 				= IO::get_input_sl_pg('f_product_id','string');
		$this->f_units 						= IO::get_input_sl_pg('f_units','string');
		$this->f_quantity_purchased 		= IO::get_input_sl_pg('f_quantity_purchased','integer');
		$this->f_quantity_sellable 			= IO::get_input_ml_pg('f_quantity_sellable','integer');
		$this->f_expiration_date 			= IO::get_input_sl_pg('f_expiration_date','string');
		$this->f_min_price_per_unit 		= IO::get_input_sl_pg('f_min_price_per_unit','string');
		
		if($this->f_quantity_purchased < 0) $this->f_quantity_purchased = 0;
		if($this->f_quantity_sellable < 0) $this->f_quantity_sellable = 0;
	}

	private function get_input_from_db()
	{
		//get values from database
		$purchase_comp_info = DB::get_single_row_fq('
			SELECT purchase_comps.*, 
			products.id AS product_id,
			products.icode AS product_icode,
			products.name AS product_name
			FROM purchase_comps
			LEFT OUTER JOIN products ON purchase_comps.product_id = products.id
			WHERE purchase_comps.id=\'' . $this->f_id . '\'
		');
		
		$this->f_product_id 				= $purchase_comp_info['product_id'];
		$this->f_units 						= $purchase_comp_info['units'];
		$this->f_quantity_purchased 		= $purchase_comp_info['quantity_purchased'];
		$this->f_quantity_sellable 			= $purchase_comp_info['quantity_sellable'];
		$this->f_expiration_date 			= $purchase_comp_info['expiration_date'];	//TODO: autofill expiration date
		$this->f_min_price_per_unit 		= $purchase_comp_info['min_price_per_unit'];

		$this->m_product_text = IO::prepout_sl($purchase_comp_info['product_icode'], 20) . ' : ' . IO::prepout_sl($purchase_comp_info['product_name'], 30);		
	}
	
	private function save_and_redirect($to_where)
	{
		$this->get_input_from_form();
		$this->action_save();	//check
		IO::navigate_to($to_where);	
	}
	
	private function action_save()
	{
		/*
		echo 		'UPDATE purchase_comps SET
		product_id=\'' . $this->f_product_id . '\',
		units=\'' . $this->f_units . '\',
		quantity_purchased=\'' . $this->f_quantity_purchased . '\',
		quantity_sellable=\'' . $this->f_quantity_sellable . '\',
		expiration_date=\'' . $this->f_expiration_date . '\',
		min_price_per_unit=\'' . $this->f_min_price_per_unit . '\'
		WHERE id=\'' . $this->f_id . '\'';
		*/
	
		DB::send_query('
		UPDATE purchase_comps SET
		product_id=\'' . $this->f_product_id . '\',
		units=\'' . $this->f_units . '\',
		quantity_purchased=\'' . $this->f_quantity_purchased . '\',
		quantity_sellable=\'' . $this->f_quantity_sellable . '\',
		expiration_date=\'' . $this->f_expiration_date . '\',
		min_price_per_unit=\'' . $this->f_min_price_per_unit . '\'
		WHERE id=\'' . $this->f_id . '\'
		');
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PURCHASES);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<form name="form_purchase_comp" method="post" action="page_purchase_comp_add_edit.php">
		<input name="f_action" type="hidden" value="">
		<input name="f_id" type="hidden" value="">
		<!-- <input name="f_comp_id" type="hidden" value=""> -->
		<table width="100%">
			<tr>
				<td width="25%"> </td>
				<td width="75%" class="text_title">
					Edit Purchase Item Details
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
				<td class="text_label">Product: </td>
				<td class="form_input">
					<input type="text" size="48" disabled="true" name="f_product_text" class="textbox" value="' . IO::prepout_sl($this->m_product_text, false) . '">
					<input type="hidden" name="f_product_id" class="textbox" value="' . IO::prepout_sl($this->f_product_id, false) . '">
					<input type="submit" value="Select Product" onclick="form_purchase_comp.f_action.value=\'selectproduct\'; form_purchase_comp.f_id.value=\'' . $this->f_id . '\';">
				</td>
			
			</tr>

			<tr>
				<td class="text_label">Units: </td>
				<td class="form_input"><input type="text" name="f_units" class="textbox" value="' . IO::prepout_sl($this->f_units, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Quantity Purchased: </td>
				<td class="form_input"><input type="text" name="f_quantity_purchased" class="textbox" value="' . IO::prepout_sl($this->f_quantity_purchased, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Quantity Sellable: </td>
				<td class="form_input"><input type="text" name="f_quantity_sellable" class="textbox" value="' . IO::prepout_sl($this->f_quantity_sellable, false) . '"></td>
			</tr>
			
			<tr>
				<td class="text_label">Expiration Date: </td>
				<td class="form_input"><input type="text" name="f_expiration_date" class="textbox" value="' . IO::prepout_sl($this->f_expiration_date, false) . '"></td>
			</tr>	

			<tr>
				<td class="text_label">Minimum Price/Unit: </td>
				<td class="form_input"><input type="text" name="f_min_price_per_unit" class="textbox" value="' . IO::prepout_sl($this->f_min_price_per_unit, false) . '"></td>
			</tr>				
			
		</table>
		');
		
		//buttons
		echo('
		<br><br>
		<table width="100%">
			<tr>
				<td align="right">
					<input type="submit" name="f_submit_btn" value="Save" class="button" onclick="form_purchase_comp.f_action.value=\'save\'; form_purchase_comp.f_id.value=\'' . $this->f_id . '\';">
				</td>
			</tr>
		</table>
		</form>
		');
		
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
