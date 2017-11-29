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
            <input id="get_livetable" type="button" class="btn btn-primary" value="Get">
        </div>
    </div>
    <div class="row"></p></div>
    <div class="col-xs-12 table-responsive" id="div_live_table" ></div>
</div>
<script>
    $(document).ready(function() {
        var league_id = gup('league', window.location.href);
        get_live_table(league_id);
    });

    $('#get_livetable').click(function() {
        var league_id = $('#league').val();
        get_live_table(league_id);
    });

    function get_live_table(league_id) {
        var data = {league: league_id, mode: 'test'};
        var div = $('#div_live_table');
        div.html('<center><b>Loading your live table... looks like you are still in last place.</b><br><br><img src="i/roll.gif" alt="ball"></center>');

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            div.html(j.BODY);
            $('[data-toggle="popover"]').popover();

            var table = $('#live_table').DataTable( {
                "order": [[7, "desc"]]
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
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            }
        });
    });


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
            $("#get_livetable").click();
            $(this).blur();
        }
    });

</script>
</body>