<?php
	
if (!class_exists('uwfWidgetControls')) {
	require_once( plugin_dir_path( __FILE__ ) . 'uwfWidgetControls.class.php' );
}
	
class ActionNetwork_Action_Widget extends WP_Widget {
	
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widgets_ops = array(
			'classname' => 'actionnetwork_action_widget',
			'description' => __('Displays an Action Network action as a widget', 'actionnetwork'),
		);
		parent::__construct( 'actionnetwork_action_widget', __('Action Network Action', 'actionnetwork'), $widgets_ops );
	}
	
	/**
	 * Output the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		if (!isset($instance['an_wp_id']) || !$instance['an_wp_id']) { return; }
		
		echo $args['before_widget'];
		if (!empty( $instance['title'])) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title']) . $args ['after_title'];
		}
		$shortcode = '[actionnetwork id="' . $instance['an_wp_id'] . '"';
		$shortcode_args = array(
			'size',
			'style',
			'thank_you',
			'help_us',
			'hide_social',
			'hide_email',
			'hide_embed',
		);
		foreach ($shortcode_args as $arg) {
			if (isset($instance[$arg]) && $instance[$arg]) {
				$shortcode .= " $arg=\"".$instance[$arg].'"';
			}
		}
		$shortcode .= ']';
		echo do_shortcode( $shortcode );
		echo $args['after_widget'];
	}
	
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'actionnetwork';
		$actions = $wpdb->get_results("SELECT wp_id, title, type FROM $table_name WHERE enabled=1 AND hidden=0");
		$actions_list = array();
		foreach ($actions as $action) {
			$actions_list[ $action->wp_id ] = $action->title . ' (' . $action->type . ')';
		}
		
		// outputs the options form on admin
		$args = array(
			'title' => array(
				'label' => __('Widget Title', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
			),
			'an_wp_id' => array(
				'label' => __('Action to display', 'actionnetwork'),
				'type' => 'select',
				'options' => $actions_list,
				'advanced' => false,
			),
			'size' => array(
				'label' => __('Size', 'actionnetwork'),
				'type' => 'select',
				'options' => array(
					'standard' => 'standard',
					'full' => 'full',
				),
				'default' => 'standard',
				'advanced' => false,
			),
			'style' => array(
				'label' => __('Style', 'actionnetwork'),
				'type' => 'select',
				'options' => array(
					'default' => 'default',
					'layout_only' => 'layout_only',
					'no' => 'no',
				),
				'default' => 'layout_only',
				'advanced' => false,
			),
			'thank_you' => array(
				'label' => __('Custom Thank-You Message', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
			),
			'help_us' => array(
				'label' => __('Custom "Help Us" Message', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
			),
			'hide_social' => array(
				'label' => __('Hide social sharing buttons', 'actionnetwork'),
				'type' => 'checkbox',
				'advanced' => true,
			),
			'hide_email' => array(
				'label' => __('Hide email sharing form', 'actionnetwork'),
				'type' => 'checkbox',
				'advanced' => true,
			),
			'hide_embed' => array(
				'label' => __('Hide embed codes', 'actionnetwork'),
				'type' => 'checkbox',
				'advanced' => true,
			),
		);
		
		wp_enqueue_style( 'actionnetwork-widget-css', plugins_url('../widget-controls.css', __FILE__) );
		wp_register_script( 'actionnetwork-widget-js', plugins_url('../widget-controls.js', __FILE__) );
		$translation_array = array(
			'showAdvanced' => __( 'Show Advanced Controls', 'actionnetwork' ),
		);
		wp_localize_script( 'actionnetwork-widget-js', 'widgetcontrolText', $translation_array );
		wp_enqueue_script( 'actionnetwork-widget-js' );
		
		uwfWidgetControls::render( $this, $args, $instance );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$sanitize_args = array(
			'title',
			'an_wp_id',
			'size',
			'style',
			'thank_you',
			'help_us',
			'hide_social',
			'hide_email',
			'hide_embed',
		);
		foreach ($sanitize_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? esc_html($new_instance[$arg]) : '';
		}
		return $instance;
	}
	
}

class ActionNetwork_List_Widget extends WP_Widget {
	
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widgets_ops = array(
			'classname' => 'actionnetwork_list_widget',
			'description' => __('Displays list of current Action Network actions', 'actionnetwork'),
		);
		parent::__construct( 'actionnetwork_list_widget', __('Action Network List', 'actionnetwork'), $widgets_ops );
	}
	
	/**
	 * Output the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		$output = $args['before_widget'];
		if (!empty( $instance['title'])) {
			$output .= $args['before_title'] . apply_filters( 'widget_title', $instance['title']) . $args ['after_title'];
		}
		$shortcode = '[actionnetwork_list ';
		$shortcode_args = array(
			'n',
			'action_types',
			'link_format',
			'link_text',
			'container_element',
			'container_class',
			'item_element',
			'item_class',
			'no_actions',
			'no_actions_hide',
		);
		foreach ($shortcode_args as $arg) {
			if (isset($instance[$arg]) && $instance[$arg]) {
				$shortcode .= " $arg=\"".$instance[$arg].'"';
			}
		}
		if (isset($instance['action_types'])) {
			$action_types = unserialize( $instance['action_types'] );
			$shortcode .= ' action_types="' . implode(',',$action_types) . '"';
		}
		$shortcode .= ']';
		if ( isset($instance['twig']) && $instance['twig'] ) {
			$shortcode .= $instance['twig'] . '[/actionnetwork_list]';
		}
		$shortcode_output = do_shortcode( $shortcode );
		
		if (isset($instance['no_actions_hide']) && $instance['no_actions_hide'] && !$shortcode_output) { return; }
		
		$output .= $shortcode_output;
		
		if ( isset($instance['footer']) && $instance['footer'] ) {
			$output .= wpautop( $instance['footer'] );
		}
		$output .= $args['after_widget'];
		
		echo $output;
	}
	
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$args = array(
			'title' => array(
				'label' => __('Widget Title', 'actionnetwork'),
				'type' => 'text',
				'default' => __('Take Action', 'actionnetwork'),
				'advanced' => false,
			),
			'n' => array(
				'label' => __('Number of Actions to display', 'actionnetwork'),
				'type' => 'number',
				'default' => 5,
				'advanced' => false,
				'description' => __( 'Set to zero to display all available actions', 'actionnetwork' ),
			),
			'action_types' => array(
				'label' => __('Action Types to display', 'actionnetwork'),
				'type' => 'checkboxes',
				'options' => array('petition','advocacy_campaign','fundraising_page','form','event','ticketed_event'),
				'default' => serialize(array('petition','advocacy_campaign','fundraising_page','form')),
				'advanced' => false,
			),
			'link_text' => array(
				'label' => __('Link text', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'classes' => 'widget-control-code',
				'description' => __( 'Template for text of link. Uses twig-like token for title. Leave blank to default to <code>{{ action.title }}</code>', 'actionnetwork' ),
			),
			'link_format' => array(
				'label' => __('Link format', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'classes' => 'widget-control-code',
				'description' => __( 'If left blank, will link to action page on Action Network. Token <code>{{ action.link }}</code> can be used to, for example, create a redirect URL', 'actionnetwork' ),
			),
			'no_actions_hide' => array(
				'label' => __('Hide widget if there are no current actions', 'actionnetwork'),
				'type' => 'checkbox',
				'advanced' => false,
			),
			'no_actions' => array(
				'label' => __('No Actions text', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'description' => __( 'Text to display if there are no current actions. Leave blank to default to "No current actions." Accepts HTML.', 'actionnetwork' ),
			),
			'footer' => array(
				'label' => __('Footer', 'actionnetwork'),
				'type' => 'textarea',
				'advanced' => false,
				'description' => __( 'Text to display after list of actions. <code>wpautop</code> will be applied (adding line breaks). Accepts HTML.', 'actionnetwork' ),
			),
			'container_element' => array(
				'label' => __('Container Element', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'HTML element (without angle brackets) to contain the list. If left blank, will default to <code>ul</code>', 'actionnetwork' ),
			),
			'container_class' => array(
				'label' => __('Container Class', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'Class to be applied to list container element. If left blank, will default to <code>actionnetwork-list</code>', 'actionnetwork' ),
			),
			'item_element' => array(
				'label' => __('Item Element', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'HTML element (without angle brackets) for each item in the list. If left blank, will default to <code>li</code>', 'actionnetwork' ),
			),
			'item_class' => array(
				'label' => __('Item Class', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'Class to be applied to list item element. If left blank, will default to <code>actionnetwork-list-item</code>', 'actionnetwork' ),
			),
			'twig' => array(
				'label' => __('Twig template', 'actionnetwork'),
				'type' => 'textarea',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( "Twig-style template for widget. Overrides Link Text and all other advanced settings\n\nMust have control structure <code>{% for action in actions %} {% else %} {% endfor %}</code>.\n\nAvailable tokens are <code>{{ action.title }}</code>, <code>{{ action.link }}</code> and <code>{{ action.id }}</code>", 'actionnetwork' ),
			),
		);
		
		wp_enqueue_style( 'actionnetwork-widget-css', plugins_url('../widget-controls.css', __FILE__) );
		wp_register_script( 'actionnetwork-widget-js', plugins_url('../widget-controls.js', __FILE__) );
		$translation_array = array(
			'showAdvanced' => __( 'Show Advanced Controls', 'actionnetwork' ),
		);
		wp_localize_script( 'actionnetwork-widget-js', 'widgetcontrolText', $translation_array );
		wp_enqueue_script( 'actionnetwork-widget-js' );
		
		uwfWidgetControls::render( $this, $args, $instance );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$sanitize_args = array(
			'title',
			'n',
			'link_format',
			'no_actions_hide',
			'container_element',
			'container_class',
			'item_element',
			'item_class',
		);
		foreach ($sanitize_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? esc_html($new_instance[$arg]) : '';
		}
		$html_args = array(
			'no_actions',
			'footer',
			'link_text',
			'twig',
		);
		foreach ($html_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? wp_kses_post($new_instance[$arg]) : '';
		}
		$instance['action_types'] = ( isset($new_instance['action_types']) && is_array($new_instance['action_types']) ) ? serialize($new_instance['action_types']) : serialize( array() );
		return $instance;
	}
	
}

/* END ActionNetwork_List_Widget */
	
