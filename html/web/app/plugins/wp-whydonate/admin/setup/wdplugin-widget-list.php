<?php


?>

<div>

    <div class="widget-list" id="widget-list">
        <div style="display: flex;">
            <h1 style="flex-grow: 1; margin-left: 5%"><?php _e('Widgets', 'whydonate-v2') ?></h1>
            <button class="btn-style" onclick="redirectToCreateWidget()" style="margin-right: 5%;"><?php _e('Add Widget', 'whydonate-v2') ?></button>
        </div>

        <table class="data-list-table">
            <thead>
                <tr>
                    <th scope="col"><?php _e('Widget ID', 'whydonate-v2') ?></th>
                    <th scope="col"><?php _e('All styles', 'whydonate-v2') ?></th>
                    <th scope="col"><?php _e('Fundraisers', 'whydonate-v2') ?></th>
                    <th scope="col"><?php _e('Shortcode', 'whydonate-v2') ?></th>
                    <th scope="col"><?php _e('Action', 'whydonate-v2') ?></th>
                </tr>
            </thead>

            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . "wdplugin_config_widget";
            $result = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

            if (empty($result)) {
            ?>
        </table>
        <div style="display: flex; flex-direction: column">

            <div style="flex-grow: 1; text-align: center; margin-top: 20px">
                <img src="<?php echo plugin_dir_url(__FILE__) . 'images/widget.png'; ?>" alt="Italian Trulli">
                <h3 style="text-align: center; padding-top: 10px"><?php _e("It seems you haven’t created any widgets yet.", "whydonate-v2") ?></h3>
            </div>
            <div style="flex-grow: 1; text-align: center">
                <button class="btn-style" onclick="redirectToCreateWidget()" style="margin: 0px; float: none"><?php _e('Add widget', 'whydonate-v2') ?></button>
            </div>

        </div>
        <?php
            } else {
                foreach ($result as $row) {
                    //$sitemap .= $row[name];
                    $shortcode =  $row['shortcode'];
                    echo '<tr>';
                    echo '<td class="widgets-list-style-id" style="text-align: center">' . $row['id'] . '</td>';
                    echo '<td class="widgets-list-plugin-style">' . $row['pluginStyle'] . '</td>';
                    echo '<td class="widgets-list-fundraiser-name">' . $row['fundraiserName'] . '</td>';
                    echo '<td class="widgets-list-shortcode">' . '[whydonate id="' . $row['shortcode'] . '"]' . '</td>';
                    echo '<td class="widgets-list-item-btn">';
        ?>
            <button class="widgets-list-item-edit-btn" style="width: 135px" onclick='copyCode("<?php echo $shortcode  ?>")'><?php _e('Copy shortcode', 'whydonate-v2') ?></button>
            <button class="widgets-list-item-edit-btn" onclick='editWidget(<?php echo json_encode($row, JSON_HEX_APOS); ?>)'><?php _e('Edit', 'whydonate-v2') ?></button>
            <button class="widgets-list-item-remove-btn" name="remove_widget" style="margin-left: 10px;"><?php _e('Remove', 'whydonate-v2') ?></button>
    <?php
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            //echo $sitemap;
    ?>
    <!-- <tr>
            <td class="widgets-list-style">1</td>
            <td class="widgets-list-plugin-style">default</td>
            <td class="widgets-list-fundraiser-name">Fundraiser Cause 1</td>
            <td class="widgets-list-shortcode">Cause_1</td>
            <td class="widgets-list-item-btn">
                <button class="widgets-list-item-edit-btn">Edit</button>
                <button class="widgets-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
            </td>
        </tr> -->
    <!-- <tr>
      <td class="widgets-list-style">2</td>
      <td class="widgets-list-plugin-style">default</td>
      <td class="widgets-list-fundraiser-name">Fundraiser Cause 2</td>
      <td class="widgets-list-shortcode">Cause_2</td>
      <td class="widgets-list-item-btn">
        <button class="widgets-list-item-edit-btn">Edit</button>
        <button class="widgets-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
      </td>
    </tr>
    <tr>
      <td class="widgets-list-style">3</td>
      <td class="widgets-list-plugin-style">default</td>
      <td class="widgets-list-fundraiser-name">Fundraiser Cause 3</td>
      <td class="widgets-list-shortcode">Cause_3</td>
      <td class="widgets-list-item-btn">
        <button class="widgets-list-item-edit-btn">Edit</button>
        <button class="widgets-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
      </td>
    </tr>
    <tr>
      <td class="widgets-list-style">4</td>
      <td class="widgets-list-plugin-style">default</td>
      <td class="widgets-list-fundraiser-name">Fundraiser Cause 4</td>
      <td class="widgets-list-shortcode">Cause_4</td>
      <td class="widgets-list-item-btn">
        <button class="widgets-list-item-edit-btn">Edit</button>
        <button class="widgets-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
      </td>
    </tr> -->

    </div>


    <div class="edit-widget" id="edit-widget" style="display: none;">

        <div>
            <button class="btn-style" style="float: none;
                margin-top: 20px;
                margin-left: 20px;
                height: 40px;
                width: 70px;" onclick="backToWidgetList()"><?php _e('Back', 'whydonate-v2') ?></button>

            <label id="edit-widget-id"></label><br />


            <div class="select-fundraiser-list" id="select-fundraiser-list">
                <label for="select-fundraiser-list-label"><?php _e('Select fundraiser', 'whydonate-v2') ?></label><br />
                <select id="fundraiser-list-dropdown" style="height: 45px; border-radius: 10px;">
                    <!-- <option value="" disabled selected>Choose your option</option> -->
                    <!-- <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option> -->
                </select>
                <p id="error-msg-p" style="display: none; color: red"><?php _e('Something went wrong. Please check your api-key.', 'whydoante-v2') ?> </p>
                <p id="no-fundraiser-yet" style="display: none; font-weight: bold; color: red"><?php _e("It seems like you haven't created any fundraiser yet.", "whydonate-v2") ?> </p>

                <div style="display: flex">
                    <p class="description" id="flist-desc">

                    </p>
                </div>

                <label id="show-select-fundraiser-error-msg" style="display: none; color: red; margin-top: 10px; font-size: 14px"></label><br>
            </div>
            <div class="select-plugin-style" id="select-plugin-style">
                <label for="select-plugin-style-label"><?php _e('Select style', 'whydonate-v2') ?></label><br>
                <select id="style-list-dropdown" style="height: 45px; border-radius: 10px">
                    <option value="" disabled selected> <?php _e('Choose your option', 'whydonate-v2') ?> </option>
                    <option value="999"><?php _e('Default', 'whydonate-v2') ?></option>
                    <?php
                    global $wpdb;
                    $style_table = $wpdb->prefix . "wdplugin_style";
                    $result = $wpdb->get_results("SELECT * FROM $style_table", ARRAY_A);
                    // echo '<option value="" disabled selected> Choose your option </option>';
                    // echo '<option value="999">Default</option>';
                    foreach ($result as $row) {
                        echo '<option value=' . $row['id'] . '>' . $row['styleName'] . '</option>';
                    }
                    ?>
                </select>
                <div style="display: flex">
                    <p class="description"><?php _e('You can', 'whydonate-v2'); ?></p>
                    <p class="description" style="margin-left: 3px">
                        <a id="custom-widget-plugin-style-link" href="" target="blank" style="font-style: italic"><?php _e('create a custom plugin style', 'whydonate-v2') ?></a>
                    </p>
                    <p class="description" style="margin-left: 3px"><?php _e('which fits the look of your website', 'whydonate-v2'); ?></p>
                </div>
                <label id="show-select-style-error-msg" style="display: none; color: red; margin-top: 10px; font-size: 14px"></label><br>
            </div>

            <div class="select-plugin-success-url" id="select-plugin-success-url">
                <label for="select-plugin-style-label"><?php _e('Successful payment redirect URL', 'whydonate-v2') ?></label><br>
                <input type="text" name="fundraiser-success-url" id="fundraiser-success-url" style="width: 600px; margin-top: 15px;height: 45px; border-radius: 10px" placeholder="Optional">
                <p class="description"><?php _e('The URL where the donor is sent after a successful payment.', 'whydonate-v2'); ?></p>
                <label id="show-redirect-url-error-msg" style="display: none; color: red; margin-top: 10px; font-size: 14px"></label><br>
            </div>

            <div class="select-plugin-failure-url" id="select-plugin-failure-url">
                <label for="select-plugin-style-label"><?php _e('Failed payment redirect URL', 'whydonate-v2') ?></label><br>
                <input type="text" name="fundraiser-failure-url" id="fundraiser-failure-url" style="width: 600px; margin-top: 15px;height: 45px; border-radius: 10px" placeholder="Optional">
                <p class="description"><?php _e('The URL where the donor is sent after a failed payment.', 'whydonate-v2'); ?></p>
                <label id="show-redirect-url-error-msg" style="display: none; color: red; margin-top: 10px; font-size: 14px"></label><br>
            </div>

        </div>

        <button id="update-widget-btn" class="btn-style" type="submit" name="action" style="float: left; margin: 20px; height: 40px; width: 80px"><?php _e('Save', 'whydonate-v2') ?>
            <!-- <i class="material-icons right">save</i> -->
        </button>
    </div>

    <div class="modal" id="modal">
        <!-- Place at bottom of page -->
    </div>

</div>