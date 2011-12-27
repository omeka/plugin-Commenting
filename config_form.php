
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
<label for='commenting_allow_public'>Allow Public Commenting?</label>
    <div class='inputs'>
        <?php echo __v()->formCheckbox('commenting_allow_public', null,
            array('checked'=> (bool) get_option('commenting_allow_public') ? 'checked' : '',
            )
        ); ?>
        <p class="explanation">Only allow public commenting if you are using Akismet.</p>

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