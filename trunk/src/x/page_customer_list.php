<?php

include('framework.php');
include('obj_result_box.php');
include('obj_page_num_nav.php');

//basic customer list, no pagination
class CustomerList
{
	private static $OBJ_NAME = 'customer';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, company_name, contact_name, contact_dept, address_line_1, city, office_phone_number, cell_phone_number';

	public static function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public static function display($action_box_mode, $cust_info_arr)
	{
		//display the list of results
		$cnt = count($cust_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = self::get_data_display($cust_info_arr[$i]);
			//select a customer for a sales order mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL) $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit?f_id=' . $cust_info_arr[$i]['id']);
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $cust_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private static function get_data_display($cust_info)
	{
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('', $cust_info['icode'], 30, 'No Code') . IO::prepout_sl_label('&nbsp;-&nbsp;', $cust_info['company_name'], 30, 'No Company Name');
		$obj_line[0] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Contact Name:&nbsp;', $cust_info['contact_name'], 20) . IO::prepout_sl_label(',&nbsp;', $cust_info['contact_dept'], 20);
		$obj_line[1] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Address:&nbsp;', $cust_info['address_line_1'], 20) . IO::prepout_sl_label(',&nbsp;', $cust_info['city'], 20);
		$obj_line[2] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Office:&nbsp;', $cust_info['office_phone_number'], 20) . IO::prepout_sl_label(',&nbsp;Mobile:&nbsp;', $cust_info['cell_phone_number'], 20);
	
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0" >
			<tr>
				<td width="75%" align="left" valign="top">
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $cust_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
				<td width="75%" align="right" valign="top">
				</td>				
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}


class SearchBar
{
	public static function display($obj_name, $search_box_value, $order_by_options, $order_by_value, $asc_or_desc_value, $cur_page_num)
	{
		// onclick="document.location=\'page_' . $obj_name . '_list.php?f_search=\' + escape(form_search.f_search.value) + \'&amp;f_order_by=\' + escape(form_search.f_order_by.value) + \'&amp;f_asc_or_desc=\' + escape(form_search.f_asc_or_desc.value)"
	
		//draw search form
		echo('
		<form name="form_search" method="post" action="page_' . $obj_name . '_list.php">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="33%" align="left" valign="middle">			
						<input name="f_page" type="hidden" value="' . $cur_page_num . '"/>
						<input name="f_search" type="text" width="30" class="textbox" value="' . IO::prepout_sl($search_box_value, false) . '"/>
						<input name="f_search_btn" class="button" type="submit" value="Search"/>
					</td>
					<td align="left" valign="middle" width="33%">
		');
		
		//draw order by select box
		self::echo_order_by_select_box($order_by_options, $order_by_value);
		
		echo('
					</td>
					<td  width="33%" align="left" valign="middle">
		');
		
		//draw ascending sort or descending sort radio buttons
		self::echo_asc_or_desc_btns($asc_or_desc_value);
		
		echo('
					</td></tr>
				</table>		
			</form>
		');
	}
	
	private function echo_order_by_select_box($order_by_options, $order_by_value)
	{
		echo('
			Order&nbsp;By:&nbsp;<select name="f_order_by" class="textbox" onchange="form_search.submit()">
		');
		
		$cnt = count($order_by_options);
		for($i = 0; $i < $cnt; $i++)
		{
			echo('<option value="' . $order_by_options[$i][1] . '" ' . (($order_by_value == $order_by_options[$i][1]) ? 'selected' : '') . '>' . $order_by_options[$i][0] . '</option>');
		}
		
		echo('
			</select>		
		');
	}

	private function echo_asc_or_desc_btns($asc_or_desc_value)
	{
		echo('
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="middle">			
						Ascending:
					</td>
					<td align="left" valign="middle">			
						<input type="radio" name="f_asc_or_desc" value="asc" ' . (($asc_or_desc_value == 'asc') ? 'checked' : '') . ' onchange="form_search.submit()">
					</td>
					<td align="left" valign="middle">			
						&nbsp;&nbsp;&nbsp;
					</td>
					<td align="left" valign="middle">			
						Descending:
					</td>
					<td align="left" valign="middle">			
						<input type="radio" name="f_asc_or_desc" value="desc" ' . (($asc_or_desc_value == 'desc') ? 'checked' : '') . ' onchange="form_search.submit()">
					</td>
				</tr>
			</table>
		');
	}
}

class PageCustomerList
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_customer_list.php';
	private static $OBJ_NAME = 'customer';
	private static $RESULTS_PER_PAGE = 2;
	private static $MAX_PAGES_IN_NAV_BAR = 10;
	
	//{display value, database field name}
	private static $ORDER_BY_OPTIONS = array(
		array('Relevance', 'relevance'),
		array('Customer Code', 'icode'),
		array('Company Name', 'company_name'),
		array('City', 'city'),
		array('Province', 'province'),
		array('Country', 'country'),
		array('Created Date', 'created_date'),
		array('Updated Date', 'updated_date')
		//array('Trash Bin', 'trash_flag')
	);
	
	private static $DEFAULT_ORDER_BY_OPTION_INX = 0;
	private static $DEFAULT_ASC_OR_DESC = 'desc';
	
	//*** MEMBERS ***
	private $m_rows;
	private $m_num_results;
	
	private $f_page;
	private $f_mode;
	private $f_search;
	private $f_order_by;
	private $f_asc_or_desc;
	
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
		$this->f_page = IO::get_input_sl_pg('f_page', 'integer', 1);
		$this->f_mode = IO::get_input_sl_pg('f_mode', 'string');
		$this->f_search = IO::get_input_sl_pg('f_search', 'string');
		$this->f_order_by = IO::get_input_sl_pg('f_order_by', 'string', self::$ORDER_BY_OPTIONS[self::$DEFAULT_ORDER_BY_OPTION_INX][1]);
		$this->f_asc_or_desc = IO::get_input_sl_pg('f_asc_or_desc', 'string', self::$DEFAULT_ASC_OR_DESC);
	}
	
	private function verify_input()
	{
		//verify/force that order by is one of the predefined values (not an sql injection), this is necessary because it is a column name and not a value (so is not in quotes) 
		$inputted_order_by = $this->f_order_by;
		$this->f_order_by = self::$ORDER_BY_OPTIONS[self::$DEFAULT_ORDER_BY_OPTION_INX][1];
		foreach(self::$ORDER_BY_OPTIONS as $cur_order_by_option)
		{
			if($inputted_order_by == $cur_order_by_option[1])
			{
				$this->f_order_by = $cur_order_by_option[1];
				break;
			}
		}
		
		//verify/force the asc/desc is the asc or desc keyword, not a command like "drop table;"
		$inputted_asc_or_desc = strtolower($this->f_asc_or_desc);
		if($inputted_asc_or_desc != 'asc' && $inputted_asc_or_desc != 'desc') $this->f_asc_or_desc = self::$DEFAULT_ASC_OR_DESC;
	}
	
	private function process_input()
	{
		//TODO: trash sort
		$offset = ($this->f_page-1)*self::$RESULTS_PER_PAGE;
		$limit = self::$RESULTS_PER_PAGE;
		
		//if empty search text, get and list everything 
		if($this->f_search == '')
		{
			$this->m_rows = DB::get_all_rows_fq('
				SELECT SQL_CALC_FOUND_ROWS ' . CustomerList::get_needed_fields() . ', 
				1 AS relevance
				FROM customers
				ORDER BY ' . $this->f_order_by . ' ' . $this->f_asc_or_desc .  ' LIMIT ' . $offset . ',' . $limit
			);			
		}
		//nonempty search text
		else
		{
			$encoded_search = DB::encode_small_words_search($this->f_search);
			$this->m_rows = DB::get_all_rows_fq('
				SELECT SQL_CALC_FOUND_ROWS ' . CustomerList::get_needed_fields() . ', 
				MATCH(search_words) AGAINST(\'' . $encoded_search . '\' IN BOOLEAN MODE) as relevance
				FROM customers WHERE MATCH(search_words) AGAINST(\'' . $encoded_search . '\' IN BOOLEAN MODE)
				ORDER BY \'' . $this->f_order_by . '\' ' . $this->f_asc_or_desc .  ' LIMIT ' . $offset . ',' . $limit
			);
		}
			
		$this->m_num_results = DB::get_num_rows_found();    //total rows found matching the where clause, ignoring the limit clause
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
		
		//page title
		echo('<div align="left" class="text_title">Search Customers</div>');

		//draw search bar
		//prototype: SearchBar::display($obj_name, $search_box_value, $order_by_options, $order_by_value, $asc_or_desc_value
		SearchBar::display(self::$OBJ_NAME, $this->f_search, self::$ORDER_BY_OPTIONS, $this->f_order_by, $this->f_asc_or_desc, $this->f_page);
		
		//display the top page number navigation bar
		//public function echo_top_bar($bar_width, $ws_border_top, $ws_border_bottom, $page_num, $num_results, $results_per_page, $action_script_left, $action_script_right, $max_pages=5, $left_col='', $last_page=-1)
		$page_nav_bar = new PageNavBar();
		$page_nav_bar->echo_top_bar('80%', 0, 10, $this->f_page, $this->m_num_results, self::$RESULTS_PER_PAGE, 'form_search.f_page.value=', '; form_search.submit();', self::$MAX_PAGES_IN_NAV_BAR);		
		
		//draw results
		//prototype: display($action_box_mode, $cust_info_arr, $num_total_results, $cur_page_num, $page_name)
		CustomerList::display($this->f_mode, $this->m_rows);
		
		//display the bottom page number navigation bar
		$page_nav_bar->echo_bottom_bar(0, 0);		
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page = new PageCustomerList();
$page->run();

?>