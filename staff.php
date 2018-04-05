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
					<?php
					$sqlt = "SELECT * FROM `support` WHERE `resolved` = 0";
					$sqlu = "SELECT * FROM `users`";
					$tickets = $conn->query($sqlt);
					$users = $conn->query($sqlu);
					if($permissions[strtolower($userRank)]["access"]["configuration"] == 'true')
					{
						echo '
						   <div class="col s4">
							  <div class="card blue accent-3">
								 <div class="card-content" style="color: #FFF !important;">
									<h5>Configuration</h5>
									<p style="font-size: 17px;">Edit/View Website Configuration</p>
								 </div>
								 <div class="card-action">
									<a href="admin.php" class="white-text">Edit</a>
								 </div>
							  </div>
						   </div>';
					}
					if($permissions[strtolower($userRank)]["access"]["status"] == 'true')
					{
						echo '   
						   <div class="col s4">
							  <div class="card pink ">
								 <div class="card-content" style="color: #FFF !important;">
									<h5>Status</h5>
									<p style="font-size: 17px;">View Website Status</p>
								 </div>
								 <div class="card-action">
									<a href="status.php" class="white-text">View</a>
								 </div>
							  </div>
						   </div>';
					}
					if($permissions[strtolower($userRank)]["access"]["tickets"] == 'true')
					{
						echo '   
						   <div class="col s4">
							  <div class="card red darken-1">
								 <div class="card-content" style="color: #FFF !important;">
									<h5>Support</h5>
									<p style="font-size: 17px;">'.$tickets->num_rows.' Tickets</p>
								 </div>
								 <div class="card-action">
									<a href="support-staff.php" class="white-text">Respond</a>
								 </div>
							  </div>
						   </div>';
					}
					if($permissions[strtolower($userRank)]["access"]["viewSiteItems"] == 'true')
					{
						echo ' 
						   <div class="col s4">
							  <div class="card orange">
								 <div class="card-content" style="color: #FFF !important;">
									<h5>Items</h5>
									<p style="font-size: 17px;">View/Remove Items</p>
								 </div>
								 <div class="card-action">
									<a href="admin-view-items.php" class="white-text">View</a>
								 </div>
							  </div>
						   </div>';
					}
					if($permissions[strtolower($userRank)]["access"]["users"] == 'true')
					{
						echo ' 
						   <div class="col s4">
							  <div class="card purple darken-1">
								 <div class="card-content" style="color: #FFF !important;">
									<h5>Users</h5>
									<p style="font-size: 17px;">'.$users->num_rows.' Users</p>
								 </div>
								 <div class="card-action">
									<a href="mod.php" class="white-text">View</a>
								 </div>
							  </div>
						   </div>';
					}
					if($permissions[strtolower($userRank)]["access"]["rankeditor"] == 'true')
					{
						echo ' 
						   <div class="col s4">
							  <div class="card green">
								 <div class="card-content" style="color: #FFF !important;">
									<h5>Permissions</h5>
									<p style="font-size: 17px;">Edit/Create Rank Permissions</p>
								 </div>
								 <div class="card-action">
									<a href="admin-rank-editor.php" class="white-text">Edit</a>
								 </div>
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
		
		<script type="text/javascript" src="js/admin.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>