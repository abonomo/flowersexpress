<?

class SearchBar
{
	public static function display($obj_name, $search_box_value, $order_by_options, $order_by_value, $asc_or_desc_value, $cur_mode, $cur_page_num, $action_box_param, $action_box_param2)
	{
		// onclick="document.location=\'page_' . $obj_name . '_list.php?f_search=\' + escape(form_search.f_search.value) + \'&amp;f_order_by=\' + escape(form_search.f_order_by.value) + \'&amp;f_asc_or_desc=\' + escape(form_search.f_asc_or_desc.value)"
	
		//draw search form
		echo('
		<form name="form_search" method="post" action="page_' . $obj_name . '_list.php">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="33%" align="left" valign="middle">	
						<input name="f_mode" type="hidden" value="' . $cur_mode . '"/>
						<input name="f_page" type="hidden" value="' . $cur_page_num . '"/>
						<input name="f_action_box_param" type="hidden" value="' . $action_box_param . '"/>
						<input name="f_action_box_param2" type="hidden" value="' . $action_box_param2 . '"/>
						<input name="f_search" type="text" width="30" class="textbox" value="' . IO::prepout_sl($search_box_value, false) . '"/>
						<input name="f_search_btn" class="button" type="submit" value="Search"/>
					</td>
					<td align="left" valign="middle" width="33%">
		');
		
		//draw order by select box
		self::echo_order_by_select_box($order_by_options, $order_by_value);
		
		echo('
					</td>
					<td  width="33%" align="left" valign="middle">
		');
		
		//draw ascending sort or descending sort radio buttons
		self::echo_asc_or_desc_btns($asc_or_desc_value);
		
		echo('
					</td></tr>
				</table>		
			</form>
		');
	}
	
	private function echo_order_by_select_box($order_by_options, $order_by_value)
	{
		echo('
			Order&nbsp;By:&nbsp;<select name="f_order_by" class="textbox" onchange="form_search.f_page.value=1; form_search.submit();">
		');
		
		$cnt = count($order_by_options);
		for($i = 0; $i < $cnt; $i++)
		{
			echo('<option value="' . $order_by_options[$i][1] . '" ' . (($order_by_value == $order_by_options[$i][1]) ? 'selected' : '') . '>' . $order_by_options[$i][0] . '</option>');
		}
		
		echo('
			</select>		
		');
	}

	private function echo_asc_or_desc_btns($asc_or_desc_value)
	{
		echo('
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="middle">			
						Ascending:
					</td>
					<td align="left" valign="middle">			
						<input type="radio" name="f_asc_or_desc" value="asc" ' . (($asc_or_desc_value == 'asc') ? 'checked' : '') . ' onchange="form_search.f_page.value=1; form_search.submit();">
					</td>
					<td align="left" valign="middle">			
						&nbsp;&nbsp;&nbsp;
					</td>
					<td align="left" valign="middle">			
						Descending:
					</td>
					<td align="left" valign="middle">			
						<input type="radio" name="f_asc_or_desc" value="desc" ' . (($asc_or_desc_value == 'desc') ? 'checked' : '') . ' onchange="form_search.f_page.value=1; form_search.submit();">
					</td>
				</tr>
			</table>
		');
	}
}


?>