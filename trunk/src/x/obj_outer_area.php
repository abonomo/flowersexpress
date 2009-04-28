<?php

include('framework.php');

class ObjOuterArea
{
	public static function echo_top()
	{
		//here there will be contextualized tabs based on the user auth level
		echo('
		<html>
		<head>
			<link rel="stylesheet" type="text/css" href="style.css" />
			<script type="text/javascript" src="style.js"></script>
		</head>
		<body>
		');
		
		//if logged in, echo a logout button
		if(LoginManager::meets_auth_level(LoginManager::$AUTH_LOGIN))
		{
			echo('<a href="op_logout.php" class="link">Logout</a>');
		}
		
		echo('
		<h1>Outer Area Top</h1>
		');
	}
	
	public static function echo_bottom()
	{
		echo('
		<h1>Outer Area Bottom</h1>
		</body>
		</html>
		');	
	}
}

?>