<?php

require_once('framework.php');

class PageProductAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_template.php';
	private static $NEXT_PAGE = 'page_product_menu.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_name;
	private $f_type;
	private $f_subtype1;
	private $f_subtype2;
	private $f_typical_lifespan_days;
	private $f_typical_units;
	private $f_typical_min_price_per_unit;
	private $f_description;
	private $f_notes;
	
	private $f_outputarray = array();
	
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
		
		$this->show_output($outputarray);
	}
	
	private function get_input()
	{
		$this->f_mode = IO::get_input_sl_g('f_mode','string');	
		$this->f_action = IO::get_input_sl_g('f_action','string');
	
		//if submitting in ADD or EDIT mode, get fields from form
		if($this->f_action == 'submit')
		{
			//if submitting in EDIT mode, additionally get the customer id to edit
			if($this->f_mode == 'edit')
			{
				//get id of customer to edit
				$this->f_id = IO::get_input_sl_g('f_id','string');
			}
	
			$this->f_icode = IO::get_input_sl_p('f_icode','string');
			$this->f_name = IO::get_input_sl_p('f_name','string');
			$this->f_type = IO::get_input_sl_p('f_type','string');
			$this->f_subtype1 = IO::get_input_sl_p('f_subtype1','string');
			$this->f_subtype2 = IO::get_input_sl_p('f_subtype2','string');
			$this->f_typical_lifespan_days = IO::get_input_sl_p('f_typical_lifespan_days','int');
			$this->f_typical_units = IO::get_input_sl_p('f_typical_units','string');
			$this->f_typical_min_price_per_unit = IO::get_input_sl_p('f_typical_min_price_per_unit','float');
			$this->f_description = IO::get_input_ml_p('f_description','string');
			$this->f_notes = IO::get_input_ml_p('f_notes','string');
		}
		//if NOT submitting, but in EDIT mode, fill the fields from database data
		else if($this->f_mode == 'edit')
		{
			//get id of customer to edit
			$this->f_id = IO::get_input_sl_g('f_id','string');
		
			//get values from database
			$supplier_info = DB::get_single_row_fq('
				SELECT icode, name, type, subtype1, subtype2, typical_lifespan_days, typical_units, typical_min_price_per_unit, description, notes
				FROM products WHERE id=\'' . $this->f_id . '\''
			);
			
			$this->f_icode = $product_info['icode'];
			$this->f_name = $product_info['name'];
			$this->f_type = $product_info['type'];
			$this->f_subtype1 = $product_info['subtype1'];
			$this->f_subtype2 = $product_info['subtyp2'];
			$this->f_typical_lifespan_days = $product_info['typical_lifespan_days'];
			$this->f_typical_units = $product_info['typical_units'];
			$this->f_typical_min_price_per_unit = $product_info['typical_min_price_per_unit'];
			$this->f_description = $product_info['description'];
			$this->f_notes = $product_info['notes'];	
		}
		//if NOT submitting, and in ADD mode, do nothing (empty textboxes)
	}
	
	private function verify_input()
	{
		if($this->f_action == 'submit')
		{
	
			//check these for both add and edit mode
			if(strlen($this->f_icode) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: ID Code is too long.';
			if(strlen($this->f_name) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Product name entry is too long.';
			if(strlen($this->f_type) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Product type entry is too long.';
			if(strlen($this->f_subtype1) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Product subtype 1 entry is too long.';
			if(strlen($this->f_subtype2) > Config::$DEFAULT_VARCHAR_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Product subtype 2 entry is too long.';
			//if(strlen($this->f_typical_lifespan_days) > Config::$DEFAULT_VARCHAR_LEN) 
			//	$f_outputarray[sizeof($f_outputarray)] = 'Error: Cell phone number  entry is too long.';
			if(strlen($this->f_typical_units) > Config::$DEFAULT_VARCHAR_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Units entry is too long.';
			//if(strlen($this->f_typical_min_price_per_unit) > Config::$DEFAULT_VARCHAR_LEN) 
			//	$f_outputarray[sizeof($f_outputarray)] = 'Error: Address line 1 entry is too long.';
			if(strlen($this->f_description) > Config::$DEFAULT_TEXT_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Description entry is too long.';
			if(strlen($this->f_notes) > Config::$DEFAULT_TEXT_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Notes entry is too long.';

			//outputs any errors
			if(sizeof($f_outputarray) > 0) 
				$this->show_output($f_outputarray);
		}
	}
	
	private function process_input()
	{
		if($this->f_action == 'submit')
		{
			//make the search words field
			$search_words = DB::encode_small_words_store(
				$this->f_icode . ' ' .
				$this->f_name . ' ' .
				$this->f_type . ' ' .
				$this->f_subtype1 . ' ' .
				$this->f_subtype2 . ' ' .
				$this->f_typical_lifespan_days . ' ' .
				$this->f_typical_units . ' ' .
				$this->f_typical_min_price_per_unit . ' ' .
				$this->f_description . ' ' .
				$this->f_notes			
			);
		
			//edit mode submit
			if($this->f_mode == 'edit')
			{
				//insert
				DB::send_query('
				UPDATE products SET
				icode=\'' . $this->f_icode . '\',
				name=\'' . $this->f_name . '\',
				type=\'' . $this->f_type . '\',
				subtype1=\'' . $this->f_subtype1 . '\',
				subtype2=\'' . $this->f_subtype2 . '\',
				typical_lifespan_days=\'' . $this->f_typical_lifespan_days . '\',
				typical_units=\'' . $this->f_typical_units . '\',
				typical_min_price_per_unit=\'' . $this->f_typical_min_price_per_unit . '\',
				description=\'' . $this->f_description . '\',
				notes=\'' . $this->f_notes . '\',
				updated_date=NOW(),
				updated_employee_id=\'' . LoginManager::get_id() . '\',
				search_words=\'' . $search_words . '\'
				WHERE id=\'' . $this->f_id . '\'
				');
			}
			//add mode submit
			else
			{
				//insert
				DB::send_query('
				INSERT INTO products 
				(icode, name, type, subtype1, subtype2, typical_lifespan_days, typical_units, typical_min_price_per_unit, description, notes, created_employee_id, updated_employee_id, created_date, updated_date, search_words) 
				VALUES (
				\'' . $this->f_icode . '\',
				\'' . $this->f_name . '\',
				\'' . $this->f_type . '\',
				\'' . $this->f_subtype1 . '\',
				\'' . $this->f_subtype2 . '\',
				\'' . $this->f_typical_lifespan_days . '\',
				\'' . $this->f_typical_units . '\',
				\'' . $this->f_typical_min_price_per_unit . '\',
				\'' . $this->f_description . '\',
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
			IO::navigate_to('page_product_view.php?f_id=' . $this->f_id);
		}
	}
	
	private function show_output($outputarray)
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PRODUCTS);
		
		//echo inner area html here
		echo('
               <div align="center">
                  <form name="form1" method="post" action="page_product_add_edit.php?f_action=submit&f_mode=' . IO::prepout_url($this->f_mode) . (($this->f_mode == 'edit') ? ('&f_id=' . IO::prepout_url($this->f_id)) : '') . '">
                    <table width="600" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="25%" align="right" valign="middle">&nbsp;</td>
                              <td width="75%" align="left" valign="middle" class="text_title">Add New Product</td>
                            </tr>
                        </table></td>
                      </tr>	
		');		
		
		if (sizeof($outputarray) > 0) 
		{
			foreach ($outputarray as $entity)
			{
				echo('
							  <tr>
								<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
									  <td width="25%" align="right" valign="middle">&nbsp;</td>
									  <td width="75%" align="left" valign="middle" class="text_error">' . $entity . '</td>
									</tr>
								</table></td>
							  </tr>
				');
			}
		}
		
		echo('			  
                      <tr>
                        <td>&nbsp;</td>
                      </tr>						
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_icode" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_icode, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Product Name:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_name" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_name, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Product Type:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_type" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_type, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Product Subtype 1:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_subtype1" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_subtype1, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Product Subtype 2:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_subtype2" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_subtype2, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="top" class="text_label">Description:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><textarea name="f_description" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_description) . '</textarea></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Lifespan (in days):&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_typical_lifespan_days" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_typical_lifespan_days, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Product Units:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_typical_units" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_typical_units, false) . '"></td>
                            </tr>
							<tr>
							  <td width="25%" align="right" valign="middle" class="text_label">Min. Price (per unit):&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_typical_min_price_per_unit" type="text" size="40" class="textbox" value="' . IO::prepout_sl($this->f_typical_min_price_per_unit, false) . '"></td>
                            </tr>
                          </table></td>
                        </tr>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
                              <td width="25%" align="right" valign="top" class="text_label">Notes:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><textarea name="f_notes" cols="40" rows="10" class="textbox">' . IO::prepout_ml_textarea($this->f_notes) . '</textarea></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="25%" align="right" valign="top">&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input type="submit" name="Submit" value="Save" class="button"></td>
                            </tr>
                        </table></td>
                      </tr>
                    </table>
                  </form>
                </div>		
		');
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page = new PageProductAddEdit();
$page->run();

?>