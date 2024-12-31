<?php

// Clean up the serialized data
function clean_serialized_data($serialized_data) {
    // Remove the starting prefix (s:{length}:" part)
    $serialized_data = preg_replace('/^string(2166) "/', '', $serialized_data);

    
    // Remove the trailing quote and semicolon (for the ending part)
    $serialized_data = rtrim($serialized_data, '"');
    var_dump($serialized_data);
    echo "<br />";
    echo "<br />";
    return $serialized_data;
}

// Function to update the post meta
function update_post_table($post_id, $firm_data) {
    $firm_activity_data = [
        "tagline_text" => "Best Weight Loss Centers and Naturopathic/Holistic center.",
        "gAddress" => $firm_data['address_line_1'] . ' ' . $firm_data['address_line_2'] . ' ' . $firm_data['address_line_3'] . ' ' . $firm_data['address_line_4'] . ',' . $firm_data['city'] . ',' . $firm_data['country'],
        "latitude" => "30.3439352",
        "longitude" => "-97.72687659999997",
        "mappin" => "",
        "phone" => $firm_data['phone'],
        "whatsapp" => "",
        "email" => $firm_data['email'],
        "website" => $firm_data['website'],
        "twitter" => "http://twitter.com",
        "facebook" => "http://facebook.com",
        "linkedin" => "http://linkedin.com",
        "youtube" => "http://youtube.com",
        "instagram" => "http://instagram.com",
        "video" => "https://www.youtube.com/watch?v=oMxLKOv_3t0",
        "gallery" => $firm_data['image_url_2'],
        "price_status" => "inexpensive",
        // "list_price" => "2",
        // "list_price_to" => "9",
        // "Plan_id" => "0",
        "lp_purchase_days" => "",
        "reviews_ids" => "",
        "claimed_section" => "not_claimed",
        "listings_ads_purchase_date" => "",
        "listings_ads_purchase_packages" => "",
        "faqs" => [
            "faq" => [
                1 => "Services",
                2 => "Overall environment"
            ],
            "faqans" => [
                1 => "Their service and staff are exceptional and very attentive when you walked in. Dr. Kaila and Dr. ManVy are by far one of the amazing doctors.",
                2 => "It is very rare to find a doctor with all those traits these days. We need more practitioners like them. I highly recommend this health center to anyone who wants excellent health care from caring doctors."
            ]
        ],
        "business_hours" => [
            "Monday" => ["open" => "09:00", "close" => "17:00"],
            "Tuesday" => ["open" => "09:00", "close" => "17:00"],
            "Wednesday" => ["open" => "09:00", "close" => "17:00"],
            "Thursday" => ["open" => "09:00", "close" => "17:00"],
            "Friday" => ["open" => "09:00", "close" => "17:00"],
            "Saturday" => ["open" => "8:00", "close" => "11:30"],
            "Sunday" => ["open" => "8:00", "close" => "11:30"]
        ],
        "campaign_id" => "7494",
        "changed_planid" => "478",
        "listing_reported_by" => "3357,4100,4153,5048,8474,8688,12676,12898,13127",
        "listing_reported" => "9",
        "business_logo" => ""
    ];

    
    
    // Update post meta with the cleaned serialized data
    update_post_meta($post_id, 'lp_listingpro_options', $firm_activity_data);

}

