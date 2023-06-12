<?php
include('lib/common.php');
include('lib/show_queries.php');
include('config/database.php');

$offgrid = $_SESSION['offgrid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $Email = $_SESSION['Email'];
    $GeneratorType = $_POST['GeneratorType'];
    $MonthlyKWH = $_POST['MonthlyKWH'];
    $StorageKWH = $_POST['StorageKWH'];

    if (empty($GeneratorType)) {
        array_push($error_msg,  "Please select generator type.");
        exit("Please select generator type.");
    }
    if (empty($MonthlyKWH)) {
        array_push($error_msg,  "Please enter Monthly KWH number.");
        exit("Please enter Monthly KWH number.");
    }

    $GeneratorId = ++$_SESSION['GeneratorId'];

    // echo $GeneratorId . $MonthlyKWH . $StorageKWH;
    // $offgrid = true; // if household is offgrid then they can not skip add power generator

    if (empty($StorageKWH)) {
        $query = "INSERT INTO powergenerator (email, generator_ID, monthly_KWH, generator_type) VALUES ( '$Email', '$GeneratorId',
        '$MonthlyKWH', '$GeneratorType')";
    } else {
        $query = "INSERT INTO powergenerator (email, generator_ID, storage_KWH, monthly_KWH, generator_type) VALUES ( '$Email', '$GeneratorId',
        '$StorageKWH', '$MonthlyKWH', '$GeneratorType')";
    }
    if (mysqli_query($db, $query)) {
        // Success
        header(REFRESH_TIME . 'url=view_power_generation_list.php');
    } else {
        // Error
        echo 'Error when inserting: ' . mysqli_error($db);
    }
}
?>

<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <title>AlternaKraft Add Power Generator</title>

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
            <li class="active">Power generation</li>
            <li>Done</li>
        </ul>
    </div>
    <h1>Add power generation</h1>
    <p style="font-size:24px">Please provide power generation details
    <p>
    <form name="add_power" action="add_power.php" method="post">
        <table>
            <tr>
                <td style="font-size:24px; width:180px">Type:</td>
                <td>
                    <select style="font-size:24px; width:250px" id="GeneratorType" name="GeneratorType">
                        <option style="font-size:24px" value=""></option>
                        <option style="font-size:24px" value="Solar-electric">Solar-electric</option>
                        <option style="font-size:24px" value="Wind">Wind</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="font-size:24px; width:180px">Monthly kWh:</td>
                <td>
                    <input style="width:250px; font-size:24px" type="number" name="MonthlyKWH" placeholder="Enter Monthly kWh" />
                </td>
            </tr>
            <tr>
                <td style="font-size:24px; width:180px">Storage kWh:</td>
                <td>
                    <input style="width:250px; font-size:24px" type="number" name="StorageKWH" placeholder="Enter Storage kWh" />
                </td>
            </tr>
        </table>
        <a href="complete.php" id="skip" style="position: absolute; top: 300px; left: 550px; font-size:30px">Skip</a>
        <script>
            // Find the element with the "skip" id
            var skipLink = document.getElementById("skip");

            // Set the skip link's display property based on the PHP variable
            var needsSkip = <?php echo $offgrid ? 'false' : 'true'; ?>;
            skipLink.style.display = needsSkip ? "block" : "none";
        </script>
        <button type="submit" class="button" style="position: absolute; top: 350px; left: 500px" value="Next">Next</button>
    </form>
    </div>

</body>

</html>