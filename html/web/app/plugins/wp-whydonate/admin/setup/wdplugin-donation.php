<?php

?>


<div class="container">

    <?php

    if (get_locale() == 'en_US') { ?>
        <div style="display: flex; flex-direction: column">
            <a class="btn-style" style="text-decoration:none; text-align: center; padding-top: 15px; width: 250px; height: 40px; margin-left: 20px; float: left; border-radius: 30px" href="https://www.whydonate.eu/dashboard" target="blank"><?php _e('Go to my Whydonate Dashboard', 'whydonate-v2') ?></a>
            <label style="font-size: 18px; font-weight: 600; margin-top: 10px"><?php _e('Click the button above to view all donations. You will be automatically redirected to the Whydonate Dashboard.', 'whydonate-v2') ?></label>
        </div>


    <?php
    } else {
    ?>
        <div style="display: flex; flex-direction: column">
            <a class="btn-style" style="text-decoration:none; text-align: center; padding-top: 15px; width: 250px; height: 40px; margin-left: 20px; float: left; border-radius: 30px" href="https://www.whydonate.nl/dashboard" target="blank"><?php _e('Go to my Whydonate Dashboard', 'whydonate-v2') ?></a>
            <label style="font-size: 18px; font-weight: 600; margin-top: 10px"><?php _e('Click the button above to view all donations. You will be automatically redirected to the Whydonate Dashboard.', 'whydonate-v2') ?></label>
        </div>
    <?php
    }

    ?>

</div>