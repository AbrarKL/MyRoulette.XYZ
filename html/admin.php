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
			<div class="container" style="margin-left: 10%;margin-top: 10px;">
				<div class="row">
					<div class="col s4">
						<div class="card green center-align">
							<div class="card-content white-text">
								<span class="card-title">$<?php $sql = "SELECT * FROM `site` WHERE `id` = 1";
																					$result = $conn->query($sql);
																					$row = mysqli_fetch_assoc($result);
																					echo number_format($row['totalIn']-$row['totalOut'], 2);
																			?>
													</span>
								<p>Total Profit</p>
							</div>
						</div>
					</div>
					<div class="col s12">
						<p><strong>Site Settings</strong></p>
						<div class="row">
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['siteName']; ?>" id="siteName">
								<input type="text" id="admin-site-name" class="validate admin-setting-text" value="<?php echo $settings['siteName']; ?>">
								<label for="admin-site-name" class="active">Site Name</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['minimumBet']; ?>" id="minimumBet">
								<input type="number" id="admin-minimum-bet-amount" min="0" step="0.01" class="validate admin-setting-text" value="<?php echo $settings['minimumBet']; ?>">
								<label for="admin-minimum-bet-amount" class="active">Minimum Bet ($)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['maximumBet']; ?>" id="maximumBet">
								<input type="number" id="admin-maximum-bet-amount" min="1" step="0.01" class="validate admin-setting-text" value="<?php echo $settings['maximumBet']; ?>">
								<label for="admin-maximum-bet-amount" class="active">Maximum Bet ($)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['maxDepositItems']; ?>" id="maxDepositItems">
								<input type="number" id="admin-maximum-items-per-deposit" min="1" class="validate admin-setting-text" value="<?php echo $settings['maxDepositItems']; ?>">
								<label for="admin-maximum-items-per-deposit" class="active">Maximum Items Per Deposit</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['maxBets']; ?>" id="maxBets">
								<input type="number" id="admin-maximum-user-bets" min="1" class="validate admin-setting-text" value="<?php echo $settings['maxBets']; ?>">
								<label for="admin-maximum-user-bets" class="active">Maximum Bets (Amount)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['itemMarkup']; ?>" id="itemMarkup">
								<input type="number" id="admin-deposit-markup" min="0" step="0.10" class="validate admin-setting-text" value="<?php echo $settings['itemMarkup']; ?>">
								<label for="admin-deposit-markup" class="active">Deposit Item Markup (%) (to not allow traders)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['chatDelay']; ?>" id="chatDelay">
								<input type="number" id="admin-chat-delay" min="0" class="validate admin-setting-text" value="<?php echo $settings['chatDelay']; ?>"
									step="0.1">
								<label for="admin-chat-delay" class="active">Chat Delay (Seconds)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['rouletteTimer']; ?>" id="rouletteTimer">
								<input type="number" id="admin-roulette-timer" min="5" max="60" class="validate admin-setting-text" value="<?php echo $settings['rouletteTimer']; ?>"
									step="1">
								<label for="admin-roulette-timer" class="active">Roulette Timer (Less than 60 seconds)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['refferalBonus']; ?>" id="refferalBonus">
								<input type="number" id="admin-refferal-bonus" step="0.01" class="validate admin-setting-text" value="<?php echo $settings['refferalBonus']; ?>"
									step="1">
								<label for="admin-refferal-bonus" class="active">Refferal Bonus ($) (User gains for redeeming)</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['refferalReward']; ?>" id="refferalReward">
								<input type="number" id="admin-refferal-reward" class="validate admin-setting-text" value="<?php echo $settings['refferalReward']; ?>"
									step="0.5">
								<label for="admin-refferal-reward" class="active">Referer Reward (%) (Referer earns)</label>
							</div>
							<div class="input-field col s6 admin-setting" data-value="<?php echo $settings['giveawayText']; ?>" id="giveawayText" style="clear:left">
								<input type="text" id="admin-giveaway-text" class="validate admin-setting-text" value="<?php echo $settings['giveawayText']; ?>">
								<label for="admin-giveaway-text" class="active">Giveaway Text. Leave blank if no giveaway. Input '$link' where you want the giveaway link.</label>
							</div>
							<div class="input-field col s3 admin-setting" data-value="<?php echo $settings['giveawayLink']; ?>" id="giveawayLink">
								<input type="text" id="admin-giveaway-link" class="validate admin-setting-text" value="<?php echo $settings['giveawayLink']; ?>">
								<label for="admin-giveaway-link" class="active">Giveaway Link</label>
							</div>
							<p style="clear: left;"><strong>Giveaway Example</strong></p>
							<div id="nav-chat" style="display: block;margin-left: 12px;">
								<div class="discussion" style="clear: left;width: 325px;border: 3px solid #2390ee;">
									<li class="other">
										<div class="messages" style="">
											<h1 style="color:#2490ee">System</h1>
											<p id="example-giveaway-text">
												<?php echo str_replace('$link','<a class="secondary-text" href="'.$settings['giveawayLink'].'" style="padding: 0;line-height: 0;height: 0;display: inline;color: #fff;">'.$settings['giveawayLink'].'</a>',$settings['giveawayText']);?>
											</p>
											<time class="grey-text"><time class="grey-text"><?php echo date('h:i A'); ?></time></time>
										</div>
									</li>
								</div>
							</div>
							<div class="input-field col s3" style="clear: left;" id="admin-save-setting">
								<button class="btn secondary waves-effect">Save</button>
							</div>
						</div>
						<div class="row">
							<p><strong>ReCaptcha Settings</strong></p>
							<div class="col s12" style="margin-top: -15px;">
								<p>	Captcha information for submitting support tickets.
									<br>Generate here <a href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a>.
									<br>Choose ReCaptcha V2, and add the domain you're using for
									<br>the website. If you have previously set a Site Key (Public) on
									<br>site, it will show in the text box below, HOWEVER if you have
									<br>previously set a Secret Key (Private) on site before, it will
									<br>not show for security reasons.
								</p>
								<div class="row">
									<div class="input-field col s4 admin-setting2" id="publicSiteKey" data-value="<?php echo $settings['publicSiteKey']; ?>">
										<input type="text" id="admin-public-site-key" class="validate admin-setting2-text valid" value="<?php echo $settings['publicSiteKey']; ?>">
										<label for="admin-public-site-key" class="active">Site Key (Public)</label>
									</div>
									<div class="input-field col s4 admin-setting2" id="privateSecretKey" data-value="">
										<input type="text" id="admin-secret-site-key" class="validate admin-setting2-text">
										<label for="admin-secret-site-key" class="">Secret Key (Private)</label>
									</div>

									<div class="input-field col s3" style="clear: left;" id="admin-save-setting2">
										<button class="btn secondary waves-effect">Save</button>
									</div>
									<div class="col s12">
										<p><strong>Captcha Example/Test (to make sure everything is working fine)</strong></p>
										<div class="g-recaptcha" data-sitekey="<?php echo $settings['publicSiteKey']; ?>"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
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
		
		<script type="text/javascript" src="js/admin.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>