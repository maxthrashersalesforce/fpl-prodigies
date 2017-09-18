<?php require_once("header.php"); ?>
<body>
<div class="container-fluid" style="margin-top: 60px;">
    <div class="row">
        <div class="col-xs-12" style="padding: 0px 0 10px 0;">
            <div class="team-select">
                <label style="padding: 0px 0px 0 0;">Team</label>
                <select id="teams"></select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12" id="div_teamsheets">
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var team_id = gup('team_id', window.location.href);
        get_teamsheet(team_id)
    });

    $('body').on('change', '#teams', function() {
        var team_id = $('option:selected', this).attr('value');
        get_teamsheet(team_id)
    });

    function get_teamsheet(team_id) {
        var screen_height = $(window).width();
        console.log(screen_height);
        var data = {team_id: team_id, mode: 'teamsheets', screen_height: screen_height};

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            $('#div_teamsheets').html(j.BODY);
            $('#teams').html(j.OPTIONS);
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

</script>
</body>