<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('tree_view')) {
	function tree_view($data)
	{
		style();
		print('<div id="tree">');
		html($data);
		print('</div>');
		script();
	}

	function html($data)
	{
		do {
			print('<ul>');
			foreach($data as $key => $val) {
				if(is_array($val)) {
					print('<li class="link">
						<a class="caret">
						'.$key.' ('.count($val).')</a>');
					html($val);
					print('</li>');
				} else {
					print('<li>'.$key.' : "'.$val.'"</li>');
				}
			}
			print('</ul>');
		} while (!is_array($data));
	}

	function style()
	{
		?>
    <style>
		#tree * {
			font-size: unset !important;
			color: unset !important;
			padding: unset !important;
			margin: unset !important;
		}
		#tree ul { 
			line-height: 1.7em !important; 
			list-style-type: none !important; 
			padding-inline-start: 1.5em !important; 
		}
		#tree ul li:before {
			content: "·" !important; 
			font-size: 3em !important; 
			vertical-align: middle !important; 
			margin-right: 10px !important; 
		}
		#tree ul li.link a {
			font-weight: 600 !important; 
			line-height: 2em !important; 
			letter-spacing: 0.1em !important; 
		}
		#tree ul li.link a { text-decoration: none !important; }
		#tree ul li.link a:hover { 
			text-decoration: underline !important; 
			cursor: pointer !important; 
			color: unset !important;
		}
		/* IF READY SHOW REMOVE THIS */
		/* #tree .link ul { display: none } */
		.hide { display: none !important; }
		.show { display: block !important; }
    </style>
    <?php
	}

	function script()
	{
		?> <script> 
		var toggler = document.getElementsByClassName("caret");
		var i;
		for (i = 0; i < toggler.length; i++) {
			toggler[i].addEventListener("click", function() {
				// this.parentElement.querySelector("ul").classList.toggle("show");
				this.parentElement.querySelector("ul").classList.toggle("hide");
			});
		}
		</script> <?php
	}
}
?>
