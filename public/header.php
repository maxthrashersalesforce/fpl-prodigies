<head>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/fixedColumns.dataTables.min.css" rel="stylesheet">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FNTSY PL</title>
    <link rel="apple-touch-icon" sizes="180x180" href="i/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="i/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="i/favicon-16x16.png">
    <link rel="manifest" href="i/manifest.json">
    <link rel="mask-icon" href="i/safari-pinned-tab.svg" color="#41488c">
    <meta name="theme-color" content="#ffffff">

    <?php
    require_once("db/db.php");
    require_once("data/from_db.php");
    ?>

    <script>
        window.heap=window.heap||[],heap.load=function(e,t){window.heap.appid=e,window.heap.config=t=t||{};var r=t.forceSSL||"https:"===document.location.protocol,a=document.createElement("script");a.type="text/javascript",a.async=!0,a.src=(r?"https:":"http:")+"//cdn.heapanalytics.com/js/heap-"+e+".js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n);for(var o=function(e){return function(){heap.push([e].concat(Array.prototype.slice.call(arguments,0)))}},p=["addEventProperties","addUserProperties","clearEventProperties","identify","removeEventProperty","setEventProperties","track","unsetEventProperty"],c=0;c<p.length;c++)heap[p[c]]=o(p[c])};
        heap.load("3528340159");

        heap.track('Load', {test: 1});
    </script>
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
            <a class="navbar-brand" id="home" href="./">FNTSY PL</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
<!--                <li><input id="search" class="search form-control" placeholder="Search..." style="background: transparent; border: none; margin-top: 8px;"></li>-->
<!--                <li><a href="./blog">Analysis</a></li>-->
                <li><a href="./live">Live Table</a></li>
                <li><a href="./selections">Selections</a></li>
                <li><a href="./players">Players</a></li>
                <li><a href="./fixtures">Fixtures</a></li>
                <li><a href="./my">My Team</a></li>
                <li><a href="./teamsheets">Teamsheets</a></li>
                <li><a href="./">Help</a></li>
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


