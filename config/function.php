<?php
$create_func = " CREATE FUNCTION Cal_Distance(lat1 float, lon1 float, lat2 float,lon2 float)
            RETURNS float 
            DETERMINISTIC
            BEGIN
            
                DECLARE Delta_lat float;
                DECLARE Delta_lon float;
                DECLARE a float;
                DECLARE c float;
                DECLARE d float;
                SET Delta_lat =RADIANS( lat2-lat1);
                SET Delta_lon =RADIANS( lon2-lon1);
                SET a = POW(SIN(Delta_lat/2),2)+COS(lat1)*COS(lat2)*POW(SIN(Delta_lat/2),2);
                SET c = 2*ATAN2(SQRT(a), SQRT(1-a));
                SET d = 3958.75*c;
                RETURN d; 
            
            END;";

$func_done = mysqli_query($conn, $create_func);

if ($func_done === TRUE) {
    echo "SQL function created successfully.";
} else {
    echo "Error creating SQL function: " . $conn->error;
}
