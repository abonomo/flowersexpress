<?php

require_once('framework.php');

class PageShipperReport
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_shipper_report.php';
	
	//*** MEMBERS ***
	private $m_shipper_info_arr; // holds information about the shipper
	private $f_excel;
	
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
		$this->f_excel = IO::get_input_sl_pg('f_excel','string');
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
		
		$this->m_shipper_info_arr = DB::get_all_rows_fq ('
			SELECT shippers.*
			FROM shippers
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
		
		if( $this->f_excel == 'true' )
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=shipper_report.xls");
		}
		else
		{
			echo ('
			<html>
			<head>
				<title>Shipper Information Report</title>
				<link href="style_report.css" rel="stylesheet">
				<link rel="stylesheet" type="text/css" media="print" href="style_print.css" />
			</head>

			<body> ');
			
		}
		
		echo ('	
			<h2>Shipper Information Report</h2>

			<table border="1" width="98%" cellspacing="0" cellpadding="0" class="report_table"> 
			<tr> 
				<th>&nbsp;Shipper ID&nbsp;</th> 
				<th>&nbsp;Notes&nbsp;</th> 
				<th>&nbsp;Company Name&nbsp;</th> 
				<th>&nbsp;Contact Name&nbsp;</th> 
				<th>&nbsp;Contact Department&nbsp;</th> 
				<th>&nbsp;Office Phone Number&nbsp;</th> 
				<th>&nbsp;Cell Phone Number&nbsp;</th> 
				<th>&nbsp;Fax Number&nbsp;</th> 
				<th>&nbsp;Address Line 1&nbsp;</th> 
				<th>&nbsp;Address Line 2&nbsp;</th> 
				<th>&nbsp;City&nbsp;</th> 
				<th>&nbsp;Province&nbsp;</th> 
				<th>&nbsp;Country&nbsp;</th> 
				<th>&nbsp;Created Date&nbsp;</th>
				<th>&nbsp;Updated Date&nbsp;</th>
			</tr> '); 

		$cnt = count($this->m_shipper_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$major_array = $this->m_shipper_info_arr[$i];		
			$created_employee_name = $this->getEmployeeName($major_array['created_employee_id']);
			$updated_employee_name = $this->getEmployeeName($major_array['updated_employee_id']);
			echo ('
			<tr>
				<td>&nbsp;'. IO::prepout_sl($major_array['icode'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_ml_html($major_array['notes'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['company_name'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['contact_name'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['contact_dept'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['office_phone_number'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_ml_html($major_array['cell_phone_number'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['fax_number'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['address_line_1'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['address_line_2'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['city'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['province'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['country'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['created_date'], false) . ' by ' . $created_employee_name .'&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['updated_date'], false) . ' by ' . $updated_employee_name . '&nbsp;</td>
			</tr>
			');  
		}
		
		echo('
		</table> ');
		
		if( $this->f_excel != 'true' )
		{
			echo ('
			<p class="print_hide"><a href="page_shipper_report.php?f_excel=true">Download as an Excel file</a></p>
			
		</body>
		</html>
		');
		}
			
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_shipper_report = new PageShipperReport();
$page_shipper_report->run();

?>
