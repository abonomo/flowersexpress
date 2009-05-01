<?php

require_once('framework.php');

class PageLogin
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_login.php';
	private static $DEFAULT_NEXT_PAGE = 'page_home.php';
	
	//*** MEMBERS ***
	
	//potential input variables
	private $f_action;
	private $f_email;
	private $f_password;
	
	private $f_err_msg;	//custom error message that can be passed in
	private $f_goto;	//next page to goto
	
	//*** FUNCTIONS ***
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
			$this->f_password = IO::get_input_password('f_password');
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
		if($this->f_email == '')
		{
			$this->show_output('Please enter an email address.');
		}
		else if($this->f_password == '')
		{
			$this->show_output('Please enter a password.');
		}
	}
	
	private function process_input()
	{
		//if login is successful
		if(LoginManager::login($this->f_email, $this->f_password))
		{
			IO::navigate_to($this->f_goto);
		}	
		//login is unsuccessful
		else
		{
			$this->show_output('Sorry, invalid login.');		
		}
	}
	
	private function show_output($err_msg = '')
	{
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_NONE);
		
			//echo inner area html here
			if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');	
			$this->echo_login_form();
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
	
	private function echo_login_form()
	{
		echo('
		<form method="post" action="' . self::$THIS_PAGE . '?f_action=submit&amp;f_goto=' . IO::prepout_url($this->f_goto) . '">
			Email: <input name="f_email" type="textbox" class="textbox" value="' . IO::prepout_sl($this->f_email, false) . '"/><br>
			Password: <input name="f_password" type="password" class="textbox" /><br>
			<input type="submit" value="Login" class="button"/>
		</form>
		');
	}
}

//create an instance of the page and run it
$page_login = new PageLogin();
$page_login->run();

?>