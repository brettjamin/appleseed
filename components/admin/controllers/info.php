<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Admin
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Admin Component Controller
 * 
 * Admin Info Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Admin
 */
class cAdminInfoController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		parent::Display( $pView, $pData );
	}

}