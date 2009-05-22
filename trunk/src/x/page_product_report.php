<?php

require_once('framework.php');

class PageProductReport
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_product_report.php';
	
	//*** MEMBERS ***
	private $m_product_info_arr; // holds information about the product
	
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
	//	$this->f_report_start = IO::get_input_sl_pg('f_report_start','string');
	//	$this->f_report_end = IO::get_input_sl_pg('f_report_end','string');		
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
	//	$report_start_datetime = OurTime::js_to_datetime($this->f_report_start, 0);
	//	$report_end_datetime = OurTime::js_to_datetime($this->f_report_end, 1);
		
		$this->m_product_info_arr = DB::get_all_rows_fq ('
			SELECT products.*
			FROM products
		');
		
		//TESTING: show how many rows we got:
		//echo count($this->m_obj_info_arr);*/

	}
	
	private function getEmployeeName($id)
	{
		$created_employee_info = DB::get_single_row_fq
		('
			SELECT first_name, last_name
			FROM employees
			WHERE id=\'' . $id . '\'
		');
		$name = $created_employee_info['first_name'] . ' ' . $created_employee_info['last_name'];
		return $name;
	}
	
	private function show_output($err_msg = '')
	{
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
					
		echo ('
			<html>
			<head>
				<link href="style_report.css" rel="stylesheet">
			</head>

			<body>
			
		<table>
				<tr>
					<td>
						<h2>Product Information Report</h2>
					</td>
				</tr>
		</table>
		');
		
		echo ('<table border="1" width="98%" cellspacing="0" cellpadding="0" class="report_table"> 
			<tr> 
				<th>&nbsp;Product ID&nbsp;</th> 
				<th>&nbsp;Notes&nbsp;</th> 
				<th>&nbsp;Product Name&nbsp;</th> 
				<th>&nbsp;Type&nbsp;</th> 
				<th>&nbsp;Subtype 1&nbsp;</th> 
				<th>&nbsp;Subtype 2&nbsp;</th> 
				<th>&nbsp;Description&nbsp;</th> 
				<th>&nbsp;Typical Lifespan (Days)&nbsp;</th> 
				<th>&nbsp;Units&nbsp;</th> 
				<th>&nbsp;Minimum Price Per Unit&nbsp;</th> 
				<th>&nbsp;Created Date&nbsp;</th> 
				<th>&nbsp;Updated Date&nbsp;</th> 
			</tr> '); 

		$cnt = count($this->m_product_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$major_array = $this->m_product_info_arr[$i];		
			$created_employee_name = $this->getEmployeeName($major_array['created_employee_id']);
			$updated_employee_name = $this->getEmployeeName($major_array['updated_employee_id']);
			echo ('
			<tr><td>&nbsp;'. IO::prepout_sl($major_array['icode'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_ml_html($major_array['notes'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['name'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['type'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['subtype1'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['subtype2'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_ml_html($major_array['description'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['typical_lifespan_days'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['typical_units'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['typical_min_price_per_unit'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['created_date'], false) . ' by ' . $created_employee_name .'&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['updated_date'], false) . ' by ' . $updated_employee_name . '&nbsp;</td>
			</tr>
			');  
		}
		
		echo('
		</table>
		</body>
		</html>
		');
			
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_product_report = new PageProductReport();
$page_product_report->run();

?>