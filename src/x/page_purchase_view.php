<?php

require_once('framework.php');
require_once('obj_comp_lists.php');

class PagePurchaseView
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_purchase_view.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_supplier_id;
	private $f_shipper_id;
	private $f_shipment_details;
	private $f_notes;
	private $trash_flag;
	
	private $f_shipper_comp_name;
	private $f_shipper_icode;	
	private $f_supplier_comp_name;
	private $f_supplier_icode;
	
	private $f_created_first;
	private $f_created_last;
	private $f_updated_first;
	private $f_updated_last;
	private $f_created_employee_id;
	private $f_updated_employee_id;
	
	private $f_created_date;
	private $f_updated_date;
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_ONLY);
	
		$this->get_input(); 
		
		$this->verify_input();
		
		$this->get_output();
		
		$this->show_output();
	}
	
	private function get_input()
	{
		$this->f_id = IO::get_input_sl_g('f_id', 'string');
	}
	
	private function verify_input()
	{
		/*
		//Error Handling Example:
		if(something is bad) $this->show_output('Error: Field X needs to be corrected');
		*/
		
	}
	
	private function get_output()
	{
					//get values from database
			$purch_info = DB::get_single_row_fq('
				SELECT  icode, 
						supplier_id, 
						shipper_id, 
						shipment_details, 
						notes,	
						trash_flag,
						
						created_employee_id, 
						updated_employee_id, 
						created_date, 
						updated_date
				FROM purchases WHERE id=\'' . $this->f_id . '\''
			);
			
			$this->f_icode 					= $purch_info['icode'];
			$this->f_supplier_id 			= $purch_info['supplier_id'];
			$this->f_shipper_id 			= $purch_info['shipper_id'];
			$this->f_shipment_details 		= $purch_info['shipment_details'];
			$this->f_notes 					= $purch_info['notes'];
			$this->f_trash_flag				= $purch_info['trash_flag'];
			
			$this->f_created_employee_id	= $purch_info['created_employee_id'];
			$this->f_updated_employee_id	= $purch_info['updated_employee_id'];
			$this->f_created_date			= $purch_info['created_date'];
			$this->f_updated_date			= $purch_info['updated_date'];
			
			
			//QUERY for created by user info
			$purch_info_cre = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM employees
				WHERE employees.id=\'' . $this->f_created_employee_id . '\''
			);
			$this->f_created_first 		= $purch_info_cre['first_name'];
			$this->f_created_last 		= $purch_info_cre['last_name'];
			
			//QUERY for Updated by user info
			$purch_info_up = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM purchases
				LEFT OUTER JOIN employees ON purchases.updated_employee_id = employees.id
				WHERE purchases.id=\'' . $this->f_id . '\''
			);

			$this->f_updated_first 		= $purch_info_up['first_name'];
			$this->f_updated_last 		= $purch_info_up['last_name'];
			
			//QUERY for order's Supplier info
			$purch_suppl = DB::get_single_row_fq
			('
				SELECT  suppliers.company_name,
						suppliers.icode
				FROM suppliers
				WHERE suppliers.id=\'' . $this->f_supplier_id . '\''
			);
			$this->f_supplier_comp_name	= $purch_suppl['company_name'];
			$this->f_supplier_icode		= $purch_suppl['icode'];
			
			//QUERY for order's Shipper info
			$purch_ship = DB::get_single_row_fq
			('
				SELECT  shippers.company_name,
						shippers.icode
				FROM shippers
				WHERE shippers.id=\'' . $this->f_shipper_id . '\''
			);
			$this->f_shipper_comp_name	= $purch_ship['company_name'];
			$this->f_shipper_icode	= $purch_ship['icode'];	
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PURCHASES);
		
		//echo inner area html
