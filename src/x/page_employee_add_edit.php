<?php

/*
 * General TODO's for this page:
 * TODO: implement a (good) method for admin to modify passwords
 * TODO: implement a way so that both admins and employees can access this page
 		* admins can modify everything
 		* employees can only modify themselves
 		* employees cannot modify their auth_level or employee ID or department name
 * TODO: verify input function...
 */

require_once('framework.php');

class PageEmployeeAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_employee_add_edit.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_email;
	private $f_password;
	private $f_auth_level;
	private $f_first_name;
	private $f_last_name;
	private $f_dept_name;
	private $f_office_location;
	private $f_office_phone_number;
	private $f_cell_phone_number;
	private $f_fax_number;
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_ADMIN);	//CHANGE required authorization level for this page, ADMIN is the strictest
	
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
			//if submitting in EDIT mode, additionally get the employee id to edit
			if($this->f_mode == 'edit' || $this->f_mode == 'delete')
			{
				//get id of employee to edit
				$this->f_id = IO::get_input_sl_g('f_id','string');
			}
			
			$this->f_auth_level 			= IO::get_input_sl_pg('f_auth_level', 'integer');
			$this->f_cell_phone_number 		= IO::get_input_sl_pg('f_cell_phone_number','string');
			$this->f_dept_name				= IO::get_input_sl_pg('f_dept_name','string');
			$this->f_email 					= IO::get_input_sl_pg('f_email','string');
			$this->f_fax_number				= IO::get_input_sl_pg('f_fax_number','string');
			$this->f_first_name 			= IO::get_input_sl_pg('f_first_name', 'string');
			$this->f_icode 					= IO::get_input_sl_pg('f_icode', 'string');
			$this->f_id 					= IO::get_input_sl_pg('f_id', 'integer');
			$this->f_last_name 				= IO::get_input_sl_pg('f_last_name', 'string');
			$this->f_office_location		= IO::get_input_sl_pg('f_office_location','string');
			$this->f_office_phone_number	= IO::get_input_sl_pg('f_office_phone_number','string');
			
			//TODO: change password so that it uses 1-way md5 encryption
			//NOTE: may want to make change a password as an entirely new page
			$this->f_password = IO::get_input_sl_p('f_password', 'string');
		} 
		//if NOT submitting, but in EDIT mode, fill the fields from database data
		else if($this->f_mode == 'edit')
		{
			//get id of employee to edit
			$this->f_id = IO::get_input_sl_pg('f_id','integer');
			
			//get values from database
			$employee_info = DB::get_single_row_fq
			(
				'SELECT icode, email, password, auth_level, first_name, last_name
				FROM employees WHERE id=\'' . $this->f_id . '\''
			);
			
			$this->f_auth_level 			= $employee_info['auth_level'];
			$this->f_cell_phone_number 		= $employee_info['cell_phone_number'];
			$this->f_dept_name				= $employee_info['dept_name'];
			$this->f_email					= $employee_info['email'];
			$this->f_fax_number				= $employee_info['fax_number'];
			$this->f_first_name 			= $employee_info['first_name'];
			$this->f_icode					= $employee_info['icode'];
			$this->f_last_name				= $employee_info['last_name'];
			$this->f_office_location		= $employee_info['office_location'];
			$this->f_office_phone_number	= $employee_info['office_phone_number'];
			$this->f_password				= $employee_info['password']; //TODO: again, will need to do something different w/ password
		}
		//if NOT submitting, and in ADD mode, do nothing (empty textboxes)
	}
	
	private function verify_input()
	{
		//TODO...
		
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
			$search_words = DB::encode_small_words_store
			(
				$this->f_cell_phone_number . ' ' .
				$this->f_dept_name . ' ' .
				$this->f_email . ' ' .
				$this->f_fax_number . ' ' .
				$this->f_first_name . ' ' .
				$this->f_icode . ' ' .
				$this->f_last_name . ' ' .
				$this->f_office_location . ' ' .
				$this->f_office_phone_number				
			);
			
			//edit mode submit
			if($this->f_mode == 'edit')
			{
				//update database
				//TODO: password stuff...
				DB::send_query
				(
					'UPDATE employees SET
					auth_level=\'' . $this->f_auth_level . '\',
					cell_phone_number=\'' . $this->f_cell_phone_number . '\',
					dept_name=
					email=\'' . $this->f_email . '\',
					fax_number=\'' . $this->f_fax_number . '\',
					first_name=\'' . $this->f_first_name . '\',
					icode=\'' . $this->f_icode . '\',
					last_name=\'' . $this->f_last_name . '\',
					office_location=\'' . $this->f_office_location . '\',
					office_phone_number=\'' . $this->f_office_phone_number . '\',
					password=\'' . $this->f_password . '\',
					updated_employee_id=\'' . LoginManager::get_id() . '\',
					search_words=\'' . $search_words . '\',
					updated_date = NOW()'
				);
			}
			else if($this->f_mode == 'delete')
			{
				//update trash flag
				DB::send_query
				(
					'UPDATE employees SET
					trash_flag = 1
					WHERE id=\'' . $this->f_id . '\''
				);
			}
			//add mode submit
			else
			{
				//TODO: password stuff...
				//insert
				DB::send_query
				(
					'INSERT INTO employees
					(
						auth_level,
						cell_phone_number,
						dept_name,
						email,
						fax_number,
						first_name,
						icode,
						last_name,
						office_location,
						office_phone_number,
						password,
						created_employee_id,
						updated_employee_id,
						created_date,
						updated_date,
						search_words
					)
					VALUES
					(
						\'' . $this->f_auth_level . '\',
						\'' . $this->f_cell_phone_number . '\',
						\'' . $this->f_dept_name . '\',
						\'' . $this->f_email . '\',
						\'' . $this->f_fax_number . '\',
						\'' . $this->f_first_name . '\',
						\'' . $this->f_icode . '\',
						\'' . $this->f_last_name . '\',
						\'' . $this->f_office_location . '\',
						\'' . $this->f_office_phone_number . '\',
						\'' . $this->f_password . '\',
						\'' . LoginManager::get_id() . '\',
						\'' . LoginManager::get_id() . '\',
						NOW(),
						NOW(),
						\'' . $search_words . '\'
					)'	
				);
				
				//fetch the id of the new row
				$this->f_id = DB::get_field_fq('SELECT LAST_INSERT_ID()');
			}
			//successful insert or update
			IO::navigate_to('page_employee_view.php?f_id=' . $this->f_id);
		}
	}
	
	private function show_output($err_msg)
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_EMPLOYEES);
		
		//echo inner area html here
		echo
		('
               <div align="center">
                  <form name="form1" method="post" action="page_employee_add_edit.php?f_action=submit&f_mode=' . IO::prepout_url($this->f_mode) . (($this->f_mode == 'edit') ? ('&f_id=' . IO::prepout_url($this->f_id)) : '') . '">
                    <table width="600" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="25%" align="right" valign="middle">&nbsp;</td>
                              <td width="75%" align="left" valign="middle" class="text_title">Add New Employee</td>
                            </tr>
                        </table></td>
                      </tr>	
		');	
		
		if (sizeof($err_msg) > 0) 
		{
			foreach ($err_msg as $entity)
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
                              <td width="25%" align="right" valign="middle" class="text_label">First Name:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_first_name" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_first_name, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Last Name:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_last_name" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_last_name, false) . '"></td>
                            </tr>
                            <tr>
                              <td width="25%" align="right" valign="middle" class="text_label">Email:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_email" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_email, false) . '"></td>
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
                            <td width="25%" align="right" valign="middle" class="text_label">Department Name:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_dept_name" type="text" size="40" class="textbox" value="' . IO::prepout_sl($this->f_dept_name, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Office Location:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_office_location" type="text" size="40" class="textbox" value="' . IO::prepout_sl($this->f_office_location, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Authorization Level:&nbsp;</td>
                            <td width="75%" align="left" valign="middle">
                            	<select name="f_auth_level">
                            		<option selected="yes" value="' . LoginManager::$AUTH_LOGIN . '">Login</option>
                            		<option value="' . LoginManager::$AUTH_READ_ONLY . '">Read Only</option>
                            		<option value="' . LoginManager::$AUTH_READ_WRITE . '"> Read/Write</option>
                            		<option value="' . LoginManager::$AUTH_ADMIN . '">Admin</option>
                            	</select>
                            </td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_password" type="text" size="24" class="textbox" value="' . IO::prepout_sl($this->f_password, false) . '"></td>
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
$page_employee_add_edit = new PageEmployeeAddEdit();
$page_employee_add_edit->run();

?>