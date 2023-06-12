<?php
include('lib/common.php');
include('lib/show_queries.php');
include('config/database.php');
?>

<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png" />
    <title>AlternaKraft Welcome</title>
    <style>
        a:link {
            color: blue;
            background-color: transparent;
            text-decoration: underline;
        }

        a:visited {
            color: red;
            background-color: transparent;
            text-decoration: underline;
        }
    </style>

</head>

<body>
    <div id="main_container">
        <div id="header">
            <h1>Welcome to Alternakraft!</h1>
        </div>

        <div class="center_content">
            <h2>Please choose what you'd like to do:</h2>
            <p style="font-size:22px"><a href="enter_household.php">Enter my household info</a></p>
            <p style="font-size:22px"><a href="view_reports.php">View reports/query data</a></p>
        </div>
</body>

</html>