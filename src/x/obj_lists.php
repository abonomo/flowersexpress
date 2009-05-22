<?php

include('obj_result_box.php');

/*
//this is what the abstract class would look like, but PHP doesn't need it:
abstract class ObjGenericList
{
	abstract public function get_needed_fields();
	abstract public function display($action_box_mode, $cust_info_arr);
	
	abstract private function get_data_display($cust_info);
}
*/

//basic customer list, no pagination
class ObjCustomerList
{
	private static $OBJ_NAME = 'customer';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, company_name, contact_name, contact_dept, address_line_1, city, office_phone_number, cell_phone_number';
	private static $NEEDED_JOINS = '';
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
	
	public function display($action_box_mode, $cust_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($cust_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($cust_info_arr[$i]);
			//select a customer for a sales order mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL) $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit.php?f_id=' . $action_box_param . '&f_action=savecustomer&f_customer_id=' . $cust_info_arr[$i]['id']);
			//full action display
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $cust_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($cust_info)
	{
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('', $cust_info['icode'], 30, 'No Code') . IO::prepout_sl_label('&nbsp;-&nbsp;', $cust_info['company_name'], 30, 'No Company Name');
		$obj_line[0] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Contact Name:&nbsp;', $cust_info['contact_name'], 20) . IO::prepout_sl_label(',&nbsp;', $cust_info['contact_dept'], 20);
		$obj_line[1] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Address:&nbsp;', $cust_info['address_line_1'], 20) . IO::prepout_sl_label(',&nbsp;', $cust_info['city'], 20);
		$obj_line[2] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Office:&nbsp;', $cust_info['office_phone_number'], 20) . IO::prepout_sl_label(',&nbsp;Mobile:&nbsp;', $cust_info['cell_phone_number'], 20);
	
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0" >
			<tr>
				<td width="75%" align="left" valign="top">
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $cust_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
				<td width="75%" align="right" valign="top">
				</td>				
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}



class ObjProductList
{
	private static $OBJ_NAME = 'product';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, name, type, subtype1, subtype2, typical_lifespan_days, typical_units, typical_min_price_per_unit, description, trash_flag';
	private static $NEEDED_JOINS = '';
	private static $EXTRA_WHERE_CLAUSE = '';
	
	// hahaha this isn't clean but I'm going for the 'just implement something' approach
	private static $m_visibility = '';
	private static $m_in_trash = '';

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
	
	public function display($action_box_mode, $prod_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($prod_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($prod_info_arr[$i]);
			//select a product for a purchase component mode	//TODO: figure this out
			if($action_box_mode == ResultSelectMenu::$MODE_VAL) $action_box_contents = ResultSelectMenu::create('page_purchase_comp_add_edit.php?f_id=' . $action_box_param . '&f_action=saveproduct&f_product_id=' . $prod_info_arr[$i]['id']);
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $prod_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($prod_info)
	{
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('', $prod_info['icode'], 30, 'No Code') . IO::prepout_sl_label('&nbsp;-&nbsp;', $prod_info['name'], 30, 'No Name');
		$obj_line[0] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Type:&nbsp;', $prod_info['type'], 20);
		$obj_line[1] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Sub Type 1:&nbsp;', $prod_info['subtype1'], 20) . IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Sub Type 2:&nbsp;', $prod_info['subtype2'], 20);
		$obj_line[2] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Lifespan:&nbsp;', $prod_info['typical_lifespan_days'], 20) . ' days';
		$obj_line[3] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Units:&nbsp;', $prod_info['typical_units'], 20) . IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Min price per unit:&nbsp;', $prod_info['typical_min_price_per_unit'], 20);
		$obj_line[4] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Description:&nbsp;', $prod_info['description'], 50);

		if( $prod_info['trash_flag'] == 1 )
		{
			$m_visibility = ' style="opacity:0.6;filter:alpha(opacity=60)"';
			$m_in_trash = '[In trash bin] - ';
		}
		
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0"' . $m_visibility . '>
			<tr>
				<td align="left">
					' . $m_in_trash . '
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $prod_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}


class ObjSupplierList
{
	private static $OBJ_NAME = 'supplier';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, company_name, contact_name, contact_dept, address_line_1, city, office_phone_number, cell_phone_number, trash_flag';
	private static $NEEDED_JOINS = '';
	private static $EXTRA_WHERE_CLAUSE = '';
	
	// hahaha this isn't clean but I'm going for the 'just implement something' approach
	private static $m_visibility = '';
	private static $m_in_trash = '';

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
	
	public function display($action_box_mode, $supplier_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($supplier_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($supplier_info_arr[$i]);
			//select a Supplier for a purchase mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL) $action_box_contents = ResultSelectMenu::create('page_purchase_add_edit.php?f_id=' . $action_box_param . '&f_action=savesupplier&f_supplier_id=' . $supplier_info_arr[$i]['id']);
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $supplier_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($supplier_info)
	{
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('', $supplier_info['icode'], 30, 'No Code') . IO::prepout_sl_label('&nbsp;-&nbsp;', $supplier_info['company_name'], 30, 'No Company Name');
		$obj_line[0] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Contact Name:&nbsp;', $supplier_info['contact_name'], 20) . IO::prepout_sl_label(',&nbsp;', $supplier_info['contact_dept'], 20);
		$obj_line[1] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Address:&nbsp;', $supplier_info['address_line_1'], 20) . IO::prepout_sl_label(',&nbsp;', $supplier_info['city'], 20);
		$obj_line[2] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Office:&nbsp;', $supplier_info['office_phone_number'], 20) . IO::prepout_sl_label(',&nbsp;Mobile:&nbsp;', $supplier_info['cell_phone_number'], 20);

		if( $supplier_info['trash_flag'] == 1 )
		{
			$m_visibility = ' style="opacity:0.6;filter:alpha(opacity=60)"';
			$m_in_trash = '[In trash bin] - ';
		}
		
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0"' . $m_visibility . '>
			<tr>
				<td align="left">
					' . $m_in_trash . '
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $supplier_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}


class ObjShipperList
{
	private static $OBJ_NAME = 'shipper';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, company_name, contact_name, contact_dept, address_line_1, city, office_phone_number, cell_phone_number, trash_flag';
	private static $NEEDED_JOINS = '';
	private static $EXTRA_WHERE_CLAUSE = '';
	
	// hahaha this isn't clean but I'm going for the 'just implement something' approach
	private static $m_visibility = '';
	private static $m_in_trash = '';

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
	
	public function display($action_box_mode, $shipper_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($shipper_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($shipper_info_arr[$i]);
			//select a Shipper for a sales order mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL . 'fororder') $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit.php?f_id=' . $action_box_param . '&f_action=saveshipper&f_shipper_id=' . $shipper_info_arr[$i]['id']);
			//select a Shipper for a purchase mode
			else if($action_box_mode == ResultSelectMenu::$MODE_VAL . 'forpurchase') $action_box_contents = ResultSelectMenu::create('page_purchase_add_edit.php?f_action=saveshipper&f_shipper_id=' . $shipper_info_arr[$i]['id']);			
			//full action display
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $shipper_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($shipper_info)
	{
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('', $shipper_info['icode'], 30, 'No Code') . IO::prepout_sl_label('&nbsp;-&nbsp;', $shipper_info['company_name'], 30, 'No Company Name');
		$obj_line[0] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Contact Name:&nbsp;', $shipper_info['contact_name'], 20) . IO::prepout_sl_label(',&nbsp;', $shipper_info['contact_dept'], 20);
		$obj_line[1] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Address:&nbsp;', $shipper_info['address_line_1'], 20) . IO::prepout_sl_label(',&nbsp;', $shipper_info['city'], 20);
		$obj_line[2] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Office:&nbsp;', $shipper_info['office_phone_number'], 20) . IO::prepout_sl_label(',&nbsp;Mobile:&nbsp;', $shipper_info['cell_phone_number'], 20);

		if( $shipper_info['trash_flag'] == 1 )
		{
			$m_visibility = ' style="opacity:0.6;filter:alpha(opacity=60)"';
			$m_in_trash = '[In trash bin] - ';
		}
		
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0"' . $m_visibility . '>
			<tr>
				<td align="left">
					' . $m_in_trash . '
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $shipper_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}


class ObjPurchaseList
{
	private static $OBJ_NAME = 'purchase';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, is_cart, shipment_details, in_warehouse, delivery_date, price, trash_flag';
	private static $NEEDED_JOINS = '';
	private static $EXTRA_WHERE_CLAUSE = 'AND is_cart = 0';
	
	// hahaha this isn't clean but I'm going for the 'just implement something' approach
	private static $m_visibility = '';
	private static $m_in_trash = '';
	private static $m_is_cart = '';
	private static $m_in_warehouse = '';

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
	
	public function display($action_box_mode, $purchase_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($purchase_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($purchase_info_arr[$i]);
			//select a purchase for a sales order mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL . 'fororder') $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit.php?f_id=' . $action_box_param . '&f_action=savepurchase&f_purchase_id=' . $purchase_info_arr[$i]['id']);
			//select a purchase for a purchase mode
			else if($action_box_mode == ResultSelectMenu::$MODE_VAL . 'forpurchase') $action_box_contents = ResultSelectMenu::create('page_purchase_add_edit?.phpf_action=savepurchase&f_purchase_id=' . $purchase_info_arr[$i]['id']);			
			//full action display
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $purchase_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($purchase_info)
	{
		$line_index = 0;
		
		if( $purchase_info['in_warehouse'] == 1 )
		{
			$m_in_warehouse = "In Warehouse";
		}
		
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('Purchase: ', $purchase_info['icode'], 30, 'No Code');
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Shipment Details:&nbsp;', $purchase_info['shipment_details'], 20) . $m_in_warehouse;
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Delivery Date:&nbsp;', $purchase_info['delivery_date'], 20);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Price:&nbsp;', $purchase_info['price'], 20);

		if( $purchase_info['trash_flag'] == 1 )
		{
			$m_visibility = ' style="opacity:0.6;filter:alpha(opacity=60)"';
			$m_in_trash = '[In trash bin] - ';
		}
		
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0"' . $m_visibility . '>
			<tr>
				<td align="left">
					' . $m_in_trash . '
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $purchase_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}



class ObjSalesOrderList
{
	private static $OBJ_NAME = 'sales_order';	//page names based on this
	private static $NEEDED_FIELDS = 'sales_orders.icode, sales_orders.id, is_cart, shipper_id, customer_id, shipment_details, special, order_date, delivery_date, price, currency, sales_orders.trash_flag';
	private static $NEEDED_JOINS = '';
	private static $EXTRA_WHERE_CLAUSE = 'AND is_cart = 0';
	
	private static $m_visibility = '';
	private static $m_in_trash = '';
	private static $m_is_cart = '';
	private static $m_in_warehouse = '';
	private static $m_special = '';

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
	
	public function display($action_box_mode, $sales_order_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($sales_order_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($sales_order_info_arr[$i]);
			//select a sales_order for a sales order mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL . 'fororder') $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit.php?f_id=' . $action_box_param . '&f_action=savesales_order&f_sales_order_id=' . $sales_order_info_arr[$i]['id']);
			//select a sales_order for a sales_order mode
			else if($action_box_mode == ResultSelectMenu::$MODE_VAL . 'forsales_order') $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit?.phpf_action=savesales_order&f_sales_order_id=' . $sales_order_info_arr[$i]['id']);			
			//full action display
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $sales_order_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($sales_order_info)
	{
		$line_index = 0;
		
		if( $sales_order_info['in_warehouse'] == 1 )
		{
			$this->m_in_warehouse = "&nbsp;&nbsp;&nbsp;&nbsp;In Warehouse";
		}
		
		if( $sales_order_info['special'] == 1 )
		{
			$this->m_special = "*Special* ";
		}
		
		//decide what is displayed with what labels
		$obj_title_link_text = $this->m_special . IO::prepout_sl_label('Sales Order: ', $sales_order_info['icode'], 30, 'No Code');
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Shipment Details:&nbsp;', $sales_order_info['shipment_details'], 20) . $this->m_in_warehouse;
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Order Date:&nbsp;', $sales_order_info['order_date'], 20);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Delivery Date:&nbsp;', $sales_order_info['delivery_date'], 20);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Price:&nbsp;', $sales_order_info['price'], 20) . IO::prepout_sl_label(' ', $sales_order_info['currency'], 20);

		if( $sales_order_info['trash_flag'] == 1 )
		{
			$this->m_visibility = ' style="opacity:0.6;filter:alpha(opacity=60)"';
			$this->m_in_trash = '[In trash bin] - ';
		}
		
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0"' . $this->m_visibility . '>
			<tr>
				<td align="left">
					' . $this->m_in_trash . '
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $sales_order_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}


class ObjEmployeeList
{
	private static $OBJ_NAME = 'employee';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id, email, auth_level, first_name, last_name, title, dept_name, office_location, office_phone_number, cell_phone_number, fax_number, trash_flag';
	private static $NEEDED_JOINS = '';
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
	
	public function display($action_box_mode, $employee_info_arr, $action_box_param)
	{
		//display the list of results
		$cnt = count($employee_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($employee_info_arr[$i]);
			//select a employee for a sales order mode
			if($action_box_mode == ResultSelectMenu::$MODE_VAL) $action_box_contents = ResultSelectMenu::create('page_sales_order_add_edit.php?f_id=' . $action_box_param . '&f_action=saveemployee&f_employee_id=' . $employee_info_arr[$i]['id']);
			//full action display
			else $action_box_contents = $action_box_contents = ResultFullMenu::create(self::$OBJ_NAME, $employee_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($employee_info)
	{
		$line_index = 0;
		//decide what is displayed with what labels
		$obj_title_link_text = IO::prepout_sl_label('', $employee_info['icode'], 30, 'No Code') . IO::prepout_sl_label('&nbsp;-&nbsp;', $employee_info['last_name'], 30) . IO::prepout_sl_label(', ', $employee_info['first_name'], 30);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Title:&nbsp;', $employee_info['title'], 30) . IO::prepout_sl_label(',&nbsp;', $employee_info['dept_name'], 30);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Office Location:&nbsp;', $employee_info['office_location'], 30);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Office:&nbsp;', $employee_info['office_phone_number'], 20) . IO::prepout_sl_label(',&nbsp;Mobile:&nbsp;', $employee_info['cell_phone_number'], 20);
		$obj_line[$line_index++] = IO::prepout_sl_label('&nbsp;&nbsp;&nbsp;Fax:&nbsp;', $employee_info['fax_number'], 20);
	
		//display the object title link and data lines
		$obj_data_display =
		'<table width="100%" cellspacing="0" cellpadding="0" >
			<tr>
				<td width="75%" align="left" valign="top">
					<a href="page_' . self::$OBJ_NAME .'_view.php?f_id=' . $employee_info['id'] . '"><b>' . $obj_title_link_text . '</b></a><br>
		';
		
		//append the data lines
		for($i = 0; $i < count($obj_line); $i++)
		{
			$obj_data_display .= $obj_line[$i] . '<br>';
		}
		
		$obj_data_display .=
		'		</td>
				<td width="75%" align="right" valign="top">
				</td>				
			</tr>
		</table>';
	
		return $obj_data_display;
	}
}


?>