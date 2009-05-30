<?php

require_once('framework.php');

/*
Undeletes a purchase with given id
For example, if
	page_purchase_undelete?f_id=5
is accessed, purchase with id 5 will have its trash flag set to 1
*/
class PagePurchaseUndelete
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_purchase_undelete.php';
	
	//*** MEMBERS ***
	private $f_id;
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_WRITE);
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output();
	}
	
	private function get_input()
	{
		//get id of Purchase to edit
		$this->f_id = IO::get_input_sl_g('f_id','string');
	}
	
	private function verify_input()
	{
		if($this->f_id == "") 
			$this->show_output('Purchase not found.');
		
	}
	
	private function process_input()
	{
		//check that there's a unique result (is this necessary if id is defined as unique?)
		if( DB::bool_get_single_row_fq('SELECT * FROM purchases where id =\'' . $this->f_id .'\'') )
		{
			//update trash_flag
			DB::send_query('
			UPDATE purchases SET
			trash_flag = 0
			WHERE id=\'' . $this->f_id . '\'
			');
		}
		else
		{
			// was not a unique result
			$this->show_output('Purchase not found');
		}
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PURCHASES);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<table width="100%">
			<tr>
				<td style="text-align:center" class="text_title">
				Undelete Purchase
				</td>
			</tr> ');

		if($err_msg != '')
		{
			echo ('
				<!-- Error message -->
				<tr>
					<td>' . $err_msg . '</td>
				</tr>
			');
		}
		else
		{
			echo ('
				<tr>
					<td align="center"> Purchase has been undeleted.</td>
				</tr>
			');
		}
		
		echo ('
			</table>
		');
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_purchase_undelete = new PagePurchaseUndelete();
$page_purchase_undelete->run();

?>

