<?php
include('lib/common.php');
include('lib/show_queries.php');

//retrieve household types 
$query_household_types = "SELECT DISTINCT household_type FROM Household";
$result_household_types = mysqli_query($db, $query_household_types);


?>

<!DOCTYPE html>  <!-- HTML 5 -->
<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset="UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png"/>
    <title>Heating/Cooling Method Details by Household Type</title>
</head>
<style>
<!DOCTYPE html>
<html>
<head>
	<title>Heating/Cooling Method Details</title>
	<style>
    table{
        border: 1px solid black;
        border-collapse: collapse;
    }

    th{
        width:250px; 
        text-align: left;
        border: 1px solid black;
    }

    td{
        border: 1px solid black;
    }
    </style>
</head>


<body>
	<div class="container">
		<h1>Heating/Cooling Method Details</h1>
		<table style="border: 1px solid black; border-collapse: collapse;">
			<thead>
				<tr>
					<th>Household Type</th>
					<th>Count of Air Conditioners</th>
					<th>Average Air Conditioner BTUs</th>
					<th>Average EER</th>
					<th>Heater Count</th>
					<th>Average Heater BTUs</th>
					<th>Most Common Energy Source</th>
					<th>Heat Pump Count</th>
					<th>Average Heat Pump BTUs</th>
					<th>Average SEER</th>
					<th>Average HSPF</th>
				</tr>
			</thead>
			<tbody>
				<?php
				while ($row_household_types = mysqli_fetch_assoc($result_household_types)) {
                    $household_type = $row_household_types['household_type'];

                    //fetching air conditioner for the corresponding household type
                    $query_air_conditioner = "SELECT COUNT(AH.appliance_ID) AS NumAirConditioner, ROUND(AVG(AH.BTU_rating)) AS AvgBTURating, ROUND(AVG(AC.EER), 1) AS AvgEER
                    FROM Household AS H
                    INNER JOIN AirHandler AS AH ON H.email = AH.email AND H.household_type = '$household_type'
                    INNER JOIN AirConditioner AS AC ON AH.email = AC.email AND AH.appliance_ID = AC.appliance_ID";
                    $result_air_conditioner = mysqli_query($db, $query_air_conditioner);
                
                    //fetching heaters for the corresponding household type
                    $query_heater = "SELECT HO.household_type, COUNT(Heater.appliance_ID) AS NumHeater, ROUND(AVG(AH.BTU_rating)) AS AvgBTURating,
                    (SELECT energy_source
                        FROM Heater 
                        WHERE HO.household_type = '$household_type'
                        GROUP BY energy_source
                        HAVING COUNT(*) > 0
                        ORDER BY COUNT(*) DESC
                        LIMIT 1
                    ) AS MostComEnergySource
                    FROM Household AS HO NATURAL JOIN AirHandler AS AH INNER JOIN Heater ON AH.email = Heater.email AND AH.appliance_ID = Heater.appliance_ID
                    WHERE HO.household_type = '$household_type';";

                    $result_heater = mysqli_query($db, $query_heater);

                    //fetching heat pumps for the corresponding household type
                    $query_heat_pump = "SELECT COUNT(HP.appliance_ID) AS NumHeatPumps, ROUND(AVG(AH.BTU_rating)) AS AvgBTURating, ROUND(AVG(HP.SEER), 1) AS AvgSEER, ROUND(AVG(HP.HSPF), 1) AS AvgHSPF
                     FROM Household AS H NATURAL JOIN AirHandler AS AH
                    INNER JOIN HeatPump AS HP ON AH.email = HP.email AND AH.appliance_ID = HP.appliance_ID AND H.household_type = '$household_type'";

                    $result_heat_pump = mysqli_query($db, $query_heat_pump);


                    // Check if appliances exist for the matching houshold type
                    $air_conditioner_exists = mysqli_fetch_assoc($result_air_conditioner);
                    $heater_exists = mysqli_fetch_assoc($result_heater);
                    $heat_pump_exists = mysqli_fetch_assoc($result_heat_pump);

                    
                
                    // print data
                    echo "<tr>";
                    echo "<td>" . $household_type . "</td>";
                    echo "<td>" . ($air_conditioner_exists ? $air_conditioner_exists['NumAirConditioner'] : "0") . "</td>";
                    echo "<td>" . (!empty($air_conditioner_exists['AvgBTURating']) ? $air_conditioner_exists['AvgBTURating'] : "0") . "</td>";
                    echo "<td>" . (!empty($air_conditioner_exists['AvgEER']) ? $air_conditioner_exists['AvgEER'] : "0") . "</td>";

                    echo "<td>" . ($heater_exists ? $heater_exists['NumHeater'] : "0") . "</td>";
                    echo "<td>" . (!empty($heater_exists['AvgBTURating']) ? $heater_exists['AvgBTURating'] : "0") . "</td>";
                    echo "<td>" . (!empty($heater_exists['MostComEnergySource']) ? $heater_exists['MostComEnergySource'] : "0") . "</td>";
                    echo "<td>" . ($heat_pump_exists ? $heat_pump_exists['NumHeatPumps'] : "0") . "</td>";
                    echo "<td>" . (!empty($heat_pump_exists['AvgBTURating']) ? $heat_pump_exists['AvgBTURating'] : "0") . "</td>";
                    echo "<td>" . (!empty($heat_pump_exists['AvgSEER']) ? $heat_pump_exists['AvgSEER'] : "0") . "</td>";
                    echo "<td>" . (!empty($heat_pump_exists['AvgHSPF']) ? $heat_pump_exists['AvgHSPF'] : "0") . "</td>";
                    echo "</tr>";
                }
				mysqli_close($con);

				?>
			</tbody>
		</table>
	</div>
</body>
