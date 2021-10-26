<?php 

class websites {

	public function __construct(){

		add_action( 'init', array($this, 'saas_website_post_type_init') );

		add_action( 'woocommerce_checkout_process', array($this, 'saas_set_password_in_cookies') );

		add_action('woocommerce_thankyou', array($this, 'saas_assign_website_token_to_subscriber'));

		add_action( 'admin_notices', array($this, 'saas_admin_notices') );

	}

	public function saas_website_post_type_init() {
	    $labels = array(
	        'name'                  => _x( 'Websites', 'Post type general name', 'saas' ),
	        'singular_name'         => _x( 'Website', 'Post type singular name', 'saas' ),
	        'menu_name'             => _x( 'Websites', 'Admin Menu text', 'saas' ),
	        'name_admin_bar'        => _x( 'Website', 'Add New on Toolbar', 'saas' ),
	        'add_new'               => __( 'Add New Website', 'saas' ),
	        'add_new_item'          => __( 'Add New Website', 'saas' ),
	        'new_item'              => __( 'New Website', 'saas' ),
	        'edit_item'             => __( 'Edit Website', 'saas' ),
	        'view_item'             => __( 'View Website', 'saas' ),
	        'all_items'             => __( 'All Websites', 'saas' ),
	        'search_items'          => __( 'Search Websites', 'saas' ),
	        'parent_item_colon'     => __( 'Parent Websites:', 'saas' ),
	        'not_found'             => __( 'No websites found.', 'saas' ),
	        'not_found_in_trash'    => __( 'No websites found in Trash.', 'saas' ),
	        'featured_image'        => _x( 'Book Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'saas' ),
	        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'saas' ),
	        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'saas' ),
	        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'saas' ),
	        'archives'              => _x( 'Website archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'saas' ),
	        'insert_into_item'      => _x( 'Insert into website', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'saas' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'saas' ),
	        'filter_items_list'     => _x( 'Filter websites list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'saas' ),
	        'items_list_navigation' => _x( 'Websites list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'saas' ),
	        'items_list'            => _x( 'Websites list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'saas' ),
	    );
	 
	    $args = array(
	        'labels'             => $labels,
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => array( 'slug' => 'saas-sites' ),
	        'capability_type'    => 'post',
	        'has_archive'        => true,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => array( 'title' ),
	    );
	 
	    register_post_type( 'saas-sites', $args );
	}

	public function saas_set_password_in_cookies(){

		$password = $_POST['account_password'];
		setcookie("password", $password, time() + (86400 * 30), "/");

	}

	public function saas_assign_website_token_to_subscriber($order_id){

		// getting user email from order id

		$order = wc_get_order($order_id);

		$user_id = $order->get_user_id();

		$userdata = get_userdata( $user_id );

		$useremail = $userdata->user_email;


		// processing curl to get token using user email from order and password from cookie
		
		$ch = curl_init();
		$url = home_url().'/wp-json/jwt-auth/v1/token';

		$fields = array(
			'username' => $useremail,
			'password' => $_COOKIE['password']
		);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec ($ch);

		$data = json_decode($data);

		setcookie("user_token", $data->token, time() + (86400 * 30), "/");

		setcookie("password", '', time() + (86400 * 30), "/");

		curl_close($ch);


		// assign token and website to subscriber

		$website_query = new WP_Query(array(
		    'post_type' => 'saas-sites',
		    'posts_per_page' => 1,
		    'order'		=> 'asc',
		    'meta_query'        => array(
		        array(
		            'key'          =>  'wms_options',
		            'value'        => '"status";i:1;',
		            'compare'      => 'NOT LIKE'
		        )
		    )
		));



		if( $website_query->have_posts() ){

			while( $website_query->have_posts() ){

			    $website_query->the_post();

			    $all_data = get_post_meta(get_the_id(), 'wms_options', true);

			    $all_data['user-id'] = $user_id;

			    $all_data['status'] = 1;

			    $all_data['user-token'] = $data->token;

			    $website_url = $all_data['website-url'];

			    $root_token = $all_data['root-token'];

			    update_post_meta(get_the_id(), 'wms_options', $all_data);


			}

			$redirect_url = $website_url.'/verify-user/?root-token='.$root_token."&user-token=".$data->token;

			wp_redirect($redirect_url);

		}
		
	}


	public function saas_admin_notices() {
		if( ! is_plugin_active('woocommerce/woocommerce.php') ) : 
	    ?>

	    <div class="notice notice-error is-dismissible">
	        <p><?php _e( '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">Woocommerce</a> is required in order to use Software SaaS with JWT !', 'saas' ); ?></p>
	    </div>

		<?php endif; 

		if( ! is_plugin_active('jwt-authentication-for-wp-rest-api/jwt-auth.php') ) :
		?>

	    <div class="notice notice-error is-dismissible">
	        <p><?php _e( '<a href="https://wordpress.org/plugins/jwt-auth/" target="_blank">jwt auth</a> is required in order to use Software SaaS with JWT !', 'saas' ); ?></p>
	    </div>
	    <?php endif; 

	    
	}
 

	
}

new websites;