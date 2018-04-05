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
				<h5 style="margin-left: 20px;margin-bottom: 25px;">Unanswered Tickets</h5>
					<ul class="collapsible popout" data-collapsible="accordion">
						<?php 
							$sql = "SELECT * FROM `support` WHERE `resolved` = 0 ORDER BY id DESC";
							$result = $conn->query($sql);
							while($row = mysqli_fetch_assoc($result)) {
								echo '<li>
										<div class="collapsible-header"><i class="material-icons">place</i>Help</div>
										<div class="collapsible-body" style="display: none;" id="'.$row['id'].'">
											<h5 style="margin-top: -10px;">'.$row['name'].' ('.$row['steamid'].')</h5>
											<span>'.$row['description'].'</span>
											<div class="row">
												<div class="input-field col s12">
												<textarea class="admin-ticket-response materialize-textarea" id="'.$row['id'].'"></textarea>
												<label for="admin-ticket-response">Response</label>
												</div>
											</div>
											<a class="waves-effect btn secondary admin-respond-ticket">Respond</a></div>
									</li>';
							}
						?>
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
		<!-- Modal Account -->
		<div id="modal-account" class="modal modal-fixed-footer" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 0px;height:480px;">
			<div class="modal-content">
				<h4>Account</h4>
				<div class="card-panel materialize-red error" style="display: none;">
					<span class="white-text"></span>
				</div>
				<div class="row">
					<div class="row">
						<div class="input-field col s12">
							<input id="account-tradeofferurl" name="tradeOfferUrl" type="url" class="validate" placeholder="https://steamcommunity.com/tradeoffer/new/?partner=XXXXXXXX&amp;token=XXXXXXXX" value="<?php if(isset($tradeLink)) { echo $tradeLink; }?>">
							<label for="account-tradeofferurl" class="active"><a href="http://steamcommunity.com/my/tradeoffers/privacy#create_new_url_btn" target="_blank" class="secondary-text lighten">Trade Offer URL (click here for yours)</a></label>
						</div>
						<div class="input-field col s2">
							<button class="btn secondary waves-effect" id="saveSettings">Save</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col s12 m6">
						<div class="card secondary center-align">
							<div class="card-content white-text">
								<span class="card-title">$0.00</span>
								<p>Total Deposited</p>
							</div>
						</div>
					</div>
					<div class="col s12 m6">
						<div class="card green center-align">
							<div class="card-content white-text">
								<span class="card-title">$0.00</span>
								<p>Total Won</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Close</a>
			</div>
		</div>
		<!-- Modal Inventory -->
		<?php require ('template_modals.php'); ?>
		
		<script type="text/javascript" src="js/mod.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>