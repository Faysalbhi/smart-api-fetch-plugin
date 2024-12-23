<?php
/**
 * CubeWP Custom Forms Templates.
 *
 * @version 1.0
 * @package cubewp/cube/classes/cubewp-forms-templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * CubeWp_Forms_Templates
 */
class CubeWp_Forms_Templates {

    public function __construct() {
        add_action( 'cubewp_custom_form_templates', array( $this, 'cwp_templates_run' ) );
    }
    /**
     * Method cwp_templates_run
     *
     * @return void
     * @since  1.0.0
     */
    public static function cwp_templates_run(  ) {
		CubeWp_Enqueue::enqueue_style('cubewp-admin-templates');
		CubeWp_Enqueue::enqueue_script('cubewp-forms-admin');
		?>
		<div class="wrap cwp-leads-templates">
			<div class="cwp_import"></div>
			<button class="button-primary cwp_import_demo" name="cwp_import_demo" style="display:none">Import Data</button>
            <h2 class="cwp-leads-templates-title">Accelerate Your Form Creation with Our Ready-Made Templates</h2>
            <p class="cwp-leads-templates-des">Select a template to streamline the form-building process. Alternatively, begin with a blank canvas or design your own.</p>
			<div class="cwp-leads-templates-main-search">
				<h3>11 Total Forms</h3>
			</div>
			<div class="cwp-popup" role="alert" style="display:none">
				<div class="cwp-popup-container">
					<p>Are you sure you want to import this template?</p>
					<ul class="cwp-buttons">
						<li><a class="cwp-forms-import-template-confirmed">Yes</a></li>
						<li><a class="cwp-popup-no">No</a></li>
					</ul>
					<a class="cwp-popup-close img-replace">Close</a>
				</div>
			</div>
			<div class="cwp-leads-templates-content">
                <div class="cwp-leads-templates-main">
				<?php 
				$import_folder_path = CWP_FORMS_PLUGIN_DIR . 'cube/import/';

				// Check if the import folder exists
				if (is_dir($import_folder_path)) {
					// Open the import folder
					if ($import_folder_handle = opendir($import_folder_path)) {
						// Loop through each entry in the import folder
						while (($entry = readdir($import_folder_handle)) !== false) {
							// Check if the entry is a directory and not '.' or '..'
							if (is_dir($import_folder_path . $entry) && $entry != '.' && $entry != '..') {
								// Get the directory path of the forms folder within the current import folder
								$forms_folder_path = $import_folder_path . $entry;
								// Check if the forms folder exists
								if (is_dir($forms_folder_path)) {
									// Open the forms folder
									if ($forms_folder_handle = opendir($forms_folder_path)) {
										// Loop through each entry in the forms folder
										while (($template = readdir($forms_folder_handle)) !== false) {
											// Check if the entry is a directory and not '.' or '..'
											if (is_dir($forms_folder_path . '/' . $template) && $template != '.' && $template != '..') {
												// Get the path of the content.txt file
												$content_txt_path = $forms_folder_path  . '/' . $template . '/content.txt';
												// Check if content.txt file exists
												if (file_exists($content_txt_path)) {
													$content = file_get_contents($content_txt_path);
													$lines = explode("\n", $content);
													$template_name = trim($lines[0]);
													$description = trim($lines[1]);
													?>
													<div class="cwp-leads-templates-main-grids">
														<span><?php echo ucwords(str_replace("-", " ", $entry ) ); ?></span>
														<h3><?php echo esc_attr( $template_name ); ?></h3>
														<p><?php echo esc_attr( $description ); ?></p>
														<div class="cwp-leads-templates-main-grids-buttons">
															<a class="preview-link" href="https://cubewp.com/extensions/cubewp-forms-templates/?form_type=<?php echo esc_attr( $template); ?>" target="blank">Preview</a>
															<button class="cwp-templates-grids-buttons-form" data-import="<?php echo CWP_FORMS_PLUGIN_DIR . 'cube/import/'.$entry.'/'.$template; ?>">Import Template</button>
														</div>
													</div>
													<?php
												}
											}
										}
										// Close the forms folder
										closedir($forms_folder_handle);
									}
								}
							}
						}
						// Close the import folder
						closedir($import_folder_handle);
					}
				}
						
				?>
                </div>
                <div class="cwp-leads-templates-sidebar cwp-dashboard-content-panel">
                    <div class="cwp-welcome-box">
						<div class="cwp-welcome-box-content">
							<h2>CubeWP Framework</h2>
							<p>CubeWP is an end-to-end dynamic content framework for WordPress to help you shrink time and cut cost of development up to 90%.</p>
							<div class="cwp-learmore-addons">
								<a target="_blank" href="https://cubewp.com/store/">Learn More</a>
							</div>
						</div>
						<div class="cwp-welcome-box-logo">
							<img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/cube-addons.png'; ?>" alt="" />
						</div>
					</div>
					<div class="cwp-welcome-box cwp-leads-template-addons">
						<div class="cwp-leads-template-addons-titles">
							<h3>Download Free Extensions</h3>
							<a href="https://cubewp.com/extensions/" target="_blank">See All</a>
						</div>
						<div class="cwp-leads-template-addons-cotent">
							<a href="https://cubewp.com/downloads/cubewp-addon-social-logins/" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Social-Login.png'; ?>" alt="image" />Social Login</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-wallet" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Digital-Wallet.png'; ?>" alt="image" />Digital Wallet</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-post-claim" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Post-Claim.png'; ?>" alt="image" />Post Claim</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-bulk-import/" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Bulk-Import.png'; ?>" alt="image" />Bulk Import</a>
						</div>
					</div>
					<div class="cwp-welcome-box cwp-leads-template-addons">
						<div class="cwp-leads-template-addons-titles">
							<h3>Premium Extensions <span>Included with All Premium Plans - Starting $19</span></h3>
							<a href="https://cubewp.com/extensions/" target="_blank">See All</a>
						</div>
						<div class="cwp-leads-template-addons-cotent">
							<a href="https://cubewp.com/downloads/cubewp-addon-frontend-pro/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Frontend.png'; ?>" alt="image" />Frontend Pro</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-payments/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Payments.png'; ?>" alt="image" />Payments</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-inbox/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Inbox.png'; ?>" alt="image" />Inbox</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-reviews/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Reviews.png'; ?>" alt="image" />Reviews</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-booster/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Booster.png'; ?>" alt="image" />Booster</a>
							<a href="https://cubewp.com/downloads/cubewp-addon-booking/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Booking.png'; ?>" alt="image" />Booking</a>
						</div>
					</div>
					<div class="cwp-welcome-box cwp-leads-template-addons">
						<div class="cwp-leads-template-addons-titles">
							<h3>Premium Themes <span>Included with All Premium Plans - Starting $19</span></h3>
							<a href="https://cubewp.com/themes/" target="_blank">See All</a>
						</div>
						<div class="cwp-leads-template-addons-cotent">
							<a href="https://cubewp.com/downloads/dubified/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/dubi.png'; ?>" alt="image" />Classified Ads Theme</a>
							<a href="https://cubewp.com/downloads/streetwise/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/street.png'; ?>" alt="image" />Real-Estate Theme</a>
							<a href="https://cubewp.com/downloads/yellowbooks/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/yellow.png'; ?>" alt="image" />Directory Theme</a>
							<a href="https://themeforest.net/item/classifiedpro-recommerce-classified-wordpress-theme/44528010" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/classi.png'; ?>" alt="image" />Classified Ads Theme</a>
						</div>
					</div>
                    <div class="cwp-welcome-col-md-12 margin-bottom-10 ">
                        <div class="cwp-welcome-faqs">
                            <div class="cwp-faqs-top cwp-welcome-row">
                                <div class="cwp-welcome-header-info">
                                    <span class="dashicons dashicons-shortcode"></span>
                                    <h3 class="cwp-welcome-section-heading">All Shortcodes Cheatsheet</h3>
                                </div>
                            </div>
                            <div class="cwp-post-grid-contentarea">
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Search Form</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpSearch type="YOUR POST TYPE"]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Search Filter</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpFilters]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Saved Posts Page</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpSaved]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>User Signup Page <?php if (!class_exists('CubeWp_Frontend_Load')) { ?>
                                                <a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
                                            <?php } ?>
                                        </h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpRegisterForm role=YOUR USER ROLE”]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Post Type Form Page <?php if (!class_exists('CubeWp_Frontend_Load')) { ?>
                                                <a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
                                            <?php } ?>
                                        </h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpForm type="YOUR POST TYPE"]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>User Dashboard Page <?php if (!class_exists('CubeWp_Frontend_Load')) { ?>
                                                <a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
                                            <?php } ?>
                                        </h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwp_dashboard]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Pricing Plan Page <?php if (!class_exists('CubeWp_Frontend_Load')) { ?>
                                                <a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
                                            <?php } ?>
                                        </h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>Copy Shortcode</p>[cwpPricingPlans]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Reset Password Form <?php if (!class_exists('CubeWp_Frontend_Load')) { ?>
                                                <a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
                                            <?php } ?>
                                        </h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>Copy Shortcode</p>[cwpResetPasswordForm]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cwp-welcome-col-md-12 margin-bottom-10">
                        <div class="cwp-welcome-faqs">
                            <div class="cwp-faqs-top cwp-welcome-row">
                                <div class="cwp-welcome-header-info">
                                    <span class="dashicons dashicons-sos"></span>
                                    <h3 class="cwp-welcome-section-heading">Top Helpful Resources</h3>
                                    <a href="https://support.cubewp.com/" class="cwp-welcome-section-all-faqs" target="_blank">All Documentation</a>
                                </div>
                            </div>
                            <div class="cwp-post-grid-contentarea">
                                <a href="https://support.cubewp.com/docs/cubewp-framework/custom-post-types/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Custom Post Types</p><span class="dashicons dashicons-arrow-right-alt2"></span>
                                </a>
                                <a href="https://support.cubewp.com/docs/cubewp-framework/custom-taxonomies/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Custom Taxonomies</p><span class="dashicons dashicons-arrow-right-alt2"></span>
                                </a>
                                <a href="https://support.cubewp.com/docs/cubewp-framework/custom-fields/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>How to Create Custom Fields</p><span class="dashicons dashicons-arrow-right-alt2"></span>
                                </a>
                                <a href="https://support.cubewp.com/docs/cubewp-framework/developer-guides/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Developer's Guide (CubeWP Filters & Actions)</p><span class="dashicons dashicons-arrow-right-alt2"></span>
                                </a>
                                <a href="https://support.cubewp.com/forums/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Community Forum</p><span class="dashicons dashicons-arrow-right-alt2"></span>
                                </a>
                                <a href="https://help.cubewp.com/" target="_blank" class="cwp-cutom-post-info-row padding-bottom-18">
                                    <p>Helpdesk</p><span class="dashicons dashicons-arrow-right-alt2"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
		
		<?php
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}