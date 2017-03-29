<?php 

function get_distance_between_two_points($point1, $point2){

	$lng1 = $point1['lng'];
    $lat1 = $point1['lat'];

    $lng2 = $point2['lng'];
    $lat2 = $point2['lat'];

    $deltaLatitude  = deg2rad( (float) $lat2 - (float) $lat1 );
    $deltaLongitude = deg2rad( (float) $lng2 - (float) $lng1 );
    $a              = sin( $deltaLatitude / 2 ) * sin( $deltaLatitude / 2 ) +
                      cos( deg2rad( (float) $lat1 ) ) * cos( deg2rad( (float) $lat2 ) ) *
                      sin( $deltaLongitude / 2 ) * sin( $deltaLongitude / 2 );
    $c              = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
    $distance       = 6371.009 * $c;


    return $distance; 
    
}