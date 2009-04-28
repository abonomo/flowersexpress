<?php

include('framework.php');

class OpLogout
{
	public static function perform()
	{
		session_start();
		DB::connect();
		
		LoginManager::logout_and_redirect();
	}
}

OpLogout::perform();

?>