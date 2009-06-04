<?php

require_once('framework.php');
require_once('our_time.php');

class PageTemplate
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_template.php';
	
	//*** MEMBERS ***
	private $m_obj_info_arr;
	private $f_get_excel;
	
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
		$this->f_report_start = IO::get_input_sl_pg('f_report_start','string');
		$this->f_report_end = IO::get_input_sl_pg('f_report_end','string');		
		$this->f_get_excel = IO::get_input_sl_pg('f_excel','string');		
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
		$report_start_datetime = OurTime::js_to_datetime($this->f_report_start, 0);
		$report_end_datetime = OurTime::js_to_datetime($this->f_report_end, 1);
		
		$this->m_obj_info_arr = DB::get_all_rows_fq ('
			SELECT purchases.*,
			suppliers.icode AS supplier_icode,
			suppliers.company_name AS supplier_company_name,
			shippers.icode AS shipper_icode,
			shippers.company_name AS shipper_company_name
			FROM purchases
			LEFT OUTER JOIN suppliers ON purchases.supplier_id = suppliers.id
			LEFT OUTER JOIN shippers ON purchases.shipper_id = shippers.id
			WHERE purchases.delivery_date > \'' . $report_start_datetime . '\' AND purchases.delivery_date < \'' . $report_end_datetime . '\'

		');
		
		//TESTING: show how many rows we got:
		//echo count($this->m_obj_info_arr);*/

	}
	
	private function show_output($err_msg = '')
	{
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
		
		if( $this->f_get_excel == 'true' )
		{
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=purchase_report.xls");
		}
		else
		{
			echo ('
			<html>
			<head>
				<title>Purchase Order Report</title>
				<link href="style_report.css" rel="stylesheet">
			</head>

			<body>');
		}
			
		echo ('
			<h2>Purchase Order Report</h2>
			<h4>Dates: ' . IO::prepout_sl($this->f_report_start, false) . ' to '. IO::prepout_sl($this->f_report_end, false) . '</h4>
		');
		
		echo ('<table border="1" width="98%" cellspacing="0" cellpadding="0" class="report_table"> 
			<tr> 
				<th>&nbsp;Order ID&nbsp;</th> 
				<th>&nbsp;Notes&nbsp;</th> 
				<th>&nbsp;Supplier ID&nbsp;</th> 
				<th>&nbsp;Supplier&nbsp;</th> 
				<th>&nbsp;Shipper ID&nbsp;</th> 
				<th>&nbsp;Shipper&nbsp;</th> 
				<th>&nbsp;Shipment Details&nbsp;</th> 
				<th>&nbsp;In Warehouse&nbsp;</th> 
				<th>&nbsp;Ordered Date&nbsp;</th> 
				<th>&nbsp;Delivery Date&nbsp;</th> 
				<th>&nbsp;Price&nbsp;</th> 
				<th>&nbsp;Created Date&nbsp;</th> 
				<th>&nbsp;Updated Date&nbsp;</th> 
			</tr> '); 

		for ($i = 0; $i < count($this->m_obj_info_arr); $i++) 
		{
			$major_array = $this->m_obj_info_arr[$i];		
		
			echo ('
			<tr><td>&nbsp;'. IO::prepout_sl($major_array['icode'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_ml_html($major_array['notes'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['supplier_icode'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['supplier_company_name'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['shipper_icode'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['shipper_company_name'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_ml_html($major_array['shipment_details'], false) . '&nbsp;</td>
				');
				if($major_array['in_warehouse'] ==1)
					echo('<td>&nbsp;Yes&nbsp;</td>');
				else
					echo('<td>&nbsp;No&nbsp;</td>');
			echo ('
				<td>&nbsp;'. IO::prepout_sl($major_array['order_date'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['delivery_date'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['price'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['created_date'], false) . '&nbsp;</td>
				<td>&nbsp;'. IO::prepout_sl($major_array['updated_date'], false) . '&nbsp;</td>
			</tr>
			');  
		}
		
		echo ('
		</table> ');
		
		if( $this->f_get_excel != 'true' )
		{
			echo ('
		</body>
		</html>
		');
		}
			
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageTemplate();
$page_template->run();

?>