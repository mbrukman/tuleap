<?php
/**
* Copyright (c) Xerox Corporation, CodeX Team, 2001-2007. All rights reserved
*
* $Id$
*/

require_once(CODEX_CLI_DIR.'/CLI_Action.class.php');

class CLI_Action_Docman_Monitor extends CLI_Action {
    function CLI_Action_Docman_Monitor() {
        $this->CLI_Action('monitor', 'Monitor a document or a folder');
        $this->addParam(array(
            'name'           => 'item_id',
            'description'    => '--id=<item_id>     ID of the item we want to monitor',
            'parameters'     => array('id'),
        ));
    }

    function validate_item_id(&$item_id) {
        if (!$item_id) {
            exit_error("You must specify the ID of the document with the --id parameter");
        }
        return true;
    }
    function validate_group_id(&$group_id) {
        $group_id = $this->get_working_group($group_id);
        return true;
    }
}

?>
