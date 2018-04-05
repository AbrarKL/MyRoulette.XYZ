var deposit_items = [];
var deposit_value = 0;

var withdraw_items = [];
var withdraw_value = 0;

$('.modal-trigger').leanModal();
$('.dropdown-button').dropdown('open');
$(".deposit-container .item-container").sort(sort_li).appendTo('.deposit-container');
$(".withdraw-container .item-container").sort(sort_li).appendTo('.withdraw-container');

$(".deposit-container").on("click", ".item-container", function () {
	var className = $(this).attr('class');
	if (className.indexOf("selected") >= 0) {
		$(this).removeClass("selected")
		deposit_items.splice(deposit_items.indexOf($(this).children().data('itemid')), 1);
		deposit_value -= $(this).children().data('value');
	} else {
		$(this).addClass("selected")
		deposit_items.push($(this).children().data('itemid'));
		deposit_value += $(this).children().data('value');
	}
	$('.btn-deposit .amount').html(deposit_items.length)
	$('.btn-deposit .value').html(parseFloat(deposit_value).toFixed(2))
});
$(".withdraw-container").on("click", ".item-container", function () {
	var className = $(this).attr('class');
	if (className.indexOf("selected") >= 0) {
		$(this).removeClass("selected")
		withdraw_items.splice(withdraw_items.indexOf($(this).children().data('itemid')), 1);
		withdraw_value -= $(this).children().data('value');
	} else {
		$(this).addClass("selected")
		withdraw_items.push($(this).children().data('itemid'));
		withdraw_value += $(this).children().data('value');
	}
	$('.btn-withdraw .amount').html(withdraw_items.length)
	$('.btn-withdraw .value').html(parseFloat(withdraw_value).toFixed(2))
});

//Night/Light Mode
$("#night-mode-toggle").click(function () {
	var mode = $('body').attr('class');
	if (mode == "light") {
		document.cookie = "setting=dark";
		$("body").attr('class', 'dark');
		$("#night-mode-toggle .material-icons").html("brightness_3");
	} else {
		document.cookie = "setting=light";
		$("body").attr('class', 'light');
		$("#night-mode-toggle .material-icons").html("brightness_5");
	}
});

// sort function callback
function sort_li(a, b) {
	return ($(b).children().data('value')) > ($(a).children().data('value')) ? 1 : -1;
}