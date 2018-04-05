$('#admin-giveaway-text').on('input',function(e){
    $('#example-giveaway-text').html(($(this).val()).replace("$link", '<a class="secondary-text" href="'+$('#admin-giveaway-link').val()+'" style="padding: 0;line-height: 0;height: 0;display: inline;color: #fff;">'+$('#admin-giveaway-link').val()+'</a>'))
});

$('#admin-giveaway-link').on('input',function(e){
	$('#example-giveaway-text').html(($('#admin-giveaway-text').val()).replace("$link", '<a class="secondary-text" href="'+$(this).val()+'" style="padding: 0;line-height: 0;height: 0;display: inline;color: #fff;">'+$(this).val()+'</a>'))
});

$("#admin-save-setting").click(function() { 
 	$(".admin-setting").each(function() {
        originalValue = $(this).data('value');
        newValue = $(this).find("input.admin-setting-text").val();
        if(originalValue != newValue)
        {
            socket.emit('saveSetting', $(this).attr('id'), newValue);
			$(this).data('value', newValue);
        }
	});
});

$("#admin-save-setting2").click(function() { 
 	$(".admin-setting2").each(function() {
        originalValue = $(this).data('value');
        newValue = $(this).find("input.admin-setting2-text").val();
        if(originalValue != newValue)
        {
            socket.emit('saveSetting', $(this).attr('id'), newValue);
			$(this).data('value', newValue);
        }
	});
});

var perms = { "": {} };
$(".admin-update-permission").click(function () {
    var rank = $(this).attr('id');
    perms = {
        "access": {}
    };
    $('input[data-id="' + rank + '"]').each(function () {
        var rank2 = $(this).attr('id')
        var perm = rank2.split('_')[1];
        perms[perm] = $(this).is(':checked').toString()
    });
    $('.admin-access-page').each(function () {
        if ($(this).attr('id').indexOf(rank) >= 0) {
            perms["access"][$(this).attr('id').split('-')[1]] = $('input#' + $(this).attr('id').replace("-", "_")).is(':checked').toString();
        }
    });
    socket.emit('updatePerm', rank, perms)
});

$("#admin-create-rank").click(function() {
  socket.emit('createRank', $('#admin-new-rank').val())
});