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
					<!-- PROBABLY DELETE THIS PROGRESS BAR -->
					<div class="col s8" style="width: 87.49%;">
						<div class="card-panel">
							<div class="timer" style="background-color: #ba554a;width: 100%;height: 20px;">
							</div>
						</div>
					</div>
					<!-- PROBABLY DELETE THIS PROGRESS BAR -->
                    <div class="col s12">
                        <div class="card-panel" id="roulette">
                            <div class="wheel">
                                <div id="case" style="" class="0">
									<div id="pointer">
									</div>
								</div>
                            </div>
                        </div>
                    </div>
					<div class="col s12" style="width: 61.24%;">
						<div class="card-panel">
							<center>
								<div id="past">
									<?php
									$sql = "SELECT * FROM `rolls` ORDER BY id DESC LIMIT 1";
									$result = mysqli_query($conn, $sql);
									while($row = mysqli_fetch_assoc($result)) {
										$roundIDMinus10 = $row['id']-10;
									}
									$sql2 = "SELECT * FROM `rolls` WHERE id > ".$roundIDMinus10 ." ORDER BY `ID` ASC";
									$result2 = mysqli_query($conn, $sql2);
									while($row2 = mysqli_fetch_assoc($result2)) 
									{
										if($row2['roll'] == 0)
										{
											echo "<div class='ball ball-0'>".$row2['roll']."</div>";
										}
										elseif($row2['roll'] <= 7)
										{
											echo "<div class='ball ball-1'>".$row2['roll']."</div>";
										}
										else
										{
											echo "<div class='ball ball-8'>".$row2['roll']."</div>";
										}
									}
									?>
								</div>
							</center>
						</div>
					</div>
					<div class="col s12" style="width: 26.45%;">
						<div class="card-panel" style="">
							<input id="betAmount" type="number" placeholder="Bet Amount" step="0.01" style="height: 31px;">
						</div>
					</div>
					<div class="col s8" style="width: 87.49%;" id="betButtons">
						<div class="card-panel">
							<button class="btn red" id="red">1-7</button>
							<button class="btn green" id="green">0</button>
							<button class="btn black" id="black">8-14</button>
						</div>
					</div>
					<div class="col s8" style="width: 87.49%;">
						<div class="card-panel">
						<div class="rows">
							<div class="redbet">
								<div class="row">
									<div class="name" style="float: left;">Player</div>
									<div class="bet" style="float: right;">Bet</div>
								</div>
							</div>
							<div class="greenbet">
								<div class="row">
									<div class="name" style="float: left;">Player</div>
									<div class="bet" style="float: right;">Bet</div>
								</div>
							</div>
							<div class="blackbet">
								<div class="row">
									<div class="name" style="float: left;">Player</div>
									<div class="bet" style="float: right;">Bet</div>
								</div>
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
			<ol class="discussion wrapper" id="navChat">
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
		<!-- Footer -->
		<footer class="page-footer primary">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text"><?php echo $settings['siteName']; ?></h5>
                    <p class="grey-text text-lighten-4"></p>
                </div>
                <div class="col l3 s12">
					<h5 class="white-text">Resources</h5>
					<ul>
						<li><a class="grey-text text-lighten-4" href="tickets.php">Contact Us</a></li>
						<li><a class="grey-text text-lighten-4 modal-trigger" href="#modal-howtoplay">How To Play</a></li>
						<li><a class="grey-text text-lighten-4 modal-trigger" href="#modal-termsofservice">Terms of Service</a></li>
						<li><a class="grey-text text-lighten-4 modal-trigger" href="#modal-privacypolicy">Privacy Policy</a></li>
					</ul>
				</div>
				<div class="col l3 s12">
					<h5 class="white-text">Connect</h5>
					<ul>
					</ul>
				</div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                Copyright © <?php echo date("Y"); ?> <?php echo $_SERVER['HTTP_HOST'];?>. All Rights Reserved.
            </div>
        </div>
    </footer>
		<!-- Modal's -->
		<?php require ('template_modals.php'); ?>
		
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>