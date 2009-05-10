<?php

require_once('framework.php');

class PageProductView
{
	//*** CONSTANTS ***
	private static $THIS_PAGE = 'page_product_view.php';
	
	//*** MEMBERS ***
	private $f_mode;	
	private $f_action;
	
	private $f_id;
	
	private $f_icode;
	private $f_name;
	private $f_type;
	private $f_subtype_1;
	private $f_subtype_2;
	private $f_description;
	private $f_lifespan;
	private $f_units;
	private $f_min_price;
	private $f_notes;
	
			//ADD THIS FOR UPDATED INFO FUNCTIONALITY
	private $f_created_first;
	private $f_created_last;
	private $f_updated_first;
	private $f_updated_last;
	
	private $f_created_date;
	private $f_updated_date;
			//to this
	
	//*** FUNCTIONS ***
	//execution entry point
	public function run()
	{
		session_start();
		DB::connect();
	
		LoginManager::assert_auth_level(LoginManager::$AUTH_READ_WRITE);
	
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
		
		//queries changed with updated info
			$prod_info = DB::get_single_row_fq('
				SELECT  products.icode, 
						products.name, 
						products.type, 
						products.notes, 
						products.subtype1, 
						products.subtype2, 
						products.description, 
						products.typical_lifespan_days, 
						products.typical_units, 
						products.typical_min_price_per_unit, 
						products.created_employee_id, 
						products.updated_employee_id,
						products.created_date,
						products.updated_date,
						employees.first_name, 
						employees.last_name
				FROM products 
				LEFT OUTER JOIN employees ON products.created_employee_id = employees.id
				WHERE products.id=\'' . $this->f_id . '\''
			);
			$prod_info_up = DB::get_single_row_fq('
				SELECT  employees.first_name, 
						employees.last_name
				FROM products 
				LEFT OUTER JOIN employees ON products.updated_employee_id = employees.id
				WHERE products.id=\'' . $this->f_id . '\''
			);

			
			$this->f_icode = $prod_info['icode'];
			$this->f_name = $prod_info['name'];
			$this->f_type = $prod_info['type'];
			$this->f_subtype_1 = $prod_info['subtype1'];
			$this->f_subtype_2 = $prod_info['subtype2'];
			$this->f_description = $prod_info['description'];
			$this->f_lifespan = $prod_info['typical_lifespan_days'];
			$this->f_units = $prod_info['typical_units'];
			$this->f_min_price = $prod_info['typical_min_price_per_unit'];
			$this->f_notes = $prod_info['notes'];
			
			//add these for UPDATED INFO
			$this->f_created_first = $prod_info['first_name'];
			$this->f_created_last = $prod_info['last_name'];
			
			$this->f_updated_first = $prod_info_up['first_name'];
			$this->f_updated_last = $prod_info_up['last_name'];
			
			$this->f_created_date = $prod_info['created_date'];
			$this->f_updated_date = $prod_info['updated_date'];
	}
	
	private function show_output($err_msg = '')
	{
		//echo the outer area with the correct tab highlighted for this page
		ObjOuterArea::echo_top(ObjOuterArea::$TAB_PRODUCTS);
		
		//echo inner area html
		Echo ('	
				   <div align="center">
			  <form name="form1" method="post" action="page_product_view.php">
				<table width="600" border="0" cellpadding="0" cellspacing="0">

				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle">&nbsp;</td>
						  <td width="75%" align="left" valign="middle" class="text_title">View Product</td>
						</tr>
					</table></td>
				  </tr>	
				  
				  <tr>

					<td>&nbsp;</td>
				  </tr>						
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">ID Code:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_icode) . '</td>
						</tr>
						<tr>

						  <td width="25%" align="right" valign="middle" class="text_label">Product Name:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_name) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Type:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_type) . '</td>
						</tr>
						<tr>

						  <td width="25%" align="right" valign="middle" class="text_label">Subtype 1:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_subtype_1) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Subtype 2:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_subtype_2) . '</td>
						</tr>
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Description:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_desciption) . '</td>
						</tr>
					</table></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
				  </tr>	
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Units:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_units) . '</td>

					  </tr>
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Typical Lifespan(Days):&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_lifespan) . '</td>
					  </tr>
					  <tr>
				  <tr>
					<td>&nbsp;</td>
				  </tr>	
					  </tr>
					  <tr>
						<td width="25%" align="right" valign="middle" class="text_label">Minimum Sell Price:&nbsp;</td>
						<td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_min_price) . '</td>
					  </tr>
					</table></td>
				  </tr>
				  	<tr>
						<td>&nbsp;</td>
					</tr>
				  <!--BEGINNNN updated info area-->
				  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Created:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_created_first) . '&nbsp;' . IO::prepout_ml_html($this->f_created_last) . '&nbsp;on&nbsp;' . IO::prepout_ml_html($this->f_created_date) . '</td>
						 </tr>
				  		<tr>
						  <td width="25%" align="right" valign="middle" class="text_label">Updated:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_updated_first) . '&nbsp;' . IO::prepout_ml_html($this->f_updated_last) . '&nbsp;on&nbsp;' . IO::prepout_ml_html($this->f_updated_date) . '</td>
						 </tr>
					</table></td>
					<!--ENDDDDDDD updated/created info area-->	
				  <tr>
					<td>&nbsp;</td>
				  </tr><td>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="25%" align="right" valign="" class="text_label">Notes:&nbsp;</td>
						  <td width="75%" align="left" valign="middle">' . IO::prepout_ml_html($this->f_notes) . '</td>
						</tr>
						</table>
						</td>
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
$page_product_view = new PageProductView();
$page_product_view->run();

?>