<?php
/*
 * Copyright (c) Enalean, 2015. All Rights Reserved.
 * Copyright 2005, STMicroelectronics
 *
 * Originally written by Manuel Vacelet
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */
require_once('www/project/admin/permissions.php');
/**
 * Manipulation of Wiki service.
 * 
 * This class is a part of the Model of Wiki Service it aims to be the
 * interface between data corresponding to a Wiki Service (instance of
 * PhpWiki for Codendi) and Codendi application
 *
 * @package   WikiService
 * @copyright STMicroelectronics, 2005
 * @author    Manuel Vacelet <manuel.vacelet-abecedaire@st.com>
 * @license   http://opensource.org/licenses/gpl-license.php GPL
 */
class PHPWiki {
  /* private int */ var $gid;
  /* private string */ var $language_id;
    /* private int */ var $exist;

  /**
   * WikiSericeModel - Constructor
   *
   * @access public
   * @param  int $id Project identifier
   */
  function PHPWiki($id=0) {
    $this->gid = (int) $id;
    $this->exist = null;
  }

  /**
   *
   * @return boolean Return if a permission is set on this Wiki
   */
  function permissionExist() {
    return permission_exist('PHPWIKI_READ', $this->gid);
  }

  /**
   * Check if user can access to whole wiki
   *
   * checkPermissions - Public
   * @param  int     User identifier
   * @return boolean Is the given user allowed to access to the Wiki
   */
  function isAutorized($uid) {
    $autorized = permission_is_authorized('PHPWIKI_READ', $this->gid, $uid, $this->gid);
    return $autorized;
  }

  /**
   * Set access permissions.
   *
   * @param  string[] $groups List of groups allowed to access to the Wiki
   * @return boolean  Modification status
   */
  function setPermissions($groups) {
    global $feedback;

    list ($ret, $feedback) = permission_process_selection_form($this->gid, 
							       'PHPWIKI_READ',
							       $this->gid, 
							       $groups);
    return $ret;
  }

 
  /**
   * Reset access permissions.
   *
   * @return boolean  Modification status
   */
  function resetPermissions() {
    return permission_clear_all($this->gid, 
                                'PHPWIKI_READ',
                                $this->gid);
  }

 
  /**
   * Check WikiEntry existance for given project.
   * @return boolean
   */
  function exist() {
      if($this->exist === null) {
          $res = db_query('SELECT count(*) AS nb FROM plugin_phpwiki_page'
                          .' WHERE group_id='.db_ei($this->gid));

          $this->exist = (db_result($res, 0, 'nb') > 0);
      }
      return $this->exist;
  }

  /**
   * Get number of wiki pages.
   * @return number of pages (0 if wiki is empty)
   */
  function getPageCount() {
    $res = db_query(' SELECT count(*) as count'
		    .' FROM plugin_phpwiki_page, plugin_phpwiki_nonempty'
		    .' WHERE plugin_phpwiki_page.group_id="'.db_ei($this->gid).'"'
		    .' AND plugin_phpwiki_nonempty.id=plugin_phpwiki_page.id');
    
    if(db_numrows($res) > 0) 
      return db_result($res,0,'count');
    else
      return 0;
  }

  
  /**
   * Get number of project wiki pages.
   * @return number of project pages (0 if wiki is empty)
   */
  function getProjectPageCount() {
    $res = db_query(' SELECT count(*) as count'
		    .' FROM plugin_phpwiki_page, plugin_phpwiki_nonempty'
		    .' WHERE plugin_phpwiki_page.group_id="'.db_ei($this->gid).'"'
		    .' AND plugin_phpwiki_nonempty.id=plugin_phpwiki_page.id'
            .' AND plugin_phpwiki_page.pagename NOT IN ("'.implode('","', PHPWikiPage::getDefaultPages()).'",
                                              "'.implode('","', PHPWikiPage::getAdminPages()).'")');
    
    if(db_numrows($res) > 0) 
      return db_result($res,0,'count');
    else
      return 0;
  }

  
  /** 
   * Get wiki language (set at creation time)
   * return 0 if no wiki document exist
   */
  function getLanguage_id() {
      // The language of the wiki is the language of all its wiki documents.
      if (!$this->language_id) {
          // We only support one language for all the wiki documents of a project.
          $wei =& PHPWikiEntry::getEntryIterator($this->gid);
          if ($wei->valid()) {
              $we =& $wei->current(); // get first element  
              $this->language_id = $we->getLanguage_id();
          }
      }
      return $this->language_id;
  }


  /**
   * Experimental
   */

  function dropLink($id) {
    $res = db_query('  DELETE FROM plugin_phpwiki_link'
		    .' WHERE linkfrom='.db_ei($id)
		    .' OR linkto='.db_ei($id));

    if(db_affected_rows($res) === 1)
      return true;

    
  }

  function dropNonEmpty($id) {
    $res = db_query('  DELETE FROM plugin_phpwiki_nonempty'
		    .' WHERE id='.db_ei($id));

   
  }

  function dropRecent($id) {
    $res = db_query('  DELETE FROM plugin_phpwiki_recent'
		    .' WHERE id='.db_ei($id));

    
  }

  function dropVersion($id) {
    $res = db_query('  DELETE FROM plugin_phpwiki_version'
		    .' WHERE id='.db_ei($id));

   
    
  }

  function dropPage($id) {
    $res = db_query('  DELETE FROM plugin_phpwiki_page'
		    .' WHERE id='.db_ei($id));
  }

  function drop() {
    //TODO: Drop entries


    //
    // PhpWiki
    //
    $res = db_query('  SELECT id FROM plugin_phpwiki_page'
		    .' WHERE group_id='.db_ei($this->gid));
    
    while($row = db_fetch_array($res)) {
      $pid = $row['id'];

      // Link
      $this->dropLink($pid);

      // Non empty
      $this->dropNonEmpty($pid);

      // Recent
      $this->dropRecent($pid);

      // Version
      $this->dropVersion($pid);

      // Page
      $this->dropPage($pid);
    }
  }
}
?>