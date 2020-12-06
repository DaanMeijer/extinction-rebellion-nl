<?php

function enqueue_styles()
{

    wp_enqueue_style('shortcode_style', plugin_dir_url(__FILE__) . '../admin/css/wdplugin-style.css', false, version_id());

    // wp_enqueue_style('google-fonts', "https://fonts.googleapis.com/css?family=Abel|Abril+Fatface|Acme|Alegreya|Alegreya+Sans|Anton|Archivo|Archivo+Black|Archivo+Narrow|Arimo|Arvo|Asap|Asap+Condensed|Beth+Ellen|Bitter|Bowlby+One+SC|Bree+Serif|Cabin|Cairo|Catamaran|Crete+Round|Crimson+Text|Cuprum|Dancing+Script|Dosis|Droid+Sans|Droid+Serif|EB+Garamond|Exo|Exo+2|Faustina|Fira+Sans|Fjalla+One|Francois+One|Gloria+Hallelujah|Hind|Inconsolata|Indie+Flower|Josefin+Sans|Julee|Karla|Lato|Libre+Baskerville|Libre+Franklin|Lobster|Lora|Mada|Manuale|Maven+Pro|Merriweather|Merriweather+Sans|Montserrat|Montserrat+Subrayada|Mukta+Vaani|Muli|Noto+Sans|Noto+Serif|Nunito|Open+Sans|Open+Sans+Condensed:300|Oswald|Oxygen|PT+Sans|PT+Sans+Caption|PT+Sans+Narrow|PT+Serif|Pacifico|Passion+One|Pathway+Gothic+One|Play|Playfair+Display|Poppins|Questrial|Quicksand|Raleway|Ranga|Roboto|Roboto+Condensed|Roboto+Mono|Roboto+Slab|Ropa+Sans|Rubik|Saira|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Sedgwick+Ave|Sedgwick+Ave+Display|Shadows+Into+Light|Signika|Slabo+27px|Source+Code+Pro|Source+Sans+Pro|Spectral|Titillium+Web|Ubuntu|Ubuntu+Condensed|Varela+Round|Vollkorn|Work+Sans|Yanone+Kaffeesatz|Zilla+Slab|Zilla+Slab+Highlight", false);

    wp_enqueue_script('shortcode_script', plugin_dir_url(__FILE__) . '../admin/js/shortcode.js', array('jquery'), version_id(), true);

    // wp_enqueue_script('shortcode_script', plugin_dir_url(__FILE__) . '../admin/js/shortcode.js', array('jquery'), true);

    wp_localize_script(
        'shortcode_script',
        'my_ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );
}

function get_fundraiser_info($slug)
{

    #$url = 'http://127.0.0.1:8000/api/v1/project/fundraising/local/user/';

    // $url = 'https://whydonate-development.appspot.com/api/v1/project/fundraising/local/?slug=' . $slug . '&client=whydonate-staging';

    $url = 'https://whydonate-production-api.appspot.com/api/v1/project/fundraising/local/?slug=' . $slug . '&client=whydonate-production';

    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");

    if (count($result) > 0) {
        global $wpdb;
        $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = 1");

        if (!empty($retrieve_data)) {
            $apiKey = '';
            foreach ($retrieve_data as $retrieved_data) {
                $apiKey = $retrieved_data->apiKey;
            }

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'API-KEY: ' . $apiKey
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
    } else {
        return 'Please set an api key first';
    }

    wp_die();
}

function shortcode_func($atts)
{
    ob_start();
    enqueue_styles();
    $a = shortcode_atts(array(
        'id' => $atts['id'],
    ), $atts);
    // echo $a['id'] . '<br>';
    $id = $a['id'];
    global $wpdb;
    //$widget_table = "wp_wdplugin_config_widget";
    $widget_table = $wpdb->prefix . "wdplugin_config_widget";
    //$styles_table = "wp_wdplugin_style";
    $styles_table = $wpdb->prefix . "wdplugin_style";
    $retrieve_data = $wpdb->get_results("SELECT * FROM $widget_table WHERE shortcode = '$id'");

    $fundraiserName = '';
    $styleId = 0;
    $pluginStyle = '';
    $colorCode = '';
    $showDonateButton = 0;
    $showProgressBar = 0;
    $showRaisedAmount = 0;
    $showEndDate = 0;
    $showDonationFormOnly = 0;
    $doNotShowBox = 0;
    $oneTimeCheck = 0;
    $monthlyCheck = 0;
    $yearlyCheck = 0;
    $firstAmountCheck = 0;
    $secondAmountCheck = 0;
    $thirdAmountCheck = 0;
    $forthAmountCheck = 0;
    $otherChecked = 0;
    $firstAmount = 0;
    $secondAmount = 0;
    $thirdAmount = 0;
    $forthAmount = 0;
    $slug = '';
    $font = '';
    $flocalId = 0;
    $progress_bar = 0.0;
    $progress_bar_width = 0.0;
    $achived_per = 0.0;
    $data = array();
    $appearanceWindowHeight = 230;
    $selectInterval = 0;
    $selectAmount = 0;
    $redirectUrl = '';
    $can_receive_donation = true;
    $donationTitle = '';
    $elapsed = '';
    $fundraiserTitle = '';
    $successUrl = '';
    $failureUrl = '';
    $buttonRadius = 30;
    $tip_enabled = true;
    $language_code = '';


    if (!empty($retrieve_data)) {

        foreach ($retrieve_data as $retrieved_data) {
            $fundraiserName = $retrieved_data->fundraiserName;
            $pluginStyle = $retrieved_data->pluginStyle;
            $slug = $retrieved_data->slugName;
            $flocalId = $retrieved_data->slugId;
            $styleId = $retrieved_data->styleId;
            $successUrl = $retrieved_data->successUrl;
            $failureUrl = $retrieved_data->failureUrl;
        }
    }

    if ($pluginStyle == 'Default' || $pluginStyle == 'Standaard' || $pluginStyle == 'Selecteer een stijl') {
        $font = '';
        $colorCode = '#2828d6';
        $showDonateButton = 1;
        $showProgressBar = 2;
        $showRaisedAmount = 3;
        $showEndDate = 4;
        $showDonationFormOnly = 0;
        $doNotShowBox = 0;
        $oneTimeCheck = 1;
        $monthlyCheck = 2;
        $yearlyCheck = 3;
        $firstAmountCheck = 1;
        $secondAmountCheck = 2;
        $thirdAmountCheck = 3;
        $forthAmountCheck = 4;
        $otherChecked = 1;
        $firstAmount = 25;
        $secondAmount = 50;
        $thirdAmount = 75;
        $forthAmount = 100;
        $donationTitle = 'My Safe Donation';
    } else {
        $retrieve_styles = $wpdb->get_results("SELECT * FROM $styles_table WHERE id = $styleId");

        // print_r($styleId);
        // print_r($retrieve_styles);

        if (!empty($retrieve_styles)) {

            foreach ($retrieve_styles as $style_data) {
                $font = $style_data->font;
                $colorCode = $style_data->colorCode;
                $showDonateButton = $style_data->showDonateButton;
                $showProgressBar = $style_data->showProgressBar;
                $showRaisedAmount = $style_data->showRaisedAmount;
                $showEndDate = $style_data->showEndDate;
                $showDonationFormOnly = $style_data->showDonationFormOnly;
                $doNotShowBox = $style_data->doNotShowBox;
                $oneTimeCheck = $style_data->oneTimeCheck;
                $monthlyCheck = $style_data->monthlyCheck;
                $yearlyCheck = $style_data->yearlyCheck;
                $firstAmountCheck = $style_data->firstAmountCheck;
                $secondAmountCheck = $style_data->secondAmountCheck;
                $thirdAmountCheck = $style_data->thirdAmountCheck;
                $forthAmountCheck = $style_data->forthAmountCheck;
                $otherChecked = $style_data->otherChecked;
                $firstAmount = $style_data->firstAmount;
                $secondAmount = $style_data->secondAmount;
                $thirdAmount = $style_data->thirdAmount;
                $forthAmount = $style_data->forthAmount;
                $donationTitle = $style_data->donationTitle;
                $buttonRadius = $style_data->buttonRadius;
            }
            // var_dump($buttonRadius);
            // var_dump($retrieve_styles);
            // var_dump($showDonateButton);
            // var_dump($showProgressBar);
            // var_dump($showRaisedAmount);
        }
    }

    if ($oneTimeCheck != 0) {
        $selectInterval = 1;
    } else if ($monthlyCheck != 0) {
        $selectInterval = 2;
    } else {
        $selectInterval = 3;
    }

    if ($firstAmountCheck != 0) {
        $selectAmount = 1;
    } else if ($secondAmountCheck != 0) {
        $selectAmount = 2;
    } else if ($thirdAmountCheck != 0) {
        $selectAmount = 3;
    } else if ($forthAmountCheck != 0) {
        $selectAmount = 4;
    } else {
        $selectAmount = 5;
    }


    if ($showRaisedAmount == 0 || $showProgressBar == 0) {
        $appearanceWindowHeight = 200;
    }

    if ($showRaisedAmount == 0 && $showProgressBar == 0) {
        $appearanceWindowHeight = 150;
    }
    #echo $slug;
    // var_dump($slug);
    $response = get_fundraiser_info($slug);
    //var_dump($response);
    // // var_dump($data['donation']['amount']);
    // // var_dump($data['amount_target']);




    if (!empty($response)) {
        $data = json_decode($response, true);
        // var_dump($data);
        $fundraiserTitle = addslashes($data['data']['title']);
        // print_r(get_locale());

        if ($data['data']['tip_enabled']) {
            $tip_enabled = $data['data']['tip_enabled'];
        } else {
            $tip_enabled = 0;
        }
        //print_r($data['data']['title']);
        //print_r($data['data']['description']);

        if (array_key_exists("amount_target", $data['data'])) {

            if ($data['data']['amount_target'] != 0) {
                $raw_value = $data['data']['donation']['amount'] / $data['data']['amount_target'] * 100;

                $progress_bar = number_format($raw_value, 2, '.', ' ');

                if ($raw_value > 100) {
                    $progress_bar_width = 100;
                } else {
                    $progress_bar_width = $progress_bar;
                }
            }
        }

        #echo $progress_bar . '<br>';

        // var_dump($data['donation']['amount']);
        // var_dump($data['amount_target']);
        //var_dump($showRaisedAmount);
    }

    $today = new DateTime();
    $end_date = new DateTime();
    if (array_key_exists("end_date", $data['data'])) {
        $end_date = new DateTime($data['data']['end_date']);
        if ($end_date < $today) {
            $elapsed = 'closed';
        } else {
            $interval = $today->diff($end_date);
            $elapsed =  $interval->format('%a');;
            if ($elapsed < 1000) {
                $elapsed = $interval->format('%a');
            } else {
                $elapsed = '';
            }
        }
    }

    if (!empty($response)) {
        if (array_key_exists('can_receive_donation', $data['data'])) {
            if ($data['data']['can_receive_donation'] == false || $elapsed == 'closed') {
                $colorCode = '#D3D3D3';
            }
        }
    }


    $btnId = 'apreview-donate-btn-' . $pluginStyle;
    $modalId = 'donate-window-modal-' . $pluginStyle;

    // echo $font  . '<br>';
    // echo $pluginStyle  . '<br>';
    // echo $colorCode  . '<br>';
    $language_code = get_locale();

?>
    <!-- <h1>Hello PHP</h1> -->

    <div class="style-container-shortcode">

        <div class="appearance-preview" id="appearance-preview" style="display : <?php $showDonationFormOnly == 1 ? print('none') : print('flex') ?>">

            <div class="appearance-preview-card-shortcode" id="appearance-preview-card-shortcode" style="height: <?php echo $appearanceWindowHeight . "px;" ?> background: <?php $doNotShowBox == 1 ? print('transparent;') : print('rgb(248, 245, 245);') ?> box-shadow: <?php $doNotShowBox == 1 ? print('none;') : print('0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);') ?>">
                <div class="apreview-donation-time" id="apreview-donation-time" style="font-family: <?php echo $font  ?>">
                    <!-- <label for="apreview-donation-time">Last donation 27m ago</label> -->
                </div>

                <div class="apreview-amount-raised" id="apreview-amount-raised" style="align-items: baseline">
                    <?php if (array_key_exists("donation", $data['data']) && array_key_exists("amount_target", $data['data'])) { ?>
                        <?php if ($showRaisedAmount == 3) { ?>
                            <div class="apreview-collected-amount-div" id="apreview-collected-amount-div" style="font-family: <?php echo $font  ?>">
                                <label for="apreview-collected-amount" style="display: block; font-size: 32px">€ <?php echo $data['data']['donation']['amount']  ?></label>
                            </div>
                        <?php } ?>
                        <?php if ($showProgressBar == 2) { ?>
                            <?php if ($data['data']['amount_target'] != 0) { ?>
                                <div class="apreview-target-amount-div" id="apreview-target-amount-div" style="font-family: <?php echo $font  ?>; font-weight: 300; display: flex">
                                    <?php if ($showProgressBar == 2 && $showRaisedAmount == 3) { ?>
                                        <div class="apreview-target-amount-div-label-1" id="apreview-target-amount-div-label-1">
                                            <label id="apreview-target-amount-of" style="color: gray; display: block; font-size: 22px"> <?php _e('of', 'whydonate-v2') ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <div class="apreview-target-amount-div-label-2" id="apreview-target-amount-div-label-2">
                                        <label for="apreview-target-amount" style="color: gray; margin-left: 10px; display: block; font-size: 22px"> €
                                            <?php echo $data['data']['amount_target'] ?></label>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                </div>
            <?php } else { ?> <h1><?php _e('No Data Found', 'whydonate-v2') ?></h1> <?php } ?>


            <div class="apreview-progress-bar" id="apreview-progress-bar">
                <?php if (array_key_exists("amount_target", $data['data'])) { ?>
                    <?php if ($showProgressBar == 2) { ?>
                        <?php if ($data['data']['amount_target'] != 0) { ?>
                            <div class="apreview-full-bar">
                                <div class="apreview-raised-bar" style="width: <?php echo $progress_bar_width . '%' ?>">
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <div class="apreview-progress-info" id="apreview-progress-info" style="font-family: <?php echo $font  ?>">
                        <?php if ($showProgressBar == 2) { ?>
                            <?php if ($data['data']['amount_target'] != 0) { ?>
                                <div class="apreview-achived-percent">
                                    <label style="font-size: 14px; font-weight: 300; color: gray; display: block"><?php echo $progress_bar ?>%
                                        <?php _e('funded', 'whydonate-v2') ?></label>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($showEndDate == 4) { ?>
                            <div class="apreview-days-left" style="font-family: <?php echo $font  ?>">
                                <label style="display: block; font-size: 14px; font-weight: 300; color: gray"><?php $data['data']['can_receive_donation'] == true && $elapsed != 'closed' ?  print($elapsed) : printf('')
                                                                                                                ?>
                                    <?php $data['data']['can_receive_donation'] == true && $elapsed != 'closed' ? _e("day(s) left", "whydonate-v2") : _e('closed', 'whydonate-v2') ?></label>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?> <h1><?php _e('No Data Found', 'whydonate-v2') ?></h1> <?php } ?>
            </div>

            <?php if ($showDonateButton == 1) { ?>
                <div class="apreview-donate-btn-div" id="apreview-donate-btn-div" style="font-family: <?php echo $font  ?>">
                    <?php if (array_key_exists("donation", $data['data']) && array_key_exists("amount_target", $data['data'])) { ?>
                        <button class="apreview-donate-btn" id="apreview-donate-btn-<?php echo $id ?>" onclick="showDonateWindow('<?php echo $id ?>', <?php echo $tip_enabled ?>, '<?php echo $colorCode ?>', '<?php echo $language_code ?>')" style="font-family: <?php echo $font  ?>; background-color: <?php echo $colorCode ?>; border-radius: <?php echo $buttonRadius ?>px" <?php if ($data['data']['can_receive_donation'] === false || $elapsed === 'closed') { ?> disabled <?php   } ?>><?php _e('Donate', 'whydonate-v2') ?></button>
                    <?php } ?>
                    <?php
                    if (array_key_exists('can_receive_donation', $data['data'])) {
                        if ($data['data']['can_receive_donation'] == false || $elapsed == 'closed') {
                    ?>
                            <div style="text-align: center; margin-top: 5px; ">
                                <label style="display: block; color: gray;  font-size: 12px"><?php _e('Fundraiser is either closed by time or owner closed it!', 'whydonate-v2') ?></label>
                            </div>

                    <?php }
                    }

                    ?>
                </div>
            <?php } ?>

            </div>
        </div>

        <!-- The Modal -->
        <div id="donate-window-modal-<?php echo $id ?>" class="donate-window-modal" style="z-index: <?php $showDonationFormOnly == 1 ? print('0;') : print('900;') ?> display: <?php $showDonationFormOnly == 1 ? print('flex;') : print('none;') ?> position: <?php $showDonationFormOnly == 1 ? print('static;') : print('fixed;') ?> background-color: <?php $showDonationFormOnly == 1 ? print('transparent;') : print('rgb(0,0,0,0.4);') ?> padding-top: <?php $showDonationFormOnly == 1 ? print('0px;') : print('100px;') ?>">

            <!-- Modal content -->
            <div class="donate-window-content">


                <div class="preview" id="preview-<?php echo $id ?>" style="margin-left: <?php $showDonationFormOnly == 1 ? print('0px;') : print('30px;') ?>">


                    <div class="preview-card" id="preview-card-<?php echo $id ?>">
                        <div class="preview-header" id="preview-header-<?php echo $id ?>" style="background-color: <?php echo $colorCode ?>;">
                            <div class="preview-header-label-div" id="preview-header-label-div-<?php echo $id ?>" style="font-family: <?php echo $font ?>">
                                <label for="preview-header-label-<?php echo $id ?>" style="color: white; display: block">
                                    <?php echo $donationTitle ?> </label>
                                <label id="preview-header-flocaldId-<?php echo $id ?>" style="display: none"><?php echo $flocalId ?></label>
                            </div>
                            <?php
                            if ($showDonationFormOnly != 1) {
                            ?>
                                <span class="close" id="<?php echo $id ?>">&times;</span>
                            <?php
                            }
                            ?>
                        </div>

                        <div class="preview-period-intervals" id="preview-period-intervals-<?php echo $id ?>">
                            <?php if ($oneTimeCheck == 1) {
                            ?>
                                <div class="preview-intervals-onetime" id="preview-intervals-onetime-<?php echo $id ?>" style="padding-top : 20px;">
                                    <input type='radio' value='onetime' name='period-intervals-<?php echo $id ?>' id='onetime-radio-<?php echo $id ?>' onchange="changeOnetimeBar('<?php echo $colorCode ?>', '<?php echo $id ?>')" <?php echo ($selectInterval == 1 ? 'checked' : '') ?> />
                                    <label for='onetime-radio-<?php echo $id ?>' style="display: block; font-family: <?php echo $font ?>; font-size: 14px;"><?php _e('One Time', 'whydonate-v2') ?></label>
                                </div>
                            <?php }
                            ?>

                            <?php if ($monthlyCheck == 2) {
                            ?>
                                <div class="preview-intervals-monthly" id="preview-intervals-monthly-<?php echo $id ?>" style="padding-top : 20px;">
                                    <input type='radio' value='monthly' name='period-intervals-<?php echo $id ?>' id='monthly-radio-<?php echo $id ?>' onchange="changeMonthlyBar('<?php echo $colorCode ?>', '<?php echo $id ?>')" <?php echo ($selectInterval == 2 ? 'checked' : '') ?> />
                                    <label for='monthly-radio-<?php echo $id ?>' style="font-family: <?php echo $font ?>; font-size: 14px; display: block"><?php _e('Monthly', 'whydonate-v2') ?></label>
                                </div>
                            <?php }
                            ?>
                            <?php if ($yearlyCheck == 3) {
                            ?>
                                <div class="preview-intervals-yearly" id="preview-intervals-yearly-<?php echo $id ?>" style="padding-top : 20px;">
                                    <input type='radio' value='yearly' name='period-intervals-<?php echo $id ?>' id='yearly-radio-<?php echo $id ?>' onchange="changeYearlyBar('<?php echo $colorCode ?>', '<?php echo $id ?>')" <?php echo ($selectInterval == 3 ? 'checked' : '') ?> />
                                    <label for='yearly-radio-<?php echo $id ?>' style="font-family: <?php echo $font ?>; font-size: 14px; display: block"><?php _e('Yearly', 'whydonate-v2') ?></label>
                                </div>
                            <?php }
                            ?>
                        </div>
                        <div class="preview-intervals-divider" id="preview-intervals-divider-<?php echo $id ?>">
                            <?php if ($oneTimeCheck == 1) {
                            ?>
                                <div class="preview-intervals-onetime-bar" id="preview-intervals-onetime-bar-<?php echo $id ?>" style="background-color: <?php echo ($selectInterval == 1 ? $colorCode : '') ?>">
                                </div>
                            <?php }
                            ?>
                            <?php if ($monthlyCheck == 2) {
                            ?>
                                <div class="preview-intervals-monthly-bar" id="preview-intervals-monthly-bar-<?php echo $id ?>" style="background-color: <?php echo ($selectInterval == 2 ? $colorCode : '') ?>">
                                </div>
                            <?php }
                            ?>

                            <?php if ($yearlyCheck == 3) {
                            ?>
                                <div class="preview-intervals-yearly-bar" id="preview-intervals-yearly-bar-<?php echo $id ?>" style="background-color: <?php echo ($selectInterval == 3 ? $colorCode : '') ?>">
                                </div>
                            <?php }
                            ?>
                        </div>

                        <div class="preview-select-amount-s" id="preview-select-amount-s-<?php echo $id ?>">
                            <label style="margin-top: 20px; font-family: <?php echo $font ?>; font-size: 14px; font-weight: 400; display: block"><?php _e('Select Amount', 'whydonate-v2') ?></label>

                            <div class="preview-select-amount-options" id="preview-select-amount-options-<?php echo $id ?>" style="padding-top: 15px;">

                                <?php if ($firstAmountCheck == 1) { ?>
                                    <div class="amount-boundary-box-1-s" id="amount-boundary-box-1-s-<?php echo $id ?>" style="text-align: center; position: relative;  background-color: <?php echo ($selectAmount == 1 ? $colorCode : '') ?>">
                                        <!-- <p id="amount-boundary-box-1-p">€25</p> -->
                                        <input type='radio' value='<?php echo $firstAmount ?>' <?php echo ($selectAmount == 1 ? 'checked' : '') ?> name='select-amount-<?php echo $id ?>' id='select-amount-first-<?php echo $id ?>' onchange="selectFirstAmount('<?php echo $colorCode ?>', '<?php echo $id ?>', <?php echo $tip_enabled ?>)" style="margin-left: -5px; z-index: 10" />
                                        <label for='select-amount-first-<?php echo $id ?>' style="display: block; position: absolute;margin-top: 8px; font-family: <?php echo $font ?>; font-size: 16px; font-weight: 600"><?php echo '€ ' . $firstAmount ?></label>
                                    </div>
                                <?php } ?>

                                <?php if ($secondAmountCheck == 2) { ?>
                                    <div class="amount-boundary-box-2-s" id="amount-boundary-box-2-s-<?php echo $id ?>" style="text-align: center; position: relative;  background-color: <?php echo ($selectAmount == 2 ? $colorCode : '') ?>">
                                        <!-- <p id="amount-boundary-box-2-p">€50</p> -->
                                        <input type='radio' value='<?php echo $secondAmount ?>' <?php echo ($selectAmount == 2 ? 'checked' : '') ?> name='select-amount-<?php echo $id ?>' id='select-amount-second-<?php echo $id ?>' onchange="selectSecondAmount('<?php echo $colorCode ?>', '<?php echo $id ?>', <?php echo $tip_enabled ?>)" style="margin-left: -5px; z-index: 10" />
                                        <label for='select-amount-second-<?php echo $id ?>' style="display: block; position: absolute;margin-top: 8px; font-family: <?php echo $font ?>; font-size: 16px; font-weight: 600"><?php echo '€ ' . $secondAmount ?></label>
                                    </div>
                                <?php } ?>

                                <?php if ($thirdAmountCheck == 3) { ?>
                                    <div class="amount-boundary-box-3-s" id="amount-boundary-box-3-s-<?php echo $id ?>" style="text-align: center; position: relative;  background-color: <?php echo ($selectAmount == 3 ? $colorCode : '') ?>">
                                        <!-- <p id="amount-boundary-box-3-p">€75</p> -->
                                        <input type='radio' value='<?php echo $thirdAmount ?>' <?php echo ($selectAmount == 3 ? 'checked' : '') ?> name='select-amount-<?php echo $id ?>' id='select-amount-third-<?php echo $id ?>' onchange="selectThirdAmount('<?php echo $colorCode ?>', '<?php echo $id ?>', <?php echo $tip_enabled ?>)" style="margin-left: -5px; z-index: 10" />
                                        <label for='select-amount-third-<?php echo $id ?>' style="display: block; position: absolute;margin-top: 8px; font-family: <?php echo $font ?>; font-size: 16px; font-weight: 600"><?php echo '€ ' . $thirdAmount ?></label>
                                    </div>
                                <?php } ?>

                                <?php if ($forthAmountCheck == 4) { ?>
                                    <div class="amount-boundary-box-4-s" id="amount-boundary-box-4-s-<?php echo $id ?>" style="text-align: center; position: relative; background-color: <?php echo ($selectAmount == 4 ? $colorCode : '') ?>">
                                        <!-- <p id="amount-boundary-box-4-p">€100</p> -->
                                        <input type='radio' value='<?php echo $forthAmount ?>' <?php echo ($selectAmount == 4 ? 'checked' : '') ?> name='select-amount-<?php echo $id ?>' id='select-amount-forth-<?php echo $id ?>' onchange="selectForthAmount('<?php echo $colorCode ?>', '<?php echo $id ?>', <?php echo $tip_enabled ?>)" style="margin-left: -5px; z-index: 10" />
                                        <label for='select-amount-forth-<?php echo $id ?>' style="display: block; position: absolute;margin-top: 8px; font-family: <?php echo $font ?>; font-size: 16px; font-weight: 600"><?php echo '€ ' . $forthAmount ?></label>
                                    </div>
                                <?php } ?>

                                <?php if ($otherChecked == 1) { ?>
                                    <div class="amount-boundary-box-other-s" id="amount-boundary-box-other-s-<?php echo $id ?>" style="text-align: center; position:relative; background-color: <?php echo ($selectAmount == 5 ? $colorCode : '') ?>">
                                        <!-- <p id="amount-boundary-box-other-p">Other</p> -->
                                        <input type='radio' value='other' <?php echo ($selectAmount == 5 ? 'checked' : '') ?> name='select-amount-<?php echo $id ?>' id='select-amount-other-<?php echo $id ?>' onchange="selectOtherAmount('<?php echo $colorCode ?>', '<?php echo $id ?>', 
                                        <?php echo $tip_enabled ?>)" style="margin-left: -5px" />
                                        <label for='select-amount-other-<?php echo $id ?>' style="display: block; position:absolute; margin-top: 8px; margin-right: 3px; font-family: <?php echo $font ?>; font-size: 16px; font-weight: 600"><?php _e('Other', 'whydonate-v2') ?></label>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php if ($otherChecked == 1) { ?>
                                <div class="other-amount-div" id="other-amount-div-<?php echo $id ?>" style="display : <?php echo ($selectAmount == 5 ? 'block' : 'none') ?>">
                                    <span style="text-align: center; width: 15px; padding-left: 2px; height: 100%">€ </span>
                                    <input type="number" name="amount" class="other-amount-input-number" placeholder="00,00" id="other-amount-number-<?php echo $id ?>" style="background-color: white; width: 100%; height: 100%; font-family: <?php echo $font ?>; font-size: 14px; border: transparent !important; min-height: auto !important;" onkeyup="handleOtherAmountInput(event.target.value, '<?php echo $id ?>')" onkeydown="if(event.key==='.' ){event.preventDefault();}" oninput="amountInputSpinner(event.target.value, '<?php echo $id ?>')" min="5">
                                </div>
                                <label id="show-other-amount-error-msg-<?php echo $id ?>" style="display: none; font-size: 10px; color: red; visibility: hidden"><?php _e('Minimum amount €5', 'whydonate-v2') ?></label>
                            <?php } ?>

                            <div class=" preview-user-info-div" id="review-user-info-div-<?php echo $id ?>" style="margin-top: 10px;">
                                <div class="preview-user-info-firstname-s" id="preview-user-info-firstname-<?php echo $id ?>">
                                    <!-- <p>First Name</p> -->
                                    <input type="text" id="firstname-<?php echo $id ?>" name="firstName-<?php echo $id ?>" placeholder="<?php _e('First name', 'whydonate-v2') ?>" style="background-color: white; width: 100%; height: 100%; font-family: <?php echo $font ?>; font-size: 14px; border-radius: 0px; border: 1px solid !important">
                                </div>
                                <label id="show-firstname-error-msg-<?php echo $id ?>" style="display: none; font-size: 10px; color: red"><?php _e('Must be between 1 and 30 characters.', 'whydonate-v2') ?></label>
                                <div class="preview-user-info-lastname-s" id="preview-user-info-lastname-<?php echo $id ?>">
                                    <!-- <p>Last Name</p> -->
                                    <input type="text" id="lastname-<?php echo $id ?>" name="lastName-<?php echo $id ?>" placeholder="<?php _e('Last name', 'whydonate-v2') ?>" style="background-color: white; width: 100%; height: 100%; font-family: <?php echo $font ?>; font-size: 14px; border-radius: 0px; border: 1px solid !important">
                                </div>
                                <label id="show-lastname-error-msg-<?php echo $id ?>" style="display: none; font-size: 10px; color: red"><?php _e('Must be between 1 and 30 characters.', 'whydonate-v2') ?></label>
                                <div class="preview-user-info-email-s" id="preview-user-info-email-<?php echo $id ?>">
                                    <!-- <p>Email</p> -->
                                    <input type="text" id="email-<?php echo $id ?>" name="email-<?php echo $id ?>" placeholder="<?php _e('Email', 'whydonate-v2') ?>" style="background-color: white; width: 100%; height: 100%; font-family: <?php echo $font ?>; font-size: 14px; border-radius: 0px; border: 1px solid !important">
                                </div>
                                <label id="show-email-error-msg-<?php echo $id ?>" style="display: none; font-size: 10px; color: red"><?php _e('Please enter a valid email.', 'whydonate-v2') ?></label>
                            </div>

                            <!-- tipbox -->
                            <?php if ($tip_enabled) { ?>
                                <div id="tip-box-<?php echo $id ?>" class="tip-box" style="display: none" data-id="<?php echo $id ?>" data-color="<?php echo $colorCode ?>" data-lang="<?php echo $language_code ?>"></div>
                            <?php } ?>

                            <div class="preview-user-donate-btn-div" id="preview-user-donate-btn-div-<?php echo $id ?>" style="display: flex; flex-direction: column; margin-top: 5px">
                                <div class="checkbox-if-anonymous-s" style="height: 20px; display: flex; flex-direction: row; text-align: left; align-items: center !important; margin-top: 10px !important; justify-content: flex-start">
                                    <div class="checkbox-if-anonymous-s-checkbox" id="checkbox-if-anonymous-s-checkbox">
                                        <input type="checkbox" id="is-anonymous-<?php echo $id ?>" style="margin-left: 5px; width: 20px !important; height: 20px !important">
                                    </div>
                                    <div class="checkbox-if-anonymous-s-div-label" id="checkbox-if-anonymous-s-div-label">
                                        <label for="is-anonymous-<?php echo $id ?>" style="display: block; margin-top: 3px; font-size: 16px; font-weight: 400; font-family: <?php echo $font ?>"><?php _e('Donate', 'whydonate-v2') ?>
                                            <strong> <?php _e('Anonymous', 'whydonate-v2') ?> </strong></label>
                                    </div>
                                </div>
                                <div class="preview-donate-btn-div" id="preview-donate-btn-div" style="font-family: <?php echo $font ?>">
                                    <button class="preview-donate-btn" id="preview-donate-btn-<?php echo $id ?>" style="margin-top: 5px; font-family: <?php echo $font ?>; background-color: <?php echo $colorCode ?>; border-radius: <?php echo $buttonRadius ?>px" <?php if ($data['data']['can_receive_donation'] === false || $elapsed === 'closed') { ?> disabled <?php   } ?> onclick="submitDonation(event, '<?php echo $fundraiserTitle ?>','<?php echo $id ?>', '<?php echo $successUrl ?>',  '<?php echo $failureUrl ?>', <?php echo $tip_enabled ?>)"><?php _e('Donate', 'whydonate-v2') ?></button>
                                </div>
                                <div class="preview-user-decalration-term" style="text-align: center; margin-top: 5px; margin-left: 0px">
                                    <label for="if-anonymous-<?php echo $id ?>" style="font-family: <?php echo $font ?>; font-size: 10px !important; font-weight: 400; display: block; text-align: center"><?php _e('By donate you agree to our <a href="https://www.whydonate.nl/terms-and-conditions/?lang=en" target="blank">Terms and Conditions</a>', 'whydonate-v2') ?></label>
                                </div>
                                <div style="display: flex; width: 100%; height: 20px; justify-content: flex-end; margin-top: 20px; margin-bottom: 10px; align-items: center">
                                    <label style="font-size: 12px; margin-top: 5px; margin-right: 5px; font-weight: 500"><?php _e('Powered by ', 'whydonate-v2') ?></label>
                                    <img src="https://www.whydonate.nl/wp-content/uploads/2020/03/logo_whydonate.svg" style="height: 100%; margin-top: 5px" />
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


<?php
    $output = ob_get_clean();
    return $output;
}
