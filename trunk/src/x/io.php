<?php

//static class
class IO
{
	/*
	=== Conventions: 
	"post" = form field input
	"get" = url variable input
	
	=== Abbreviations for Input Sources:
	"ml" = multiline (e.g. multi-line form textarea)
	"sl" = single-line (e.g. single-line form textbox, radio button value, etc.)
	"pgs" = post, get, session
	"pg" = post, get
	"p" = post only
	"g" = get only
	
	=== IO Functions Overview:
	=== Input:
	input_exists_pg:		Returns true if a variable by a certain name was provided in post or get.
	input_exists_p:		Returns true if a variable by a certain name was provided in post or get.
	input_exists_g:		Returns true if a variable by a certain name was provided in post or get.
	
	get_input_ml_pgs:	Get multi-line input. First try post, then get, then session. If nothing, return default value.
	get_input_ml_pg:	Get multi-line input. First try post, then get. If nothing, return default value.
	get_input_ml_p:		Get multi-line input. Only try post. If nothing, return default value.
	get_input_ml_g:		Get multi-line input. Only try get. If nothing, return default value.
	
	get_input_password:	Get (single-line) password type field. If nothing, returns ''.
	get_input_sl_pgs:	Get single-line input. First try post, then get, then session. If nothing, return default value.
	get_input_sl_pg:	Get single-line input. First try post, then get. If nothing, return default value.
	get_input_sl_p:		Get single-line input. Only try post. If nothing, return default value.
	get_input_sl_g:		Get single-line input. Only try get. If nothing, return default value.
	
	get_server_var:		Get $_SERVER array variable safely.
	
	=== Output: ("prepout" = prepare for output)
	prepout_url:		Prepare an inputted or database variable for output as part of an HTML url.
	prepout_sl:			Prepare a single-line inputted or database variable for output onto an HTML page OR form. Provide false as the second argument if no abbreviation is desired.
	prepout_sl_compact:	Prepare a single-line with ultra-compact abbreviation (no ellipsis, 2+ char trailing words kept)
	
	prepout_ml_html:	Prepare a multiline inputted or database variable for output onto an HTML page.
	prepout_ml_textarea:Prepare a multiline inputted or database variable for output into a textarea type form field.
	
	=== Navigation/URLS
	navigate_to:		Immediately redirect to a page in the code directory and exit the current script.
	get_home_url:		Get the site home directory.
	get_code_url:		Get the site code directory.
	
	*/

	
	
	/*
	Class Configuration Variables:
		OUTER_TRIMMING indicates whether both single-line and multiline post/get
		input will have leading and trailing whitespace removed.
		
		INNER_TRIMMING indicates whether single-line input will convert multiple
		whitespace characters between words in a string to a single space.
	*/
	private static $OUTER_TRIMMING = true;
	private static $INNER_TRIMMING = true;
	
	
	//*** INPUT ***
	
	//*** INPUT PRIVATE HELPER FUNCTIONS ***
	
	//Description:	Escape Input If Magic Quotes Is Off On The Server
	//Operation:	(escape_input)
	private static function escape_input($input_str)
	{
		//escape if magic quotes is off
		if(!get_magic_quotes_gpc())
		{
			return  mysql_real_escape_string($input_str);
		}
		else
		{
			return $input_str;
		}
	}
	
	//Description:	Trims The Extra Whitespace Between Words (Used For Single Line RAW INPUT)
	//Operation:	trim_inner_ws
	private static function trim_inner_ws($the_str)
	{
		return preg_replace('/\s+/', ' ', $the_str);
	}	
	
	//Description:	Performs First Common Filtering Steps For RAW INPUT
	//Operation:	[RAW INPUT]->(escape_input)->trim->
	private static function filter_input_ml($input_str)
	{	
		$ret_val = self::escape_input($input_str);
		if(self::$OUTER_TRIMMING) trim($ret_val);
		return $ret_val;
	}
	
	private static function filter_input_sl($input_str)
	{	
		$ret_val = self::escape_input($input_str);
		if(self::$OUTER_TRIMMING) trim($ret_val);
		if(self::$INNER_TRIMMING) self::trim_inner_ws($ret_val);
		return $ret_val;
	}

	//*** INPUT EXISTENCE CHECKING ***
	
	//Description: Checks for existence of input
	public static function input_exists_pg($var_name)
	{
		return isset($_REQUEST[$var_name]);
	}	
	
	public static function input_exists_p($var_name)
	{
		return isset($_POST[$var_name]);
	}	
	
	public static function input_exists_g($var_name)
	{
		return isset($_GET[$var_name]);
	}
	
	//*** MULTILINE FIELD INPUT ***
	//Description:	Gets RAW INPUT From From Various Sources (POST/GET/SESSION) And Converts To Multiline DB FORMAT 
	//[RAW INPUT]->(escape_input)->trim->settype->[DB FORMAT]
	
