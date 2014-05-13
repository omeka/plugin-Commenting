<script type='text/javascript'>
<?php include('config_form.js'); ?>
</script>

<?php $view = get_view(); ?>
    <div class="field">
        <div class="three columns alpha">
            <label><?php echo __("Use Threaded Comments?"); ?></label>    
        </div>    
        <div class="inputs four columns omega">
            <p class="explanation"><?php echo __("If checked, replies will be displayed indented below the comment."); ?></p>
            <div class="input-block">        
            <?php echo $view->formCheckbox('commenting_threaded', null,
                array('checked'=> (bool) get_option('commenting_threaded') ? 'checked' : ''
            
                )
            ); ?>                
            </div>
        </div>
    </div>
    
    <div class='field'>
        <div class="three columns alpha">
            <label><?php echo __("Text for comments label"); ?></label>
        </div>
        <div class='inputs four columns omega'>
            <p class='explanation'><?php echo __("A label instead of 'Comments' to use. Leave empty to use 'Comments'."); ?></p>
            <div class='input-block'>
                <?php echo $view->formText('commenting_comments_label', get_option('commenting_comments_label')); ?>
            </div>        
        </div>
    </div>    
    
    <div class="field">
        <div class="three columns alpha">
            <label><?php echo __("Allow public commenting?"); ?></label>    
        </div>    
        <div class="inputs four columns omega">
                <p class="explanation"><?php echo __("Allows everyone, including non-registered users to comment. Using this without Akismet is strongly discouraged."); ?></p>
            <div class="input-block">        
                <?php echo $view->formCheckbox('commenting_allow_public', null,
                    array('checked'=> (bool) get_option('commenting_allow_public') ? 'checked' : '',
                    )
                ); ?>              
            </div>
        </div>
    </div>    
    
<div class='field' id='commenting-moderate-public'>
    <div class="three columns alpha">
        <label><?php echo __("Require moderation for all public comments?"); ?></label>
    </div>
    <div class='inputs four columns omega'>
        <p class='explanation'><?php echo __("If unchecked, comments will appear immediately."); ?></p>
        <div class="input-block">
            <?php echo $view->formCheckbox('commenting_require_public_moderation', null, 
                            array('checked'=> (bool) get_option('commenting_require_public_moderation') ? 'checked' : '',
                            )); ?>
        </div>
    </div>
</div>    
    
<div class="field" id='moderate-options'>
    <div class="three columns alpha">
        <label><?php echo __("User roles that can moderate comments"); ?></label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"><?php echo __("The user roles that are allowed to moderate comments."); ?></p>
        <div class="input-block">        
            <?php
                $moderateRoles = unserialize(get_option('commenting_moderate_roles'));
                $userRoles = get_user_roles();
                unset($userRoles['super']);
                echo '<ul>';
        
                foreach($userRoles as $role=>$label) {
                    echo '<li>';
                    echo $view->formCheckbox('commenting_moderate_roles[]', $role,
                        array('checked'=> in_array($role, $moderateRoles) ? 'checked' : '')
                        );
                    echo $label;
                    echo '</li>';
                }
                echo '</ul>';
            ?>
        </div>
    </div>
</div>


<div id='non-public-options'>
    <div class="field">
        <div class="three columns alpha">
            <label><?php echo __("User roles that can comment"); ?></label>    
        </div>    
        <div class="inputs four columns omega">
            <p class="explanation"><?php echo __("Select the roles that can leave comments"); ?></p>
            <div class="input-block">        
                <?php
                    $commentRoles = unserialize(get_option('commenting_comment_roles'));
            
                    echo '<ul>';
            
                    foreach($userRoles as $role=>$label) {
                        echo '<li>';
                        echo $view->formCheckbox('commenting_comment_roles[]', $role,
                            array('checked'=> in_array($role, $commentRoles) ? 'checked' : '')
                            );
                        echo $label;
                        echo '</li>';
            
                    }
                    echo '</ul>';
                ?>
            </div>
        </div>
    </div>
    
    <div class="field">
        <div class="three columns alpha">
            <label><?php echo __("User roles that require moderation before publishing."); ?></label>    
        </div>    
        <div class="inputs four columns omega">
            <p class="explanation"><?php echo __("If the role is allowed to moderate comments, that will override the setting here."); ?></p>
            <div class="input-block">        
                <?php
                    $reqAppCommentRoles = unserialize(get_option('commenting_reqapp_comment_roles'));
                    echo '<ul>';
                    foreach($userRoles as $role=>$label) {
                        echo '<li>';
                        echo $view->formCheckbox('commenting_reqapp_comment_roles[]', $role,
                            array('checked'=> in_array($role, $reqAppCommentRoles) ? 'checked' : '')
                            );
                        echo $label;
                        echo '</li>';
            
                    }
                    echo '</ul>';
                ?>
            </div>
        </div>
    </div>

    <div class="field">
        <div class="three columns alpha">
            <label><?php echo __("Allow public to view comments?"); ?></label>    
        </div>    
        <div class="inputs four columns omega">
            <p class="explanation"></p>
            <div class="input-block">        
                <?php echo $view->formCheckbox('commenting_allow_public_view', null,
                    array('checked'=> (bool) get_option('commenting_allow_public_view') ? 'checked' : '',
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>

    <div class="field view-options">
        <div class="three columns alpha">
            <label><?php echo __("User roles that can view comments"); ?></label>    
        </div>    
        <div class="inputs four columns omega">
            <div class="input-block">        
                <?php
                    $viewRoles = unserialize(get_option('commenting_view_roles'));
                    if(!$viewRoles) {
                        $viewRoles = array();
                    }
                    echo '<ul>';
                    foreach($userRoles as $role=>$label) {
                        echo '<li>';
                        echo $view->formCheckbox('commenting_view_roles[]', $role,
                            array('checked'=> in_array($role, $viewRoles) ? 'checked' : '')
                            );
                        echo $label;
                        echo '</li>';
                    }
                    echo '<ul>';
                ?>
            </div>
        </div>
    </div>
    
<?php if(!Omeka_Captcha::isConfigured()): ?>
<p class="alert"><?php echo __("You have not entered your %s API keys under %s. We recommend adding these keys, or the commenting form will be vulnerable to spam.", '<a href="http://recaptcha.net/">reCAPTCHA</a>', "<a href='" . url('security#recaptcha_public_key') . "'>" . __('security settings') . "</a>");?></p>
<?php endif; ?>


<div class="field">
    <div class="three columns alpha">
        <label><?php echo __("WordPress API key for Akismet"); ?></label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"></p>
        <div class="input-block">        
            <?php echo $view->formText('commenting_wpapi_key', get_option('commenting_wpapi_key'),
                array('size'=> 45)
            );?>        
        </div>
    </div>
</div>
