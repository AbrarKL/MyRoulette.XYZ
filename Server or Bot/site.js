//Steam 
var SteamCommunity = require('steamcommunity');
var community = new SteamCommunity();
var SteamTotp = require('steam-totp');
var sharedSecret = "FILLIN";
var identitySecret = "FILLIN";
var TradeOfferManager = require('steam-tradeoffer-manager');
var manager = new TradeOfferManager({
  "domain": "localhost", 
  "language": "en",
  "pollInterval": 30000
});
var logOnOptions = {
  'accountName': "FILLIN",
  'password': "FILLIN",
  'twoFactorCode': SteamTotp.generateAuthCode(sharedSecret)
};
//END STEAM

//Site Server
var fs = require('fs');
var request = require('request');
var mysql = require('mysql');
var io = require('socket.io')(8000);

var settingsRaw = fs.readFileSync('html/settings.json');  
var secretSettingsRaw = fs.readFileSync('secretSettings.json');
var permissionsRaw = fs.readFileSync('html/permissions.json');  
var settings = JSON.parse(settingsRaw);  
var permissions = JSON.parse(permissionsRaw);  
var secretSettings = JSON.parse(secretSettingsRaw);

var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : 'FILLIN',
  database : 'myroulette'
});
connection.connect();


var users = [];
var userInfo = [];
var timer = settings.rouletteTimer;
var currentBets = [];
var currentRollInfo = [];
var rolling = false;

