var socket = null;
var identifier = null;
var steamid = null;
//NOTIF EXAMPLE
//Materialize.toast("The socket server is currently offline! Please try again later.", 5000, "rounded");



$(document).ready(function() {
    resize();
    identifier = $('meta[name="identifier"]').attr('content');
    steamid = $('meta[name="steamid"]').attr('content');
    //Socket IO
    if (!socket) {
        if (!identifier) {
            console.log("User not logged in.");
			$('.timer').css('width', '100%')
        } else {
            socket = io("http://FILLIN:8000");
            socket.on('connect', function(msg) {
                console.log("Connected!");
                socket.emit('authenticate', identifier);
                $.each($('.row #bet'), function(index, value) {
                    $(this).remove();
                });
            });
                                                                                //REMOVE THIS LATER MAYBE
                                                                                setInterval(function(){
                                                                                    if($('.timer').attr('id') == null)
                                                                                    {
                                                                                        socket.emit('getTime');
                                                                                    }
                                                                                }, 600);
        }
    } else {
        console.log("Connection already exists.");
    }
    //Rolling Roulette
    var currentNum = 0;
    function roll(num) {
            var numWidth = 1050 / 15;

            var layout = [1, 14, 2, 13, 3, 12, 4, 0, 11, 5, 10, 6, 9, 7, 8];

            function getMoves() {
                let to = layout.indexOf(num);
                let at = layout.indexOf(currentNum);

                if (to > at) {
                    return (to - at);
                } else {
                    return (layout.length - at + to);
                }
            }

            var currentPos = parseInt($('#case').css("background-position").split(" ")[0].slice(0, -2));
            currentPos ? null : currentPos = 0;
            $('#case').animate({
                "background-position": currentPos - 2100 - (getMoves() * numWidth),
            }, 3000);
            $('#case').attr('class', currentPos - 2100 - (getMoves() * numWidth));
            currentNum = num;
            setTimeout(function() {
                if ($("#past .ball").length >= 10) {
                    $("#past .ball").first().remove();
                }
                if (num == 0) {
                    $(".ball").last().after("<div class='ball ball-0'>" + num + "</div>");
                } else if (num <= 7) {;
                    $(".ball").last().after("<div class='ball ball-1'>" + num + "</div>");
                } else {
                    $(".ball").last().after("<div class='ball ball-8'>" + num + "</div>");
                }
                $.each($('.row #bet'), function(index, value) {
                    $(this).remove();
                });
            }, 3300);
    }
    //Move roulette wheel on resize to fit perfect
    $(window).resize(function () {
        resize();
    });
    // Socket requests
    socket.on('roll', function(rolledNumber) {
        roll(rolledNumber);
		$(".timer").attr('id', '')
    });
    socket.on('timer', function(timer) {
        $(".timer").attr('id', 'from' + timer)
    });
    socket.on('users', function(num) {
        $('.users-online').html(num);
    });
    socket.on('currentBets', function(currentBets) {
        for (i = 0; i < currentBets.length; i++) {
            $('.' + currentBets[i].colour + 'bet').append('<div class="row" id="bet"><div class="name" style="float: left;">' + currentBets[i].name + '</div><div class="bet" style="float: right;">' + currentBets[i].amount + '</div></div>');
        }
    });
    socket.on('bet', function(bet) {
        $('.' + bet.colour + 'bet').append('<div class="row" id="bet"><div class="name" style="float: left;">' + bet.name + '</div><div class="bet" style="float: right;">' + bet.amount + '</div></div>');
    });
    socket.on('message', function(msg) {
        if (msg.steamid == steamid) {
            $('#nav-chat .discussion.wrapper').append('<li data-steamid="' + msg.steamid + '" class="self" style=""><div class="avatar"><a href="http://steamcommunity.com/profiles/' + msg.steamid + '" target="_blank"><img src="' + msg.avatar + '" class="circle"></a></div><div class="messages"><p>' + msg.message + '</p><time datetime="' + new Date().toString() + '" class="grey-text">' + moment().format("h:mm A") + '</time></div></li>');
        } else {
            $('#nav-chat .discussion.wrapper').append('<li data-steamid="' + msg.steamid + '" class="other" style=""><div class="avatar"><a href="http://steamcommunity.com/profiles/' + msg.steamid + '" target="_blank"><img src="' + msg.avatar + '" class="circle"></a></div><div class="messages"><h1>' + msg.username + '</h1><p>' + msg.message + '</p><time datetime="' + new Date().toString() + '" class="grey-text">' + moment().format("h:mm A") + '</time></div></li>');
        }
        document.getElementById("navChat").scrollTop = document.getElementById("navChat").scrollHeight;
    });
    socket.on('notify', function(info) {
        Materialize.toast(info.message, info.time, "rounded");
        if(info.type == "balance") {
            $('#balance').html(info.data);
        }
        else if(info.type == "affiliateAvailablebalance") {
            $('#balance').html(info.data);
            $('#affiliate-available-earnings-label').html('$0.00');
        }
    });
    //Placing Bet
    $("#betButtons .btn").click(function() {
        var betAmount = $('#betAmount').val();
        socket.emit('placeBet', $(this).attr('id'), betAmount);
    });
    //Chat
    $('#user-message').keydown(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            socket.emit('chatMessage', $('#user-message').val());
            $('#user-message').val("");
            return false;
        }
    });
    //Creating Support Ticket
    $("#user-submit-ticket").click(function() {
        socket.emit('createTicket', grecaptcha.getResponse(), $('#ticketSubject').val(), $('#ticketDescription').val());
    });
    //Remove Support Ticket
    $(".user-close-ticket").click(function() {
        socket.emit('closeTicket', $(this).attr('id'));
    });
    //Responding to Ticket (requires permission)
    $(".admin-respond-ticket").click(function() {
        var ticketID = $(this).parent().closest('div').attr('id');
        var ticketResponse = $('#'+ticketID +' .admin-ticket-response').val();
        socket.emit('respondTicket', ticketID, ticketResponse)
    });
    //Updating Trade Link
    $("#modal-account #saveSettings").click(function() 
    {
    	socket.emit('tradeUpdate', $('#account-tradeofferurl').val())
    });
    //Redeeming Refferal Code
    $("#refferal-code-redeem").click(function() {
        socket.emit('redeemCode', $('#refferal-code-text').val())
    });
    //Creating Refferal Code
     $("#refferal-code-create").click(function() {
        socket.emit('createCode', $('#create-refferal-code-text').val())
    });
    //Claiming available affiliate earnings
     $("#refferal-code-claim").click(function() {
        socket.emit('claimEarnings');
    });
});

