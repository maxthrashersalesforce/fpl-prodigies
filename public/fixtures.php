<?php
    require_once("header.php");
?>
<body>
<div class="container-fluid" style="margin-top: 60px;">
    <div class="row">
        <div class="col-xs-8">
            <input id="search" class="search form-control" placeholder="Search...">
        </div>
        <div class="col-xs-4" style="padding: 5px 0 10px 0;">
            <label style="padding: 0px 10px 0 0;">GW's</label>
            <select id="gameweeks" >
                <option value="1">1</option>
                <option value="3">3</option>
                <option selected value="5">5</option>
                <option value="10">10</option>
                <option value="35">35</option>
            </select>
        </div>
    </div>
    <div class="col-xs-12 table-responsive" id="div_fixtures" >
    </div>
</div>
<script>
    $(document).ready(function() {

        var gw = 5;
        var data = {gameweeks: gw, mode: 'fixtures'};

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            $('#div_fixtures').html(j.TABLE);

            var table = $('#fixtures').DataTable( {
                "order": [[ 1, "asc" ]]
                ,"paging": false
                //,"scrollY": "95%"
                ,"info": false
                ,"stateSave": true
                ,"compact": true
                ,"dom": 'ltipr'
                ,"searching": true
                , fixedColumns: {
                    leftColumns: 2
                }
            });

            // #myInput is a <input type="text"> element
            $('#search').on( 'keyup', function () {
                table.search( this.value ).draw();
            } );
        });

    } );

    $('#gameweeks').change(function() {
        var gw = this.value;
        var data = {gameweeks: gw, mode: 'fixtures'};

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            $('#div_fixtures').html(j.TABLE);

            var table = $('#fixtures').DataTable( {
                "order": [[ 1, "asc" ]]
                ,"paging": false
                //,"scrollX": true
                ,"info": false
                ,"stateSave": true
                ,"compact": true
                ,"dom": 'ltipr'
                ,"searching": true
                , fixedColumns: {
                    leftColumns: 2
                }
            });

            // #myInput is a <input type="text"> element
            $('#search').on( 'keyup', function () {
                table.search( this.value ).draw();
            } );
        });


    });


</script>
</body>