<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Mutual Controller
 * 
 * Friends Component Mutual Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsMutualController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		switch ( $pView ) {
			case 'summary':
				return ( $this->_HandleSummary() );
			break;
			default:
				return ( $this->_Handle() );
			break;
		}
		
	}
	
	private function _HandleSummary ( ) {
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		// If user isn't logged in, or we're viewing our own page, then don't display.
		if ( ( $focus->Username == $current->Username ) or ( !$current->Username ) ) {
			return ( true );
		}
		
		$this->View = $this->GetView ( 'summary' ); 
		
		$this->View->Find ( '[class=all-mutual-friends-link]', 0 )->href = 'http://' . $focus->Domain . '/profile/' . $focus->Username . '/friends/mutual/';
		
		if ( !$this->_PrepSummary() ) return ( false );
		
		$this->View->Display();
		
	}
	
	private function _PrepSummary ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$tabs =  $this->View->Find ('nav[id=profile-friends-tabs]', 0);
		$tabs->innertext = $this->GetSys ( 'Components' )->Buffer ( 'friends', 'tabs' );
		
		$currentAccount = $current->Username . '@' . $current->Domain;
		$data = array ( "account" => $currentAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
		$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		$currentInfo->username = $current->Username;
		$currentInfo->domain = $current->Domain;
		$currentInfo->account = $current->Username . '@' . $current->Domain;
		
		$focusAccount = $focus->Username . '@' . $focus->Domain;
		$data = array ( "account" => $focusAccount, 'source' => ASD_DOMAIN, 'request' => $focusAccount );
		$focusInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		$focusInfo->username = $focus->Username;
		$focusInfo->domain = $focus->Domain;
		$focusInfo->account = $focus->Username . '@' . $focus->Domain;
		
		$mutualFriends = array_intersect ( $currentInfo->friends, $focusInfo->friends );
		
		if ( count ( $mutualFriends ) < 1 ) return ( false );
		
		$li = $this->View->Find ( "[class=friends-mutual-summary-item]", 0);
		
		$row = $this->View->Copy ( "[class=friends-mutual-summary-item]" )->Find ( "[class=friends-mutual-summary-item]", 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		foreach ( $mutualFriends as $m => $mutualFriend ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
		
			list ( $username, $domain ) = split ( '@', $mutualFriend );
			
			$data = array ( "username" => $username, "domain" => $domain, "width" => 32, "height" => 32 );
			$row->Find ( '[class=friends-icon]', 0 )->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
			
			$row->Find ( '[class=friends-icon-link]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		return ( true );
	}
	
	
	private function _Handle ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->_Prep();
		
		if ( !$current ) {
			$this->_PrepAnonymous();
		} else if ( $current->Account == $focus->Account ) {
			$this->_PrepFocus();
		} else {
			$this->_PrepCurrent();
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$this->GetSys ( "Request" )->Set ( "Circle", "mutual" );
		$tabs =  $this->View->Find ('nav[id=profile-friends-tabs]', 0);
		$tabs->innertext = $this->GetSys ( 'Components' )->Buffer ( 'friends', 'tabs' );
		
		if ( $current ) {
			$currentAccount = $current->Username . '@' . $current->Domain;
			$data = array ( "account" => $currentAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			$currentInfo->username = $current->Username;
			$currentInfo->domain = $current->Domain;
			$currentInfo->account = $current->Username . '@' . $current->Domain;
		}
		
		$currentAccount = $current->Username . '@' . $current->Domain;
		
		$this->Model = $this->GetModel();
		
		list ( $start, $step, $page ) = $this->_PageCalc();
		$this->Model->Retrieve ( array ( "userAuth_uID" => $focus->uID ), null, array ( "start" => $start, "step" => $step ) );
		
		$friendCount = $this->Model->Get ( "Total" );
		
		$this->View->Find ( '[class=profile-friends-owner]', 0 )->innertext = __( "Friends Of User", array ( "fullname" => $focus->Fullname ) );
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( "Number Of Friends", array ( "count" => $friendCount ) );
		
		$li = $this->View->Find ( "ul[class=friends-list] li", 0);
		
		$row = $this->View->Copy ( "[class=friends-list]" )->Find ( "li", 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		while ( $this->Model->Fetch() ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
		
			$username = $this->Model->Get ( "Username" );
			$domain = $this->Model->Get ( "Domain" );
			$account = $username . '@' . $domain;
			
			$data = array ( "username" => $username, "domain" => $domain, "width" => 64, "height" => 64 );
			$row->Find ( '[class=friends-icon]', 0 )->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
			
			$data = array ( "username" => $username, "domain" => $domain, "currentUsername" => $current->Username, "currentDomain" => $current->Domain );
			$row->Find ( "[class=friends-add-friend-link]", 0 )->href = $this->GetSys ( "Event" )->Trigger ( "Create", "Friend", "Addlink", $data );
			$row->Find ( "[class=friends-remove-friend-link]", 0 )->href = $this->GetSys ( "Event" )->Trigger ( "Create", "Friend", "Removelink", $data );
			
			$data = array ( "account" => $account, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$userInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			$userInfo->username = $username;
			$userInfo->domain = $domain;
			$userInfo->account = $username . '@' . $domain;
			
			$row->Find ( '[class=friends-location]', 0 )->innertext = $userInfo->location;
			
			$row->Find ( '[class=friends-status]', 0 )->innertext = $userInfo->status;
			
			$row->Find ( '[class=friends-identity]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			$row->Find ( '[class=friends-identity]', 0 )->innertext = $username . '@' . $domain;
			$row->Find ( '[class=friends-fullname-link]', 0 )->innertext = $userInfo->fullname;
			$row->Find ( '[class=friends-fullname-link]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			$row->Find ( '[class=friends-icon-link]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			
			$mutualFriendsCount = count ( array_intersect ( $currentInfo->friends, $userInfo->friends ) );
			
			if ( $mutualFriendsCount == 1 ) {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( "Mutual Friend Count", array ( "count" => $mutualFriendsCount ) );
			} else if ( $mutualFriendsCount > 1 ) {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( "Mutual Friends Count", array ( "count" => $mutualFriendsCount ) );
			} else {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = "";
			}
			
			if ( !$current ) {
				$row = $this->_PrepAnonymousRow ( $row );
			} else if ( $current->Account == $focus->Account ) {
				$row = $this->_PrepFocusRow ( $row );
			} else {
				$row = $this->_PrepCurrentRow ( $row, $currentInfo, $userInfo );
			}
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$link = $this->GetSys ( "Router" )->Get ( "Base" ) . '(.*)';
		$pageData = array ( 'start' => $start, 'step'  => $step, 'total' => $friendCount, 'link' => $link );
		$pageControl =  $this->View->Find ('nav[class=pagination]', 0);
		$pageControl->innertext = $this->GetSys ( 'Components' )->Buffer ( 'pagination', $pageData ); 
		
		$this->View->Reload();
		
		return ( true );
	}
	
	private function _PageCalc ( ) {
		
		$page = $this->GetSys ( "Request" )->Get ( "Page");
		
		if ( !$page ) $page = 1;
		
		$step = 10;
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		$return = array ( $start, $step, $page );
		
		return ( $return );
	}
	
	private function _PrepFocus ( ) {
		
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$editor = false;
		if ( ( $focus->Username == $current->Username ) and ( $focus->Domain == $current->Domain ) ) {
			$editor = true;
		}
		
		if ( $editor ) {
			$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/edit/';
		} else {
			$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->outertext = " ";
		}
		
		return ( true );
	}
	
	private function _PrepAnonymous ( ) {
		// Remove "edit circles" link
		$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->outertext = " ";
		
		return ( true );
	}
	
	private function _PrepCurrent ( ) {
		
		$editor = false;
		
		if ( $editor ) {
			$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/edit/';
		} 
		
		return ( true );
	}
	
	private function _PrepAnonymousRow ( $pRow ) {
		// Remove "add as friend" 
		$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		
		// Remove "remove from friends"
		$pRow->Find ( "[class=friends-remove-friend]", 0 )->innertext = "";
		
		// Remove "add circles" dropdown
		$pRow->Find ( "[class=friends-circle-editor]", 0 )->innertext = "";
		
		// Remove "Mutual Friends" count
		$pRow->Find ( "[class=friends-mutual-count]", 0 )->innertext = "";
		
		return ( $pRow );
	}
	
	private function _PrepFocusRow ( $pRow ) {
		
		// Remove "add as friend" 
		$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		
		// Prep "add circles" dropdown
		// $pRow->Find ( "[class=friends-circle-editor]", 0 )->innertext = "";
		
		// Remove "Mutual Friends" count
		$pRow->Find ( "[class=friends-mutual-count]", 0 )->innertext = "";
		
		return ( $pRow );
	}
	
	private function _PrepCurrentRow ( $pRow, $pCurrentUserInfo, $pUserInfo ) {
		
		// Remove "add circles" dropdown
		$pRow->Find ( "[class=friends-circle-editor]", 0 )->innertext = "";
		
		if ( in_array ( $pCurrentUserInfo->account, $pUserInfo->friends ) ) {
			// Remove "add as friend" if already friends
			$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		} else if ( $pCurrentUserInfo->account == $pUserInfo->account ) {
			// Remove both since we're looking at our own account.
			$pRow->Find ( "[class=friends-remove-friend]", 0 )->innertext = "";
			$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		} else {
			// Remove "remove from friends" if not friends
			$pRow->Find ( "[class=friends-remove-friend]", 0 )->innertext = "";
		}
		
		return ( $pRow );
	}
	
}
