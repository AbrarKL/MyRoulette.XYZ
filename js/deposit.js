$(document).ready(function () {
	socket.on('attemptingDeposit', function (item) {
		$('#' + item + ' .deposit-item-button').html('<div class="progress" style="background-color: #303030;margin-top: 15px;"><div class="indeterminate secondary" style="background-color: #FFF;"></div></div>')
	});
	socket.on('tradeSent', function (item, offerid) {
		$('#' + item + ' .deposit-item-button').html('Acceppt Here')
		$('#' + item + ' .deposit-item-button').attr('href', offerid)
		$('#' + item + ' .progress').remove()
	});
	socket.on('tradeError', function (item, offerid) {
		$('#' + item + ' .deposit-item-button').html('Deposit')
	});
});

$(".deposit-item-button").click(function () {
	var itemid = $(this).parent().attr('id');
	if ($('#' + itemid + ' .deposit-item-button').attr('href') == null) {
		socket.emit('deposit', itemid);
	}
});