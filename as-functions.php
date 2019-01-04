<?php
    if ( !defined( 'AS_VERSION' ) ) { // don't allow this page to be requested directly from browser
        header( 'Location: ../../' );
        exit;
    }
/**
     * Reset the options in $names to their defaults
     *
     * @param $names
     */
    function smatika_reset_options( $names )
    {
        foreach ( $names as $name )
            as_set_option( $name, smatika_default_option( $name ) );
    }

    /**
     * Return the default value for option $name
     *
     * @param $name
     *
     * @return bool|mixed|string
     */
    function smatika_default_option( $name )
    {
        $fixed_defaults = array(
            'smatika_activate_prod_mode'           => 0,
            'smatika_use_local_font'               => 1,
            'smatika_enable_top_bar'               => 1,
            'smatika_show_top_social_icons'        => 1,
            'smatika_enable_sticky_header'         => 1,
            'smatika_enable_back_to_top_btn'       => 1,
            'smatika_show_home_page_banner'        => 1,
            'smatika_banner_closable'              => 1,
            'smatika_banner_show_ask_box'          => 1,
            'smatika_show_collapsible_btns'        => 1,
            'smatika_show_breadcrumbs'             => 1,
            'smatika_show_site_stats_above_footer' => 1,
            'smatika_show_social_links_at_footer'  => 1,
            'smatika_show_copyright_at_footer'     => 1,
            'smatika_show_custom_404_page'         => 1,
            'smatika_copyright_text'               => as_lang( 'smatika/smatika_theme' ),
            'smatika_banner_head_text'             => as_lang( 'smatika/smatika_discussion_forum' ),
            'smatika_banner_div1_text'             => as_lang( 'smatika/search_answers' ),
            'smatika_banner_div1_icon'             => 'fa fa-search-plus',
            'smatika_banner_div2_text'             => as_lang( 'smatika/one_destination' ),
            'smatika_banner_div2_icon'             => 'fa fa-question-circle',
            'smatika_banner_div3_text'             => as_lang( 'smatika/get_expert_answers' ),
            'smatika_banner_div3_icon'             => 'fa fa-check-square-o',
            'smatika_top_bar_left_text'            => as_lang( 'smatika/responsive_q2a_theme' ),
            'smatika_top_bar_right_text'           => as_lang( 'smatika/ask_us_anything' ),
            'smatika_custom_404_text'              => as_lang( 'smatika/page_not_found_default_text' ),
        );

        if ( isset( $fixed_defaults[$name] ) ) {
            $value = $fixed_defaults[$name];
        } else {
            switch ( $name ) {

                default: // call option_default method in any registered modules
                    $modules = as_load_all_modules_with( 'option_default' );  // Loads all modules with the 'option_default' method

                    foreach ( $modules as $module ) {
                        $value = $module->option_default( $name );
                        if ( strlen( $value ) )
                            return $value;
                    }

                    $value = '';
                    break;
            }
        }

        return $value;
    }

    /**
     * Returns an array of all options used in Blog Tool
     *
     * @return array
     */
    function smatika_get_all_options()
    {
        return array(
            'smatika_activate_prod_mode',
            'smatika_use_local_font',
            'smatika_enable_top_bar',
            'smatika_show_top_social_icons',
            'smatika_enable_sticky_header',
            'smatika_enable_back_to_top_btn',
            'smatika_show_home_page_banner',
            'smatika_banner_closable',
            'smatika_banner_show_ask_box',
            'smatika_show_collapsible_btns',
            'smatika_show_breadcrumbs',
            'smatika_show_site_stats_above_footer',
            'smatika_show_social_links_at_footer',
            'smatika_show_copyright_at_footer',
            'smatika_copyright_text',
            'smatika_banner_head_text',
            'smatika_banner_div1_text',
            'smatika_banner_div1_icon',
            'smatika_banner_div2_text',
            'smatika_banner_div2_icon',
            'smatika_banner_div3_text',
            'smatika_banner_div3_icon',
            'smatika_top_bar_left_text',
            'smatika_top_bar_right_text',
        );
    }

    /**
     * reset all blog options
     *
     * @return bool
     */
    function smatika_reset_all_options()
    {
        smatika_reset_options( smatika_get_all_options() );

        return true;
    }
	
    function smatika_get_glyph_icon( $icon )
    {
        if ( !empty( $icon ) ) {
            return '<span class="glyphicon glyphicon-' . $icon . '"></span> ';
        } else {
            return '';
        }
    }

    function smatika_get_fa_icon( $icon )
    {
        if ( !empty( $icon ) ) {
            return '<span class="fa fa-' . $icon . '"></span> ';
        } else {
            return '';
        }
    }

    function smatika_get_voting_icon( $tags )
    {
        $icon = '';
        switch ( $tags ) {
            case 'vote_up_tags':
                $icon = 'chevron-up';
                break;
            case 'vote_down_tags':
                $icon = 'chevron-down';
                break;
            case 'unselect_tags':
            case 'select_tags':
                $icon = 'check';
                break;
            default:
                break;
        }

        return smatika_get_fa_icon( $icon );
    }

    if ( !function_exists( 'starts_with' ) ) {
        function starts_with( $haystack, $needle )
        {
            return $needle === "" || strpos( $haystack, $needle ) === 0;
        }
    }

    if ( !function_exists( 'ends_with' ) ) {
        function ends_with( $haystack, $needle )
        {
            return $needle === "" || substr( $haystack, -strlen( $needle ) ) === $needle;
        }
    }

    function smatika_remove_brackets( &$nav_cat )
    {
        if ( is_array( $nav_cat ) && count( $nav_cat ) ) {
            foreach ( $nav_cat as $key => &$nav_cat_item ) {
                if ( !empty( $nav_cat_item['note'] ) ) {
                    $nav_cat_item['note'] = str_replace( array( '(', ')' ), '', $nav_cat_item['note'] );
                }
                if ( !empty( $nav_cat_item['subnav'] ) ) {
                    smatika_remove_brackets( $nav_cat_item['subnav'] );
                }
            }
        }
    }

    function smatika_get_member_data( $handle )
    {
        $memberid = as_handle_to_memberid( $handle );
        $identifier = AS_FINAL_EXTERNAL_MEMBERS ? $memberid : $handle;
        $member = array();
        if ( defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ) {
            $u_rank = as_db_select_with_pending( as_db_member_rank_selectspec( $memberid, true ) );
            $u_points = as_db_select_with_pending( as_db_member_points_selectspec( $memberid, true ) );

            $memberinfo = array();
            $member_info = get_memberdata( $memberid );
            $memberinfo['memberid'] = $memberid;
            $memberinfo['handle'] = $handle;
            $memberinfo['email'] = $member_info->member_email;

            $member[0] = $memberinfo;
            $member[1]['rank'] = $u_rank;
            $member[2] = $u_points;
            $member = ( $member[0] + $member[1] + $member[2] );
        } else {
            $member['account'] = as_db_select_with_pending( as_db_member_account_selectspec( $memberid, true ) );
            $member['rank'] = as_db_select_with_pending( as_db_member_rank_selectspec( $handle ) );
            $member['points'] = as_db_select_with_pending( as_db_member_points_selectspec( $identifier ) );

            $member['followers'] = as_db_read_one_value( as_db_query_sub( 'SELECT count(*) FROM ^memberfavorites WHERE ^memberfavorites.entityid = # and ^memberfavorites.entitytype = "U" ', $memberid ), true );

            $member['following'] = as_db_read_one_value( as_db_query_sub( 'SELECT count(*) FROM ^memberfavorites WHERE ^memberfavorites.memberid = # and ^memberfavorites.entitytype = "U" ', $memberid ), true );
        }

        return $member;
    }

    function smatika_member_profile( $handle, $field = null )
    {
        $memberid = as_handle_to_memberid( $handle );
        if ( defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ) {
            return get_member_meta( $memberid );
        } else {
            $query = as_db_select_with_pending( as_db_member_profile_selectspec( $memberid, true ) );

            if ( !$field ) return $query;
            if ( isset( $query[$field] ) )
                return $query[$field];
        }

        return false;
    }

    function smatika_member_badge( $handle )
    {
        if ( as_opt( 'badge_active' ) ) {
            $memberids = as_handles_to_memberids( array( $handle ) );
            $memberid = $memberids[$handle];


            // displays small badge widget, suitable for meta

            $result = as_db_read_all_values(
                as_db_query_sub(
                    'SELECT badge_slug FROM ^memberbadges WHERE member_id=#',
                    $memberid
                )
            );

            if ( count( $result ) == 0 ) return;

            $badges = as_get_badge_list();
            foreach ( $result as $slug ) {
                $bcount[$badges[$slug]['type']] = isset( $bcount[$badges[$slug]['type']] ) ? $bcount[$badges[$slug]['type']] + 1 : 1;
            }
            $output = '<ul class="member-badge clearfix">';
            for ( $x = 2 ; $x >= 0 ; $x-- ) {
                if ( !isset( $bcount[$x] ) ) continue;
                $count = $bcount[$x];
                if ( $count == 0 ) continue;

                $type = as_get_badge_type( $x );
                $types = $type['slug'];
                $typed = $type['name'];

                $output .= '<li class="badge-medal ' . $types . '"><i class="icon-badge" title="' . $count . ' ' . $typed . '"></i><span class="badge-pointer badge-' . $types . '-count" title="' . $count . ' ' . $typed . '"> ' . $count . '</span></li>';
            }
            $output = substr( $output, 0, -1 );  // lazy remove space
            $output .= '</ul>';

            return ( $output );
        }
    }

    function smatika_get_member_level( $memberid )
    {
        global $smatika_memberid_and_levels;
        if ( empty( $smatika_memberid_and_levels ) ) {
            $smatika_memberid_and_levels = as_db_read_all_assoc( as_db_query_sub( "SELECT memberid , level from ^members" ), 'memberid' );
        }

        if ( isset( $smatika_memberid_and_levels[$memberid] ) ) {
            return $smatika_memberid_and_levels[$memberid]['level'];
        } else {
            return 0;
        }
    }

    function smatika_get_member_avatar( $memberid, $size = 40 )
    {
        if ( !defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ) {
            $memberaccount = as_db_select_with_pending( as_db_member_account_selectspec( $memberid, true ) );

            $member_avatar = as_get_member_avatar_html( $memberaccount['flags'], $memberaccount['email'], null,
                $memberaccount['avatarblobid'], $memberaccount['avatarwidth'], $memberaccount['avatarheight'], $size );
        } else {
            $member_avatar = as_get_external_avatar_html( $memberid, as_opt( 'avatar_members_size' ), true );
        }

        if ( empty( $member_avatar ) ) {
            // if the default avatar is not set by the admin , then take the default
            $member_avatar = smatika_get_default_avatar( $size );
        }

        return $member_avatar;
    }

    function smatika_get_post_avatar( $post, $size = 40, $html = false )
    {
        if ( !isset( $post['raw'] ) ) {
            $post['raw']['memberid'] = $post['memberid'];
            $post['raw']['flags'] = $post['flags'];
            $post['raw']['email'] = $post['email'];
            $post['raw']['handle'] = $post['handle'];
            $post['raw']['avatarblobid'] = $post['avatarblobid'];
            $post['raw']['avatarwidth'] = $post['avatarwidth'];
            $post['raw']['avatarheight'] = $post['avatarheight'];
        }

        if ( defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ) {
            $avatar = get_avatar( as_get_member_email( $post['raw']['memberid'] ), $size );
        }
        if ( AS_FINAL_EXTERNAL_MEMBERS )
            $avatar = as_get_external_avatar_html( $post['raw']['memberid'], $size, false );
        else
            $avatar = as_get_member_avatar_html( $post['raw']['flags'], $post['raw']['email'], $post['raw']['handle'],
                $post['raw']['avatarblobid'], $post['raw']['avatarwidth'], $post['raw']['avatarheight'], $size );

        if ( empty( $avatar ) ) {
            // if the default avatar is not set by the admin , then take the default
            $avatar = smatika_get_default_avatar( $size );
        }

        if ( $html )
            return '<div class="avatar" data-id="' . $post['raw']['memberid'] . '" data-handle="' . $post['raw']['handle'] . '">' . $avatar . '</div>';

        return $avatar;
    }

    function smatika_get_default_avatar( $size = 40 )
    {
        return '<img src="' . THEME_URL . '/images/default-profile-pic.png" width="' . $size . '" height="' . $size . '" class="as-avatar-image" alt="">';
    }

    function smatika_include_template( $template_file, $echo = true )
    {
        ob_start();
        require( THEME_TEMPLATES . $template_file );
        $op = ob_get_clean();
        if ( $echo ) echo $op;

        return $op;
    }

    function smatika_get_link( $params )
    {
        if ( !empty( $params['icon'] ) ) {
            $icon = '<span class="fa fa-' . $params['icon'] . '"></span> ';
        }

        if ( @$params['tooltips'] ) {
            $tooltips_data = 'data-toggle="tooltip" data-placement="' . @$params['hover-position'] . '" title="' . $params['hover-text'] . '"';
        }

        return sprintf( '<a href="%s" %s>%s %s</a>', @$params['link'], @$tooltips_data, @$icon, @$params['text'] );
    }

    function smatika_get_social_link( $params, $icon_only = false )
    {
        if ( $icon_only ) $params['text'] = '';

        $params['tooltips'] = true;
        $params['hover-position'] = 'bottom';

        return smatika_get_link( $params );
    }

    function smatika_stats_output( $value, $langsingular, $langplural )
    {
        echo '<div class="count-item">';

        if ( $value == 1 )
            echo as_lang_html_sub( $langsingular, '<span class="count-data">1</span>', '1' );
        else
            echo as_lang_html_sub( $langplural, '<span class="count-data">' . number_format( (int) $value ) . '</span>' );

        echo '</div>';
    }

    function smatika_generate_social_links()
    {

        $social_links = array(
            'facebook'    => array(
                'icon'       => 'facebook',
                'text'       => as_lang( 'smatika/facebook' ),
                'hover-text' => as_lang( 'smatika/follow_us_on_x', as_lang( 'smatika/facebook' ) ),
            ),
            'twitter'     => array(
                'icon'       => 'twitter',
                'text'       => as_lang( 'smatika/twitter' ),
                'hover-text' => as_lang( 'smatika/follow_us_on_x', as_lang( 'smatika/twitter' ) ),
            ),
            'email'       => array(
                'icon'       => 'envelope',
                'text'       => as_lang( 'smatika/email' ),
                'hover-text' => as_lang( 'smatika/send_us_an_email' ),
            ),
            'pinterest'   => array(
                'icon'       => 'pinterest',
                'text'       => as_lang( 'smatika/pinterest' ),
                'hover-text' => as_lang( 'smatika/follow_us_on_x', as_lang( 'smatika/pinterest' ) ),
            ),
            'google-plus' => array(
                'icon'       => 'google-plus',
                'text'       => as_lang( 'smatika/google-plus' ),
                'hover-text' => as_lang( 'smatika/follow_us_on_x', as_lang( 'smatika/google-plus' ) ),
            ),
            'vk'          => array(
                'icon'       => 'vk',
                'text'       => as_lang( 'smatika/vk' ),
                'hover-text' => as_lang( 'smatika/follow_us_on_x', as_lang( 'smatika/vk' ) ),
            ),
        );

        foreach ( $social_links as $key => $s ) {

            if ( $key == 'email' ) {

                $address = as_opt( 'smatika_email_address' );
                
                if ( empty( $address ) ) {
                    unset( $social_links[$key] );
                    continue;
                }

                $social_links[$key]['link'] = 'mailto:' . $address ;
                continue;
            }

            $url = as_opt( 'smatika_' . $key . '_url' );

            if ( empty( $url ) ) {
                unset( $social_links[$key] );
                continue;
            }

            $social_links[$key]['link'] = $url;
        }

        return $social_links;
    }