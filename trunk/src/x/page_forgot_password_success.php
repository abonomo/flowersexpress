<?php

require_once('framework.php');

class PageForgotPasswordSuccess
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_forgot_password_success.php';
	private static $DEFAULT_NEXT_PAGE = 'page_home.php';
	
	//*** MEMBERS ***
	
	//potential input variables
	private $f_action;
	private $f_email;
	private $f_password;
	
	private $f_err_msg;	//custom error message that can be passed in
	private $f_goto;	//next page to goto

	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();	
	
		$this->get_input(); 
		
		if($this->f_action == 'submit')
		{
			$this->verify_input();
		
			$this->process_input();
		}
		
		$this->show_output();
	}
	
	private function get_input()
	{
		//get page action
		$this->f_action = IO::get_input_sl_g('f_action', 'string');
		
		//get next page
		$this->f_goto = IO::get_input_sl_g('f_goto', 'string', self::$DEFAULT_NEXT_PAGE);
		
		//if page action is submit, get form input
		if($this->f_action == 'submit')
		{
			$this->f_email = IO::get_input_sl_p('f_email', 'string');
		}
		//if a custom error message was sent into this page
		else if(IO::input_exists_g('f_err_msg'))
		{
			$this->f_err_msg = IO::get_input_sl_g('f_err_msg', 'string');
			
			//show error
			$this->show_output($this->f_err_msg);
		}
	}
	
	private function verify_input()
	{
	}
	
	private function process_input()
	{	
	}

	
	
	private function show_output($err_msg = '')
	{
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_NONE);
		
			//echo inner area html here
			if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');	
			$this->echo_form();
		
		ObjOuterArea::echo_bottom(false);
	
		//output is always the last thing done when called
		exit();
	}
	
	private function echo_form()
	{
		echo('
		<b>Your password has been reset, Please check your email.</b>
		<br><br>
		
		<!--print the Home Page button-->
		<a href="page_home.php">Home Page</a><br>
					 
		');
	}
	
}


//create an instance of the page and run it
$page_login = new PageForgotPasswordSuccess();
$page_login->run();

?>