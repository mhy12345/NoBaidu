<?php
class NoBaiduSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'No Baidu Settings', 
            'manage_options', 
            'no-baidu-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('no_baidu_option');
        ?>
        <div class="wrap">
            <h1>No Baidu 设置</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'no_baidu_option_group' );
                do_settings_sections( 'no-baidu-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'no_baidu_option_group', // Option group
            'no_baidu_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'no_baidu_user_preference', // ID
            '偏好设置', // Title
            array( $this, 'print_section_info' ), // Callback
            'no-baidu-setting-admin' // Page
        );  

        add_settings_field(
            'warn_text', // ID
            '抵制文字', // Title 
            array( $this, 'warn_text_callback' ), // Callback
            'no-baidu-setting-admin', // Page
            'no_baidu_user_preference' // Section           
        );      

        add_settings_field(
            'method', 
            '抵制方式', 
            array( $this, 'method_callback' ), 
            'no-baidu-setting-admin', 
            'no_baidu_user_preference'
        );      

        add_settings_field(
            'change_robots', 
            '是否允许百度爬虫', 
            array( $this, 'change_robots_callback' ), 
            'no-baidu-setting-admin', 
            'no_baidu_user_preference'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['warn_text'] ) )
            $new_input['warn_text'] = sanitize_text_field( $input['warn_text'] );

        if( isset( $input['method'] ) )
            $new_input['method'] = absint( $input['method'] );
        if( isset( $input['change_robots'] ) )
            $new_input['change_robots'] = absint( $input['change_robots'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print '';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function warn_text_callback()
    {
        printf(
            '<textarea rows="3" cols="50" id="warn_text" name="no_baidu_option[warn_text]"> %s </textarea>',
            isset( $this->options['warn_text'] ) ? esc_attr( $this->options['warn_text']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function method_callback()
    {
		$tag0 = '';
		$tag1 = '';
		if (isset( $this->options['method'] )) {
		    $tag0 = esc_attr( $this->options['method']) == 0 ? 'checked' : '';
		    $tag1 = esc_attr( $this->options['method']) == 1 ? 'checked' : '';
		}
        printf(
			'<input type="radio" id="method" name="no_baidu_option[method]" value="0" %s>仅显示为页头条幅</input>
			<input type="radio" id="method" name="no_baidu_option[method]" value="1" %s>替代整个页面</input>'
			, $tag0, $tag1
        );
    }
    public function change_robots_callback()
    {
		$tag0 = '';
		$tag1 = '';
		if (isset( $this->options['change_robots'] )) {
		    $tag0 = esc_attr( $this->options['change_robots']) == 0 ? 'checked' : '';
		    $tag1 = esc_attr( $this->options['change_robots']) == 1 ? 'checked' : '';
		}
        printf(
			'<input type="radio" id="change_robots_0" name="no_baidu_option[change_robots]" value="0" %s>不阻止百度爬虫</input>
			<input type="radio" id="change_robots_1" name="no_baidu_option[change_robots]" value="1" %s>阻止百度爬虫</input>'
			, $tag0, $tag1
        );
    }
}

if( is_admin() )
    $no_baidu_settings_page = new NoBaiduSettingsPage();
