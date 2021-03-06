<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Config
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Config Component
 * 
 * Config Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Config
 */
class cConfig extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AdminMenu ( $pData = null ) {
		
		$return = array ();
		
		$return[] = array ( 'title' =>"Configuration", 'class' => "config", 'link' => "/admin/config/" );
		
		return ( $return );
	} 
	
	
}
