<?php

require_once('framework.php');

class PageTemplate
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_template.php';
	
	//*** MEMBERS ***
	
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_ADMIN);	//CHANGE required authorization level for this page, ADMIN is the strictest
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output();
	}
	
	private function get_input()
	{
	
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

	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_HOME);
		
		//echo inner area html here
		/*
		//Error Printing Example:
		if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');
		*/
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_template = new PageTemplate();
$page_template->run();

?>