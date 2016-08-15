<?php

namespace Ocoder\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CmsLinkSort extends AbstractHelper {

    public function __invoke($name, $column, $ssFilter, $options = null) {
        $order = ($ssFilter['order'] == 'ASC') ? 'DESC' : 'ASC';
        $class = null;
        $optionClass = null;
        if (!empty($options['class'])) {
            $optionClass = $options['class'];
            $class = 'sorting ' . $optionClass;
        }
        if ($ssFilter['order_by'] == $column) {
            $class = $optionClass . ' sorting_' . strtolower($ssFilter['order']);
        }

        $style = null;
        if (!empty($options['style'])) {
            $style = 'style=' . $options['style'];
        }

        return sprintf(
                '<th class="%s" %s><a href="#" onclick="javascript:sortList(\'%s\', \'%s\')">%s</a></th>', 
                $class, $style, $column, $order, $name
            );
    }

}
