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
            <a title="Enter your FPL League ID in the text box and press the Get button to retrieve your League. Your League ID can be found by going to the League page of the FPL site and copying the number between 'standings/' and '/classic' in the URL.<br><br>You can bookmark this page as fntsypl.com/live?league=00000 where the zeros are your League ID." data-html="true" data-toggle="tooltip" data-placement="bottom">
                <input id="league" class="form-control" placeholder="League ID">
            </a>
        </div>
        <div class="col-xs-3">
            <input id="get_livetable" type="button" class="btn btn-primary" value="Get">
        </div>
<!--        <div class="col-xs-2">-->
<!--            <div class="dropdown">-->
<!--                <button class="btn btn-default dropdown-toggle" type="button" id="dropdown_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">-->
<!--                    Quick-League Select-->
<!--                    <span class="caret"></span>-->
<!--                </button>-->
<!--                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">-->
<!--                    <li><a href="#" class="quick_league" id="313">Top 50 Overall</a></li>-->
<!--                    <li><a href="#" class="quick_league" id="25763">FML FPL</a></li>-->
<!--                    <li><a href="#" class="quick_league" id="433">Just Offside</a></li>-->
<!--                </ul>-->
<!--            </div>-->
<!--        </div>-->
    </div>
    <div class="row"></p></div>

    <div class="col-xs-12 table-responsive" id="div_live_table" ></div>
<!--    <div class="col-xs-12">Sorry folks - busy week for me and not going to be able to get the first matches for WHU and TOT sorted. Everything should be back smooth for next week, cheers.</div>-->
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

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('.quick_league').click(function() {
        var quick_league = $(this).attr('id');
        get_live_table(quick_league);
        $('#dropdown_button').html($(this).text() + " <span class=\"caret\"></span>");
    });

//    $(document).on('click', '.transfers', function(e) {
//        var obj = $(this);
//        var entry = obj.data('entry');
//        var data = {entry: entry, mode: 'transfers'};
//        $.post('data/from_db.php', data, function(resp) {
//            var j = JSON.parse(resp);
//
//            obj.popover({
//                trigger: 'focus',
//                placement: 'left',
//                html: 'true',
//                container: 'body',
//                toggle: 'popover',
//                title: '<b>Transfers</b>',
//                content: j.BODY
//            });
//
//            obj.popover('show');
//            $('[data-toggle="popover"]').popover();
//        });
//
//    });

    function get_live_table(league_id) {
        var data = {league: league_id, mode: 'test'};
        var div = $('#div_live_table');
        $('.popover').hide();
        div.html('<center><b>Loading your live table... looks like you are still in last place.</b><br><br><img src="i/roll.gif" alt="ball"></center>');

        $.post('data/from_db.php', data, function(resp) {
            var j = JSON.parse(resp);
            div.html(j.BODY);
            $('#league').val(j.LEAGUE);
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
            get_transfer__();

        });
    }

    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            }
        });
    });

    function get_transfer__() {
        var table = $('#live_table');
        $('.entry_row').each(function() {
            var entry = $(this).attr('entry');
            var data = {entry: entry, mode: 'transfers'};
            $.post('data/from_db.php', data, function(resp) {
                var j = JSON.parse(resp);
                var transfer_popover = $('#transfers_' + entry);
                transfer_popover.attr('data-content', j.BODY);
                transfer_popover.attr('title', j.COST);
            });
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

    $("#league").keyup(function(event) {
        if (event.keyCode === 13) {
            $("#get_livetable").click();
            $(this).blur();
        }
    });

</script>
</body>