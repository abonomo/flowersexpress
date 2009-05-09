<?php

require_once('framework.php');

class PageSalesMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_report_sales_menu.php';
	
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_REPORT);
		
		//echo inner area html here
		//  TODO : Search algorithm
			echo ('
			<form name="form">
				<div class="text_title">Sales Report Menu</div>
				<input name="f_report_search" class="textbox" type="text" /><input value="Search" type="button" onclick="document.location=(\'page_report_list.php?f_search=\' + form.f_report_search.value)" /><br>
				<a href="page_report_sales_order_daily.php">Daily Sales Order</a><br>
				<a href="page_report_sales_order_monthly.php">Monthly Sales Order</a><br>
				<a href="page_report_sales_order_range.php">Sales Order : Range</a><br>
				<a href="page_report_customer.php">Customer Information</a><br>
			</form>
			');
			
			



		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageSalesMenu();
$page_template->run();

?>