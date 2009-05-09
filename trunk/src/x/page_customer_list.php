<?php

include('framework.php');
include('obj_result_box.php');

//basic customer list, no pagination
class CustomerList
{
	private static $OBJ_NAME = 'customer';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, company_name, contact_name, contact_dept, address_line_1, city, office_phone_number, cell_phone_number';

	public static function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public static function display_full($cust_info_arr)
	{
		$cnt = count($cust_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			ResultBox::display(
				self::get_data_display($cust_info_arr[$i]),
				ResultFullMenu::create(self::$OBJ_NAME, $cust_info_arr[$i]['id'])
			);
		}
	}
	
	private static function get_data_display($cust_info)
	{
		return
		'<table width="100%" cellspacing="0" cellpadding="0" >
			<tr>
				<td width="75%" align="left" valign="top">
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $cust_info['id'] . '"><b>' . IO::prepout_sl($cust_info['icode'], 20) . '&nbsp;-&nbsp;' .IO::prepout_sl($cust_info['company_name'], 40) . '</b></a><br>
					' . IO::prepout_sl($cust_info['contact_name'], 30) . ($cust_info['contact_dept'] != '' ? (',&nbsp;' . IO::prepout_sl($cust_info['contact_dept'], 30)) : '') . '<br>
					' . IO::prepout_sl($cust_info['address_line_1'], 20) . ($cust_info['city'] != '' ? (',&nbsp;' . IO::prepout_sl($cust_info['city'], 20)) : '') . '<br>
					' . ($cust_info['office_phone_number'] != '' ? ('Office: ' . IO::prepout_sl($cust_info['office_phone_number'], 30)) : '') . ($cust_info['cell_phone_number'] != '' ? (', Mobile:' . IO::prepout_sl($cust_info['cell_phone_number'], 30)) : '') . '
				</td>
				<td width="75%" align="right" valign="top">
				</td>				
			</tr>
		</table>';
	}
}



class PageCustomerList
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_customer_list.php';
	private static $OBJ_NAME = 'customer';
	
	//*** MEMBERS ***
	private $m_rows;
	private $m_num_results;
	
	private $f_mode;
	private $f_search;
	private $f_order_by;
	
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
		$this->f_mode = IO::get_input_sl_g('f_mode', 'string');
		$this->f_search = IO::get_input_sl_g('f_search', 'string');
		$this->f_order_by = IO::get_input_sl_g('f_order_by', 'string');
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
		//CHANGE and FIX
		$offset = 0;
		$limit = 100;
	
		//$this->m_rows = DB::get_all_rows_fq('SELECT * FROM customers');
		
		$encoded_search = DB::encode_small_words_search($this->f_search);
		$this->m_rows = DB::get_all_rows_fq('
			SELECT SQL_CALC_FOUND_ROWS ' . CustomerList::get_needed_fields() . ', MATCH(search_words) AGAINST(\'' . $encoded_search . '\' IN BOOLEAN MODE) as relevance
			FROM customers WHERE MATCH(search_words) AGAINST(\'' . $encoded_search . '\' IN BOOLEAN MODE)
			ORDER BY relevance DESC LIMIT ' . $offset . ',' . $limit
		);

		$this->m_num_results = DB::get_field_fq('SELECT FOUND_ROWS()');    //total rows found matching the where clause, ignoring the limit clause
	}
	
	private function echo_order_by_select_box()
	{
	
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_CUSTOMERS);
		
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
		
		//draw search form
		echo('
		<div align="left" class="text_title">Search Customers</div>
		<form name="form_search">
			<input name="f_search" type="text" width="30" class="textbox" value="' . IO::prepout_sl($this->f_search, false) . '"/>&nbsp;
			<input name="f_search_btn" class="button" type="button" value="Search" onclick="document.location=\'page_' . self::$OBJ_NAME . '_list.php?f_search=\' + escape(form_search.f_search.value) + \'&amp;f_order_by=\' + escape(form_search.f_order_by.value)"/>
			<select name="f_order_by" class="textbox">
			  <option value="123">dfs</option>
			</select>
		</form>
		');
		
		//draw results
		CustomerList::display_full($this->m_rows);
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page = new PageCustomerList();
$page->run();

?>