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
        <div class="col-xs-12 col-lg-4 col-md-4" style="margin-left: 10px; display: inline-block;">
            <input id="1" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="1">GK</label>
            <input id="2" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="2">DEF</label>
            <input id="3" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="3">MID</label>
            <input id="4" type="checkbox" class="position-checkbox" name="position-checkbox" checked/>
            <label for="4">FWD</label>
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
        get_players();

        $('.position-checkbox').change(function() {
            var position = $(this).attr('id');
            if (this.checked) {
                $('tr[position=' + position + ']').show();
            } else {
                $('tr[position=' + position + ']').hide();
            }
        });
    });

    function get_players() {
        var positions = new Array();
        $("input:checkbox[name=position-checkbox]:checked").each(function(){
            positions.push($(this).attr('id'));
        });

        var data = {mode: 'players', positions: positions};

        $.post('data/from_db.php', data, function(resp) {
            var table = $('#players');
            var j = JSON.parse(resp);
            table.html(j.BODY);

            var dataTable = table.DataTable({
                "order": [3, "desc"]
                , "paging": false
                //,"scrollY": "95%"
                //,"scrollX": "auto"
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