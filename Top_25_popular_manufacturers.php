<?php
include('lib/common.php');
include('lib/show_queries.php');


$query = "SELECT manufacturer_name, count(*) AS NumAppliance
FROM ( SELECT manufacturer_name, ' Air handler ' FROM AirHandler
UNION ALL
SELECT manufacturer_name, ' Water heater ' FROM WaterHeater ) AS LIST 
GROUP BY manufacturer_name
ORDER BY NumAppliance DESC Limit 25;";

$result = mysqli_query($db, $query);

?>



<!DOCTYPE html>  <!-- HTML 5 -->
<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset="UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png"/>
    <title>AlternaKraft Top 25 popular manufacturer</title>
</head>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-size:24px;
}

</style>

<body> 
    <h1>Top 25 popular manufactures</h1>
    <div>
            <table>
                <tr>
                    <th style="width:250px; text-align: left" >Manufacturer</th>
                    <th style="width:250px; text-align: left"># of appliances</th>
                    <th style="width:150px; text-align: left"></th>
                </tr>
                <!-- PHP CODE TO FETCH DATA FROM ROWS -->
                <?php
                $NumofRow=0;
                while($rows=$result->fetch_assoc())
                {
                ?>
                <tr>
                <!-- FETCHING DATA FROM EACH
                    ROW OF EVERY COLUMN -->
                <td><?php echo $rows['manufacturer_name'];?></td>
                <td><?php echo $rows['NumAppliance'];?></td>
                <td> 
                    <a href='Manufacturer_report.php?manufacName=<?php echo $rows['manufacturer_name'];?>' target="_blank">show report</a>
                </td>
                </tr>
                <?php
                $NumofRow++;
                }
                ?>
            </table>    

        <?php
        if (mysqli_num_rows($result) < 25){
            print "<li>Only {$NumofRow} manufacturers of appliances are in the database</li>";
        }
        ?>

    </div>
</body> 

</html>
