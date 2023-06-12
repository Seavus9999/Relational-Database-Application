<?php
include('lib/common.php');
include('lib/show_queries.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $Email = $_SESSION['Email'];
    $ApplianceType = $_POST['ApplianceType'];
    $ManufacturerName = $_POST['Manufacturer'];
    $BTURating = $_POST['BTURatings'];
    $ModelName =  $_POST['ModelName'];
    $EER = $_POST['EER'];
    $EnergySource_AH = $_POST['EnergySource_AH'];
    $SEER = $_POST['SEER'];
    $HSPF = $_POST['HSPF'];
    $EnergySource = $_POST['EnergySource'];
    $Capacity = $_POST['Capacity'];
    $TemperatureSetting = $_POST['Temperature'];

    // echo $_POST['Airconditioner']."</br>";


    if (empty($ApplianceType)) {
        array_push($error_msg,  "Please select appliance type.");
        exit("Please select appliance type.");
    }
    if (empty($ManufacturerName)) {
        array_push($error_msg,  "Please enter manufacturer name.");
        exit("Please enter manufacturer name.");
    }

    if (empty($BTURating)) {
        array_push($error_msg,  "Please enter BTU rating for the appliance.");
        exit("Please enter BTU rating for the appliance.");
    }

    if (empty($ModelName)) {
        $ModelName = "NULL";
    } else {
        $ModelName = "'$ModelName'";
    }

    echo "model name=" . $ModelName;

    if ($ApplianceType == "Air handler") {
        if ($_POST['Airconditioner'] == "true" and empty($EER)) {
            array_push($error_msg,  "Please enter Energy efficiency ratio.");
            exit("Please enter Energy efficiency ratio.");
        }

        if ($_POST['Heater'] == "true" and empty($EnergySource_AH)) {
            array_push($error_msg,  "Please select energy source for heater.");
            exit("Please select energy source for heater.");
        }

        if ($_POST['Heatpump'] == "true") {
            if (empty($SEER)) {
                array_push($error_msg,  "Please enter Seasonal energy efficiency rating.");
                exit("Please enter Seasonal energy efficiency rating.");
            }
            if (empty($HSPF)) {
                array_push($error_msg,  "Please enter Heating seasonal performance factor.");
                exit("Please enter Heating seasonal performance factor.");
            }
        }
        $ApplianceId = ++$_SESSION['ApplianceId'];

        $query = "INSERT INTO AirHandler (email, appliance_ID, model_name, BTU_rating,
        manufacturer_name) VALUES ( '$Email', '$ApplianceId', $ModelName,
        '$BTURating', '$ManufacturerName')";
        $queryID = mysqli_query($db, $query);

        if ($_POST['Airconditioner'] == "true") {
            $query = "INSERT INTO AirConditioner (email, appliance_ID, EER) VALUES
            ('$Email', '$ApplianceId', '$EER')";
            $queryID = mysqli_query($db, $query);
        }

        if ($_POST['Heater'] == "true") {
            $query = "INSERT INTO Heater (email, appliance_ID, energy_source) VALUES
            ('$Email', '$ApplianceId', '$EnergySource_AH')";
            $queryID = mysqli_query($db, $query);
        }
        if ($_POST['Heatpump'] == "true") {
            $query = "INSERT INTO Heatpump (email, appliance_ID, SEER, HSPF) VALUES
            ( '$Email', '$ApplianceId', '$SEER', '$HSPF')";
            $queryID = mysqli_query($db, $query);
        }
    } else {

        if (empty($Capacity)) {
            array_push($error_msg,  "Please enter capacity for the water heater.");
            exit("Please enter capacity for the water heater.");
        }
        if (empty($EnergySource)) {
            array_push($error_msg,  "Please select energy source for the water heater.");
            exit("Please select energy source for the water heater.");
        }
        if (empty($TemperatureSetting)) {
            $TemperatureSetting = "NULL";
        } else {
            $TemperatureSetting = "'$TemperatureSetting'";
        }

        $ApplianceId = ++$_SESSION['ApplianceId'];

        $query = "INSERT INTO WaterHeater (email, appliance_ID, model_name,
        BTU_rating, capacity, temperature_setting, energy_source,
        manufacturer_name) VALUES ( '$Email', '$ApplianceId', $ModelName,
        '$BTURating', '$Capacity', $TemperatureSetting, '$EnergySource',
        '$ManufacturerName' )";
        $queryID = mysqli_query($db, $query);
    }
    header(REFRESH_TIME . 'url=appliances_list.php');
}

?>


<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset=" UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png" />
    <title>AlternaKraft Add appliance</title>

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
    <script type="text/javascript">
        function ShowHideDiv() {
            var selection = document.getElementById("ApplianceType");
            var air_div = document.getElementById("air_handler");
            var water_div = document.getElementById("water_heater");
            air_div.style.display = selection.value == "Air handler" ? "block" : "none";
            water_div.style.display = selection.value == "Water heater" ? "block" : "none";
        }

        function ShowHideDiv_AC() {
            var ac_selection = document.getElementById("Airconditioner").checked;
            var ac_div = document.getElementById("aircondispec");
            ac_div.style.display = ac_selection == true ? "block" : "none";
        }

        function ShowHideDiv_Heater() {
            var heater_selection = document.getElementById("Heater").checked;
            var heater_div = document.getElementById("heaterspec");
            heater_div.style.display = heater_selection == true ? "block" : "none";
        }

        function ShowHideDiv_pump() {
            var pump_selection = document.getElementById("Heatpump").checked;
            var pump_div = document.getElementById("heatpumpspec");
            pump_div.style.display = pump_selection == true ? "block" : "none";
        }
    </script>
