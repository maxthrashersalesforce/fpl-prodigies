<html>
<?php
require_once("header.php");
?>
<body>

<div class="container-fluid" style="margin-top: 60px;">
    <div class="row" style="padding: 0 0 5px 0;">
        <div class="col-xs-12 col-lg-6 col-md-6">
            <input id="search" class="search form-control" placeholder="Search..." style="">
        </div>
        <div class="col-xs-12 col-lg-5 col-md-5" style="margin-left: 10px; display: inline-block;">
            <input id="1" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="1">GK</label>
            <input id="2" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="2">DEF</label>
            <input id="3" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="3">MID</label>
            <input id="4" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="4">FWD</label>
            <input id="mins_cb" type="checkbox" class="mins-checkbox" name="mins-checkbox" checked/>
            <label for="mins_cb">Mins > 850</label>
            <input id="form_cb" type="checkbox" class="form-checkbox" name="form-checkbox" checked/>
            <label for="form_cb">Form > 2</label>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 table-responsive" id="player">
            <table id="players" class="table table-striped table-sm"></table>
        </div>
    </div>
</div>
<script>

    $(document).ready(function() {
        get_players('true', 'true');

//        $('.position-checkbox').change(function() {
//            var position = $(this).attr('id');
//            if (this.checked) {
//                $('tr[position=' + position + ']').show();
//            } else {
//                $('tr[position=' + position + ']').hide();
//            }
//        });
    });

    $('#mins_cb').change(function() {
        var set_min = $(this).is(':checked');
        var set_form = $('#form_cb').is(':checked');
        get_players(set_min, set_form);
    });

    $('.position-checkbox').change(function() {
        var set_min = $('#mins_cb').is(':checked');
        var set_form = $('#form_cb').is(':checked');
        get_players(set_min, set_form);
    });

    $('#form_cb').change(function() {
        var set_min = $('#mins_cb').is(':checked');
        var set_form = $(this).is(':checked');
        get_players(set_min, set_form);
    });

    function get_players(set_min, set_form) {
        var positions = new Array();
        var table = $('#players');
        table.html('<center><br><br><img src="i/roll.gif" alt="ball"></center>');

        $("input:checkbox[name=position-checkbox]:checked").each(function(){
            positions.push($(this).attr('id'));
        });

        var data = {mode: 'players', positions: positions, set_min: set_min, set_form: set_form};

        $.post('data/from_db.php', data, function(resp) {

            var j = JSON.parse(resp);
            table.html(j.BODY);

            var dataTable = table.DataTable({
                "order": [[2, "desc"]]
                , "paging": false
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