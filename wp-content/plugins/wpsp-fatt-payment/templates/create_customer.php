<div id="wpsp" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Create Customer <a
                        href="<?= admin_url( 'admin.php?page=wpcc-fatt-customers' ) ?>">View
                    Customers</a></h1>

			<?php if ( ! empty( $error ) ) { ?>
				<?= apply_filters( 'wpsp_error', $error ) ?>
			<?php } ?>

            <form method="POST">
                <div class="wpsp-row">

					<?php if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'payment-method' ) { ?>

                        <h3>Payment Method</h3>
						<?php
						$customer              = get_user_by( 'id', $_REQUEST['id'] );
						$fatt_customer_id      = get_user_meta( $customer->ID, 'fatt_token', true );
						$fatt_payment_id       = get_user_meta( $customer->ID, 'fatt_payment_id', true );
						$fatt_customer_details = unserialize( get_user_meta( $customer->ID, 'fatt_customer_details', true ) );
						?>
                        <span
                                id="fatt_customer_id"
                                token="<?= $fatt_customer_id ?>"
                                fname="<?= $fatt_customer_details['firstname'] ?>"
                                lname="<?= $fatt_customer_details['lastname'] ?>"
                                company="<?= $fatt_customer_details['company'] ?>"
                                address1="<?= $fatt_customer_details['address_1'] ?>"
                                address2="<?= $fatt_customer_details['address_2'] ?>"
                                city="<?= $fatt_customer_details['address_city'] ?>"
                                state="<?= $fatt_customer_details['address_state'] ?>"
                                country="<?= $fatt_customer_details['address_country'] ?>"
                                zip="<?= $fatt_customer_details['address_zip'] ?>"></span>

                        <div class="wpsp-form-group select-payment-type">
                            <label>Payment Type</label>
                            <select name="payment_type" id="payment_type">
                                <option value="">Select Payment Type</option>
                                <option<?= ( ( isset( $data['payment_type'] ) && 'eCheck' === $data['payment_type'] ) ? ' selected' : '' ) ?>>
                                    eCheck
                                </option>
                                <option<?= ( ( isset( $data['payment_type'] ) && 'Card' === $data['payment_type'] ) ? ' selected' : '' ) ?>>
                                    Card
                                </option>
                            </select>
                        </div>

                        <div class="echeck-fields" style="display: none;">
                            <div class="wpsp-form-group">
                                <label>Account Type</label>
                                <select name="paymethod[echeck][account_type]" required>
                                    <option value="checking"<?= ( ( isset( $data['paymethod']['echeck']['account_type'] ) && 'checking' === $data['paymethod']['echeck']['account_type'] ) ? ' selected' : '' ) ?>>
                                        Checking
                                    </option>
                                    <option value="saving"<?= ( ( isset( $data['paymethod']['echeck']['account_type'] ) && 'saving' === $data['paymethod']['echeck']['account_type'] ) ? ' selected' : '' ) ?>>
                                        Saving
                                    </option>
                                </select>
                            </div>
                            <div class="wpsp-form-group">
                                <label>Account Holder Name</label>
                                <input type="text" name="paymethod[echeck][account_holder]" id="bank_account_holder"
                                       value="<?= ( ( isset( $data['paymethod']['echeck']['account_holder'] ) ? $data['paymethod']['echeck']['account_holder'] : '' ) ) ?>"
                                />
                            </div>
                            <div class="wpsp-form-group">
                                <label>Account Number</label>
                                <input type="text" name="paymethod[echeck][account_number]" id="bank_account_no"
                                       value="<?= ( ( isset( $data['paymethod']['echeck']['account_number'] ) ? $data['paymethod']['echeck']['account_number'] : '' ) ) ?>"
                                />
                            </div>
                            <div class="wpsp-form-group">
                                <label>Routing Number</label>
                                <input type="text" name="paymethod[echeck][routing_number]" id="routing_no"
                                       value="<?= ( ( isset( $data['paymethod']['echeck']['routing_number'] ) ? $data['paymethod']['echeck']['routing_number'] : '' ) ) ?>"
                                />
                            </div>
                        </div>

                        <div class="card-fields" style="display: none;">
                            <div class="wpsp-form-group">
                                <label>Card Type</label>
                                <select name="paymethod[card][card_type]" id="card_type">
                                    <option value="visa"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'visa' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                        visa
                                    </option>
                                    <option value="mast"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'mast' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                        mast
                                    </option>
                                    <option value="amex"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'amex' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                        amex
                                    </option>
                                    <option value="disc"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'disc' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                        disc
                                    </option>
                                    <option value="dine"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'dine' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                        dine
                                    </option>
                                    <option value="jcb"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'jcb' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                        jcb
                                    </option>
                                </select>
                            </div>
                            <div class="wpsp-row">
                                <div class="wpsp-one-half">
                                    <div class="wpsp-form-group">
                                        <label>Name on Card</label>
                                        <input type="text" name="paymethod[card][name_on_card]" id="card_holder_name"
                                               value="<?= ( ( isset( $data['paymethod']['card']['name_on_card'] ) ? $data['paymethod']['card']['name_on_card'] : '' ) ) ?>"
                                        />
                                    </div>
                                </div>
                                <div class="wpsp-one-half">
                                    <div class="wpsp-form-group">
                                        <label>Card Number</label>
                                        <input type="text" name="paymethod[card][account_number]" id="card-number-no"
                                               value="<?= ( ( isset( $data['paymethod']['card']['account_number'] ) ? $data['paymethod']['card']['account_number'] : '' ) ) ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="wpsp-row">
                                <div class="wpsp-one-half">
                                    <div class="wpsp-form-group">
                                        <label>Expire Month</label>
                                        <input type="number" name="paymethod[card][expire_month]" id="card_exp_mm"
                                               value="<?= ( ( isset( $data['paymethod']['card']['expire_month'] ) ? $data['paymethod']['card']['expire_month'] : '' ) ) ?>"
                                        />
                                    </div>
                                </div>
                                <div class="wpsp-one-half">
                                    <div class="wpsp-form-group">
                                        <label>Expire Year</label>
                                        <input type="number" name="paymethod[card][expire_year]" id="card_exp_yy"
                                               value="<?= ( ( isset( $data['paymethod']['card']['expire_year'] ) ? $data['paymethod']['card']['expire_year'] : '' ) ) ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="wpsp-form-group">
                                <label>CVV</label>
                                <input type="text" name="paymethod[card][card_verification_value]" id="card-cvv-no"
                                       value="<?= ( ( isset( $data['paymethod']['card']['card_verification_value'] ) ? $data['paymethod']['card']['card_verification_value'] : '' ) ) ?>"
                                />
                            </div>
                        </div>

                        <div class="wpsp-form-group">
                            <input type="hidden" id="payment_method_id" value="">
                            <input type="hidden" name="action" value="get_data"/>
                            <input type="hidden" id="ajax_url" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
                            <button class="wpsp-btn-blue" id="submit_payment_detials">Submit Payment Details</button>
                        </div>

                        <div class="wpsp-form-group">
                            <form onsubmit="return false;">
                                <div class="form-group">
                                    <div id="card-number" style="display: none;width:200px; height:30px;"></div>
                                    <div id="card-cvv" style="display: none;width:50px; height:30px;"></div>
                                    <input type="hidden" id="web_payment_token" name="web_payment_token"
                                           value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_web_payment_token' ) ?>"/>
                                </div>
                                <button class="btn btn-success" id="paybutton">
                                    Save Payment Method
                                </button>
                            </form>
                        </div>

					<?php } else { ?>

                        <div class="wpsp-form-group customers-list">
                            <label>Customer</label>
                            <select class="wpsp-chosen" name="customer" required>
                                <option value="">Select Customer</option>
								<?php
								foreach ( $customers as $customer ) :
									$fatt_customer_id = get_user_meta( $customer->ID, 'fatt_customer_ID_token', true );

									if ( empty( $fatt_customer_id ) ) {
										?>
                                        <option value="<?= $customer->ID ?>"<?= ( ( isset( $data['customer'] ) && $customer->ID == $data['customer'] ) ? ' selected' : '' ) ?>><?= $customer->display_name ?></option>
										<?php
									}
									?>
								<?php endforeach; ?>
                            </select>
                        </div>
                        <h3>Address</h3>
                        <div class="wpsp-row">
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>First Name</label>
                                    <input type="text" name="addresses[0][first_name]"
                                           value="<?= ( ( isset( $data['addresses'][0]['first_name'] ) ? $data['addresses'][0]['first_name'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="addresses[0][last_name]"
                                           value="<?= ( ( isset( $data['addresses'][0]['last_name'] ) ? $data['addresses'][0]['last_name'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-row">
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Company Name</label>
                                    <input type="text" name="addresses[0][company_name]"
                                           value="<?= ( ( isset( $data['addresses'][0]['company_name'] ) ? $data['addresses'][0]['company_name'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Phone</label>
                                    <input type="text" name="addresses[0][phone]"
                                           value="<?= ( ( isset( $data['addresses'][0]['phone'] ) ? $data['addresses'][0]['phone'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-form-group">
                            <label>Email</label>
                            <input type="text" name="addresses[0][email]"
                                   value="<?= ( ( isset( $data['addresses'][0]['email'] ) ? $data['addresses'][0]['email'] : '' ) ) ?>"
                                   required/>
                        </div>
                        <div class="wpsp-row">
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Street 1</label>
                                    <input type="text" name="addresses[0][physical_address][street_line1]"
                                           value="<?= ( ( isset( $data['addresses'][0]['physical_address']['street_line1'] ) ? $data['addresses'][0]['physical_address']['street_line1'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Street 2</label>
                                    <input type="text" name="addresses[0][physical_address][street_line2]"
                                           value="<?= ( ( isset( $data['addresses'][0]['physical_address']['street_line2'] ) ? $data['addresses'][0]['physical_address']['street_line2'] : '' ) ) ?>"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-row">
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>City</label>
                                    <input type="text" name="addresses[0][physical_address][locality]"
                                           value="<?= ( ( isset( $data['addresses'][0]['physical_address']['locality'] ) ? $data['addresses'][0]['physical_address']['locality'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>State</label>
                                    <input type="text" name="addresses[0][physical_address][region]"
                                           value="<?= ( ( isset( $data['addresses'][0]['physical_address']['region'] ) ? $data['addresses'][0]['physical_address']['region'] : '' ) ) ?>"
                                           placeholder="Use 2-letter state abbreviation" required/>
                                </div>
                            </div>
                            <div class="wpsp-one-fourth">
                                <div class="wpsp-form-group">
                                    <label>Country</label>
                                    <select class="wpsp-chosen" name="addresses[0][physical_address][country]">
                                        <option value="AFG">Afghanistan</option>
                                        <option value="ALA">Åland Islands</option>
                                        <option value="ALB">Albania</option>
                                        <option value="DZA">Algeria</option>
                                        <option value="ASM">American Samoa</option>
                                        <option value="AND">Andorra</option>
                                        <option value="AGO">Angola</option>
                                        <option value="AIA">Anguilla</option>
                                        <option value="ATA">Antarctica</option>
                                        <option value="ATG">Antigua and Barbuda</option>
                                        <option value="ARG">Argentina</option>
                                        <option value="ARM">Armenia</option>
                                        <option value="ABW">Aruba</option>
                                        <option value="AUS">Australia</option>
                                        <option value="AUT">Austria</option>
                                        <option value="AZE">Azerbaijan</option>
                                        <option value="BHS">Bahamas</option>
                                        <option value="BHR">Bahrain</option>
                                        <option value="BGD">Bangladesh</option>
                                        <option value="BRB">Barbados</option>
                                        <option value="BLR">Belarus</option>
                                        <option value="BEL">Belgium</option>
                                        <option value="BLZ">Belize</option>
                                        <option value="BEN">Benin</option>
                                        <option value="BMU">Bermuda</option>
                                        <option value="BTN">Bhutan</option>
                                        <option value="BOL">Bolivia, Plurinational State of</option>
                                        <option value="BES">Bonaire, Sint Eustatius and Saba</option>
                                        <option value="BIH">Bosnia and Herzegovina</option>
                                        <option value="BWA">Botswana</option>
                                        <option value="BVT">Bouvet Island</option>
                                        <option value="BRA">Brazil</option>
                                        <option value="IOT">British Indian Ocean Territory</option>
                                        <option value="BRN">Brunei Darussalam</option>
                                        <option value="BGR">Bulgaria</option>
                                        <option value="BFA">Burkina Faso</option>
                                        <option value="BDI">Burundi</option>
                                        <option value="KHM">Cambodia</option>
                                        <option value="CMR">Cameroon</option>
                                        <option value="CAN">Canada</option>
                                        <option value="CPV">Cape Verde</option>
                                        <option value="CYM">Cayman Islands</option>
                                        <option value="CAF">Central African Republic</option>
                                        <option value="TCD">Chad</option>
                                        <option value="CHL">Chile</option>
                                        <option value="CHN">China</option>
                                        <option value="CXR">Christmas Island</option>
                                        <option value="CCK">Cocos (Keeling) Islands</option>
                                        <option value="COL">Colombia</option>
                                        <option value="COM">Comoros</option>
                                        <option value="COG">Congo</option>
                                        <option value="COD">Congo, the Democratic Republic of the</option>
                                        <option value="COK">Cook Islands</option>
                                        <option value="CRI">Costa Rica</option>
                                        <option value="CIV">Côte d'Ivoire</option>
                                        <option value="HRV">Croatia</option>
                                        <option value="CUB">Cuba</option>
                                        <option value="CUW">Curaçao</option>
                                        <option value="CYP">Cyprus</option>
                                        <option value="CZE">Czech Republic</option>
                                        <option value="DNK">Denmark</option>
                                        <option value="DJI">Djibouti</option>
                                        <option value="DMA">Dominica</option>
                                        <option value="DOM">Dominican Republic</option>
                                        <option value="ECU">Ecuador</option>
                                        <option value="EGY">Egypt</option>
                                        <option value="SLV">El Salvador</option>
                                        <option value="GNQ">Equatorial Guinea</option>
                                        <option value="ERI">Eritrea</option>
                                        <option value="EST">Estonia</option>
                                        <option value="ETH">Ethiopia</option>
                                        <option value="FLK">Falkland Islands (Malvinas)</option>
                                        <option value="FRO">Faroe Islands</option>
                                        <option value="FJI">Fiji</option>
                                        <option value="FIN">Finland</option>
                                        <option value="FRA">France</option>
                                        <option value="GUF">French Guiana</option>
                                        <option value="PYF">French Polynesia</option>
                                        <option value="ATF">French Southern Territories</option>
                                        <option value="GAB">Gabon</option>
                                        <option value="GMB">Gambia</option>
                                        <option value="GEO">Georgia</option>
                                        <option value="DEU">Germany</option>
                                        <option value="GHA">Ghana</option>
                                        <option value="GIB">Gibraltar</option>
                                        <option value="GRC">Greece</option>
                                        <option value="GRL">Greenland</option>
                                        <option value="GRD">Grenada</option>
                                        <option value="GLP">Guadeloupe</option>
                                        <option value="GUM">Guam</option>
                                        <option value="GTM">Guatemala</option>
                                        <option value="GGY">Guernsey</option>
                                        <option value="GIN">Guinea</option>
                                        <option value="GNB">Guinea-Bissau</option>
                                        <option value="GUY">Guyana</option>
                                        <option value="HTI">Haiti</option>
                                        <option value="HMD">Heard Island and McDonald Islands</option>
                                        <option value="VAT">Holy See (Vatican City State)</option>
                                        <option value="HND">Honduras</option>
                                        <option value="HKG">Hong Kong</option>
                                        <option value="HUN">Hungary</option>
                                        <option value="ISL">Iceland</option>
                                        <option value="IND">India</option>
                                        <option value="IDN">Indonesia</option>
                                        <option value="IRN">Iran, Islamic Republic of</option>
                                        <option value="IRQ">Iraq</option>
                                        <option value="IRL">Ireland</option>
                                        <option value="IMN">Isle of Man</option>
                                        <option value="ISR">Israel</option>
                                        <option value="ITA">Italy</option>
                                        <option value="JAM">Jamaica</option>
                                        <option value="JPN">Japan</option>
                                        <option value="JEY">Jersey</option>
                                        <option value="JOR">Jordan</option>
                                        <option value="KAZ">Kazakhstan</option>
                                        <option value="KEN">Kenya</option>
                                        <option value="KIR">Kiribati</option>
                                        <option value="PRK">Korea, Democratic People's Republic of</option>
                                        <option value="KOR">Korea, Republic of</option>
                                        <option value="KWT">Kuwait</option>
                                        <option value="KGZ">Kyrgyzstan</option>
                                        <option value="LAO">Lao People's Democratic Republic</option>
                                        <option value="LVA">Latvia</option>
                                        <option value="LBN">Lebanon</option>
                                        <option value="LSO">Lesotho</option>
                                        <option value="LBR">Liberia</option>
                                        <option value="LBY">Libya</option>
                                        <option value="LIE">Liechtenstein</option>
                                        <option value="LTU">Lithuania</option>
                                        <option value="LUX">Luxembourg</option>
                                        <option value="MAC">Macao</option>
                                        <option value="MKD">Macedonia, the former Yugoslav Republic of</option>
                                        <option value="MDG">Madagascar</option>
                                        <option value="MWI">Malawi</option>
                                        <option value="MYS">Malaysia</option>
                                        <option value="MDV">Maldives</option>
                                        <option value="MLI">Mali</option>
                                        <option value="MLT">Malta</option>
                                        <option value="MHL">Marshall Islands</option>
                                        <option value="MTQ">Martinique</option>
                                        <option value="MRT">Mauritania</option>
                                        <option value="MUS">Mauritius</option>
                                        <option value="MYT">Mayotte</option>
                                        <option value="MEX">Mexico</option>
                                        <option value="FSM">Micronesia, Federated States of</option>
                                        <option value="MDA">Moldova, Republic of</option>
                                        <option value="MCO">Monaco</option>
                                        <option value="MNG">Mongolia</option>
                                        <option value="MNE">Montenegro</option>
                                        <option value="MSR">Montserrat</option>
                                        <option value="MAR">Morocco</option>
                                        <option value="MOZ">Mozambique</option>
                                        <option value="MMR">Myanmar</option>
                                        <option value="NAM">Namibia</option>
                                        <option value="NRU">Nauru</option>
                                        <option value="NPL">Nepal</option>
                                        <option value="NLD">Netherlands</option>
                                        <option value="NCL">New Caledonia</option>
                                        <option value="NZL">New Zealand</option>
                                        <option value="NIC">Nicaragua</option>
                                        <option value="NER">Niger</option>
                                        <option value="NGA">Nigeria</option>
                                        <option value="NIU">Niue</option>
                                        <option value="NFK">Norfolk Island</option>
                                        <option value="MNP">Northern Mariana Islands</option>
                                        <option value="NOR">Norway</option>
                                        <option value="OMN">Oman</option>
                                        <option value="PAK">Pakistan</option>
                                        <option value="PLW">Palau</option>
                                        <option value="PSE">Palestinian Territory, Occupied</option>
                                        <option value="PAN">Panama</option>
                                        <option value="PNG">Papua New Guinea</option>
                                        <option value="PRY">Paraguay</option>
                                        <option value="PER">Peru</option>
                                        <option value="PHL">Philippines</option>
                                        <option value="PCN">Pitcairn</option>
                                        <option value="POL">Poland</option>
                                        <option value="PRT">Portugal</option>
                                        <option value="PRI">Puerto Rico</option>
                                        <option value="QAT">Qatar</option>
                                        <option value="REU">Réunion</option>
                                        <option value="ROU">Romania</option>
                                        <option value="RUS">Russian Federation</option>
                                        <option value="RWA">Rwanda</option>
                                        <option value="BLM">Saint Barthélemy</option>
                                        <option value="SHN">Saint Helena, Ascension and Tristan da Cunha</option>
                                        <option value="KNA">Saint Kitts and Nevis</option>
                                        <option value="LCA">Saint Lucia</option>
                                        <option value="MAF">Saint Martin (French part)</option>
                                        <option value="SPM">Saint Pierre and Miquelon</option>
                                        <option value="VCT">Saint Vincent and the Grenadines</option>
                                        <option value="WSM">Samoa</option>
                                        <option value="SMR">San Marino</option>
                                        <option value="STP">Sao Tome and Principe</option>
                                        <option value="SAU">Saudi Arabia</option>
                                        <option value="SEN">Senegal</option>
                                        <option value="SRB">Serbia</option>
                                        <option value="SYC">Seychelles</option>
                                        <option value="SLE">Sierra Leone</option>
                                        <option value="SGP">Singapore</option>
                                        <option value="SXM">Sint Maarten (Dutch part)</option>
                                        <option value="SVK">Slovakia</option>
                                        <option value="SVN">Slovenia</option>
                                        <option value="SLB">Solomon Islands</option>
                                        <option value="SOM">Somalia</option>
                                        <option value="ZAF">South Africa</option>
                                        <option value="SGS">South Georgia and the South Sandwich Islands</option>
                                        <option value="SSD">South Sudan</option>
                                        <option value="ESP">Spain</option>
                                        <option value="LKA">Sri Lanka</option>
                                        <option value="SDN">Sudan</option>
                                        <option value="SUR">Suriname</option>
                                        <option value="SJM">Svalbard and Jan Mayen</option>
                                        <option value="SWZ">Swaziland</option>
                                        <option value="SWE">Sweden</option>
                                        <option value="CHE">Switzerland</option>
                                        <option value="SYR">Syrian Arab Republic</option>
                                        <option value="TWN">Taiwan, Province of China</option>
                                        <option value="TJK">Tajikistan</option>
                                        <option value="TZA">Tanzania, United Republic of</option>
                                        <option value="THA">Thailand</option>
                                        <option value="TLS">Timor-Leste</option>
                                        <option value="TGO">Togo</option>
                                        <option value="TKL">Tokelau</option>
                                        <option value="TON">Tonga</option>
                                        <option value="TTO">Trinidad and Tobago</option>
                                        <option value="TUN">Tunisia</option>
                                        <option value="TUR">Turkey</option>
                                        <option value="TKM">Turkmenistan</option>
                                        <option value="TCA">Turks and Caicos Islands</option>
                                        <option value="TUV">Tuvalu</option>
                                        <option value="UGA">Uganda</option>
                                        <option value="UKR">Ukraine</option>
                                        <option value="ARE">United Arab Emirates</option>
                                        <option value="GBR">United Kingdom</option>
                                        <option value="USA">United States</option>
                                        <option value="UMI">United States Minor Outlying Islands</option>
                                        <option value="URY">Uruguay</option>
                                        <option value="UZB">Uzbekistan</option>
                                        <option value="VUT">Vanuatu</option>
                                        <option value="VEN">Venezuela, Bolivarian Republic of</option>
                                        <option value="VNM">Viet Nam</option>
                                        <option value="VGB">Virgin Islands, British</option>
                                        <option value="VIR">Virgin Islands, U.S.</option>
                                        <option value="WLF">Wallis and Futuna</option>
                                        <option value="ESH">Western Sahara</option>
                                        <option value="YEM">Yemen</option>
                                        <option value="ZMB">Zambia</option>
                                        <option value="ZWE">Zimbabwe</option>


                                    </select>
                                </div>
                            </div>
                            <div class="wpsp-one-fourth">
                                <div class="wpsp-form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="addresses[0][physical_address][postal_code]"
                                           value="<?= ( ( isset( $data['addresses'][0]['physical_address']['postal_code'] ) ? $data['addresses'][0]['physical_address']['postal_code'] : '' ) ) ?>"
                                           required/>
                                </div>
                            </div>
                        </div>

						<?php wp_nonce_field( 'wpcc_fatt_add_customer' ) ?>
                        <div class="wpsp-clearfix"></div>
                        <button class="wpsp-btn-green" type="submit">Authorize & Save</button>

					<?php } ?>

                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://fattjs.fattpay.com/js/fattmerchant.js"></script>