var screenWidth = window.innerWidth;

function resize() {
    if(parseInt($('#case').attr('class')) < 0) {
        for (i = 0; i <= 1; i++) { 
            if(i == 0)
            {
                if(screenWidth > window.innerWidth)
                {
                    for (z = 0; z <= 1; z++) 
                    { 
                        if(z == 0)
                        {
                            if(parseFloat($('#case').css('background-position').split(' ')[0].replace("px", "")) > parseFloat($('#case').attr('class')))
                            {
                                console.log(parseFloat($('#case').attr('data-id')));
                                $('#case').css('background-position', parseInt($('#case').attr('class')) + "px")
                                z = 2;
                                return;
                            }
                        }
                        if (z == 1)
                        {
                            $('#case').css('background-position', parseInt($('#case').attr('class')) + ($('#case').width()/70)/2 * 70 -525 + "px")
                            $('#case').attr('data-id', ($('#case').width()/70)/2 * 70 -525);
                        }
                    }
                }
                else
                {
                    for (x = 0; x <= 1; x++) { 
                        if(x == 0)
                        {
                            var a = parseFloat($('#case').css('background-position').split(' ')[0].replace("px", ""))
                            var b = parseFloat($('#case').attr('data-id'));
                            var c = ($('#case').width()/70)/2 * 70 -525;
                            d = b - c;
                            e = a - d;
                            $('#case').css('background-position', e + "px")
                            // it should be a - d position perfect
                        }
                        if(x == 1)
                        {
                            $('#case').attr('data-id', ($('#case').width()/70)/2 * 70 -525);
                        }
                    }
                }
            }
            if(i == 1)
            {
                screenWidth = window.innerWidth;
            }
        }
    }
    else
    {
        $('#case').css('background-position', parseInt($('#case').attr('class')) + ($('#case').width()/70)/2 * 70 -525 + "px")
	    $('#case').attr('data-id', ($('#case').width()/70)/2 * 70 -525);
    }
}