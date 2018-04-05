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
			<div class="container" style="margin-left: 10%;">
				<div class="row">
					<div class="col s12">
						<form class="row">
							<h4>User</h4>
							<div class="input-field col s3">
								<?php
								if(isset($_GET['steamid'])) {
									echo '<input type="text" id="steamid" name="steamid" value="'.$_GET['steamid'].'">
									 	  <label for="steamid" class="active">SteamID</label>';
								}
								else {
									echo '<input type="text" id="steamid" name="steamid">
									 	  <label for="steamid">SteamID</label>';
								}
								?>
							</div>
							<div class="input-field col s4 l1">
								<input type="submit" class="btn secondary" value="Retrieve">
							</div>
						</form>
						<?php
						if(isset($_GET['steamid'])) {
							//Obtain steam user details from database
							$sql = "SELECT * FROM `users` WHERE `steamid` = ".mysqli_real_escape_string($conn, $_GET['steamid']);
							$result = $conn->query($sql);
							if($result->num_rows == 1)
							{
								while($row = mysqli_fetch_assoc($result)) {
									// Create Vars from database for steamids
									$nickName = $row['nickname'];
									$rank = $row['rank'];
									$userBalance = str_replace(",","",number_format($row['balance'], 2));;
									if($row['muted'] == 1)
									{
										$muted = 'checked';
									}
									else
									{
										$muted = '';
									}
								}
								if ($permissions[strtolower($userRank)]["updateBalance"] == 'true')
								{
									$adminAbove = '
									<h5>Balance</h5>
									<div class="row">
										<div class="input-field col s12 l2">
											<input type="number" id="admin-update-balance" step="0.01" class="validate" value="'.$userBalance.'">
											<label for="admin-update-balance" class="active">Balance</label>
										</div>
										<div class="input-field col s3">
											<button class="btn secondary waves-effect">Update</button>
										</div>
									</div>';
								}
								echo '<meta name="targetSteamID" content="'.$_GET['steamid'].'"/>';
								echo '<div class="row">
										<h4>Actions for '.$nickName.'</h4>
											<div class="col s12">
												<h5>Rank</h5>
												<div class="row">
													<div class="col s12">
														User\'s rank: '.$rank.'
													</div>
												</div>
												<div class="row">
													<div class="input-field col s9 l3">
														<div class="select-wrapper">
															<select id="mod-rank-select">
																<option value="7">User</option>
																<option value="6">Moderator</option>
																<option value="3">Admin</option>
															</select>
														</div>
														<label>Select Rank</label>
													</div>
													<div class="input-field col s3 l2" style="margin-top: 30px;">
														<button class="btn secondary waves-effect" id="saveSelRank">Save</button>
													</div>	
												</div>
												<h5>Permissions</h5>
												<div class="row">
													<p class="input-field col s12" style="padding-bottom: 20px;">
														<input type="checkbox" class="filled-in" id="mute" '.$muted.'>
														<label for="mute">Muted (cannot use chat)</label>
													</p>
													<div class="input-field col s3" style="margin-top: -5px;">
														<button class="btn secondary waves-effect" id="permissionApply">Apply</button>
													</div>
												</div>
												'.$adminAbove.'
											</div>
										</div>';
							}
						}
						?>
					</div>
				</div>
				<?php 
				if(!isset($_GET['steamid'])) 
				{
				?>
					<input id="searchUserMod" type="text" placeholder="Search for User" style="width: 22%;margin-left: 35%;margin-top: 5px;">
					<div class="row" style="margin-top: 10px;width: 100%;">
					<?php 
					$sql = "SELECT * FROM `users`"; 
					$result = $conn->query($sql);
					while($row = mysqli_fetch_assoc($result)) {
						echo '
						<a href="mod.php?steamid='.$row['steamid'].'">
							<div class="person" style="width: 100px;display: inline-block;margin-left:13px" data-title="'.$row['nickname'].'">
								<img src="'.$row['avatar'].'"
									style="width: 100px;">
								<center>
									<h6 class="secondary-text">'.substr($row['nickname'], 0, 10).'</h6>
								</center>
							</div>
						</a>
						';
					}
				}
				?>
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
		
		<script type="text/javascript" src="js/mod.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>