</head>

<body>
    <div class="progressbar-wrapper">
        <ul class="progressbar">
            <li>Household Info</li>
            <li class="active">Appliances</li>
            <li>Power generation</li>
            <li>Done</li>
        </ul>
    </div>


    <h1>Add appliance</h1>
    <p style="font-size:24px">Please provide the details for the appliance
    <p>
    <form name="add_appliance" action="add_appliance.php" method="post">
        <table>
            <tr>
                <td style="font-size:24px; width:180px">Appliance type:</td>
                <td>
                    <select style="font-size:24px; width:200px" id="ApplianceType" name="ApplianceType" onchange="ShowHideDiv()">
                        <option style="font-size:24px" value=""></option>
                        <option style="font-size:24px" value="Air handler">Air handler</option>
                        <option style="font-size:24px" value="Water heater">Water heater</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="font-size:24px; width:180px">Manufacturer:</td>
                <td>
                    <select style="font-size:24px; width:300px" name="Manufacturer">
                        <option style="font-size:24px" value=""></option>
                        <?php
                        $query = "SELECT manufacturer_name FROM Manufacturer";
                        $result = mysqli_query($db, $query);
                        while ($manufacturer = mysqli_fetch_array($result, MYSQLI_ASSOC)) :;
                        ?>
                            <option value="<?php echo $manufacturer["manufacturer_name"]; ?>">
                                <?php echo $manufacturer["manufacturer_name"]; ?>
                            </option>
                        <?php
                        endwhile;
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="font-size:24px; width:180px">Model name:</td>
                <td>
                    <input style="width:500px; font-size:24px" type="text" name="ModelName" value="" />
                </td>
            </tr>
            <tr>
                <td style="font-size:24px; width:180px">BTU ratings:</td>
                <td>
                    <input style="width:300px; font-size:24px" type="number" name="BTURatings" value="" />
                </td>
            </tr>
        </table>
        </br>
        <div id="air_handler" style="display: none">
            <table style=" border: 1px solid black">
                <tr>
                    <td>
                        <input style="position: relative; left: 10px; width:30px" type="checkbox" name="Airconditioner" id="Airconditioner" value="true" onchange="ShowHideDiv_AC()">
                    </td>
                    <td style="font-size:24px; width:170px">Air conditioner</td>
                </tr>
                <tr>
                    <td>
                        <input style="position: relative; left: 10px; width:30px" type="checkbox" name="Heater" id="Heater" value="true" onchange="ShowHideDiv_Heater()">
                    </td>
                    <td style="font-size:24px">Heater</td>
                </tr>
                <tr>
                    <td>
                        <input style="position: relative; left: 10px; width:30px" type="checkbox" name="Heatpump" id="Heatpump" value="true" onchange="ShowHideDiv_pump()">
                    </td>
                    <td style="font-size:24px">Heat pump</td>
                </tr>
            </table>

            <table style="position: relative; left: 250px; bottom: 100px; display: none" id="aircondispec">

                <tr>
                    <td style="font-size:24px">Energy efficiency ratio:</td>
                    <td>
                        <input style="width:200px; font-size:24px" type="number" name="EER" value="" step="0.1" />
                    </td>

                </tr>
            </table>
            <table style="position: relative; left: 250px; bottom: 100px; display: none" id="heaterspec">
                <tr>
                    <td style="font-size:24px">Energy source:</td>
                    <td>
                        <select style="font-size:24px; width:150px" name="EnergySource_AH">
                            <option style="font-size:24px" value=""></option>
                            <option style="font-size:24px" value="Electric">Electric</option>
                            <option style="font-size:24px" value="Gas">Gas</option>
                            <option style="font-size:24px" value="Fuel oil">Fuel oil</option>
                    </td>
                </tr>
            </table>

            <table style="position: relative; left: 250px; bottom: 100px; display: none" id="heatpumpspec">

                <tr>
                    <td style="font-size:24px">Seasonal energy efficiency rating:</td>
                </tr>
                <tr>
                    <td>
                        <input style="width:400px; font-size:24px" type="number" name="SEER" value="" step="0.1" />
                    </td>
                </tr>
                <tr>
                    <td style="font-size:24px">Heating seasonal performance factor:</td>
                </tr>
                <tr>
                    <td>
                        <input style="width:400px; font-size:24px" type="number" name="HSPF" value="" step="0.1" />
                    </td>
                </tr>

            </table>
        </div>

        <div id="water_heater" style="display: none">
            <table>
                <tr>
                    <td style="font-size:24px">Energy source:</td>
                    <td>
                        <select style="font-size:24px; width:200px" name="EnergySource">
                            <option style="font-size:24px" value=""></option>
                            <option style="font-size:24px" value="Electric">Electric</option>
                            <option style="font-size:24px" value="Gas">Gas</option>
                            <option style="font-size:24px" value="Thermosolar">Thermosolar</option>
                            <option style="font-size:24px" value="Heat pump">Heat pump</option>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:24px; width:170px">Capacity(gallons):</td>
                    <td>
                        <input style="width:400px; font-size:24px" type="number" name="Capacity" value="" step="0.1" />
                    </td>
                </tr>
                <tr>
                    <td style="font-size:24px; width:170px">Temperature:</td>
                    <td>
                        <input style="width:400px; font-size:24px" type="number" name="Temperature" value="" />
                    </td>
                </tr>
            </table>
        </div>

        <button type="submit" class="button" style="position: absolute; top: 660px; left: 500px" value="Add">Add</button>
    </form>
    </div>

</body>

</html>