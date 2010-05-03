<?php

require_once('framework.php');

class PageForgotPassword
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_forgot_password.php';
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
		if($this->f_email == '')
		{
			$this->show_output('Please enter an email address.');
		}
	}
	
	private function process_input()
	{	
		//generate new password - HARDCODED
		$length=10;
		$strength=4;
		$vowels = 'aieouy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AIEOUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		
		//send email
		$sendTo = $this->f_email;
		$subject = "[Flowers Express] - Password Reset";
		$headers = "From: do_not_reply@flowersexpress.com<do_not_reply@flowersexpress.com>\r\n";
		$headers .= "Reply-To: do_not_reply@flowersexpress.com" . "\r\n";
		$headers .= "Return-path: do_not_reply@flowersexpress.com";
		$message = "Your Password has been Reset!\n\n" .	
					"Your new password is " . $password . "\n\n" .
					"THIS IS AN AUTOMATICALLY GENERATED EMAIL - PLEASE DO NOT REPLY";
		
		// once the variables have been defined, they can be included
		// in the mail function call which will send you an email
		mail($sendTo, $subject, $message, $headers);
		
		//update the database	
		$email_exist = DB::result_exists_fq('
		SELECT * from employees
		Where email=\'' . $this->f_email. '\'
		');
		
		if ($email_exist)
		{
			DB::send_query('
			UPDATE employees SET
			password=\'' . $password . '\'
			WHERE email=\'' . $this->f_email . '\'
			');
			IO::navigate_to('page_forgot_password_success.php');
		}
		else
		{
			$this->show_output('There are no records of that email address!');
		}
		
		
	}

	
	
	private function show_output($err_msg = '')
	{
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_NONE);
		
			//echo inner area html here
			if($err_msg != '') echo('<font class="text_error">' . $err_msg . '</font>');	
			$this->echo_form();
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
	
	private function echo_form()
	{
		echo('
		<form method="post" action="' . self::$THIS_PAGE . '?f_action=submit&amp;f_goto=' . IO::prepout_url($this->f_goto) . '">
			<b>Enter your email address and your password will be reset and sent to you.</b><br><br>
			Email: <input name="f_email" type="textbox" class="textbox" value="' . IO::prepout_sl($this->f_email, false) . '"/><br>
			<input type="submit" value="Reset Password" class="button"/>
		</form>
		');
	}
	
}


//create an instance of the page and run it
$page_login = new PageForgotPassword();
$page_login->run();

?>