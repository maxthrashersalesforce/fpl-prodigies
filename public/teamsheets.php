<?php
require_once("header.php");
?>
<body>
<div class="container-fluid" style="margin-top: 60px;">
    <div class="col-xs-12" id="div_teamsheets">
    </div>
</div>
<script>
    $(document).ready(function() {
        var team_id = 10;
        var data = {team_id: team_id, mode: 'teamsheets'};

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            $('#div_teamsheets').html(j.BODY);
        });
    });
</script>
</body>