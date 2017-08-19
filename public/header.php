<head>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/fixedColumns.dataTables.min.css" rel="stylesheet">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FNTSY PL</title>

    <?php
    require_once("db/db.php");
    require_once("data/from_db.php");
    ?>
</head>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container navcontainer">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" id="home" href="https://fantasy.premierleague.com/" target="_blank">FNTSY PL</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
<!--                <li><input id="search" class="search form-control" placeholder="Search..." style="background: transparent; border: none; margin-top: 8px;"></li>-->
                <li><a href="./blog">Analysis</a></li>
                <li><a href="./players">Players</a></li>
                <li><a href="./fixtures">Fixtures</a></li>
                <li><a href="./teamsheets">Teamsheets</a></li>
            </ul>
        </div>
    </div>
</nav>
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/test.js"></script>
<script type="text/javascript" src="js/dt.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/dataTables.fixedColumns.min.js"></script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-104523463-1', 'auto');
    ga('send', 'pageview');

</script>

