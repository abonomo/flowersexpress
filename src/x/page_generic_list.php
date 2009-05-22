<?php

require_once('obj_search_bar.php');
require_once('obj_page_num_nav.php');

class PageGenericList
{
	//*** CONSTANTS ***
	private static $RESULTS_PER_PAGE = 5;
	private static $MAX_PAGES_IN_NAV_BAR = 10;
	
	private static $DEFAULT_ORDER_BY_OPTION_INX = 0;
	private static $DEFAULT_ASC_OR_DESC = 'desc';
	
	//*** CONFIGURATION/CONSTRUCTION MEMBERS ***
	private $m_tab_inx;
	private $m_obj_name;
	private $m_order_by_options;
	private $m_list_object;
	private $m_search_obj_name;
	private $m_has_trash_flag;
	private $m_obj_text_name;
	
	
	//*** MEMBERS ***
	private $m_rows;
	private $m_num_results;
	
	private $f_page;
	private $f_mode;
	private $f_search;
	private $f_order_by;
	private $f_asc_or_desc;
	private $f_action_box_param;
	private $f_action_box_param2;
	
	//*** FUNCTIONS ***
	public function __construct($the_tab_inx, $the_obj_name, $the_order_by_options, $the_list_object, $the_search_obj_name='', $the_has_trash_flag=true, $the_obj_text_name='')
	{ 
		$this->m_tab_inx = $the_tab_inx;
		$this->m_obj_name = $the_obj_name;
		$this->m_order_by_options = $the_order_by_options;
		$this->m_list_object = $the_list_object;
	
		if($the_search_obj_name == '') $this->m_search_obj_name = $the_obj_name; 
		else $this->m_search_obj_name =$the_search_obj_name;
		
		$this->m_has_trash_flag = $the_has_trash_flag;
		
		if($the_obj_text_name == '') $this->m_obj_text_name = ucfirst($this->m_obj_name) . 's';
		else $this->m_obj_text_name = $the_obj_text_name;
	}
	
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
		$this->f_order_by = IO::get_input_sl_pg('f_order_by', 'string', $this->m_order_by_options[self::$DEFAULT_ORDER_BY_OPTION_INX][1]);
		$this->f_asc_or_desc = IO::get_input_sl_pg('f_asc_or_desc', 'string', self::$DEFAULT_ASC_OR_DESC);
		$this->f_action_box_param = IO::get_input_sl_pg('f_action_box_param', 'integer');
		$this->f_action_box_param2 = IO::get_input_sl_pg('f_action_box_param2', 'integer');
	}
	
	private function verify_input()
	{
		//verify/force that order by is one of the predefined values (not an sql injection), this is necessary because it is a column name and not a value (so is not in quotes) 
		$inputted_order_by = $this->f_order_by;
		$this->f_order_by = $this->m_order_by_options[self::$DEFAULT_ORDER_BY_OPTION_INX][1];
		foreach($this->m_order_by_options as $cur_order_by_option)
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
		$offset = ($this->f_page-1)*self::$RESULTS_PER_PAGE;
		$limit = self::$RESULTS_PER_PAGE;
		
		//special query for sort by trash_flag, ignores asc or desc
		if($this->m_has_trash_flag)
		{
			if($this->f_order_by == 'trash_flag') $order_by_clause = 'ORDER BY trash_flag DESC, relevance DESC ';
			else $order_by_clause = 'ORDER BY trash_flag ASC, ' . $this->f_order_by . ' ' . $this->f_asc_or_desc . ' ';
		}
		else
		{
			$order_by_clause = 'ORDER BY ' . $this->f_order_by . ' ' . $this->f_asc_or_desc . ' ';
		}
		
		//echo $order_by_clause;
		
		//if empty search text, get and list everything 
		if($this->f_search == '')
		{
			$this->m_rows = DB::get_all_rows_fq('
				SELECT SQL_CALC_FOUND_ROWS ' . $this->m_list_object->get_needed_fields() . ', 
				1 AS relevance
				FROM ' . $this->m_obj_name . 's ' . $this->m_list_object->RENAME_MAIN_TABLE . ' ' .
				$this->m_list_object->get_needed_joins() . ' ' .
				'WHERE 1 ' .  $this->m_list_object->get_where_clause() . ' ' .
				$order_by_clause .
				'LIMIT ' . $offset . ',' . $limit
			);				
		}
		//nonempty search text
		else
		{
			$encoded_search = DB::encode_small_words_search($this->f_search);
			$this->m_rows = DB::get_all_rows_fq('
				SELECT SQL_CALC_FOUND_ROWS ' . $this->m_list_object->get_needed_fields() . ', 
				MATCH(' . $this->m_search_obj_name . 's.search_words) AGAINST(\'' . $encoded_search . '\' IN BOOLEAN MODE) as relevance
				FROM ' . $this->m_obj_name . 's ' . $this->m_list_object->RENAME_MAIN_TABLE . ' ' .
				$this->m_list_object->get_needed_joins() . ' 
				WHERE MATCH(' . $this->m_search_obj_name . 's.search_words) AGAINST(\'' . $encoded_search . '\' IN BOOLEAN MODE) ' . $this->m_list_object->get_where_clause() . ' ' .
				$order_by_clause . ' 
				LIMIT ' . $offset . ',' . $limit
			);
		}
			
		$this->m_num_results = DB::get_num_rows_found();    //total rows found matching the where clause, ignoring the limit clause
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top($this->m_tab_inx);
		
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
		
		//page title
		echo('<div align="left" class="text_title">Search ' . $this->m_obj_text_name . '</div>');

		//draw search bar
		//prototype: SearchBar::display($obj_name, $search_box_value, $order_by_options, $order_by_value, $asc_or_desc_value
		SearchBar::display($this->m_obj_name, $this->f_search, $this->m_order_by_options, $this->f_order_by, $this->f_asc_or_desc, $this->f_mode, $this->f_page, $this->f_action_box_param, $this->f_action_box_param2);
		
		//display the top page number navigation bar
		//public function echo_top_bar($bar_width, $ws_border_top, $ws_border_bottom, $page_num, $num_results, $results_per_page, $action_script_left, $action_script_right, $max_pages=5, $left_col='', $last_page=-1)
		$page_nav_bar = new ObjPageNavBar();
		$page_nav_bar->echo_top_bar('80%', 0, 10, $this->f_page, $this->m_num_results, self::$RESULTS_PER_PAGE, 'form_search.f_action_box_param.value=' . $this->f_action_box_param . '; form_search.f_action_box_param2.value=' . $this->f_action_box_param2 . '; form_search.f_page.value=', '; form_search.submit();', self::$MAX_PAGES_IN_NAV_BAR);		
		
		//draw results
		//prototype: display($action_box_mode, $cust_info_arr, $num_total_results, $cur_page_num, $page_name)
		$this->m_list_object->display($this->f_mode, $this->m_rows, $this->f_action_box_param, $this->f_action_box_param2);
		
		//display the bottom page number navigation bar
		$page_nav_bar->echo_bottom_bar(0, 0);		
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

?>