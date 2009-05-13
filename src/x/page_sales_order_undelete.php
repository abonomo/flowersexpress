<?php

require_once('framework.php');

/*
Undeletes a sales order with given id
For example, if
	page_sales_order_undelete?f_id=5
is accessed, sales_order with id 5 will have its trash flag set to 1
*/
class PageSalesOrderUndelete
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_sales_order_undelete.php';
	
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
		//get id of sales order to edit
		$this->f_id = IO::get_input_sl_g('f_id','string');
	}
	
	private function verify_input()
	{
		if($this->f_id == "") 
			$this->show_output('Sales order not found.');
		
	}
	
	private function process_input()
	{
		//check that there's a unique result (is this necessary if id is defined as unique?)
		if( DB::bool_get_single_row_fq('SELECT * FROM sales_orders where id =\'' . $this->f_id .'\'') )
		{
			//update trash_flag
			DB::send_query('
			UPDATE sales_orders SET
			trash_flag = 0
			WHERE id=\'' . $this->f_id . '\'
			');
		}
		else
		{
			// was not a unique result
			$this->show_output('Sales order not found');
		}
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_SALES_ORDERS);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<table width="100%">
			<tr>
				<td style="text-align:center" class="text_title">
				Undelete Sales Order
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
					<td> Sales order has been undeleted.</td>
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
$page_sales_order_undelete = new PageSalesOrderUndelete();
$page_sales_order_undelete->run();

?>
