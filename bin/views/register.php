<!DOCTYPE html>
<html>
	<head>
		<title>Xobs - Register an account</title>
		<link rel="stylesheet" type="text/css" href="/assets/css/kit.css" />
	</head>
	<body>
		<div class="outerWrapper" id="outerWrapper">
			<div class="innerWrapper">
				<div id="pageNavigation">
					<a href="#" id="logo">Xobs</a>
					
					<form id="search" action="#">
						<input type="text" name="search" placeholder="Search..." />
						<input type="submit" value="Search" />
					</form>
					
					<div id="userCP">
						<a id="loginButton" href="#login">Login
						</a>
					</div>
				</div>
				

				<div style="" id="sectionNavigation">
					<h1>Register <small>Create a new account</small></h1>
					<a class="tab active" href="#/profile">Register</a>
					<a class="tab" href="#/profile">Help</a>
				</div>
				<div class="fixedWrapper">
					<div class="fixedContent padded">
						<div class="sysmsg error">Please enter a valid password</div>
						<div style="float:right; width: 450px;">
							<h1>Xobs is the place to sell and buy arts</h1>
							<p>It'll help you displaying your art and allow people looking for a commissions actually find you.</p>
							<p>As a difference to other art communities Xobs is designed to make your commission information actually searchable.
								You'll only appear on search results if you're actually interested in selling commissions.</p>
							<p>To register an artist profile just fill in the fields on the left and you can start creating art commission offers.</p>
							<p>Easy as cake, and it's not a lie!</p>
							<a class="button withBG" href="#/btton">
								<img class="icon" src="http://www.gratis-webspace-provider.com/images/icon_need_help.jpg" />
								<span class="caption">Need Help?</span>
								<span class="description">Visit our FAQ!</span>
							</a>
							
							<a class="button" href="#/btton">
								<!--<img class="icon" src="http://www.kartosoft.de/uploads/pics/download3.png" />-->
								<span class="caption">Download</span>
								<span class="description">2000px x 1000px</span>
							</a>
							<a class="button" href="#/btton">
								<span class="caption">Commission</span>
								<span class="description">Get a piece like this!</span>
							</a>
							
							
							
						</div>
						<form class="data narrow" action="/register" method="post">
							<h1> Register an account</h1>
							<div style="padding:5px;">
								<?php echo html::input('email', 'email', 'Email', 'Your email address', Array('tabindex' => '"1"')) ?>
							</div>
							<div style="padding:5px;">
								<?php echo html::input('text', 'username', 'Username', 'Pick a username', Array('tabindex' => '"2"')) ?>
							</div>
							<div style="padding:5px;">
								<?php echo html::input('password', 'password[]', 'Password', 'Enter a password', Array('tabindex' => '"3"')) ?>
							</div>
							<div style="padding:5px;">
								<?php echo html::input('password', 'password[]', 'Repeat password', 'Reenter your password', Array('tabindex' => '"4"')) ?>
							</div>
							<div style="padding:5px;">
								<label for="bio">Your bio</label>
								<textarea id="bio" style="width: 204px; height: 138px;" name="bio" placeholder="A bit about you and your work" tabindex="5"><?php echo (isset($_POST['bio'])?$_POST['bio']:'')?></textarea>
							</div>
							<div style="padding:5px;">
								<?php echo html::input('checkbox', 'open', 'Open for commissions', '', Array('tabindex' => '"6"')) ?>
							</div>
							<div class="footer">
								<input class="formSecondary" type="reset" value="Clear fields">
								<input type="submit" value="Register">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="footer">2011 &copy; Magic3W - All Rights Reserved</div>
	</body>
</html>