	//POST/GET/SESSION
	public static function get_input_ml_pgs($var_name, $sess_var_name, $var_type, $default_val='')
	{
		//find input var in input arrays or use default value	
		if(isset($_POST[$var_name])) $ret_val = self::filter_input_ml($_POST[$var_name]);
		else if(isset($_GET[$var_name])) $ret_val = self::filter_input_ml($_GET[$var_name]);
		else if(isset($_SESSION[$sess_var_name])) $ret_val = $_SESSION[$sess_var_name];	//no filter, session integrity assumed
		else $ret_val = $default_val;
	
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;
	}
	
	//POST/GET
	public static function get_input_ml_pg($var_name, $var_type, $default_val='')
	{
		//find input var in input arrays or use default value	
		if(isset($_POST[$var_name])) $ret_val = self::filter_input_ml($_POST[$var_name]);
		else if(isset($_GET[$var_name])) $ret_val = self::filter_input_ml($_GET[$var_name]);
		else $ret_val = $default_val;
	
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;	
	}
	
	//POST ONLY
	public static function get_input_ml_p($var_name, $var_type, $default_val='')
	{
		//find input var in input arrays or use default value	
		if(isset($_POST[$var_name])) $ret_val = self::filter_input_ml($_POST[$var_name]);
		else $ret_val = $default_val;
	
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;		
	}
	
	//GET ONLY
	public static function get_input_ml_g($var_name, $var_type, $default_val='')
	{
		//find input var in input arrays or use default value	
		if(isset($_GET[$var_name])) $ret_val = self::filter_input_ml($_GET[$var_name]);
		else $ret_val = $default_val;
	
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;		
	}
	
	
	
	//*** SINGLE LINE INPUT ***
	//Description:	Gets RAW INPUT From Various Sources (POST/GET/SESSION) And Converts To Single Line DB FORMAT
	//Operation:	[RAW INPUT]->(escape_input)->trim->trim_inner_ws->settype->[DB FORMAT]
	
	//SPECIAL CASE: GET PASSWORD FROM POST (ONLY)
	//Operation: [RAW INPUT]->(escape_input)->settype->[DB FORMAT]
	public static function get_input_password($var_name)
	{
		$ret_val = self::escape_input($_POST[$var_name]);
		settype($ret_val, 'string');
		return $ret_val;
	}
	
	//POST/GET/SESSION
	public static function get_input_sl_pgs($var_name, $sess_var_name, $var_type, $default_val='')
	{	
		//find input var in input arrays or use default value	
		if(isset($_POST[$var_name])) $ret_val = self::filter_input_sl($_POST[$var_name]);
		else if(isset($_GET[$var_name])) $ret_val = self::filter_input_sl($_GET[$var_name]);
		else if(isset($_SESSION[$sess_var_name])) $ret_val = $_SESSION[$sess_var_name];	//no filter, session integrity assumed
		else $ret_val = $default_val;
	
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;
	}
	
	//POST/GET
	public static function get_input_sl_pg($var_name, $var_type, $default_val='') 
	{	
		//find input var in input arrays or use default value	
		if(isset($_POST[$var_name])) $ret_val = self::filter_input_sl($_POST[$var_name]);
		else if(isset($_GET[$var_name])) $ret_val = self::filter_input_sl($_GET[$var_name]);
		else $ret_val = $default_val;
		
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;		
	}
	
	//POST ONLY
	public static function get_input_sl_p($var_name, $var_type, $default_val='')
	{
		//find input var in input arrays or use default value	
		if(isset($_POST[$var_name])) $ret_val = self::filter_input_sl($_POST[$var_name]);
		else $ret_val = $default_val;
		
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;				
	}	
	
	//GET ONLY
	public static function get_input_sl_g($var_name, $var_type, $default_val='')
	{	
		//find input var in input arrays or use default value	
		if(isset($_GET[$var_name])) $ret_val = self::filter_input_sl($_GET[$var_name]);
		else $ret_val = $default_val;
		
		//force and return the specified type	
		settype($ret_val, $var_type);
		return $ret_val;			
	}	
	
	
	
	//*** OTHER INPUT ***
	
	//Description: 	Get Input From SERVER VARS Safely, since $_SERVER must not be trusted.
	//Operation:	[Raw Input]->(escape_input)->trim->settype->[READY FOR DB, BUT NOT YET SAFE FOR OUTPUT (USE A PREPOUT FUNCTION)]
	public static function get_server_var($var_name)	//TODO: maybe check length, too
	{
		if(isset($_SERVER[$var_name]))
		{
			$server_var = self::filter_input_ml($_SERVER[$var_name]);
			settype($server_var,'string');	//not totally necessary, but good to be defensive
			return $server_var;
		}
		else return '';	
	}
	
	
	
	//*** OUTPUT ***
	
	//Description:	Limits Length Of A String And Adds Ellipsis
	//Operation:	abbrev
	private static function abbrev($the_str, $the_max_len)
	{
		if(strlen($the_str) > $the_max_len)
		{
			return  substr($the_str, 0, $the_max_len-3) . '...';
		}
		else
		{
			return $the_str;
		}
	}

