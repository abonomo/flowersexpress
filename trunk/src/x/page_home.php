<?php

require_once('framework.php');

class PageHome
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_home.php';
	
	//*** MEMBERS ***
	
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);	
	
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
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function process_input()
	{

	}
	
	private function show_output($err_msg = '')
	{
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_HOME);

	echo(' 
			<div align="center">
			  <form name="page_home" method="post" action="page_home">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
					<td align="left" valign="middle" class="text_title"><strong>Welcome to Flowers Express!</strong></td>
				</tr>
	
				<tr>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">					
						<tr>
							<td class="text_label">Sales Order Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_sales_order_add_edit.php">Add Sales Order</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_sales_order_list.php">List All Sales Orders</a></td>
						</tr>	
						
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Purchase Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_purchase_add_edit.php">Add Purchase</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_purchase_list.php">List All Purchases</a></td>
						</tr>	
						
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Customer Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_customer_add_edit.php">Add Customer</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_customer_list.php">List All Customers</a></td>
						</tr>	
						
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Product Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_product_add_edit.php">Add Product</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_product_list.php">List All Products</a></td>
						</tr>	

						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Supplier Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_supplier_add_edit.php">Add Supplier</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_supplier_list.php">List All Suppliers</a></td>
						</tr>	
						
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Shipper Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_shipper_add_edit.php">Add Shipper</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_shipper_list.php">List All Shippers</a></td>
						</tr>	
						
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Report Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_report_sales_menu.php">Sales Report</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_report_purchase_menu.php">Purchase Report</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_report_product.php">Product Information Report</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_inventory_report.php">Inventory Report</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_suppliers_report.php">Suppliers Report</a></td>
						</tr>	
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_customer_report.php">Customer Report</a></td>
						</tr>	
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<tr>
							<td class="text_label">Employee Operations&nbsp;</td>
						</tr>
						
						<tr>
							<td width="30%" align="right" valign="middle">&nbsp;</td>
							<td width="70%" align="left" valign="middle"><a href="page_employee_list.php">List All Employees</a></td>
						</tr>	
					</table></td>
					</tr>
			   </table>
			  </form>
			</div>		
			
		');
		
		ObjOuterArea::echo_bottom();
	
		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_home = new PageHome();
$page_home->run();

?>

