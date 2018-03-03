<html>
<?php
require_once("header.php");
?>
<body>
<div class="container-fluid" style="margin-top: 50px;">
    <div class="row">
        <div class="col-xs-12">
        <h3>Using FNTSY PL</h3>
        <p>FNTSY PL currently contains six different tools.</p>
        <ul>
            <li><b>Live Table:</b> The only live table tool currently available that updates within minutes! Input your Mini-League ID to get live updates of who is playing and their point totals during a gameweek. League ID can be found in the URL of the League page on FPL. Only the top 50 teams in the League are considered.</li>
            <ul>
                <li>Bookmark this page with your League ID!<br><a href="http://fntsypl.com/live?league=00000" target="_blank">http://fntsypl.com/live?league=00000</a></li>
                <li>Search for Player Names in order to filter on teams with a player!</li>
            </ul>
            <li><b>Selections:</b> Input your Mini-League ID to get a list of selected players for your league. League ID can be found in the URL of the League page on FPL. Compare your league ownership to overall ownership. Only the top 50 teams in the League are considered.</li>
            <ul><li>Bookmark this page with your League ID<br><a href="http://fntsypl.com/selections?league=00000" target="_blank">http://fntsypl.com/selections?league=00000</a></li><li>Defaults to the top 50 Overall FPL players.</li></ul>
            <li><b>Players:</b> A comprehensive list of statistics at the individual player level. These statistics come from the FPL API except for VAPM which is calculated here. The next two fixtures for each player are also displayed and formatted based on difficulty (according to FPL).</li>
            <li><b>Fixtures:</b> An outline of upcoming fixtures based on difficulty. The formatting is sourced by the underlying numbers used by FPL. Home and Away form is taken into consideration. The Overall number is an equally weighted average among the number of fixtures selected in the Gameweeks dropdown.</li>
            <li><b>My Team (BETA):</b> A team planner for the next four gameweeks. Click players to turn their background green in order to plan a starting lineup. Click again to turn red to plan a transfer. Click a third time to reset. Future plans included incorporating budget and transfers.</li>
            <ul><li>Bookmark this page with your Team ID!<br><a href="http://fntsypl.com/my?team=00000" target="_blank">http://fntsypl.com/my?team=00000</a></li></ul>
            <li><b>Teamsheets (BETA):</b> Projected teamsheets for each team, along with PPG. Disclaimer: this is not always completely up to date and is a work in progress for formatting.</li>
        </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h3>Feedback / Requests</h3>
            <p>Submit feed back below or get in touch with us on Twitter <a href="https://twitter.com/FNTSYPL">@FNTSYPL</a>.</p>
            <textarea class="form-control" id="feedback" rows="3"></textarea>
            <br>
            <input id="btn_feedback" type="button" class="btn btn-primary" value="Submit">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h3>Change Log</h3>
            <ul>
                <li><b>DEC.25.2017</b> : Bench Boost is now included in live total. Bug found by <a href="http://fmlfpl.com/" target="_blank">FML FPL</a>!</li>
                <li><b>DEC.23.2017</b> : Live Totals cleaned up. Bug found by <a href="https://www.reddit.com/user/Trustme_I_lie" target="_blank">/u/Trustme_I_lie</a> and <a href="https://www.reddit.com/user/Oggiva" target="_blank">/u/Oggiva</a>!</li>
                <li><b>DEC.12.2017</b> : Changed Transfer Cost to show # of Transfers instead. Request by Uncle Brian!</li>
                <li><b>DEC.12.2017</b> : Resolved Live Points sorting issues. Bug found by <a href="https://www.reddit.com/user/NorwegianHammerworks" target="_blank">/u/NorwegianHammerworks</a>!</li>
                <li><b>DEC.08.2017</b> : Added Captain to Live table. Request by <a href="https://www.reddit.com/user/Arcanium_TT" target="_blank">/u/Arcanium_TT</a> and <a href="https://www.reddit.com/user/beskaj" target="_blank">/u/beskaj</a>!</li>
                <li><b>DEC.02.2017</b> : Added Transfers to Live table. Does not work on mobile yet.</li>
                <li><b>DEC.02.2017</b> : Added Live Bonus to Live table. Request by <a href="https://www.reddit.com/user/xangto" target="_blank">/u/xangto</a>!</li>
                <li><b>DEC.01.2017</b> : Added Live Rank to Live table next to Live Points. Request by <a href="https://www.reddit.com/user/millssyyy" target="_blank">/u/millssyyy</a>!</li>
                <li><b>NOV.25.2017</b> : Added My Team BETA. Request by <a href="https://www.reddit.com/user/theequaliser72" target="_blank">/u/theequaliser72</a>!</li>
            </ul>
        </div>
    </div>
</div>
<script>
    $('#btn_feedback').click (function() {
        var msg = $('#feedback').val();
        var data = {msg: msg};

        $.post('feedback.php', data, function(resp) {
            $('#feedback').val('');
            alert('Thanks for the feedback!');
        });
    });
</script>
</body>
</html>