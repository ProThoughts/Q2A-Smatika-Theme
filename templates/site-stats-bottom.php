<div class="smatika-site-stats-bottom">
    <div class="container">
        <div class="row">
            <div class="stats-wrap">
                <?php
                    smatika_stats_output( as_opt( 'cache_qcount' ), 'main/1_question', 'main/x_questions' );
                    smatika_stats_output( as_opt( 'cache_acount' ), 'main/1_answer', 'main/x_answers' );

                    if ( as_opt( 'comment_on_qs' ) || as_opt( 'comment_on_as' ) )
                        smatika_stats_output( as_opt( 'cache_ccount' ), 'main/1_comment', 'main/x_comments' );

                    smatika_stats_output( as_opt( 'cache_memberpointscount' ), 'main/1_member', 'main/x_members' );
                ?>
            </div>
        </div>
    </div>
</div>