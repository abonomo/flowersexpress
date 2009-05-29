<?php

require_once('framework.php');

class PageSupplierMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_supplier_menu.php';
	
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SUPPLIERS);
		
		//echo inner area html here
			echo ('
				<div align="center">
			
					<div class="text_title">Supplier Actions Menu</div><br>
			');
			
			// ** READ/WRITE required to view  **
			if (LoginManager::meets_auth_level(LoginManager::$AUTH_READ_WRITE) == true)
			{
			echo ('
						<a href="page_supplier_add_edit.php">Add Supplier</a><br>
				');
			}
			
			echo ('
					<a href="page_supplier_list.php">List All Suppliers</a><br>
					<br>
					<form method="post" action="page_supplier_list.php">
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
$page_template = new PageSupplierMenu();
$page_template->run();

?>