<?php
include('lib/common.php');
include('lib/show_queries.php');
include('config/database.php');

function highlight_match($string, $match)
{
    $highlighted = preg_replace("/($match)/i", "<span style=\"background-color: lightgreen;\">$1</span>", $string);
    return $highlighted;
}

if (isset($_GET['search'])) {
    // echo "Search for: " . $_GET['search'];
    $EnteredString = $_GET['search'];

    $query = "SELECT DISTINCT model_name, manufacturer_name
    FROM waterheater
    WHERE LOWER(manufacturer_name) LIKE LOWER('%$EnteredString%') OR LOWER(model_name) LIKE LOWER('%$EnteredString%')
    UNION
    SELECT DISTINCT model_name, manufacturer_name
    FROM airhandler
    WHERE LOWER(manufacturer_name) LIKE LOWER('%$EnteredString%') OR LOWER(model_name) LIKE LOWER('%$EnteredString%')
    ORDER BY manufacturer_name ASC, model_name ASC
    ";

    if (mysqli_query($db, $query)) {
        // Success
        $result = mysqli_query($db, $query);
        // echo "show list successfully!";
        // header(REFRESH_TIME . 'url=view_power_generation_list.php');
    } else {
        // Error
        echo 'Error: ' . mysqli_error($db);
    }
}

?>

<!DOCTYPE html> <!-- HTML 5 -->

<head>
    <link rel="stylesheet" href="css/AlternaKraftStyle.css">
    <meta http-equiv="Content-Type" content="text/html; charset=" UTF-8" />
    <title>Manufacturer/model search</title>
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
    <h1>Manufacturer/model search</h1>
    <p style="font-size:24px">Please enter keywords:
    <p>
    <div>
        <form method="get" action="">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search">
            <button type="submit">Search</button>
        </form>
        <p style="font-size:24px">Search for Manufacturer Name and Model Name contain: <?php echo $EnteredString ?>
        <p>
            <?php if (isset($result)) : ?>
                <?php if (mysqli_num_rows($result) > 0) : ?>
        <table>
            <thead>
                <tr>
                    <th>Manufacturer Name</th>
                    <th>Model Name</th>
                    <!-- Add more columns here as needed -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo highlight_match($row['manufacturer_name'], $EnteredString); ?></td>
                        <td><?php echo highlight_match($row['model_name'], $EnteredString); ?></td>
                        <!-- Add more columns here as needed -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No results found.</p>
    <?php endif; ?>
<?php endif; ?>

    </div>
    <p>
    <p style="font-size:22px; position: relative; left: 0px"><a href="view_reports.php">Return to view reports</a></p>
</body>

</html>