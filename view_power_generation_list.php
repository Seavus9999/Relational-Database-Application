<?php
include('lib/common.php');
include('lib/show_queries.php');
include('config/database.php');

$Email = $_SESSION['Email'];
$offgrid = $_SESSION['offgrid'];
$query = "SELECT generator_ID, generator_type, monthly_KWH, storage_KWH
FROM powergenerator
WHERE email='$Email'";

if (mysqli_query($db, $query)) {
    // Success
    $result = mysqli_query($db, $query);
    // echo "show list successfully!";
    // header(REFRESH_TIME . 'url=view_power_generation_list.php');
} else {
    // Error
    echo 'Error: ' . mysqli_error($db);
}

if (!empty($_GET['generator_ID'])) {
    $delete_ID = mysqli_real_escape_string($db, $_GET['generator_ID']);
    $query = "DELETE FROM powergenerator 
    WHERE email= '$Email' AND generator_ID='$delete_ID'";
    if (mysqli_query($db, $query)) {
        // Success
        $result = mysqli_query($db, $query);
        // header(REFRESH_TIME . 'url=view_power_generation_list.php');
    } else {
        // Error
        echo 'Error: ' . mysqli_error($db);
    }

    header(REFRESH_TIME . 'url=view_power_generation_list.php');
}

$count_query = "SELECT count(*)
FROM powergenerator
WHERE email='$Email'";

$count = mysqli_query($db, $count_query);
if (!$count) {
    die("Query failed: " . mysqli_error($db));
}
$check_row = mysqli_fetch_array($count, MYSQLI_NUM);
// echo "Number of power generators for email $Email: " . $check_row[0];
$num_items = $check_row[0];
// $offgrid = true; // if household is offgrid then they can not skip add power generator
// $num_items = 0;
$disableButton = ($offgrid && $num_items == 0) ? true : false;
?>

<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <link rel="shortcut icon" href="img/gtonline_icon.png">
    <meta http-equiv="Content-Type" content="text/html; charset=" UTF-8 " />
    <link rel=" shortcut icon" type="image/png" href="img/gtonline_icon.png" />
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
            <li>Appliances</li>
            <li class="active">Power generation</li>
            <li>Done</li>
        </ul>
    </div>
    <h1>Power generation</h1>
    <p style="font-size:24px">You have added the following power generators to your household:
    <p>


    <div>
        <form name="add_power" action="add_power.php" method="post">

            <table>
                <tr>
                    <th style="width:140px; text-align: left">Num</th>
                    <th style="width:140px; text-align: left">Type</th>
                    <th style="width:160px; text-align: left">Monthly kWh</th>
                    <th style="width:160px; text-align: left">Battery kWh</th>
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
                        <td><?php echo $rows['generator_ID']; ?></td>
                        <td><?php echo $rows['generator_type']; ?></td>
                        <td><?php echo $rows['monthly_KWH']; ?></td>
                        <td><?php echo $rows['storage_KWH']; ?></td>

                        <td>
                            <a href='view_power_generation_list.php?generator_ID= <?php echo $rows['generator_ID']; ?>&type=<?php echo $rows['generator_type']; ?>'>delete</a>
                        </td>

                    </tr>
                <?php
                }
                ?>
            </table>
        </form>

    </div>
    <p style="font-size:22px; position: relative; left: 550px"><a href="add_power.php">+Add more power</a></p>
    <button id="finishButton" type="button" class="button" style="position: relative; left: 550px" onclick="window.location.href='complete.php';" <?php if ($disableButton) {
                                                                                                                                                        echo "disabled";
                                                                                                                                                    } ?>>Finish</button>



</body>

</html>