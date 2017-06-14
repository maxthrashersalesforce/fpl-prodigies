$('#home').on('click', function() {
	// alert(1);
});

$(document).ready(function() {
    $('#players').DataTable( {
    	"order": [[ 1, "desc" ]]
        ,"paging": false
        ,"scrollY": "600px"
        ,"info": false
        ,"stateSave": true
    });
} );