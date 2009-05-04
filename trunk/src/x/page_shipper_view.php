<?php

require_once('framework.php');

class PageShipperView
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_shipper_view.php';
	
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
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_WRITE);
	
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
			$ship_info = DB::get_single_row_fq('
				SELECT icode, company_name, contact_name, contact_dept, office_phone_number, cell_phone_number, fax_number, address_line_1, address_line_2, city, province, country, notes
				FROM shippers WHERE id=\'' . $this->f_id . '\''
			);
			
			
			$this->f_icode = $ship_info['icode'];
			$this->f_company_name = $ship_info['company_name'];
			$this->f_contact_name = $ship_info['contact_name'];
			$this->f_contact_dept = $ship_info['contact_dept'];
			$this->f_office_phone_number = $ship_info['office_phone_number'];
			$this->f_cell_phone_number = $ship_info['cell_phone_number'];
			$this->f_fax_number = $ship_info['fax_number'];
			$this->f_address_line_1 = $ship_info['address_line_1'];
			$this->f_address_line_2 = $ship_info['address_line_2'];
			$this->f_city = $ship_info['city'];
			$this->f_province = $ship_info['province'];
			$this->f_country = $ship_info['country'];
			$this->f_notes = $ship_info['notes'];	
		
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SHIPPER);
		
		//echo inner area html
		Echo ('	
				   <div align="center">
			  <form name="form1" method="post" action="page_shipper_view.php">
				<table width="600" border="0" cellpadding="0" cellspacing="0">

				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle">&nbsp;</td>
						  <td width="75%" align="left" valign="middle" class="text_title">View Shipper</td>
						</tr>
					</table></td>
				  </tr>	
				  
				  <tr>

					<td>&nbsp;</td>
				  </tr>						
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_icode) . '</td>
						</tr>
						<tr>

						  <td width="25%" align="right" valign="middle" class="text_label">Company Name:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_company_name) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Contact Name:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_contact_name) . '</td>
						</tr>
						<tr>

						  <td width="25%" align="right" valign="middle" class="text_label">Contact Department:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_contact_dept) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Office Phone:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_office_phone_number) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Cell Phone:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_cell_phone_number) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Fax:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_fax_number) . '</td>
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
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_address_line_1) . '</td>

					  </tr>
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Address Line 2:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_address_line_2) . '</td>
					  </tr>
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">City:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_city) . '</td>

					  </tr>
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Province:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_province) . '</td>
					  </tr>
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Country:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_country) . '</td>

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
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_notes) . '</td>
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
						  <td width="75%" align="left" valign="middle"><input type="submit" name="Submit" value="This page, just blank" class="button"></td>
						</tr>
					</table></td>
				  </tr>
				</table>

			  </form>
			</div>		

				  </td>
				</tr>
		');

		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_shipper_view = new PageShipperView();
$page_shipper_view->run();

?>