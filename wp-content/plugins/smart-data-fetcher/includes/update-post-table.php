<?php


// Function to update the post meta
function update_post_table($post_id, $firm_data) {
    global $geocoding;
    $address = $firm_data['address_line_1'] . ' ' . $firm_data['address_line_2'] . ' ' . $firm_data['address_line_3'] . ' ' . $firm_data['address_line_4'] . ' ' . $firm_data['city'] . ' ' . $firm_data['country'];
    // $geocoding = get_latitude_longitude($address);
    
    $firm_activity_data = [
        "tagline_text" => $firm_data['firm_status'],
        "gAddress" => $address ?? null,
        "latitude" => $firm_data['latitude'] ?? null,
        "longitude" => $firm_data['longitude'] ?? null,
        "phone" => $firm_data['phone'],
        "whatsapp" => "",
        "email" => $firm_data['email'],
        "website" => $firm_data['website'],
        // "twitter" => "http://twitter.com",
        // "facebook" => "http://facebook.com",
        // "linkedin" => "http://linkedin.com",
        // "youtube" => "http://youtube.com",
        // "instagram" => "http://instagram.com",
        // "video" => "https://www.youtube.com/watch?v=oMxLKOv_3t0",
        "price_status" => "inexpensive",
        // "list_price" => "2",
        // "list_price_to" => "9",
        // "Plan_id" => "0",
        "lp_purchase_days" => "",
        "reviews_ids" => "",
        "claimed_section" => "not_claimed",
        "listings_ads_purchase_date" => "",
        "listings_ads_purchase_packages" => "",
        // "faqs" => [
        //     "faq" => [
        //         1 => "Services",
        //         2 => "Overall environment"
        //     ],
        //     "faqans" => [
        //         1 => "Their service and staff are exceptional and very attentive when you walked in. Dr. Kaila and Dr. ManVy are by far one of the amazing doctors.",
        //         2 => "It is very rare to find a doctor with all those traits these days. We need more practitioners like them. I highly recommend this health center to anyone who wants excellent health care from caring doctors."
        //     ]
        // ],
        // "business_hours" => [
        //     "Monday" => ["open" => "09:00", "close" => "17:00"],
        //     "Tuesday" => ["open" => "09:00", "close" => "17:00"],
        //     "Wednesday" => ["open" => "09:00", "close" => "17:00"],
        //     "Thursday" => ["open" => "09:00", "close" => "17:00"],
        //     "Friday" => ["open" => "09:00", "close" => "17:00"],
        //     "Saturday" => ["open" => "8:00", "close" => "11:30"],
        //     "Sunday" => ["open" => "8:00", "close" => "11:30"]
        // ],
        // "campaign_id" => "7494",
        // "changed_planid" => "478",
        // "listing_reported_by" => "3357,4100,4153,5048,8474,8688,12676,12898,13127",
        // "listing_reported" => "9",
        "business_logo" => $firm_data['image_url_1'] ?? 'https://gratisography.com/wp-content/uploads/2024/10/gratisography-birthday-dog-sunglasses-1036x780.jpg'
    ];

    $additional_info = [
        'fca-registration-number' => $firm_data['frn'],
        'company-registration-number' => $firm_data['registered_company_number'],
        'year-established' => $firm_data['authorisation_date'],
        'fca-registration-number-mfilter' => 'fca-registration-number-' . $firm_data['frn'],
        'company-registration-number-mfilter' =>'company-registration-number-'. $firm_data['registered_company_number'],
        'year-established-mfilter' =>'year-established-'. $firm_data['authorisation_date'],
    ];

    
    
    // Update post meta with the cleaned serialized data
    update_post_meta($post_id, 'lp_listingpro_options', $firm_activity_data);

    update_post_meta($post_id, 'lp_listingpro_options_fields', $additional_info);

}

