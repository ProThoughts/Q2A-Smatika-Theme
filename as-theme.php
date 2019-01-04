<?php
    if ( !defined( 'AS_VERSION' ) ) { // don't allow this page to be requested directly from browser
        header( 'Location: ../../' );
        exit;
    }
	
    @define( 'THEME_DIR', dirname( __FILE__ ) . '/' );
    @define( 'THEME_URL', as_opt('site_url') . 'as-theme/' . as_get_site_theme() . '/' );
    @define( 'THEME_DIR_NAME', basename( THEME_DIR ) ); 
    @define( 'THEME_TEMPLATES', THEME_DIR . 'templates/' );
    @define( 'THEME_VERSION', "1.2" );
	
    require_once 'as-functions.php';
    require_once THEME_DIR . '/options/options.php';
	as_register_layer('as-admin-options.php', 'Smatika Theme', THEME_DIR, THEME_URL );
    as_register_phrases( THEME_DIR.'as-smatika-lang-*.php', 'smatika' );
	
    class as_html_theme extends as_html_theme_base
    {        function doctype()
        {
            if ( !property_exists( 'as_html_theme_base', 'isRTL' ) ) {
                $this->isRTL = isset( $this->content['direction'] ) && $this->content['direction'] === 'rtl';
            }
            parent::doctype();
        }
		function head()
        {			
            $this->output(
                    '<head>',
                    '<meta http-equiv="content-type" content="' . $this->content['content_type'] . '"/>',
					'<meta charset="utf-8">',
					'<meta name="viewport" content="width=device-width, initial-scale=1">'
            );
            $this->head_title();
            $this->head_metas();
            $this->head_css();
            $this->head_links();
            $this->head_lines();
            $this->head_script();
            $this->head_custom();
            $this->output( '</head>' );
        }
		function head_css()
        {
            parent::head_css();
            $css_paths = array(
                    'fonts'     => 'css/font-awesome.min.css?4.2.0',
                    'bootstrap' => 'css/bootstrap.min.css?3.3.5',
                    'smatika'     => 'css/smatika.css?' . THEME_VERSION,
            );
            if ( as_opt( 'smatika_activate_prod_mode' ) ) {
                $cdn_css_paths = array(
                        'bootstrap' => Smatika_Option_Keys::BS_CSS_CDN,
                        'fonts'     => Smatika_Option_Keys::FA_CDN,
                );
                unset( $css_paths['bootstrap'] );
                unset( $css_paths['fonts'] );
                $this->smatika_resources( $cdn_css_paths, 'css', true );
                $css_paths['smatika'] = 'css/smatika.min.css?' . THEME_VERSION;  //put the smatika.min.css for the prod mode
            }
            $this->smatika_resources( $css_paths, 'css' );
            if( as_opt('smatika_use_local_font') ){
                $this->smatika_resources( array( 'css/open-sans.css?' . THEME_VERSION) );
            } else {
                $this->smatika_resources( array( 'https://fonts.googleapis.com/css?family=Open+Sans:400,700,700italic,400italic' ) , 'css' , true );
            }
			$this->output( '
				<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
				   <!--[if lt IE 9]>
					 <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
					 <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
				<![endif]-->
			' );
        }
/**
         * prints the CSS and JS links
         *
         * @param  array   $paths    list of the resources
         * @param  string  $type     type of the resource css or js
         * @param  boolean $external weather it is relative to the theme or a external to the theme
         *
         * @return null
         */
        function smatika_resources( $paths, $type = 'css', $external = false )
        {
            if ( count( $paths ) ) {
                foreach ( $paths as $key => $path ) {
                    if ( $type === 'js' ) {
                        $this->smatika_js( $path, $external );
                    } else if ( $type === 'css' ) {
                        $this->smatika_css( $path, $external );
                    }
                }
            }
        }
        /**
         * prints the js path
         *
         * @param  string  $path     path of the js file
         * @param  boolean $external weather it is relative to the theme or a external to the theme
         *
         * @return null
         */
        function smatika_js( $path, $external = false )
        {
            if ( $external ) {
                $full_path = $path;
            } else {
                $full_path = THEME_URL . '/' . $path;
            }
            if ( !empty( $path ) ) {
                $this->output( '<script src="' . $full_path . '" type="text/javascript"></script>' );
            }
        }
        /**
         * prints the css path
         *
         * @param  string  $path     path of the css file
         * @param  boolean $external weather it is relative to the theme or a external to the theme
         *
         * @return null
         */
        function smatika_css( $path, $external = false )
        {
            if ( $external ) {
                $full_path = $path;
            } else {
                $full_path = THEME_URL . '/' . $path;
            }
            if ( !empty( $path ) ) {
                $this->output( '<link rel="stylesheet" type="text/css" href="' . $full_path . '"/>' );
            }
        }
		function head_script() // change style of WYSIWYG editor to match theme better
        {
            parent::head_script();
            $js_paths = array(
                    'bootstrap' => 'js/bootstrap.min.js?3.3.5',
                    'smatika'     => 'js/smatika.js?' . THEME_VERSION,
            );
            if ( $this->template == 'admin' ) $js_paths['admin'] = 'js/admin.js?' . THEME_VERSION;
            if ( as_opt( 'smatika_activate_prod_mode' ) ) {
                $cdn_js_paths = array(
                        'bootstrap' => Smatika_Option_Keys::BS_JS_CDN,
                );
                unset( $js_paths['bootstrap'] );
                $this->smatika_resources( $cdn_js_paths, 'js', true );
            }
            $this->smatika_resources( $js_paths, 'js' );
        }
		function body_content()
        {
            $sub_navigation = @$this->content['navigation']['sub'];			
			
            if ( $this->template === 'admin' ) { 
				$sub_navigation['admin/smatika'] = array(
					'label' => as_lang('smatika/smatika_theme'), 
					'url' => as_path_html('admin/smatika'),
					'selected' => as_request() === 'admin/smatika' ? 'selected' : null
				);
				unset( $this->content['navigation']['sub'] );
			}
			
            $navigation = &$this->content['navigation'];
            if ( isset( $navigation['cat'] ) )
				smatika_remove_brackets( $navigation['cat'] );
			
            $this->body_prefix();
            $this->output( '<div class="container">' );
            $this->output( '<div class="top-divider"></div>' );
            $this->output( '</div>' );
            $this->smatika_site_header();
			
            $this->output( '<div class="container visible-xs">' );
            $this->output( '<div class="top-search-bar">' );
            $this->search();
            $this->output( '</div>', '</div>' );
            $this->output( '<main class="smatika-masthead">' );
            $this->output( '<div class="container">' );
            $this->notices();
            $this->output( '</div>' );
            $this->output( '<div class="container">' );
			
            $extra_title_class = $this->smatika_page_has_favorite() ? ' has-favorite' : '';
			
            $this->output( '<div class="page-title' . $extra_title_class . '">' );
            $this->page_title_error();
            $this->output( '</div>' );
            $this->smatika_breadcrumb();
            $this->output( '</div>' );
            $this->output( '</main>' );
            $this->output( '<div class="as-body-wrapper container">', '' );
            $this->widgets( 'full', 'top' );
            $this->header();
            $this->widgets( 'full', 'high' );
			
			if ($sub_navigation && count( $sub_navigation ) ) 
				$this->left_side_bar( $sub_navigation );
            $this->main();
            if ( !$this->smatika_do_hide_sidebar() ) $this->sidepanel();
			
            $this->widgets( 'full', 'low' );
            $this->footer();
            $this->widgets( 'full', 'bottom' );
            $this->output( '</div> <!-- END body-wrapper -->' );
            $this->body_suffix();
        }
		function page_title_error()
        {
            $favorite = @$this->content['favorite'];
            if ( isset( $favorite ) ) $this->output( '<form ' . $favorite['form_tags'] . '>' );
            $this->feed_link();
            $this->output( '<h1>' );
            $this->favorite();
            $this->title();
            $this->output( '</h1>' );
            if ( $this->template == 'not-found' && as_opt( 'smatika_show_custom_404_page' ) ) {
                $this->output( smatika_include_template( 'page-not-found.php', false ) );
            } else if ( isset( $this->content['error'] ) ) $this->error( @$this->content['error'] );
            if ( isset( $favorite ) ) {
                $this->form_hidden_elements( @$favorite['form_hidden'] );
                $this->output( '</form>' );
            }
        }
        /**
         * add RSS feed icon after the page title
         *
         * @return null
         */
        function feed_link()
        {
            if ( $this->smatika_page_has_feed() ) {
                $feed = @$this->content['feed'];
                $this->output( '<a href="' . $feed['url'] . '" title="' . @$feed['label'] . '" class="as-rss-feed"><i class="fa fa-rss as-rss-icon" ></i></a>' );
            }
        }
		function header() // removes member navigation and search from header and replaces with custom header content. Also opens new <div>s
        {
            $this->output( '<div class="as-header clearfix">' );
            $this->header_clear();
            $this->header_custom();
            $this->output( '</div> <!-- END as-header -->', '' );
            $this->output( '<div class="as-main-shadow clearfix">', '' );
            $this->output( '<div class="as-main-wrapper clearfix row">', '' );
        }
		function header_custom() // allows modification of custom element shown inside header after logo
        {
            if ( isset( $this->content['body_header'] ) ) {
                $this->output( '<div class="header-banner">' );
                $this->output_raw( $this->content['body_header'] );
                $this->output( '</div>' );
            }
        }
		function left_side_bar( $sub_navigation )
        {
            $this->output( '<div class="as-left-side-bar" id="sidebar" role="navigation">', '' );
            if ( count( $sub_navigation ) ) {
                $this->output( '<div class="list-group">', '' );
                foreach ( $sub_navigation as $key => $sub_navigation_item ) {
                    $this->smatika_nav_side_bar_item( $sub_navigation_item );
                }
                $this->output( '</div>', '' );
                if ( $this->template === 'admin' ) unset( $this->content['navigation']['sub'] );
            }
            $this->output( '</div>', '<!-- END of left-side-bar -->' );
        }
        /**
         * nav item for the sidebar
         *
         * @param  array $nav_item navigation item
         *
         * @return null
         */
        function smatika_nav_side_bar_item( $nav_item )
        {
            $class = ( !!@$nav_item['selected'] ) ? ' active' : '';
            $icon = ( !!@$nav_item['icon'] ) ? smatika_get_fa_icon( @$nav_item['icon'] ) : '';
            $this->output( '<a href="' . $nav_item['url'] . '" class="list-group-item ' . $class . '">' . $icon . $nav_item['label'] . '</a>' );
        }
		function main()
        {
            $content = $this->content;
            $width_class = ( $this->smatika_do_hide_sidebar() && $this->template != 'admin' ) ? 'col-xs-12' : 'as-main col-md-9 col-xs-12 pull-left';
            $extratags = isset($this->content['main_tags']) ? $this->content['main_tags'] : '';
            $this->output( '<div class="' . $width_class . ( @$this->content['hidden'] ? ' as-main-hidden' : '' ) . '"'.$extratags.'>' );
            if ( !empty( $this->content['navigation']['sub'] ) || $this->template == 'admin' ) {
                $this->smatika_sidebar_toggle_nav_btn();
            }
            $this->widgets( 'main', 'top' );
            if ( !empty( $this->content['navigation']['sub'] ) || $this->template == 'admin' ) {
                $this->output( '<div class="hidden-xs subnav-row clearfix">' );
                $this->nav_main_sub();
                $this->output( '</div>' );
            }
            $this->widgets( 'main', 'high' );
            $this->main_parts( $content );
            $this->widgets( 'main', 'low' );
            $this->page_links();
            $this->suggest_next();
            $this->widgets( 'main', 'bottom' );
            $this->output( '</div> <!-- END as-main -->', '' );
        }
        /**
         * prints sidebar navigation
         *
         * @return  null
         */
        function smatika_sidebar_toggle_nav_btn()
        {
            $this->output( '<div class="row">' );
            $this->output( '<div class="pull-left col-xs-12 visible-xs side-toggle-button">' );
            $this->output( '<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">' );
            $this->output( '<i class="fa fa-chevron-right toggle-icon"></i>' );
            $this->output( '</button>' );
            $this->output( '</div>' );
            $this->output( '</div>' );
        }
		function sidepanel()
        {
            $this->output( '<div class="as-sidepanel col-md-3 col-xs-12 pull-right">' );
            $this->output( '<div class="side-search-bar hidden-xs">' );
            $this->search();
            $this->output( '</div>' );
            $this->widgets( 'side', 'top' );
            $this->sidebar();
            $this->widgets( 'side', 'high' );
            $this->nav( 'cat', 1 );
            $this->widgets( 'side', 'low' );
            $this->output_raw( @$this->content['sidepanel'] );
            $this->feed();
            $this->widgets( 'side', 'bottom' );
            $this->output( '</div>', '' );
        }
        /**
         * the feed icon with a link
         *
         * @return null
         */
        function feed()
        {
            $feed = @$this->content['feed'];
            if ( !empty( $feed ) ) {
                $icon = smatika_get_fa_icon( 'rss' );
                $this->output( '<div class="as-feed">' );
                $this->output( '<a href="' . $feed['url'] . '" class="as-feed-link"> <span class="icon-wrapper"> <span class="as-feed-icon">' . $icon . ' </span></span>' . @$feed['label'] . '</a>' );
                $this->output( '</div>' );
            }
        }
        /**
         * prevent display of regular footer content (see body_suffix()) and replace with closing new <div>s
         *
         * @return  null
         */
        function footer()
        {
            $this->output( '</div> <!-- END main-wrapper -->' );
            $this->output( '</div> <!-- END main-shadow -->' );
        }
		function body_suffix() // to replace standard Q2A footer
        {
            if ( as_opt( 'smatika_show_site_stats_above_footer' ) ) {
                $this->smatika_site_stats_bottom();
            }
            $this->output( '<footer class="smatika-footer">' );
            if ( as_opt( 'smatika_enable_back_to_top_btn' ) ) {
                $this->output( '<a class="smatika-top"><span class="fa fa-chevron-up"></span></a>' );
            }
            $this->output( '<div class="container">' );
            parent::footer();
            $this->output( '</div> <!--END Container-->' );
            $this->output( '</footer> <!-- END footer -->', '' );
        }
		function logged_in()
        {
            if ( as_is_logged_in() ) // output member avatar to login bar
                $this->output(
                        '<div class="as-logged-in-avatar">',
                        AS_FINAL_EXTERNAL_MEMBERS
                                ? as_get_external_avatar_html( as_get_logged_in_memberid(), 24, true )
                                : as_get_member_avatar_html( as_get_logged_in_flags(), as_get_logged_in_email(), as_get_logged_in_handle(),
                                as_get_logged_in_member_field( 'avatarblobid' ), as_get_logged_in_member_field( 'avatarwidth' ), as_get_logged_in_member_field( 'avatarheight' ),
                                24, true ),
                        '</div>'
                );
            parent::logged_in();
            if ( as_is_logged_in() ) { // adds points count after logged in membername
                $memberpoints = as_get_logged_in_points();
                $pointshtml = ( $memberpoints == 1 )
                        ? as_lang_html_sub( 'main/1_point', '1', '1' )
                        : as_lang_html_sub( 'main/x_points', as_html( number_format( $memberpoints ) ) );
                $this->output(
                        '<span class="as-logged-in-points">',
                        '(' . $pointshtml . ')',
                        '</span>'
                );
            }
        }
		function body_header() // adds login bar, member navigation and search at top of page in place of custom header content
        {
            if ( !empty( $this->content['navigation']['main'] ) ) {
                $this->output( $this->smatika_nav_bar( $this->content['navigation'] ) );
                unset( $this->content['navigation']['main'] );
            }
        }
        /**
         * prints the complete navbar
         *
         * @param  $navigation
         *
         * @return text
         */
        function smatika_nav_bar( $navigation )
        {
            ob_start();
            if ( as_opt( 'smatika_enable_top_bar' ) ) {
                smatika_include_template( 'top-header.php' );
            }
            ?>
            <header id="nav-header">
                <nav id="nav" class="navbar navbar-static-top"
                     role="navigation" <?php echo( as_opt( 'smatika_enable_sticky_header' ) ? 'data-spy="affix" data-offset-top="120"' : '' ) ?>>
                    <div class="container">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="glyphicon glyphicon-menu-hamburger"></span>
                            </button>
                        </div>
                        <div class="col-sm-3 col-xs-8 logo-wrapper">
                            <?php $this->logo(); ?>
                        </div>
                        <div class="smatika-navigation col-sm-2 col-xs-3 pull-right">
                            <?php $this->smatika_member_drop_down(); ?>
                        </div>
                        <div class="col-sm-7 navbar-collapse collapse main-nav navbar-left">
                            <ul class="nav navbar-nav inner-drop-nav">
                                <?php $this->smatika_nav_bar_main_links( $navigation['main'] ); ?>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
            <?php
            return ob_get_clean();
        }
        /**
         * prints the navbar search on the top
         *
         * @return null
         */
        function search()
        {
            $search = $this->content['search'];
            $this->output(
                    '<form class="search-form" role="form" ' . $search['form_tags'] . '>',
                    @$search['form_extra']
            );
            $this->search_field( $search );
            // $this->search_button($search);
            $this->output(
                    '</form>'
            );
        }
        /**
         * prints the search field
         *
         * @param  array $search
         *
         * @return null
         */
        function search_field( $search )
        {
            $this->output(
                    '<div class="input-group">',
                    '<input type="text" ' . $search['field_tags'] . ' value="' . @$search['value'] . '" class="as-search-field form-control" placeholder="' . $search['button_label'] . '"/>' );
            $this->search_button( $search );
            $this->output( '</div>' );
        }
        
		public function form_password( $field, $style )
        {
            $this->output( '<input ' . @$field['tags'] . ' type="password" value="' . @$field['value'] . '" class="as-form-' . $style . '-text form-control"/>' );
        }
        
		public function form_number( $field, $style )
        {
            $this->output( '<input ' . @$field['tags'] . ' type="text" value="' . @$field['value'] . '" class="as-form-' . $style . '-number form-control"/>' );
        }
        
		public function form_text_single_row( $field, $style )
        {
            $this->output( '<input ' . @$field['tags'] . ' type="text" value="' . @$field['value'] . '" class="as-form-' . $style . '-text form-control"/>' );
        }
        
		public function form_text_multi_row( $field, $style )
        {
            $this->output( '<textarea ' . @$field['tags'] . ' rows="' . (int) $field['rows'] . '" cols="40" class="as-form-' . $style . '-text  form-control">' . @$field['value'] . '</textarea>' );
        }
        /**
         * Output a <select> element. The $field array may contain the following keys:
         *   options: (required) a key-value array containing all the options in the select.
         *   tags: any attributes to be added to the select.
         *   value: the selected value from the 'options' parameter.
         *   match_by: whether to match the 'value' (default) or 'key' of each option to determine if it is to be selected.
         */
        
		public function form_select( $field, $style )
        {
            $this->output( '<select ' . ( isset( $field['tags'] ) ? $field['tags'] : '' ) . ' class="as-form-' . $style . '-select form-control">' );
            // Only match by key if it is explicitly specified. Otherwise, for backwards compatibility, match by value
            $matchbykey = isset( $field['match_by'] ) && $field['match_by'] === 'key';
            foreach ( $field['options'] as $key => $value ) {
                $selected = isset( $field['value'] ) && (
                                ( $matchbykey && $key === $field['value'] ) ||
                                ( !$matchbykey && $value === $field['value'] )
                        );
                $this->output( '<option value="' . $key . '"' . ( $selected ? ' selected' : '' ) . '>' . $value . '</option>' );
            }
            $this->output( '</select>' );
        }
        
		public function form_select_radio( $field, $style )
        {
            $radios = 0;
            foreach ( $field['options'] as $tag => $value ) {
                if ( $radios++ )
                    $this->output( '<br/>' );
                $this->output( '<input ' . @$field['tags'] . ' type="radio" value="' . $tag . '"' . ( ( $value == @$field['value'] ) ? ' checked' : '' ) . ' class="as-form-' . $style . '-radio"/> ' . $value );
            }
        }
        /**
         * prints the search button
         *
         * @param  array $search
         *
         * @return null
         */
        function search_button( $search )
        {
            $this->output( '<span class="input-group-btn">' );
            $this->output( '<button type="submit" value="" class="btn as-search-button" ><span class="fa fa-search"></span></button>' );
            $this->output( '</span>' );
        }
        /**
         * prints the drop down for the member
         *
         */
        function smatika_member_drop_down()
        {
            if ( as_is_logged_in() ) {
                require_once THEME_DIR . '/templates/member-loggedin-drop-down.php';
            } else {
                require_once THEME_DIR . '/templates/member-login-drop-down.php';
            }
        }
        /**
         * grabs the sub-nav links for the navigation items
         *
         * @param  array $navigation navigation links
         *
         * @return null
         */
        function smatika_nav_bar_main_links( $navigation )
        {
            if ( count( $navigation ) ) {
                foreach ( $navigation as $key => $nav_item ) {
                    $this->smatika_nav_bar_item( $nav_item );
                }
            }
        }
        /**
         * Prints the drop down menu
         *
         * @param  array $nav_item      the navigation item
         * @param  array $sub_nav_items sub-nav items to be displayed
         *
         * @return null
         */
        function smatika_nav_bar_drop_down( $nav_item, $sub_nav_items )
        {
            $class = ( !!@$nav_item['selected'] ) ? 'active' : '';
            if ( !empty( $sub_nav_items ) && count( $sub_nav_items ) ) {
                $nav_item['class'] = "dropdown-split-left";
                $this->smatika_nav_bar_item( $nav_item );
                $this->output( '<li class="dropdown dropdown-split-right hidden-xs ' . $class . '">' );
                $this->output( '<a href="#" class="dropdown-toggle transparent" data-toggle="dropdown"><i class="fa fa-caret-down"></i></a>' );
                $this->output( '<ul class="dropdown-menu" role="menu">' );
                foreach ( $sub_nav_items as $key => $sub_nav_item ) {
                    $this->smatika_nav_bar_item( $sub_nav_item );
                }
                $this->output( '</ul>' );
                $this->output( '</li>' );
            } else {
                $this->smatika_nav_bar_item( $nav_item );
            }
        }
        /**
         * prints a single nav-bar item
         *
         * @param  array $nav_item navigation item
         *
         * @return null
         */
        function smatika_nav_bar_item( $nav_item )
        {
            $class = ( !!@$nav_item['class'] ) ? $nav_item['class'] . ' ' : '';
            $class .= ( !!@$nav_item['selected'] ) ? 'active' : '';
            if ( !empty( $class ) ) {
                $class = 'class="' . $class . '"';
            }
            $icon = ( !!@$nav_item['icon'] ) ? smatika_get_fa_icon( @$nav_item['icon'] ) : '';
            $this->output( '<li ' . $class . '><a href="' . $nav_item['url'] . '">' . $icon . $nav_item['label'] . '</a></li>' );
        }
		function page_links_item( $page_link )
        {
            $active_class = ( @$page_link['type'] === 'this' ) ? ' active' : '';
            $disabled_class = ( @$page_link['type'] === 'ellipsis' ) ? ' disabled' : '';
            $this->output( '<li class="as-page-links-item' . $active_class . $disabled_class . '">' );
            $this->page_link_content( $page_link );
            $this->output( '</li>' );
        }
		function a_selection( $post )
        {
            $this->output( '<div class="as-a-selection">' );
            if ( isset( $post['select_tags'] ) )
                $this->post_hover_button( $post, 'select_tags', '', 'as-a-select' );
            elseif ( isset( $post['unselect_tags'] ) )
                $this->post_hover_button( $post, 'unselect_tags', '', 'as-a-unselect' );
            elseif ( $post['selected'] )
                $this->output( '<div class="as-a-selected"> <span class="fa fa-check"></span> </div>' );
            if ( isset( $post['select_text'] ) )
                $this->output( '<div class="as-a-selected-text">' . @$post['select_text'] . '</div>' );
            $this->output( '</div>' );
        }
		function post_hover_button( $post, $element, $value, $class )
        {
            if ( isset( $post[$element] ) ) {
                $icon = smatika_get_voting_icon( $element );
                $this->output( '<button ' . $post[$element] . ' type="submit" value="' . $value . '" class="' . $class . '-button"/> ' . $icon . '</button>' );
            }
        }
        
		public function q_list_item( $q_item )
        {
            $this->output( '<div class="as-q-list-item row' . rtrim( ' ' . @$q_item['classes'] ) . '" ' . @$q_item['tags'] . '>' );
            $this->q_item_stats( $q_item );
            //$this->q_item_avatar( $q_item );
            $this->q_item_main( $q_item );
            $this->q_item_clear();
            $this->output( '</div> <!-- END as-q-list-item -->', '' );
        }
		function q_item_avatar( $q_item )
        {
            $avatar = smatika_get_post_avatar( $q_item, 60 );
            $this->output( '<div class="as-q-item-avatar-warp">' );
            $this->output( $avatar );
            $this->output( '</div>' );
        }
        /**
         * add view count to question list
         *
         * @param  array $q_item
         *
         * @return null
         */
        function q_item_stats( $q_item )
        {
            $this->output( '<div class="as-q-item-stats">' );
            $this->voting( $q_item );
            //$this->a_count( $q_item );
            $this->output( '</div>' );
        }
		function post_meta( $post, $class, $prefix = null, $separator = '<br/>' )
        {
            $this->output( '<span class="' . $class . '-meta">' );
            if ( isset( $prefix ) )
                $this->output( $prefix );
            $order = explode( '^', @$post['meta_order'] );
            foreach ( $order as $element )
                switch ( $element ) {
                    case 'what':
                        $this->post_meta_what( $post, $class );
                        break;
                    case 'when':
                        $this->post_meta_when( $post, $class );
                        break;
                    case 'where':
                        $this->post_meta_where( $post, $class );
                        break;
                    case 'who':
                        $this->post_meta_who( $post, $class );
                        break;
                }
            $this->post_meta_flags( $post, $class );
            if ( !empty( $post['what_2'] ) ) {
                $this->output( $separator );
                foreach ( $order as $element )
                    switch ( $element ) {
                        case 'what':
                            $this->output( '<span class="' . $class . '-what">' . $post['what_2'] . '</span>' );
                            break;
                        case 'when':
                            $this->output_split( @$post['when_2'], $class . '-when' );
                            break;
                        case 'who':
                            $this->output_split( @$post['who_2'], $class . '-who' );
                            break;
                    }
            }
            $this->smatika_view_count( $post );
            $this->output( '</span>' );
        }
        /**
         * prints the view count
         *
         * @param  array
         *
         * @return null
         */
        function smatika_view_count( $post )
        {
            if ( !empty( $post['views'] ) ) {
                $this->output( '<span class="as-q-item-view-count">' );
                $this->output( ' | <i class="fa fa-eye"></i>' );
                $this->output_split( @$post['views'], 'q-item-view' );
                $this->output( '</span>' );
            }
        }
		function view_count( $q_item ) // prevent display of view count in the usual place
        {
            if ( isset($q_item['content']) )
                parent::view_count( $q_item );
        }
		function post_disabled_button( $post, $element, $value, $class )
        {
            if ( isset( $post[$element] ) ) {
                $icon = smatika_get_voting_icon( $element );
                $this->output( '<button ' . $post[$element] . ' type="submit" value="' . $value . '" class="' . $class . '-disabled" disabled="disabled"/> ' . $icon . '</button>' );
            }
        }
		function form_button_data( $button, $key, $style )
        {
            $baseclass = 'as-form-' . $style . '-button as-form-' . $style . '-button-' . $key;
            $this->output( '<button' . rtrim( ' ' . @$button['tags'] ) . ' title="' . @$button['popup'] . '" type="submit"' .
                    ( isset( $style ) ? ( ' class="' . $baseclass . '"' ) : '' ) . '>' . @$button['label'] . '</button>' );
        }
        /**
         * prints the favorite button
         *
         * @param  array $tags parameters
         * @param  [type] $class class
         *
         * @return null
         */
        function favorite_button( $tags, $class )
        {
            if ( isset( $tags ) ) {
                $icon = smatika_get_fa_icon( 'heart' );
                $this->output( '<button ' . $tags . ' type="submit" value="" class="' . $class . '-button"/> ' . $icon . '</button>' );
            }
        }
        /**
         * Attribution link for the theme which adds the authors name
         *
         * @return null
         */
        function attribution()
        {
            if ( as_opt( 'smatika_show_social_links_at_footer' ) ) {
                $this->footer_social_links();
            }
            $this->output( '<div class="footer-bottom">' );
            $this->output(
				'<div class="as-attribution">',
				'Developed by <a href="http://github.com/Jacksiro">Jack Siro</a>',
				'</div>'
            );
            //parent::attribution();
            $this->output(
                    '<div class="smatika-copyright">',
                   '&copy; Mshindi ' . date('Y'),//as_opt( 'smatika_copyright_text' ),
                    '</div>'
            );
            $this->output( '</div>' );
        }
        /**
         * beautifies the default waiting template with a font aswome icon
         *
         * @return null
         */
        function waiting_template()
        {
            $this->output( '<span id="as-waiting-template" class="as-waiting fa fa-spinner fa-spin"></span>' );
        }
        /**
         * beautifies the default notice
         *
         * @param  array $notice notice parameters
         *
         * @return null
         */
        function notice( $notice )
        {
            $this->output( '<div class="as-notice alert alert-info text-center alert-dismissible" role="alert" id="' . $notice['id'] . '">' );
            if ( isset( $notice['form_tags'] ) )
                $this->output( '<form ' . $notice['form_tags'] . '>' );
            $this->output( '<button ' . $notice['close_tags'] . ' type="submit" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' );
            $this->output_raw( $notice['content'] );
            if ( isset( $notice['form_tags'] ) ) {
                $this->form_hidden_elements( @$notice['form_hidden'] );
                $this->output( '</form>' );
            }
            $this->output( '</div>' );
        }
        
		public function error( $error )
        {
            if ( strlen( $error ) ) {
                $this->output(
                        '<div class="smatika-error alert alert-dismissible" role="alert">',
                        $error,
                        '<button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>',
                        '</div>'
                );
            }
        }
		function smatika_favicon()
        {
            $this->output_raw( '<link rel="shortcut icon" href="favicon.ico">' );
        }
		function ranking( $ranking )
        {
            $class = ( @$ranking['type'] == 'members' ) ? 'as-top-members' : 'as-top-tags';
            $item_count = min( $ranking['rows'], count( $ranking['items'] ) );
            if ( @$ranking['type'] == 'members' ) {
                if ( count( $ranking['items'] ) ) {
                    $this->output( '<div class="page-members-list clearfix"><div class="row">' );
                    $columns = as_opt( 'columns_members' );
                    $pagesize = as_opt( 'page_size_members' );
                    $start = as_get_start();
                    $members = as_db_select_with_pending( as_db_top_members_selectspec( $start, as_opt_if_loaded( 'page_size_members' ) ) );
                    $members = array_slice( $members, 0, $pagesize );
                    $membershtml = as_memberids_handles_html( $members );
                    foreach ( $ranking['items'] as $member ) {
                        $this->output( '<div class="member-box col-sm-' . ceil( 12 / $columns ) . ' col-xs-12">' );
                        $member_raw = !empty( $member['raw'] ) ? $member['raw'] : $member;
                        $handle = @$member_raw['handle'];
                        $handle_html = @$membershtml[$member_raw['memberid']];
                        if ( defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ) {
                            $level_html = $member['score'];
                            unset( $member['score'] );
                        } else {
                            if ( is_numeric( $member['score'] ) ) {
                                $member_level = smatika_get_member_level( $member_raw['memberid'] );
                                $level_html = as_member_level_string( $member_level );
                            } else {
                                $level_html = $member['score'];
                                unset( $member['score'] );
                            }
                        }
                        if ( empty( $handle_html ) ) {
                            $handle_html = $member['label'];
                        }
                        $avatar = ( AS_FINAL_EXTERNAL_MEMBERS
                                ? as_get_external_avatar_html( @$member_raw['memberid'], as_opt( 'avatar_members_size' ), true )
                                : as_get_member_avatar_html( @$member_raw['flags'], @$member_raw['email'], @$member_raw['handle'],
                                        @$member_raw['avatarblobid'], @$member_raw['avatarwidth'], @$member_raw['avatarheight'], 70, true )
                        );
                        if ( isset( $member['score'] ) ) {
                            $memberpoints = $member['score'];
                            $pointshtml = ( $memberpoints === 1 ) ? as_lang_html_sub( 'main/1_point', '1', '1' )
                                    : as_lang_html_sub( 'main/x_points', as_html( $memberpoints ) );
                            if ( !empty( $pointshtml ) ) {
                                $pointshtml = '<p class="score">' . $pointshtml . '</p>';
                            }
                        }
                        $this->output( '
								<div class="member-box-inner">
									<div class="member-avatar">
										' . $avatar . '
									</div>
									<div class="member-data">
										' . $handle_html . '
										<div class="member-level">
											' . $level_html . '
										</div>
										<div class="counts clearfix">
											' . @$pointshtml . '
										</div>
								</div>' );
                        if ( as_opt( 'badge_active' ) && function_exists( 'as_get_badge_list' ) )
                            $this->output( '<div class="badge-list">' . smatika_member_badge( $handle ) . '</div>' );
                        $this->output( '</div>' );
                        $this->output( '</div>' );
                    }
                    $this->output( '</div>' );
                    $this->output( '</div>' );
                } else {
                    $title = isset( $this->content['ranking_members']['title'] ) ? $this->content['ranking_members']['title'] : @$this->content['title'];
                    $this->output( '
								<div class="no-items">
									<div class="alert alert-info"><span class="fa fa-warning"></span> ' . $title . '</div>
								</div>' );
                }
            } elseif ( @$ranking['type'] == 'tags' ) {
                if ( count( $ranking['items'] ) ) {
                    $this->output( '<div id="tags-list" class="row ' . $class . '">' );
                    $columns = as_opt( 'columns_tags' );
                    for ( $column = 0 ; $column < $columns ; $column++ ) {
                        $this->set_context( 'ranking_column', $column );
                        $this->output( '<div class="col-md-' . ceil( 12 / $columns ) . ' col-xs-12" >' );
                        $this->output( '<ul class="smatika-tags-list">' );
                        for ( $row = 0 ; $row < $item_count ; $row++ ) {
                            $this->set_context( 'ranking_row', $row );
                            $this->smatika_tags_item( @$ranking['items'][$column * $item_count + $row], $class, $column > 0 );
                        }
                        $this->clear_context( 'ranking_column' );
                        $this->output( '</ul>' );
                        $this->output( '</div>' );
                    }
                    $this->clear_context( 'ranking_row' );
                    $this->output( '</div>' );
                } else {
                    $this->output( '
						<div class="no-items">
						<div class="alert alert-info"><span class="fa fa-warning"></span> ' . $this->content['ranking_tags']['title'] . '</div>
						</div>' );
                }
            } else {
                parent::ranking( $ranking );
            }
        }
		function smatika_tags_item( $item, $class, $spacer )
        {
            $content = as_db_read_one_value( as_db_query_sub( "SELECT ^tagmetas.content FROM ^tagmetas WHERE ^tagmetas.tag =$ ", strip_tags( $item['label'] ) ), true );
            if ( isset( $item ) )
                $this->output(
                        '<li class="tag-item">',
                        '<div class="tag-head clearfix">',
                        '<span> ' . $item['count'] . ' &#215;</span>',
                        '<div class="as-tags-rank-tag-item">',
                        $item['label'],
                        '</div>',
                        '</div>'
                );
            if ( !empty( $content ) ) {
                $this->output( '<p class="desc">',
                        $this->truncate( $content, 150 ),
                        '</p>' );
            }
            $this->output( '</li>' );
        }
		function truncate( $string, $limit, $pad = "..." )
        {
            if ( strlen( $string ) <= $limit )
                return $string;
            else {
                $text = $string . ' ';
                $text = substr( $text, 0, $limit );
                $text = substr( $text, 0, strrpos( $text, ' ' ) );
                return $text . $pad;
            }
        }
        private function smatika_breadcrumb()
        {
            if ( class_exists( 'Ami_Breadcrumb' ) && as_opt( 'smatika_show_breadcrumbs' ) ) {
                if ( !$this->is_home() && $this->template !== 'admin' ) {
                    $args = array(
                            'themeobject' => $this,
                            'content'     => $this->content,
                            'template'    => $this->template,
                            'request'     => as_request(),
                    );
                    $breadcrumb = new Ami_Breadcrumb( $args );
                    $this->output( '<div class="smatika-breadcrumb">' );
                    $breadcrumb->generate();
                    $this->output( '</div>' );
                }
            }
        }
        
		public function message_content( $message )
        {
            if ( !empty( $message['content'] ) ) {
                $this->output( '<div class="as-message-content-wrapper">' );
                $this->output( '<div class="as-message-content">' );
                $this->output_raw( $message['content'] );
                $this->output( '</div>' );
                $this->output( '</div>' );
            }
        }
        
		public function vote_clear()
        {
            $this->output(
                    '<div class="as-vote-clear clearfix">',
                    '</div>'
            );
        }
        
		public function page_links_clear()
        {
            $this->output(
                    '<div class="as-page-links-clear clearfix">',
                    '</div>'
            );
        }
        
		public function q_view_clear()
        {
            $this->output(
                    '<div class="as-q-view-clear clearfix">',
                    '</div>'
            );
        }
        
		public function a_item_clear()
        {
            $this->output(
                    '<div class="as-a-item-clear clearfix">',
                    '</div>'
            );
        }
        
		public function c_item_clear()
        {
            $this->output(
                    '<div class="as-c-item-clear clearfix">',
                    '</div>'
            );
        }
        
		public function nav_clear( $navtype )
        {
            $this->output(
                    '<div class="as-nav-' . $navtype . '-clear clearfix">',
                    '</div>'
            );
        }
        
		public function header_clear()
        {
            $this->output(
                    '<div class="as-header-clear clearfix">',
                    '</div>'
            );
        }
        
		public function footer_clear()
        {
            $this->output(
                    '<div class="as-footer-clear clearfix">',
                    '</div>'
            );
        }
        
		public function q_item_clear()
        {
            $this->output(
                    '<div class="as-q-item-clear clearfix">',
                    '</div>'
            );
        }
        private function smatika_hide_sidebar_for_template()
        {
            return array(
                    'members',
					//'tags', 'categories',
                    'admin', 'member', 'account',
                    'favorites', 'member-wall', 'messages',
                    'member-activity', 'member-questions', 'member-answers' );
        }
        private function smatika_do_hide_sidebar()
        {
            return in_array( $this->template, $this->smatika_hide_sidebar_for_template() );
        }
		function smatika_page_has_feed()
        {
            return !empty( $this->content['feed'] );
        }
		function smatika_page_has_favorite()
        {
            return isset( $this->content['favorite'] );
        }
        
		public function is_home()
        {
            return empty( $this->request );
        }
        
		public function smatika_site_header()
        {
            if ( $this->is_home() && as_opt( 'smatika_show_home_page_banner' ) ) {
                //check if member closed the header intentionally
                $member_hidden = as_opt( 'smatika_banner_closable' ) ?
                        @$_COOKIE['smatika_hide_site_header'] : 'no';
                if ( $member_hidden !== 'yes' )
                    smatika_include_template( 'site-header.php' );
            }
        }
        
		public function suggest_next()
        {
            $suggest = @$this->content['suggest_next'];
            if ( !empty( $suggest ) ) {
                $this->output( '<div class="as-suggest-next col-xs-12 text-center clearfix alert">' );
                $this->output( $suggest );
                $this->output( '</div>' );
            }
        }
        private function smatika_site_stats_bottom()
        {
            smatika_include_template( 'site-stats-bottom.php' );
        }
        
        private function footer_social_links()
        {
            $this->output( '<div class="footer-social">' );
            $social_links = smatika_generate_social_links();
            $this->output( '<ul>' );
            foreach ( $social_links as $key => $value ) {
                $this->output( '<li>' );
                $this->output( smatika_get_social_link( $value, true ) );
                $this->output( '</li>' );
            }
            $this->output( '</ul>' );
            $this->output( '</div>' );
        }
        
		public function post_tags( $post, $class )
        {
            if ( !empty( $post['q_tags'] ) ) {
                $this->output( '<div class="' . $class . '-tags clearfix">' );
                $this->post_tag_list( $post, $class );
                $this->output( '</div>' );
            }
        }
        
		public function q_view_buttons( $q_view )
        {
            if ( as_opt( 'smatika_show_collapsible_btns' ) ) {
                if ( !empty( $q_view['form'] ) ) {
                    $q_view_temp = $q_view;
                    $allowed_main_btns = array( 'answer', 'comment' );
                    $q_view_temp['form']['buttons'] = $this->smatika_divide_array( $q_view['form']['buttons'], $allowed_main_btns );
                    $this->output( '<div class="as-q-view-buttons clearfix">' );
                    $this->output( '<div class="default-buttons pull-left">' );
                    $this->form( $q_view['form'] );
                    $this->output( '</div>' );
                    $this->smatika_generate_action_button( $q_view_temp );
                    $this->output( '</div>' );
                }
            } else {
                parent::q_view_buttons( $q_view );
            }
        }
        
		public function a_item_buttons( $a_item )
        {
            if ( as_opt( 'smatika_show_collapsible_btns' ) ) {
                if ( !empty( $a_item['form'] ) ) {
                    $a_item_temp = $a_item;
                    $allowed_main_btns = array( 'comment' );
                    $a_item_temp['form']['buttons'] = $this->smatika_divide_array( $a_item['form']['buttons'], $allowed_main_btns );
                    $this->output( '<div class="as-a-item-buttons clearfix">' );
                    $this->output( '<div class="default-buttons pull-left">' );
                    $this->form( $a_item['form'] );
                    $this->output( '</div>' );
                    $this->smatika_generate_action_button( $a_item_temp );
                    $this->output( '</div>' );
                }
            } else parent::a_item_buttons( $a_item );
        }
        
		public function c_item_buttons( $c_item )
        {
            if ( as_opt( 'smatika_show_collapsible_btns' ) ) {
                if ( !empty( $c_item['form'] ) ) {
                    $this->output( '<div class="as-c-item-buttons collapsed">' );
                    $this->smatika_generate_action_button( $c_item );
                    $this->output( '</div>' );
                }
            } else parent::c_item_buttons( $c_item );
        }
        private function smatika_generate_action_button( $action_view, $btn_style = 'vertical' )
        {
            $this->output( '<div class="action-buttons pull-right">' );
            $this->output( '<div class="btn-group">' );
            $this->output( '<button type="button" class="as-form-light-button dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More actions">' );
            $this->output( '<span class="glyphicon glyphicon-option-' . $btn_style . '"></span>' );
            $this->output( '</button>' );
            $this->smatika_generate_action_dropdown( $action_view['form'] );
            $this->output( '</div>', '</div>' );
        }
		function smatika_generate_action_dropdown( $form )
        {
            if ( !empty( $form['buttons'] ) ) {
                $this->output( '<ul class="dropdown-menu action-buttons-dropdown">' );
                foreach ( $form['buttons'] as $key => $btn ) {
                    $this->output( '<li>' );
                    $this->output( $this->form_button_data( $btn, $key, @$form['style'] ) );
                    $this->output( '</li>' );
                }
                $this->output( '</ul>' );
            }
        }
		function smatika_divide_array( &$original, $allowed_keys )
        {
            if ( count( $original ) && count( $allowed_keys ) ) {
                $copy = $original;
                foreach ( $original as $key => $btn ) {
                    if ( !in_array( $key, $allowed_keys ) ) unset( $original[$key] );
                }
                foreach ( $copy as $key => $btn ) {
                    if ( in_array( $key, $allowed_keys ) ) unset( $copy[$key] );
                }
                return $copy;
            }
            return $original;
        }
    }
/*
	Omit PHP closing tag to help avoid accidental output
*/