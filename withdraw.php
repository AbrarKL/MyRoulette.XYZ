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
				<div class="row">
				<?php
					//https://github.com/DoctorMcKay/node-steam-tradeoffer-manager/blob/master/resources/ETradeOfferState.js
					$offerStates = array(
						2 => "active",
						4 => "pending",
						9 => "active",
						11 => "pending",
						0 => "pending"
					);
					$pricedata = file_get_contents('https://api.csgofast.com/price/all');
					$pricing   = json_decode($pricedata);
					$sql = "SELECT * FROM market";
					$result = $conn->query($sql);
					while($row = $result->fetch_assoc()) {
								$sql2 = "SELECT * FROM offers WHERE item = '".$row['itemid']."' ORDER BY ID DESC";
								$result2 = $conn->query($sql2);
								$row2 = $result2->fetch_assoc();
								if($offerStates[$row2['state']] == "pending")
								{
									//Show Progressbar
									$buttonText = '<a class="waves-effect btn withdraw-item-button" style="width: 100%;background-color: #ba554a !important;margin-top: 15px;"><div class="progress" style="background-color: #303030;margin-top: 15px;"><div class="indeterminate secondary" style="background-color: #FFF;"></div></div></a>';
								}
								elseif($offerStates[$row2['state']] == "active")
								{
									//Say Acceppt Here
									$buttonText = '<a class="waves-effect btn withdraw-item-button" style="width: 100%;background-color: #ba554a !important;margin-top: 15px;" href="http://steamcommunity.com/tradeoffer/'.$row2['tradeofferid'].'">Acceppt Here</a>';
								}
								else
								{
									//Say Withdraw
									$buttonText = '<a class="waves-effect btn withdraw-item-button" style="width: 100%;background-color: #ba554a !important;margin-top: 15px;">Withdraw</a>';
								}
								$itemname = rawurldecode($row["name"]);
								$condition = str_replace(')', '', explode('(', $itemname)[1]);
								$condition = $condition != '' ? $condition : 'None';
								echo '<div class="col s2" style="width:20%">
								<div class="card-panel" id="'.$row['itemid'].'"> 
									<span class="white-text">'.explode('(', $itemname)[0].'</span>
									<p class="white-text" style="margin-top:-1px;">'.$condition.'</p>
									<img src="https://steamcommunity-a.akamaihd.net/economy/image/'.$row['img'].'" style="width: 200px;height: 150px;">
									'.$buttonText.'
								</div>
							</div>';
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
		
		<script type="text/javascript" src="js/main.js"></script>
		<script type="text/javascript" src="js/withdraw.js"></script>
	</body>
</html>