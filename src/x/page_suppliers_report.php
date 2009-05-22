<?php

require_once('framework.php');

class PageSuppliersReport
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_suppliers_report.php';
	
	//*** MEMBERS ***
	private $m_supplier_info_arr; 	//holds information about the supplier
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);	
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output();
	}
	
	private function get_input()
	{
		//TODO: get supplier boundary conditions?
	}
	
	private function verify_input()
	{
		//Nothing to do...
	}
	
	private function process_input()
	{
		//get supplier information
		$this->m_supplier_info_arr = DB::get_all_rows_fq
		('
			SELECT suppliers.*
			FROM suppliers
		');
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
		//echo the outer area with the correct tab highlighted for this page
		//ObjOuterArea::echo_top(ObjOuterArea::$TAB_HOME);
		
		echo 
		('
			<html>
			<head>
				<link href="style_report.css" rel="stylesheet">
			</head>

			<body>
		');
		
		echo 
		('<table border="1" width="98%" cellspacing="0" cellpadding="0" class="report_table"> 
			<tr> 
				<th>&nbsp;Supplier ID&nbsp;</th> 
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
				<th>&nbsp;Last Contacted&nbsp;</th> 
			</tr>
		');

		$cnt = count($this->m_supplier_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$major_array = $this->m_supplier_info_arr[$i];
			$created_employee_name = $this->getEmployeeName($major_array['created_employee_id']);
			$updated_employee_name = $this->getEmployeeName($major_array['updated_employee_id']);
			echo 
			('
			<tr><td>&nbsp;'. IO::prepout_sl($major_array['icode'], false) . '&nbsp;</td>
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
				<td>&nbsp;'. IO::prepout_sl($major_array['last_contacted'], false) . ' by ' . $updated_employee_name . '&nbsp;</td>
			</tr>
			');
		}
		
		//ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_suppliers_report = new PageSuppliersReport();
$page_suppliers_report->run();

?>