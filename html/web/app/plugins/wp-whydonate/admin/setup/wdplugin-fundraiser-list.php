<?php

?>

<div>

  <div class="fundraiser-list" id="fundraiser-list">
    <div style="display: flex;">
      <h1 style="flex-grow: 1; margin-left: 5%"><?php _e('Fundraisers', 'whydonate-v2') ?></h1>
      <button id="add-new-fundraiser-btn" class="btn-style" style="margin-right: 5%; height: 50px; border-radius: 30px" onclick="addNewFundraiserList()"><?php _e('Add Fundraiser', 'whydonate-v2') ?></button>
    </div>

    <table class="data-list-table" id="fundraiser-list-table">
      <thead>
        <tr>
          <th scope="col"><?php _e('ID', 'whydonate-v2') ?></th>
          <th scope="col"><?php _e('Title', 'whydonate-v2') ?></th>
          <th scope="col"><?php _e('Created On', 'whydonate-v2') ?></th>
          <th scope="col"><?php _e('Action', 'whydonate-v2') ?></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <!-- <tr>
        <td class="fundraiser-list-item-id" style="text-align: center">1</td>
        <td class="fundraiser-list-item-name">Fundraiser Cause 1</td>
        <td class="fundraiser-list-item-create-on">12/3/2014</td>
        <td class="fundraiser-list-item-slug" style="text-align: center">Slug</td>
        <td class="fundraiser-list-item-btn">
          <button class="fundraiser-list-item-edit-btn">Edit</button>
          <button class="fundraiser-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
        </td>
      </tr>
      <tr>
        <td class="fundraiser-list-item-id" style="text-align: center">2</td>
        <td class="fundraiser-list-item-name">Fundraiser Cause 2</td>
        <td class="fundraiser-list-item-create-on">12/3/2014</td>
        <td class="fundraiser-list-item-slug" style="text-align: center">Slug</td>
        <td class="fundraiser-list-item-btn">
          <button class="fundraiser-list-item-edit-btn">Edit</button>
          <button class="fundraiser-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
        </td>
      </tr>
      <tr>
        <td class="fundraiser-list-item-id" style="text-align: center">3</td>
        <td class="fundraiser-list-item-name">Fundraiser Cause 3</td>
        <td class="fundraiser-list-item-create-on">12/3/2014</td>
        <td class="fundraiser-list-item-slug" style="text-align: center">Slug</td>
        <td class="fundraiser-list-item-btn">
          <button class="fundraiser-list-item-edit-btn">Edit</button>
          <button class="fundraiser-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
        </td>
      </tr>
      <tr>
        <td class="fundraiser-list-item-id" style="text-align: center">4</td>
        <td class="fundraiser-list-item-name">Fundraiser Cause 3</td>
        <td class="fundraiser-list-item-create-on">12/3/2014</td>
        <td class="fundraiser-list-item-slug" style="text-align: center">Slug</td>
        <td class="fundraiser-list-item-btn">
          <button class="fundraiser-list-item-edit-btn">Edit</button>
          <button class="fundraiser-list-item-remove-btn" style="margin-left: 5px;">Remove</button>
        </td>
      </tr> -->
    </table>
    <div id="api-key-error-view" style="display: none">
      <div style="display: flex; flex-direction: column">
        <div style="flex-grow: 1; text-align: center; margin-top: 20px">
          <img src="<?php echo plugin_dir_url(__FILE__) . 'images/fundraiser-mark.png'; ?>" alt="Italian Trulli">
          <h3 style="text-align: center; padding-top: 10px"><?php _e("The API key doesn't seem to work. Please check it.", "whydonate-v2") ?></h3>
        </div>
      </div>
    </div>

    <div id="not-have-fundraisers-view" style="display: none">
      <div style="display: flex; flex-direction: column">
        <div style="flex-grow: 1; text-align: center; margin-top: 20px">
          <img src="<?php echo plugin_dir_url(__FILE__) . 'images/fundraiser-mark.png'; ?>" alt="Italian Trulli">
          <h3 style="text-align: center; padding-top: 10px"><?php _e("It seems like you haven't created any fundraiser yet.", "whydonate-v2") ?></h3>
          <button class="btn-style" onclick="addNewFundraiserList()" style="margin: 0px; float: none"><?php _e('Add Fundraiser', 'whydonate-v2') ?></button>
        </div>
      </div>
    </div>

    <div id="create-fundraiser-msg" style="background:khaki; height: 40px; display: none; margin-top: 20px; width: 90%; margin-left: 5%">
      <label style="font-size: 18px; margin-left: 10px; margin-top:10px"><?php _e('Good news! Fundraiser created from this plugin can also be made visible Whydonate. To reach more people and increase your chances of donations, publish your fundraiser on the <a href=" https://www.whydonate.eu/dashboard" target="blank">Whydonate platform</a> by adding the required extra information.', 'whydonate-v2') ?></label>

    </div>
  </div>


  <div class="add-fundraiser" style="display: none;" id="add-fundraiser">

    <button class="btn-style" onclick="backToFundraiserList()" style="                             float: none;
                margin-top: 20px;
                margin-left: 0px;
                height: 40px;
                width: 70px;"><?php _e('Back', 'whydonate-v2') ?></button>
    <h1><?php _e('Add Fundraiser', 'whydonate-v2') ?></h1>

    <form method="post">
      <label id="fundraiser-id" style="display: none"></label>
      <label id="fundraiser-slug"></label>
      <div class="fundraiser-name">
        <label for="add-fundraiser-name-label" style="margin-top: 25px; font-size: 18px;"><?php _e('Title fundraiser', 'whydonate-v2') ?></label><br />
        <input type="text" id="add-fundraiser-name-input" maxlength="70" name="add-fundraiser-name-input" style="margin-top: 15px; width: 500px;"><br />
        <span class="description"><?php _e('(Maximum of 70 characters)', 'whydonate-v2'); ?></span>
        <span class="description" id="title-error-message" style="display: none; font-weight: 600; font-size: 12px; color: red"><?php _e('Please add a title', 'whydonate-v2'); ?></span>
      </div>

      <div class=" set-target" style="margin-top: 20px;">
        <label for="set-target-label" style="font-size: 18px;"><?php _e('Optional fields', 'whydonate-v2') ?></label>
        <div class="target-amount">
          <label for="set-target-amount-label" style="margin-top: 20px;"><?php _e('Target Amount', 'whydonate-v2') ?></label>
          <input type="number" id="set-target-amount-input" name="set-target-amount-input" style="margin-top: 20px; margin-left: 15px;">
          <span class="description"><?php _e('(optional)', 'whydonate-v2'); ?></span>
          <span class="description" id="amount-error-message" style="display: none; font-weight: 600; font-size: 12px; color: red"><?php _e('Decimals are not allowed.', 'whydonate-v2'); ?></span>
          <span class="description" id="negetive-amount-error-message" style="display: none; font-weight: 600; font-size: 12px; color: red"><?php _e('Negetive amount is not allowed.', 'whydonate-v2'); ?> </span>
        </div>

        <div class="end-date">
          <label for="set-end-date-label" style="margin-top: 20px;"><?php _e('End Date', 'whydonate-v2') ?></label>
          <input type="date" id="set-end-date-input" name="set-end-date-input" style="margin-top: 20px; margin-left: 15px;" min=<?php echo date("Y-m-d") ?>>
          <span class="description"><?php _e('(optional)', 'whydonate-v2'); ?></span>
        </div>
      </div>

      <div class="tipbox-div" style="margin-top: 30px; display: flex; flex-direction: column">
        <!-- <label style="font-size: 16px; font-weight: 600; margin-bottom: 10px; width: 100% ">Set payment charges</label> -->
        <label style="font-size: 16px; font-weight: 400;"><?php _e('Tipbox settings', 'whydonate-v2') ?></label>
        <div style="margin-top: 10px">
          <div style="margin: 10px">
            <input type="radio" id="tipbox-enable" name="tipbox" value="1" checked>
            <label for="male" style="vertical-align: top"><?php _e('Enable', 'whydonate-v2') ?></label><br>
          </div>
          <div style="margin: 10px">
            <input type="radio" id="tipbox-disable" name="tipbox" value="0">
            <label for="female" style="vertical-align: top"><?php _e('Disable', 'whydonate-v2') ?></label><br>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-style" name="addNewFundraiser" id="save-new-fundraiser" style="float: none;
                margin-top: 20px;
                margin-left: 0px;
                height: 40px;
                width: 80px;"><?php _e('Save', 'whydonate-v2') ?>
      </button>

    </form>

    <!-- <div id="create-fundraiser-msg" style="background:khaki; height: 40px; display: none">
      <label style="font-size: 18px; margin-left: 10px; margin-top:10px">Good news! Fundraiser created from this plugin are visible only in this plugin. To reach more people and increase your chances of donations, publish your fundraiser on <a href="https://www.whydonate.nl">Whydonate platform</a> </label>
    </div> -->

  </div>
  <div class="modal" id="modal">
    <!-- Place at bottom of page -->
  </div>

</div>