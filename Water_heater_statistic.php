<?php
include('lib/common.php');
include('lib/show_queries.php');
?>

<!DOCTYPE html>  <!-- HTML 5 -->
<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset="UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png"/>
    <title>AlternaKraft Water Heater Statistics by State</title>
</head>

<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>

<body>  
    <div>
    <h1>View Water Heater Statistics</h1>
        <table>
            <tr>
                <th style="font-size:20px" rowspan='2'>State</th>
                <th style="font-size:20px" rowspan='2'>Average Water Heater Capacity</th>
                <th style="font-size:20px" rowspan='2'>Average Water Heater BTUs</th>
                <th style="font-size:20px" rowspan='2'>Average Water Heater Temperature Setting</th>
                <th style="font-size:20px" colspan='2'>The Count of Water Heater</th>
            </tr>
            <tr>
                <th style="font-size:16px">With Temperature Setting Provided</th>
                <th style="font-size:16px">With No Temperature Setting Provided</th>
            </tr>

            <?php								
                $query = "SELECT state_abre, ROUND(avg(capacity),0) AS avgcapacity, ROUND(avg(BTU_rating),0) AS avgBTU, ROUND(avg(temperature_setting),1) AS avgtemp, count(temperature_setting), (count(*)-count(temperature_setting)) " .
                         "FROM (Household NATURAL JOIN Location) NATURAL JOIN WaterHeater " . //这里state需要加DISTINCT吗
                         "GROUP BY state_abre " .
                         "ORDER BY state_abre ASC";
                     
                $result1 = mysqli_query($db, $query);
            
                while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
                    $state = $row['state_abre'];
                    $link = "Water_heater_statistic_state.php?state_abre=$state";
                    print "<tr>";
                    print "<td><a href=\"$link\" target=\"_blank\">{$row['state_abre']}</a></td>";
                    print "<td>{$row['avgcapacity']}</td>";
                    print "<td>{$row['avgBTU']}</td>";
                    print "<td>{$row['avgtemp']}</td>";
                    print "<td>{$row['count(temperature_setting)']}</td>";
                    print "<td>{$row['(count(*)-count(temperature_setting))']}</td>";
                    print "</tr>";							
                }
                
                $query = "SELECT DISTINCT(state_abre) " . 
                         "FROM Location " .
                         "WHERE state_abre NOT IN " .
                         "(SELECT state_abre " .
                         "FROM (Household NATURAL JOIN Location) NATURAL JOIN WaterHeater)";
                
                $result2 = mysqli_query($db, $query);

                while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)){
                    $state = $row['state_abre'];
                    $link = "Water_heater_statistic_state.php?state_abre=$state";
                    print "<tr>";
                    print "<td><a href=\"$link\" target=\"_blank\">{$row['state_abre']}</a></td>";
                    print "<td>0</td>";
                    print "<td>0</td>";
                    print "<td>0</td>";
                    print "<td>0</td>";
                    print "<td>0</td>";
                    print "</tr>";							
                }
                
            ?>
        </table>
    </form>
    </div> 

</body>
</html>