//Steam
function getPrices() {
	request('https://api.csgofast.com/price/all', (error, response, body) => {
		if (!error && response.statusCode === 200) 
		{
			fs.writeFileSync('html/api/prices.json', body);
		}
		else 
		{
			console.log('Error obtaining prices: ' + error +'- Status Code: ' + response.statusCode);
		}
	});
}
setInterval(getPrices, 60000);
community.login(logOnOptions, function(err, sessionID, cookies, steamguard) {
    if (err) {
        console.log("There was an error logging in! Error details: " + err.message);
        process.exit(1); //terminates program
    }
    else
    {
       console.log("Successfully logged in as " + logOnOptions.accountName);
       community.chatLogon();
       manager.setCookies(cookies, function(err) {
         if (err) {
           console.log(err);
           process.exit(1);
         }
       });
     }
    	community.startConfirmationChecker(30000, identitySecret);
});
manager.on('sentOfferChanged', sentOfferChanged);
//If the offer you sent changes
function sentOfferChanged(offer) {
	connection.query("UPDATE offers SET state = '" + offer.state + "' WHERE tradeofferid = '" + offer.id + "'");
		switch (offer.data('type')) {
			case "deposit": //Deposit Trade
				if(offer.state == 3) //Acceppted
				{
					var prices = JSON.parse(fs.readFileSync('html/api/prices.json').toString());
					var marketName = offer.itemsToReceive[0].market_hash_name;
					var img = offer.itemsToReceive[0].icon_url;
					connection.query('UPDATE `users` SET `balance` = `balance` + '+prices[marketName]+' WHERE `steamid` = '+offer.partner);
					//This is to send a notification if they acceppted the trade
					if(userInfo[offer.partner] != null)
					{
							connection.query("SELECT * from users WHERE steamid = " + offer.partner, function(err, row) {
								if (err) 
								{
									console.log('Error while performing Query.');
									return;
								}
								io.sockets.to(userInfo[offer.partner].socketid).emit('notify', { message: "Received Item. You have now been credited $" + prices[marketName] , time: 4500, type: "balance", data: parseFloat(row[0].balance).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")});
							});
					}
					offer.getReceivedItems(function(err, items) { 
						item = {
								itemid: items[0].id,
								name: encodeURIComponent(marketName),
								img: img,
								color: '000000'
						   };
						connection.query('INSERT INTO market SET ?', item);
					});
					request({
						url: secretSettings.discordWebhookURL,
						json: {
							"embeds": [{
								"thumbnail": {
									"url": "https://steamcommunity-a.akamaihd.net/economy/image/" + offer.itemsToReceive[0].icon_url
								},
								"color": 7584112,
								"fields": [{
									"name": offer.partner + " Deposited an Item!",
									"value": "Worth $" + prices[marketName],
									"inline": true
								}]
							}]
						},
						method: 'POST'
					}, function (err, res, body) {});
				}
				break;
			case "withdraw": //Withdraw Trade
				if(offer.state == 3)
				{
					connection.query('DELETE FROM market WHERE itemid='+offer.itemsToGive[0].id);
				}
				else if(offer.state == 1 || offer.state == 5 || offer.state == 6 || offer.state == 7 || offer.state == 8)
				{
					console.log("Trade Error");
					connection.query("SELECT * FROM offers WHERE tradeofferid =" + offer.id, function (err, result) {
						connection.query('UPDATE `users` SET `balance` = `balance` + '+result[0].value+' WHERE `steamid` = '+offer.partner);
						if(userInfo[offer.partner] != null)
						{
							connection.query("SELECT * from users WHERE steamid = " + offer.partner, function(err, row) {
								if (err) 
								{
									console.log('Error while performing Query.');
									return;
								}
								io.sockets.to(userInfo[offer.partner].socketid).emit('notify', { message: "Declined Trade. You have now been credited your $" + result[0].value , time: 4500, type: "balance", data: parseFloat(row[0].balance).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")});
							});
						}
					});
				}
			default:
				return;
		}
}
//END STEAM

io.on('connection', function(socket) {
	//Get the current time to update incase.
	socket.on('getTime', function(identifier) {
		io.sockets.to(socket.id).emit("timer", timer);
	});
	// When user connects to site
	socket.on('authenticate', function(identifier) {
		getAccountInfo(identifier,function(account){
			if(account != null)
			{
				//Only one session can be open
				//if(userInfo[account.steamid] == null)
				//{
				//}					
				io.sockets.emit("users", Object.keys(users).length+1);
				users[socket.id] = {steamid: account.steamid, identifier: identifier, avatar: account.avatar, name: account.nickname, redeemedCode: account.redeemedCode, referredBy: account.referredBy };
				userInfo[account.steamid] = { rank: account.rank.toLowerCase(), muted: account.muted, canTalk: true, socketid: socket.id };
				io.sockets.to(socket.id).emit('currentBets', currentBets);
				io.sockets.to(socket.id).emit("timer", timer);
			}
		});
	});
	// When user Withdraws an item
	socket.on('withdraw', function (item) {
		if(users[socket.id] != null)
		{
			io.sockets.to(socket.id).emit('attemptingWithdraw', item);
			var prices = JSON.parse(fs.readFileSync('html/api/prices.json').toString());
			manager.getInventoryContents(730, 2, true, function (err, inventory, currencies) {
				if (err) {
					return;
				}
				for (var i = 0; i < inventory.length; i++) {
					if (inventory[i].id == item) {
						var marketName = inventory[i]['market_hash_name'];
						console.log("Attempting to Withdrawing " + marketName);
						getAccountInfo(users[socket.id].identifier, function (account) {
							if (account.balance >= prices[marketName]) {
								if (prices[marketName] != 0) {
									//Sending the trade
									//Creating the Trade
									connection.query('UPDATE `users` SET `balance` = `balance` - ' + prices[marketName] + ' WHERE `steamid` = ' + users[socket.id].steamid);
									io.sockets.to(socket.id).emit('notify', {
										message: "Attempting to Withdraw",
										time: 3500,
										type: "balance",
										data: parseFloat(account.balance - prices[marketName]).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
									});
									try {
										var offer = manager.createOffer(account.tlink);
									} catch (e) {
										console.log(e);
										io.sockets.to(socket.id).emit('notify', {
											message: "An error occured whilst sending trade offer. Please make sure your Trade URL is accurate.",
											time: 3500
										});
										io.sockets.to(socket.id).emit('tradeError', item);
										return;
									}
									//Checking escrow days
									offer.getUserDetails(function (err, me, them) {
										if (err) {
											console.log("An error occured whilst Checking escrow details: " + err);
											io.sockets.to(socket.id).emit('notify', {
												message: "An error occured whilst sending trade offer. Please try again later.",
												time: 3500
											});
											io.sockets.to(socket.id).emit('tradeError', item);
											return;
										}
										if (them.escrowDays == 0) {
											offer.setMessage("By acceppting this offer you will lose $" + prices[marketName] + " on site credits.");
											offer.addMyItem({
												"assetid": item,
												"appid": 730,
												"contextid": 2
											});
											offer.data('type', 'withdraw');
											if (users[socket.id] != null) {
												offer.send(function (err, status) {
													if (err) {
														console.log(err); //REMOVE LATER FOR DEBUG PURPOSES
														io.sockets.to(socket.id).emit('notify', {
															message: "An error occured whilst sending trade offer. Please try again later.",
															time: 3500
														});
														io.sockets.to(socket.id).emit('tradeError', item);
														return;
													}
													if (status == "pending") {
														if (users[socket.id] != null) {
															io.sockets.to(socket.id).emit('notify', {
																message: "Trade Offer Sent!",
																time: 3500
															});
															io.sockets.to(socket.id).emit('tradeSent', item, 'http://steamcommunity.com/tradeoffer/' + offer.id);
															offer = {
																steamid: users[socket.id].steamid,
																item: item,
																type: 'withdraw',
																state: offer.state,
																value: prices[marketName],
																tradeofferid: offer.id
															};
															connection.query('INSERT INTO offers SET ?', offer);
															community.checkConfirmations();
														}
													}
												});
											}
										}
									});
								} else {
									io.sockets.to(socket.id).emit('notify', {
										message: "An error occured whilst sending trade offer. Please try again later.",
										time: 3500
									});
									io.sockets.to(socket.id).emit('tradeError', item);
								}
							} else {
								io.sockets.to(socket.id).emit('notify', {
									message: "You do not have enough balance to withdraw.",
									time: 3500
								});
								io.sockets.to(socket.id).emit('tradeError', item);
							}
							//END Sending the trade
						});
					}
				}
			});
		}
	});
	// When user Deposits an item
	socket.on('deposit', function (item) {
		if(users[socket.id] != null)
		{
			io.sockets.to(socket.id).emit('attemptingDeposit', item);
			var prices = JSON.parse(fs.readFileSync('html/api/prices.json').toString());
			var inventory = JSON.parse(fs.readFileSync('html/cache/'+users[socket.id].steamid).toString());
			var classID = inventory['rgInventory'][item]['classid'];
			var instanceID = inventory['rgInventory'][item]['instanceid'];
			var combined = classID + '_' + instanceID;
			var itemDescription = inventory['rgDescriptions'][combined];
			
			console.log("Depositing " + itemDescription['market_hash_name']);
			
			getAccountInfo(users[socket.id].identifier, function (account) {
				//Sending the trade
				//Creating the Trade
				try {
					var offer = manager.createOffer(account.tlink);
				}
				catch (e) {
					console.log(e);
					io.sockets.to(socket.id).emit('notify', {
							message: "An error occured whilst sending trade offer. Please make sure your Trade URL is accurate.",
							time: 3500
						});
					io.sockets.to(socket.id).emit('tradeError', item);
					return;
				}
				//Checking escrow days
				offer.getUserDetails(function (err, me, them) {
					if (err) {
						console.log("An error occured whilst Checking escrow details: " + err);
						io.sockets.to(socket.id).emit('notify', {
							message: "An error occured whilst sending trade offer. Please try again later.",
							time: 3500
						});
						io.sockets.to(socket.id).emit('tradeError', item);
						return;
					}
					if (them.escrowDays == 0) {
						offer.setMessage("By acceppting this offer you will receive $" + prices[itemDescription['market_hash_name']] + " on site credits.");
						offer.addTheirItem({
							"assetid": item,
							"appid": 730,
							"contextid": 2
						});
						offer.data('type', 'deposit');
						if(users[socket.id] != null)
						{
						offer.send(function (err, status) {
							if (err) {
								console.log(err); //REMOVE LATER FOR DEBUG PURPOSES
								io.sockets.to(socket.id).emit('notify', {
									message: "An error occured whilst sending trade offer. Please try again later.",
									time: 3500
								});
								io.sockets.to(socket.id).emit('tradeError', item);
								return;
							}
							if (status == "sent") {
								if(users[socket.id] != null)
								{
									io.sockets.to(socket.id).emit('notify', {
										message: "Trade Offer Sent!",
										time: 3500
									});
									io.sockets.to(socket.id).emit('tradeSent', item, 'http://steamcommunity.com/tradeoffer/'+offer.id);
									offer = {
										steamid: users[socket.id].steamid,
										item: item,
										type: 'deposit',
										state: offer.state,
										value: prices[itemDescription['market_hash_name']],
										tradeofferid: offer.id
									};
									connection.query('INSERT INTO offers SET ?', offer);
								}
							}
						});
					}
					}
				});
				//END Sending the trade
			});
		}
	});
	// When user places a bet
	socket.on('placeBet', function(colour, amount) {
		if(users[socket.id] != null)
		{
			var betAmount = parseFloat(amount).toFixed(2);
			if(betAmount > 0)
			{
				getAccountInfo(users[socket.id].identifier, function(account)
				{
					if(account.balance >= betAmount)
					{
						if(timer > 0)
						{
							var bets = 0;
							currentBets.forEach(function(bet) {
								if(bet.steamid ==  users[socket.id].steamid)
								{
									bets++;
								}
							});
							if(bets < settings.maxBets) // If user hasnt bet 3 times
							{
								if(betAmount >= parseFloat(settings.minimumBet))
								{
									if(betAmount <= parseFloat(settings.maximumBet))
									{
										io.sockets.to(socket.id).emit('notify', { message: "Placing bet. " + (bets+1) + "/"+settings.maxBets, time: 3500, type: "balance", data: parseFloat(account.balance-betAmount).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") });
										//.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") adds comma seperators on balance
										connection.query('UPDATE `users` SET `balance` = `balance` - '+betAmount+' WHERE `steamid` = '+users[socket.id].steamid);
										currentBets.push({
												amount: betAmount,
												colour: colour,
												name: users[socket.id].name,
												steamid: users[socket.id].steamid
											});
										bet = { user: users[socket.id].steamid, amount: betAmount, colour: colour, name: users[socket.id].name };
										io.sockets.emit('bet', bet);
										connection.query('INSERT INTO bets SET ?', bet);
										connection.query('UPDATE `site` SET `totalIn` = `totalIn` + '+betAmount+' WHERE `id` = 1');
										if(users[socket.id].redeemedCode == 1)
										{
											connection.query('UPDATE `users` SET `lifeTimeEarnings` = `lifeTimeEarnings` + '+(settings.refferalReward/100)*betAmount+' WHERE `steamid` = ' + users[socket.id].referredBy);
											connection.query('UPDATE `users` SET `availableEarnings` = `availableEarnings` + '+(settings.refferalReward/100)*betAmount+' WHERE `steamid` = ' + users[socket.id].referredBy);
										}
									}
									else
									{
										io.sockets.to(socket.id).emit('notify', { message: "Your bet is above the maximum bet, which is $"+settings.maximumBet, time: 3500 });
									}
								}
								else
								{
									io.sockets.to(socket.id).emit('notify', { message: "Your bet is below the minimum bet, which is $"+settings.minimumBet, time: 3500 });
								}
							}
							else
							{
								io.sockets.to(socket.id).emit('notify', { message: "You can only bet "+settings.maxBets+" times per round.", time: 3500 });
							}
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "You can not bet when rolling.", time: 3500 });
						}
					}
					else
					{
						io.sockets.to(socket.id).emit('notify', { message: "You do not have enough balance to place this bet.", time: 3500 });
					}
				});
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "Your bet must be above $0.00.", time: 3500 });
			}
		}
	});
	// When user creates a ticket
	socket.on('createTicket', function (captchaResponse, subject, description) {
		if(users[socket.id] != null)
		{
			if(/^\s*$/.test(subject)) // if the text is blank
			{
				io.sockets.to(socket.id).emit('notify', { message: "Please enter a Subject.", time: 3500 });  
			}
			else
			{
				if(/^\s*$/.test(description)) // if the text is blank
				{
					io.sockets.to(socket.id).emit('notify', { message: "Please enter a Description.", time: 3500 });  
				}
				else
				{
					request('https://www.google.com/recaptcha/api/siteverify?secret=' + secretSettings.privateSecretKey + '&response=' + captchaResponse, function (error, response, body) {
						var bodyParsed = JSON.parse(body);
						if(bodyParsed.success == true)
						{
							ticket = { steamid: users[socket.id].steamid, name: users[socket.id].name, subject: subject, description: description, response: 'Awaiting Reply.', resolved: 0 };
							connection.query('INSERT INTO support SET ?', ticket);
							io.sockets.to(socket.id).emit('notify', { message: "Ticket Submitted.", time: 3500 });  
						}
						else
						{
							if(bodyParsed["error-codes"][1] == 'invalid-input-secret')
							{
								io.sockets.to(socket.id).emit('notify', { message: "Invalid Secret Key (Private). Please contact the Site Owner.", time: 3500 }); 
								return;
							}
							io.sockets.to(socket.id).emit('notify', { message: "Please complete the ReCaptcha.", time: 3500 });  
							return;
						}
					});
				}
			}
		}
	});
	// When user closes a ticket
	socket.on('closeTicket', function (ticketID) {
		if(users[socket.id] != null)
		{
			connection.query("SELECT * FROM support WHERE id =" + parseInt(ticketID), function (err, result) {
				if(result[0].steamid == users[socket.id].steamid)
				{
					if(result.length >= 1)
					{
						connection.query("DELETE FROM support WHERE id = " + parseInt(ticketID));
						io.sockets.to(socket.id).emit('notify', { message: "Ticket Removed.", time: 3500 });  
					}
					else
					{
						io.sockets.to(socket.id).emit('notify', { message: "Please select a valid ticket to remove.", time: 3500 });  
					}
				}
				else
				{
					io.sockets.to(socket.id).emit('notify', { message: "Please select your own ticket to remove.", time: 3500 });  
				}
			});
		}
	});
	// When user sends a chat message
	socket.on('chatMessage', function (message) {
		if(users[socket.id] != null)
		{
			if(/^\s*$/.test(message)) // if the text is blank
			{
			}
			else
			{
				if(userInfo[users[socket.id].steamid].canTalk == true)
				{
					if(userInfo[users[socket.id].steamid].muted == 0) 
					{
						// message regex replaces < and > with html equivelant to stop html injection
						io.sockets.emit('message', { message: message.replace(/</g, "&lt;").replace(/>/g, "&gt;"), steamid: users[socket.id].steamid, avatar: users[socket.id].avatar, username: users[socket.id].name });
						userInfo[users[socket.id].steamid].canTalk = false;
						setTimeout(function() {
							userInfo[users[socket.id].steamid].canTalk = true;
						}, settings.chatDelay*1000);
					}
					else
					{
						io.sockets.to(socket.id).emit('notify', { message: "You are muted from the chat.", time: 3500 });
					}
				}
				else
				{
					io.sockets.to(socket.id).emit('notify', { message: "You can only send a message once every "+settings.chatDelay+" seconds.", time: 3000 });
				}
			}
		}
	});
	// When user updates their trade url
	socket.on('tradeUpdate', function (tLink) {
		if(users[socket.id] != null)
		{
			tradeLink = connection.escape(tLink);
			if(tradeLink.indexOf('https://steamcommunity.com/tradeoffer/new/?partner=') > -1) 
			{
				connection.query('UPDATE `users` SET `tlink` = '+tradeLink+' WHERE `steamid` = '+users[socket.id].steamid);
				io.sockets.to(socket.id).emit('notify', { message: "Trade Offer URL updated.", time: 2500 });
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid trade URL.", time: 2500 });
			}
		}
	});
	// When user redeems an affiliate code
	socket.on('redeemCode', function (code) {
		if(users[socket.id] != null)
		{
			if(users[socket.id].redeemedCode == 0)
			{
				if(/^\s*$/.test(code)) // if the text is blank
				{
					io.sockets.to(socket.id).emit('notify', { message: "Please enter a Referral Code.", time: 3500 });  
				}
				else
				{
					getAccountInfo(users[socket.id].identifier, function(account)
					{
						refCode = connection.escape(code);
						connection.query("SELECT * from users WHERE referralCode = " + refCode, function(err, row) {
							if (err) 
							{
								console.log('Error while performing Query.');
								return;
							}
							if(row.length == 1)
							{
								if(row[0].steamid == users[socket.id].steamid)
								{
									io.sockets.to(socket.id).emit('notify', { message: "You can not redeem your own code.", time: 3500 });  
								}
								else
								{
									connection.query('UPDATE `users` SET `balance` = `balance` + '+settings.refferalBonus+' WHERE `steamid` = '+users[socket.id].steamid);
									connection.query('UPDATE `users` SET `redeemedCode` = 1 WHERE `steamid` = '+users[socket.id].steamid);
									connection.query('UPDATE `users` SET `referredBy` = '+row[0].steamid+' WHERE `steamid` =  '+users[socket.id].steamid)
									users[socket.id].redeemedCode = 1;
									users[socket.id].referredBy = row[0].steamid;
									io.sockets.to(socket.id).emit('notify', { message: "Code Redeemed.", time: 3500, type: "balance", data: parseFloat(account.balance+settings.refferalBonus).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") });  
								}
							}
							else
							{
								io.sockets.to(socket.id).emit('notify', { message: "Code Not Found.", time: 3500 });  
							}
						});
					});
				}
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You have already redeemed a Refferal Code.", time: 3500 });  
			}
		}
	});
	// When user creates an affiliate code
	socket.on('createCode', function (code) {
		if(users[socket.id] != null)
		{
			if(/^\s*$/.test(code)) // if the text is blank
			{
				io.sockets.to(socket.id).emit('notify', { message: "Please enter a Referral Code.", time: 3500 });  
			}
			else
			{
				refCode = connection.escape(code);
				connection.query("SELECT * from users WHERE referralCode = " + refCode, function(err, row) {
					if (err) 
					{
						console.log('Error while performing Query.');
						return;
					}
					if(row.length >= 1)
					{
						io.sockets.to(socket.id).emit('notify', { message: "This Refferal Code already exists.", time: 3500, type: "balance", data: parseFloat(row[0].balance+settings.refferalBonus).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") });  
					}
					else if(row.length == 0)
					{
						io.sockets.to(socket.id).emit('notify', { message: "Updated refferal code to: " + refCode, time: 3500 });  
						connection.query('UPDATE `users` SET `referralCode` = '+refCode+' WHERE `steamid` = '+users[socket.id].steamid);
					}
				});
			}
		}
	});
	// When user claims available affiliate earnings
	socket.on('claimEarnings', function () {
		if(users[socket.id] != null)
		{
			getAccountInfo(users[socket.id].identifier, function(account)
			{
				if(parseFloat(account.availableEarnings) > 0)
				{
					connection.query('UPDATE `users` SET `balance` = `balance` + '+parseFloat(account.availableEarnings)+' WHERE `steamid` = '+users[socket.id].steamid);
					connection.query('UPDATE `users` SET `availableEarnings` = 0');
					io.sockets.to(socket.id).emit('notify', { message: "Claimed $" + parseFloat(account.availableEarnings) + " available earnings.", time: 3500, type: "affiliateAvailablebalance", data: parseFloat(account.balance+parseFloat(account.availableEarnings)).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") });  
				}
				else
				{
					io.sockets.to(socket.id).emit('notify', { message: "You do not have enough available earnings to claim.", time: 3500 });  
				}
			});
		}
	});
	//****************************************PERMISSIONS STUFF****************************************
	socket.on('mute', function (targetSteamID, mute) {
		if(users[socket.id] != null)
		{
			if(permissions[userInfo[users[socket.id].steamid].rank].mute == 'true') 
			{
				if(Object.keys(userInfo).indexOf(targetSteamID) > -1)
				{
					userInfo[targetSteamID].muted = mute;
				}
				connection.query('UPDATE `users` SET `muted` = '+ parseInt(~~mute) + ' WHERE `steamid` = '+connection.escape(targetSteamID));
				io.sockets.to(socket.id).emit('notify', { message: targetSteamID + " is now " + (mute ? 'muted' : 'unmuted') + ".", time: 2500 });
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You are not a suitable rank to perform this function.", time: 2500 });
			}
		}
 	});
	socket.on('selectRank', function (targetSteamID, newrank) {
		if(users[socket.id] != null)
		{
			if(permissions[userInfo[users[socket.id].steamid].rank].selectRank == 'true') 
			{
				if(newrank.toLowerCase() == 'moderator' || newrank.toLowerCase() == 'admin' || newrank.toLowerCase() == 'user')
				{
					if(Object.keys(userInfo).indexOf(targetSteamID) > -1)
					{
						userInfo[targetSteamID].rank = newrank.toLowerCase();
					}
					connection.query('UPDATE `users` SET `rank` = '+ connection.escape(newrank) + ' WHERE `steamid` = '+connection.escape(targetSteamID));
					io.sockets.to(socket.id).emit('notify', { message: targetSteamID + "'s rank is now : " + newrank + ".", time: 2500 });
				}
				else
				{
					io.sockets.to(socket.id).emit('notify', { message: "This rank is not valid.", time: 2500 });
				}
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You are not a suitable rank to perform this function.", time: 2500 });
			}
		}
 	});
	socket.on('createRank', function (rank) {
		if(users[socket.id] != null)
		{
			if(permissions[userInfo[users[socket.id].steamid].rank].modifyRank == 'true') 
			{
				if(/^\s*$/.test(rank)) // if the text is blank
				{
					io.sockets.to(socket.id).emit('notify', { message: "Please enter a rank name.", time: 2500 });
				}
				else
				{
					permissions[rank.toLowerCase()] = { "staff" : "false"};
					fs.writeFileSync('html/permissions.json', JSON.stringify(permissions, null, 2));
					io.sockets.to(socket.id).emit('notify', { message: "Successfully created rank: " + rank + ".", time: 2500 });
				}
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You are not a suitable rank to perform this function.", time: 2500 });
			}
	 	}
 	});
	socket.on('updatePerm', function (rank, perms) {
		if(users[socket.id] != null)
		{
			if(permissions[userInfo[users[socket.id].steamid].rank].modifyRank == 'true') 
			{
				permissions[rank] = perms;
				fs.writeFileSync('html/permissions.json', JSON.stringify(permissions, null, 2));
				io.sockets.to(socket.id).emit('notify', { message: "Successfully updated " + rank.charAt(0).toUpperCase() + rank.slice(1) + " rank's permissions.", time: 2500 });
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You are not a suitable rank to perform this function.", time: 2500 });
			}
		}
 	});
	socket.on('respondTicket', function (ticketID, ticketResponse) {
		if(users[socket.id] != null)
		{
			if(permissions[userInfo[users[socket.id].steamid].rank].answerTickets == 'true') 
			{
				if(/^\s*$/.test(ticketResponse)) // if the text is blank
				{
					io.sockets.to(socket.id).emit('notify', { message: "Please enter a response.", time: 3500 });
				}
				else
				{
					if(parseInt(ticketID) == null)
					{
						io.sockets.to(socket.id).emit('notify', { message: "Please select a valid ticket.", time: 3500 });
					}
					else
					{
						connection.query('UPDATE `support` SET `response` = '+ connection.escape(ticketResponse) + ' WHERE `id` = '+parseInt(ticketID));
						connection.query('UPDATE `support` SET `resolved` = 1 WHERE `id` = '+parseInt(ticketID));
						io.sockets.to(socket.id).emit('notify', { message: "Responded to ticket #" + parseInt(ticketID) + ".", time: 3500 });
					}
				}
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You are not a suitable rank to perform this function.", time: 2500 });
			}
		}
 	});
	socket.on('saveSetting', function (setting, value) {
		if(users[socket.id] != null)
		{
			if(permissions[userInfo[users[socket.id].steamid].rank][setting] == 'true') 
			{
				switch(setting) {
					case "minimumBet":
						if(!isNaN(parseFloat(value)))
						{
							settings.minimumBet = parseFloat(value);
							io.sockets.to(socket.id).emit('notify', { message: "Minimum Bet updated to: $" + parseFloat(value) + ".", time: 4500 });  
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "maximumBet":
						if(!isNaN(parseFloat(value)))
						{
							settings.maximumBet = parseFloat(value);
							io.sockets.to(socket.id).emit('notify', { message: "Maximum Bet updated to: $" + parseFloat(value) + ".", time: 4500 }); 
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "maxDepositItems":
						if(!isNaN(parseInt(value)))
						{
							settings.maxDepositItems = parseInt(value);
							io.sockets.to(socket.id).emit('notify', { message: "Maximum Items Per Deposit updated to: " + parseInt(value) + " items.", time: 4500 });  
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "maxBets":
						if(!isNaN(parseInt(value)))
						{
							settings.maxBets = parseInt(value);
							io.sockets.to(socket.id).emit('notify', { message: "Maximum Bets Per User updated to: " + parseInt(value) + " bets.", time: 4500 });  
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "itemMarkup":
						if(!isNaN(parseFloat(value)))
						{
							settings.itemMarkup = parseFloat(value);
							io.sockets.to(socket.id).emit('notify', { message: "Deposit Item Markup (%) updated to: " + value + "%.", time: 4500 });  
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "chatDelay":
						if(!isNaN(parseFloat(value)))
						{
							settings.chatDelay = parseFloat(value);
							io.sockets.to(socket.id).emit('notify', { message: "Chat Delay updated to: " + value + " seconds.", time: 4500 });  
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "rouletteTimer":
						if(parseInt(value) >= 5 && parseInt(value) <= 60)
						{
							if(!isNaN(parseInt(value)))
							{
								settings.rouletteTimer = parseInt(value);
								io.sockets.to(socket.id).emit('notify', { message: "Roulette Timer updated to: " + parseInt(value) + " seconds.", time: 4500 }); 
							}
							else
							{
								io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
							}
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Timer must be over 5 seconds and less than 60 seconds.", time: 4500 });  
						}
						break;
					case "refferalBonus":
						if(!isNaN(parseFloat(value)))
						{
							settings.refferalBonus = parseFloat(value);
							io.sockets.to(socket.id).emit('notify', { message: "Refferal Bonus updated to: $" + parseFloat(value) + ".", time: 4500 }); 
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "refferalReward":
						if(!isNaN(parseFloat(value)))
						{
							settings.refferalReward = parseFloat(value);
							io.sockets.to(socket.id).emit('notify', { message: "Refferal Reward updated to: " + parseFloat(value) + "%.", time: 4500 }); 
						}
						else
						{
							io.sockets.to(socket.id).emit('notify', { message: "Please enter a valid number.", time: 4500 });  
						}
						break;
					case "siteName":
						settings.siteName = value;
						io.sockets.to(socket.id).emit('notify', { message: "Site name updated to: "+value+".", time: 4500 });  
						break;
					case "giveawayText":
						settings.giveawayText = value;
						io.sockets.to(socket.id).emit('notify', { message: "Giveaway text updated to: "+value+".", time: 4500 });  
						break;
					case "giveawayLink":
						settings.giveawayLink = value;
						io.sockets.to(socket.id).emit('notify', { message: "Giveaway link updated to: "+value+".", time: 4500 });  
						break;
					case "publicSiteKey":
						settings.publicSiteKey = value;
						io.sockets.to(socket.id).emit('notify', { message: "Public Site Key updated to: "+value+".", time: 4500 });  
						break;
					case "privateSecretKey":
						secretSettings.privateSecretKey = value;
						io.sockets.to(socket.id).emit('notify', { message: "Private Secret Key updated to: "+value+".", time: 4500 });  
						break;
					default:
						io.sockets.to(socket.id).emit('notify', { message: "Please change a valid Setting.", time: 2500 });
						
				}
				if(setting == 'privateSecretKey') 
				{ 
					fs.writeFileSync('secretSettings.json', JSON.stringify(secretSettings, null, 2));
				}
				else
				{
					fs.writeFileSync('html/settings.json', JSON.stringify(settings, null, 2));
				}
			}
			else
			{
				io.sockets.to(socket.id).emit('notify', { message: "You are not a suitable rank to perform this function.", time: 2500 });
			}
		}
 	});
	//****************************************PERMISSIONS STUFF END****************************************
	socket.on('disconnect', function () {
		//discord.sendMessage(users[socket.id].steamid + ' disconnected');
	    //console.log(users[socket.id].steamid + ' disconnected');
		if(users[socket.id] != null)
		{
			delete userInfo[users[socket.id].steamid];
	    	delete users[socket.id];
	   	    io.sockets.emit("users", Object.keys(users).length)+1;
		}
  });
});

//Roulette Timer
setInterval(function() {
	//For Debug Purposes console.log(timer);
	if(timer == 0)
	{
		var rolledNumber = (Math.floor(Math.random() * 14));
		var colour;
		if (rolledNumber == 0) 
		{
			colour = "green";
		} 
		else if (rolledNumber <= 7) 
		{
			colour = "red";
		}
		else
		{
			colour = "black";
		}
		io.sockets.emit("roll", rolledNumber);
		currentRollInfo.push({roll: rolledNumber, colour: colour, time: Math.round((new Date()).getTime() / 1000)});
		console.log("Rolled " + colour);
		connection.query('INSERT INTO rolls SET ?', currentRollInfo);
	} 
	if(timer == -4) // -3 = more on time
	{
		currentBets.forEach(function(bet) {
			if(bet.colour == currentRollInfo[0].colour)
			{
				console.log(bet.steamid + " Has won " + bet.amount*2);
				connection.query('UPDATE `users` SET `balance` = `balance` + '+bet.amount*2+' WHERE `steamid` = '+bet.steamid);
				connection.query('UPDATE `site` SET `totalOut` = `totalOut` + '+bet.amount+' WHERE `id` = 1');
			}
		});
		currentBets = [];
		currentRollInfo = [];
		timer = settings.rouletteTimer;
		io.sockets.emit("timer", timer);
	}
	timer-= 1;
},  1000);

function getAccountInfo(identifier, callback)
{
	connection.query("SELECT * from users WHERE identifier = " + connection.escape(identifier), function(err, row) {
		if (err) 
		{
			console.log('Error while performing Query.');
			//io.sockets.emit('notification', { message: "Error obtaining user details. Please try again later.", identifier: identifier, type: "error" });
			return;
		}
		callback(row[0]);
	});
}