class ActionNetwork_Calendar_Widget extends WP_Widget {
	
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widgets_ops = array(
			'classname' => 'actionnetwork_calendar_widget',
			'description' => __('Displays list of upcoming Action Network events', 'actionnetwork'),
		);
		parent::__construct( 'actionnetwork_calendar_widget', __('Action Network Calendar', 'actionnetwork'), $widgets_ops );
	}
	
	/**
	 * Output the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if (!empty( $instance['title'])) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title']) . $args ['after_title'];
		}
		$shortcode = '[actionnetwork_calendar ignore_url_id="1"';
		$shortcode_args = array(
			'n',
			'no_events',
			'location',
			'description',
			'date_format',
			'link_format',
			'link_text',
			'container_element',
			'container_class',
			'item_element',
			'item_class',
		);
		foreach ($shortcode_args as $arg) {
			if (isset($instance[$arg]) && $instance[$arg]) {
				$shortcode .= " $arg=\"".$instance[$arg].'"';
			}
		}
		$shortcode .= ']';
		if ( isset($instance['twig']) && $instance['twig'] ) {
			$shortcode .= $instance['twig'] . '[/actionnetwork_calendar]';
		}
		echo do_shortcode( $shortcode );
		if ( isset($instance['footer']) && $instance['footer'] ) {
			echo wpautop( $instance['footer'] );
		}
		echo $args['after_widget'];
	}
	
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$args = array(
			'title' => array(
				'label' => __('Widget Title', 'actionnetwork'),
				'type' => 'text',
				'default' => __('Upcoming Events', 'actionnetwork'),
				'advanced' => false,
			),
			'n' => array(
				'label' => __('Number of Events to display', 'actionnetwork'),
				'type' => 'number',
				'default' => 3,
				'advanced' => false,
				'description' => __( 'Set to zero to display all available events', 'actionnetwork' ),
			),
			'date_format' => array(
				'label' => __('Date format', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'classes' => 'widget-control-code',
				'description' => __( 'Formatting string for date. Leave blank to default to F j, Y.', 'actionnetwork' ) . ' <a href="http://php.net/date" target="_blank">' . __( 'Date format documentation', 'actionnetwork' ) . '</a>',
			),
			'link_text' => array(
				'label' => __('Link text', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'classes' => 'widget-control-code',
				'description' => __( 'Template for text of link. Uses twig-like tokens for title and date. Leave blank to default to <code>{{ event.date }}: {{ event.title }}</code>', 'actionnetwork' ),
			),
			'link_format' => array(
				'label' => __('Link format', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'classes' => 'widget-control-code',
				'description' => __( 'If left blank, will link to event page on Action Network. Otherwise, use <code>{{ event.id }}</code> token to link to a page that has the [actionnetwork_calendar] shortcode on it (i.e., something like <code>/calendar/{{ event.id }}</code>), which will then display the event. This is the only way to link to non-API-synced events.', 'actionnetwork' ),
			),
			'location' => array(
				'label' => __('Display location', 'actionnetwork'),
				'type' => 'checkbox',
				'advanced' => false,
			),
			'description' => array(
				'label' => __('Display description', 'actionnetwork'),
				'type' => 'checkbox',
				'advanced' => false,
			),
			'no_events' => array(
				'label' => __('No Events text', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'description' => __( 'Text to display if there are no upcoming events. Leave blank to default to "No upcoming events." Accepts HTML.', 'actionnetwork' ),
			),
			'footer' => array(
				'label' => __('Footer', 'actionnetwork'),
				'type' => 'textarea',
				'advanced' => false,
				'description' => __( 'Text to display after list of events (most useful to link to a full calendar page that has the <code>[actionnetwork_calendar]</code> shortcode on it). <code>wpautop</code> will be applied (adding line breaks). Accepts HTML.', 'actionnetwork' ),
			),
			'container_element' => array(
				'label' => __('Container Element', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'HTML element (without angle brackets) to contain the list. If left blank, will default to <code>ul</code>', 'actionnetwork' ),
			),
			'container_class' => array(
				'label' => __('Container Class', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'Class to be applied to list container element. If left blank, will default to <code>actionnetwork-calendar</code>', 'actionnetwork' ),
			),
			'item_element' => array(
				'label' => __('Item Element', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'HTML element (without angle brackets) for each item in the list. If left blank, will default to <code>li</code>', 'actionnetwork' ),
			),
			'item_class' => array(
				'label' => __('Item Class', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'Class to be applied to list item element. If left blank, will default to <code>actionnetwork-calendar-item</code>', 'actionnetwork' ),
			),
			'twig' => array(
				'label' => __('Twig template', 'actionnetwork'),
				'type' => 'textarea',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( "Twig-style template for widget. Overrides Link Text and all other advanced settings\n\nMust have control structure <code>{% for event in events %} {% else %} {% endfor %}</code>.\n\nAvailable tokens are <code>{{ event.title }}</code>, <code>{{ event.date }}</code>, <code>{{ event.link }}</code>, <code>{{ event.id }}</code>, <code>{{ event.location }}</code> and <code>{{ event.description }}</code>", 'actionnetwork' ),
			),
		);
		
		wp_enqueue_style( 'actionnetwork-widget-css', plugins_url('../widget-controls.css', __FILE__) );
		wp_register_script( 'actionnetwork-widget-js', plugins_url('../widget-controls.js', __FILE__) );
		$translation_array = array(
			'showAdvanced' => __( 'Show Advanced Controls', 'actionnetwork' ),
		);
		wp_localize_script( 'actionnetwork-widget-js', 'widgetcontrolText', $translation_array );
		wp_enqueue_script( 'actionnetwork-widget-js' );
		
		uwfWidgetControls::render( $this, $args, $instance );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$sanitize_args = array(
			'title',
			'n',
			'location',
			'description',
			'date_format',
			'link_format',
			'container_element',
			'container_class',
			'item_element',
			'item_class',
		);
		foreach ($sanitize_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? esc_html($new_instance[$arg]) : '';
		}
		$html_args = array(
			'no_events',
			'footer',
			'link_text',
			'twig',
		);
		foreach ($html_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? wp_kses_post($new_instance[$arg]) : '';
		}
		return $instance;
	}
	
}

class ActionNetwork_Signup_Widget extends WP_Widget {
	
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widgets_ops = array(
			'classname' => 'actionnetwork_signup_widget',
			'description' => __('Displays signup form for Action Network', 'actionnetwork'),
		);
		$widget_name = __('Action Network Signup', 'actionnetwork');
		if (!get_option('actionnetwork_api_key', null)) {
			$widget_name .= ': ' . __('disabled', 'actionnetwork');
		}
		parent::__construct( 'actionnetwork_signupwidget', $widget_name, $widgets_ops );
	}
	
	/**
	 * Output the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		if (!get_option('actionnetwork_api_key', null)) { return; }
		
		// process submission
		$submission = $this->processForm( $instance );
		$first_name = isset($submission['first_name']) ? $submission['first_name'] : '';
		$last_name = isset($submission['last_name']) ? $submission['last_name'] : '';
		$email = isset($submission['email']) ? $submission['email'] : '';
		$zip_code = isset($submission['zip_code']) ? $submission['zip_code'] : '';
		$errors = isset($submission['errors']) ? $submission['errors'] : array();
		
		wp_enqueue_style( 'actionnetwork-signup-css', plugins_url('../signup.css', __FILE__) );
		echo $args['before_widget'];
		if (!empty( $instance['title'])) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title']) . $args ['after_title'];
		}
		
		// get instance info
		$defaults = array(
			'introduction' => '',
			'first_name_label' => __('First Name', 'actionnetwork'),
			'last_name_label' => __('Last Name', 'actionnetwork'),
			'email_label' => __('Email', 'actionnetwork'),
			'zip_code_label' => __('Zip Code', 'actionnetwork'),
			'submit' => __('Submit', 'actionnetwork'),
			'ajax' => 0,
			'thank_you_message' => __('Thank you for signing up!', 'actionnetwork'),
			'container_element' => 'ul',
			'container_class' => 'actionnetwork-signup',
			'item_element' => 'li',
			'item_class' => 'actionnetwork-signup-item',
		);
		foreach ($defaults as $key => $value) {
			if (!isset($instance[$key]) || !$instance[$key]) { $instance[$key] = $value; }
		}
		extract($instance);
		
		if (isset($submission['message']) && $submission['message']) {
			echo "<div class=\"actionnetwork-signup-message" . (count($errors) ? ' error' : '') . "\">";
			echo $submission['message'];
			echo "</div>";
		}
		
		if ($ajax) {
			wp_enqueue_script( 'actionnetwork-signup-js', plugins_url('../signup.js', __FILE__), array( 'jquery' ), false, true );
			wp_localize_script( 'actionnetwork-signup-js', 'ajax_object',
            	array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
		
		$form = "<form class=\"actionnetwork-signup-form". ($ajax ? ' use-ajax' : '') . ( $submission ? ' submitted' : '' ) ."\" method=\"post\" novalidate=\"novalidate\">\n";
		$form .= '<input type="hidden" name="widget_id" value="'.$args['widget_id'].'" />';
		$form .= wp_nonce_field( 'actionnetwork_signup', 'actionnetwork_signup_nonce_field', true, false );
		$form .= $introduction ? wpautop($introduction) : '';
		$form .= "<$container_element class=\"$container_class\">";
		
		// first name
		if ($first_name_display) {
			$error_class = isset($errors['first_name']) ? ' error' : '';
			$form .= "<$item_element class=\"$item_class $item_class-first-name\">\n";
			$form .= "<label for=\"{$args['widget_id']}-first-name\">$first_name_label" . ( $first_name_require ? " <span class=\"required\">*</span>" : '' ) . "</label>\n";
			$form .= "<input id=\"{$args['widget_id']}-first-name\" name=\"actionnetwork_signup_first_name\" type=\"text\" class=\"text" . ( $first_name_require ? " required" : '' ) . "$error_class\" value=\"$first_name\" />\n";
			$form .= isset($errors['first_name']) ? "<label class=\"error\" for=\"{$args['widget_id']}-first-name\">".$errors['first_name']."</label>" : '';
			$form .= "</$item_element>\n";
		}
		
		// last name
		if ($last_name_display) {
			$error_class = isset($errors['last_name']) ? ' error' : '';
			$form .= "<$item_element class=\"$item_class $item_class-last-name\">\n";
			$form .= "<label for=\"{$args['widget_id']}-last-name\">$last_name_label" . ( $last_name_require ? " <span class=\"required\">*</span>" : '' ) . "</label>\n";
			$form .= "<input id=\"{$args['widget_id']}-last-name\" name=\"actionnetwork_signup_last_name\" type=\"text\" class=\"text" . ( $last_name_require ? " required" : '' ) . "$error_class\" value=\"$last_name\" />\n";
			$form .= isset($errors['last_name']) ? "<label class=\"error\" for=\"{$args['widget_id']}-last-name\">".$errors['last_name']."</label>" : '';
			$form .= "</$item_element>\n";
		}
		
		// email (always required by Action Network)
		$error_class = isset($errors['email']) ? ' error' : '';
		$form .= "<$item_element class=\"$item_class $item_class-email\">";
		$form .= "<label for=\"{$args['widget_id']}-email\">$email_label <span class=\"required\">*</span></label>\n";
		$form .= "<input id=\"{$args['widget_id']}-email\" type=\"email\" name=\"actionnetwork_signup_email\" class=\"email required$error_class\" value=\"$email\" />";
		$form .= isset($errors['email']) ? "<label class=\"error\" for=\"{$args['widget_id']}-email\">".$errors['email']."</label>" : '';
		$form .= "</$item_element>";
		
		// zipcode (always required by Action Network)
		$error_class = isset($errors['zip_code']) ? ' error' : '';
		$form .= "<$item_element class=\"$item_class $item_class-zip-code\">";
		$form .= "<label for=\"{$args['widget_id']}-zip-code\">$zip_code_label <span class=\"required\">*</span></label>\n";
		$form .= "<input id=\"{$args['widget_id']}-zip-code\" type=\"text\" name=\"actionnetwork_signup_zip_code\" class=\"text required$error_class\" value=\"$zip_code\" />";
		$form .= isset($errors['zip_code']) ? "<label class=\"error\" for=\"{$args['widget_id']}-zip-code\">".$errors['zip_code']."</label>" : '';
		$form .= "</$item_element>";
		
		// submit button
		$form .= "<$item_element class=\"$item_class $item_class-submit actionnetwork-signup-submit-wrapper\">";
		$form .= "<input id=\"{$args['widget_id']}-submit\" type=\"submit\" value=\"$submit\" />";
		$form .= "</$item_element>";
			
		$form .= "</$container_element></form>";
		
		echo $form;
		echo $args['after_widget'];
	}
	
	public function processForm( $instance, $data = null ) {
		
		if ( $data === null ) { $data = $_REQUEST; }
		
		// check for nonce
		if ( ! isset( $data['actionnetwork_signup_nonce_field'] ) ||
			!! ! wp_verify_nonce( $data['actionnetwork_signup_nonce_field'], 'actionnetwork_signup' )
		) {
			return null;
		}
		
		// sanitize
		$submission['first_name'] = isset( $data['actionnetwork_signup_first_name'] ) ? sanitize_text_field( $data['actionnetwork_signup_first_name'] ) : '';
		$submission['last_name'] = isset( $data['actionnetwork_signup_last_name'] ) ? sanitize_text_field( $data['actionnetwork_signup_last_name'] ) : '';
		$submission['email'] = isset( $data['actionnetwork_signup_email'] ) ? sanitize_email( $data['actionnetwork_signup_email'] ) : '';
		$submission['zip_code'] = isset( $data['actionnetwork_signup_zip_code'] ) ? sanitize_text_field( $data['actionnetwork_signup_zip_code'] ) : '';
		
		// verify
		$submission['errors'] = array();
		
		if (isset($instance['first_name_require']) && $instance['first_name_require'] && !$submission['first_name']) {
			$submission['errors']['first_name'] = __('This field is required','actionnetwork');
		}
		if (isset($instance['last_name_require']) && $instance['last_name_require'] && !$submission['last_name']) {
			$submission['errors']['last_name'] = __('This field is required','actionnetwork');
		}
		if (!is_email($submission['email'])) { $submission['errors']['email'] = __('A valid email is required','actionnetwork'); }
		if (!$submission['zip_code']) { $submission['errors']['zip_code'] = __('This field is required','actionnetwork'); }
		
		// submit to Action Network
		$submission['message'] = '';
		$actionnetwork_api_key = get_option('actionnetwork_api_key', null);
		if ($actionnetwork_api_key && !count($submission['errors'])) {
			$ActionNetwork = new ActionNetwork($actionnetwork_api_key);
			$person = array(
				'email' => $submission['email'],
				'postal_code' => $submission['zip_code'],
			);
			if ($submission['first_name']) { $person['given_name'] = $submission['first_name']; }
			if ($submission['last_name']) { $person['family_name'] = $submission['last_name']; }
			
			if ( isset( $instance['tags'] ) ) {
				$tags = unserialize( $instance['tags'] );
				if ( ! is_array( $tags ) ) { $tags = null; }
			} else {
				$tags = null;
			}
			
			$response = $ActionNetwork->signupPerson( $person, $tags );
			if (isset($response->error)) {
				$submission['message'] = wpautop( __('There was an error connecting to Action Network. Please try again later.', 'actionnetwork') );
				$submission['errors']['connection'] = true;
			} else {
				$submission['message'] = wpautop( $instance['thank_you_message'] );
			}
		} else {
			$submission['message'] = wpautop( __('There were errors in the form. Please try again.', 'actionnetwork') );
		}
		
		return $submission;
	}
	
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin pages
		
		// disable widget if there is no API key
		$actionnetwork_api_key = get_option('actionnetwork_api_key', null);
		
		if (!$actionnetwork_api_key) {
			echo '<p>';
			_e('Signup widget requires an API key.', 'actionnetwork');
			echo " ";
			printf(
				/* translators: %s is link to text "settings page" */
				__('Please visit the plugin %s and enter your API key.', 'actionnetwork'),
				'<a href="admin.php?page=actionnetwork&actionnetwork_tab=settings">' . __('settings page','actionnetwork') . '</a>'
			);
			echo '</p>';
			return;
		}
		
		$ActionNetwork = new ActionNetwork($actionnetwork_api_key);
		$tags_response = $ActionNetwork->getFullSimpleCollection( 'tags' );
		$tags = array();
		if ( ! isset( $tags_response->error ) ) {
			foreach ( $tags_response as $tag_item ) {
				if ( isset($tag_item['title']) ) { $tags[] = $tag_item['title']; }
			}
		}
		
		$args = array(
			'title' => array(
				'label' => __('Widget Title', 'actionnetwork'),
				'type' => 'text',
				'default' => __('Sign Up', 'actionnetwork'),
				'advanced' => false,
			),
			'introduction' => array(
				'label' => __('Introduction', 'actionnetwork'),
				'type' => 'textarea',
				'advanced' => false,
				'description' => __( 'Text to display before the form. <code>wpautop</code> will be applied (adding line breaks). Accepts HTML.', 'actionnetwork' ),
			),
			'first_name_label' => array(
				'label' => __('First name label', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'description' => __( 'Label for first name field. Leave blank to default to "First Name"', 'actionnetwork' ),
			),
			'first_name_display' => array(
				'label' => __('Display field for first name', 'actionnetwork'),
				'type' => 'checkbox',
				'default' => 1,
				'advanced' => false,
			),
			'first_name_require' => array(
				'label' => __('Require first name', 'actionnetwork'),
				'type' => 'checkbox',
				'default' => 0,
				'advanced' => false,
			),
			'last_name_label' => array(
				'label' => __('Last name label', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'description' => __( 'Label for last name field. Leave blank to default to "Last Name"', 'actionnetwork' ),
			),
			'last_name_display' => array(
				'label' => __('Display field for last name', 'actionnetwork'),
				'type' => 'checkbox',
				'default' => 1,
				'advanced' => false,
			),
			'last_name_require' => array(
				'label' => __('Require last name', 'actionnetwork'),
				'type' => 'checkbox',
				'default' => 0,
				'advanced' => false,
			),
			'email_label' => array(
				'label' => __('Email label', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'description' => __( 'Label for email field. Leave blank to default to "Email"', 'actionnetwork' ),
			),
			'zip_code_label' => array(
				'label' => __('Zip Code label', 'actionnetwork'),
				'type' => 'text',
				'advanced' => false,
				'description' => __( 'Label for zip code. Leave blank to default to "Zip Code"', 'actionnetwork' ),
			),
			'tags' => array(
				'label' => __('Tags to add', 'actionnetwork'),
				'type' => 'checkboxes',
				'options' => $tags,
				'advanced' => false,
				'description' => __( 'Any checked tags will be added to the person object', 'actionnetwork' ),
			),
			'submit' => array(
				'label' => __('Submit button text', 'actionnetwork'),
				'type' => 'text',
				'default' => __('Join Us', 'actionnetwork'),
				'advanced' => false,
				'description' => __( 'Text to display in the sign-up form button. If left blank, will default to "Submit"', 'actionnetwork' ),
			),
			'ajax' => array(
				'label' => __('Submit using AJAX', 'actionnetwork'),
				'type' => 'checkbox',
				'default' => 0,
				'advanced' => false,
				'description' => __( 'If checked, form will be submitted without loading a new page', 'actionnetwork' ),
			),
			'thank_you_message' => array(
				'label' => __('Thank you message', 'actionnetwork'),
				'type' => 'textarea',
				'advanced' => false,
				'description' => __( 'Text to display after successful signup. If left blank, will default to "Thank you for signing up!" <code>wpautop</code> will be applied (adding line breaks). Accepts HTML.', 'actionnetwork' ),
			),
			'container_element' => array(
				'label' => __('Container Element', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'HTML element (without angle brackets) to contain the form items. If left blank, will default to <code>ul</code>', 'actionnetwork' ),
			),
			'container_class' => array(
				'label' => __('Container Class', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'Class to be applied to form item container element. If left blank, will default to <code>actionnetwork-signup</code>', 'actionnetwork' ),
			),
			'item_element' => array(
				'label' => __('Item Element', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'HTML element (without angle brackets) for each form item. If left blank, will default to <code>li</code>', 'actionnetwork' ),
			),
			'item_class' => array(
				'label' => __('Item Class', 'actionnetwork'),
				'type' => 'text',
				'advanced' => true,
				'classes' => 'widget-control-code',
				'description' => __( 'Class to be applied to list item element. If left blank, will default to <code>actionnetwork-signup-item</code>', 'actionnetwork' ),
			),
		);
		
		wp_enqueue_style( 'actionnetwork-widget-css', plugins_url('../widget-controls.css', __FILE__) );
		wp_register_script( 'actionnetwork-widget-js', plugins_url('../widget-controls.js', __FILE__) );
		$translation_array = array(
			'showAdvanced' => __( 'Show Advanced Controls', 'actionnetwork' ),
		);
		wp_localize_script( 'actionnetwork-widget-js', 'widgetcontrolText', $translation_array );
		wp_enqueue_script( 'actionnetwork-widget-js' );
		
		uwfWidgetControls::render( $this, $args, $instance );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$sanitize_args = array(
			'title',
			'first_name_display',
			'first_name_require',
			'first_name_label',
			'last_name_display',
			'last_name_require',
			'last_name_label',
			'email_label',
			'zip_code_label',
			'submit',
			'ajax',
			'container_element',
			'container_class',
			'item_element',
			'item_class',
		);
		foreach ($sanitize_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? esc_html($new_instance[$arg]) : '';
		}
		$html_args = array(
			'introduction',
			'thank_you_message',
		);
		foreach ($html_args as $arg) {
			$instance[$arg] = isset($new_instance[$arg]) ? wp_kses_post($new_instance[$arg]) : '';
		}
		
		$instance['tags'] = ( isset($new_instance['tags']) && is_array($new_instance['tags']) ) ? serialize($new_instance['tags']) : serialize( array() );
		
		// can't require name fields that aren't displayed
		if (!$instance['first_name_display']) { $instance['first_name_require'] = ''; }
		if (!$instance['last_name_display']) { $instance['last_name_require'] = ''; }
		
		return $instance;
	}
	
}

add_action( 'wp_ajax_actionnetwork_signup', 'actionnetwork_signup_ajax' );
add_action( 'wp_ajax_nopriv_actionnetwork_signup', 'actionnetwork_signup_ajax' );

function actionnetwork_signup_ajax(){
	
	$data_str = isset($_POST['data']) ? $_POST['data'] : '';
	parse_str($data_str, $data);
	
	$widget = new ActionNetwork_Signup_Widget();
	$settings = $widget->get_settings();
	
	$widget_id = isset($data['widget_id']) ? str_replace('actionnetwork_signupwidget-','',$data['widget_id']) : null;
	
	$instance = isset($settings[$widget_id]) ? $settings[$widget_id] : null;
	
	$backend_info = "BACKEND INFO:\n\n";
	$backend_info .= '$_POST:'."\n\n".print_r($_POST,1)."\n\n-----------\n\n";
	$backend_info .= '$data:'."\n\n".print_r($data,1)."\n\n-----------\n\n";
	$backend_info .= '$settings:'."\n\n".print_r($settings,1)."\n\n-----------\n\n";
	$backend_info .= '$instance:'."\n\n".print_r($instance,1)."\n\n-----------\n\n";
	
	// mail('uekissam@gmail.com','backend info',$backend_info,"From: noreply@ueref.org\r\n");
	
	if (is_array($instance)) {
		$submission = $widget->processForm( $instance, $data );
	} else {
		$submission = array( 'message' => 'Could not find widget instance' );
	}
	
	wp_send_json( $submission );
	
	wp_die();
}