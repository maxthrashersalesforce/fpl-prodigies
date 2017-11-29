<?php
require_once("header.php");
?>
<body>
<div class="container-fluid" style="margin-top: 60px;">
    <div class="row">
        <div class="col-xs-5">
            <input id="search" class="search form-control" placeholder="Search...">
        </div>
        <div class="col-xs-4">
            <input id="player_id" class="form-control" placeholder="Player ID">
        </div>
        <div class="col-xs-3">
            <input id="get_player" type="button" class="btn btn-primary" value="Get">
        </div>
    </div>
    <div class="row"></p></div>
    <div class="col-xs-12 table-responsive" id="div_player" ></div>
</div>
<script>
    $(document).ready(function() {
        var player_id = gup('player', window.location.href);
        get_player(player_id);
    });

    $('#get_player').click(function() {
        var player_id = $('#player_id').val();
        get_player(player_id);
    });

    function get_player(player_id) {
        var data = {player: player_id, mode: 'weather'};
        var div = $('#div_player');
        div.html('<center><b>Loading the player\'s performance...</b><br><br><img src="i/roll.gif" alt="ball"></center>');

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            div.html(j.BODY);

            var table = $('#player_table').DataTable( {
                "order": [[1, "desc"]]
                ,"paging": false
                ,"info": false
                ,"compact": true
                ,"dom": 'ltipr'
                ,"searching": true
            });

            $('#search').on( 'keyup', function () {
                table.search( this.value ).draw();
            } );

        });
    }

    function gup( name, url ) {
        if (!url) url = location.href;
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( url );
        return results == null ? null : results[1];
    }

    $("#league").keyup(function(event) {
        if (event.keyCode === 13) {
            $("#get_player").click();
            $(this).blur();
        }
    });

</script>
</body>