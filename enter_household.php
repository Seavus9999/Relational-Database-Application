<?php
include('lib/common.php');
include('lib/show_queries.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {




    $Email = mysqli_real_escape_string($db, $_POST['Email']);
    $PostalCode = mysqli_real_escape_string($db, $_POST['PostalCode']);
    $HouseholdType = mysqli_real_escape_string($db, $_POST['HouseholdType']);
    $SquareFootage = (int)$_POST['SquareFootage'];

    $Heating = $_POST['Heating'];
    $NoHeating = $_POST['NoHeating'];
    $Cooling = $_POST['Cooling'];
    $NoCooling = $_POST['NoCooling'];
    $PublicUtilities =  $_POST['PublicUtilities'];


    if (empty($Email)) {
        array_push($error_msg,  "Please enter your email.");
        exit("Please enter your email.");
    }
    if (!strpos($Email, '@')) {
        array_push($error_msg,  "Please enter your email.");
        exit("Please enter your email.");
    }
    if (empty($HouseholdType)) {
        array_push($error_msg,  "Please select house type.");
        exit("Please select house type.");
    }
    if (empty($SquareFootage)) {
        array_push($error_msg,  "Please enter square footage as a whole number.");
        exit("Please enter square footage as a whole number.");
    } else {
        (int)$SquareFootage;
    }

    if (empty($Heating) and empty($NoHeating)) {
        array_push($error_msg,  "Please select enter thermostat setting for heating or indicate no heating.");
        exit("Please select enter thermostat setting for heating or indicate no heating.");
    }
    if (!empty($Heating) and $NoHeating == 'true') {
        array_push($error_msg,  "Cannot have thermostat setting for heating and indicate no heating at the same time.");
        exit("Cannot have thermostat setting for heating and indicate no heating at the same time.");
    }
    if (empty($Cooling) and empty($NoCooling)) {
        array_push($error_msg,  "Please select enter thermostat setting for cooling or indicate no cooling.");
        exit("Please select enter thermostat setting for cooling or indicate no cooling.");
    }
    if (!empty($Cooling) and $NoCooling == 'true') {
        array_push($error_msg,  "Cannot have thermostat setting for cooling and cooling no heating at the same time.");
        exit("Cannot have thermostat setting for cooling and cooling no heating at the same time.");
    }


    $query = "SELECT email FROM Household WHERE email='$Email'";
    $result = mysqli_query($db, $query);
    if (!is_bool($result) && (mysqli_num_rows($result) > 0)) {
        array_push($error_msg,  "The entered email already exists");
        exit("The entered email already exists");
    } else {
        $_SESSION['Email'] = $Email;
        $_SESSION['ApplianceId'] = 0;
        $_SESSION['GeneratorId'] = 0;
    }


    $query = "SELECT postal_code FROM Location WHERE postal_code ='$PostalCode'";
    $result = mysqli_query($db, $query);


    if (!is_bool($result) && (mysqli_num_rows($result) < 1)) {
        array_push($error_msg,  "The entered postal code doesn't exist");
        exit("The entered postal code doesn't exist");
    }

    if (empty($PublicUtilities)) {
        $_SESSION['offgrid'] = true;
    } else {
        $_SESSION['offgrid'] = false;
    }


    if (!$NoHeating == "true") {
        $Heating = "'$Heating'";
    } else {
        $Heating = "NULL";
    }


    if (!$NoCooling == "true") {
        $Cooling = "'$Cooling'";
    } else {
        $Cooling = "NULL";
    }



    $query = "INSERT INTO household (email, square_footage, household_type, heating, cooling, postal_code) VALUES ( '$Email', '$SquareFootage', '$HouseholdType', $Heating, $Cooling,'$PostalCode');";
    $queryID = mysqli_query($db, $query);



    foreach ($PublicUtilities as $value) {
        $query = "INSERT INTO Utilities (email, public_utilities)
        VALUES ( '$Email', '$value' )";
        $queryID = mysqli_query($db, $query);
    }
    header(REFRESH_TIME . 'url=add_appliance.php');
}  //end of if($_POST)

