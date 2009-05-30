<?php

require_once('framework.php');

class PageReportMenu
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_report_menu.php';
	
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
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_REPORTS);
		
		//echo inner area html here
		//  TODO : Search algorithm
			echo ('
			<div align="center">
			<table cellspacing="0" cellpadding="0">
			<tr><td align="left">
			<form name="form">
				<div class="text_title">Generate Reports</div><br>
				<a href="page_report_sales_menu.php">Sales Order Report</a><br>
				<a href="page_report_purchase_menu.php">Purchase Order Report</a><br>
				<a href="page_product_report.php">Product Information Report</a><br>
				<a href="page_purchase_comp_list.php">Available Product Report</a><br>
				<a href="page_customer_report.php">Customer Information Report</a><br>
				<a href="page_suppliers_report.php">Suppliers Information Report</a><br>
				<a href="page_shipper_report.php">Shipper Information Report</a><br>
			</form>
			</td></tr>
			</table>
			</div>
			');
			
			



		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageReportMenu();
$page_template->run();

?>
