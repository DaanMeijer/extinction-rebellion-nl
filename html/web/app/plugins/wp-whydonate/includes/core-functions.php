<?php // MyPlugin - Core Functionality

// disable direct file access

if (!defined('ABSPATH')) {

    exit;
}

add_shortcode('whydonate', 'shortcode_func');

function fetchDataWithId()
{
    //print_r("hello Shuvo " . $_POST["fundraiser-id-input"]);

    $GLOBALS['dataVariables']['whydonate_id'] = $_POST["fundraiser-id-input"];
    if ($GLOBALS['dataVariables']['whydonate_id'] == null) {
        return;
    } else {
        $GLOBALS['dataVariables']['fundraiser_list'] = ["Option one", "Option Two", "Option Three"];
        // foreach ($GLOBALS['fundraiser_list'] as $item) {
        //     echo $item;
        // }
    }
}

if (isset($_POST['submitFundraiserId'])) {
    fetchDataWithId();
}

// Add New Fundraiser

function addNewFundraiser()
{
    $name = $_POST['add-fundraiser-name-input'];
    $targetAmount = $_POST['set-target-amount-input'];
    $endDate = $_POST['set-end-date-input'];
    //showData($name, $targetAmount, $endDate);
}

if (isset($_POST['addNewFundraiser'])) {
    addNewFundraiser();
}


// function submitApiKey()
// {
//     $apiKey = '';
//     $array = array();
//     if (isset($_POST['fundraiser-id-input'])) {
//         $apiKey = $_POST['fundraiser-id-input'];
//         #array_push($array, $apiKey);

//         $url = 'https://httpbin.org/get';

//         $data = http_build_query(array('ApiKey' => 'Shuvo'));

//         $options = array(
//             'http' => array(
//                 'header'  => "Api-key: $apiKey",
//                 'method'  => 'GET',
//                 'content' => $data,
//             ),
//         );

//         $context = stream_context_create($options);

//         file_get_contents($url, false, $context);

//         $status_code = var_dump(getallheaders());

//         array_push($array, $status_code);
//     }
//     showData($array);
// }

// if (isset($_POST['submitFundraiserId'])) {
//     submitApiKey();
// }

