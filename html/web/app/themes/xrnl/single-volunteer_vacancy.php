<?php
/**
 * The template for displaying posts for volunteer positions
 */

get_header(); ?>

<?php $volunteerPageURL = get_permalink(apply_filters('wpml_object_id', 51, 'page', true)); ?>
<article id="post-<?php the_ID(); ?>" ?>
<div class="row p-2 pt-4 p-md-5 m-2 bg-navy text-white background-icon-container">
<img src="/app/uploads/2019/04/XR-symbol.svg" class="background-icon">
    <div class="col-12 col-xl-8">
<a href="<?php echo $volunteerPageURL ?>" class="btn btn-black mb-4"><i class="fas fa-arrow-left"></i>
<?php _e('View all roles', 'theme-xrnl'); ?>
</a>
<header>
<h2><?php the_title(); ?></h2>
</header>
<?php $role = json_decode(get_the_content()); ?>
    <h4 class="xr-font text-white"><?php echo $role->workingGroup ?>, <?php echo $role->localGroup ?></h4>
    <h6>
        <?php _e('Published on', 'theme-xrnl'); ?>
        <?php the_date(); ?></h6>
    <h5 class="role-section-header">
        <?php _e('Responsibilities', 'theme-xrnl'); ?>
    </h5>
    <p class="preserve-line-breaks"><?php echo $role->responsibilities ?></p>
    <?php if($role->description): ?>
    <h5 class="role-section-header">
        <?php _e('Description', 'theme-xrnl'); ?>
    </h5>
    <p class="preserve-line-breaks"><?php echo $role->description ?></p>
    <?php endif; ?>
    <?php if($role->requirements): ?>
    <h5 class="role-section-header">
    <?php _e('Requirements', 'theme-xrnl'); ?>
    </h5>
    <p class="preserve-line-breaks"><?php echo $role->requirements ?></p>
    <?php endif; ?>
    <h5 class="role-section-header">
    <?php _e('Time commitment', 'theme-xrnl'); ?>
    </h5>
    <p><?php echo $role->timeCommitment->min ?>&ndash;<?php echo $role->timeCommitment->max ?> <?php _e('hours / week', 'theme-xrnl'); ?></p>
    </div>
    </div>
    <div class="p-5 m-2 bg-navy text-white">
    <h4 class="role-section-header" style="margin: 0"><?php _e('Apply', 'theme-xrnl'); ?></h3>
    <p>
    <?php _e('Contact the role aide to apply or to learn more about the role.', 'theme-xrnl'); ?>
    </p>
    <div class="mt-2">
        <div>
            <i class="fas fa-envelope mr-2"></i>
            <b>Email: </b>
            <a href="mailto:<?php echo $role->email ?>?subject=Role application: <?php echo $role->title ?>" target="_blank">
            <?php echo $role->email ?>
            </a>
        </div>
        <div>
            <i class="fas fa-comment mr-2"></i>
            <b>Mattermost: </b><a href="https://organise.earth/xr-netherlands/messages/<?php echo $role->mattermostId ?>" target="_blank">
            <?php echo $role->mattermostId ?>
            </a>
        </div>
        <?php if($role->phone): ?>
        <div>
            <i class="fas fa-phone mr-2"></i>
            <b><?php _e('Phone', 'theme-xrnl'); ?>: </b><a href="tel:<?php echo $role->phone ?>" target="_blank">
            <?php echo $role->phone ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
</div>
        </article>
<?php get_footer(); ?>
