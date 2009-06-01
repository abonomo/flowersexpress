<?php

include('framework.php');

class ObjOuterArea
{
	//pass one of these into echo_top to highlight that tab as currently active
	public static $TAB_NONE = -1;
	public static $TAB_HOME = 0;
	public static $TAB_SALES_ORDERS = 1;
	public static $TAB_PURCHASES = 2;
	public static $TAB_CUSTOMERS = 3;
	public static $TAB_PRODUCTS = 4;
	public static $TAB_SUPPLIERS = 5;
	public static $TAB_SHIPPERS = 6;
	public static $TAB_REPORTS = 7;
	public static $TAB_EMPLOYEES = 8;
	public static $TAB_ADMIN = 9;
	

	public static function echo_top($active_tab)
	{
		//here there will be contextualized tabs based on the user auth level
		echo('
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		  <head>
			<title>Flowers Express</title>
			<script type="text/javascript" src="style.js"></script>
			<link href="style.css" rel="stylesheet">
			<link href="favicon.ico" rel="shortcut icon">
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		  </head>

		<body>
		<div align="center">&nbsp;
		<a name="top"></a>

		  <table class="content_area" cellspacing="0" cellpadding="0">
		  <tbody>

		<!-- top row of page: header -->
			<tr>
			  <td height="120" valign="top" align="right" colspan="6" style="background-image: url(\'../img/top_logo.png\'); background-position: 50% 50%; background-repeat: no-repeat;">
		');
		
		//if logged in, echo a logout button
		if(LoginManager::meets_auth_level(LoginManager::$AUTH_LOGIN))
		{
		//get login name:
			$login_info = DB::get_single_row_fq
			('
				SELECT  employees.first_name, 
						employees.last_name
				FROM employees
				WHERE employees.id=\'' . LoginManager::get_id() . '\''
			);
			echo('Welcome,&nbsp;<strong>' . IO::prepout_sl($login_info['first_name'] , false) . '&nbsp;' .   IO::prepout_sl($login_info['last_name'] , false) . '&nbsp;&nbsp;</strong>');
			echo('<a href="op_logout.php" style="padding-right:10px">Logout</a>');
		}		
		
		echo('
			  </td>
			</tr>

		<!-- middle "row" of page: all the words -->
			<tr>

		<!-- left column -->
			  <td valign="top" class="left" rowspan="2" height="600">

		<!-- left menu links -->
				<table width="120" cellspacing="0" cellpadding="0" id="navigation">
				  <tbody>
					<tr>

		<!-- leading space between banner and menu -->
					  <td bgcolor="#FFFFFF">&nbsp;</td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_HOME) ? 'active_menu' : 'menu') . '" href="page_home.php">Home</a></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_SALES_ORDERS) ? 'active_menu' : 'menu') . '" href="page_sales_order_menu.php">Sales Order</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_PURCHASES) ? 'active_menu' : 'menu') . '" href="page_purchase_menu.php">Purchase</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_CUSTOMERS) ? 'active_menu' : 'menu') . '" href="page_customer_menu.php">Customer</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_PRODUCTS) ? 'active_menu' : 'menu') . '" href="page_product_menu.php">Product</a></td></tr><tr>					  
					  <td><a class="' . (($active_tab == self::$TAB_SUPPLIERS) ? 'active_menu' : 'menu') . '" href="page_supplier_menu.php">Supplier</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_SHIPPERS) ? 'active_menu' : 'menu') . '" href="page_shipper_menu.php">Shipper</a></td></tr><tr>					  
					  <td><a class="' . (($active_tab == self::$TAB_REPORTS) ? 'active_menu' : 'menu') . '" href="page_report_menu.php">Report</a></td></tr><tr>			  
					  <td><a class="' . (($active_tab == self::$TAB_EMPLOYEES) ? 'active_menu' : 'menu') . '" href="page_employee_menu.php">Employee</a></td>
		');
		
		//** ADMIN ONLY - view admin tab
		if(LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN))
		{
			echo('
					  </tr><tr><td><a class="' . (($active_tab == self::$TAB_ADMIN) ? 'active_menu' : 'menu') . '" href="page_admin_menu.php">Admin</a></td>
			');
		}		
		
		echo('
		
					</tr>

				  </tbody>
				</table>

		<!-- end left menu links -->
			  </td>

		<!-- 4 spaces separating left menu and middle -->

			  <td valign="top" >&nbsp;&nbsp;&nbsp;&nbsp; </td>

		<!-- inner area -->

			 <td valign="top">

		<!-- date today -->
				<p style="text-align: right;padding-right:10px">&nbsp;

				  <b> ' . date("l, j. F Y") . '</b>

				</p>

		<!-- keep middle and top spaced correctly, insert empty very-wide table -->
				<table cellpadding="0" cellspacing="0"><tr><td width="1000"></td></tr></table>	
		');

		//inner area comes next
	}
	
	public static function echo_bottom($has_back_btn = true)
	{
		//NOTE: back button turned off in all cases for now
		$has_back_btn = false;

		if($has_back_btn == true)
		{
			//print the back button
			echo(' 
			<br><br><input class="button" type="button" value="Back" onClick="history.go(-1)">
					   ');
		}
		echo('
						
		<!-- end of all Announcements

				  </tbody>
				</table> -->

		<!-- end of Announcements table -->

			  </td>

		<!-- end of middle column -->

			</tr>
		<!-- end of right"row": text -->

	

		<!-- "3rd" row of page: link to top -->

			  <td colspan="3" valign="bottom">
				<p style="text-align: center">

				<a href="#top">top</a>

				</p><br>
			  </td>

		<!-- end of 3rd row of page: link -->

		<!-- bottom row: footer -->

			<tr>
			  <td valign="top" class="footer" colspan="6">
				<p style="text-align: center">

		Copyright &#169; 2009 Someone

				</p>
			  </td>
			</tr>
		<!-- end of bottom row, footer -->

		  </tbody>
		  </table>

		<!-- end page disclaimer -->
				<p style="text-align: center">

				  <font size="0">
		<br><i>Submitted for CS 130 Software Engineering, Spring \'09, UCLA</i>
				  </font>

				</p>

		<!-- end page -->
		</div>
		</body>
		</html>
		');	
	}
}

?>