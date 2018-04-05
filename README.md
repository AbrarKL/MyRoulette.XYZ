# MyRoulette.XYZ
CSGO / Steam Roulette Site created with PHP and Node JS, using the Material Design front-end framework.

![](https://i.imgur.com/G3f8UBj.png)

# Usage

1. Clone every file to a web server

2. Upload the database

2. Input your details in 'config.php' (dont forget the steam api key)

3. Input your details in 'site.js'

4. Input your details in '/js/roulette.js' (Enter your Domain or Server IP)

5. Input your details in steamauth/SteamConfig.php

5. In the console goto the 'site.js directory and input this command
   > node site.js

# Requires

steamcommunity
>npm install steamcommunity

steam-totp
>npm install steam-totp

steam-tradeoffer-manager
>npm install steam-tradeoffer-manager

fs
>npm install fs

mysql
>npm install mysql

socket.io
>npm install socket.io

[**Demo Link**](http://104.131.65.32)

# Improvements
I am open sourcing this project as I do not have time to finish it, im busy doing other stuff, and CSGO gambling isn't having a happy ending. This is why you shall expect improvements I haven't completed, however the complete site is working!

Finish Withdraw

Finish 'Status Page' (Staff)

Support proxies instead of having to cache an inventory and not refreshing said inventory until they reopen the deposit page in 4 hours

When user clicks 'Deposit' and 'Withdraw on the Modal, take user to /deposit.php or /withdraw.php, as the modals for depositing and withdrawing, I've stopped using.

Finish footer (Pages for each item) and add footer to every page

Switch Pricing API

# Features
# Roulette

**Dark Mode**

![](https://i.imgur.com/wiPWpAq.png)

**Light Mode**

![](https://i.imgur.com/G3f8UBj.png)

**Chat**
(Hidden in screenshots)

![](https://i.imgur.com/KQOTVPg.png)

**[Other Features (Image Album)](https://imgur.com/a/OSRTf)**

# Admin Panel
**Staff Page**

![](https://i.imgur.com/8oEJ4Hv.png)

**Configuration Page**

![](https://i.imgur.com/NJIRM7a.png)
![](https://i.imgur.com/38LsOD1.png)

**Status Page**

Uncomplete

**Support Page**

![](https://i.imgur.com/wIpXsXD.png)

**Items Page**

![](https://i.imgur.com/izxBPjZ.png)

**Users Page**

**User Not Selected**

![](https://i.imgur.com/Pgn4KPP.png)

**User Selected**

![](https://i.imgur.com/JwoXLGv.png)

**Permissions Page**

**Once Opened**

![](https://i.imgur.com/jaugtGn.png)

**Once Creating a Rank**

![](https://i.imgur.com/QrmHJyo.png)

**Once a rank is Clicked**

![](https://i.imgur.com/579opKR.png)