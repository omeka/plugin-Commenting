<?php $view = get_view(); ?>
<div class="field">
    <div class="three columns alpha">
        <label>Use Threaded Comments?</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"></p>
        <div class="input-block">        
        <?php echo $view->formCheckbox('commenting_threaded', null,
            array('checked'=> (bool) get_option('commenting_threaded') ? 'checked' : ''
        
            )
        ); ?>                
        </div>
    </div>
</div>

<div class="field">
    <div class="three columns alpha">
        <label>ReCaptcha Public Key</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class='explanation'>This can also be set in the Security Settings.</p>
    
        <div class="input-block">        
        <?php echo $view->formText('recaptcha_public_key', get_option('recaptcha_public_key'),
            array('size'=>45)
        
            ); ?>
                        
        </div>
    </div>
</div>

<div class="field">
    <div class="three columns alpha">
        <label>ReCaptcha Private Key</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class='explanation'>This can also be set in the Security Settings.</p>
        <div class="input-block">        
        <?php echo $view->formText('recaptcha_private_key', get_option('recaptcha_private_key'),
            array('size'=>45)
        
            ); ?>
                        
        </div>
    </div>
</div>

<div class="field">
    <div class="three columns alpha">
        <label>User roles that can moderate comments</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"></p>
        <div class="input-block">        
            <?php
                $moderateRoles = unserialize(get_option('commenting_moderate_roles'));
                $userRoles = get_user_roles();
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

<div class="field">
    <div class="three columns alpha">
        <label>User roles that can comment WITH approval</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"></p>
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
        <label>User roles that can comment WITHOUT approval</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"></p>
        <div class="input-block">        
            <?php
                $noAppCommentRoles = unserialize(get_option('commenting_noapp_comment_roles'));
        
                echo '<ul>';
        
                foreach($userRoles as $role=>$label) {
                    echo '<li>';
                    echo $view->formCheckbox('commenting_noapp_comment_roles[]', $role,
                        array('checked'=> in_array($role, $noAppCommentRoles) ? 'checked' : '')
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
        <label>User roles that can view comments</label>    
    </div>    
    <div class="inputs four columns omega">
        <p class="explanation"></p>
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

<div class="field">
    <div class="three columns alpha">
        <label>Allow public to view comments?</label>    
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

<div class="field">
    <div class="three columns alpha">
        <label>Allow public commenting?</label>    
    </div>    
    <div class="inputs four columns omega">
            <p class="explanation">This overrides all of the above limitations on commenting and viewing. Commenters are required to provide an email. Only allow public commenting if you are using Akismet.</p>
        <div class="input-block">        
            <?php echo $view->formCheckbox('commenting_allow_public', null,
                array('checked'=> (bool) get_option('commenting_allow_public') ? 'checked' : '',
                )
            ); ?>
    
                    
        </div>
    </div>
</div>

<div class="field">
    <div class="three columns alpha">
        <label>WordPress API key for Akismet</label>    
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
