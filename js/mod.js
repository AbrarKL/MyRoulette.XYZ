$('#searchUserMod').on('input',function(e){ 
	var searchFor = $(this).val();
    $( ".person" ).each(function( index ) {
    	 var name = $(this).attr('data-title').toLowerCase();
		 if (name.indexOf(searchFor.toLowerCase()) >= 0) {
             $(this).css('display', 'inline-block')
         } else {
             $(this).css('display', 'none')
         }
    });
});



$("#permissionApply").click(function() {
    socket.emit('mute', $('meta[name="targetSteamID"]').attr('content'), $('input#mute').is(':checked'))
});
$("#saveSelRank").click(function() {
    socket.emit('selectRank', $('meta[name="targetSteamID"]').attr('content'), $('#mod-rank-select').find(":selected").text());
});