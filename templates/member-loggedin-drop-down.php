<?php
    $memberid = as_get_logged_in_memberid();
	$handle = as_get_logged_in_handle();
	
    if ( !defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ) {
        $memberaccount = as_db_select_with_pending( as_db_member_account_selectspec( $memberid, true ) );
    }

    $logged_in_member_avatar = smatika_get_member_avatar( $memberid, 30 );

    if ( isset( $this->content['navigation']['member']['updates'] ) ) {
        $this->content['navigation']['member']['updates']['icon'] = 'bell-o';
    }

?>
<ul class="nav navbar-nav navbar-right member-nav">
    <?php if (as_opt('q2apro_onsitenotifications_enabled') && !empty($this->content['loggedin']['suffix'])): ?>
    <li class="notf-bubble visible-lg">
        <?php echo $this->content['loggedin']['suffix'] ?>
    </li>
    <?php endif ?>
    <li class="dropdown member-dropdown">
        <a href="#" class="navbar-member-img dropdown-toggle" data-toggle="dropdown">
            <?php echo $logged_in_member_avatar; ?>
        </a>
        <ul class="dropdown-menu" role="menu" id="member-dropdown-menu">
            <li>
                <a href="<?php echo as_path_html( $handle ); ?>">
                    <span class="fa fa-member"></span>
                    <?php echo $this->content['loggedin']['prefix'].' '.as_get_member_name($handle); ?>
                </a>
            </li>
            <?php if ( !defined( 'AS_WORDPRESS_INTEGRATE_PATH' ) ): ?>
                <?php if ( as_opt( 'allow_private_messages' ) && !( $memberaccount['flags'] & AS_MEMBER_FLAGS_NO_MESSAGES ) ): ?>
                    <li>
                        <a href="<?php echo as_path_html( 'messages' ) ?>">
                            <span class="fa fa-envelope"></span>
                            <?php echo as_lang_html( 'misc/nav_member_pms' ) ?>
                        </a>
                    </li>
                <?php endif ?>
                <li>
                    <a href="<?php echo as_path_html( $handle ); ?>">
                        <span class="fa fa-money"></span>
                        <?php echo as_get_logged_in_points() . ' ' . as_lang_html( 'admin/points_title' ) ?>
                    </a>
                </li>
                <?php foreach ( $this->content['navigation']['member'] as $key => $member_nav ): ?>
                    <?php if ( $key !== 'logout' ): ?>
                        <li>
                            <a href="<?php echo @$member_nav['url']; ?>">
                                <?php if ( !empty( $member_nav['icon'] ) ): ?>
                                    <span class="fa fa-<?php echo $member_nav['icon']; ?>"></span>
                                <?php endif ?>
                                <?php echo @$member_nav['label']; ?>
                            </a>
                        </li>
                    <?php endif ?>
                <?php endforeach ?>
                <li>
                    <a href="<?php echo @$this->content['navigation']['member']['logout']['url'] ?>">
                        <span class="fa fa-sign-out"></span>
                        <?php echo @$this->content['navigation']['member']['logout']['label'] ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
</ul>
