<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: groups.php                              CREATED: 11-15-2006 + 
  // | LOCATION: /code/content/                     MODIFIED: 04-11-2007 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2008 Appleseed Project                         |
  // +-------------------------------------------------------------------+
  // | This program is free software; you can redistribute it and/or     |
  // | modify it under the terms of the GNU General Public License       |
  // | as published by the Free Software Foundation; either version 2    |
  // | of the License, or (at your option) any later version.            |
  // |                                                                   |
  // | This program is distributed in the hope that it will be useful,   |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of    |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     |
  // | GNU General Public License for more details.                      |	
  // |                                                                   |
  // | You should have received a copy of the GNU General Public License |
  // | along with this program; if not, write to:                        |
  // |                                                                   |
  // |   The Free Software Foundation, Inc.                              |
  // |   59 Temple Place - Suite 330,                                    | 
  // |   Boston, MA  02111-1307, USA.                                    |
  // |                                                                   |
  // |   http://www.gnu.org/copyleft/gpl.html                            |
  // +-------------------------------------------------------------------+
  // | AUTHORS: Michael Chisari <michael.chisari@gmail.com>              |
  // +-------------------------------------------------------------------+
  // | VERSION:      0.7.7                                               |
  // | DESCRIPTION:  Discussion Groupss.                                 |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('legacy/code/include/classes/BASE/application.php'); 
  require_once ('legacy/code/include/classes/BASE/debug.php'); 
  require_once ('legacy/code/include/classes/base.php'); 
  require_once ('legacy/code/include/classes/system.php'); 
  require_once ('legacy/code/include/classes/BASE/remote.php'); 
  require_once ('legacy/code/include/classes/BASE/tags.php'); 
  require_once ('legacy/code/include/classes/BASE/xml.php'); 

  // Include Appleseed classes.
  require_once ('legacy/code/include/classes/appleseed.php'); 
  require_once ('legacy/code/include/classes/comments.php'); 
  require_once ('legacy/code/include/classes/groups.php'); 
  require_once ('legacy/code/include/classes/privacy.php'); 
  require_once ('legacy/code/include/classes/friends.php'); 
  require_once ('legacy/code/include/classes/messages.php'); 
  require_once ('legacy/code/include/classes/users.php'); 
  require_once ('legacy/code/include/classes/auth.php'); 
  require_once ('legacy/code/include/classes/search.php'); 
  
  // Create the Application class.
  $zOLDAPPLE = new cOLDAPPLESEED ();

  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("content.groups", TRUE);

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE, '/content/groups/');

  // Create the Comments class.
  global $zGROUPS;
  $zGROUPS = new cGROUPINFORMATION ("content.groups");

  global $bMAINSECTION;

  // Initialize the groups subsystem.
  $zGROUPS->Initialize ();

  // Determine which action to take.
  switch ($gACTION) {
    case 'SAVE':
    case 'SUBMIT':
     $zGROUPS->Synchronize ();
     $zGROUPS->userAuth_uID = $zLOCALUSER->uID;
     $zGROUPS->Stamp = SQL_NOW;
     $zGROUPS->Sanity();
     if (!$zGROUPS->Error) {
       $zGROUPS->Add ();

       $zGROUPS->tID = $zGROUPS->AutoIncremented();

       // Add creating user to the members list.
       $zGROUPS->Join ($zGROUPS->Name, $gSITEDOMAIN, GROUP_VERIFICATION_APPROVED);

       // Create the link to the group.
       global $gGROUPLINK;
       $gGROUPLINK = "http://" . $gSITEDOMAIN . "/group/" . $zGROUPS->Name . "/";

       $gGROUPSECTION = 'created';
     } else {
     } // if
    break;
    case 'SEARCH':
    break;
    default:
    break;
  } // switch

  global $gGROUPSTAB;
  // If user is anonymous only show 'search' tab.
  if ( ($zAUTHUSER->Anonymous) ) { 
    $gGROUPSTAB = 'main';
  } else {
    if ($zAUTHUSER->Remote) {
      // If user is remote, show remote tab.
      $gGROUPSTAB = 'remote';
    } else {
      // If user is local and logged in, show focus tab.
      $gGROUPSTAB = 'focus';
    } // if
  } // if

  // Determine which section we're viewing.
  global $gCREATETAB, $gSEARCHTAB;
  $gCREATETAB = "_off"; $gSEARCHTAB = "_off";
  switch ($gGROUPSECTION) {
    case 'create':
      if ( ($zAUTHUSER->Anonymous) or ($zAUTHUSER->Remote) ) { 
        // Section unavailable to anonymous or remote users.
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if
      $section = 'create';
      $gCREATETAB = ""; 
    break;
    case 'created':
      $section = 'created';
      $gCREATETAB = ""; 
    break;
    case 'search':
    default:
      $section = 'search';
      $gSEARCHTAB = ""; 
    break;
  } // switch

  $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/groups/$section.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/content/groups.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zOLDAPPLE->End ();

?>
