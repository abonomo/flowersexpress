<?php

class ResultBox
{
	public static function display($data, $actions)
	{
		self::start_data_box();
			echo $data;
		self::end_data_box();
		self::start_action_box();
			echo $actions;
		self::end_action_box();
	}

	private static function start_data_box()
	{
		echo('
			<table width="80%" cellspacing="0" cellpadding="0" class="result_box">
			  <tr>
				<td align="left" valign="top">	
		');	
	}
	
	private static function end_data_box()
	{
		echo('
				</td>
				<td width="25">
					&nbsp;
				</td>
		');	
	}
	
	private static function start_action_box()
	{
		echo('
				<td width="20%" align="center" valign="middle">  		
		');	
	}
	
	private static function end_action_box()
	{
		echo('
				</td>
			  </tr>
			</table>
			<br>
		');	
	}
}

class ResultDeleteMenu
{	
	public static $MODE_VAL = 'delete';
	
	public static function create($obj_name, $obj_id)	//middle of the page name: e.g. page_CUSTOMER_add_edit.php
	{
		return('
			<input class="button" type="button" value="Delete" onclick="if(window.confirm(\'Are you sure you want to delete this entry?\')) { document.location=\'page_' . $obj_name . '_delete.php?f_id=' . $obj_id .'\' }"/>
		');			
	}
}

class ResultFullMenu
{	
	public static $MODE_VAL = 'full';

	public static function create($obj_name, $obj_id)	//middle of the page name: e.g. page_CUSTOMER_add_edit.php
	{
		return('
			<b>
			<a href="page_' . $obj_name . '_view.php?f_id=' . $obj_id .'"/>View</a><br>
			<a href="page_' . $obj_name . '_add_edit.php?f_mode=edit&amp;f_id=' . $obj_id . '\'"/>Edit</a><br>
			<a href="#" onclick="if(window.confirm(\'Are you sure you want to delete this entry?\')) { document.location=\'page_' . $obj_name . '_delete.php?f_id=' . $obj_id .'\'; } return false;"/>Delete</a>
			<b>
		');			
	}
}

class ResultSelectMenu
{
	public static $MODE_VAL = 'select';

	public static function create($select_btn_url)	//put f_id in url
	{
		return('
			<input class="button" type="button" value="Select" onclick="document.location=\'' . $select_btn_url . '\'"/>
		');
	}
}

//select total cost and quantity textboxes
class ResultQuantityMenu
{
	public static $MODE_VAL = 'quantity';

	public static function create($select_btn_url)	//put f_id in url
	{
		return('
			<form action="' . $select_btn_url . '">
				<input class="textbox" type="textbox" name="f_total_cost"><br>
				<input class="textbox" type="textbox" name="f_quantity"><br>
				<input class="button" type="submit" value="Select"/>
			</form>
		');		
	}
}

/*
include('framework.php');
session_start();
DB::connect();
ObjOuterArea::echo_top();
ResultBox::display('asdf','asdf');
ObjOuterArea::echo_bottom();
*/
?>