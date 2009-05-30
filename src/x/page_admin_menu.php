<?php

require_once('framework.php');

class PageAdminMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_admin_menu.php';
	
	//*** MEMBERS ***
	
	
	
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_ADMIN);
		
		//echo inner area html here
			echo ('
				<div align="center">
			
					<div class="text_title">Employee Management</div><br>
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
					<a href="page_employee_list.php">List All Employees</a><br>
					<br>
					<form method="post" action="page_employee_list.php">
						<input name="f_search" class="textbox" type="text" />
						<input value="Search" type="submit" class="button"/><br>
					</form>				
					<br>
					<div class="text_title">Special Admin Commands </div>
					<a href="page_admin_empty_trash.php">Empty Trash</a><br>				
				</div>
			');	


		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageAdminMenu();
$page_template->run();

?>