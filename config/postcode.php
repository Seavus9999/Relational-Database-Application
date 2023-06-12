<?php
$filename = 'postal_codes.csv'; // replace with the name of your CSV file
$data = [];
if (($handle = fopen($filename, "r")) !== FALSE) {
    while (($line = fgetcsv($handle, 2000, ",")) !== FALSE) {
        // $data is an array containing the values in each row of the CSV file
        // do something with $data, for example:
        // echo $data[0] . ", " . $data[1] . ", " . $data[2] . "<br>";
        $data[] = $line;
    }
    fclose($handle);
}
$postcode_list = array_column($data, '0');
// print_r($postcode_list);
$postcode_list_pure = array_slice($postcode_list, 1);
