<select name="quick-filter" class="quick-filter" aria-label="<?php echo __('Quick Filter'); ?>">
    <option><span class="quick-filter-heading"><?php echo __('Quick Filter'); ?></span></option>
    <option value="<?php echo url('commenting/comment/browse'); ?>"><?php echo __('View All') ?></a></option>
    <option value="<?php echo url('commenting/comment/browse', array('approved' => 1)); ?>"><?php echo __('Approved'); ?></a></option>
    <option value="<?php echo url('commenting/comment/browse', array('approved' => 0)); ?>"><?php echo __('Needs Approval'); ?></a></option>
    <option value="<?php echo url('commenting/comment/browse', array('is_spam' => 1)); ?>"><?php echo __('Spam'); ?></a></option>
    <option value="<?php echo url('commenting/comment/browse', array('is_spam' => 0)); ?>"><?php echo __('Not Spam'); ?></a></option>
    <option value="<?php echo url('commenting/comment/browse', array('flagged' => 1)); ?>"><?php echo __('Flagged'); ?></a></option>
    <option value="<?php echo url('commenting/comment/browse', array('flagged' => 0)); ?>"><?php echo __('Not Flagged'); ?></a></option>
</select>
