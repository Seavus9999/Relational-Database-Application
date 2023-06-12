<?php
include('lib/common.php');
include('lib/show_queries.php');
include('config/database.php');
// include('config/postcode.php');
// include('config/function.php');

// echo $_POST['entered_postcode'] . $_POST['radius'];
// echo $_POST['entered_postcode'] . $_POST['radius'];
// Validate radius

$postcode_query = "SELECT postal_code FROM location;";
$postcodelist_result = mysqli_query($db, $postcode_query);
// Fetch all the rows from the result set as an array
$postcodelist = mysqli_fetch_all($postcodelist_result, MYSQLI_ASSOC);
// print_r($postcodelist);
// Extract the postal_code values from the array and store them in a new array
$postal_codes = array_column($postcodelist, 'postal_code');
// print_r($postal_codes);

if (isset($_POST['submit'])) {
    if ($_POST['radius'] === null || $_POST['radius'] === '') {
        $radiusErr = 'Please choose a search radius!';
        // array_push($error_msg,  $radiusErr);
        // exit($radiusErr);
    } else {
        $radius = $_POST['radius'];
        // Validate postcode
        if (empty($_POST['entered_postcode'])) {
            $postcodeErr = 'Please enter your postal code!';
            // array_push($error_msg,  $postcodeErr);
            // exit($postcodeErr);
        } else {
            $check_postcode = $_POST['entered_postcode'];
            // Check if the postcode is in the array
            if (in_array($check_postcode, $postal_codes) && preg_match('/^\d{5}$/', $check_postcode)) {
                // echo "$check_postcode is valid!";
                // echo "Search Radius: " . $radius;

                $query_lat = "SELECT latitude AS LAT FROM location WHERE postal_code='$check_postcode';";
                $result_lat = mysqli_query($db, $query_lat);
                $row_lat = mysqli_fetch_array($result_lat);

                $query_lon = "SELECT longitude AS LON FROM location WHERE postal_code='$check_postcode';";
                $result_lon = mysqli_query($db, $query_lon);
                $row_lon = mysqli_fetch_array($result_lon);

                $lat = $row_lat['LAT'];
                $lon = $row_lon['LON'];

                // echo " LAT: " . $lat;
                // echo " LON: " . $lon;

                $query1 = "SELECT 
                COUNT(*) AS nHousehold, 
                COUNT(CASE WHEN household_type = 'House' THEN 1 END) AS nHouse, 
                COUNT(CASE WHEN household_type = 'apartment' THEN 1 END) AS nApt, 
                COUNT(CASE WHEN household_type = 'townhome' THEN 1 END) AS nTown, 
                COUNT(CASE WHEN household_type = 'condominium' THEN 1 END) AS nCond, 
                COUNT(CASE WHEN household_type = 'mobile home' THEN 1 END) AS nMobile,
                AVG(square_footage) AS avgSQFT,
                AVG(heating) AS avgHEAT,
                AVG(cooling) AS avgCOOL
                FROM household NATURAL JOIN location
                WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius;";

                $result1 = mysqli_query($db, $query1);
                $row1 = mysqli_fetch_array($result1);

                $query2 = "SELECT GROUP_CONCAT(DISTINCT public_utilities SEPARATOR ', ') AS utilities
                FROM household NATURAL JOIN location NATURAL JOIN utilities
                WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius;";


                $result2 = mysqli_query($db, $query2);
                $row2 = mysqli_fetch_array($result2);

                $query3 = "SELECT count(*) AS offgrid_Household
                FROM household NATURAL JOIN location 
                WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius
                AND email NOT IN ( SELECT DISTINCT email FROM utilities) ;";

                $result3 = mysqli_query($db, $query3);
                $row3 = mysqli_fetch_array($result3);

                $query4 = "SELECT count(DISTINCT email) AS NumHWG, avg(monthly_KWH) AS MonthlyKWH
                FROM household NATURAL JOIN location NATURAL JOIN powergenerator
                WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius;";

                $result4 = mysqli_query($db, $query4);
                $row4 = mysqli_fetch_array($result4);

                $query5 = "SELECT generator_type AS MostMethod
                FROM household NATURAL JOIN location NATURAL JOIN powerGenerator
                WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius                
                GROUP BY generator_type
                ORDER BY count(DISTINCT(email)) DESC
                LIMIT 1;";

                $result5 = mysqli_query($db, $query5);
                $row5 = mysqli_fetch_array($result5);

                $query6 = "SELECT count(DISTINCT email) AS NumHwB
                FROM household NATURAL JOIN location NATURAL JOIN powergenerator
                WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius              
                AND storage_KWH IS NOT NULL;";

                $result6 = mysqli_query($db, $query6);
                $row6 = mysqli_fetch_array($result6);

                // $query_st = "SELECT *
                // FROM household NATURAL JOIN location
                // WHERE ST_Distance_Sphere(point(longitude, latitude), point($lon, $lat)) / 1609.34 <= $radius;";

                // $result_st = mysqli_query($conn, $query_st);
                // // $row_st = mysqli_fetch_array($result_st);
                // while ($row_st = mysqli_fetch_assoc($result_st)) {
                //     echo " ST email: " . $row_st["email"] . "<br>";
                // }

                // $query_r = "SELECT *
                // FROM household NATURAL JOIN location
                // WHERE FLOOR((3958.75)*2*ATAN2(SQRT(POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lat))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2)),SQRT(1 - (POW(SIN(RADIANS(latitude - $lat)/2),2) + COS(RADIANS($lon))*COS(RADIANS(latitude))*POW(SIN(RADIANS(longitude - $lon)/2),2))))) <= $radius;";

                // $result_r = mysqli_query($conn, $query_r);
                // while ($row_r = mysqli_fetch_assoc($result_r)) {
                //     echo " my function email: " . $row_r["email"] . "<br>";
                // }

                // $query_fn = "SELECT *
                // FROM household NATURAL JOIN location
                // WHERE Cal_Distance ($lat, $lon, latitude, longitude) <= '$radius';";

                // $result_fn = mysqli_query($conn, $query_fn);
                // // $row_st = mysqli_fetch_array($result_st);
                // while ($row_fn = mysqli_fetch_assoc($result_fn)) {
                //     echo "FN email: " . $row_fn["email"] . "<br>";
                // }
            } else {
                echo "$check_postcode is not a valid postal code, please re-enter your postal code!";
            }
        }
    }

    echo $radiusErr;
    echo $postcodeErr;
}

