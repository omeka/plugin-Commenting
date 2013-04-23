<ul class="quick-filter-wrapper">
    <li><a href="#" tabindex="0"><?php echo __('Quick Filter'); ?></a>
    <ul class="dropdown">
        <li><span class="quick-filter-heading"><?php echo __('Quick Filter') ?></span></li>
        <li><a href="<?php echo url('commenting/comment/browse'); ?>"><?php echo __('View All') ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('approved' => 1)); ?>"><?php echo __('Approved'); ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('approved' => 0)); ?>"><?php echo __('Needs Approval'); ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('is_spam' => 1)); ?>"><?php echo __('Spam'); ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('is_spam' => 0)); ?>"><?php echo __('Not Spam'); ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('flagged' => 1)); ?>"><?php echo __('Flagged'); ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('flagged' => 0)); ?>"><?php echo __('Not Flagged'); ?></a></li>                
    </ul>
    </li>
</ul>