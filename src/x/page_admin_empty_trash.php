<?php

require_once('framework.php');

class PageAdminEmptyTrash
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_admin_empty_trash.php';
	
	//*** MEMBERS ***
	private $m_tables_to_delete_from = array (
		'customers',
		'products',
		'purchases',
		'sales_orders',
		'suppliers',
		'shippers' );
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_ADMIN);
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->process_input();
		
		$this->show_output();
	}
	
	private function get_input()
	{
	}
	
	private function verify_input()
	{
		
	}
	
	private function process_input()
	{
		// loop through each table name in $m_tables_to_delete_from
		// and delete items where trash_flag is set

		foreach ($this->m_tables_to_delete_from as $table)
		{
			//update trash_flag
			DB::send_query('
				DELETE FROM ' . $table . '
				WHERE
				trash_flag = 1
			');
		}
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_ADMIN);
		
		//echo inner area html here
		echo ('
		<!-- Title of the page -->
		<table width="100%">
			<tr>
				<td style="text-align:center" class="text_title">
				Trash was emptied successfully.
				</td>
			</tr> 
		</table>
		');

		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_admin_empty_trash = new PageAdminEmptyTrash();
$page_admin_empty_trash->run();

?>