//	echo ('
//		<!-- Title of the page -->
//		<form name="purchases_view" method="post" action="page_purchases_view.php?f_id=' . IO::prepout_url($this->f_id) . '">
//		<table width="100%">
//			<tr>
//				<td width="25%"> </td>
//				<td width="75%" class="text_title">
//				View Purchase
//				</td>
//			</tr>
//		</table>
echo('
		<div align="center">
		<form name="purchases_view" method="post" action="page_purchases_view.php?f_id=' . IO::prepout_url($this->f_id) . '">
		<table width="600" border="0" cellpadding="0" cellspacing="0">

				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle">&nbsp;</td>
						  <td width="75%" align="left" valign="middle" class="text_title">View Purchase Order</td>
						</tr>
					</table></td>
				  </tr>

<!-- Link of Edit, Delete & Undeleted starts here -->
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="27%" align="right" valign="middle">&nbsp;</td>
					<td width="73%" align="left" valign="middle"> ');
					
// show edit & delete link below the title if trash_flag != 1
// show undelete link below the title if trash_flag == 1
				if( $this->f_trash_flag != '1' )
				{
					echo (' <img src="../img/icon_edit.gif"/> ');
					echo (' <a href="page_purchase_add_edit.php?f_mode=edit&amp;f_id=' . $this->f_id . '">Edit</a>');
					echo ('  ');
					echo (' <img src="../img/icon_delete.gif"/> ');
					echo (' <a href="#" onclick="if(window.confirm(\'Are you sure you want to delete this entry?\')) { document.location=\'page_purchase_delete.php?f_id=' . $this->f_id .'\'; } return false;"/>Delete</a></td>');
				}
				else
				{
					echo (' <img src="../img/icon_undelete.gif"/> ');
					echo (' <a href="page_purchase_undelete.php?f_id=' . $this->f_id . '">Undelete</a></td>');
				}
				echo ('	</tr>
					</table></td>
				  </tr>
<!-- end of Link eddition -->
		
			<!-- View fields of a purchase -->
			<table width="100%">
			
				<tr>
					<td>&nbsp;</td>
				 </tr>	
			
				<tr>
					<td class="text_label">ID Code:&nbsp;</td>
					<td class="form_input">' . IO::prepout_sl($this->f_icode, false) . '');
						  
						 //if the purchase has been deleted, print trash icon
						if( $this->f_trash_flag == '1' )
						{
							echo (' <img src="../img/icon_trash.gif"/> ');
							echo (' (This item has been deleted!)');
						}
						
			echo('</td>
				</tr>
				
				<tr>
					<td class="text_label">Supplier:&nbsp;</td>
					<td class="form_input">' . IO::prepout_sl($this->f_supplier_icode, false) . '&nbsp;:&nbsp;<a href="page_supplier_view.php?f_id=' . 
						IO::prepout_sl($this->f_supplier_id, false) . '">' . IO::prepout_ml_html($this->f_supplier_comp_name) . '</a></td>
				</tr>
				
				<tr>
					<td class="text_label">Shipper:&nbsp;</td>
					<td class="form_input">' . IO::prepout_sl($this->f_shipper_icode, false) . '&nbsp;:&nbsp;<a href="page_shipper_view.php?f_id=' . 
						IO::prepout_sl($this->f_shipper_id, false) . '">' . IO::prepout_ml_html($this->f_shipper_comp_name) . '</a></td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
				 </tr>	
				 
				<tr>
					<td class="text_label">Shipment&nbsp;Details:&nbsp;</td>
					<td class="form_input"><div>' . IO::prepout_ml_textarea($this->f_shipment_details) . '</div></td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
				</tr>	
				  
				<tr>
				  <td width="25%" align="right" valign="middle" class="text_label">Created:&nbsp;</td>
				  <td width="75%" align="left" valign="middle"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_created_employee_id, false) . '">'  . 
							IO::prepout_sl($this->f_created_first, false) . '&nbsp;' . IO::prepout_sl($this->f_created_last, false)  . '</a>&nbsp;on&nbsp;' . 
							IO::prepout_ml_html($this->f_created_date) . '</td>
				 </tr>
				 
				<tr>
				  <td width="25%" align="right" valign="middle" class="text_label">Updated:&nbsp;</td>
				  <td width="75%" align="left" valign="middle"><a href="page_employee_view.php?f_id=' . IO::prepout_sl($this->f_updated_employee_id, false) . '">'  . 
							IO::prepout_sl($this->f_updated_first, false) . '&nbsp;' . IO::prepout_sl($this->f_updated_last, false)  . '</a>&nbsp;on&nbsp;' . 
							IO::prepout_ml_html($this->f_updated_date) . '</td>
				 </tr>	
				 
				  <tr>
					<td>&nbsp;</td>
				 </tr>	
				 
				 <tr>
					<td class="text_label">Notes:&nbsp;</td>
					<td class="form_input"><div>' . IO::prepout_ml_textarea($this->f_notes) . '</div></td>
				</tr>
								
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				 </tr>	
				 
				 <tr>
					<td>&nbsp;</td>
					<td class="text_title">Purchase Contents:&nbsp;</td>
				</tr>
				
			</table>
			');	
			
		
			//make list object
			$this->m_obj_purchase_comp_list = new ObjEditPurchaseCompList();
		
			$this->m_comp_info_arr = DB::get_all_rows_fq
			(
				'SELECT ' . 
				$this->m_obj_purchase_comp_list->get_needed_fields() . 
				'FROM purchase_comps' .
				$this->m_obj_purchase_comp_list->get_needed_joins() .
				'WHERE purchase_comps.purchase_id=\'' . $this->f_id . '\''
			);
			
			$this->m_obj_purchase_comp_list->display('empty', $this->m_comp_info_arr, true);
			

			
		ObjOuterArea::echo_bottom();

		//output is always the last thing done when called
		exit();
	}
}

//create an instance of the page and run it
$page_purchase_view = new PagePurchaseView();
$page_purchase_view->run();

?>