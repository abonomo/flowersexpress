<?php

require_once('framework.php');

class PageShipperMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_shipper_menu.php';
	
	//*** MEMBERS ***
	
	
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);	//CHANGE required authorization level for this page, ADMIN is the strictest
	
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SHIPPERS);
		
		//echo inner area html here
		//  TODO : Search algorithm
			echo ('
				<form name="form">
				<div class="text_title">Shipper Menu</div>	
				<input name="f_shipper_search" class="textbox" type="text" /><input value="Search" type="button" onclick="document.location=(\'page_shipper_list.php?f_search=\' + form.f_shipper_search.value)" /><br>
			');
			
			// ** READ/WRITE required to view  **
			if (LoginManager::meets_auth_level(LoginManager::$AUTH_READ_WRITE) == true)
			{
				echo ('
					<a href="page_shipper_add_edit.php">Add Shipper</a><br>
				');
			}
			
			echo ('
				<a href="page_shipper_list.php">List All Shipper</a><br>
				</form>
			');
			
			



		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageShipperMenu();
$page_template->run();

?>