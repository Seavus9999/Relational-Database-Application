<?php
include('lib/common.php');
include('lib/show_queries.php');

$Email = $_SESSION['Email'];

$query = "SELECT *
FROM(
SELECT appliance_ID,'Air handler' AS type, manufacturer_name, model_name
FROM AirHandler WHERE Email= '$Email'
UNION
SELECT appliance_ID, 'Water heater' AS type, manufacturer_name, model_name
FROM WaterHeater WHERE email= '$Email') AS LIST
ORDER BY appliance_ID ASC;";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) == 0) {
    $No_appliance = true;
} else {
    $No_appliance = false;
}


if (!empty($_GET['appliance_ID'])) {

    $delete_ID = mysqli_real_escape_string($db, $_GET['appliance_ID']);
    $delete_type = mysqli_real_escape_string($db, $_GET['type']);
    if ($delete_type == "Air handler") {
        $query = "DELETE FROM AirHandler WHERE email= '$Email' AND
        appliance_ID= '$delete_ID'";
    } else {
        $query = "DELETE FROM WaterHeater WHERE email= '$Email' AND
        appliance_ID= '$delete_ID'";
    }
    $result = mysqli_query($db, $query);
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
</head>



<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        font-size: 24px;
    }

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


<body>
    <div class="progressbar-wrapper">
        <ul class="progressbar">
            <li>Household Info</li>
            <li class="active">Appliances</li>
            <li>Power generation</li>
            <li>Done</li>
        </ul>
    </div>
    <h1>Appliances</h1>
    <p style="font-size:24px">You have added the following appliances to your household:
    <p>


    <div>
        <form name="add_appliance" action="add_appliance.php" method="post">

            <table>
                <tr>
                    <th style="width:140px; text-align: left">Appliance #</th>
                    <th style="width:140px; text-align: left">Type</th>
                    <th style="width:160px; text-align: left">Manufacturer</th>
                    <th style="width:160px; text-align: left">Model</th>
                    <th style="width:100px; text-align: left"></th>

                </tr>
                <!-- PHP CODE TO FETCH DATA FROM ROWS -->
                <?php
                // LOOP TILL END OF DATA
                while ($rows = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <!-- FETCHING DATA FROM EACH
                    ROW OF EVERY COLUMN -->
                        <td><?php echo $rows['appliance_ID']; ?></td>
                        <td><?php echo $rows['type']; ?></td>
                        <td><?php echo $rows['manufacturer_name']; ?></td>
                        <td><?php echo $rows['model_name']; ?></td>

                        <td>
                            <a href='appliances_list.php?appliance_ID= <?php echo $rows['appliance_ID']; ?>&type=<?php echo $rows['type']; ?>'>delete</a>
                        </td>

                    </tr>
                <?php
                }
                ?>
            </table>
        </form>

    </div>
    <p style="font-size:22px; position: relative; left: 450px"><a href="add_appliance.php">+Add another appliance</a></p>


    <button type="button" class="button" style="position: relative; left: 450px" <?php if ($No_appliance) { ?> disabled <?php } ?> onclick="window.location.href='add_power.php';">Next</button>
    <?php if ($No_appliance) { ?> <p>need have at least one appliance to leave this page</p> <?php } ?>
</body>

</html>