<?php

include('obj_result_box.php');

class ObjSalesOrderCompList
{
	private static $OBJ_NAME = 'sales_order_comp';	//page names based on this
	private static $NEEDED_FIELDS = '
		sales_orders.id AS sales_order_id,
		sales_orders.currency,
		sales_order_comps.id AS sales_order_comp_id,
		sales_order_comps.quantity_ordered,
		sales_order_comps.total_cost,
		purchase_comps.units,
		purchase_comps.transit_status,
		purchases.id AS purchase_id,
		purchases.icode AS purchase_icode,
		suppliers.id AS supplier_id,
		suppliers.icode AS supplier_icode,
		suppliers.company_name AS supplier_company_name,
		products.id AS product_id,
		products.icode AS product_icode,
		products.name AS product_name
		';
		
	private static $NEEDED_JOINS = '
		LEFT OUTER JOIN purchase_comps ON sales_order_comps.purchase_comp_id = purchase_comps.id
		LEFT OUTER JOIN purchases ON purchase_comps.purchase_id = purchases.id
		LEFT OUTER JOIN suppliers ON purchases.supplier_id = suppliers.id
		LEFT OUTER JOIN products ON purchase_comps.product_id = products.id
		LEFT OUTER JOIN sales_orders ON sales_order_comps.sales_order_id = sales_orders.id
		';
	
	public function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public function get_needed_joins()
	{
		return self::$NEEDED_JOINS;
	}
	
	public function display($action_box_mode, $obj_info_arr)
	{
		//display the list of results
		$cnt = count($obj_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($obj_info_arr[$i]);
			//$action_box_contents = ResultDeleteMenu::create('page_sales_order_add_edit.php?f_id=' . $obj_info_arr[$i]['sales_order_id'] . '&f_action=removecomp&f_comp_id=' . $obj_info_arr[$i]['sales_order_comp_id']);
			$action_box_contents = ResultDeleteMenu::create("form_sales_order.f_action.value='removecomp'; form_sales_order.f_id.value='" . $obj_info_arr[$i]['sales_order_id'] . "'; form_sales_order.f_comp_id.value='" . $obj_info_arr[$i]['sales_order_comp_id'] . "'; document.form_sales_order.submit();");

			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($obj_info)
	{
		//decide what is displayed with what labels
		$obj_line[0] = '<td align="left">&nbsp;Product&nbsp;ID:&nbsp;</td><td align="left"><a href="page_product_view.php?f_id=' . $obj_info['product_id'] . '">' . IO::prepout_sl($obj_info['product_icode'], 40) . '&nbsp;:&nbsp;' . IO::prepout_sl($obj_info['product_name'], 40) . '</a></td>';
		$obj_line[1] = '<td align="left">&nbsp;Quantity:&nbsp;</td><td align="left">' . IO::prepout_sl($obj_info['quantity_ordered'], 40) . '&nbsp;' . IO::prepout_sl($obj_info['units'], 40) . '</td>';
		$obj_line[2] = '<td align="left">&nbsp;Total&nbsp;Cost:&nbsp;</td><td align="left">' . IO::prepout_sl($obj_info['total_cost'], 40) . '&nbsp;' . IO::prepout_sl($obj_info['currency'], 40) . '</td>';
		$obj_line[3] = '<td align="left">&nbsp;Purchase&nbsp;ID:&nbsp;</td><td align="left"><a href="page_purchase_view.php?f_id=' . $obj_info['purchase_id'] . '">' . IO::prepout_sl($obj_info['purchase_icode'], 40) . '</a></td>';
		$obj_line[4] = '<td align="left">&nbsp;Supplier:&nbsp;</td><td align="left"><a href="page_supplier_view.php?f_id=' . $obj_info['supplier_id'] . '">' . IO::prepout_sl($obj_info['supplier_icode'] . ' : ' . $obj_info['supplier_company_name'], 40) . '</a></td>';
	
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0" >
			<tr>
				<td width="75%" align="left" valign="top">
					<table border="0" cellspacing="0" cellpadding="0" >
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= '<tr>' . $obj_line[$i] . '</tr>';
		}
		
		$obj_data_display .=
		'			</table>
				</td>
				<td width="75%" align="right" valign="top">
				</td>				
			</tr>
		</table>';

		return $obj_data_display;
	}
}

?>