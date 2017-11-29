<?php
require_once("header.php");
?>
<body>
<div class="container-fluid" style="margin-top: 60px;">

<!---->
<!--    <div class="wrapper-action-bar">-->
<!--        <div style="grid-column: 1 / 7; grid-row: 1;">-->
<!--            <input id="team" class="form-control" placeholder="Team ID">-->
<!--        </div>-->
<!--        <div style="grid-column: 7; grid-row: 1; text-align: center;">-->
<!--            <a title="Enter your FPL Team ID in the text box and press the Get button to retreive your team. Your Team ID can be found by going to the Points tab of the FPL site and copying the number between 'team/' and '/event' in the URL." data-html="true" data-toggle="tooltip" data-placement="bottom" ><img src="i/q.png" alt="Help" style="width: 20%"></a>-->
<!--        </div>-->
<!--        <div style="grid-column: 8; grid-row: 1; text-align: center;">-->
<!--            <input id="get_team" type="button" class="btn btn-primary" value="Get">-->
<!--        </div>-->
<!--    </div>-->

    <div class="row">
        <div class="col-xs-9 col-md-9 col-lg-9">
            <a title="Enter your FPL Team ID in the text box and press the Get button to retrieve your team. Your Team ID can be found by going to the Points tab of the FPL site and copying the number between 'team/' and '/event' in the URL.<br><br>You can bookmark this page as fntsypl.com/my?team=00000 where the zeros are your Team ID.<br><br>Click a player to highlight him green to help plan your weekly lineup. Click again to turn red to plan transfers out. Click a third time to remove highlighting." data-html="true" data-toggle="tooltip" data-placement="bottom">
                <input id="team" class="form-control" placeholder="Team ID">
            </a>
        </div>
        <div class="col-xs-3 col-md-3 col-lg-3">
<!--            -->
            <input id="get_team" type="button" class="btn btn-primary" value="Get">
        </div>
    </div>
    <div class="row"></p></div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-team" id="div_team"></div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-team" id="div_team2"></div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-team" id="div_team3"></div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-team" id="div_team4"></div>
</div>
<script>
    $(document).ready(function() {
        var team_id = gup('team', window.location.href);
        get_team(team_id);
    });

    $('#get_team').click(function() {
        var team_id = $('#team').val();
        get_team(team_id);
    });

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });

    function get_team(team) {
        var data = {team: team, mode: 'team'};
        var div = $('#div_team');
        var div2 = $('#div_team2');
        var div3 = $('#div_team3');
        var div4 = $('#div_team4');
        div.html('<center><b>Loading your team... great choice on Rondón.</b><br><br><img src="i/roll.gif" alt="ball"></center>');
        div2.html();
        div3.html();
        div4.html();

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            div.html(j.BODY[0]);
            div2.html(j.BODY[1]);
            div3.html(j.BODY[2]);
            div4.html(j.BODY[3]);
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

    $("#team").keyup(function(event) {
        if (event.keyCode === 13) {
            $("#get_team").click();
            $(this).blur();
        }
    });

    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            }
        });
    });

    $('.div-team').on('click', '.my-player-wrapper', function() {
        var start = '#00FF87';
        var out = '#ff3333';
        var current = $(this).attr('status');

        if (current === start) {
            $(this).css('background-color', out);
            $(this).attr('status', out)
        } else if (current === out) {
            $(this).css('background-color', '');
            $(this).attr('status', '')
        } else {
            $(this).css('background-color', start);
            $(this).attr('status', start)
        }
    });
</script>
</body>