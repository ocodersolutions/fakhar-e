<?php

namespace Ocoder\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CmsButtonPublished extends AbstractHelper {

    public function __invoke($id, $published, $options = null) {
        $classPublished = ($published == 1) ? 'success' : 'default';
        return sprintf('<a href="#" onclick="javascript:changePublished(\'%s\',\'%s\')" class="label label-%s"><i class="fa fa-check"></i></a>', $id, $published, $classPublished);
    }

}
