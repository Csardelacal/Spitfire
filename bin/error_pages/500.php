<!DOCTYPE html>
<html>
	<head>
		<title>Spitfire - Server error</title>
		<style type="text/css">
			.wrapper {
				display: block;
				width:   800px;
				padding:  10px 20px;
				border:  solid 1px #999;
				border-radius: 5px;
				background: #FFF;
				box-shadow: 3px 3px 3px #777;
				color: #555;
				margin: 50px auto 0;
				font-size: 13px;
			}

			.wrapper h1 {
				color: #911;
				margin: 5px;
				font-size: 17px;
			}
			body{ 
				background: #CCC;
			}
		</style>
	</head>
	<body>
		<div class="wrapper">
			<h1><?=$message?></h1>
			<p>Sorry, but the page you requested is generating an error that prevents it from loading.	
			Please try visiting another site or try again in five minutes. </p>
			<?php if(environment::get('debugging_mode')): ?>
				<p>Following details were provided to solve the the error:</p> 
				<h2>Scope</h2>
				<pre><?=$moreInfo?></pre>
				<?php
					$messages = SpitFire::$debug->getMessages();
					if ($messages){
						echo '<h2>Debugging messages</h2><ul>';
						foreach ($messages as $msg) {
							echo "<li>$msg</li>";
						}
						echo '</ul>';
					}
				?>
			<?php endif; ?>
		</div>
	</body>
</html>