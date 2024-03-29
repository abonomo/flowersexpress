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
		
		//different type of employee list for admins vs. everybody else
		if (LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN))
		{
			$employee_list_page = 'page_employee_list.php';
		}
		else
		{
			$employee_list_page = 'page_employee_list_limited.php';
		}
		
		//echo inner area html here
			echo ('
				<div align="center">
			
					<div class="text_title">Employee Actions Menu</div><br>
					<a href="page_employee_add_edit.php?f_mode=edit&f_id=' . LoginManager::get_id() . '">Edit My Profile</a><br>
			');
			
			// ** READ/WRITE required to view  **
			if (LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN) == true)
			{
			echo ('
						<a href="page_employee_add_edit.php">Add Employee</a><br>
				');
			}
			
			echo ('
					<a href="' . $employee_list_page . '">List All Employees</a><br>
					<br>
					<form method="post" action="' . $employee_list_page . '">
						<input name="f_search" class="textbox" type="text" />
						<input value="Search" type="submit" class="button"/><br>
					</form>				
				
				</div>
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