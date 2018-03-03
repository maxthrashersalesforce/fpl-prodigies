<html>
<?php
require_once("header.php");
?>
<body>

<div class="container-fluid" style="margin-top: 60px;">
    <div class="row" style="padding: 0 0 5px 0;">
        <div class="col-xs-12 col-lg-7 col-md-7">
            <input id="search" class="search form-control" placeholder="Search..." style="">
        </div>
        <div class="col-xs-4">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdown_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Gameweek <?php echo CURRENT_GW; ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="#" class="phase" id="1">Gameweek <?php echo CURRENT_GW; ?></a></li>
                    <li><a href="#" class="phase" id="7">January</a></li>
                    <li><a href="#" class="phase" id="6">December</a></li>
                    <li><a href="#" class="phase" id="5">November</a></li>
                    <li><a href="#" class="phase" id="4">October</a></li>
                    <li><a href="#" class="phase" id="3">September</a></li>
                    <li><a href="#" class="phase" id="2">August</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 table-responsive" id="jo_table"></div>
    </div>
    <div class="row">
        <div class="col-xs-12 table-responsive" id="league_table_div"></div>
    </div>
</div>
<script>

    $(document).ready(function() {
        get_players(1);
    });

    $('.phase').click(function() {
        var phase = $(this).attr('id');
        get_players(phase);
        $('#dropdown_button').html($(this).text() + " <span class=\"caret\"></span>");
    });

    function get_players(phase) {
        var data = {mode: 'justoffside', phase: phase};
        $('#league_table_div').html('<center><b>Loading the results...</b><br><br><img src="i/roll.gif" alt="ball"></center>');

        $.post('data/from_db.php', data, function(resp) {
            var jo_table = $('#jo_table');
            var league_table = $('#league_table_div');
            var j = JSON.parse(resp);
            jo_table.html(j.JO);
            league_table.html(j.LEAGUE);

            var dataTable = $('#total_league_table').DataTable({
                "paging": false
                , "info": false
                , "stateSave": true
                , "compact": false
                , "dom": 'ltipr'
                , "searching": true
                , "destroy": true
            });

            $('#search').on( 'keyup', function () {
                dataTable.search( this.value ).draw();
            });
        });
    }
</script>
</body>
</html>