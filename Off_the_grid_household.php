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
    <title>Off the Grid Household</title>
</head>

<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>

<body>  
    <div>
    <h1>Off-the-grid Household Dashboard</h1>
        <table>
            <tr>
                <th style="text-align: center">State with the Most Off-the-Grid Households</th>
                <th style="text-align: center">Number of off-the-grid Household</th>
                <th style="text-align: center">Number of all Household</th>
            <tr>
            <?php								
                $query = "SELECT state_abre, (count(DISTINCT(H.email))-count(DISTINCT(U.email))) AS NUMOFF, count(DISTINCT(H.email)) AS NUMtotal " .
                         "FROM Household AS H INNER JOIN Location AS L ON H.postal_code= L.postal_code LEFT JOIN Utilities AS U on U.email=H.email " .
                         "GROUP BY state_abre " .
                         "ORDER BY NUMOFF DESC " .
                         "LIMIT 1";                          
                     
                $result1 = mysqli_query($db, $query);
          
                while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
                    print "<tr><td>{$row['state_abre']}</td><td>{$row['NUMOFF']}</td><td>{$row['NUMtotal']}</td></tr>";		
                }
            ?>
        </table>
        <br>

        <table>
            <tr>
                <th style="text-align: center">The Average Battery Storage Capacity for All Off-the-Grid Households</th>
            <tr>
            <?php								
                $query = "SELECT ROUND(avg(storage_KWH),0) AS avgKWH " .
                         "FROM Household AS H, PowerGenerator AS P " .
                         "WHERE H.email=P.email AND H.email NOT IN " .
                         "(SELECT Email " .
                         "FROM Utilities)";                          
                     
                $result2 = mysqli_query($db, $query);
            
                while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)){
                    print "<tr><td>{$row['avgKWH']}</td></tr>";		
                }
            ?>
        </table>
        <br>

        <table>
            <tr>
                <th style="text-align: center" colspan='2'>The Percentages for Each Power Generation Type for All Off-the-Grid Households</th>
            <tr>
            <?php								
                $query = "SELECT generator_type, ROUND((count(H.Email)/(SUM(count(H.Email)) OVER())) * 100,1) AS perc " .
                         "FROM Household AS H, PowerGenerator AS P " .
                         "WHERE H.email=P.email AND H.email NOT IN " .
                         "(SELECT Email " .
                         "FROM Utilities) " .
                         "GROUP BY generator_type";          
                     
                $result3 = mysqli_query($db, $query);
            
                while ($row = mysqli_fetch_array($result3, MYSQLI_ASSOC)){
                    print "<tr>";
                    print "<td>{$row['generator_type']}</td>";		
                    print "<td>{$row['perc']}%</td>";	
                    print "</tr>";
                }
            ?>
        </table>
        <br>

        <table>
            <tr>
                <th style="text-align: center">Average Water Heater Gallon Capacity for All Off-the-Grid Households</th>
                <th style="text-align: center">Average Water Heater Gallon Capacity for All On-the-Grid Households</th>
            <tr>
            <?php								
                $query = "SELECT ROUND(avg(capacity),1) AS avgoff " .
                         "FROM Household AS H, WaterHeater AS W " .
                         "WHERE H.email=W.email AND H.email NOT IN " .
                         "(SELECT Email " .
                         "FROM Utilities)";          

                $result4 = mysqli_query($db, $query);
            
                while ($row = mysqli_fetch_array($result4, MYSQLI_ASSOC)){
                    print "<tr>";
                    print "<td>{$row['avgoff']}</td>";			
                }

                $query = "SELECT ROUND(avg(capacity),1) AS avgin " .
                         "FROM Household AS H, WaterHeater AS W " .
                         "WHERE H.email=W.email AND H.email IN " .
                         "(SELECT Email " .
                         "FROM Utilities)";          

                $result5 = mysqli_query($db, $query);
            
                while ($row = mysqli_fetch_array($result5, MYSQLI_ASSOC)){
                    print "<td>{$row['avgin']}</td>";			
                    print "</tr>";
                }
            ?>
        </table>
        <br>

        <table>
            <tr>
                <th style="font-size: 16px; text-align: center">Appliance Type</th>
                <th style="font-size: 15px; text-align: center">Minimum BTUs for all Off-the-Grid Households</th>
                <th style="font-size: 15px; text-align: center">Average BTUs for all Off-the-Grid Households</th>
                <th style="font-size: 15px; text-align: center">Maximum BTUs for all Off-the-Grid Households</th>
            <tr>
            <?php								
                $query = "SELECT type, ROUND(min(BTU_rating),0) AS minBTU, ROUND(avg(BTU_rating),0) AS avgBTU, ROUND(max(BTU_rating),0) AS maxBTU  " .
                         "FROM (" .
                         "(SELECT email, BTU_rating, 'Water heater' AS type  " .
                         "FROM Household NATURAL JOIN WaterHeater " .
                         "WHERE email NOT IN " .
                         "(SELECT email " .
                         "FROM Utilities)) " .
                         "UNION ALL " .
                         "(SELECT email, BTU_rating, 'Air handler' AS type " .
                         "FROM Household NATURAL JOIN AirHandler " .
                         "WHERE email NOT IN " .
                         "(SELECT email " .
                         "FROM Utilities)) " .
                         "UNION ALL " .
                         "(SELECT email, BTU_rating, 'Air Conditioner' AS type " .
                         "FROM (Household NATURAL JOIN AirHandler) NATURAL JOIN AirConditioner " .
                         "WHERE email NOT IN " .
                         "(SELECT email " .
                         "FROM Utilities)) " .
                         "UNION ALL " .
                         "(SELECT email, BTU_rating, 'Heater' AS type " .
                         "FROM (Household NATURAL JOIN AirHandler) NATURAL JOIN Heater " .
                         "WHERE email NOT IN " .
                         "(SELECT Email " .
                         "FROM Utilities)) " .
                         "UNION ALL " .
                         "(SELECT email, BTU_rating, 'Heatpump' AS type " .
                         "FROM (Household NATURAL JOIN AirHandler) NATURAL JOIN Heatpump " .
                         "WHERE email NOT IN " .
                         "(SELECT Email " .
                         "FROM Utilities)) " .
                         ") AS List " .
                         "GROUP BY type";          

                $result6 = mysqli_query($db, $query);
            
                while ($row = mysqli_fetch_array($result6, MYSQLI_ASSOC)){
                    print "<tr>";
                    print "<td>{$row['type']}</td>";	
                    print "<td>{$row['minBTU']}</td>";	
                    print "<td>{$row['avgBTU']}</td>";		
                    print "<td>{$row['maxBTU']}</td>";	
                    print "</tr>";	
                }
            ?>
        </table>

    </form>
    </div> 

</body>
</html>