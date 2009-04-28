<?php

require_once('../../../server_config.php');

class DB
{
	/*
	=== Conventions:
	"fq" = from query (i.e. the argument is a query)
	"fr" = from result (i.e the argument is a mysql result)
	
	"bool" prefix = returns the item or false if the query did not find anything/failed
	
	=== DB Functions Overview:
	=== Connecting:
	connect:					Connect to MySQL database.
	
	=== Sending Non-Retrieval Queries (like INSERT, UPDATE):
	send_query:					Send a query that does not retrieve data like INSERT, UPDATE.
	bool_send_query:			Send a query, return true if succeeded, false if failed.
	
	=== Getting a General MySQL Result Resource
	get_result_fq:				Get a MySQL result resource from a query.
	
	=== Get or Count Multiple Rows:
	get_all_rows_fq:			Return a 2D array of all of the rows and their fields.
	get_num_rows_found:			Return the number of rows found in the previous query (if it included the SQL_CALC_FOUND_ROWS keyword)
	
	=== Get a Single Row:
	bool_get_single_row_fq:		Get a single row from a query. Return false if result was empty.
	get_single_row_fq:			Get a single row from a query. Return empty array if result was empty.
	get_single_row_fr:			Get a single row from a result.
	
	=== Get a Single Field (From a Single Row):
	bool_get_field_fq:			Returns false if the query did not find anything.
	get_field_fq:				Get a single field from a single row from a query.
	get_field_fr:				Get a single frield from a single row from a result.
	
	=== Check the Existence or Uniqueness of a Query or Result:
	result_exists_fq:			Returns true if any rows were found from the query.
	unique_result_exists_fq:	Returns true if exactly one row was found from the query.
	is_unique_result:			Returns true if the result has exactly one row.
	
	=== Overcome the Small Words Not Indexed MySQL Fulltext Search Problem
	encode_small_words_search:	Encodes small words in a search string to find them in fulltext-indexed fields.
	encode_small_words_store:	Encodes (artifically lengthens) small words to force MySQL to index them. Apply this to a string you are storing in a search words field.
	*/

	//*** Establish Database Connection ***
	// connects to the website specific database and returns the connection (for 'close(connection)', but usually you don't have to close the connection)
	public static function connect()
	{
		$mysql_connection = mysql_connect(ServerConfig::$MYSQL_HOST, ServerConfig::$MYSQL_USER, ServerConfig::$MYSQL_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db(ServerConfig::$MYSQL_DATABASE_NAME);
		return $mysql_connection;
	}	
	
	//*** Send Query ***
	//regular integrated error handling
	public static function send_query($query)
	{
		mysql_query($query) or die("Query Failed: $query"); 
	}
	
	//returns true or false for custom error handling (when extra cleanup is required on failure), CHECK: is used
	public static function bool_send_query($query)
	{
		return mysql_query($query);
	}

	//*** Get Result ***
	public static function get_result_fq($query)
	{
		$result = mysql_query($query) or die("Query Failed: $query"); 
		return $result;
	}
	
	//*** Get Multiple Rows ***
	//good for when multiple iterations through a result set are required
	public static function get_all_rows_fq($query)
	{
		//loop through rows, storing them in an empty multidimensional array passed by reference
		$result = self::get_result_fq($query);
		$i = 0;
		while($cur_row = mysql_fetch_array($result))
		{
			$two_dim_arr[$i] = $cur_row;
			$i++;
		}
		return $two_dim_arr;
	}	
	
	//returns number of rows found in previous query (beyond the limits)
	public static function get_num_rows_found()
	{
		return self::get_field_fq('SELECT FOUND_ROWS()');	
	}
	
	//*** Get Single Row ***
	public static function bool_get_single_row_fq($query)
	{
		$result = self::get_result_fq($query);
		if(self::is_unique_r($result)) return self::get_single_row_fr($result);
		else return false;
	}
	
	public static function get_single_row_fq($query)
	{
		$result = self::get_result_fq($query);
		return mysql_fetch_array($result);
	}
	
	public static function get_single_row_fr($result)
	{
		return mysql_fetch_array($result);
	}
	
	//*** Get Single Fields ***
	public static function bool_get_field_fq($query)
	{
		$result = get_result_fq($query);
		if(is_unique_r($result)) return get_field_fr($result);
		else return false;
	}
	
	public static function get_field_fq($query)
	{
		$array = self::get_single_row_fq($query);
		return $array[0];
	}
	
	public static function get_field_fr($result)
	{
		$array = mysql_fetch_array($result);
		return $array[0];
	}

	//*** Check Existance/Uniqueness of Query Results***
	public static function result_exists_fq($query)
	{
		$result = self::get_result_fq($query);
		$numrows = mysql_num_rows($result);
		return ($numrows >= 1);		
	}

	public static function unique_result_exists_fq($query)
	{
		$result = self::get_result_fq($query);
		$numrows = mysql_num_rows($result);
		return ($numrows == 1);		
	}	

	public static function is_unique_result($result)
	{
		return (mysql_num_rows($result) == 1);
	}

	
	//*** Searching Limitations Work-Around Functions ***

	//appends 'QQQ' to all search words that contain less than 4 characters so mysql will index them
	public static function encode_small_words_search($the_str)
	{
		//NOTE: watch out before changing this, this is carefully thought out
		//replace all non-alphanumeric and underscore chars with spaces, except keep ESCAPEs and double quotes
		$the_str = preg_replace('/[^A-Za-z0-9_\\\\"]+/',' ', $the_str);
		
		//append QQQ's
		$the_str = preg_replace('/(?<=[^A-Za-z0-9_])([A-Za-z0-9_]{1,3})(?=([^A-Za-z0-9_]|$))/','\\1QQQ', $the_str); //most words
		return preg_replace('/^([A-Za-z0-9_]{1,3})(?=([^A-Za-z0-9_]|$))/','\\1QQQ', $the_str); //first word only, because back assertions must be fixed-length
	}
	
	//appends 'QQQ' to all field words that contain less than 4 characters so mysql will index them
	public static function encode_small_words_store($the_str)
	{
		//replace all non-alphanumeric and underscore chars with spaces, ESCAPES get lost, but so do all dangerous characters too
		$the_str = preg_replace('/[^A-Za-z0-9_]+/',' ', $the_str);
		
		//append QQQ's
		$the_str = preg_replace('/(?<=[^A-Za-z0-9_])([A-Za-z0-9_]{1,3})(?=([^A-Za-z0-9_]|$))/','\\1QQQ', $the_str); //most words
		return preg_replace('/^([A-Za-z0-9_]{1,3})(?=([^A-Za-z0-9_]|$))/','\\1QQQ', $the_str); //first word only, because back assertions must be fixed-length
	}	
}
?>