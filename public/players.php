<html>
<?php
    require_once("header.php");
?>
<body>

<div class="container-fluid" style="margin-top: 60px;">
<!--    <div class="col-sm-2 col-xs-12" id="filters">-->
<!--        <input id="search" class="search form-control" placeholder="Search...">-->
<!--        <ul class="filters">-->
<!--            <li>-->
<!--                <input id="1" type="checkbox" class="position-checkbox" checked/>-->
<!--                <label>GK</label>-->
<!--            </li>-->
<!--            <li>-->
<!--                <input id="2" type="checkbox" class="position-checkbox" checked/>-->
<!--                <label>DEF</label>-->
<!--            </li>-->
<!--            <li>-->
<!--                <input id="3" type="checkbox" class="position-checkbox" checked/>-->
<!--                <label>MID</label>-->
<!--            </li>-->

<!--            <li>-->
<!--                <input id="4" type="checkbox" class="position-checkbox" checked/>-->
<!--                <label>FWD</label>-->
<!--            </li>-->
<!--        </ul>-->
<!--    </div>-->
    <div class="row" style="padding: 0 0 5px 0;">
        <div class="col-xs-12">
            <input id="search" class="search form-control" placeholder="Search..." style="">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 table-responsive" id="player">

            <table id="players" class="table table-striped">
                <?php echo players_table(); ?>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var table = $('#players').DataTable( {
            "order": [[ 3, "desc" ]]
            ,"paging": false
            //,"scrollY": "95%"
            //,"scrollX": "auto"
            ,"info": false
            ,"stateSave": true
            ,"compact": false
            ,"dom": 'ltipr'
            ,"searching": true
        });

        // #myInput is a <input type="text"> element
        $('#search').on( 'keyup', function () {
            table.search( this.value ).draw();
        } );

//        $('.position-checkbox').change(function() {
//            if(this.checked) {
//                var $rowsNo = $('#players tbody tr').filter(function () {
//                    return $.trim($(this).find('td').eq(7).text()) === "GK"
//                }).toggle();
//            } else {
//                // $('#players').html(<?// echo players_table("2,3,4"); ?>//)
//            }
//        });
    });



</script>
</body>


</html>