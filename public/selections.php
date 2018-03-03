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
            <input id="get_selections" type="button" class="btn btn-primary" value="Get">
        </div>
    </div>
    <div class="row"></p></div>
    <div class="col-xs-12 table-responsive" id="div_selections" ></div>
</div>
<script>
    $(document).ready(function() {
        var league_id = gup('league', window.location.href);
        get_selections(league_id)
    });

    $('#get_selections').click(function() {
        var league_id = $('#league').val();
        get_selections(league_id);
    });

    function get_selections(league_id) {
        var data = {league: league_id, mode: 'selections'};
        var div = $('#div_selections');
        $('.popover').hide();
        div.html('<center><b>Loading your league selections... who\'s the genius with Xhaka?</b><br><br><img src="i/roll.gif" alt="ball"></center>');

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            div.html(j.BODY);
            $('#league').val(j.LEAGUE);
            $('[data-toggle="popover"]').popover();

            var table = $('#selections').DataTable( {
                "order": [[3, "desc"]]
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
            $("#get_selections").click();
            $(this).blur();
        }
    });

</script>
</body>