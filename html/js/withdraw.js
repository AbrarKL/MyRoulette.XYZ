$(document).ready(function () {
	socket.on('attemptingWithdraw', function (item) {
		$('#' + item + ' .withdraw-item-button').html('<div class="progress" style="background-color: #303030;margin-top: 15px;"><div class="indeterminate secondary" style="background-color: #FFF;"></div></div>')
	});
	socket.on('tradeSent', function (item, offerid) {
		$('#' + item + ' .withdraw-item-button').html('Acceppt Here')
		$('#' + item + ' .withdraw-item-button').attr('href', offerid)
		$('#' + item + ' .progress').remove()
	});
	socket.on('tradeError', function (item, offerid) {
		$('#' + item + ' .withdraw-item-button').html('Withdraw')
	});
});

$(".withdraw-item-button").click(function () {
	var itemid = $(this).parent().attr('id');
	if ($('#' + itemid + ' .withdraw-item-button').attr('href') == null) {
		console.log($('#' + itemid + ' .withdraw-item-button').html());
		socket.emit('withdraw', itemid);
	}
});