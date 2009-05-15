<?php
/*
 * General TODO's:
 * TODO: ensure m_id is not -1 before doing anything
 * TODO: implement at way to handle bad input
 * TODO: implement showOutput function
 */
require_once('framework.php');

class PageEmployeeChangePassword
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_template.php';
	
	//*** MEMBERS ***
	private $m_id = -1;
	private $m_oldPassword;
	private $m_err_msg = array();
	
	private $f_action;
	
	private $f_checkOldPassword;
	private $f_newPassword;
	private $f_confirmNewPassword;
	
	//*** FUNCTIONS ***
	//constructor, securely pass id of employee password to change
	public function PageEmployeeChangePassword($id)
	{
		$this->m_id = id;
	}
	
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_LOGIN);	//CHANGE required authorization level for this page, ADMIN is the strictest
	
		//TODO: check and make sure $this->m_id != -1; what to do if it is?? just return?
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output($this->m_err_msg);
	}
	
	private function get_input()
	{
		$this->f_action = IO::get_input_sl_g('f_action','string');
		
		if($this->f_action == 'submit')
		{
			$this->m_oldPassword = DB::get_single_row_fq('SELECT password FROM employees WHERE id=\'' . $this->m_id . '\'');
			
			$this->f_checkOldPassword 	= IO::get_input_sl_pg('f_checkOldPassword', 'string');
			$this->f_newPassword 		= IO::get_input_sl_pg('f_newPassword', 'string');
			$this->f_confirmNewPassword	= IO::get_input_sl_pg('f_confirmNewPassword', 'string');
		}
	}
	
	private function verify_input()
	{
		if(strlen($this->f_checkOldPassword) > Config::$DEFAULT_VARCHAR_LEN)
			$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Old Password Length is too long.';
		if(strlen($this->f_newPassword) > Config::$DEFAULT_VARCHAR_LEN
			|| strlen($this->f_confirmNewPassword > Config::$DEFAULT_VARCHAR_LEN))
			$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: New Password Length is too long.';
		
		//TODO: what to do if one of these cases happens??
		if($this->f_checkOldPassword != $this->m_oldPassword)
		{
			$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Inccorect Current Password.';
			//TODO: anything else??...
		}
		if($this->f_confirmNewPassword != $this->f_newPassword)
		{
			$this->m_err_msg[sizeof($this->m_err_msg)] = 'Error: Passwords  do not match.';
			//TODO: anything else??...
		}
	}
	
	private function process_input()
	{
		DB::send_query
		(
			'UPDATE employees 
			SET password=\'' . $this->f_newPassword . '\'
			WHERE id=\'' . $this->m_id . '\''
		);
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_HOME);
		
		//TODO:...
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}
?>