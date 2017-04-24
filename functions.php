<?php

class themeupdater {

	private $file;

	private $plugin;

	private $basename;

	private $active;

	private $username;

	private $repository;

	private $authorize_token;

	private $github_response;

	public function __construct( $file ) {

		$this->file = $file;
//$this->set_plugin_properties();
$this->basename = $file;
		//add_action( 'admin_init', array( $this, 'set_plugin_properties' ) );

		return $this;
	}

	public function set_plugin_properties() {
		//$this->plugin	= get_plugin_data( $this->file );
		$this->basename = plugin_basename( $this->file );
		//$this->active	= is_plugin_active( $this->basename );
	}

	public function set_username( $username ) {
		$this->username = $username;
	}

	public function set_repository( $repository ) {
		$this->repository = $repository;
	}

	public function authorize( $token ) {
		$this->authorize_token = $token;
	}

	private function get_repository_info() {
	    if ( is_null( $this->github_response ) ) { // Do we have a response?
	        $request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository ); // Build URI

	        if( $this->authorize_token ) { // Is there an access token?
	            $request_uri = add_query_arg( 'access_token', $this->authorize_token, $request_uri ); // Append it
	        }

	        $response = json_decode( wp_remote_retrieve_body( wp_remote_get( $request_uri ) ), true ); // Get JSON and parse it

	        if( is_array( $response ) ) { // If it is an array
	            $response = current( $response ); // Get the first item
	        }

	        if( $this->authorize_token ) { // Is there an access token?
	            $response['zipball_url'] = add_query_arg( 'access_token', $this->authorize_token, $response['zipball_url'] ); // Update our zip url with token
	        }

	        $this->github_response = $response; // Set it to our property
	    }
	}

	public function initialize() {
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'modify_transient' ), 10, 1 );
		//add_filter( 'plugins_api', array( $this, 'plugin_popup' ), 10, 3);
		//add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
	}

	public function modify_transient( $transient ) {

  if( property_exists( $transient, 'checked') ) { // Check if transient has a checked property
if( $checked = $transient->checked ) { // Did Wordpress check for updates?
				$this->get_repository_info(); // Get the repo info

				$out_of_date = version_compare( $this->github_response['tag_name'], $checked[ $this->basename ], 'gt' ); // Check if we're out of date



				if( $out_of_date ) {

                    $new_files = $this->github_response['zipball_url']; // Get the ZIP
                                    $theme_data = wp_get_theme();
            $theme_slug = $theme_data->get_template();
            $transient->response[$theme_slug] = array(
                'new_version' => $this->github_response['tag_name'],
             'package' => $new_files,
                'url' => 'ttt',
                'theme'=>$theme_slug
            );
			}
			}
		}

		return $transient; // Return filtered transient
	}

}
