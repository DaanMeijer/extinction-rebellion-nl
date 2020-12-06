<?php

?>

<div class='container'>

    <div id="connect-account-msg" style="position: absolute; top: 10px; background:khaki; height: 40px; display: none; width: 90%;">
        <img src="<?php echo plugin_dir_url(__FILE__) . 'images/tickmark.png'; ?>" alt="Tick mark">
        <label style="font-size: 18px; margin-left: 10px; margin-top:10px"><?php _e('Your plugin is now connected to this account: ', 'whydonate-v2') ?></label>
        <label id="apikey-owner-email-label" style="font-size: 18px; margin-left: 10px; margin-top:10px"></label>
    </div>

    <h1><?php _e("Account setup", 'whydonate-v2') ?></h1>
    <div class="title-bar">
        <h4><?php _e("How to use this plugin", 'whydonate-v2') ?></h4>
        <div style="width: 80%; height: 1px; background: black; margin-left: 20px; margin-top: -25px;">
        </div>

        <div>
            <p><span><strong><?php _e("1. Connect the plugin to your Whydonate account", "whydonate-v2") ?></strong></span></p>
            <p style="margin-top: -10px;"><span>
                    <strong><?php _e("For Whydonate members: ", "whydonate-v2") ?></strong><?php _e("Open your <a href='https://www.whydonate.eu/profile' style='text-decoration:none' target='blank'> Whydonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page.", "whydonate-v2") ?></span>
            </p>
            <p style="margin-top: -10px;"><span>
                    <strong><?php _e("No Whydonate account yet: ", "whydonate-v2") ?></strong><?php _e('<a href="https://www.whydonate.eu/registration/en" style="text-decoration:none" target="blank">Register for free at Whydonate</a>. Open your <a href="https://www.whydonate.eu/registration/eu" style="text-decoration:none" target="blank">Whydonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page.', 'whydonate-v2') ?></span>
            </p>
        </div>

        <div style="margin-top: 25px">
            <p><span><strong><?php _e("2. Create a Widget", "whydonate-v2") ?></strong></span></p>
            <p style="margin-top: -10px;">
                <span><?php _e("Create a new fundraiser or select an existing fundraiser from your fundraiser list. Change the style as you like and then save this in your widgets.", "whydonate-v2") ?></span>
            </p>
        </div>

        <div style="margin-top: 25px">
            <p><span><strong><?php _e("3. Add the widget to your Website", "whydonate-v2") ?></strong></span></p>
            <p style="margin-top: -10px;">
                <span><?php _e('Copy the "Shortcode" of the widget that you want to place and paste it directly on the page. A shortcode looks like this: [whydonate id = "*****"]', "whydonate-v2") ?></span>
            </p>
        </div>


    </div>

    <form method="post">
        <div class="setup-account">
            <div class="title-bar">
                <h4><?php _e("Account setup", 'whydonate-v2') ?></h4>
                <div style="width: 80%; height: 1px; background: black; margin-left: 20px; margin-top: -25px;">
                </div>
                <fieldset>
                    <div style="display: flex; flex-direction: row; padding-left: 20px; margin-top: 20px ">
                        <div style="flex-grow: 1">
                            <input type="radio" id="whydonate-member" name="whydonate-member" value="yes" checked onclick="ShowHideDiv()"><?php _e("I have a Whydonate account", 'whydonate-v2') ?><br>
                        </div>
                        <div style="flex-grow: 4">
                            <input type="radio" id="whydonate-member" name="whydonate-member" value="no" onclick="ShowHideDiv()"><?php _e("I don't have a Whydonate account", 'whydonate-v2') ?><br>
                        </div>
                    </div>
                </fieldset>

            </div>

            <div class="fundraiser-apikey-div" id="fundraiser-apikey-div" style="display: flex; flex-direction: column">
                <div>
                    <label for="fundraiser-id-label" style="margin-left: 5px"><?php _e('Api Key', 'whydonate-v2') ?></label><br>
                    <input type="text" name="fundraiser-id-input" id="fundraiser-id-input" style="height: 40px; border-radius: 10px;">
                    <p id="blank-apikey-msg" style="display: none; font-weight: bold; color: red"><?php _e('Enter your api-key.', 'whydonate-v2') ?></p>
                    <p id="apikey-error-msg" style="display: none; font-weight: bold; color: red"><?php _e('Your api-key is not valid. We failed to connect the plugin.', 'whydonate-v2') ?></p>
                    <p id="updating-apikey-msg" style="display: none; font-weight: bold; color: green"><?php _e('Updating the api-key...', 'whydonate-v2') ?></p>
                    <p id="update-apikey-success-msg" style="display: none; font-weight: bold; color: green"><?php _e('Update success.', 'whydonate-v2') ?></p>
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . "wdplugin_api_key";
                    $result = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
                    $email = '';

                    if (!empty($result)) {
                        $email = $result[0]['email'];
                        if ($email != "") { ?>
                            <div id="apikey-account" style="display: flex">
                                <p style="font-weight: bold; color: gray"><?php _e('Your plugin is now connected to this account: ', 'whydonate-v2') ?></p>
                                <p id="apikey-account-owner-email" style="font-weight: bold; color: gray; margin-left: 5px"><?php echo $email ?></p>
                            </div>

                    <?php }
                    }

                    ?>


                    <p><?php _e('Open your <a href="https://whydonate.eu/profile" style="text-decoration:none" target="blank"> Whydonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page. ', 'whydonate-v2') ?>
                    </p>
                </div>

                <div style="margin-top: 20px">
                    <button class="btn-style" type="submit" name="submitFundraiserId" id="submit-api-key-btn" style="float: none; margin: 0px; margin-left: 10px; width: 100px"><?php _e('Save', 'whydonate-v2') ?>
                        <!-- <i class="material-icons right">save</i> -->
                    </button>
                    <!-- <button style="margin-left: 10px" onclick="checkDatabase()">Check</button> -->
                </div>

            </div>

            <div id="registration-div" class="registration-div" style="display: none">
                <h3><?php _e('<a href="https://www.whydonate.eu/registration/en" style="text-decoration:none" target="blank">Register for free at Whydonate</a>. Open your <a href="https://whydonate.eu/profile" style="text-decoration:none" target="blank">Whydonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page.', 'whydonate-v2') ?></h3>
            </div>
        </div>

        <br><br>
        <!-- <input type="submit" name="submit" value="Submit"> -->

    </form>

    <div class="modal" id="modal">
        <!-- Place at bottom of page -->
    </div>


</div>