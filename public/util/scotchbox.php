<?php 
    require_once("header.php");
    require_once("db/db.php");
    require_once("data/from_db.php");
?>
<main>     
    <div class="container theme-showcase" role="main">
        <ul class="nav nav-tabs">
            <li>
                <a data-toggle="tab" href="#standings">Standings</a>
            </li>
            <li>
                <a data-toggle="tab" href="#power">Power Rankings</a>
            </li>
            <li class="active">
                <a data-toggle="tab" href="#player">Players</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="standings" class="tab-pane fade">
                <table class="table table-striped">
                    <?php echo standings_table('270578'); ?>
                </table>
            </div>
            <div id="power" class="tab-pane fade">
                <table id="power" class="table table-striped">
                    <?php echo power_table(270578); ?>
                </table>
            </div>
            <div id="player" class="tab-pane fade in active">
                <table id="players" class="table table-striped">
                    <?php echo players_table(); ?>
                </table>
            </div>
        </div>
    </div>
</main>