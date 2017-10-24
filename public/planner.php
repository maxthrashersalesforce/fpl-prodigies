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
            <input id="league" class="form-control" placeholder="League ID">
        </div>
        <div class="col-xs-3">
            <input id="get_team" type="button" class="btn btn-primary" value="Get">
        </div>
    </div>
    <div class="row"></p></div>
    <div class="col-xs-12 table-responsive" id="div_team" ></div>
</div>
<script>
    $(document).ready(function() {
        var team = gup('team', window.location.href);
        get_team(team)
    });

    $('#get_team').click(function() {
        var league_id = $('#team').val();
        get_team(league_id);
    });

    function get_team(team) {
        var data = {league: team, mode: 'team'};
        var div = $('#div_team');
        div.html('Loading...');

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            div.html(j.BODY);
            $('[data-toggle="popover"]').popover();

            var table = $('#team').DataTable( {
                "order": [[2, "desc"]]
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

    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            }
        });
    });

    // TODO: move this to a base js script
    function gup( name, url ) {
        if (!url) url = location.href;
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( url );
        return results == null ? null : results[1];
    }

</script>
</body>