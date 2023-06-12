<?php
include('lib/common.php');
include('lib/show_queries.php');

$ManufacturerName= $_GET['manufacName'];
$query = "SELECT T, count(*)  AS NumAppliance
FROM (SELECT email, 'Air handler' AS T FROM AirHandler
WHERE manufacturer_name ='$ManufacturerName'
UNION ALL
SELECT email, 'Water heater' AS T FROM WaterHeater
WHERE manufacturer_name ='$ManufacturerName'
UNION ALL
SELECT email, 'Air conditioner' AS T FROM AirHandler NATURAL JOIN AirConditioner
WHERE manufacturer_name ='$ManufacturerName'
UNION ALL
SELECT email, 'Heater' AS T FROM AirHandler NATURAL JOIN Heater
WHERE manufacturer_name ='$ManufacturerName'
UNION ALL
SELECT email, 'Heat pump' AS T FROM AirHandler NATURAL JOIN Heatpump
WHERE manufacturer_name ='$ManufacturerName') AS L
GROUP BY T;";
$result = mysqli_query($db, $query);
?>

<!DOCTYPE html>  <!-- HTML 5 -->
<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset="UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png"/>
    <title>Manufacturer drilldown report</title>
</head>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-size:24px;
}
</style>
<body> 
    <h1><?php echo $ManufacturerName;?></h1>
    <div>
        <form name="add_appliance" action="add_appliance.php" method="post">

        <table>
            <tr>
                <th style="width:250px; text-align: left" >Appliance Type</th>
                <th style="width:250px; text-align: left"># of appliances</th>
  
            </tr>
            <!-- PHP CODE TO FETCH DATA FROM ROWS -->
            <?php
                // LOOP TILL END OF DATA
                while($rows=$result->fetch_assoc())
                {
            ?>
            <tr>
                <!-- FETCHING DATA FROM EACH
                    ROW OF EVERY COLUMN -->
                <td><?php echo $rows['T'];?></td>
                <td><?php echo $rows['NumAppliance'];?></td>
                
            </tr>
            <?php
                }
            ?>
        </table>
        </form>

    </div>
</body> 




</html>