?>

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <meta http-equiv="Content-Type" content="text/html; charset=" UTF-8" />
    <title>Household averages by radius</title>
</head>

<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        font-size: 24px;
    }
</style>

<body>
    <h1>Household averages by radius</h1>
    <p style="font-size:24px">Please enter your postal code and choose search radius:
    <p>
    <div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <!-- <form method="post" action=""> -->
            <label for="search">Your household postal code:</label>
            <input type="number" name="entered_postcode" id="entered_postcode">
            <label for="radius">and search radius is:</label>
            <select id="radius" name="radius">
                <option type="number" value=""></option>
                <option type="number" value="0">0</option>
                <option type="number" value="5">5</option>
                <option type="number" value="10">10</option>
                <option type="number" value="15">15</option>
                <option type="number" value="20">20</option>
                <option type="number" value="25">25</option>
                <option type="number" value="50">50</option>
                <option type="number" value="100">100</option>
            </select>
            <label>miles</label>
            <button type="submit" name="submit" value="Submit">Check Statistics</button>
        </form>
        <h1>Household information within <?php echo $radius ?> miles around <?php echo $check_postcode ?></h1>
        <?php if (isset($result1)) : ?>
            <?php if (mysqli_num_rows($result1) > 0) : ?>
                <table>
                    <tr>
                        <th>Total households</th>
                        <th>Number of houses</th>
                        <th>Number of apartments</th>
                        <th>Number of townhomes</th>
                        <th>Number of condominiums</th>
                        <th>Number of mobile homes</th>
                        <th>Average square footage</th>
                        <th>Average heating</th>
                        <th>Average cooling</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo $row1['nHousehold']; ?></td>
                        <td style="text-align: center;"><?php echo $row1['nHouse']; ?></td>
                        <td style="text-align: center;"><?php echo $row1['nApt']; ?></td>
                        <td style="text-align: center;"><?php echo $row1['nTown']; ?></td>
                        <td style="text-align: center;"><?php echo $row1['nCond']; ?></td>
                        <td style="text-align: center;"><?php echo $row1['nMobile']; ?></td>
                        <td style="text-align: center;"><?php echo number_format($row1['avgSQFT'], 0); ?></td>
                        <td style="text-align: center;"><?php echo number_format($row1['avgHEAT'], 1); ?></td>
                        <td style="text-align: center;"><?php echo number_format($row1['avgCOOL'], 1); ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Total households</th>
                        <th>Number of houses</th>
                        <th>Number of apartments</th>
                        <th>Number of townhomes</th>
                        <th>Number of condominiums</th>
                        <th>Number of mobile homes</th>
                        <th>Average square footage</th>
                        <th>Average heating</th>
                        <th>Average cooling</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($result2)) : ?>
            <?php if (mysqli_num_rows($result2) > 0) : ?>
                <table>
                    <tr>
                        <th>Available Utilities</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo $row2['utilities']; ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Available Utilities</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo "N.A."; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($result3)) : ?>
            <?php if (mysqli_num_rows($result3) > 0) : ?>
                <table>
                    <tr>
                        <th>Number of Offgrid Household</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo $row3['offgrid_Household']; ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Number of Offgrid Household</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($result4)) : ?>
            <?php if (mysqli_num_rows($result4) > 0) : ?>
                <table>
                    <tr>
                        <th>Number of Homes with Power Generation</th>
                        <th>Average Monthly Power Generation</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo $row4['NumHWG']; ?></td>
                        <td style="text-align: center;"><?php echo number_format($row4['MonthlyKWH'], 1); ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Number of Homes with Power Generation</th>
                        <th>Average Monthly Power Generation</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($result5)) : ?>
            <?php if (mysqli_num_rows($result5) > 0) : ?>
                <table>
                    <tr>
                        <th>Most Common Generation Method</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo $row5['MostMethod']; ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Most Common Generation Method</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo "N.A."; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($result6)) : ?>
            <?php if (mysqli_num_rows($result6) > 0) : ?>
                <table>
                    <tr>
                        <th>Number of Households with Battery Storage</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo $row6['NumHwB']; ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Number of Households with Battery Storage</th>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><?php echo 0; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <p>
    <p style="font-size:22px; position: relative; left: 0px"><a href="view_reports.php">Return to view reports</a></p>
</body>

</html>