function set_new_style()
{

    // try {
    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_style";
    $array = array();
    $styleName = '';
    $oneTimeCheck = 0;
    $monthlyCheck = 0;
    $yearlyCheck = 0;
    $firstAmountCheck = 0;
    $secondAmountCheck = 0;
    $thirdAmountCheck = 0;
    $forthAmountCheck = 0;
    $firstAmount = 0;
    $secondAmount = 0;
    $thirdAmount = 0;
    $forthAmount = 0;
    $otherChecked = 0;
    $showDonateButton = 0;
    $showProgressBar = 0;
    $showRaisedAmount = 0;
    $showEndDate = 0;
    $showDonationFormOnly = 0;
    $doNotShowBox = 0;
    $colorCode = '';
    $font = '';
    $buttonRadius = 0;
    $rowId = -1;
    $donationTitle = '';

    if (isset($_POST['styleName'])) {
        // $styleName = $_POST['styleName'];
        // $styleName = htmlspecialchars($_POST['styleName'], ENT_QUOTES, "UTF-8");
        $styleName = htmlspecialchars(stripslashes($_POST['styleName']));
        var_dump($styleName);
    }

    if (isset($_POST['styleId'])) {
        $rowId = $_POST['styleId'];
    }

    if (isset($_POST['oneTimeCheck'])) {
        $oneTimeCheck = $_POST['oneTimeCheck'];
    }

    if (isset($_POST['monthlyCheck'])) {
        $monthlyCheck = $_POST['monthlyCheck'];
    }

    if (isset($_POST['yearlyCheck'])) {
        $yearlyCheck = $_POST['yearlyCheck'];
    }

    if (isset($_POST['firstCheck'])) {
        $firstAmountCheck = $_POST['firstCheck'];
    }

    if (isset($_POST['secondCheck'])) {
        $secondAmountCheck = $_POST['secondCheck'];
    }

    if (isset($_POST['thirdCheck'])) {
        $thirdAmountCheck = $_POST['thirdCheck'];
    }

    if (isset($_POST['forthCheck'])) {
        $forthAmountCheck = $_POST['forthCheck'];
    }

    if (isset($_POST['otherCheck'])) {
        $otherChecked = $_POST['otherCheck'];
    }

    if (isset($_POST['firstAmount'])) {
        $firstAmount = $_POST['firstAmount'];
    }

    if (isset($_POST['secondAmount'])) {
        $secondAmount = $_POST['secondAmount'];
    }

    if (isset($_POST['thirdAmount'])) {
        $thirdAmount = $_POST['thirdAmount'];
    }

    if (isset($_POST['forthAmount'])) {
        $forthAmount = $_POST['forthAmount'];
    }

    if (isset($_POST['showDonateButton'])) {
        $showDonateButton = $_POST['showDonateButton'];
    }

    if (isset($_POST['showProgressBar'])) {
        $showProgressBar = $_POST['showProgressBar'];
    }

    if (isset($_POST['showRaisedAmount'])) {
        $showRaisedAmount = $_POST['showRaisedAmount'];
    }

    if (isset($_POST['showEndDate'])) {
        $showEndDate = $_POST['showEndDate'];
    }

    if (isset($_POST['showDonationFormOnly'])) {
        $showDonationFormOnly = $_POST['showDonationFormOnly'];
    }

    if (isset($_POST['doNotShowBox'])) {
        $doNotShowBox = $_POST['doNotShowBox'];
    }

    if (isset($_POST['colorCode'])) {
        $colorCode = $_POST['colorCode'];
    }

    if (isset($_POST['fontName'])) {
        $font = $_POST['fontName'];
    }

    if (isset($_POST['buttonRadius'])) {
        $buttonRadius = $_POST['buttonRadius'];
    }

    if (isset($_POST['donationTitle'])) {
        $donationTitle = $_POST['donationTitle'];
    }


    // var_dump($styleName);
    // var_dump($rowId);
    // var_dump($oneTimeCheck);
    // var_dump($monthlyCheck);
    // var_dump($yearlyCheck);
    // var_dump($firstAmountCheck);
    // var_dump($secondAmountCheck);
    // var_dump($thirdAmountCheck);
    // var_dump($forthAmountCheck);
    // var_dump($otherChecked);
    // var_dump($firstAmount);
    // var_dump($secondAmount);
    // var_dump($thirdAmount);
    // var_dump($forthAmount);
    // var_dump($showDonateButton);
    // var_dump($showProgressBar);
    // var_dump($showRaisedAmount);
    // var_dump($showEndDate);
    // var_dump($colorCode);
    // var_dump($font);
    // var_dump($buttonRadius);


    // if (isset($_POST['set-style-name-input'])) {
    //     $styleName = $_POST['set-style-name-input'];
    // }

    // if (isset($_POST['select-interval-onetime'])) {
    //     $oneTimeCheck = $_POST['select-interval-onetime'];
    // }
    // if (isset($_POST['select-interval-monthly'])) {
    //     $monthlyCheck = $_POST['select-interval-monthly'];
    // }
    // if (isset($_POST['select-interval-yearly'])) {
    //     $yearlyCheck = $_POST['select-interval-yearly'];
    // }

    // if (isset($_POST['select-amount-first'])) {
    //     $firstAmountCheck = $_POST['select-amount-first'];
    // }

    // if (isset($_POST['select-amount-second'])) {
    //     $secondAmountCheck = $_POST['select-amount-second'];
    // }

    // if (isset($_POST['select-amount-third'])) {
    //     $thirdAmountCheck = $_POST['select-amount-third'];
    // }

    // if (isset($_POST['select-amount-forth'])) {
    //     $forthAmountCheck = $_POST['select-amount-forth'];
    // }

    // if (isset($_POST['select-amount-first'])) {
    //     $firstAmount = $_POST['select-amount-value-first'];
    // }

    // if (isset($_POST['select-amount-second'])) {
    //     $secondAmount = $_POST['select-amount-value-second'];
    // }

    // if (isset($_POST['select-amount-third'])) {
    //     $thirdAmount = $_POST['select-amount-value-third'];
    // }

    // if (isset($_POST['select-amount-forth'])) {
    //     $forthAmount = $_POST['select-amount-value-forth'];
    // }

    // if (isset($_POST['select-amount-other'])) {
    //     $otherChecked = $_POST['select-amount-other'];
    // }

    // if (isset($_POST['select-donate-button'])) {
    //     $showDonateButton = $_POST['select-donate-button'];
    // }

    // if (isset($_POST['select-progress-bar'])) {
    //     $showProgressBar = $_POST['select-progress-bar'];
    // }

    // if (isset($_POST['select-raised-amount'])) {
    //     $showRaisedAmount = $_POST['select-raised-amount'];
    // }

    // if (isset($_POST['set-color-input'])) {
    //     $colorCode = $_POST['set-color-input'];
    // }

    // if (isset($_POST['selected-font'])) {
    //     $font = $_POST['selected-font'];
    // }

    // if (isset($_POST['set-button-radius-input'])) {
    //     $buttonRadius = $_POST['set-button-radius-input'];
    // }

    // if (isset($_POST['set-id'])) {
    //     $rowId = $_POST['set-id'];
    // }



    // array_push($array, $styleName, $oneTimeCheck, $monthlyCheck, $yearlyCheck, $firstAmountCheck, $secondAmountCheck, $thirdAmountCheck, $forthAmountCheck, $firstAmount, $secondAmount, $thirdAmount, $forthAmount, $otherChecked, $showDonateButton, $showProgressBar, $showRaisedAmount, $colorCode, $font, $buttonRadius);

    // #showData($array);


    $config_widget_table = $wpdb->prefix . "wdplugin_config_widget";
    $success = $wpdb->update(
        $config_widget_table,
        array(
            'pluginStyle' => $styleName
        ),
        array(
            'styleId' => $rowId
        ),
        array(
            '%s'
        ),
        array(
            '%d'
        )
    );

    if ($success) {
        echo 'Update success';
    } else {
        echo 'Update not success';
    }

    if ($rowId != 0) {
        #echo "update update update update update update update update" . $rowId;
        $success = $wpdb->update(
            $table_name,
            array(
                'styleName' => $styleName,
                'oneTimeCheck' => $oneTimeCheck,
                'monthlyCheck' => $monthlyCheck,
                'yearlyCheck' => $yearlyCheck,
                'firstAmountCheck' => $firstAmountCheck,
                'secondAmountCheck' => $secondAmountCheck,
                'thirdAmountCheck' => $thirdAmountCheck,
                'forthAmountCheck' => $forthAmountCheck,
                'firstAmount' => $firstAmount,
                'secondAmount' => $secondAmount,
                'thirdAmount' => $thirdAmount,
                'forthAmount' => $forthAmount,
                'otherChecked' => $otherChecked,
                'showDonateButton' => $showDonateButton,
                'showProgressBar' => $showProgressBar,
                'showRaisedAmount' => $showRaisedAmount,
                'showEndDate' => $showEndDate,
                'showDonationFormOnly' => $showDonationFormOnly,
                'donotShowBox' => $doNotShowBox,
                'colorCode' => $colorCode,
                'font' => $font,
                'buttonRadius' => $buttonRadius,
                'donationTitle' => $donationTitle
            ),
            array('ID' => $rowId),
            array(
                '%s',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
            ),
            array('%d')
        );

        if ($success) {
            echo 'Update success';
        } else {
            echo 'Update not success';
        }
    } else {
        #echo "insert insert insert insert insert insert insert insert insert " . $rowId;
        $success = $wpdb->insert(
            $table_name,
            array(
                'styleName' => $styleName,
                'oneTimeCheck' => $oneTimeCheck,
                'monthlyCheck' => $monthlyCheck,
                'yearlyCheck' => $yearlyCheck,
                'firstAmountCheck' => $firstAmountCheck,
                'secondAmountCheck' => $secondAmountCheck,
                'thirdAmountCheck' => $thirdAmountCheck,
                'forthAmountCheck' => $forthAmountCheck,
                'firstAmount' => $firstAmount,
                'secondAmount' => $secondAmount,
                'thirdAmount' => $thirdAmount,
                'forthAmount' => $forthAmount,
                'otherChecked' => $otherChecked,
                'showDonateButton' => $showDonateButton,
                'showProgressBar' => $showProgressBar,
                'showRaisedAmount' => $showRaisedAmount,
                'showEndDate' => $showEndDate,
                'showDonationFormOnly' => $showDonationFormOnly,
                'doNotShowBox' => $doNotShowBox,
                'colorCode' => $colorCode,
                'font' => $font,
                'buttonRadius' => $buttonRadius,
                'donationTitle' => $donationTitle
            ),
            array(
                '%s',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
            )
        );

        if ($success) {
            echo 'Update success';
        } else {
            echo 'Update not success';
        }
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_set_new_style', 'set_new_style');

// if (isset($_POST['addNewStyle'])) {
//     addNewStyle();
// }

function remove_row()
{
    // try {
    $id = $_POST['id'];

    echo 'called and try to delete called and try to delete called and try to delete called and try to delete called and try to delete' . $id;

    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_style";
    $reponse = $wpdb->delete($table_name, array('id' => $id), array('%d'));

    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_remove_row', 'remove_row');
add_action('wp_ajax_nopriv_remove_row', 'remove_row');

function showData($values)
{

?>
    <div style="margin-left: 500px; margin-top: 20px;">
        <ul><?php foreach ($values as $value) {
                echo '<li>' . $value . '</li>';
            }
            ?></ul>
    </div>

<?php

}

add_action('wp_ajax_check_api_key', 'check_api_key');

function check_api_key()
{

    // try {

    if ((isset($_POST['payload'])) && isset($_POST['api_key'])) {
        $data = $_POST['payload'];
        $apiKey = $_POST['api_key'];

        // $url = 'https://whydonate-development.appspot.com/api/v1/account/apikey/user/?client=whydonate-staging';
        $url = 'https://whydonate-production-api.appspot.com/api/v1/account/apikey/user/?client=whydonate-production';
        // $url = 'http://127.0.0.1:8000/api/v1/account/apikey/user/';

        // $myJSON = json_encode($data);

        // $curl = curl_init($url);
        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt(
        //     $curl,
        //     CURLOPT_HTTPHEADER,
        //     array(
        //         'Content-Type: application/json',
        //         'API-KEY: ' . $apiKey
        //     )
        // );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'API-KEY: ' . $apiKey
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}


add_action('wp_ajax_api_key', 'api_key');

function api_key()
{
    // try {
    $api_key = '';
    $username = '';
    $email = '';

    if (isset($_POST['api_key'])) {
        $api_key = $_POST['api_key'];
    }

    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }

    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }



    updateApiKey($api_key, $username, $email);

    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

function updateApiKey($key, $username, $email)
{
    // try {
    global $wpdb;
    // $table_name = "wp_wdplugin_api_key";
    // $table_name = $wpdb->prefix . 'wdplugin_api_key';
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");
    if (count($result) == 0) {
        // echo "not filled";
        if (!empty($key)) {

            $success = $wpdb->insert($table_name, array(
                "apiKey" => $key,
                "username" => $username,
                "email" => $email
            ), array('%s', '%s', '%s'));
            //echo $success;
            if ($success) {
                // var_dump($success);
                echo 'Insert success';
            } else {
                echo 'Insert not success';
            }
        }
    } else {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE apiKey = '$key'");

        if ($count > 0) {
            echo 'Update success';
        } else {

            // echo "filled";
            $success = $wpdb->update(
                $table_name,
                array(
                    'apiKey' => $key,
                    'username' => $username,
                    'email' => $email
                ),
                array('ID' => 1),
                array(
                    '%s',
                    '%s',
                    '%s'
                ),
                array('%s')
            );

            if ($success) {
                // var_dump($success);
                echo 'Update success';
            } else {
                echo 'Update not success';
            }
        }
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }

    //  }
}

add_action('wp_ajax_edit_config_widget', 'edit_config_widget');

function edit_config_widget()
{
    // try {
    $selected_style = '';
    $fundraiser_name = '';
    $slug_name = '';
    $id = 0;
    $slug_id = 0;
    // $redirect_url = '';
    $success_url = '';
    $failure_url = '';

    global $wpdb;

    if (isset($_POST['fundraiser_name'])) {
        $fundraiser_name = $_POST['fundraiser_name'];
    }

    if (isset($_POST['slug_name'])) {
        $slug_name = $_POST['slug_name'];
    }

    if (isset($_POST['selected_style'])) {
        $selected_style = $_POST['selected_style'];
        // $selected_style = htmlspecialchars(stripslashes($_POST['selected_style']));
        var_dump($selected_style);
    }

    if (isset($_POST['slug_id'])) {
        $slug_id = $_POST['slug_id'];
    }

    // if (isset($_POST['redirect_url'])) {
    //     $redirect_url = $_POST['redirect_url'];
    // }

    if (isset($_POST['success_url'])) {
        $success_url = $_POST['success_url'];
    }

    if (isset($_POST['failure_url'])) {
        $failure_url = $_POST['failure_url'];
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }

    // $style_table = "wp_wdplugin_style";
    $style_table = $wpdb->prefix . 'wdplugin_style';
    $config_widget_table = $wpdb->prefix . 'wdplugin_config_widget';

    $retrieve_data = $wpdb->get_results("SELECT * FROM $style_table WHERE styleName = '$selected_style'");
    var_dump($retrieve_data);
    $styleId = 999;
    if (!empty($retrieve_data)) {
        foreach ($retrieve_data as $retrieved_data) {
            $styleId = $retrieved_data->id;
        }
    }

    $selected_style = htmlspecialchars(stripslashes($_POST['selected_style']));

    $success = $wpdb->update(
        $config_widget_table,
        array(
            'styleId' => $styleId,
            'pluginStyle' => $selected_style,
            'fundraiserName' => $fundraiser_name,
            'slugName' => $slug_name,
            'slugId' => $slug_id,
            'successUrl' => $success_url,
            'failureUrl' => $failure_url
            // 'redirectUrl' => $redirect_url
        ),
        array('id' => $id),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s'
        ),
        array('%d')
    );

    if ($success) {
        echo 'Update success';
    } else {
        echo 'Update not success';
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}


add_action('wp_ajax_make_donation', 'make_donation');
add_action('wp_ajax_nopriv_make_donation', 'make_donation');

function make_donation()
{
    // try {
    if (isset($_POST['info'])) {
        $data = $_POST['info'];

        // $url = 'http://127.0.0.1:8000/api/v1/donation/order/';
        // $url = 'https://whydonate-development.appspot.com/api/v1/donation/order/?client=whydonate-staging';
        $url = 'https://whydonate-production-api.appspot.com/api/v1/donation/order/?client=whydonate-production';

        $myJSON = json_encode((object) $data, JSON_NUMERIC_CHECK);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_create_fundraiser', 'create_fundraiser');

function create_fundraiser()
{
    // try {
    if (isset($_POST['payload'])) {
        $data = $_POST['payload'];

        $data['unlimited'] = false;
        $data['show_donation_details'] = true;
        $data['title'] = htmlspecialchars(stripslashes($data['title']));

        if ($data['tip_enabled'] == 'true') {
            $data['tip_enabled'] = true;
        } else {
            $data['tip_enabled'] = false;
        }

        var_dump($data);

        // $url = 'https://whydonate-development.appspot.com/api/v1/project/fundraising/local/?client=whydonate-staging';
        $url = 'https://whydonate-production-api.appspot.com/api/v1/project/fundraising/local/?client=whydonate-production';
        // $url = 'http://127.0.0.1:8000/api/v1/project/fundraising/local/';
        $myJSON = json_encode($data, JSON_NUMERIC_CHECK);

        global $wpdb;
        $table_name = $wpdb->prefix . 'wdplugin_api_key';
        $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");
        var_dump($result);

        if (count($result) > 0) {
            global $wpdb;
            $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = 1");

            if (!empty($retrieve_data)) {
                $apiKey = '';
                foreach ($retrieve_data as $retrieved_data) {
                    $apiKey = $retrieved_data->apiKey;
                }
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $curl,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json',
                        'API-KEY: ' . $apiKey
                    )
                );

                $response = curl_exec($curl);
                curl_close($curl);
                echo $response;
            }
        } else {
            echo 'Please set an api key first';
        }
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}


add_action('wp_ajax_edit_fundraiser', 'edit_fundraiser');

function edit_fundraiser()
{
    // try {
    if (isset($_POST['payload'])) {
        $data = $_POST['payload'];

        $data['unlimited'] = false;
        $data['show_donation_details'] = true;
        $data['title'] = htmlspecialchars(stripslashes($data['title']));

        if ($data['tip_enabled'] == 'true') {
            $data['tip_enabled'] = true;
        } else {
            $data['tip_enabled'] = false;
        }

        var_dump($data);

        // $url = 'https://whydonate-development.appspot.com/api/v1/project/fundraising/local/?client=whydonate-staging';
        $url = 'https://whydonate-production-api.appspot.com/api/v1/project/fundraising/local/?client=whydonate-production';
        // $url = 'http://127.0.0.1:8000/api/v1/project/fundraising/local/';
        $myJSON = json_encode($data, JSON_NUMERIC_CHECK);

        global $wpdb;
        $table_name = $wpdb->prefix . 'wdplugin_api_key';
        $result = $wpdb->get_results("SELECT `id` from $table_name WHERE `id` IS NOT NULL");

        if (count($result) > 0) {
            global $wpdb;
            // $table_name = "wp_wdplugin_api_key";
            $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE `id` = 1");

            if (!empty($retrieve_data)) {
                $apiKey = '';
                foreach ($retrieve_data as $retrieved_data) {
                    $apiKey = $retrieved_data->apiKey;
                }
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $curl,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json',
                        'API-KEY: ' . $apiKey
                    )
                );

                $response = curl_exec($curl);
                curl_close($curl);
                echo $response;
            }
        } else {
            echo 'Please set an api key first';
        }
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_update_config_widget', 'update_config_widget');

function update_config_widget()
{
    // try {
    $pluginStyle = '';
    $fundraiserName = '';
    $slugId = 0;
    $slugName = '';
    #$shortCode = '2004';
    $shortCode = gen_uid(5);
    // $redirectUrl = '';
    $successUrl = '';
    $failureUrl = '';

    #$shortCode .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
    global $wpdb;

    // $style_table = "wp_wdplugin_style";
    $style_table = $wpdb->prefix . 'wdplugin_style';
    $config_widget_table = $wpdb->prefix . 'wdplugin_config_widget';

    if (isset($_POST['fundraiser_name'])) {
        $fundraiserName = $_POST['fundraiser_name'];
    }

    if (isset($_POST['slug_id'])) {
        $slugId = $_POST['slug_id'];
    }

    if (isset($_POST['slug_name'])) {
        $slugName = $_POST['slug_name'];
    }

    if (isset($_POST['selected_style'])) {
        $pluginStyle = $_POST['selected_style'];
        // $pluginStyle = $_POST['selected_style'];
        // $selected_style = htmlspecialchars(stripslashes($_POST['selected_style']));
        // var_dump($pluginStyle);
    }

    if (isset($_POST['success_url'])) {
        $successUrl = $_POST['success_url'];
    }

    if (isset($_POST['failure_url'])) {
        $failureUrl = $_POST['failure_url'];
    }



    $retrieve_data = $wpdb->get_results("SELECT * FROM $style_table WHERE styleName = '$pluginStyle'");
    var_dump($retrieve_data);
    $id = 999;
    if (!empty($retrieve_data)) {
        foreach ($retrieve_data as $retrieved_data) {
            $id = $retrieved_data->id;
        }
    }


    if (!empty($fundraiserName) && $slugId != 0 && !empty($pluginStyle)) {
        $pluginStyle = htmlspecialchars(stripslashes($_POST['selected_style']));
        $response = $wpdb->insert(
            $config_widget_table,
            array(
                'styleId' => $id,
                'pluginStyle' => $pluginStyle,
                'fundraiserName' => $fundraiserName,
                'slugId' => $slugId,
                'slugName' => $slugName,
                'shortcode' => $shortCode,
                'successUrl' => $successUrl,
                'failureUrl' => $failureUrl
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
        echo $response;
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}



function gen_uid($l)
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
}

add_action('wp_ajax_fundraiser_list', 'fundraiser_list');

function fundraiser_list()
{

    #$url = 'http://127.0.0.1:8000/api/v1/project/fundraising/local/user/';

    // $url = 'https://whydonate-development.appspot.com/api/v1/project/fundraising/local/user/?filter=&sort_col=&sort_direction=asc&page=1&page_size=500&from_date=0&to_date=' . time() . '&client=whydonate-staging';


    $url = 'https://whydonate-production-api.appspot.com/api/v1/project/fundraising/local/user/?filter=&sort_col=&sort_direction=asc&page=1&page_size=500&from_date=0&to_date=' . time() . '&client=whydonate-production';

    // $url = 'http://127.0.0.1:8000/api/v1/project/fundraising/local/user/?filter=&sort_col=&sort_direction=asc&page=1&page_size=50&from_date=0&to_date=' . time();

    // $url = 'http://127.0.0.1:8000/api/v1/project/fundraising/local/user/?filter=&sort_col=&sort_direction=asc&page=1&page_size=50&from_date=0&to_date=' . time();

    // try {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wdplugin_api_key';
    $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");

    if (count($result) > 0) {
        global $wpdb;
        // $table_name = "wp_wdplugin_api_key";
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
            echo $response;
        }
    } else {
        echo 'Please set an api key first';
    }

    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_open_close_fundraiser', 'open_close_fundraiser');

function open_close_fundraiser()
{

    if (isset($_POST['payload'])) {
        $payload = $_POST['payload'];
    }

    // var_dump($payload);

    if ($payload['is_draft'] == 'true') {
        $payload['is_draft'] = true;
    } else {
        $payload['is_draft'] = false;
    }

    if ($payload['visible'] == 'true') {
        $payload['visible'] = true;
    } else {
        $payload['visible'] = false;
    }

    if ($payload['can_receive_donation'] == 'true') {
        $payload['can_receive_donation'] = true;
    } else {
        $payload['can_receive_donation'] = false;
    }

    $myJSON = json_encode($payload);

    // var_dump($myJSON);

    // $url = 'https://whydonate-development.appspot.com/api/v1/project/fundraising/set_visible?client=whydonate-staging';
    $url = 'https://whydonate-production-api.appspot.com/api/v1/project/fundraising/set_visible?client=whydonate-production';

    // try {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wdplugin_api_key';
    $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");

    if (count($result) > 0) {
        global $wpdb;
        // $table_name = "wp_wdplugin_api_key";
        $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = 1");

        if (!empty($retrieve_data)) {
            $apiKey = '';
            foreach ($retrieve_data as $retrieved_data) {
                $apiKey = $retrieved_data->apiKey;
            }
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'API-KEY: ' . $apiKey
                )
            );

            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
        }
    } else {
        echo 'Bad Request!';
    }

    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}


add_action('wp_ajax_check_database', 'check_database');

function check_database()
{
    // try {
    global $wpdb;
    $api_table = $wpdb->prefix . 'wdplugin_api_key';
    // $api_table = 'wp_wdplugin_api_key';
    $pluginStyle =  $wpdb->prefix . 'wdplugin_style';
    // $pluginStyle = 'wp_wdplugin_style';
    $configWidget = $wpdb->prefix . 'wdplugin_config_widget';
    // $configWidget = 'wp_wdplugin_config_widget';

    if ($wpdb->get_var("SHOW TABLES LIKE '$api_table'")) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$pluginStyle'")) {
            if ($wpdb->get_var("SHOW TABLES LIKE '$configWidget'")) {
                //echo 'all tables exist';
                return true;
            } else {
                //echo 'config widget table not exist';
                return false;
            }
        } else {
            //echo 'plugin table not exist';
            return false;
        }
    } else {
        //echo 'api key table not exist!';
        return false;
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}





add_action('wp_ajax_my_action', 'my_action');

function my_action()
{
    // global $wpdb; // this is how you get access to the database

    // $whatever = intval($_POST['whatever']);

    // $whatever += 10;

    // echo $whatever . "adsfjashfkjas fahsdjfhas fhasfhashf asdkfhkasdhfkashflkashf a";

    // wp_die(); // this is required to terminate immediately and return a proper response

    $id = intval($_POST['id']);

    // try {
    global $wpdb;
    $style_table = $wpdb->prefix . 'wdplugin_style';
    $config_widget_table = $wpdb->prefix . 'wdplugin_config_widget';

    $reponse = $wpdb->delete($style_table, array('id' => $id), array('%d'));

    $success = $wpdb->update(
        $config_widget_table,
        array(
            'pluginStyle' => 'Default'
        ),
        array(
            'styleId' => $id
        ),
        array(
            '%s'
        ),
        array(
            '%d'
        )
    );

    if ($success) {
        echo json_encode(array('status' => $reponse));
    } else {
        echo 'Update not success';
    }

    #echo 'Delete Confirmed!';

    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_remove_widget_action', 'remove_widget_action');

function remove_widget_action()
{

    $id = intval($_POST['id']);

    // try {
    global $wpdb;
    $config_widget_table = $wpdb->prefix . 'wdplugin_config_widget';
    $reponse = $wpdb->delete($config_widget_table, array('id' => $id), array('%d'));

    echo json_encode(array('status' => $reponse));

    #echo 'Delete Confirmed!';

    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_check_order_status', 'check_order_status');
add_action('wp_ajax_nopriv_check_order_status', 'check_order_status');

function check_order_status()
{
    // try {
    if (isset($_POST['order_id'])) {
        $data = $_POST['order_id'];

        //$url = 'http://127.0.0.1:8000/api/v1/donation/order/status/?order_id='.$data;
        // $url = 'https://whydonate-development.appspot.com/api/v1/donation/order/status/?order_id='.$data. '&client=whydonate-staging';
        $url = 'https://whydonate-production-api.appspot.com/api/v1/donation/order/status/?order_id=' . $data . '&client=whydonate-production';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        //     'order_id: ' . $data
        // ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

function addNewFundraiserToList()
{
    echo "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum";
}

// // custom login logo url
// function myplugin_custom_login_url($url)
// {

//     $options = get_option('myplugin_options', myplugin_options_default());

//     if (isset($options['custom_url']) && !empty($options['custom_url'])) {

//         $url = esc_url($options['custom_url']);
//     }

//     return $url;
// }
// add_filter('login_headerurl', 'myplugin_custom_login_url');

// // custom login logo title
// function myplugin_custom_login_title($title)
// {

//     $options = get_option('myplugin_options', myplugin_options_default());

//     if (isset($options['custom_title']) && !empty($options['custom_title'])) {

//         $title = esc_attr($options['custom_title']);
//     }

//     return $title;
// }
// add_filter('login_headertitle', 'myplugin_custom_login_title');

// // custom login styles
// function myplugin_custom_login_styles()
// {

//     // $styles = false;

//     // $options = get_option( 'myplugin_options', myplugin_options_default() );

//     // if ( isset( $options['custom_style'] ) && ! empty( $options['custom_style'] ) ) {

//     //     $styles = sanitize_text_field( $options['custom_style'] );

//     // }

//     // if ( 'enable' === $styles ) {

//     /*

//         wp_enqueue_style(
//             string           $handle,
//             string           $src = '',
//             array            $deps = array(),
//             string|bool|null $ver = false,
//             string           $media = 'all'
//         )

//         wp_enqueue_script(
//             string           $handle,
//             string           $src = '',
//             array            $deps = array(),
//             string|bool|null $ver = false,
//             bool             $in_footer = false
//         )

//         */

//     wp_enqueue_style('myplugin', plugin_dir_url(dirname(__FILE__)) . 'public/css/myplugin-login.css', array(), null, 'screen');

//     wp_enqueue_script('myplugin', plugin_dir_url(dirname(__FILE__)) . 'public/js/myplugin-login.js', array(), null, true);

//     //    }

// }
// add_action('login_enqueue_scripts', 'myplugin_custom_login_styles');

// // custom login message
// function myplugin_custom_login_message($message)
// {

//     $options = get_option('myplugin_options', myplugin_options_default());

//     if (isset($options['custom_message']) && !empty($options['custom_message'])) {

//         $message = wp_kses_post($options['custom_message']) . $message;
//     }

//     return $message;
// }
// add_filter('login_message', 'myplugin_custom_login_message');

// // custom admin footer
// function myplugin_custom_admin_footer($message)
// {

//     $options = get_option('myplugin_options', myplugin_options_default());

//     if (isset($options['custom_footer']) && !empty($options['custom_footer'])) {

//         $message = sanitize_text_field($options['custom_footer']);
//     }

//     return $message;
// }
// add_filter('admin_footer_text', 'myplugin_custom_admin_footer');

// // custom toolbar items
// function myplugin_custom_admin_toolbar()
// {

//     $toolbar = false;

//     $options = get_option('myplugin_options', myplugin_options_default());

//     if (isset($options['custom_toolbar']) && !empty($options['custom_toolbar'])) {

//         $toolbar = (bool)$options['custom_toolbar'];
//     }

//     if ($toolbar) {

//         global $wp_admin_bar;

//         $wp_admin_bar->remove_menu('comments');
//         $wp_admin_bar->remove_menu('new-content');
//     }
// }
// add_action('wp_before_admin_bar_render', 'myplugin_custom_admin_toolbar', 999);

// // custom admin color scheme
// function myplugin_custom_admin_scheme($user_id)
// {

//     $scheme = 'default';

//     $options = get_option('myplugin_options', myplugin_options_default());

//     if (isset($options['custom_scheme']) && !empty($options['custom_scheme'])) {

//         $scheme = sanitize_text_field($options['custom_scheme']);
//     }

//     $args = array('ID' => $user_id, 'admin_color' => $scheme);

//     wp_update_user($args);
// }
// add_action('user_register', 'myplugin_custom_admin_scheme');
