<?php

require_once('framework.php');

class PagePurchaseMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_report_purchase_menu.php';
	
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_REPORTS);
		
		//echo inner area html here
		//  TODO : Search algorithm
			echo ('
			<form name="form">
				<div class="text_title">Purchase Report Menu</div>
				<input name="f_report_search" class="textbox" type="text" /><input value="Search" type="button" onclick="document.location=(\'page_report_list.php?f_search=\' + form.f_report_search.value)" /><br>
				<a href="page_report_purchase_order_daily.php">Daily Purchase Order</a><br>
				<a href="page_report_purchaes_order_monthly.php">Monthly Purchase Order</a><br>
				<a href="page_report_purchase_order_range.php">Purchase Order : Range</a><br>
				<a href="page_report_supplier.php">Supplier Information</a><br>
			</form>
			');
			
			



		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PagePurchaseMenu();
$page_template->run();

?>