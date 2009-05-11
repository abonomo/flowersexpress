//javascript scripts for every page go here

//Generic Element Highlighting (Page Num Nav)
var gen_elm_orig_color = "#FFFFFF";
var gen_elm_hl_color = "#A5C786";

function gen_elm_highlight(the_elm)
{
	the_elm.style.backgroundColor = gen_elm_hl_color;
	document.body.style.cursor = "pointer";
}

function gen_elm_dull(the_elm)
{
	the_elm.style.backgroundColor = gen_elm_orig_color;
	document.body.style.cursor = "default";	
}

function gen_elm_click(the_elm, the_url)
{
	gen_elm_dull(the_elm); 
	document.location = the_url;	
}