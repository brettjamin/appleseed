<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: options.php                             CREATED: 02-11-2005 + 
  // | LOCATION: /code/user/                       MODIFIED: 07-19-2005 +
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
  // | DESCRIPTION:  Displays options section of user profile page.      |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+
  
  // Check if user is admin.
  if ($gFOCUSUSERID != $zAUTHUSER->uID) {
    // Error out if user does not have access privileges.
    if ($zLOCALUSER->userAccess->a == FALSE) {
      $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
      $zOLDAPPLE->End();
    } // if
  } // if

  // Create a warning message if user has no write access.
  if ( ($zAUTHUSER->uID != $gFOCUSUSERID) and
       ($zLOCALUSER->userAccess->a == TRUE) and
       ($zLOCALUSER->userAccess->w == FALSE) ) {
    $zLOCALUSER->Message = __("Write Access Denied");
    $zLOCALUSER->Error = 0;
  } // if

  // Set which tab to highlight.
  $gUSEROPTIONSTAB = '';
  $this->SetTag ('USEROPTIONSTAB', $gUSEROPTIONSTAB);

  global $gOPTION;

  // Default to hidden.
  global $gOPTIONSECURITY, $gOPTIONCONFIG, $gOPTIONPHOTO, $gOPTIONICONS;
  global $gOPTIONGENERAL, $gOPTIONQUESTIONS, $gOPTIONEMAILS, $gOPTIONJOURNAL;
  global $gOPTIONPHOTOS, $gOPTIONMESSAGES, $gOPTIONFRIENDS, $gOPTIONGROUPS;

  $gOPTIONSECURITY = 'off'; $gOPTIONCONFIG = 'off'; 
  $gOPTIONPHOTO = 'off'; $gOPTIONICONS = 'off'; 
  $gOPTIONGENERAL = 'off'; $gOPTIONQUESTIONS = 'off'; 
  $gOPTIONEMAILS = 'off'; $gOPTIONJOURNAL = 'off'; 
  $gOPTIONPHOTOS = 'off'; $gOPTIONMESSAGES = 'off'; 
  $gOPTIONFRIENDS = 'off'; $gOPTIONGROUPS = 'off';

  // Create the image class instances.
  global $zPHOTO, $zICON; 
  $zPHOTO = new cIMAGE ("USER.OPTIONS.ICONS");
  $zICON = new cIMAGE ("USER.OPTIONS.ICONS");
 
  global $gSECTIONDEFAULT;

  if ($gSECTION) {
    $on = "gOPTION" . $gSECTION;
    $$on = 'on';
    $gSECTIONDEFAULT = $gSECTION;
  } else {
    $gOPTIONGENERAL = 'on';
    $gSECTIONDEFAULT = 'GENERAL';
  } // if

  // Split up the birthday into manageable parts.
  global $gBIRTHDAYLIST;
  $gBIRTHDAYLIST = $zHTML->SplitDate ($zFOCUSUSER->userProfile->Birthday);

  // Pre-Load the User Theme List.
  global $gTHEMELIST;
  $gTHEMELIST = $zOLDAPPLE->GetThemeList ();
  
  // Determine which action to take.
  switch ($gACTION) {
    case 'SUBMIT':
    case 'SAVE':

      // Check if admin user has proper access.
      if ( ($zAUTHUSER->uID != $gFOCUSUSERID) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zLOCALUSER->Message = __("Write Access Denied");
        $zLOCALUSER->Error = -1;
        break;
      } // if

      switch ($gSECTION) {
        case 'QUESTIONS':
          $zFOCUSUSER->SaveQuestions ();
        break;

        case 'GENERAL':
          $zFOCUSUSER->SaveGeneral ();
        break;

        // Save the user's icons.
        case 'ICONS':
          $zFOCUSUSER->SaveIcons ();
        break;

        // Save the user's profile photo.
        case 'PHOTO':
          $zFOCUSUSER->SavePhoto ();
        break;

        case 'CONFIG':
          $zFOCUSUSER->SaveConfig ();
        break;

        case 'EMAILS':
          $zFOCUSUSER->SaveEmails();
        break;

        default:
        break;
      } // switch
    break;

    case 'DELETE_ICON': 

      // Check if admin user has proper access.
      if ( ($zAUTHUSER->uID != $gFOCUSUSERID) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zLOCALUSER->Message = __("Write Access Denied");
        $zLOCALUSER->Error = -1;
        break;
      } // if

      $zFOCUSUSER->DeleteIcon ();
    break;

    case 'SAVE_ALL':

      // Check if admin user has proper access.
      if ( ($zAUTHUSER->uID != $gFOCUSUSERID) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zLOCALUSER->Message = __("Write Access Denied");
        $zLOCALUSER->Error = -1;
        break;
      } // if

      $zFOCUSUSER->SavePhoto ();
      $zFOCUSUSER->SaveQuestions ();
      $zFOCUSUSER->SaveGeneral ();
      $zFOCUSUSER->SaveIcons ();
      // NOTE: Not working for SAVE_ALL.
      $zFOCUSUSER->SaveConfig ();
      $zFOCUSUSER->SaveEmails();
      $zICON->Message = NULL;
      $zPHOTO->Message = NULL;
      $zFOCUSUSER->Message = __("All options saved");
    break;

    default:
    break;
  } // switch

  // Buffer the profile questions list.
  ob_start ();  

  // Load the configurable questions
  $QUESTIONSLIST = new cUSERQUESTIONS ();

  $criterialist = array ("Language" => "en",
                         "Visible"  => TRUE );   
  $QUESTIONSLIST->SelectByMultiple ($criterialist);

  global $gFOCUSQUESTION;
  
  echo "<table>";
  echo "<tbody>";

  $QuestionCount = 0;
  while ($QUESTIONSLIST->FetchArray ()) {
    $gFOCUSQUESTION = $QUESTIONSLIST->FullQuestion;
    echo "<tr>";
    echo "<th><label id='question" . $QUESTIONSLIST->tID . "'>\n";
    echo "$gFOCUSQUESTION";
    echo "</label></th>\n";

    // Select the user answer.
    $questionid = $QUESTIONSLIST->tID;
    $answercriteria = array ("userAuth_uID"      => $zFOCUSUSER->uID,
                             "userQuestions_tID" => $questionid); 
    $zFOCUSUSER->userAnswers->SelectByMultiple ($answercriteria);
    $zFOCUSUSER->userAnswers->FetchArray ();

    // Determine whether to load from database or post data.
    if ($gANSWERS) {
      $selected = $gANSWERS[$QuestionCount];
    } else {
      $selected = $zFOCUSUSER->userAnswers->Answer;
    } // if

    echo "<td>";
    
    // Determine which kind of input is needed.
    switch ($QUESTIONSLIST->TypeOf) {

      case QUESTION_MENU:
       $zOPTIONS->Menu ($QUESTIONSLIST->Concern, $selected, "gANSWERS[]");
      break;

      case QUESTION_CHECKLIST:
       $referencename = 'g' . $QUESTIONSLIST->Concern;
       $selected = explode (",", $zFOCUSUSER->userAnswers->Answer);
       $zOPTIONS->Checklist ($QUESTIONSLIST->Concern, $selected, $QUESTIONSLIST->Concern);
       $QuestionCount--;
      break;

      case QUESTION_STRING:
       $zHTML->TextBox ("gANSWERS[]", 64, $selected);
      break;

      case QUESTION_WEBLINK:
       $zHTML->TextBox ("gANSWERS[]", 64, $selected);
      break;

      case QUESTION_LINKED:
       $zHTML->TextArea ("gANSWERS[]", $selected);
      break;
    } // switch
    
    echo "</td>";
    echo "</tr>";

    $QuestionCount++;
  } // while
  
  echo "</tbody>";
  echo "</table>";

  unset ($QUESTIONSLIST);

  // Retrieve output buffer.
  global $bFULLQUESTIONS;
  $bFULLQUESTIONS = ob_get_clean (); 
  
  // End buffering.
  ob_end_clean (); 

  // Create the icon list buffer.
  $zFOCUSUSER->userIcons->Select ("userAuth_uID", $zFOCUSUSER->uID);
  $gICONCOUNT = $zFOCUSUSER->userIcons->CountResult();

  $zICONLIST = new cIMAGE ("USER.OPTIONS.ICONS");
  global $bICONLIST;

  if ($gICONCOUNT == 0) {

    // Start buffering.
    ob_start ();

    // Look up the error message.
    $zICONLIST->Message = __( "No Icons Founds" );
    $zICONLIST->Error = 0;
    $zICONLIST->Broadcast ("member");
    $zICONLIST->Message = "";

    // Retrieve the buffer.
    $bICONLIST = ob_get_clean (); 
  } else {
   
    // Start buffering.
    ob_start ();

    global $gICONLOCATION;
    global $gNEWUPLOAD;
    global $gPOSTDATA;

    // Loop through and list icons.
    while ($zFOCUSUSER->userIcons->FetchArray ()) {
      if ($zFOCUSUSER->userIcons->Filename == NO_ICON) {
        $gICONLOCATION = $gTHEMELOCATION . "/images/icons/noicon.gif";
      } else {
        $gICONLOCATION = "/_storage/legacy/photos/" . $zFOCUSUSER->Username . "/icons/" . $zFOCUSUSER->userIcons->Filename;
      } // if

      $gPOSTDATA['tID'] = $zFOCUSUSER->userIcons->tID; 
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/options/icons.aobj", INCLUDE_SECURITY_NONE);
    } // while

    unset ($gPOSTDATA['tID']); 
    unset ($gICONLOCATION);

    // Check to see if too many icons are already loaded.
    if ($gICONCOUNT >= $gMAXICONS) {
      // Look up the error message.
      $zICONLIST->Message = __( "Icon Limit Reached", array ( "max" => $gMAXICONS ) );
      $zICONLIST->Error = -1;
      $zICONLIST->Broadcast ("member");
      $zICONLIST->Message = "";
      $gNEWUPLOAD = DISABLED;

    } // if

    // Retrieve the buffer.
    $bICONLIST = ob_get_clean (); 
  } // if

  unset ($zICONLIST);

  // Load the profile questions and answers into a buffer.
  $zOLDAPPLE->Profile ();
  
  // Check if a user is logged in.
  if ((!$zAUTHUSER->Anonymous) && ($zLOCALUSER->Username != $zFOCUSUSER->Username)) {
    // Buffer the contact box.
    $bCONTACTBOX = $zOLDAPPLE->BufferContactBox ();
  } // if

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/options.afrw", INCLUDE_SECURITY_NONE);

?>