<script>

    var fattjs;
    var customer_id;
    var web_payment_token;

    var fname_fatt;
    var lname_fatt;
    var address1_fatt;
    var address2_fatt;
    var city_fatt;
    var state_fatt;
    var country_fatt;
    var zip_fatt;
    var phone_fatt;
    var company_fatt;

    var type;
    var payment_id;

    var card_placeholder;
    var card_exp_mm;
    var card_exp_yy;
    var card_cvv;
    var card_holder_name;
    var card_type;

    var bank_account_holder;
    var bank_account_no;
    var routing_no;
    var ajax_url;


    jQuery(document).ready(function ($) {

        web_payment_token = $("#web_payment_token").val();

        $("#paybutton").hide();

        $("#payment_type").change(function () {
            type = $("#payment_type option:selected").val();
        });

        $("#card_type").change(function () {
            card_type = $("#card_type option:selected").val();
        });

        $("#submit_payment_detials").parents('form').on('submit', function (e) {

            var extraDetails;

            $("#submit_payment_detials").attr('disabled', 'disabled');
            $("#submit_payment_detials").text('Please wait...');

            // making our instance of fattjs
            fattjs = new FattJs(web_payment_token, {
                // attributes for the credit card number field
                number: {
                    // the html id of the div you want to contain the credit card number field
                    id: 'card-number',
                    // the placeholder the field should contain
                    placeholder: card_placeholder,
                    // the style to apply to the field
                    style: 'height: 30px; width: 100%; font-size: 15px;'
                },
                // attributes for the cvv field
                cvv: {
                    // the html id of the div you want to contain the cvv field
                    id: 'card-cvv',
                    // the placeholder the field should contain
                    placeholder: card_cvv,
                    // the style to apply to the field
                    style: 'height: 30px; width: 100%; font-size: 15px;'
                }
            });

            if (type === "Card") {

                card_placeholder = $("#card-number-no").val();
                card_cvv = $("#card-cvv-no").val();
                card_exp_mm = $("#card_exp_mm").val();
                card_exp_yy = $("#card_exp_yy").val();
                card_holder_name = $("#card_holder_name").val();

                customer_id = $("#fatt_customer_id").attr('token');
                fname_fatt = $("#fatt_customer_id").attr('fname');
                lname_fatt = $("#fatt_customer_id").attr('lname');
                company_fatt = $("#fatt_customer_id").attr('company');
                address1_fatt = $("#fatt_customer_id").attr('address1');
                address2_fatt = $("#fatt_customer_id").attr('address2');
                city_fatt = $("#fatt_customer_id").attr('city');
                state_fatt = $("#fatt_customer_id").attr('state');
                zip_fatt = $("#fatt_customer_id").attr('zip');
                country_fatt = $("#fatt_customer_id").attr('country');

                // tokenization (card)
                //document.querySelector('#paybutton').onclick = () => { }
                extraDetails = {
                    // extra details to be saved into this transaction
                    firstname: fname_fatt, // customer first name
                    lastname: lname_fatt, // customer last name
                    month: card_exp_mm, // credit card expiration month
                    year: card_exp_yy, // credit card expiration year
                    phone: "5555555555", // customer phone number
                    address_1: address1_fatt, // customer address line 1
                    address_2: address2_fatt, // customer address line 2
                    address_city: city_fatt, // customer address city
                    address_state: state_fatt, // customer address state
                    address_zip: zip_fatt, // customer address zip
                    address_country: country_fatt, // customer address country
                    customer_id: customer_id, // OPTIONAL customer_id -
                    url: "https://omni.fattmerchant.com/#/bill/", // url -- just keep this as is unless you're testing
                    validate: false,
                };

            }
            else if (type === "eCheck") {

                routing_no = $("#routing_no").val();
                bank_account_no = $("#bank_account_no").val();
                bank_account_holder = $("#bank_account_holder").val();
                customer_id = $("#customer_id").val();

                // extra details to be saved into this transaction
                extraDetails = {
                    /* Start Bank Details */
                    method: "bank", // very important to set this as "bank" for ACH payments
                    bank_name: "Chase", // bank name, e.g. "Chase"
                    bank_account: bank_account_no, // bank account number
                    bank_routing: routing_no, // bank routing number
                    bank_type: "checking", // "checking" or "savings"
                    bank_holder_type: "personal", // "personal" or "business"
                    /* End Bank Details */

                    firstname: fname_fatt, // customer first name
                    lastname: lname_fatt, // customer last name,
                    person_name: fname_fatt + +lname_fatt,
                    month: "10", // credit card expiration month
                    year: "2020", // credit card expiration year
                    phone: "5555555555", // customer phone number
                    address_1: address1_fatt, // customer address line 1
                    address_2: address2_fatt, // customer address line 2
                    address_city: city_fatt, // customer address city
                    address_state: state_fatt, // customer address state
                    address_zip: zip_fatt, // customer address zip
                    address_country: country_fatt, // customer address country
                    customer_id: customer_id, // OPTIONAL customer_id -
                    // please pass this if you have previously created a customer (using POST customer) and don't want
                    // to create a new customer OR match an existing customer. Passing this will disregard the other
                    // customer fields such as firstname, lastname, phone, all address fields etc.
                    // In this case address_zip will still be stored on the payment method though
                    url: "https://omni.fattmerchant.com/#/bill/", // url -- just keep this as is unless you're testing
                    // validate is optional and can be true or false.
                    // determines whether or not fattmerchant.js does client-side validation.
                    // the validation follows the sames rules as the api.
                    // check the Validation section for more details.
                    validate: false,
                };

            }

            if (extraDetails !== undefined) {

                fattjs
                    .showCardForm()
                    .then(function (handler) {
                        console.log('form was loaded');
                        // for quick testing, you can set a test number and test cvv here
                        handler.setTestPan('4111 1111 1111 1111');
                        handler.setTestCvv('123');
                    }).catch(function (err) {
                    console.log('there was an error loading the form: ', err);
                });

                fattjs.on('card_form_uncomplete', function (message) {
                    // the customer hasn't quite finished filling in the fields
                    // or the input in the fields are invalid
                    console.log(message);
                    // activate pay button
                    var payButton = document.querySelector('#paybutton');
                    //payButton.disabled = true;
                });

                fattjs.on('card_form_complete', function (message) {
                    // the customer has finished filling in the fields, and they're valid!
                    // Nice!
                    console.log(message);
                    // activate pay button
                    var payButton = document.querySelector('#paybutton');
                    payButton.disabled = true;
                });

                setTimeout(function () {
                    // call pay api
                    fattjs
                        .tokenize(extraDetails)
                        .then(function (tokenizedPaymentMethod) {
                            // tokenizedPaymentMethod is the tokenized payment record
                            var token_obj1 = tokenizedPaymentMethod;
                            payment_id = token_obj1.id;

                            console.log('successful tokenization:', tokenizedPaymentMethod);
                            console.log("id", token_obj1.id);

                            $("#payment_method_id").val(payment_id);

                            var ajax_url = $("#ajax_url").val();

                            $.ajax({
                                type: 'POST',
                                url: ajax_url,
                                data: {
                                    'payment_id': payment_id,
                                    'customer_id': customer_id,
                                    'action': 'save_payment_method'
                                },
                                success: function (html) {
                                    window.location.href = '<?= admin_url( 'admin.php?page=wpcc-fatt-customers' ) ?>';
                                }
                            });
                        })
                        .catch(function (err) {
                            // handle errors here
                            console.log('unsuccessful tokenization:', err);
                            alert(err.message);

                            $("#submit_payment_detials").removeAttr('disabled');
                            $("#submit_payment_detials").text('Submit');
                        });
                }, 2000);
            } else {
                alert('More data required');
            }

            return false;
        });
    });
</script>