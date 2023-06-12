<?php
include('lib/common.php');
include('lib/show_queries.php');


    $state_abre = $_GET['state_abre'];

    $query = "SELECT state_abre, energy_source, ROUND(min(capacity),0) AS mincapacity, ROUND(avg(capacity),0) as avgcapacity, ROUND(max(capacity),0) AS maxcapacity, ROUND(min(temperature_setting),1) AS mintemp, ROUND(avg(temperature_setting),1) AS avgtemp, ROUND(max(temperature_setting),1) AS maxtemp " . 
                "FROM Household AS H, Location AS L, WaterHeater AS W " .
                "WHERE H.postal_code=L.postal_code AND H.email=W.email AND L.state_abre ='$state_abre' " .
                "GROUP BY energy_source " .
                "ORDER BY energy_source ASC ";
                 
    $result1 = mysqli_query($db, $query);

    $query = "SELECT DISTINCT(state_abre) " . 
                "FROM Location " .
                "WHERE state_abre = '$state_abre' AND state_abre NOT IN " .
                "(SELECT state_abre " .
                "FROM (Household NATURAL JOIN Location) NATURAL JOIN WaterHeater)";
    
    $result2 = mysqli_query($db, $query);
?>


<!DOCTYPE html>  <!-- HTML 5 -->
<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset="UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png"/>
    <title>AlternaKraft Water Heater Statistics</title>
</head>

<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>

<body>
    <?php
        print "<h1>{$state_abre}</h1>";	
        print "<table>";
        print "<tr>";
                print "<th>energy_source</th>";
                print "<th>min(capacity)</th>";
                print "<th>avg(capacity)</th>";
                print "<th>max(capacity)</th>";
                print "<th>min(temperature_setting)</th>";
                print "<th>avg(temperature_setting)</th>";
                print "<th>max(temperature_setting)</th>";
        print "</tr>";		
        
        while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
            print "<tr>";
                print "<td>{$row['energy_source']}</td>";
                print "<td>{$row['mincapacity']}</td>";
                print "<td>{$row['avgcapacity']}</td>";
                print "<td>{$row['maxcapacity']}</td>";
                print "<td>{$row['mintemp']}</td>";
                print "<td>{$row['avgtemp']}</td>";
                print "<td>{$row['maxtemp']}</td>";
            print "</tr>";		
        }

        while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)){
            print "<tr>";
                print "<td>0</td>";
                print "<td>0</td>";
                print "<td>0</td>";
                print "<td>0</td>";
                print "<td>0</td>";
                print "<td>0</td>";
                print "<td>0</td>";
            print "</tr>";							
        }

        print "</table>";	
    ?>
</body>
</html>
				