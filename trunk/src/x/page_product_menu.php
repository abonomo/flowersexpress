<?php

require_once('framework.php');

class PageProductMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_product_menu.php';
	
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
		echo "he";
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PRODUCTS);
		
		//echo inner area html here
		//  TODO : Search algorithm
			echo ('
			<form name="form">
				<div class="text_title">Product Menu</div>
				<input name="f_product_search" class="textbox" type="text" /><input value="Search" type="button" onclick="document.location=(\'page_product_list.php?f_search=\' + form.f_product_search.value)" /><br>
				<a href="page_product_add_edit.php">Add Product</a><br>
				<a href="page_product_list.php">List All Products</a><br>
			</form>
			');
			
			



		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageProductMenu();
$page_template->run();

?>