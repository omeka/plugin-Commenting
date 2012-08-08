<script type='text/javascript'>
<?php include('config_form.js'); ?>
</script>

<h2>Basic Setup</h2>
<div class='field'>
    <label for='commenting_threaded'>Use Threaded Comments?</label>
    <div class='inputs'>
    <?php echo __v()->formCheckbox('commenting_threaded', null,
        array('checked'=> (bool) get_option('commenting_threaded') ? 'checked' : ''
    
        )
    ); ?>
    
    </div>
</div>

<!--  Basic settings -->

<div class='field'>
    <label for='commenting_allow_public'>Allow public commenting?</label>
    <div class='inputs'>
        <?php echo __v()->formCheckbox('commenting_allow_public', null,
            array('checked'=> (bool) get_option('commenting_allow_public') ? 'checked' : '',
            )
        ); ?>
        <p class="explanation">Allows everyone, including non-registered users to comment. Using this without Akismet is strongly discouraged.</p>
    </div>
</div>


<div class='field'>
    <label for='commenting_require_public_moderation'>Require moderation for all public comments?</label>
    <div class='inputs'>
        <?php echo __v()->formCheckbox('commenting_require_public_moderation', null, 
                        array('checked'=> (bool) get_option('commenting_require_public_moderation') ? 'checked' : '',
                        )); ?>
        <p class='explanation'>If unchecked, comments will appear immediately.</p>
    </div>
</div>


<div class='field moderate-options'>
    <label for='commenting_moderate_roles'>User roles that can moderate comments</label>
    <div class='inputs'>
        <?php
            $moderateRoles = unserialize(get_option('commenting_moderate_roles'));
            $userRoles = get_user_roles();
            echo '<ul>';
    
            foreach($userRoles as $role=>$label) {
                echo '<li>';
                echo __v()->formCheckbox('commenting_moderate_roles[]', $role,
                    array('checked'=> in_array($role, $moderateRoles) ? 'checked' : '')
                    );
                echo $label;
                echo '</li>';
    
            }
            echo '</ul>';
        ?>
    
    </div>
</div>


<!--  End basic settings -->


<div id='non-public-options'>

    <div class='field'>
        <label for='commenting_comment_roles'>User roles that can comment</label>
        <div class='inputs'>
            <?php
                $commentRoles = unserialize(get_option('commenting_comment_roles'));
        
                echo '<ul>';
        
                foreach($userRoles as $role=>$label) {
                    echo '<li>';
                    echo __v()->formCheckbox('commenting_comment_roles[]', $role,
                        array('checked'=> in_array($role, $commentRoles) ? 'checked' : '')
                        );
                    echo $label;
                    echo '</li>';
        
                }
                echo '</ul>';
            ?>
        
        </div>
    </div>
    
    <div class='field'>
        <label for='commenting_comment_roles'>User roles that require moderation before publishing</label>
        <div class='inputs'>
            <?php
                $moderatedCommentRoles = unserialize(get_option('commenting_moderated_comment_roles'));
        
                echo '<ul>';
        
                foreach($userRoles as $role=>$label) {
                    echo '<li>';
                    echo __v()->formCheckbox('commenting_moderated_comment_roles[]', $role,
                        array('checked'=> in_array($role, $moderatedCommentRoles) ? 'checked' : '')
                        );
                    echo $label;
                    echo '</li>';
        
                }
                echo '</ul>';
            ?>
        
        </div>
    </div>
    
    
    
    <div class='field'>
        <label for='commenting_allow_public_view'>Allow public to view comments?</label>
        <div class='inputs'>
            <?php echo __v()->formCheckbox('commenting_allow_public_view', null,
                array('checked'=> (bool) get_option('commenting_allow_public_view') ? 'checked' : '',
                )
            ); ?>
    
        </div>
    </div>

</div>

<div class='field view-options'>
    <label for='commenting_view_roles'>User roles that can view comments</label>
    <div class='inputs'>
        <?php
            $viewRoles = unserialize(get_option('commenting_view_roles'));
            if(!$viewRoles) {
                $viewRoles = array();
            }
            echo '<ul>';
            foreach($userRoles as $role=>$label) {
                echo '<li>';
                echo __v()->formCheckbox('commenting_view_roles[]', $role,
                    array('checked'=> in_array($role, $viewRoles) ? 'checked' : '')
                    );
                echo $label;
                echo '</li>';
    
            }
            echo '<ul>';    
        ?>    
    </div>
</div>



<div class='field'>
    <label for='recaptcha_public_key'>Recaptcha Public Key</label>
    <div class='inputs'>
        <?php echo __v()->formText('recaptcha_public_key', get_option('recaptcha_public_key'),
            array('size'=>45)
        
            ); ?>
        <p class='explanation'>This can also be set in the Security Settings.</p>    
    </div>
</div>

<div class='field'>
    <label for='recaptcha_private_key'>Recaptcha Private Key</label>
    <div class='inputs'>    
        <?php echo __v()->formText('recaptcha_private_key', get_option('recaptcha_private_key'),
            array('size'=>45)
        
            ); ?>
        <p class='explanation'>This can also be set in the Security Settings.</p>
    </div>
</div>






<div class='field'>
<label for='commenting_wpapi_key'>WordPress API key for Akismet</label>
    <div class='inputs'>
        <?php echo __v()->formText('commenting_wpapi_key', get_option('commenting_wpapi_key'),
            array('size'=> 45)
        );?>
    </div>


</div>