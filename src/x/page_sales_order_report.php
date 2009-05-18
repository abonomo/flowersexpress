<?php

require_once('framework.php');

class PageTemplate
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_template.php';
	
	//*** MEMBERS ***
	private $m_obj_info_arr;
	
	private $f_report_start;
	private $f_report_end;
	
	private $f_id;	
	private $f_icode;
	private $f_customer_id;
	private $f_shipper_id;
	private $f_shipment_details;
	private $f_order_date;
	private $f_special;
	private $f_delivery_date;
	private $f_price;
	private $f_currency;
	private $f_created_date;
	private $f_updated_date;
	
	private $f_notes;
	
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
		//echo 'here:' . $_POST['f_report_start'];
		$this->f_report_start = IO::get_input_sl_pg('f_report_start','string');
		$this->f_report_end = IO::get_input_sl_pg('f_report_end','string');		
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
		
	private function js_to_datetime($js_date, $time)
	{
		//convert mm/dd/yyyy to YYYY-MM-DD HH:MM:SS
		$first_slash_pos = strpos($js_date, '/');
		$second_slash_pos = strpos(substr($js_date, $first_slash_pos+1), '/') + $first_slash_pos+1;
		
		$month = substr($js_date, 0, $first_slash_pos);
		$day = substr($js_date, $first_slash_pos+1, $second_slash_pos-$first_slash_pos-1);
		$year = substr($js_date, $second_slash_pos+1);
		
		if ($time == 0)
			return "$year-$month-$day 00:00:00";
		else 
			return "$year-$month-$day 23:59:59"; 
	}
	
	
	private function process_input()
	{
		$report_start_datetime = $this->js_to_datetime($this->f_report_start, 0);
		$report_end_datetime = $this->js_to_datetime($this->f_report_end, 1);
		
		$this->m_obj_info_arr = DB::get_all_rows_fq ('
			SELECT *
			FROM sales_orders
			WHERE created_date > \'' . $report_start_datetime . '\' AND created_date < \'' . $report_end_datetime . '\'

		');
		
		//TESTING: show the query:
		/*echo 'SELECT *
			FROM sales_orders
			WHERE created_date > \'' . $report_start_datetime . '\' AND created_date < \'' . $report_end_datetime . '\'<BR>';
		
		//TESTING: show how many rows we got:
		echo count($this->m_obj_info_arr);*/

	}
	
	private function show_output($err_msg = '')
	{
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
		
		/*echo ('		
		<tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="50%" align="right" valign="middle" class="text_label">Sales Order Reports</td>
						  <td width="50%" align="left" valign="middle"> Sales Order Reports </td>
						</tr>
		');*/
					
		echo ('	
			<table>
			<tr><td width="200"> </td><td width="300"><h2>Sales Order Reports</h2></td>
			<td width="100"><h4>Date: ' . IO::prepout_sl($this->f_report_start, false) . ' to '. IO::prepout_sl($this->f_report_end, false) . '</h4></td></tr>
			</table>
		');
		
		//for ($i = 0; $i < 
		//$major_array = $this->m_obj_info_arr[$i];		
		//$this->f_icode = $major_array['created_date'];
		
		echo ('<table border="1" width="98%" cellspacing="0" cellpadding="0" class="sortable"> 
		<thead> 
			<tr> 
			<th width = "50 scope="col"> ID </th> 
			<th width = "100" scope="col"> Icode </th> 
			<th width = "100" scope="col"> Notes </th> 
			<th width = "100" scope="col"> Cust_ID </th> 
			<th width = "100" scope="col"> Ship_ID </th> 
			<th width = "100" scope="col"> Ship_Detail </th> 
			<th width = "100" scope="col"> Special </th> 
			<th width = "100" scope="col"> Order_Date </th> 
			<th width = "100" scope="col"> Delivery_Date </th> 
			<th width = "100" scope="col"> Price </th> 
			<th width = "100" scope="col"> Currency </th> 
			<th width = "100" scope="col"> Create_date </th> 
			<th width = "100" scope="col"> Update_date </th> 
			</tr> 
			</thead> 
			<tbody></table>'); 

			//while ($major_array = mysql_fetch_array($this->m_obj_info_arr, MYSQL_NUM)) {
				//while ($row = mysql_fetch_array($major_array, MYSQL_NUM)) 
			//{ 
		for ($i = 0; $i < count($this->m_obj_info_arr); $i++) {
			$major_array = $this->m_obj_info_arr[$i];		
			
			$this->f_id = $major_array['id'];	
			$this->f_icode = $major_array['icode'];
			$this->f_customer_id = $major_array['customer_id'];
			$this->f_shipper_id = $major_array['shipper_id'];
			$this->f_shipment_details = $major_array['shipment_details']; 
			$this->f_order_date = $major_array['order_date'];
			$this->f_special = $major_array['shipper_id'];
			$this->f_delivery_date = $major_array['special'];
			$this->f_price = $major_array['price'];
			$this->f_currency = $major_array['currency'];
			$this->f_created_date = $major_array['created_date'];
			$this->f_updated_date = $major_array['updated_date'];
			$this->f_notes = $major_array['notes'];
		
			echo ('
			<table>
			<tr><td width="50" align="middle">'. IO::prepout_sl($this->f_id, false) . '</td><td width="100" align="middle">'. IO::prepout_sl($this->f_icode, false) . '</td>
			<td width="100" align="middle">'. IO::prepout_sl($this->f_notes, false) . '</td><td width="100" align="middle">'. IO::prepout_sl($this->f_customer_id, false) . '</td>
			<td width="100" align="middle">'. IO::prepout_sl($this->f_shipper_id, false) . '</td><td width="100" >'. IO::prepout_sl($this->f_shipment_details, false) . '</td>
			<td width="100" >'. IO::prepout_sl($this->special, false) . '</td><td width="100" >'. IO::prepout_sl($this->f_order_date, false) . '</td>
			<td width="100" >'. IO::prepout_sl($this->delivery_date, false) . '</td><td width="100" >'. IO::prepout_sl($this->f_price, false) . '</td>
			<td width="100" >'. IO::prepout_sl($this->f_currency, false) . '</td><td width="100" >'. IO::prepout_sl($this->f_created_date, false) . '</td>
			<td width="100" >'. IO::prepout_sl($this->f_updated_date, false) . '</td>
			</tr></table>
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