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
	
	/*public function convert_datetime($str, $hour, $minute, $second) {

		//list($date, $time) = explode(' ', $str);
		list($year, $month, $day) = explode('/', $str);
		//list($hour, $minute, $second) = explode(':', $time);
		
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

		return $timestamp;
	}*/
	
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
	//created_date  > \'' . $start_begin . '\' && 
								//created_date  < \'' . $start_end . '\'
	private function process_input()
	{
		//list($date, $time) = explode(' ', $str);
		list($year, $month, $day) = explode('/', $this->f_report_start);
		//list($hour, $minute, $second) = explode(':', $time);
		$hour = '00'; 
		$minute = '00';
		$second = '00';
		$start_begin = mktime($hour, $minute, $second, $month, $day, $year);
		$hour = '23';
		$minute = '59';
		$second = '59';
		$start_end = mktime($hour, $minute, $second, $month, $day, $year);
		
		$start_begin = str_replace('/', '-', $start_begin);
		$start_end = str_replace('/', '-', $start_end);
		//$start_begin = convert_datetime($this->f_report_start, 00, 00, 00);
		//$start_end = convert_datetime($this->f_report_start, 23, 59, 59);
	
		$this->m_obj_info_arr = DB::get_all_rows_fq ('
			SELECT *
			FROM sales_orders
			WHERE created_date  > \'' . $f_report_start . '\' 
					
		');
		
		/*$i = 0;
		$major_array = $this->m_obj_info_arr[$i];		
		$this->f_icode = $major_array['created_date'];
		//$this->f_icode = date($this->f_icode);*/
		


	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_REPORTS);
		
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
		/*echo ('Hello
		
		<tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_sl($this->f_icode, 100) . '</td>
						</tr>
			 ');*/
		
		//for ($i = 0; $i < 
		//$major_array = $this->m_obj_info_arr[$i];		
		//$this->f_icode = $major_array['created_date'];
		
		echo "<table border='1' width='98%' cellspacing='0' cellpadding='0' class='sortable'> 
		<thead> 
			<tr> 
			<th scope='col'> ID </th> 
			<th scope='col'> Icode </th> 
			<th scope='col'> Notes </th> 
			<th scope='col'> Cust_ID </th> 
			<th scope='col'> Ship_ID </th> 
			<th scope='col'> Ship_Detail </th> 
			<th scope='col'> Special </th> 
			<th scope='col'> Order_Date </th> 
			<th scope='col'> Delivery_Date </th> 
			<th scope='col'> Price </th> 
			<th scope='col'> Currency </th> 
			<th scope='col'> Create_date </th> 
			<th scope='col'> Update_date </th> 
			</tr> 
			</thead> 
			<tbody>"; 

			//while ($major_array = mysql_fetch_array($this->m_obj_info_arr, MYSQL_NUM)) {
				//while ($row = mysql_fetch_array($major_array, MYSQL_NUM)) 
			//{ 
		$i = 0;
		$major_array = $this->m_obj_info_arr[$i];		
		$this->f_icode = $major_array['created_date'];
		$f_id = $major_array['id'];
	
		$f_icode = $major_array['icode'];
		$f_customer_id = $major_array['customer_id'];
		$f_shipper_id = $major_array['shipper_id'];
		/*$f_shipment_details;   
		$f_order_date;
		$f_special;
		$f_delivery_date;
		$f_price;
		$f_currency;
		$f_created_date;
		$f_updated_date;*/
		
		//private $f_notes;
		
			//echo "<tr><td>"; 
			echo $f_id; 
			//echo "</td><td>"; 
			echo $f_customer_id; 
			//echo "</td><td>"; 
			echo $f_shipper_id; 
			//echo "</td></tr>"; 
			echo $f_icode; 
			//echo "</td><td>"; 
			//echo $row[4]; 
			//echo "</td><td>"; 
			//} 
			//}
			echo "</tbody> 
			</table>"; 
			 
		/*Echo ('	
			<div align="center">
			  <form name="form1" method="post" action="page_customer_view.php">
				<table width="600" border="0" cellpadding="0" cellspacing="0">

				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle">&nbsp;</td>
						  <td width="75%" align="left" valign="middle" class="text_title">Sales Order Report</td>
						</tr>
					</table></td>
				  </tr>	
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>	
				  
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_icode) . ' </td>
						  
	
						
			
						  </td>
						</tr>
						<tr>

						  <td width="25%" align="right" valign="middle" class="text_label">Company Name:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_company_name) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Contact Name:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_contact_name) . '</td>
						</tr>
						<tr>

						  <td width="25%" align="right" valign="middle" class="text_label">Contact Department:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_contact_dept) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Office Phone:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_office_phone_number) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Cell Phone:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_cell_phone_number) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Fax:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_fax_number) . '</td>
						</tr>
					</table></td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>	
				  
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Address Line 1:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_address_line_1) . '</td>
					  </tr>
					  
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Address Line 2:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_address_line_2) . '</td>
					  </tr>
					  
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">City:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_city) . '</td>
					  </tr>
					  
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Province:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_province) . '</td>
					  </tr>
					  
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Country:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_country) . '</td>
					  </tr>
					  
					</table></td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>	
				  
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
				  
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Created:&nbsp;</td>
						  <td width="75%" align="left" valign="middle"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_created_employee_id, false) . '">'  . 
									IO::prepout_sl($this->f_created_first, false) . '&nbsp;' . IO::prepout_sl($this->f_created_last, false)  . '</a>&nbsp;on&nbsp;' . 
									IO::prepout_ml_html($this->f_created_date) . '</td>
						 </tr>
						 
				  		<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Updated:&nbsp;</td>
						  <td width="75%" align="left" valign="middle"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_updated_employee_id, false) . '">'  . 
									IO::prepout_sl($this->f_updated_first, false) . '&nbsp;' . IO::prepout_sl($this->f_updated_last, false)  . '</a>&nbsp;on&nbsp;' . 
									IO::prepout_ml_html($this->f_updated_date) . '</td>
						 </tr>
						 
					</table></td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>	
				  
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					
						<tr>
						  <td width="25%" align="right" valign="top" class="text_label">Notes:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_notes) . '</td>
						</tr>
						
					</table></td>
				  </tr>		
				  
				</table>
			  </form>
			</div>		
		');*/
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageTemplate();
$page_template->run();

?>