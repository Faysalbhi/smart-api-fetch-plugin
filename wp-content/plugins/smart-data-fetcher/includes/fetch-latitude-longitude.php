<?php

function get_latitude_longitude($address) {

    global $open_cage_data_api_key;
    
    // Encode the address for URL
    $address = urlencode($address);
    
    // API request URL
    $url = "https://api.opencagedata.com/geocode/v1/json?q={$address}&key={$open_cage_data_api_key}";
    
    // Make the API request
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        return false; // Return false if there's an error in the API request
    }
    
    // Decode the JSON response
    $data = json_decode(wp_remote_retrieve_body($response), true);
    $city_name = $data['results'][0]['components']['_normalized_city'] ?? '';
    // Check if results exist
    if (isset($data['results'][0]['geometry']['lat']) && isset($data['results'][0]['geometry']['lng'])) {
        // Return the latitude and longitude as an associative array
        return array(
            'latitude' => $data['results'][0]['geometry']['lat'],
            'longitude' => $data['results'][0]['geometry']['lng'],
            'city_name' => $city_name,
        );
    }
    
    return array(
            'latitude' => '',
            'longitude' => '',
            'city_name' => '',
        );
}