	//Description:	Limits Length Of A String, No Ellipsis, Keeps Trailing Words of at Least 2 Chars
	//Operation:	abbrev_compact	
	private static function abbrev_compact($the_str, $the_max_len)
	{
		if(strlen($the_str) > $the_max_len)
		{
			//truncate
			$the_str = substr($the_str, 0, $the_max_len);
		
			//cut off trailing words unless they're at least 2 characters, cut off extra non alphanum chars
			return (eregi_replace('[^A-Za-z0-9]+[A-Za-z0-9]?$', '', $the_str));	
		}
		else
		{
			return $the_str;
		}	
	}
	
	//Description:	Prepares Any DB FORMAT Text For URL's In HTML
	//Operation:	[DB FORMAT]->stripslashes->urlencode->[URL's in HTML]
	public static function prepout_url($output_str)
	{
		$output_str = stripslashes($output_str);									//-> stripslashes ->
		return urlencode($output_str);												//-> urlencode ->
	}		
	
	//Description:	Prepares Single Line DB FORMAT Text For HTML PAGE OR FORM OUTPUT
	//				Provide false as the second argument if no abbreviation is desired.
	//Operation:	[DB FORMAT]->stripslashes->abbrev->htmlspecialchars->nbsper->[HTML PAGE OR FORM OUTPUT]
	public static function prepout_sl($output_str, $max_len)
	{
		$output_str = stripslashes($output_str);									//-> stripslashes ->
		if($max_len !== false) $output_str = self::abbrev($output_str, $max_len);	//-> abbrev ->
		$output_str = htmlspecialchars($output_str);								//-> htmlspecialchars ->
		return str_replace(' ', '&nbsp;', $output_str);								//-> nbsper ->
	}
	
	//Description:	Prepares Single Line DB FORMAT Text For HTML PAGE OR FORM OUTPUT, Ultra-Compact, 2 Char+ Word Keeping Abbreviator	
	//Operation:	[DB FORMAT]->stripslashes->abbrev_compact->htmlspecialchars->nbsper->[HTML PAGE OR FORM OUTPUT]
	public function prepout_sl_compact($output_str, $max_len)
	{
		$output_str = stripslashes($output_str);									//-> stripslashes ->
		$output_str = self::abbrev_compact($output_str, $max_len);					//-> abbrev_compact ->
		$output_str = htmlspecialchars($output_str);								//-> htmlspecialchars ->
		return str_replace(' ', '&nbsp;', $output_str);								//-> nbsper ->
	}		

	//Description:	Prepares Multiline DB FORMAT Text For HTML PAGE OUTPUT	
	//Operation:	[DB FORMAT]->stripslashes->htmlspecialchars->nl2br->[HTML PAGE OUTPUT]	
	public static function prepout_ml_html($output_str)
	{
		return nl2br(htmlspecialchars(stripslashes($output_str)));	//-> stripslashes -> htmlspecialchars		
	}
	
	//Description:	Prepares Multiline DB FORMAT Text For FORM (TEXTAREA) OUTPUT
	//Operation:	[DB FORMAT]->stripslashes->htmlspecialchars->[FORM (TEXTAREA) OUTPUT]
	public static function prepout_ml_textarea($output_str)
	{
		return htmlspecialchars(stripslashes($output_str));	//-> stripslashes -> htmlspecialchars				
	}
	
	
	//*** NAVIGATION ***
	
	/*========================================
	== Function:			IO::navigate_to
	== Description:			Immediately redirect to a different page in the code directory.
	== Arguments:			$page_name:	filename including extension of page to redirect to 
	== Return Value:		-
	== Return By Ref:		-
	== Example Call:		IO::navigate_to('page_edit.php')
	== Example Return:		-
	== Side-Effects:		Exits the current script at time of call.
	== Notes:				-
	========================================*/
	public static function navigate_to($page_name)
	{
	    header("Status: 200");	//everything okay
		header("Location: " . self::get_code_url() . $page_name);
		exit();	//just in case, execution should have stopped at the line above
	}
	
	/*========================================
	== Function:			IO::get_home_url
	== Description:			Get the URL of the home directory.
	== Arguments:			-
	== Return Value:		URL string
	== Return By Ref:		-
	== Example Call:		IO::get_home_url()
	== Example Return:		http://vklaboratory.com/flowersexpress/
	== Side-Effects:		-
	== Notes:				-
	========================================*/
	public static function get_home_url()
	{
		return 'http://' . self::get_server_var('HTTP_HOST') . dirname(dirname(self::get_server_var('PHP_SELF'))) . '/';		
	}
	
	/*========================================
	== Function:			IO::get_code_url
	== Description:			Get the URL of the code directory.
	== Arguments:			-
	== Return Value:		URL string
	== Return By Ref:		-
	== Example Call:		IO::get_code_url()
	== Example Return:		http://vklaboratory.com/flowersexpress/x/
	== Side-Effects:		-
	== Notes:				-
	========================================*/
	public static function get_code_url()
	{
		return 'http://' . self::get_server_var('HTTP_HOST') . dirname(self::get_server_var('PHP_SELF')) . '/';	
	}
}

?>