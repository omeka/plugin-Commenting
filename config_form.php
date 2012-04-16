
<div class='field'>
<label for='commenting_threaded'>Use Threaded Comments?</label>
<div class='inputs'>
<?php echo __v()->formCheckbox('commenting_threaded', null,
    array('checked'=> (bool) get_option('commenting_threaded') ? 'checked' : ''

    )
); ?>

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


<div class='field'>
<label for='commenting_comment_roles'>User roles that can comment WITH approval</label>
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
<label for='commenting_comment_roles'>User roles that can comment WITHOUT approval</label>
<div class='inputs'>
    <?php
        $noAppCommentRoles = unserialize(get_option('commenting_noapp_comment_roles'));

        echo '<ul>';

        foreach($userRoles as $role=>$label) {
            echo '<li>';
            echo __v()->formCheckbox('commenting_noapp_comment_roles[]', $role,
                array('checked'=> in_array($role, $noAppCommentRoles) ? 'checked' : '')
                );
            echo $label;
            echo '</li>';

        }
        echo '</ul>';
    ?>

</div>
</div>



<div class='field'>
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
<label for='commenting_allow_public_view'>Allow public to view comments?</label>
    <div class='inputs'>
        <?php echo __v()->formCheckbox('commenting_allow_public_view', null,
            array('checked'=> (bool) get_option('commenting_allow_public_view') ? 'checked' : '',
            )
        ); ?>

    </div>
</div>

<div class='field'>
<label for='commenting_allow_public'>Allow public commenting?</label>
    <div class='inputs'>
        <?php echo __v()->formCheckbox('commenting_allow_public', null,
            array('checked'=> (bool) get_option('commenting_allow_public') ? 'checked' : '',
            )
        ); ?>
        <p class="explanation">This overrides all of the above limitations on commenting and viewing. Commenters are required to provide an email. Only allow public commenting if you are using Akismet.</p>

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