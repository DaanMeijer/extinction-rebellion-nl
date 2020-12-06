<?php

$font_list = array('', 'Abel', 'Abril Fatface', 'Acme', 'Alegreya', 'Alegreya Sans', 'Anton', 'Archivo', 'Archivo Black', 'Archivo Narrow', 'Arimo', 'Arvo', 'Asap', 'Asap Condensed', 'Beth Ellen', 'Bitter', 'Bowlby One SC', 'Bree Serif', 'Cabin', 'Cairo', 'Catamaran', 'Crete Round', 'Crimson Text', 'Cuprum', 'Dancing Script', 'Dosis', 'Droid Sans', 'Droid Serif', 'EB Garamond', 'Exo', 'Exo 2', 'Faustina', 'Fira Sans', 'Fjalla One', 'Francois One', 'Gloria Hallelujah', 'Hind', 'Inconsolata', 'Indie Flower', 'Josefin Sans', 'Julee', 'Karla', 'Lato', 'Libre Baskerville', 'Libre Franklin', 'Lobster', 'Lora', 'Mada', 'Manuale', 'Maven Pro', 'Merriweather', 'Merriweather Sans', 'Montserrat', 'Montserrat Subrayada', 'Mukta Vaani', 'Muli', 'Noto Sans', 'Noto Serif', 'Nunito', 'Open Sans', 'Open Sans Condensed:300', 'Oswald', 'Oxygen', 'PT Sans', 'PT Sans Caption', 'PT Sans Narrow', 'PT Serif', 'Pacifico', 'Passion One', 'Pathway Gothic One', 'Play', 'Playfair Display', 'Poppins', 'Questrial', 'Quicksand', 'Raleway', 'Ranga', 'Roboto', 'Roboto Condensed', 'Roboto Mono', 'Roboto Slab', 'Ropa Sans', 'Rubik', 'Saira', 'Saira Condensed', 'Saira Extra Condensed', 'Saira Semi Condensed', 'Sedgwick Ave', 'Sedgwick Ave Display', 'Shadows Into Light', 'Signika', 'Slabo 27px', 'Source Code Pro', 'Source Sans Pro', 'Spectral', 'Titillium Web', 'Ubuntu', 'Ubuntu Condensed', 'Varela Round', 'Vollkorn', 'Work Sans', 'Yanone Kaffeesatz', 'Zilla Slab', 'Zilla Slab Highlight');

// var_dump(count($font_list));
?>

