<?php

require_once('framework.php');

class PageShipperAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_template.php';
	private static $NEXT_PAGE = 'page_shipper_menu.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_company_name;
	private $f_contact_name;
	private $f_contact_dept;
	private $f_office_phone_number;
	private $f_cell_phone_number;
	private $f_fax_number;
	private $f_address_line_1;
	private $f_address_line_2;
	private $f_city;
	private $f_province;
	private $f_country;
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
			$this->f_company_name = IO::get_input_sl_p('f_company_name','string');
			$this->f_contact_name = IO::get_input_sl_p('f_contact_name','string');
			$this->f_contact_dept = IO::get_input_sl_p('f_contact_dept','string');
			$this->f_office_phone_number = IO::get_input_sl_p('f_office_phone_number','string');
			$this->f_cell_phone_number = IO::get_input_sl_p('f_cell_phone_number','string');
			$this->f_fax_number = IO::get_input_sl_p('f_fax_number','string');
			$this->f_address_line_1 = IO::get_input_sl_p('f_address_line_1','string');
			$this->f_address_line_2 = IO::get_input_sl_p('f_address_line_2','string');
			$this->f_city = IO::get_input_sl_p('f_city','string');
			$this->f_province = IO::get_input_sl_p('f_province','string');
			$this->f_country = IO::get_input_sl_p('f_country','string');
			$this->f_notes = IO::get_input_ml_p('f_notes','string');
		}
		//if NOT submitting, but in EDIT mode, fill the fields from database data
		else if($this->f_mode == 'edit')
		{
			//get id of customer to edit
			$this->f_id = IO::get_input_sl_g('f_id','string');
		
			//get values from database
			$shipper_info = DB::get_single_row_fq('
				SELECT icode, company_name, contact_name, contact_dept, office_phone_number, cell_phone_number, fax_number, address_line_1, address_line_2, city, province, country, notes
				FROM shippers WHERE id=\'' . $this->f_id . '\''
			);
			
			$this->f_icode = $shipper_info['icode'];
			$this->f_company_name = $shipper_info['company_name'];
			$this->f_contact_name = $shipper_info['contact_name'];
			$this->f_contact_dept = $shipper_info['contact_dept'];
			$this->f_office_phone_number = $shipper_info['office_phone_number'];
			$this->f_cell_phone_number = $shipper_info['cell_phone_number'];
			$this->f_fax_number = $shipper_info['fax_number'];
			$this->f_address_line_1 = $shipper_info['address_line_1'];
			$this->f_address_line_2 = $shipper_info['address_line_2'];
			$this->f_city = $shipper_info['city'];
			$this->f_province = $shipper_info['province'];
			$this->f_country = $shipper_info['country'];
			$this->f_notes = $shipper_info['notes'];	
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
			if(strlen($this->f_company_name) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Company name entry is too long.';
			if(strlen($this->f_contact_name) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Contact name entry is too long.';
			if(strlen($this->f_contact_dept) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Contact department entry is too long.';
			if(strlen($this->f_office_phone_number) > Config::$DEFAULT_VARCHAR_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Office phone number  entry is too long.';
			if(strlen($this->f_cell_phone_number) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Cell phone number  entry is too long.';
			if(strlen($this->f_fax_number) > Config::$DEFAULT_VARCHAR_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Fax number  entry is too long.';
			if(strlen($this->f_address_line_1) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Address line 1 entry is too long.';
			if(strlen($this->f_address_line_2) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Address line 2 entry is too long.';
			if(strlen($this->f_city) > Config::$DEFAULT_VARCHAR_LEN) 
				$f_outputarray[sizeof($f_outputarray)] = 'Error: City entry is too long.';
			if(strlen($this->f_province) > Config::$DEFAULT_VARCHAR_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Province entry is too long.';
			if(strlen($this->f_country) > Config::$DEFAULT_VARCHAR_LEN)
				$f_outputarray[sizeof($f_outputarray)] = 'Error: Country entry is too long.';
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
				$this->f_company_name . ' ' .
				$this->f_contact_name . ' ' .
				$this->f_contact_dept . ' ' .
				$this->f_office_phone_number . ' ' .
				$this->f_cell_phone_number . ' ' .
				$this->f_fax_number . ' ' .
				$this->f_address_line_1 . ' ' .
				$this->f_address_line_2 . ' ' .
				$this->f_city . ' ' .
				$this->f_province . ' ' .
				$this->f_country . ' ' .
				$this->f_notes			
			);
		
			//edit mode submit
			if($this->f_mode == 'edit')
			{
				//insert
				DB::send_query('
				UPDATE shippers SET
				icode=\'' . $this->f_icode . '\',
				company_name=\'' . $this->f_company_name . '\',
				contact_name=\'' . $this->f_contact_name . '\',
				contact_dept=\'' . $this->f_contact_dept . '\',
				office_phone_number=\'' . $this->f_office_phone_number . '\',
				cell_phone_number=\'' . $this->f_cell_phone_number . '\',
				fax_number=\'' . $this->f_fax_number . '\',
				address_line_1=\'' . $this->f_address_line_1 . '\',
				address_line_2=\'' . $this->f_address_line_2 . '\',
				city=\'' . $this->f_city . '\',
				province=\'' . $this->f_province . '\',
				country=\'' . $this->f_country . '\',
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
				INSERT INTO shippers 
				(icode, company_name, contact_name, contact_dept, office_phone_number, cell_phone_number, fax_number, address_line_1, address_line_2, city, province, country, notes, created_employee_id, updated_employee_id, created_date, updated_date, search_words) 
				VALUES (
				\'' . $this->f_icode . '\',
				\'' . $this->f_company_name . '\',
				\'' . $this->f_contact_name . '\',
				\'' . $this->f_contact_dept . '\',
				\'' . $this->f_office_phone_number . '\',
				\'' . $this->f_cell_phone_number . '\',
				\'' . $this->f_fax_number . '\',
				\'' . $this->f_address_line_1 . '\',
				\'' . $this->f_address_line_2 . '\',
				\'' . $this->f_city . '\',
				\'' . $this->f_province . '\',
				\'' . $this->f_country . '\',
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
			IO::navigate_to('page_shipper_view.php?f_id=' . $this->f_id);
		}
	}
	
	private function show_output($outputarray)
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SHIPPERS);
		
		//echo inner area html here
		echo('
               <div align="center">
                  <form name="form1" method="post" action="page_shipper_add_edit.php?f_action=submit&f_mode=' . IO::prepout_url($this->f_mode) . (($this->f_mode == 'edit') ? ('&f_id=' . IO::prepout_url($this->f_id)) : '') . '">
                    <table width="600" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="25%" align="right" valign="middle">&nbsp;</td>
                              <td width="75%" align="left" valign="middle" class="text_title">Add New Shippers</td>
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
                              <td width="25%" align="right" valign="middle" class="text_label">Company Name:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_company_name" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_company_name, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Contact Name:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_contact_name" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_contact_name, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Contact Department:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_contact_dept" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_contact_dept, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Office Phone Number:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_office_phone_number" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_office_phone_number, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Cell Phone Number:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_cell_phone_number" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_cell_phone_number, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Fax Number:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_fax_number" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_fax_number, false) . '"></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>	
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Address Line 1:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_address_line_1" type="text" size="40" class="textbox" value="' . IO::prepout_sl($this->f_address_line_1, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Address Line 2:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_address_line_2" type="text" size="40" class="textbox" value="' . IO::prepout_sl($this->f_address_line_2, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">City:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_city" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_city, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Province:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_province" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_province, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Country:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_country" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_country, false) . '"></td>
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
$page = new PageShipperAddEdit();
$page->run();

?>