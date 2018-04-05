<?php
echo '<!-- Modal Account -->
		<div id="modal-account" class="modal modal-fixed-footer" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 0px;height:480px;">
			<div class="modal-content">
				<h4>Account</h4>
				<div class="card-panel materialize-red error" style="display: none;">
					<span class="white-text"></span>
				</div>
				<div class="row">
					<div class="row">
						<div class="input-field col s12">
							<input id="account-tradeofferurl" name="tradeOfferUrl" type="url" class="validate" placeholder="https://steamcommunity.com/tradeoffer/new/?partner=XXXXXXXX&amp;token=XXXXXXXX" value="'.$tradeLink.'">
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
		<!-- Modal Deposit -->
		<div id="modal-deposit" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4 style="display: inline-block;">Deposit</h4>
                <div style="display: inline-block; position: absolute; padding: 2px 10px 10px;">
                    <a id="inventory-refresh" class="btn-flat secondary" href="#">Refresh</a>
                </div>
                <div class="deposit-container row">';
						$pricedata = file_get_contents('https://api.csgofast.com/price/all');
						$pricing   = json_decode($pricedata);
						$itemdata  = getFile($_SESSION['steamid']);
						$items     = json_decode($itemdata);
						foreach ($items->rgInventory as $itemRGInventory) {
							$classID         = $itemRGInventory->classid;
							$combined        = $classID . '_' . $itemRGInventory->instanceid;
							$itemID          = $itemRGInventory->id;
							$itemDescription = $items->rgDescriptions->$combined;
							$tradable        = $itemDescription->tradable;
							if ($tradable == 1) {
								$marketName = $itemDescription->market_hash_name;
								$iconURL    = $itemDescription->icon_url;
								$type       = $itemDescription->type;
								echo '<div class="item-container">
														<div class="item tooltipped" style="background-color: #' . $itemDescription->tags[4]->color . '; box-sizing: border-box; border-bottom: solid 5px #D2D2D2;" data-position="bottom" data-delay="0" data-tooltip="' . $marketName . '" data-itemid="' . $itemID . '" data-value="' . $pricing->$marketName . '">
															<img src="https://steamcommunity-a.akamaihd.net/economy/image/class/730/' . $classID . '/95fx95f">
															<span class="helper"></span>
															<span class="price secondary">$' . $pricing->$marketName . '</span>
														</div>
													</div>';
							}
						}
                        echo '
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="waves-effect waves-green btn-flat secondary btn-deposit"><span class="deposit-text grey-text text-darken-4">Deposit (<span class="amount">0</span>) - $<span class="value">0.00</span></span></a>
                <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Close</a>
                <a href="#" class="btn-flat disabled" onclick="return false;">Min Bet: $0.00, Max Items Per Round: '.$settings['maxDepositItems'] .'</a>
            </div>
        </div>


		<div id="modal-withdraw" class="modal modal-fixed-footer" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 0px;">
			<div class="modal-content">
				<h4 style="display: inline-block;">Withdraw</h4>
                <div style="display: inline-block; position: absolute; padding: 2px 10px 10px;">
                    <a id="inventory-refresh" class="btn-flat secondary" href="#">Refresh</a>
                </div>
				<div class="withdraw-container row">
					';
						$pricedata = file_get_contents('https://api.csgofast.com/price/all');
						$pricing   = json_decode($pricedata);
						$sql = "SELECT * FROM market";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								$itemname = rawurldecode($row["name"]);
								echo '
									<div class="item-container">
										<div class="item tooltipped" style="background-color: #'.$row['color'].'; box-sizing: border-box; border-bottom: solid 5px #D2D2D2;"
											data-position="bottom" data-delay="0" data-tooltip="'.$itemname.'" data-itemid="'.$row['itemid'].'" data-value="'.$pricing->$itemname.'">
											<img src="https://steamcommunity-a.akamaihd.net/economy/image/'.$row['img'].'/95fx95f">
											<span class="helper"></span>
											<span class="price secondary">$'.$pricing->$itemname.'</span>
										</div>
									</div>';
							}
						}
						else
						{
							echo '<h5>No Items to View.</h5>';
						}
					echo '
				</div>
			</div>
			<div class="modal-footer">
                <a href="#" class="waves-effect waves-green btn-flat secondary btn-withdraw"><span class="withdraw-text grey-text text-darken-4">Withdraw (<span class="amount">0</span>) - $<span class="value">0.00</span></span></a>
                <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Close</a>
			</div>
		</div>

		<!-- Modal Free Coins -->
		<div id="modal-free-coins" class="modal modal-fixed-footer" style="height: 260px;width: 40%;">
            <div class="modal-content">
                <h4 style="display: inline-block;">Free Coins</h4>
				<div class="row">	
					<div class="input-field col s12">
						<input type="text" id="refferal-code-text" class="validate" placeholder="Enter a referral Code ($'. number_format($settings['refferalBonus'], 2).')">
						<label for="refferal-code-text" class="active">Referral Code</label>
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <a href="#" class="waves-effect waves-green btn-flat secondary" id="refferal-code-redeem"><span class="grey-text text-darken-4">Redeem</span></a>
                <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Close</a>
            </div>
        </div>
		<!-- Modal Affiliates -->
		<div id="modal-affiliates" class="modal modal-fixed-footer" style="height: 390px; width: 35%; z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 0px; overflow: hidden;">
			<div class="modal-content" style="overflow-y: hidden;">
				<h4 style="display: inline-block;">Affiliates</h4>
				<div class="row" style="margin-left: -10px;">
					<div class="input-field col s12">
						<input type="text" id="create-refferal-code-text" class="validate" placeholder="Create a Refferal Code" value="'.$refferalCode.'">
						<label for="create-refferal-code-text" class="active">Referral Code</label>
					</div>
					<div class="input-field col s3" style="clear: left;margin-top: -5px;">
						<button class="btn secondary waves-effect" id="refferal-code-create">Create</button>
					</div>
				</div>
				<div class="row">
					<div class="affiliates-info secondary-text">
						<div class="row">
							<div style="float: left;">Visitors</div>
							<div style="float: right;">'.$affiliateVisitors.'</div>
						</div>
						<div class="row" style="margin-top: -10px;">
							<div style="float: left;">Multiplier</div>
							<div style="float: right;">'.number_format($settings['refferalReward'], 2).'%</div>
						</div>
						<div class="row" style="margin-top: -10px;">
							<div style="float: left;">Lifetime Earnings</div>
							<div style="float: right;">$'.number_format($afiliateLifeTimeEarnings, 2).'</div>
						</div>
						<div class="row" style="margin-top: -10px;">
							<div style="float: left;">Available Earnings</div>
							<div id="affiliate-available-earnings-label" style="float: right;">$'.number_format($affiliateAvailableEarnings, 2).'</div>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="waves-effect waves-green btn-flat secondary" id="refferal-code-claim"><span class="grey-text text-darken-4">Claim</span></a>
				<a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Close</a>
			</div>
		</div>';

?>