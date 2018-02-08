<?php
/**
 * Job_CommentNotification class - handles email notifications for Comments
 * 
 * @package Commenting
 */

class Job_CommentNotification extends Omeka_Job_AbstractJob
{
    const QUEUE_NAME = 'CommentNotification';

    /**
     * Performs the task 
     */
    public function perform()
    {
        // TODO - in future we may have logic also for daily notifications...
        $this->instantNotification();
    }

    /**
     * Send instant notification for new comment
     */
    public function instantNotification()
    {
        if (empty($this->_options['recipients'])) {
            _log(__CLASS__ .": finished. No notification recipients found.");
            return;
        }
        if (empty($this->_options['id'])) {
            _log(__CLASS__ .": finished. Missing comment ID.");
            return;
        }
        $record = $this->_db->getTable('Comment')->find($this->_options['id']);
        if (empty($record)) {
            _log(__CLASS__ .": finished. Comment not found.");
            return;
        }
        if (empty($this->_options['webRoot'])) {
            _log(__CLASS__ .": Missing WEB_ROOT.", Zend_Log::WARN);
        }

        $target = $this->_db->getTable($record->record_type)->find($record->record_id);
        $user = current_user();

        $mail = new Zend_Mail('UTF-8');
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->setFrom(get_option('administrator_email'), get_option('site_title'));
        $subject = __('[%s] - New comment on: %s', get_option('site_title'), $this->getTargetTitle($target));
        $body = "<p>"
            . __('New comment from %s', $this->getAuthor($record))
            . "<blockquote>{$record->body}</blockquote>" 
            . "</p>";
        $url = $this->_options['webRoot'] 
            . '/admin/commenting/comment/browse?' 
            . http_build_query(array('record_type' => $record->record_type, 'record_id' => $record->record_id))
            . '#comment-' . $record->id;
        $body .= "<p>" . __("You can manage the comment %s", '<a href="' . $url . '">' . __('here') . '</a>' ) . '</p>';
        $mail->setSubject($subject);
        $mail->setBodyHtml($body);

        foreach ($this->_options['recipients'] as $recipient) {
            // do not sent notifications for comments created by current_user()
            if ($record->author_email == $recipient || ($user && $user->email == $recipient)) {
                continue;
            }
            $mail->addTo($recipient);
            try {
                $mail->send();
            } catch(Exception $e) {
                // continue the loop, even if one notification fails
                _log($e->getMessage());
            }
            $mail->clearRecipients();
        }
    }

    protected function getTargetTitle(Omeka_Record_AbstractRecord $target)
    {
        $title = '';
        $recordType = get_class($target);
        switch ($recordType) {
            case 'Item':
            case 'Collection':
            default:
                $title = metadata($target, 'display_title');
                // TODO - is there no better way to get the title?
                if (empty($title)) {
                    try {
                        $title = metadata($target, 'name');
                    } catch(InvalidArgumentException $e) {}
                }
                if (empty($title)) {
                    try {
                        $title = metadata($target, 'title');
                    } catch(InvalidArgumentException $e) {}
                }
                if (empty($title)) {
                    try {
                        $title = metadata($target, 'label');
                    } catch(InvalidArgumentException $e) {}
                }
                if (!empty($title)) {
                    $title = __($recordType) . ' - ' . $title;
                }
                break;
        }
        return $title ?: __('[Untitled]');
    }

    protected function getAuthor(Comment $record)
    {
        $author = '';
        if (!empty($record->author_name)) {
            $author = html_escape($record->author_name);
        } else {
            $author = __('Anonymous');
        }
        if (!empty($record->author_email)) {
            $author .= ' [<a href="mailto:' . $record->author_email . '">' . $record->author_email . '</a>]';
        }
        return $author;
    }

}
