<?php

require_once('framework.php');

class PageEmployeeView
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_employee_view.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_email;
	private $f_auth_level;
	private $f_first_name;
	private $f_last_name;
	private $f_dept_name;
	private $f_office_location;
	private $f_office_phone;
	private $f_cell_phone;
	private $f_fax_number;
	private $f_trash_flag;
	
	private $f_created_id;
	private $f_updated_id;	
	
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
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);
	
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
			//QUERY for the employee info
			$emp_info = DB::get_single_row_fq
			('
				SELECT  employees.icode, 
						employees.first_name, 
						employees.last_name, 
						employees.dept_name, 
						employees.office_phone_number, 
						employees.cell_phone_number, 
						employees.fax_number, 
						employees.office_location,  
						employees.email, 
						employees.auth_level, 
						employees.trash_flag,
						
						employees.created_employee_id, 
						employees.updated_employee_id,
						employees.created_date,
						employees.updated_date
						
				FROM employees
				WHERE employees.id=\'' . $this->f_id . '\''
			);
						
			$this->f_icode 					= $emp_info['icode'];
			$this->f_email 					= $emp_info['email'];
			$this->f_auth_level 			= $emp_info['auth_level'];
			$this->f_first_name 			= $emp_info['first_name'];
			$this->f_last_name 				= $emp_info['last_name'];
			$this->f_dept_name 				= $emp_info['dept_name'];
			$this->f_office_location 		= $emp_info['office_location'];
			$this->f_office_phone 			= $emp_info['office_phone_number'];
			$this->f_cell_phone 			= $emp_info['cell_phone_number'];
			$this->f_fax_number 			= $emp_info['fax_number'];
			$this->f_trash_flag 			= $emp_info['trash_flag'];	
			
			$this->f_created_employee_id 	= $emp_info['created_employee_id'];
			$this->f_updated_employee_id 	= $emp_info['updated_employee_id'];
			
			$this->f_created_date 			= $emp_info['created_date'];
			$this->f_updated_date 			= $emp_info['updated_date'];
			
			//QUERY for the uodated employee info
			$emp_info_up = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM employees
				WHERE employees.id=\'' . $this->f_updated_employee_id . '\''
			);
			
			$this->f_updated_first 			= $emp_info_up['first_name'];
			$this->f_updated_last 			= $emp_info_up['last_name'];		
			
			//QUERY for the created employee info
			$emp_info_cre = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM employees
				WHERE employees.id=\'' . $this->f_created_employee_id . '\''
			);

			$this->f_created_first		 	= $emp_info_cre['first_name'];
			$this->f_created_last 			= $emp_info_cre['last_name'];
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_EMPLOYEES);
		
		//echo inner area html
		Echo ('	
			<div align="center">
			  <form name="form1" method="post" action="page_employee_view.php">
				<table width="600" border="0" cellpadding="0" cellspacing="0">

				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle">&nbsp;</td>
						  <td width="75%" align="left" valign="middle" class="text_title">View Employee</td>
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
					echo (' <a href="page_employee_add_edit.php?f_mode=edit&amp;f_id=' . $this->f_id . '">Edit</a>');
					echo ('  ');
					echo (' <img src="../img/icon_delete.gif"/> ');
					echo (' <a href="#" onclick="if(window.confirm(\'Are you sure you want to delete this entry?\')) { document.location=\'page_employee_delete.php?f_id=' . $this->f_id .'\'; } return false;"/>Delete</a></td>');
				}
				else
				{
					echo (' <img src="../img/icon_undelete.gif"/> ');
					echo (' <a href="page_employee_undelete.php?f_id=' . $this->f_id . '">Undelete</a></td>');
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
						  
						  //if the product has been deleted, print trash icon
						  if( $this->f_trash_flag == '1' )
						  {
							echo (' <img src="../img/icon_trash.gif"/> ');
							echo (' (This item has been deleted!)');
						  }
						
						  echo('</td>
						</tr>
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Name:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_first_name) . '&nbsp;' . IO::prepout_ml_html($this->f_last_name) .'</td>
						</tr>
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Department:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_dept_name) . '</td>
						</tr>
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Office Location:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_office_location) . '</td>
						</tr>
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Authorization Level:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_auth_level) . '</td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
						</tr>	
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Email Address:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_email) . '</td>
						</tr>
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Office Phone:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_office_phone) . '</td>
						</tr>
						
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Cell Phone:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_cell_phone) . '</td>
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
$page_employee_view = new PageemployeeView();
$page_employee_view->run();

?>