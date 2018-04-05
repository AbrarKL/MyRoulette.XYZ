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
				<div class="rankContainer">
				<h5 style="margin-left: 20px;margin-bottom: 25px;">Rank Editor</h5>
					<ul class="collapsible popout" data-collapsible="accordion">
						<li>
							<div class="collapsible-header"><i class="material-icons">place</i>Create Rank</div>
							<div class="collapsible-body" style="display: none;" id="">
								<div class="row" style="margin-top: -15px;">
									<div class="input-field col s5 admin-setting">
										<input type="text" id="admin-new-rank" class="validate" placeholder="Rank Name">
										<label for="admin-new-rank" class="active">Rank Name</label>
									</div>
									<div class="input-field col s3" style="clear: left;">
										<button class="btn secondary waves-effect" style="margin-top: -10px;" id="admin-create-rank">Create</button>
									</div>
								</div>
							</div>
						</li>
						<?php 
							$permissionsDesc = array(
							"configuration" => "Configuration Page",
							"users" => "Users Page",
							"tickets" => "Tickets Page",
							"rankeditor" => "Rank Editor Page",
							"viewSiteItems" => "Items Page",
							"status" => "Status Page",
							"staff" => "Staff",
							"rouletteTimer" => "Edit Roulette Timer",
							"minimumBet" => "Edit Minimum Bet",
							"maximumBet" => "Edit Maximum Bet",
							"maxDepositItems" => "Edit Maxium Deposit Items",
							"maxBets" => "Edit Maximum Bets",
							"itemMarkup" => "Edit Item Markup",
							"chatDelay" => "Edit Chat Delay",
							"siteName" => "Edit Site Name",
							"giveawayText" => "Edit Giveaway Text",
							"giveawayLink" => "Edit Giveaway Link",
							"publicSiteKey" => "Edit Public site Key (Google ReCaptcha)",
							"privateSecretKey" => "Edit Private Secret Key (Google ReCaptcha)",
							"mute" => "Mute",
							"selectRank" => "Select Rank",
							"updateBalance" => "Update Balance",
							"answerTickets" => "Answer Tickets",
							"modifyRank" => "Modify Rank",
							"refferalBonus" => "Modify Refferal Bonus",
							"refferalReward" => "Modify Refferal Reward"
							);
							for ($i = 0; $i < count($permissions); $i++) {
								$rankName = array_keys($permissions)[$i];
								echo '<li>
										<div class="collapsible-header"><i class="material-icons">place</i>'.ucfirst($rankName).'</div>
										<div class="collapsible-body" style="display: none;" id="'.$row['id'].'">
											';
												for ($x = 0; $x < count(array_keys($permissions["owner"])); $x++) {
													if($x == 1)
													{
														echo '<h5>Permissions</h5>';
													}
													$permissionName = array_keys($permissions[$rankName])[$x];
													$permissionName2 = array_keys($permissions["owner"])[$x];
													if($permissionName2 == "access")
													{
														echo '<h5 style="margin-top: -15px;">Access</h5>';
														for ($z = 0; $z < count(array_keys($permissions["owner"]["access"])); $z++) {
															$permissionName = array_keys($permissions[$rankName]["access"])[$z];
															$permissionName2 = array_keys($permissions["owner"]["access"])[$z];
															if($permissions[$rankName]["access"][$permissionName] == 'true')
															{
																$hasPermission = true;
															}
															else
															{
																$hasPermission = false;
															}
															echo '
															<div class="row">
																<div class="input-field col s12 admin-access-page" id="'.$rankName."-".$permissionName2.'" style="margin-top:-10px">
																	<input type="checkbox" class="filled-in" id="'.$rankName."_".$permissionName2.'" '.($hasPermission ? 'checked' : '').' />
																	<label for="'.$rankName."_".$permissionName2.'">'.$permissionsDesc[$permissionName2].'</label>
																</div>
															</div>';
														}
													}
													else
													{
														if($permissions[$rankName][$permissionName] == 'true')
														{
															$hasPermission = true;
														}
														else
														{
															$hasPermission = false;
														}
														echo '
															<div class="row">
																<div class="input-field col s12" style="margin-top:-10px">
																	<input type="checkbox" class="filled-in" data-id="'.$rankName.'" id="'.$rankName."_".$permissionName2.'" '.($hasPermission ? 'checked' : '').' />
																	<label for="'.$rankName."_".$permissionName2.'">'.$permissionsDesc[$permissionName2].'</label>
																</div>
															</div>';
													}
												}
												  echo '
											<a class="waves-effect btn secondary admin-update-permission" id="'.$rankName.'">Update Permissions</a></div>
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
		<?php require ('template_modals.php'); ?>
		
		<script type="text/javascript" src="js/admin.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>