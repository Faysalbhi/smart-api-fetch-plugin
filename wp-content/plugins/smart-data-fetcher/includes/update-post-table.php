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
        "faqs" => [
            "faq" => [
                1 => "What are Insurances?",
                2 => "Tell me about Mortgages",
                3 => "How can a Pension help me",
                4 => "What is an investment?",
            ],
            "faqans" => [
                1 => "/><p><strong>Insurance</strong> is a financial arrangement that provides protection against financial loss or risk. It involves a contract (policy) between an individual or business (the policyholder) and an insurance company. The policyholder pays regular payments (premiums) to the insurance company, and in return, the insurance company agrees to compensate them for specific losses, damages, illnesses, or other covered events.</p>
                    
                    <h3>Types of Insurance:</h3>
                    <ul>
                        <li><strong>Health Insurance</strong> – Covers medical expenses, including doctor visits, hospital stays, and prescription drugs.</li>
                        <li><strong>Life Insurance</strong> – Provides financial support to beneficiaries in case of the policyholder’s death.</li>
                        <li><strong>Auto Insurance</strong> – Covers damages to vehicles and liability in case of accidents.</li>
                        <li><strong>Homeowners/Renters Insurance</strong> – Protects homes and belongings from theft, fire, or other damages.</li>
                        <li><strong>Travel Insurance</strong> – Covers unexpected travel-related expenses such as trip cancellations, lost baggage, or medical emergencies.</li>
                        <li><strong>Business Insurance</strong> – Protects businesses from financial losses due to lawsuits, property damage, or employee-related risks.</li>
                        <li><strong>Disability Insurance</strong> – Provides income if a person becomes unable to work due to disability.</li>
                    </ul>

                    <h3>How Insurance Works:</h3>
                    <ol>
                        <li>The policyholder buys an insurance policy and pays premiums.</li>
                        <li>If an insured event happens (e.g., car accident, illness, or property damage), the policyholder files a claim.</li>
                        <li>The insurance company evaluates the claim and, if valid, provides financial compensation based on the policy terms.</li>
                    </ol>

                    <p>Insurance helps individuals and businesses manage risk and protect against unexpected financial burdens.</p>",

                2 => "/><p>A <strong>mortgage</strong> is a loan used to buy property in the UK, where most homebuyers rely on financing due to high property prices. The loan is secured against the property, meaning the lender can repossess it if repayments are not made.</p>

                    <h3>How Mortgages Work in the UK:</h3>
                    <p>When you take out a mortgage, you repay it in monthly installments over an agreed period, typically 25 to 40 years. Each payment consists of:</p>
                    <ul>
                        <li><strong>Capital</strong> – The amount borrowed.</li>
                        <li><strong>Interest</strong> – The cost charged by the lender for borrowing.</li>
                    </ul>

                    <h3>Types of Mortgages in the UK:</h3>
                    <ul>
                        <li><strong>Fixed-Rate Mortgage</strong> – The interest rate stays the same for a set period (e.g., 2, 5, or 10 years), providing payment stability.</li>
                        <li><strong>Tracker Mortgage</strong> – The interest rate follows the Bank of England base rate, meaning payments can fluctuate.</li>
                        <li><strong>Standard Variable Rate (SVR) Mortgage</strong> – A lender’s default rate after a fixed or tracker deal ends; rates can go up or down.</li>
                        <li><strong>Interest-Only Mortgage</strong> – Monthly payments only cover interest, with the full loan repaid at the end of the term.</li>
                        <li><strong>Buy-to-Let Mortgage</strong> – Designed for landlords purchasing properties to rent out.</li>
                    </ul>",

                3 => "/><p>A <strong>pension</strong> is a long-term savings plan designed to provide financial security in retirement. In the UK, pensions offer tax benefits and employer contributions, making them one of the best ways to prepare for later life.</p>

                    <h3>Types of Pensions in the UK:</h3>
                    <ul>
                        <li><strong>State Pension</strong> – Provided by the government, based on your National Insurance contributions.</li>
                        <li><strong>Workplace Pensions</strong> – Employees are automatically enrolled in a pension scheme where both they and their employer contribute.</li>
                        <li><strong>Private or Personal Pensions</strong> – Self-arranged pensions useful for the self-employed or those looking to top up their retirement savings.</li>
                    </ul>

                    <h3>How a Pension Helps You:</h3>
                    <ul>
                        <li>✅ <strong>Tax Relief</strong> – Pension contributions get tax relief, meaning you pay less tax while saving more.</li>
                        <li>✅ <strong>Employer Contributions</strong> – In workplace pensions, your employer must contribute at least 3%.</li>
                        <li>✅ <strong>Long-Term Growth</strong> – Pension funds are invested, helping them grow over time.</li>
                        <li>✅ <strong>Financial Security in Retirement</strong> – A well-funded pension allows you to enjoy retirement without relying solely on the State Pension.</li>
                    </ul>",

                4 => "/><h3>Types of Regulated Investments in the UK</h3>
                    <ul>
                        <li><strong>Stocks & Shares ISAs</strong> – A tax-free way to invest in shares, funds, and bonds, regulated by the FCA.</li>
                        <li><strong>Unit Trusts & Investment Funds</strong> – Pooled funds managed by professionals, including mutual funds and exchange-traded funds (ETFs).</li>
                        <li><strong>Pensions & Annuities</strong> – Workplace and private pensions are heavily regulated to protect retirement savings.</li>
                        <li><strong>Government & Corporate Bonds</strong> – Fixed-interest securities issued by governments or businesses.</li>
                        <li><strong>Financial Derivatives</strong> – Complex products like futures and options, regulated to protect against excessive risk-taking.</li>
                        <li><strong>Insurance-Based Investments</strong> – Investment-linked policies like endowment plans or whole-of-life insurance.</li>
                    </ul>

                    <h3>Who Regulates Financial Investments?</h3>
                    <ul>
                        <li><strong>Financial Conduct Authority (FCA)</strong> – Ensures financial firms comply with consumer protection laws.</li>
                        <li><strong>Prudential Regulation Authority (PRA)</strong> – Oversees banks and insurance firms.</li>
                        <li><strong>Financial Services Compensation Scheme (FSCS)</strong> – Protects up to £85,000 of investments if a firm goes bust.</li>
                    </ul>

                    <h3>How to Invest Safely:</h3>
                    <ul>
                        <li>✅ <strong>Check FCA Registration</strong> – Only invest with FCA-authorized firms.</li>
                        <li>✅ <strong>Understand the Risks</strong> – Higher returns often mean higher risks.</li>
                        <li>✅ <strong>Use Tax-Efficient Accounts</strong> – ISAs and pensions offer tax benefits.</li>
                        <li>✅ <strong>Seek Independent Advice</strong> – A regulated financial advisor can help tailor investments to your goals.</li>
                    </ul>"
                ]

        ],
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

