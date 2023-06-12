<?php
include('lib/common.php');
include('lib/show_queries.php');
include('config/database.php');
?>

<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <title>AlternaKraft Done</title>

    <style>
        .button {
            background-color: #0080ff;
            border: none;
            width: 160px;
            color: white;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            font-size: 24px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <div class="progressbar-wrapper">
        <ul class="progressbar">
            <li>Household Info</li>
            <li>Appliances</li>
            <li>Power generation</li>
            <li class="active">Done</li>
        </ul>
    </div>

    <h1>Submission complete!</h1>
    <p style="font-size:24px">Thank you for providing your information to Alternakraft!
    <p>
    <p style="font-size:22px; position: relative; left: 0px"><a href="welcome.php">Return to the main menu</a></p>