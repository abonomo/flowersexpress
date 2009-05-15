<?php

require_once('framework.php');

class LoginManager
{
	/*
	=== Functions Overview:
	=== Logging in:
	login:					Attempts a login with an email/password combination. Returns true on success or false on failure.
	
	=== Checking Logins/Authorization Levels:
	assert_auth_level:		Checks that a user is logged in and meets a desired authorization level. Immediately redirects to login page on failure.
	meets_auth_level:		Checks that a user is logged in and meets a desired authorization level. Returns true or false.
	
	=== Getting the Current User:
	get_id:					Gets the logged-in user's id.
	
	=== Logging Out:
	logout:					Logs the user out. (Erases their session). No redirect.
	logout_and_redirect:	Logs the user out and redirects to the login page.
	
	=== Checking/Changing Passwords:
	verify_password_again:	Checks the user's password again. Returns true or false.
	change_password:		Changes the user's password in the database to a different one.
	
	=== Other Notes:
	=== Managed Session Variables:
	login_id = logged-in employees id
	*/
	
	private static $LOGIN_PAGE = 'page_login.php';

	//authorization levels (lowest is 1), they build on each other, each has priviledges of those below
	public static $AUTH_ADMIN = 4;				//admininstrator (read and write everything)
	public static $AUTH_READ_WRITE = 3;			//read and write (most things)
	public static $AUTH_READ_ONLY = 2;			//read only (most things)
	public static $AUTH_LOGIN = 1;				//simply logged in, no permissions
	
	
	private static function encryptPassword($thePassword)
	{
		return md5($thePassword);
	}
	
	public static function login($the_email, $the_password)
	{
		$the_password = self::encryptPassword($the_password);
		$login_result = DB::get_result_fq('SELECT id FROM employees WHERE email=\'' . $the_email . '\' AND password=\'' . $the_password . '\'');
		
		//if login is successful	
		if(DB::is_unique_result($login_result))
		{
			//TODO: maybe retain session data for same user logging back in after auto-timeout of some sort
			//clear any previous session data
			self::logout();
			
			//save login id for login verification
			$_SESSION['login_id'] = DB::get_field_fr($login_result);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//FORCES a login with authorized permissions and employee_id existence to proceed, default check 
	public static function assert_auth_level($req_auth_level)
	{
		//redirects immediately if not logged in, otherwise just returns nothing
		if(!self::meets_auth_level($req_auth_level))
		{	
			//determine desired page (and query string it was called with)
			$login_err_msg = 'Permission denied. Please log in as an authorized user.';
			$desired_page = basename(IO::get_server_var('PHP_SELF')) . '?' . IO::get_server_var('QUERY_STRING');
		
			//go to login page and ask for a login
			IO::navigate_to(self::$LOGIN_PAGE . '?f_err_msg=' . IO::prepout_url($login_err_msg) . '&f_goto=' . IO::prepout_url($desired_page) );
		}		
	}
	
	//side-effect-less login check
	public static function meets_auth_level($req_auth_level)
	{
		//if logged in
		if(isset($_SESSION['login_id']))
		{
			//get employee authorization level
			$emp_info_res = DB::get_result_fq('SELECT auth_level FROM employees WHERE id=' . $_SESSION['login_id']);
			
			//if employee still exists in database (make sure employee hasn't been deleted since login)
			if(DB::is_unique_result($emp_info_res))
			{
				$emp_auth_level = DB::get_field_fr($emp_info_res);
				
				//if employee matches or exceeds required auth level, return true
				if($emp_auth_level >= $req_auth_level) return true;
				//if employee does not meet required auth level
				else return false;
			}
			//if employee no longer exists
			else
			{
				//defensively logout (delete session)
				self::logout();
				return false;
			}
		}
		//not logged in
		else return false;
	}	
	
	//get logged-in employee's id
	public static function get_id()
	{
		if(isset($_SESSION['login_id'])) return $_SESSION['login_id'];
		else return -1;
	}
	
	//side-effect-less logout
	public static function logout()
	{
		session_unset();
	}
	
	//logs out user and redirects to login page
	public static function logout_and_redirect()
	{
		self::logout();
		IO::navigate_to(self::$LOGIN_PAGE);
	}
	
	
	
	//*** PASSWORD CHANGING ***
	public static function verify_password_again($the_password, $id)
	{
		if(!self::meets_auth_level(self::$AUTH_ADMIN) || strlen($id) == 0)
			$id = self::get_id();
		$verify_result = DB::get_result_fq('SELECT id FROM employees WHERE id=\'' . $id . '\' AND password=\'' . $the_password . '\'');
		return DB::is_unique_result($verify_result);
	}
	
	public static function change_password($the_password, $id)
	{
		if(!self::meets_auth_level(self::$AUTH_ADMIN) || strlen($id) == 0)
			$id = self::get_id();
		$the_password = self::encryptPassword($the_password);
		DB::send_query('UPDATE employees SET password=\'' . $the_password . '\' WHERE id=\'' . $id . '\'');
		//login session data does not have to be touched
	}	
}

?>