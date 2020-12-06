<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $ime_events;
?>
<div class="ime_container">
    <div class="ime_row">
        <div class="ime-column ime_well">
            <h3><?php esc_attr_e( 'Meetup Import', 'import-meetup-events' ); ?></h3>
            <form method="post" id="ime_meetup_form">
           	
               	<table class="form-table">
		            <tbody>
		                <tr class="meetup_group_url">
					    	<th scope="row">
					    		<?php esc_attr_e( 'Meetup Group URL','import-meetup-events' ); ?> : 
					    	</th>
					    	<td>
					    		<input class="ime_text" name="meetup_url" type="url" required="required" />
			                    <span class="ime_small">
			                        <?php _e( 'Insert meetup group url ( Eg. https://www.meetup.com/ny-tech/).', 'import-meetup-events' ); ?>
			                    </span>
					    	</td>
					    </tr>

					    <tr class="import_type_wrapper">
					    	<th scope="row">
					    		<?php esc_attr_e( 'Import type','import-meetup-events' ); ?> : 
					    	</th>
					    	<td>
						    	<?php $ime_events->common->render_import_type(); ?>
					    	</td>
					    </tr>

					    <?php 
					    $ime_events->common->render_import_into_and_taxonomy();
					    $ime_events->common->render_eventstatus_input();
					    ?>

						<tr>
							<th scope="row">
								<?php _e('Author','import-meetup-events'); ?> :
							</th>
							<td>
								<?php wp_dropdown_users( array( 'show_option_none' => esc_attr__( 'Select Author','import-meetup-events'), 'name' => 'event_author', 'option_none_value' => get_current_user_id() ) ); ?>
								<span class="ime_small">
									<?php _e( 'Select event author for imported events. Default event auther is current loggedin user.', 'import-meetup-events' ); ?>
								</span>
							</td>
						</tr>

					</tbody>
		        </table>
                
                <div class="ime_element">
                	<input type="hidden" name="import_origin" value="meetup" />
                    <input type="hidden" name="ime_action" value="ime_import_submit" />
                    <?php wp_nonce_field( 'ime_import_form_nonce_action', 'ime_import_form_nonce' ); ?>
                    <input type="submit" class="button-primary ime_submit_button" style=""  value="<?php esc_attr_e( 'Import Event', 'import-meetup-events' ); ?>" />
                </div>
            </form>
        </div>
    </div>
</div>
