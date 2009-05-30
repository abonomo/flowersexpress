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
	private $f_trash_flag;	
			
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
			$ship_info = DB::get_single_row_fq
			('
				SELECT  shippers.icode, 
						shippers.company_name, 
						shippers.contact_name, 
						shippers.contact_dept, 
						shippers.office_phone_number, 
						shippers.cell_phone_number, 
						shippers.fax_number, 
						shippers.address_line_1, 
						shippers.address_line_2, 
						shippers.city, 
						shippers.province, 
						shippers.country,
						shippers.notes,
						shippers.trash_flag,
												
						shippers.created_employee_id, 
						shippers.updated_employee_id,
						shippers.created_date,
						shippers.updated_date,
						
						employees.first_name,
						employees.last_name
				FROM shippers
				LEFT OUTER JOIN employees ON shippers.created_employee_id = employees.id
				WHERE shippers.id=\'' . $this->f_id . '\''
			);
			
			$this->f_icode 					= $ship_info['icode'];
			$this->f_company_name 			= $ship_info['company_name'];
			$this->f_contact_name 			= $ship_info['contact_name'];
			$this->f_contact_dept 			= $ship_info['contact_dept'];
			$this->f_office_phone_number 	= $ship_info['office_phone_number'];
			$this->f_cell_phone_number 		= $ship_info['cell_phone_number'];
			$this->f_fax_number 			= $ship_info['fax_number'];
			$this->f_address_line_1 		= $ship_info['address_line_1'];
			$this->f_address_line_2 		= $ship_info['address_line_2'];
			$this->f_city 					= $ship_info['city'];
			$this->f_province 				= $ship_info['province'];
			$this->f_country 				= $ship_info['country'];
			$this->f_notes 					= $ship_info['notes'];	
			$this->f_trash_flag 			= $ship_info['trash_flag'];
			$this->f_created_employee_id	= $ship_info['created_employee_id'];
			$this->f_updated_employee_id	= $ship_info['updated_employee_id'];			
			
			$this->f_created_date 			= $ship_info['created_date'];
			$this->f_updated_date 			= $ship_info['updated_date'];	
			
			$this->f_created_first 			= $ship_info['first_name'];
			$this->f_created_last 			= $ship_info['last_name'];
			
			//Query for updated employee info
			$ship_info_up = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM shippers
				LEFT OUTER JOIN employees ON shippers.updated_employee_id = employees.id
				WHERE shippers.id=\'' . $this->f_id . '\''
			);

			$this->f_updated_first = $ship_info_up['first_name'];
			$this->f_updated_last = $ship_info_up['last_name'];	
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SHIPPERS);
		
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
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_icode) . '');
						  
						 //if the shipper has been deleted, print trash icon
						if( $this->f_trash_flag == '1' )
						{
							echo (' <img src="../img/icon_trash.gif"/> ');
							echo (' (This item has been deleted!)');
						}
						
						echo('</td>
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
						  <td width="25%" align="right" valign="middle" class="text_label">Created:&nbsp;</td>
						  <td width="75%" align="left" valign="middle"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_created_employee_id, false) . '">'  . 
									IO::prepout_sl($this->f_created_first, false) . '&nbsp;' . IO::prepout_sl($this->f_created_last, false)  . '</a>&nbsp;on&nbsp;' . 
									IO::prepout_ml_html($this->f_created_date) . '</td>
						 </tr>
						 
				  		<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Updated:&nbsp;</td>
						  <td width="75%" align="left" valign="middle"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_updated_employee_id, false) . '">'  . 
									IO::prepout_sl($this->f_updated_first, false) . '&nbsp;' . IO::prepout_sl($this->f_updated_last, false)  . '</a>&nbsp;on&nbsp;' . 
									IO::prepout_ml_html($this->f_updated_date) . '</td>
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
$page_shipper_view = new PageShipperView();
$page_shipper_view->run();

?>