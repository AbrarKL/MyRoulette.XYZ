<?php	
	// MYSQL
	$servername = 'localhost'; 
	$username = 'root';
	$password = 'FILLIN'; 
	$dbname = 'myroulette'; 

	$settingsRaw = file_get_contents("settings.json");
	$settings = json_decode($settingsRaw, true);

	$permissionsRaw = file_get_contents("permissions.json");
	$permissions = json_decode($permissionsRaw, true);
	
	$identifier = "";
	//Database stuff (Register User to database etc)
	//Connect MYSQL
	$conn = new mysqli($servername, $username, $password, $dbname);
	//Check connection
	if ($conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}
	$sql3 = "SELECT id FROM users WHERE referredBy = '".$_SESSION['steamid']."'";
	$result3 = $conn->query($sql3);
	$affiliateVisitors = $result3->num_rows;
	
	if(isset($_SESSION['steamid']))
	{
		//Obtain steam user details from database
		$sql = "SELECT * FROM `users` WHERE `steamid` = ".$_SESSION['steamid'];
		$result = $conn->query($sql);
		while($row = mysqli_fetch_assoc($result)) {
			// Create Vars from database for steamids
			$identifier = $row['identifier'];
			$balance = number_format($row['balance'], 2);
			$tradeLink = $row['tlink'];
			$userRank = $row['rank'];
			$avatar = $row['avatar'];
			$afiliateLifeTimeEarnings = $row['lifeTimeEarnings'];
			$affiliateAvailableEarnings = $row['availableEarnings'];
			$refferalCode = $row['referralCode'];
		}
		//Obtain steam user details from database
		
		//Check if new user exists in database, if not add
		$sql2 = "SELECT id FROM users WHERE steamid='".$_SESSION['steamid']."'";
		$result2 = $conn->query($sql2);
		if ($result2->num_rows == 0) // If user already exists in the db
		{
			//DONT FORGET TO FILL IN THE API KEY SECTIONDONT FORGET TO FILL IN THE API KEY SECTIONDONT FORGET TO FILL IN THE API KEY SECTIONDONT FORGET TO FILL IN THE API KEY SECTIONDONT FORGET TO FILL IN THE API KEY SECTIONDONT FORGET TO FILL IN THE API KEY SECTIONDONT FORGET TO FILL IN THE API KEY SECTION
			$user = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=FILLIN&steamids='.$_SESSION['steamid'].'&format=json');
			$userdecoded = json_decode($user, true);
			$identifierLength = 40;
			$todaysDate = date("m.d.y.h.s"); // e.g. "03.10.01"
			$identifier = substr(hash('md5', $todaysDate + mt_rand(0,1000000000)), 0, $identifierLength); 
			$nickname = $userdecoded['response']['players'][0]['personaname'];
			$avatar = $userdecoded['response']['players'][0]['avatarfull'];
			
			$sql2 = "INSERT INTO users (identifier, steamid, nickname, avatar, balance, tlink, rank, muted, referralCode, redeemedCode, referredBy, lifeTimeEarnings, availableEarnings) VALUES ('".$identifier."', '".$_SESSION['steamid']."', '". $nickname."', '".$avatar."', '0.00', '', 'User', 0, '', 0, '', 0, 0)";
			$conn->query($sql2);
		}
		//Check if new user exists in database, if not add
	}
?>