?>



<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset=" UTF-8" />
    <link rel="shortcut icon" type="image/png" href="img/gtonline_icon.png" />
    <title>AlternaKraft Enter Household Info</title>

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
            <li class="active">Household Info</li>
            <li>Appliances</li>
            <li>Power generation</li>
            <li>Done</li>
        </ul>
    </div>
    <h1>Enter household info</h1>
    <form name="household_info" action="enter_household.php" method="post">
        <table>
            <tr>
                <td style="width:40%; font-size:24px">Please enter your email address:</td>
            </tr>
            <tr>
                <td>
                    <input style="width:120%; font-size:24px" type="text" name="Email" value="" />
                </td>
            </tr>
            <tr>
                <td style="font-size:24px">Please enter your five digit postal code:</td>
                <td>
                    <input style="width:30%; font-size:24px; position: relative; left: -130px" type="text" name="PostalCode" value="" />
                </td>
            </tr>
            <tr>
                <td style="font-size:24px">Please enter the following details for your household.</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="font-size:24px; width:180px">Home Type:</td>
                <td>
                    <select style="font-size:24px; width:200px" name="HouseholdType">
                        <option style="font-size:24px" value="empty"></option>
                        <option style="font-size:24px" value="House">House</option>
                        <option style="font-size:24px" value="apartment">apartment</option>
                        <option style="font-size:24px" value="townhome">townhome</option>
                        <option style="font-size:24px" value="condominium">condominium</option>
                        <option style="font-size:24px" value="mobile home">mobile_home</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="font-size:24px">Square footage:</td>
                <td>
                    <input style="font-size:24px" type="number" name="SquareFootage" value="" />
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="font-size:24px; width:300px">Thermostat setting for heating:</td>
                <td>
                    <input style="font-size:24px; width:120px" type="number" name="Heating" value="" />
                </td>
                <td>
                    <input style="position: relative; left: 10px; width:30px" type="checkbox" name="NoHeating" value="true">
                </td>
                <td style="font-size:24px">No heat</td>
            </tr>
            <tr>
                <td style="font-size:24px">Thermostat setting for cooling:</td>
                <td>
                    <input style="font-size:24px; width:120px" type="number" name="Cooling" value="" />
                </td>
                <td>
                    <input style="position: relative; left: 10px; width:30px" type="checkbox" name="NoCooling" value="true">
                </td>
                <td style="font-size:24px">No cooling</td>
            </tr>
        </table>
        <p style="width:60%; font-size:24px">Public utilities:</p>
        <p style="width:60%; position: relative; top: -25px; font-size:14px">(if none,leave unchecked)</p>
        <table style="position: relative; left: 200px; top: -80px; border: 1px solid black">
            <tr>
                <td>
                    <input style="position: relative; left: 10px; width:30px" type="checkbox" name="PublicUtilities[]" value="Electric">
                </td>
                <td style="font-size:24px; width:150px">Electric</td>
            </tr>
            <tr>
                <td>
                    <input style="position: relative; left: 10px; width:30px" type="checkbox" name="PublicUtilities[]" value="Gas">
                </td>
                <td style="font-size:24px">Gas</td>
            </tr>
            <tr>
                <td>
                    <input style="position: relative; left: 10px; width:30px" type="checkbox" name="PublicUtilities[]" value="Steam">
                </td>
                <td style="font-size:24px">Steam</td>
            </tr>
            <tr>
                <td>
                    <input style="position: relative; left: 10px; width:30px" type="checkbox" name="PublicUtilities[]" value="Fuel oil">
                </td>
                <td style="font-size:24px">Fuel oil</td>
            </tr>
        </table>
        <button type="submit" class="button" style="position: relative; bottom: 180px; left: 450px" value="Next">Next</button>

    </form>

    </div>

</body>

</html>