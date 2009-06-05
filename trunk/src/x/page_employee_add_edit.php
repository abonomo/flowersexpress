<?php

/*
 * General TODO's for this page:
 * TODO: implement a (good) method for admin/users to modify passwords
 * TODO: string comparison for checking passwords - current code doesn't work and therefore is commented out
 * TODO: fix password fields for admin
 * TODO: better way to change modifiable fields for non-admin users?
 * TODO: better way to show output?
 */

require_once('framework.php');

class PageEmployeeAddEdit
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_employee_add_edit.php';
	
	//*** MEMBERS ***
	private $m_err_msg = array();
	private $m_isChangePassword = false;
	
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
	private $f_title;
	private $f_old_password;
	private $f_new_password;
	private $f_confirm_password;
	
	//*** FUNCTIONS ***
	private function isAdmin()
	{
		return LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN);
	}
	
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_LOGIN);	
		
		//TODO: is this secure?
		if(!($this->isAdmin()))
		{
			$this->f_mode = 'edit';
			$this->f_id = LoginManager::get_id();
		}
		
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output($this->m_err_msg);
	}
	
	private function get_input()
	{
		//get mode if admin, otherwise mode is set to 'edit'
		if($this->isAdmin())
			$this->f_mode = IO::get_input_sl_g('f_mode','string');	
				
		$this->f_action = IO::get_input_sl_g('f_action','string');
		
		//if submitting in ADD or EDIT mode, get fields from form
		if($this->f_action == 'submit')
		{
			//if submitting in EDIT mode, additionally get the employee id to edit, only if admin
			if(($this->f_mode == 'edit' || $this->f_mode == 'delete') && $this->isAdmin())
			{
				//get id of employee to edit
				$this->f_id = IO::get_input_sl_pg('f_id','string');
			}
			
			//if admin
			if($this->isAdmin())
			{
				$this->f_auth_level = IO::get_input_sl_pg('f_auth_level', 'integer');
				$this->f_dept_name	= IO::get_input_sl_pg('f_dept_name','string');
				$this->f_icode 		= IO::get_input_sl_pg('f_icode', 'string');
				$this->f_id 		= IO::get_input_sl_pg('f_id', 'integer');
				$this->f_title		= IO::get_input_sl_pg('f_title', 'string');
			}
			
			$this->f_cell_phone_number 		= IO::get_input_sl_pg('f_cell_phone_number','string');
			$this->f_email 					= IO::get_input_sl_pg('f_email','string');
			$this->f_fax_number				= IO::get_input_sl_pg('f_fax_number','string');
			$this->f_first_name 			= IO::get_input_sl_pg('f_first_name', 'string');
			$this->f_last_name 				= IO::get_input_sl_pg('f_last_name', 'string');
			$this->f_office_location		= IO::get_input_sl_pg('f_office_location','string');
			$this->f_office_phone_number	= IO::get_input_sl_pg('f_office_phone_number','string');
			$this->f_old_password			= IO::get_input_sl_pg('f_old_password','string');
			$this->f_new_password			= IO::get_input_sl_pg('f_new_password','string');
			$this->f_confirm_password		= IO::get_input_sl_pg('f_confirm_password','string');
			
			if(strlen($this->f_old_password) > 0 || strlen($this->f_new_password) > 0 || strlen($this->f_confirm_password) > 0)
				$this->m_isChangePassword = true;
		} 
		//if NOT submitting, but in EDIT mode, fill the fields from database data
		else if($this->f_mode == 'edit')
		{
			if($this->isAdmin())
			{
				//get id of employee to edit
				$this->f_id = IO::get_input_sl_pg('f_id','integer');
			}
						
			//get values from database
			if($this->isAdmin())
			{
				$employee_info = DB::get_single_row_fq
				(
					'SELECT icode, email, auth_level, first_name, last_name, dept_name, office_location, office_phone_number, cell_phone_number, fax_number, title
					FROM employees WHERE id=\'' . $this->f_id . '\''
				);
			}
			else
			{
				$employee_info = DB::get_single_row_fq
				(
					'SELECT email, first_name, last_name, office_location, office_phone_number, cell_phone_number, fax_number
					FROM employees WHERE id=\'' . $this->f_id . '\''
				);
			}
			
			
			if($this->isAdmin())
			{
				$this->f_auth_level	= $employee_info['auth_level'];
				$this->f_dept_name	= $employee_info['dept_name'];
				$this->f_icode		= $employee_info['icode'];
				$this->f_title		= $employee_info['title'];
			}
			
			$this->f_cell_phone_number 		= $employee_info['cell_phone_number'];
			$this->f_email					= $employee_info['email'];
			$this->f_fax_number				= $employee_info['fax_number'];
			$this->f_first_name 			= $employee_info['first_name'];
			$this->f_last_name				= $employee_info['last_name'];
			$this->f_office_location		= $employee_info['office_location'];
			$this->f_office_phone_number	= $employee_info['office_phone_number'];
		}
		//if NOT submitting, and in ADD mode, do nothing (empty textboxes)
	}
	
	private function verify_input()
	{
		if($this->f_action == 'submit')
		{
			//check these for both add and edit mode
			if(strlen($this->f_cell_phone_number) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Cell Phone Number is too long.';
			if(strlen($this->f_dept_name) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Department Name is too long.';
			if(strlen($this->f_email) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Email is too long.';
			if(strlen($this->f_fax_number) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Fax Number is too long.';
			if(strlen($this->f_first_name) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: First Name is too long.';
			if(strlen($this->f_icode) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: ID Code is too long.';
			if(strlen($this->f_last_name) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Last Name is too long.';
			if(strlen($this->f_office_location) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Office Location is too long.';
			if(strlen($this->f_office_phone_number) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Office Phone Number is too long.';
			if(strlen($this->f_title) > Config::$DEFAULT_VARCHAR_LEN)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Title is too long.';
			if(($this->f_auth_level < 1 || $this->f_auth_level > 4) && $this->isAdmin())
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Invalid Authorization Level.';
				
			if($this->f_action != 'edit' && strlen($this->f_new_password) < 6)
				$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Password must be at least 6 characters.';
		
			//change password stuff
			if($this->m_isChangePassword)
			{
			
				//verify length
				if(strlen($this->f_old_password) > Config::$DEFAULT_VARCHAR_LEN)
					$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Old Password Length is too long.';
				if(strlen($this->f_new_password) > Config::$DEFAULT_VARCHAR_LEN
					|| strlen($this->f_confirm_password) > Config::$DEFAULT_VARCHAR_LEN)
					$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: New Password Length is too long.';
				
				//verify other password stuff
				if((!LoginManager::verify_password_again($this->f_old_password, $this->f_id)) && $this->f_mode == 'edit')
					$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Incorrect Current Password.';
				if($this->f_confirm_password != $this->f_new_password)
					$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Passwords  do not match.';
			}
				
			//outputs any errors
			if(sizeof($this->m_err_msg) > 0) 
				$this->show_output($this->m_err_msg);
		}
		
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
				$this->f_office_phone_number . ' ' .
				$this->f_title			
			);
			
			//edit mode submit
			if($this->f_mode == 'edit')
			{
				if($this->m_isChangePassword)
					LoginManager::change_password($this->f_new_password, $this->f_id);
					
				//update database				
				if($this->isAdmin())
				{
					DB::send_query
					(
						'UPDATE employees SET
						auth_level=\'' . $this->f_auth_level . '\',
						cell_phone_number=\'' . $this->f_cell_phone_number . '\',
						dept_name=\'' . $this->f_dept_name . '\',
						email=\'' . $this->f_email . '\',
						fax_number=\'' . $this->f_fax_number . '\',
						first_name=\'' . $this->f_first_name . '\',
						icode=\'' . $this->f_icode . '\',
						last_name=\'' . $this->f_last_name . '\',
						office_location=\'' . $this->f_office_location . '\',
						office_phone_number=\'' . $this->f_office_phone_number . '\',
						title=\'' . $this->f_title . '\',
						updated_employee_id=\'' . LoginManager::get_id() . '\',
						search_words=\'' . $search_words . '\',
						updated_date = NOW()
						WHERE id=\'' . $this->f_id . '\''
					);
				}
				else
				{
					DB::send_query
					(
						'UPDATE employees SET
						cell_phone_number=\'' . $this->f_cell_phone_number . '\',
						email=\'' . $this->f_email . '\',
						fax_number=\'' . $this->f_fax_number . '\',
						first_name=\'' . $this->f_first_name . '\',
						last_name=\'' . $this->f_last_name . '\',
						office_location=\'' . $this->f_office_location . '\',
						office_phone_number=\'' . $this->f_office_phone_number . '\',
						updated_employee_id=\'' . LoginManager::get_id() . '\',
						search_words=\'' . $search_words . '\',
						updated_date = NOW()
						WHERE id=\'' . $this->f_id . '\''
					);
				}
				
			}
			else if($this->f_mode == 'delete' && $this->isAdmin())
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
			else if($this->isAdmin())
			{
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
						title,
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
						\'' . $this->f_title . '\',
						\'' . LoginManager::get_id() . '\',
						\'' . LoginManager::get_id() . '\',
						NOW(),
						NOW(),
						\'' . $search_words . '\'
					)'	
				);
				
				//fetch the id of the new row
				$this->f_id = DB::get_field_fq('SELECT LAST_INSERT_ID()');
				
				LoginManager::change_password($this->f_new_password, $this->f_id);
			}
			//successful insert or update
			IO::navigate_to('page_employee_view.php?f_id=' . $this->f_id);
		}
	}
	
	private function show_admin_output()
	{
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
                              <td width="25%" align="right" valign="middle" class="text_label">Title:&nbsp;</td>
                              <td width="75%" align="left" valign="middle"><input name="f_title" type="text" size="32" class="textbox" value="' . IO::prepout_sl($this->f_title, false) . '"></td>
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
								');
								
								// Select the value that the user currently has
								if ($this->f_auth_level == LoginManager::$AUTH_ADMIN)
								{
									echo('								
                            		<option value="' . LoginManager::$AUTH_READ_ONLY . '">Read Only</option>
                            		<option value="' . LoginManager::$AUTH_READ_WRITE . '"> Read/Write</option>
                            		<option selected="yes" value="' . LoginManager::$AUTH_ADMIN . '">Admin</option>
									');
								}
								else if ($this->f_auth_level == LoginManager::$AUTH_READ_ONLY)
								{
									echo('
                            		<option selected="yes" value="' . LoginManager::$AUTH_READ_ONLY . '">Read Only</option>
                            		<option value="' . LoginManager::$AUTH_READ_WRITE . '"> Read/Write</option>
                            		<option value="' . LoginManager::$AUTH_ADMIN . '">Admin</option>
									');
								}else if ($this->f_auth_level == LoginManager::$AUTH_READ_WRITE)
								{
									echo('
                            		<option value="' . LoginManager::$AUTH_READ_ONLY . '">Read Only</option>
                            		<option selected="yes" value="' . LoginManager::$AUTH_READ_WRITE . '"> Read/Write</option>
                            		<option value="' . LoginManager::$AUTH_ADMIN . '">Admin</option>
									');
								}else
								{
									echo('
                            		<option value="' . LoginManager::$AUTH_READ_ONLY . '">Read Only</option>
                            		<option value="' . LoginManager::$AUTH_READ_WRITE . '"> Read/Write</option>
                            		<option value="' . LoginManager::$AUTH_ADMIN . '">Admin</option>
									');
								}
								
								echo('
                            	</select>
			     </table></td> 
                          </tr>
			  <tr>
			     <td>&nbsp;</td>
			  </tr>
                          <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0"> ');
			if($this->f_mode == 'edit')
			{
				echo ('
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Old Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_old_password" type="password" size="40" class="textbox" value="' . IO::prepout_sl($this->f_old_password, false) . '"></td>
                          </tr> ');
			}
				echo ('
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">New Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_new_password" type="password" size="40" class="textbox" value="' . IO::prepout_sl($this->f_new_password, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Confirm Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_confirm_password" type="password" size="40" class="textbox" value="' . IO::prepout_sl($this->f_confirm_password, false) . '"></td>
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
	}
	
	private function show_user_output()
	{
		echo('			  
                      <tr>
                        <td>&nbsp;</td>
                      </tr>						
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                            <td width="25%" align="right" valign="middle" class="text_label">Office Location:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_office_location" type="text" size="40" class="textbox" value="' . IO::prepout_sl($this->f_office_location, false) . '"></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
					  <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0"> ');
			if($this->f_mode == 'edit')
			{
				echo ('
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Old Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_old_password" type="password" size="40" class="textbox" value="' . IO::prepout_sl($this->f_old_password, false) . '"></td>
                          </tr> ');
			}
				echo ('
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">New Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_new_password" type="password" size="40" class="textbox" value="' . IO::prepout_sl($this->f_new_password, false) . '"></td>
                          </tr>
                          <tr>
                            <td width="25%" align="right" valign="middle" class="text_label">Confirm Password:&nbsp;</td>
                            <td width="75%" align="left" valign="middle"><input name="f_confirm_password" type="password" size="40" class="textbox" value="' . IO::prepout_sl($this->f_confirm_password, false) . '"></td>
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
	}
	
	private function show_output($err_msg)
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_EMPLOYEES);
		
		//echo inner area html here
		if($this->f_mode == 'edit')
		{
			if($this->isAdmin())
			{
				echo
				('
	               <div align="center">
	                  <form name="form1" method="post" action="page_employee_add_edit.php?f_action=submit&f_mode=' . IO::prepout_url($this->f_mode) . (($this->f_mode == 'edit') ? ('&f_id=' . IO::prepout_url($this->f_id)) : '') . '">
	                    <table width="600" border="0" cellpadding="0" cellspacing="0">
	                      <tr>
	                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                            <tr>
	                              <td width="25%" align="right" valign="middle">&nbsp;</td>
	                              <td width="75%" align="left" valign="middle" class="text_title">Edit Employee</td>
	                            </tr>
	                        </table></td>
	                      </tr>	
				');
			}
			else
			{
				echo
				('
	               <div align="center">
	                  <form name="form1" method="post" action="page_employee_add_edit.php?f_action=submit&f_mode=' . IO::prepout_url($this->f_mode) . (($this->f_mode == 'edit') ? ('&f_id=' . IO::prepout_url($this->f_id)) : '') . '">
	                    <table width="600" border="0" cellpadding="0" cellspacing="0">
	                      <tr>
	                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                            <tr>
	                              <td width="25%" align="right" valign="middle">&nbsp;</td>
	                              <td width="75%" align="left" valign="middle" class="text_title">Edit Profile</td>
	                            </tr>
	                        </table></td>
	                      </tr>	
				');
			}
			
		}
		else
		{
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
		}
			
		
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
		
		if($this->isAdmin())
			$this->show_admin_output();
		else
			$this->show_user_output();	
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}

}

//create an instance of the page and run it
$page_employee_add_edit = new PageEmployeeAddEdit();
$page_employee_add_edit->run();

?>