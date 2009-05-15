<?php

include('obj_result_box.php');

class ObjSalesOrderCompList
{
	private static $OBJ_NAME = 'sales_order_comp';	//page names based on this
	private static $NEEDED_FIELDS = 'icode, id';
	private static $NEEDED_JOINS = '';
	
	public function get_needed_fields()
	{
		return self::$NEEDED_FIELDS;
	}
	
	public function get_needed_joins()
	{
		return self::$NEEDED_JOINS;
	}
	
	public function display($action_box_mode, $cust_info_arr)
	{
		//display the list of results
		$cnt = count($cust_info_arr);
		for($i = 0; $i < $cnt; $i++)
		{
			$data_box_contents = $this->get_data_display($cust_info_arr[$i]);
			$action_box_contents = ResultDeleteMenu::create(self::$OBJ_NAME, $cust_info_arr[$i]['id']);
		
			ResultBox::display($data_box_contents, $action_box_contents);
		}
	}
	
	private function get_data_display($cust_info)
	{
		/*
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
		*/
		$obj_data_display = 'asdf';
		return $obj_data_display;
	}
}

?>