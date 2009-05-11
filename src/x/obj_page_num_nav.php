<?php

/*
	Originally Written: Max Vujovic, May 10, 2009
	Revision History: -
*/

class PageNavBar
{
	//*** Member Variables ***
	//member variable for quickly printing the similar but not idential bottom page navigation bar
	private $m_cached_bottom_bar = '';

	//*** Public Functions ***
	//last_page is an optimization, saves calculations when passed in, usually is because page num has to be checked before this
	public function echo_top_bar($bar_width, $ws_border_top, $ws_border_bottom, $page_num, $num_results, $results_per_page, $action_script_left, $action_script_right, $max_pages=5, $left_col='', $last_page=-1)
	{	
		//top bar, top whitespace border: NOTE the regular echo (the borders are variable between top and bottom nav bars)
		if($ws_border_top != 0)
			echo('<table height="' . $ws_border_top. '" border="0" cellspacing="0" cellpadding="0" class="style_text_tiny"><tr><td>&nbsp;</td></tr></table>');
		
		//begin bar table, left side
		$this->echo_and_cache('<table width="' . $bar_width . '" border="0" cellspacing="0" cellpadding="0"><tr><td align="left" class="style_text_smg" height="15">');
	
		//print "results X-X" on left side by default, if no left column filler specified
		if($left_col == '')
		{
			//figure out "results X-X"
			$first_result_num = ($page_num-1)*$results_per_page + 1;
			$last_result_num = $page_num*$results_per_page;
			if($last_result_num > $num_results) $last_result_num = $num_results;
		
			//show "results X-X out of X": NOTE the regular echoes (the bottom nav bar doesn't show the results)
			if($num_results == 1)
			{
				//print message for one result total
				echo('One result found:');
			}
			else if($first_result_num == $last_result_num)
			{
				//print message for one result on the current page
				echo('Result ' . $first_result_num . ' out of ' . $num_results . ':');
			}
			else if($num_results != 0)	//if 0, output nothing here
			{
				//print message for multiple results
				echo('Results ' . $first_result_num . '-' . $last_result_num . ' out of ' . $num_results . ':');
			}
		}
		else echo($left_col);	//left column filler
	
		//finish up left side of bar
		$this->echo_and_cache('</td>');

		//if there are multiple pages, continue (at least two)
		if($num_results > $results_per_page)
		{
			//begin right side of bar
			$this->echo_and_cache('<td align="right"><table height="15" border="0" cellspacing="0" cellpadding="0"><tr>');
		
			//figure out last page num in result set, if not passed in (if not precalculated)
			if($last_page == -1) $last_page = (int)ceil((float)$num_results/(float)$results_per_page);
			
			//determine page num layout values, make max_pages odd
			$num_displayed_pages = ($max_pages <= $last_page) ? ($max_pages - !($max_pages % 2)) : $last_page;
			//if($last_page != $max_pages) $num_displayed_pages -= !($num_displayed_pages % 2);
			$mid_pt = (int)((int)$num_displayed_pages/(int)2);
			$start_page = $page_num-$mid_pt;
			if($start_page <= 0) $start_page = 1;
			else if($start_page > ($last_page - $num_displayed_pages + 1)) $start_page = ($last_page - $num_displayed_pages + 1);
			$end_page = $start_page + $num_displayed_pages;
			
			if($page_num != 1) $this->echo_active_page_num('Prev', $action_script_left, $action_script_right, $page_num-1);
			
			$this->echo_horiz_space();				
			for($i = $start_page; $i < $end_page; $i++)
			{
				if($i == $page_num) $this->echo_cur_page_num($page_num);				
				else $this->echo_active_page_num($i, $action_script_left, $action_script_right);
				
				$this->echo_horiz_space();					
			}
			
			if($page_num != $last_page) $this->echo_active_page_num('Next', $action_script_left, $action_script_right, $page_num+1);
		
				
			//finish up right side of bar
			$this->echo_and_cache('</tr></table></td>');
		}

		//finish up bar
		$this->echo_and_cache('</tr></table>');
		
		//top bar, bottom whitespace border
		if($ws_border_bottom != 0)
			echo('<table height="' . $ws_border_bottom. '" border="0" cellspacing="0" cellpadding="0" class="text_tiny"><tr><td>&nbsp;</td></tr></table>');
	}

	public function echo_bottom_bar($ws_border_top, $ws_border_bottom)
	{
		//bottom bar, top whitespace border (the borders are variable between top and bottom nav bars)
		if($ws_border_top != 0)
			echo('<table width="0%" height="' . $ws_border_top. '" border="0" cellspacing="0" cellpadding="0" class="text_tiny"><tr><td>&nbsp;</td></tr></table>');

		//print the saved "meat" from the top nav bar
		echo( $this->m_cached_bottom_bar );
		
		//bottom bar, bottom whitespace border, regular echo
		if($ws_border_bottom != 0)
			echo('<table width="0%" height="' . $ws_border_bottom. '" border="0" cellspacing="0" cellpadding="0" class="text_tiny"><tr><td>&nbsp;</td></tr></table>');
	}
	
	
	
	//*** Private Functions ***

	//intermediate echo and store for reechoing for this module
	private function echo_and_cache($output_str)
	{
		$this->m_cached_bottom_bar .= $output_str;
		echo( $output_str );
	}

	private function echo_horiz_space()
	{
		$this->echo_and_cache('<td>&nbsp;</td>');
	}

	private function echo_active_page_num($page_num_lbl, $action_script_left, $action_script_right, $page_num_digit='0')
	{
		if($page_num_digit == '0') $page_num_digit = $page_num_lbl;	//default value is page_num_lbl
		$this->echo_and_cache('
			<td align="center"
				onmouseover="gen_elm_highlight(this);" 
				onmouseout="gen_elm_dull(this);" 
				onclick="gen_elm_click(this, \'' . $action_url . $page_num_digit . '\'); ' . $action_script_left . $page_num_digit . $action_script_right . '">
				' . $page_num_lbl . '
			</td>
		');
	}

	private function echo_cur_page_num($the_page_num)
	{
		$this->echo_and_cache('
			<td align="center">
			<u>
				' . $the_page_num . '
			</u>
			</td>
		');
	}
}

?>