<ul class="quick-filter-wrapper">
    <li><a href="#" tabindex="0"><?php echo __('Quick Filter'); ?></a>
    <ul class="dropdown">
        <li><span class="quick-filter-heading"><?php echo __('Quick Filter') ?></span></li>
        <li><a href="<?php echo url('commenting/comment/browse'); ?>"><?php echo __('View All') ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('approved' => 1)); ?>"><?php echo __('Approved'); ?></a></li>
        <li><a href="<?php echo url('commenting/comment/browse', array('approved' => 0)); ?>"><?php echo __('Needs Approval'); ?></a></li>
    </ul>
    </li>
</ul>