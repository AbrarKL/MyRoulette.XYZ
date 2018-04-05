<?php
    require ('steamauth/steamauth.php');
	require ('config.php');
	require ('functions.php');
	if(isset($_SESSION['steamid']))
	{
		require ('steamauth/userInfo.php');
	}
?>
<!DOCTYPE html>
<html>
	<head>
	
		<title>MyRoulette.XYZ</title>
		
		<meta name="identifier" content="<?php echo $identifier ?>"/>
		<meta name="steamid" content="<?php echo $_SESSION['steamid'] ?>"/>
		
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel='stylesheet' href='css/main.css'>
		<link rel='stylesheet' href='css/materialize-dark.css'>
		<link rel='stylesheet' href='css/materialize-theme.css'>
		<link rel='stylesheet' href='css/materialize.css'>
		<link rel='stylesheet' href='css/normalize.css'>
		<link rel='stylesheet' href='css/page-home.css'>
		<link rel='stylesheet' href='css/style.css'>
		<link rel='stylesheet' href='css/timer.css'>
		
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
		<script src="js/materialize.js"></script>
		<script type="text/javascript" src="js/roulette.js"></script>
		<script src="js/moment.min.js"></script>
	</head>
	<body class="<?php if(isset($_COOKIE['setting'])) { echo $_COOKIE['setting']; } else { echo 'dark'; } ?>">
	
		<?php require ('template_navbar.php'); ?>
		
		<main>
			<div class="container" style="margin-left: 8%;margin-top: 15px;">
				<div class="ticketContainer">
					<ul class="collapsible popout" data-collapsible="accordion">
						<li>
							<div class="collapsible-header"><i class="material-icons">create</i>Submit a Support Ticket</div>
							<div class="collapsible-body" style="padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px; display: none;">
								<div class="row" style="margin-top: 10px;">
									<div class="input-field col s12">
										<input id="ticketSubject" type="text" class="validate">
										<label for="ticketSubject">Subject</label>
									</div>
									<div class="input-field col s12">
										<textarea id="ticketDescription" class="materialize-textarea"></textarea>
										<label for="ticketDescription">Description</label>
									</div>
									<div class="input-field col s12"><div class="g-recaptcha" data-sitekey="<?php echo $settings['publicSiteKey']; ?>"></div></div>
									<div class="input-field col s12" id="user-submit-ticket"><a class="waves-effect btn secondary">Submit</a></div>
								</div>
							</div>
						</li>
						<li>
							<div class="collapsible-header"><i class="material-icons">web_asset</i>My Tickets</div>
							<div class="collapsible-body" style="padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px; display: none;">
								<div class="row" style="margin-top: 10px;">
									<ul class="collapsible popout" data-collapsible="accordion">
									<?php 
									$sql = "SELECT * FROM `support` WHERE `steamid` = ".$_SESSION['steamid']." ORDER BY id DESC";
									$result = $conn->query($sql);
									while($row = mysqli_fetch_assoc($result)) {
										echo '<li>
												<div class="collapsible-header"><i class="material-icons">'.($row['resolved'] ? 'done' : 'clear').'</i>'.$row['subject'].'</div>
												<div class="collapsible-body" style="display: none;">
													<h5 style="margin-top: -10px;">Subject: '.$row['subject'].'</h5>
													<span>'.$row['description'].'</span>
													<h5 style="">Our Reply:</h5>
													<span>'.$row['response'].'</span>
													<a class="waves-effect btn user-close-ticket" style="display: block;width: 150px;margin-top: 10px;margin-left: 0;background-color: #ef5350;" id="'.$row['id'].'">Close</a>
												</div>
											</li>';
									}
									?>
										<li>
											<div class="collapsible-body" style="padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px; display: none;">
												<div class="row" style="margin-top: 10px;">
												</div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
		</main>
		<div id="nav-chat" class="side-nav right right-aligned" style="width: 350px; right: 0px;">
			<nav class="primary">
				<div class="nav-wrapper white-text z-depth-1 center-align">
					<span>Users online: </span>
					<span class="users-online">-</span>
					<span class="close" style="position: absolute; right: 0;">
						<a href="#" class="material-icons white-text">close</a>
					</span>
				</div>
			</nav>
			<ol class="discussion wrapper">
			<?php if($settings['giveawayText'] != '')
			{ ?>
				<li class="other">
					<div class="avatar"><a href="http://steamcommunity.com/profiles/76561197965129042" target="_blank" style="background-color: #2490ee;height: 40px;border-radius: 50%;"></a></div>
					<div class="messages">
						<h1 style="color:#2490ee">System</h1>
						<p>
							<?php echo str_replace('$link', '<a class="secondary-text" href="'.$settings['giveawayLink'].'" style="padding: 0;line-height: 0;height: 0;display: inline;color: #fff;">'.$settings['giveawayLink'].'</a>', $settings['giveawayText']);?>
						</p>
						<time class="grey-text"><?php echo date('h:i A'); ?></time>
					</div>
				</li>
			<?php }?>
			</ol>
			<ol class="discussion input">
				<li class="self input">
					<div class="avatar">
						<img src="<?php if (isset($_SESSION['steamid'])) { echo $avatar; } else { echo 'https://steamuserimages-a.akamaihd.net/ugc/109607797346317755/F7B06C602744549F7C64A6AC4C90444C1A79C03A/'; }?>" class="circle" id="chat-user-avatar">
					</div>
					<div class="messages">
						<textarea id="user-message" class="materialize-textarea" rows="1" placeholder="Send a message" <?php if(!isset($_SESSION['steamid'])) { echo 'disabled';}?>></textarea>
					</div>
				</li>
			</ol>
		</div>
		<!-- Modal's -->
		<?php require ('template_modals.php'); ?>
		
		<script type="text/javascript" src="js/mod.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>