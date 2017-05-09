$('input#submitbryce').on('click', function() {
	alert(1);
});

$(document).ready(function() {
    $('#players').DataTable( {
    	"order": [[ 1, "desc" ]]
    });
} );