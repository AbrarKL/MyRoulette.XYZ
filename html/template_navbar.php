<?php
echo '<header>
			<nav role="navigation" class="primary">
				<div class="nav-wrapper container">
					<a id="logo-container" href="/" class="brand-logo" style="font-weight: 300;">'. $settings['siteName'] .'</a>
					<ul class="right hide-on-med-and-down" style="margin-right: 250px;">';
						if(isset($_SESSION['steamid'])) 
						{
							echo '<li class="waves-effect">
									    <a class="modal-trigger secondary-text" href="#modal-deposit">Deposit</a>
								  </li>
								  <li class="waves-effect">
									    <a class="modal-trigger secondary-text" href="#modal-withdraw">Withdraw</a>
								  </li>
								  <li class="waves-effect">
									    <a class="modal-trigger" href="#modal-free-coins">Free Coins</a>
								  </li>';
							if ($permissions[strtolower($userRank)]["staff"] == 'true')
							{
								echo '<li class="waves-effect"><a href="/staff.php">Staff</a></li>';
							}
							echo '<li><div id="balance" style="padding: 1px 6px;">'.$balance.'</div></li>';
							echo '<li>
									<a href="#" class="dropdown-button user" data-activates="user-dropdown">'.$_SESSION['steam_personaname'].'<i class="material-icons right">arrow_drop_down</i></a>
									<ul id="user-dropdown" class="dropdown-content" style="width: 117px; position: absolute; top: 0px; left: 1175.84px; opacity: 1; display: none;">
										<li class="waves-effect"><a href="#modal-account" class="modal-trigger secondary-text">Account</a></li>
										<li class="divider"></li>
										<li class="waves-effect"><a href="#modal-affiliates" class="modal-trigger secondary-text">Affiliates</a></li>
										<li class="divider"></li>
										<li class="waves-effect"><a href="http://104.131.65.32/steamauth/logout.php" class="secondary-text">Logout</a></li>
									</ul>
								 </li>';
						}
						else
						{
							echo '<li>';
							loginbutton();
							echo '</li>';
						} 
                        echo '
						<li class="waves-effect chat-link">
							<a href="#" data-activates="nav-chat" class="chat show-on-large"><i class="material-icons">chat_bubble_outline</i></a>
						</li>
						<li class="waves-effect">
							<a href="#" id="night-mode-toggle" style="height: 64px;"><i class="material-icons">';
                                if(isset($_COOKIE['setting']))
                                { 
                                    if($_COOKIE['setting'] == 'dark') 
                                    { 
                                        echo 'brightness_3';
                                    } 
                                    else 
                                    {
                                        echo 'brightness_5'; 
                                    } 
                                } 
                                else
                                { 
                                    echo'brightness_3';
                                } 
                                echo '</i></a>
						</li>
					</ul>
				</div>
			</nav>
		</header>';
?>