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
		//  printing the calendar and getting date inputs
			echo ('
			<div align="center">
			<table cellspacing="0" cellpadding="0" border="0"><tr><td>			
			<form name="form_report_menu" method="post" action="page_sales_order_report.php">
				<div class="text_title">Generate Sales Order Report</div>
				<br>
				Select Date Range:<br>
				<input name="f_report_start" class="textbox" type="text" /> <a href="#" onclick="calbeg.select(document.forms[0].f_report_start, \'datexx\', \'MM/dd/yyyy\'); return false; " name="datexx" id="datexx"> Select Begin Date</a>
				<div id="testdiv1" style="position:absolute;visibility:hidden;"></div><br>
				
				<input name="f_report_end" class="textbox" type="text" /> <a href="#" onclick="calend.select(document.forms[0].f_report_end, \'datex\', \'MM/dd/yyyy\'); return false; " name="datex" id="datex"> Select End Date</a>
				<div id="testdiv2" style="position:absolute;visibility:hidden;"></div><br><br>
				
				<input name="f_excel" type="checkbox" value="true"/> Download as Excel<br><br>
				
				<input type="submit" class="button" value="Generate!"><br>
				
			</form>
			</td></tr></table>
			</div>
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