<?php

require_once('framework.php');

class PageSupplierView
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_supplier_view.php';
	
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

	
				//ADD THIS FOR UPDATED INFO FUNCTIONALITY
	private $f_created_first;
	private $f_created_last;
	private $f_updated_first;
	private $f_updated_last;
	
	private $f_created_date;
	private $f_updated_date;
	private $f_last_contacted;
			//to this
	
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
		$this->f_action = IO::get_input_sl_pg('f_action', 'string');
		if($this->f_action == 'just_called')
		{
			DB::send_query
			('
				UPDATE suppliers SET
				last_contacted = NOW(),
				updated_date = NOW(),
				updated_employee_id=\'' . LoginManager::get_id() . '\'
				WHERE id = \'' . $this->f_id . '\'
			');
		}
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
			$suppl_info = DB::get_single_row_fq
			('
				SELECT  suppliers.icode, 
						suppliers.company_name, 
						suppliers.contact_name, 
						suppliers.contact_dept, 
						suppliers.office_phone_number, 
						suppliers.cell_phone_number, 
						suppliers.fax_number, 
						suppliers.address_line_1, 
						suppliers.address_line_2, 
						suppliers.city, 
						suppliers.province, 
						suppliers.country, 
						suppliers.notes,
						suppliers.trash_flag,

						
						suppliers.created_employee_id, 
						suppliers.updated_employee_id,
						suppliers.created_date,
						suppliers.updated_date,
						suppliers.last_contacted,
						
						employees.first_name,
						employees.last_name
				FROM suppliers 
				LEFT OUTER JOIN employees ON suppliers.created_employee_id = employees.id
				WHERE suppliers.id=\'' . $this->f_id . '\''
			);
			$suppl_info_up = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM suppliers
				LEFT OUTER JOIN employees ON suppliers.updated_employee_id = employees.id
				WHERE suppliers.id=\'' . $this->f_id . '\''
			);
			
			$this->f_icode = $suppl_info['icode'];
			$this->f_company_name = $suppl_info['company_name'];
			$this->f_contact_name = $suppl_info['contact_name'];
			$this->f_contact_dept = $suppl_info['contact_dept'];
			$this->f_office_phone_number = $suppl_info['office_phone_number'];
			$this->f_cell_phone_number = $suppl_info['cell_phone_number'];
			$this->f_fax_number = $suppl_info['fax_number'];
			$this->f_address_line_1 = $suppl_info['address_line_1'];
			$this->f_address_line_2 = $suppl_info['address_line_2'];
			$this->f_city = $suppl_info['city'];
			$this->f_province = $suppl_info['province'];
			$this->f_country = $suppl_info['country'];
			$this->f_notes = $suppl_info['notes'];
			$this->f_trash_flag = $suppl_info['trash_flag'];
			$this->f_last_contacted = $suppl_info['last_contacted'];
			
					//add these for UPDATED INFO
			$this->f_created_first = $suppl_info['first_name'];
			$this->f_created_last = $suppl_info['last_name'];
			
			$this->f_updated_first = $suppl_info_up['first_name'];
			$this->f_updated_last = $suppl_info_up['last_name'];
			
			$this->f_created_date = $suppl_info['created_date'];
			$this->f_updated_date = $suppl_info['updated_date'];
			
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SUPPLIERS);
		
		//echo inner area html
		Echo ('	
				   <div align="center">
			  <form name="form1" method="post" action="page_supplier_view.php?f_id=' . IO::prepout_url($this->f_id) . '&f_action=just_called">
				<table width="600" border="0" cellpadding="0" cellspacing="0">

				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle">&nbsp;</td>
						  <td width="75%" align="left" valign="middle" class="text_title">View Supplier</td>
						</tr>
					</table></td>
				  </tr>	
				  
<!-- Link of Edit, Delete & Undeleted starts here -->
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="27%" align="right" valign="middle">&nbsp;</td>
					<td width="73%" align="left" valign="middle"> ');
					
// show edit & delete link below the title if trash_flag != 1
// show undelete link below the title if trash_flag == 1
				if( $this->f_trash_flag != '1' )
				{
					echo (' <img src="../img/icon_edit.gif"/> ');
					echo (' <a href="page_supplier_add_edit.php?f_mode=edit&amp;f_id=' . $this->f_id . '">Edit</a>');
					echo ('  ');
					echo (' <img src="../img/icon_delete.gif"/> ');
					echo (' <a href="#" onclick="if(window.confirm(\'Are you sure you want to delete this entry?\')) { document.location=\'page_supplier_delete.php?f_id=' . $this->f_id .'\'; } return false;"/>Delete</a></td>');
				}
				else
				{
					echo (' <img src="../img/icon_undelete.gif"/> ');
					echo (' <a href="page_supplier_undelete.php?f_id=' . $this->f_id . '">Undelete</a></td>');
				}
				echo ('	</tr>
					</table></td>
				  </tr>
<!-- end of Link eddition -->
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>						
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_icode) . '');
						  
						 //if the supplier has been deleted, print trash icon
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
				<!--BEGINNNN updated info area-->
				  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Created:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_created_first) . '&nbsp;' . IO::prepout_ml_html($this->f_created_last) . '&nbsp;on&nbsp;' . IO::prepout_ml_html($this->f_created_date) . '</td>
						 </tr>
				  		<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Updated:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_updated_first) . '&nbsp;' . IO::prepout_ml_html($this->f_updated_last) . '&nbsp;on&nbsp;' . IO::prepout_ml_html($this->f_updated_date) . '</td>
						 </tr>
						<tr>
						   <td width="25%" align="right" valign="middle" class="text_label">Last Contacted:&nbsp;</td>
						   <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_last_contacted) . '</td>
					  	</tr>
					</table></td>
					<!--ENDDDDDDD updated/created info area-->
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
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    	<tr>
                        	<td width="25%" align="right" valign="top">&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input type="submit" name="Just Called" value="Just Called" class="button"></td>
                        </tr>
                    </table></td>
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
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
$page_supplier_view = new PageSupplierView();
$page_supplier_view->run();

?>