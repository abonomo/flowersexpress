<?php

require_once('framework.php');

/*
Deletes a shipper with given id
For example, if
	page_shipper_delete?f_id=5
is accessed, shipper with id 5 will have its trash flag set to 1
*/
class PageShipperDelete
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_shipper_delete.php';
	
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
		//get id of Shipper to edit
		$this->f_id = IO::get_input_sl_g('f_id','string');
	}
	
	private function verify_input()
	{
		if($this->f_id == "") 
			$this->show_output('Shipper not found.');
		
	}
	
	private function process_input()
	{
		//check that there's a unique result (is this necessary if id is defined as unique?)
		if( DB::bool_get_single_row_fq('SELECT * FROM shippers where id =\'' . $this->f_id .'\'') )
		{
			//update trash_flag
			DB::send_query('
			UPDATE shippers SET
			trash_flag = 1
			WHERE id=\'' . $this->f_id . '\'
			');
		}
		else
		{
			// was not a unique result
			$this->show_output('Shipper not found');
		}
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SHIPPERS);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<table width="100%">
			<tr>
				<td style="text-align:center" class="text_title">
				Delete Shipper
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
					<td align="center"> Shipper has been deleted.</td>
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
$page_shipper_delete = new PageShipperDelete();
$page_shipper_delete->run();

?>

