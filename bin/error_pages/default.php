<?php

use spitfire\core\Environment;

?><!DOCTYPE html>
<html>
	<head>
		<title>Spitfire - Server Error</title>
		<style>
			body, html {
				margin: 0;
				padding: 0;
				font-family: arial;
				color: #555;
			}
			.errormsg {
				background-color: #2478C6;
				background-image: -moz-linear-gradient(90deg, #1E63B4 0%, #2478C6 10%, #2478C6 90%, #1E63B4);
				background-image: -webkit-linear-gradient(90deg, #1E63B4 0%, #2478C6 10%, #2478C6 90%, #1E63B4);
				color: #FFF;
				padding: 20px;
			}
			
			.errormsg p {
				color: #FFF;
			}
			
			.wrapper {
				margin: 0 auto;
				width: 960px;
			}
			
			h1 {
				margin: 8px 0 4px;
				font-size: 20px;
			}
			h2 {
				margin: 8px 0 3px;
				font-size: 17px;
			}
			
			p {
				font-size: 13px;
				color: #555;
			}
			
			small {
				font-size: 70%;
				color: #777;
			}
			
			.sfheader {
				margin: 20px 0 10px 0;
			}
			
			.errordescription pre,
			.errordescription .debugmessages {
				border: dashed 1px #cccccc;
				background: #f2f2f2;
				border-radius: 5px;
				padding: 5px;
				max-height: 300px;
				overflow: auto;
			}
		</style>
	</head>
	<body>
		<div class="wrapper">
			<h1 class="sfheader">Spitfire <small>//Error page</small></h1>
		</div>
		<div class="errormsg">
			<div class="wrapper">
				<h1>500: Server error</h1>
			<p><?=$message?></p>
			</div>
		</div>

		<?php if(Environment::get('debugging_mode')): ?>
		<div class="errordescription wrapper">
			<h2>Further error information <small>To hide this set debug_mode to false.</small></h2>
			<p>The stacktrace displays the function calls made that led to the error. They are displayed in an inverted order to how they were called.</p>
			<pre><?=$moreInfo?></pre>
			
			<?php
				$messages = spitfire()->getMessages();
				if ($messages){
					echo '<h2>Debugging messages <small>To hide this set debug_mode to false.</small></h2>';
					echo '<p>List of messages the app generated during it\'s execution</p>';
					echo '<div class="debugmessages">';
					echo '<ul>';
					foreach ($messages as $msg) {
						echo "<li>$msg</li>";
					}
					echo '</ul>';
					echo '</div>';
				}
			?>
		</div>
		<?php endif; ?>
	</body>
</html>