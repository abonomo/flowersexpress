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
	
	private $f_icode;
	
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
	//WHERE DATE(created_date) = \'' . $this->f_report_start . '\'
	private function process_input()
	{
	
		$this->m_obj_info_arr = DB::get_all_rows_fq ('
			SELECT *
			FROM sales_orders
			WHERE created_date > \'' . $this->f_report_start . '\' 
					
		');
		
		$i = 0;
		$major_array = $this->m_obj_info_arr[$i];		
		$this->f_icode = $major_array['created_date'];
		//$this->f_icode = date($this->f_icode);

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
		echo ('Hello
		
		<tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_sl($this->f_icode, 100) . '</td>
						</tr>
			 ');
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageTemplate();
$page_template->run();

?>