<div>
    <div class="plugin-style-list" id="plugin-style-list">
        <div style="display: flex;">
            <h1 style="flex-grow: 1; margin-left: 5%"><?php _e('All styles', 'whydonate-v2') ?></h1>
            <button class="btn-style" onclick="addNewStyle()" style="margin-right: 5%"><?php _e('Add style', 'whydonate-v2') ?></button>
        </div>

        <table class="data-list-table">
            <thead>
                <tr>
                    <th scope="col">Colors</th>
                    <th scope="col">Name</th>
                    <th scope="col">ID</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . "wdplugin_style";
            $result = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
            foreach ($result as $row) {
                #$sitemap .= $row[name];
                echo '<tr>';
                echo '<td class="plugin-style-list-thumbnail">';
            ?>
                <div style="width: 100px; height: 30px; margin: auto; background-color: <?php echo $row['colorCode'] ?>">
                </div>
                <?php
                // $row['styleName'] = str_replace("'", "\'", $row['styleName']);
                echo '<td class="plugin-style-list-name">' . htmlspecialchars(stripslashes($row['styleName']))  . '</td>';
                echo '<td class="plugin-style-list-id">' . $row['id'] . '</td>';
                echo '<td class="plugin-style-list-item-btn">';
                ?>
                <button class="plugin-style-list-item-edit-btn" onclick='editStyle(<?php echo json_encode($row, JSON_HEX_APOS); ?>)'><?php _e('Edit', 'whydonate-v2') ?></button>
                <button class="plugin-style-list-item-remove-btn" name="remove_row" style="margin-left: 5px;"><?php _e('Remove', 'whydonate-v2') ?></button>

            <?php
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>

        <?php
        if (empty($result)) {
        ?>

            <div style="display: flex; flex-direction: column">

                <div style="flex-grow: 1; text-align: center; margin-top: 20px">
                    <img src="<?php echo plugin_dir_url(__FILE__) . 'images/style.png'; ?>" alt="Italian Trulli">
                    <h3 style="text-align: center; padding-top: 10px"><?php _e("It seems you haven’t created any styles yet.", "whydonate-v2") ?></h3>
                </div>
                <div style="flex-grow: 1; text-align: center">
                    <button class="btn-style" onclick="addNewStyle()" style="margin: 0px; float: none"><?php _e('Add style', 'whydonate-v2') ?></button>
                </div>

            </div>
        <?php
        }
        ?>
    </div>


    <div class="add-new-style" id="add-new-style" style="display: none;">
        <button class="btn-style" style="float: none;
                margin-top: 20px;
                margin-left: 0px;
                height: 40px;
                width: 70px;" onclick="backToStyleList()"><?php _e('Back', 'whydonate-v2') ?></button>
        <h1><?php _e('Add style', 'whydonate-v2') ?></h1>


        <div class="style-container" style="display: flex; flex-direction: row;">

            <div class="left-side" style="display: flex; flex-grow: 1">
                <div style="display: flex; margin: 10px; justify-content: space-between; flex-direction: row; flex-grow: 1">
                    <!-- <form method="post"> -->

                    <div class="customise-style" style="margin-top: 20px;">

                        <div class="style-name">
                            <label for="set-style-name-label" style="margin-top: 20px; font-size: 14px; font-weight: 600"><?php _e('Style Name', 'whydonate-v2') ?></label>
                            <input type="text" id="set-style-name-input" name="set-style-name-input" style="margin-top: 20px; margin-left: 15px; width: 250px" onchange="changeStyleName(this)">
                            <input type="number" id="set-id" name="set-id" value="0" style="margin-top: 20px; margin-left: 15px; width: 50px">

                            <label id="show-title-error" style="display: none"></label>
                        </div>

                        <div class="style-options" style="margin-top: 30px;">

                            <label for="customise-label" style="margin-top: 25px; font-size: 18px;"><?php _e('Widget options', 'whydonate-v2') ?></label>

                            <div class="appearance-option-section" id="appearance-option-section" style="margin-top: 25px">
                                <label for="select-apperance-label" style="font-size: 14px; font-weight: 600"><?php _e('Widget appearance', 'whydonate-v2') ?></label>

                                <div class="select-apperance-options">
                                    <input type="checkbox" id="select-donate-button" name="select-donate-button" value="1" onchange="showHideDonateButton(this.checked)" checked><label><?php _e('Donation Button', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-progress-bar" name="select-progress-bar" value="2" onchange="showHideProgressBar(this.checked)" checked><label><?php _e('Progressbar', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-raised-amount" name="select-raised-amount" value="3" onchange="showHideRaisedAmount(this.checked)" checked><label><?php _e('Amount Raised', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-show-days-left" name="select-show-days-left" value="4" onchange="showHideDaysLeft(this.checked)" checked><label><?php _e('End Date', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-show-donate-form-only" name="select-show-only-donate-form" value="0" onchange="showDonationFormOnly(this.checked)"><label><?php _e('No widget, show donation form only', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-donot-show-box" name="select-box-donot-show" value="0" onchange="doNotShowBox(this.checked)"><label><?php _e('Do not show box', 'whydonate-v2') ?></label><br><br>
                                </div>
                                <label id="show-appearance-error" style="display: none"></label>

                            </div>

                            <div class="select-amount-section" id="select-amount-section">
                                <label for="select-amount-label" style="font-size: 14px; font-weight: 600"><?php _e('Select and edit amounts', 'whydonate-v2') ?></label>
                                <div class="select-amount-options" id="select-amount-options">
                                    <span>
                                        <input type="checkbox" id="select-first" name="select-amount-first" value="1" checked onchange="showAmountFirst(this.checked)">
                                        <input type="number" id="select-first-amount" name="select-amount-value-first" value="25" onchange="changeFirstAmount()" style="margin-top: 10px;">
                                    </span>
                                    <span>
                                        <input type="checkbox" id="select-second" name="select-amount-second" value="2" checked onchange="showAmountSecond(this.checked)">
                                        <input type="number" name="select-amount-value-second" id="select-second-amount" value="50" onchange="changeSecondAmount()" style="margin-top: 10px;">
                                    </span>
                                    <span>
                                        <input type="checkbox" id="select-third" name="select-amount-third" value="3" checked onchange="showAmountThird(this.checked)">
                                        <input type="number" name="select-amount-value-third" id="select-third-amount" value="75" onchange="changeThirdAmount()" style="margin-top: 10px;">
                                    </span>
                                    <span>
                                        <input type="checkbox" id="select-forth" name="select-amount-forth" value="4" onchange="showAmountForth(this.checked)" checked>
                                        <input type="number" name="select-amount-value-forth" id="select-forth-amount" value="100" onchange="changeForthAmount()" style="margin-top: 10px;">
                                    </span>
                                    <span>
                                        <input type="checkbox" id="select-other" name="select-amount-other" value="1" style="margin-top: 10px;
                        			    margin-bottom: 10px;" onchange="showOtherAmount(this.checked)" checked><label style="margin: 5px">Other</label>
                                    </span>
                                </div>
                                <label id="show-amount-error" style="display: none"></label>

                            </div>

                            <div class="select-interval-section" id="select-interval-section" style="margin-top: 25px">
                                <label for="select-interval-label" style="font-size: 14px; font-weight: 600"><?php _e('Donation intervals', 'whydonate-v2') ?></label>
                                <div class="select-interval-options" id="select-interval-options">
                                    <input type="checkbox" id="select-interval-onetime" name="select-interval-onetime" value="1" checked onchange="showHideOneTime(this.checked)"><label><?php _e('One time', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-interval-monthly" name="select-interval-monthly" value="2" checked onchange="showHideMonthly(this.checked)"><label><?php _e('Monthly', 'whydonate-v2') ?></label><br>
                                    <input type="checkbox" id="select-interval-yearly" name="select-interval-yearly" value="3" checked onchange="showHideYearly(this.checked)"><label><?php _e('Yearly', 'whydonate-v2') ?></label><br><br>
                                </div>
                                <label id="show-interval-error" style="display: none"></label>
                            </div>


                            <div class="customise-branding" id="customise-branding">

                                <label for="customise-braning-label" style="margin-top: 25px; font-size: 18px;"><?php _e('Branding options', 'whydonate-v2') ?></label>

                                <div class="set-color" id="set-color" style="margin-top: 20px;">
                                    <label for="set-color-label" style="font-size: 14px; font-weight: 600"><?php _e('Color', 'whydonate-v2') ?></label>
                                    <span>
                                        <input type="text" id="set-color-input-field" class="set-color-input-field" name="set-color-input-field" value="#2828d6" style="margin-top: 10px;margin-left: 15px;width: 100px;" onchange="changeColor(this.value)">
                                        <input type="color" id="set-color-input" name="set-color-input" value="#2828d6" onchange="changeColor(this.value)">
                                    </span>
                                </div>

                                <div class="set-font" id="set-font" style="margin-top: 20px; display: none">
                                    <label for="set-font-label" style="font-size: 14px; font-weight: 600"><?php _e('Font', 'whydonate-v2') ?></label>
                                    <input type="text" id="set-font-input" name="set-font-input" style="display: none; margin-left: 15px;" onkeydown="setFont(this.value)" onfocusout="setFont(this.value) ">
                                    <select id="set-font-input-list" name="selected-font" onchange="setFont(this)" style="margin-left: 10px;">
                                        <?php
                                        foreach ($font_list as $font)
                                            echo '<option value=' . $font . '>' . $font . '</option>';
                                        ?>
                                    </select>
                                </div>

                                <div class="set-button-radius" id="set-button-radius" style="margin-top: 20px;">
                                    <label for="set-button-radius-label" style="font-size: 14px; font-weight: 600"><?php _e('Button Radius', 'whydonate-v2') ?></label>
                                    <input type="range" min="0" max="30" value="30" id="set-button-radius-input" name="set-button-radius-input" style="margin-left: 15px;" onchange="changeButtonRadius(this.value)">
                                </div>

                            </div>

                            <div style="margin-top: 20px">
                                <label for="donation-form-label" style="font-size: 14px; font-weight: 600"><?php _e('Donation form title', 'whydonate-v2') ?></label>
                                <input type="text" id="set-donation-form" name="set-donation-form" style="margin-left: 15px; width: 250px" onchange="changeDonationLabel(this.value)">
                            </div>

                        </div>

                        <button id="set-plugin-style" class="btn-style" style="float: none;
                margin-top: 20px;
                margin-left: 0px;
                height: 40px;
                width: 80px;"><?php _e('Save', 'whydonate-v2') ?></button>

                        <!-- </form> -->

                    </div>

                </div>
            </div>

            <div class="right-side" style="display: flex; flex-grow: 1">
                <label for="appearance-preview-label" style="font-size: 14px; font-weight: 600;"><?php _e('Widget options', 'whydonate-v2') ?></label>
                <div class="appearance-preview-config" id="appearance-preview">

                    <div class="appearance-preview-card-style-config" id="appearance-preview-card" style="height: 220px">
                        <div class="apreview-donation-time" id="apreview-donation-time">
                            <label for="apreview-donation-time"></label>
                        </div>
                        <div class="apreview-amount-raised" id="apreview-amount-raised">
                            <div class="apreview-collected-amount-div" id="apreview-collected-amount-div">
                                <label for="apreview-collected-amount">€ 23.505</label>
                            </div>
                            <div class="apreview-target-amount-of-div" id="apreview-target-amount-of-div">
                                <label for="apreview-target-amount"><?php _e('of', 'whydonate-v2') ?></label>
                            </div>
                            <div class="apreview-target-amount-div" id="apreview-target-amount-div">
                                <label for="apreview-target-amount" style="margin-left: 10px">€ 80.000</label>
                            </div>
                        </div>

                        <div class="apreview-progress-bar" id="apreview-progress-bar" style="margin-top: 5px;">
                            <div class="apreview-full-bar" id="apreview-full-bar">
                                <div class="apreview-raised-bar">
                                </div>
                            </div>
                            <div class="apreview-progress-info" id="apreview-progress-info">
                                <div class="apreview-achived-percent" id="apreview-achived-percent">
                                    <label>29.4% <?php _e('funded', 'whydonate-v2') ?></label>
                                </div>
                                <div class="apreview-days-left" id="apreview-days-left">
                                    <label>96 <?php _e('day(s) left', 'whydonate-v2') ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="apreview-donate-btn-div" id="apreview-donate-btn-div">
                            <button class="apreview-donate-btn" id="apreview-donate-btn" style="border: none"><?php _e('Donate', 'whydonate-v2') ?></button>
                        </div>

                    </div>
                </div>

                <label for="preview-label" style="font-size: 14px; font-weight: 600;"><?php _e('Donation form appearance', 'whydonate-v2') ?></label>

                <div class="preview-style" id="preview-style">

                    <div class="preview-card-config" id="preview-card" style="height: 580px">
                        <div class="preview-header" id="preview-header">
                            <div class="preview-header-label-div" id="preview-header-label-div">
                                <label for="preview-header-label" id="preview-header-label"> My Safe Donation</label>
                            </div>
                            <div class="preview-header-close-btn" id="preview-header-close-btn">
                                <button style="border: none"><i class="material-icons">close</i></button>
                            </div>
                        </div>

                        <div class="preview-period-intervals" id="preview-period-intervals">
                            <div class="preview-intervals-onetime" id="preview-intervals-onetime">
                                <h4><?php _e('One Time', 'whydonate-v2') ?></h4>
                            </div>
                            <div class="preview-intervals-monthly" id="preview-intervals-monthly">
                                <h4><?php _e('Monthly', 'whydonate-v2') ?></h4>
                            </div>
                            <div class="preview-intervals-yearly" id="preview-intervals-yearly">
                                <h4><?php _e('Yearly', 'whydonate-v2') ?></h4>
                            </div>
                        </div>

                        <div class="preview-intervals-divider" id="preview-intervals-divider">
                            <div class="preview-intervals-onetime-bar" id="preview-intervals-onetime-bar">
                            </div>
                            <div class="preview-intervals-monthly-bar" id="preview-intervals-onetime-bar">
                            </div>
                            <div class="preview-intervals-yearly-bar" id="preview-intervals-onetime-bar">
                            </div>
                        </div>

                        <div class="preview-select-amount" id="preview-select-amount">
                            <h4><?php _e('Select Amount', 'whydonate-v2') ?></h4>

                            <div class="preview-select-amount-options" id="preview-select-amount-options">
                                <div class="amount-boundary-box-1" id="amount-boundary-box-1" style="width: 60px">
                                    <p id="amount-boundary-box-1-p" id="amount-boundary-box-1-p" style="line-height: 0.5; font-size: 16px; font-weight: 500">€ 25</p>
                                </div>
                                <div class="amount-boundary-box-2" id="amount-boundary-box-2" style="width: 60px">
                                    <p id="amount-boundary-box-2-p" id="amount-boundary-box-2-p" style="line-height: 0.5; font-size: 16px; font-weight: 500">€ 50</p>
                                </div>
                                <div class="amount-boundary-box-3" id="amount-boundary-box-3" style="width: 60px">
                                    <p id="amount-boundary-box-3-p" id="amount-boundary-box-3-p" style="line-height: 0.5; font-size: 16px; font-weight: 500">€ 75</p>
                                </div>
                                <div class="amount-boundary-box-4" id="amount-boundary-box-4" style="width: 60px">
                                    <p id="amount-boundary-box-4-p" id="amount-boundary-box-4-p" style="line-height: 0.5; font-size: 16px; font-weight: 500">€ 100</p>
                                </div>
                                <div class="amount-boundary-box-other" id="amount-boundary-box-other" style="width: 60px">
                                    <p id="amount-boundary-box-other-p" id="amount-boundary-box-other-p" style="line-height: 0.5; font-size: 16px; font-weight: 500"><?php _e('Other', 'whydonate-v2') ?></p>
                                </div>
                            </div>

                            <div class="preview-user-info-div" id="review-user-info-div">
                                <div class="preview-user-info-firstname" id="preview-user-info-firstname" style="width: 97%">
                                    <p style="line-height: 0.5; font-size: 16px"><?php _e('First name', 'whydonate-v2') ?></p>
                                </div>
                                <div class="preview-user-info-lastname" id="preview-user-info-lastname" style="width: 97%">
                                    <p style="line-height: 0.5; font-size: 16px"><?php _e('Last name', 'whydonate-v2') ?></p>
                                </div>
                                <div class="preview-user-info-email" id="preview-user-info-email" style="width: 97%">
                                    <p style="line-height: 0.5; font-size: 16px"><?php _e('Email', 'whydonate-v2') ?></p>
                                </div>
                            </div>

                            <div class="preview-user-donate-btn-div" id="preview-user-donate-btn-div" style="margin-top: 20px">
                                <div class="checkbox-if-anonymous">
                                    <input type="checkbox" style="margin-left: 10px">
                                    <label for="if-anonymous" style="font-size: 14px;"><?php _e('Donate', 'whydonate-v2')
                                                                                        ?><strong style="margin-left: 3px"><?php _e('Anonymous', 'whydonate-v2') ?></strong></label>
                                </div>

                                <div class="preview-donate-btn-div" id="preview-donate-btn-div">
                                    <button class="preview-donate-btn" id="preview-donate-btn" style="border: none"><?php _e('Donate', 'whydonate-v2') ?></button>
                                </div>
                                <div class="preview-user-decalration-term" style="text-align: center">
                                    <label for="if-anonymous" style="font-size: 12px;"><?php _e('By donate you agree to our <a href="https://www.whydonate.nl/terms-and-conditions/?lang=en" target="blank">Terms & Conditions</a>', 'whydonate-v2') ?></label>
                                </div>
                                <div style="display: flex; width: 100%; height: 20px; justify-content: flex-end; margin-top: 30px">
                                    <label style="font-size: 12px; margin-top: 5px; margin-right: 5px; font-weight: 500"><?php _e('Powered by ', 'whydonate-v2') ?></label>
                                    <img src="https://www.whydonate.nl/wp-content/uploads/2020/03/logo_whydonate.svg" style="height: 100%; margin-top: 5px" />
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <div class="modal" id="modal">
        <!-- Place at bottom of page -->
    </div>
</div>