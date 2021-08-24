<?php

/**
 * Show the currently-active filters for a browse.
 * 
 * @package Commenting\View\Helper
 */
class Commenting_View_Helper_CommentFilters extends Zend_View_Helper_Abstract
{
    /**
     * Get a list of the currently-active filters for comment browse.
     *
     * @param array $params Optional array of key-value pairs to use instead of
     *  reading the current params from the request.
     * @return string HTML output
     */
    public function commentFilters(array $params = null)
    {
        if ($params === null) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $requestArray = $request->getParams();
        } else {
            $requestArray = $params;
        }

        $filters = ['flagged', 'is_spam', 'approved'];
        $db = get_db();
        $displayArray = array();
        foreach ($requestArray as $key => $value) {
            if (($value != null) && (array_search($key,$filters) !== false)) {
                $filter = ucfirst($key);
                switch ($key) {
                    case 'is_spam':
                        $filter = __('Spam');
                        break;
                }
                $displayArray[$filter] = ($value == 1) ? __('Yes') : __('No');
            }
        }

        $displayArray = apply_filters('item_search_filters', $displayArray, array('request_array' => $requestArray));

        $html = '';
        if (!empty($displayArray) || !empty($advancedArray)) {
            $html .= '<div id="item-filters">';
            $html .= '<ul>';
            foreach ($displayArray as $name => $query) {
                $class = html_escape(strtolower(str_replace(' ', '-', $name)));
                $html .= '<li class="' . $class . '">' . html_escape(__($name)) . ': ' . html_escape($query) . '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        return $html;
    }
}
