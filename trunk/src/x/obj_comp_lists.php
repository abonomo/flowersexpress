<?php

require_once('our_time.php');
include('obj_result_box.php');

class ObjSalesOrderCompList
{
	private static $OBJ_NAME = 'sales_order_comp';	//page names based on this
	private static $NEEDED_FIELDS = '
		sales_orders.id AS sales_order_id,
		sales_order_comps.id AS sales_order_comp_id,
		sales_order_comps.quantity_ordered,
		sales_order_comps.total_cost,
		purchase_comps.units,
		purchases.in_warehouse,
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
	
	private static $EXTRA_WHERE_CLAUSE = '';
	
	public function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public function get_needed_joins()
	{
		return self::$NEEDED_JOINS;
	}
	
	public function get_where_clause()
	{
		return self::$EXTRA_WHERE_CLAUSE;
	}
	
	public function display($action_box_mode, $obj_info_arr, $empty_action_box=false)
	{
		//display the list of results
		$cnt = count($obj_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($obj_info_arr[$i]);
			//$action_box_contents = ResultDeleteMenu::create('page_sales_order_add_edit.php?f_id=' . $obj_info_arr[$i]['sales_order_id'] . '&f_action=removecomp&f_comp_id=' . $obj_info_arr[$i]['sales_order_comp_id']);
			if($empty_action_box) $action_box_contents = '';
			else $action_box_contents = ResultDeleteMenu::create("form_sales_order.f_action.value='removecomp'; form_sales_order.f_id.value='" . $obj_info_arr[$i]['sales_order_id'] . "'; form_sales_order.f_comp_id.value='" . $obj_info_arr[$i]['sales_order_comp_id'] . "'; document.form_sales_order.submit();");

			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($obj_info)
	{
		//decide what is displayed with what labels
		$obj_line[0] = '<td align="left"><font class="data_label">&nbsp;Product&nbsp;ID:&nbsp;</font></td><td align="left"><a href="page_product_view.php?f_id=' . $obj_info['product_id'] . '">' . IO::prepout_sl($obj_info['product_icode'], 40) . '&nbsp;:&nbsp;' . IO::prepout_sl($obj_info['product_name'], 40) . '</a></td>';
		$obj_line[1] = '<td align="left"><font class="data_label">&nbsp;Quantity:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['quantity_ordered'], 40) . '&nbsp;' . IO::prepout_sl($obj_info['units'], 40) . '</font></td>';
		$obj_line[2] = '<td align="left"><font class="data_label">&nbsp;Total&nbsp;Cost:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['total_cost'], 40) . '&nbsp;</font></td>';
		$obj_line[3] = '<td align="left"><font class="data_label">&nbsp;Purchase&nbsp;ID:&nbsp;</font></td><td align="left"><a href="page_purchase_view.php?f_id=' . $obj_info['purchase_id'] . '">' . IO::prepout_sl($obj_info['purchase_icode'], 40) . '</a></td>';
		$obj_line[4] = '<td align="left"><font class="data_label">&nbsp;Supplier:&nbsp;</font></td><td align="left"><a href="page_supplier_view.php?f_id=' . $obj_info['supplier_id'] . '">' . IO::prepout_sl($obj_info['supplier_icode'] . ' : ' . $obj_info['supplier_company_name'], 40) . '</a></td>';
	
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

//TODO:!!! don't count deleted

class ObjPurchaseCompList
{
	public $RENAME_MAIN_TABLE = 'obj_table';
	private static $EXTRA_WHERE_CLAUSE = ' AND purchases.is_cart = 0 AND purchases.trash_flag = 0';

	private static $OBJ_NAME = 'purchase_comp';	//page names based on this
	private static $NEEDED_FIELDS = '
		obj_table.id,
		obj_table.units,
		obj_table.expiration_date,
		obj_table.min_price_per_unit,
		suppliers.id AS supplier_id,
		products.id as product_id,
		products.icode AS product_icode,
		products.name AS product_name,
		purchases.id AS purchase_id,
		purchases.delivery_date,
		purchases.in_warehouse,
		purchases.icode AS purchase_icode,
		suppliers.company_name AS supplier_company_name,
		(obj_table.quantity_sellable - IFNULL((SELECT SUM(sales_order_comps.quantity_ordered) FROM sales_order_comps LEFT OUTER JOIN sales_orders ON sales_order_comps.sales_order_id = sales_orders.id WHERE sales_order_comps.purchase_comp_id=obj_table.id AND sales_orders.trash_flag = 0 GROUP BY sales_order_comps.purchase_comp_id),0)) AS quantity_left
		';	//CHANGE: in_warehouse, delivery_date table belonging
		
	private static $NEEDED_JOINS = '
		LEFT OUTER JOIN purchases ON obj_table.purchase_id = purchases.id
		LEFT OUTER JOIN suppliers ON purchases.supplier_id = suppliers.id
		LEFT OUTER JOIN products ON obj_table.product_id = products.id
		';
	
	public function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public function get_needed_joins()
	{
		return self::$NEEDED_JOINS;
	}
	
	public function get_where_clause()
	{
		return self::$EXTRA_WHERE_CLAUSE;
	}
	
	public function display($action_box_mode, $obj_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($obj_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($obj_info_arr[$i]);
			$action_box_contents = ResultQuantityMenu::create('page_sales_order_add_edit.php?f_action=addcomp&f_id=' . $action_box_param . '&f_comp_id=' . $obj_info_arr[$i]['id']);

			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($obj_info)
	{
		$transit_status = ($obj_info['in_warehouse'] != 0 ? 'in warehouse' : 'in transit');
	
		if($obj_info['quantity_left'] < 0) $quantity_left_color = "#DD0000";
		else $quantity_left_color = "#0000DD";
	
		//decide what is displayed with what labels
		$obj_line[0] = '<td align="left"><font class="data_label">&nbsp;Product&nbsp;ID:&nbsp;</font></td><td align="left"><a href="page_product_view.php?f_id=' . $obj_info['product_id'] . '">' . IO::prepout_sl($obj_info['product_icode'], 20) . '&nbsp;:&nbsp;' . IO::prepout_sl($obj_info['product_name'], 20) . '</a></td>';
		$obj_line[1] = '<td align="left"><font class="data_label">&nbsp;Available:&nbsp;</font></td><td align="left"><font style="font-weight: bold; color: ' . $quantity_left_color . ';">' . IO::prepout_sl($obj_info['quantity_left'], 20) . '</font>&nbsp;' . IO::prepout_sl($obj_info['units'], 20) . '&nbsp;<i>' . IO::prepout_sl($transit_status, 20) . '</i></font></td>';
		$obj_line[2] = '<td align="left"><font class="data_label">&nbsp;Min&nbsp;Price/Unit:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['min_price_per_unit'], 20) . '</font></td>';
		$obj_line[3] = '<td align="left"><font class="data_label">&nbsp;Expires:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl(OurTime::datetime_to_js($obj_info['expiration_date']), 20) . '</font></td>';
		$obj_line[4] = '<td align="left"><font class="data_label">&nbsp;Expected:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl(OurTime::datetime_to_js($obj_info['delivery_date']), 20) . '</font></td>';
		$obj_line[5] = '<td align="left"><font class="data_label">&nbsp;Purchase&nbsp;ID:&nbsp;</font></td><td align="left"><a href="page_purchase_view.php?f_id=' . $obj_info['purchase_id'] . '">' . IO::prepout_sl($obj_info['purchase_icode'], 20) . '&nbsp;(' . IO::prepout_sl($obj_info['supplier_company_name'], 20) . ')</a></td>';
	
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



class ObjEditPurchaseCompList
{
	public $RENAME_MAIN_TABLE = 'obj_table';
	public $EXTRA_WHERE_CLAUSE = ' AND purchases.is_cart = 0';

	private static $OBJ_NAME = 'purchase_comps';	//page names based on this
	private static $NEEDED_FIELDS = '
		purchase_comps.id,
		purchase_comps.purchase_id,
		purchase_comps.units,
		purchase_comps.quantity_purchased,
		purchase_comps.quantity_sellable,
		purchase_comps.expiration_date,
		purchase_comps.min_price_per_unit,
		products.icode AS product_icode,
		products.name AS product_name
		';	//CHANGE: in_warehouse, delivery_date table belonging
		
	private static $NEEDED_JOINS = '
		LEFT OUTER JOIN products ON purchase_comps.product_id = products.id
		';
	
	public function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public function get_needed_joins()
	{
		return self::$NEEDED_JOINS;
	}
	
	public function display($action_box_mode, $obj_info_arr, $empty_action_box=false)
	{
		//display the list of results
		$cnt = count($obj_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($obj_info_arr[$i]);
			if($empty_action_box) $action_box_contents = '';
			else $action_box_contents = ResultFullPurchaseCompMenu::create($obj_info_arr[$i]['purchase_id'], $obj_info_arr[$i]['id']);

			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($obj_info)
	{
		$transit_status = ($obj_info['in_warehouse'] != 0 ? 'in warehouse' : 'in transit');
	
		//decide what is displayed with what labels
		$obj_line[0] = '<td align="left"><font class="data_label">&nbsp;Product&nbsp;ID:&nbsp;</font></td><td align="left"><a href="page_product_view.php?f_id=' . $obj_info['product_id'] . '">' . IO::prepout_sl($obj_info['product_icode'], 20) . '&nbsp;:&nbsp;' . IO::prepout_sl($obj_info['product_name'], 20) . '</a></td>';
		$obj_line[1] = '<td align="left"><font class="data_label">&nbsp;Quantity&nbsp;Bought:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['quantity_purchased'], 20) . '&nbsp;' . IO::prepout_sl($obj_info['units'], 20) . '</font></td>';
		$obj_line[2] = '<td align="left"><font class="data_label">&nbsp;Quantity&nbsp;Sellable:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['quantity_sellable'], 20) . '&nbsp;' . IO::prepout_sl($obj_info['units'], 20) . '</font></td>';
		$obj_line[3] = '<td align="left"><font class="data_label">&nbsp;Units:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['units'], 20) . '</font></td>';		
		$obj_line[4] = '<td align="left"><font class="data_label">&nbsp;Min&nbsp;Price/Unit:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl($obj_info['min_price_per_unit'], 20) . '</font></td>';
		$obj_line[5] = '<td align="left"><font class="data_label">&nbsp;Expires:&nbsp;</font></td><td align="left"><font class="data_value">' . IO::prepout_sl(OurTime::datetime_to_js($obj_info['expiration_date']), 20) . '</td>';
	
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