<?php

require_once('framework.php');

class PageEmployeeMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_employee_menu.php';
	
	//*** MEMBERS ***
	
	
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output();
	}
	
	private function get_input()
	{
	
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function process_input()
	{

	}
	
	private function show_output($err_msg = '')
	{
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_EMPLOYEES);
		
		//echo inner area html here
		//  TODO : Search algorithm
			echo ('
			<form name="form">
				<div class="text_title">Employee Menu</div>
				<input name="f_employee_search" class="textbox" type="text" /><input value="Search" type="button" onclick="document.location=(\'page_employee_list.php?f_search=\' + form.f_employee_search.value)" /><br>
			');
			
			// ** ADMIN required to view - can add employee **
			if (LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN) == true)
			{
				echo ('
				<a href="page_employee_add_edit.php">Add Employee</a><br>
				');
			}
			echo
			('
				<a href="page_employee_add_edit.php?f_mode=edit&f_id=' . LoginManager::get_id() . '">Edit Profile</a><br>
			');
			echo ('
				<a href="page_employee_list.php">List All Employees</a><br>
			</form>
			');
					
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageEmployeeMenu();
$page_template->run();

?>