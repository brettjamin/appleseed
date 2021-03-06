<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: groups.php                              CREATED: 09-05-2005 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 09-05-2005 +
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
  // | DESCRIPTION.  Group class definitions.                            |
  // +-------------------------------------------------------------------+

  // Group content class.
  class cGROUPINFORMATION extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID;
    
    // Variables
    var $Name, $Description, $Stamp, $Access, $Tags;

    // Sub-classes
    var $groupContent, $groupMembers;

    function cGROUPINFORMATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'groupInformation';
      $this->tID = '';
      $this->Context = '';
      $this->userAuth_uID = '';
      $this->Name = '';
      $this->Description = '';
      $this->Stamp = '';
      $this->Access = '';
      $this->Tags = '';
      $this->Error = 0;
      $this->Message = '';
      $this->PageContext = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Name'           => array ('max'        => '32',
                                   'min'        => '6',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

    'Fullname'           => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

 'Description'           => array ('max'        => '4096',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),


        'Access'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Tags'           => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();

      // Create sub-classes.
      $this->groupContent = new cGROUPCONTENT ($pDEFAULTCONTEXT);
      $this->groupMembers = new cGROUPMEMBERS ($pDEFAULTCONTEXT);
 
    } // Constructor

    // Initialize the group posting subsystem.
    function Initialize () {
      global $gCONTENTGROUPSINFOTAB;
      global $gCONTENTGROUPSTAB;
      global $gCONTENTGROUPSMEMBERSTAB;
      global $gCONTENTGROUPSOPTIONSTAB;

      global $zAUTHUSER, $zLOCALUSER, $zOLDAPPLE;

      global $gGROUPVIEW, $gGROUPVIEWTYPE, $gGROUPVIEWADMIN; 
      global $gPOSTDATA;
      global $gFOCUSUSERNAME;
      global $gSITEDOMAIN;
      global $gSELECTBUTTON;
      global $gSCROLLSTEP, $gSCROLLSTART, $gSCROLLMAX;
      global $gCONTINUEFLAG;

      $gCONTENTGROUPSINFOTAB = "_off";
      $gCONTENTGROUPSTAB = "_off";
      $gCONTENTGROUPSMEMBERSTAB = "_off";
      $gCONTENTGROUPSOPTIONSTAB = "_off";

      $gCONTINUEFLAG = FALSE;

      $this->groupContent->Context = $zOLDAPPLE->Context;
      
      $gSCROLLSTEP[$this->groupContent->Context] = 20;
      
      $gGROUPVIEWTYPE = "GROUPVIEW";

      // Display the select all button by default.
      $gSELECTBUTTON = 'Select All';

      // Depracate the view.
      if ($gGROUPVIEWADMIN != "") {
        $gGROUPVIEW = $gGROUPVIEWADMIN;
      } // if
      $gGROUPVIEWADMIN = $gGROUPVIEW;
      $gPOSTDATA['GROUPVIEW'] = $gGROUPVIEW;

      // Check if user is admin or is viewing their own page.
      if ( ($gFOCUSUSERNAME == $zAUTHUSER->Username) and
           ($gSITEDOMAIN == $zAUTHUSER->Domain) ) {
        $gGROUPVIEWTYPE = "GROUPVIEWADMIN";
      } else {
        if ($zLOCALUSER->userAccess->e == TRUE) {
          $gGROUPVIEWTYPE = "GROUPVIEWADMIN";
       } // if
      } // if

      // Determine which mode we are viewing in, and set flags accordingly.
      $this->Flags ();
    } // Initialize

    function Save () {

      global $gPARENTID;
      global $gUSERICON;
      global $zAUTHUSER, $zFOCUSUSER;
      global $gGROUPVIEW;
      global $gSITEDOMAIN;

      global $zOLDAPPLE;
      global $HTTP_SERVER_VARS;

      $this->groupContent->Synchronize ();

      $this->groupContent->userAuth_uID = $this->userAuth_uID;
      $this->groupContent->Views = SQL_SKIP;

      if ($gPARENTID) {
        $this->groupContent->parent_tID = $gPARENTID;
      } else {
        $this->groupContent->parent_tID = 0;
      } // if

      if ($zAUTHUSER->Anonymous) {
        // Anonymous User.
        $this->groupContent->Owner_Icon = NO_ICON;
        $this->groupContent->Owner_Username = ANONYMOUS; 
        $this->groupContent->Owner_Domain = $gSITEDOMAIN;
        $this->groupContent->Owner_Address = $HTTP_SERVER_VARS['REMOTE_ADDR'];
      } else {
        // Logged In User.
        $this->groupContent->Owner_Icon = $gUSERICON;
        $this->groupContent->Owner_Username = $zAUTHUSER->Username;
        $this->groupContent->Owner_Domain = $zAUTHUSER->Domain;
        $this->groupContent->Owner_Address = $HTTP_SERVER_VARS['REMOTE_ADDR'];
      } // if
      $this->groupContent->groupInformation_tID = $this->tID;
      $this->groupContent->Stamp = SQL_NOW;

      $this->groupContent->Sanity ();

      if ($this->groupContent->Error == 0) {
        
        $this->groupContent->Add ();

        $tid = $this->groupContent->AutoIncremented ();
        $this->groupContent->Select ("tID", $tid);
        $this->groupContent->FetchArray ();
        $this->SetLatest ($tid, $gPARENTID);

        // Send a notification to the parent we're replying to.
        if ($this->groupContent->parent_tID !== 0) {
          // Select the parent information
          $this->groupContent->Select ("tID", $this->groupContent->parent_tID);
          $this->groupContent->FetchArray();

          // Make sure we haven't already sent a notification.
          // Make sure we're not replying to our own post.
          if ( ($this->groupContent->Owner_Username != $zAUTHUSER->Username) or
               ($this->groupContent->Owner_Domain != $gSITEDOMAIN) ) {

            // Don't bother with a reply to an anonymous post.
            if ($this->groupContent->Owner_Username != ANONYMOUS) {
              $FRIEND = new cFRIENDINFORMATION();
              $FRIEND->Username = $this->groupContent->Owner_Username;
              $FRIEND->Domain = $this->groupContent->Owner_Domain;
              list ($fullname, $online, $email) = $FRIEND->GetUserInformation();

              // Disabled. 04-27-2007.
              // $this->NotifyReply ($email, $fullname, $this->groupContent->Owner_Username, $zAUTHUSER->Fullname);

              unset ($FRIEND);
            } // if
            
          } // if
        } // if

      } // if

      $gACTION = "";

    } // Save

    function AddForm () {

      global $zOLDAPPLE;

      global $gFRAMELOCATION;

      global $gREADDATA, $gADDDATA;

      global $gGROUPVIEWFLAG;
      global $gTARGET;
      global $zLOCALUSER;

      global $gREPLYDATA;
      global $gPARENTID;
      global $gSUBJECT;

      global $zHTML;

      $gTARGET = $_SERVER[REQUEST_URI];

      $replyfile = "new";

      if ($gPARENTID) {

        $replyfile = "reply";
        
        global $gPARENTAUTHOR, $gPARENTBODY, $gPARENTSUBJECT;
        global $zHTML;
        global $zPARENT;

        $gREPLYDATA = array ("ACTION" => "ADD",
                             "PARENTID" => $gPARENTID);

        $zPARENT = new cGROUPCONTENT ($this->groupContent->PageContext);
        $zPARENT->Select ("tID", $gPARENTID);
        $zPARENT->FetchArray ();
        $gPARENTBODY = $zOLDAPPLE->Format ($zPARENT->Body, FORMAT_BASIC);
        $gPARENTSUBJECT = $zPARENT->Subject;

        // Check if the subject field has been modified by user.
        if (!$gSUBJECT) {
         // Check to see if we haven't already added the prefix.
         if (strpos ($gPARENTSUBJECT, __("Re"), 0) === 0) {
           // Just inherit the parent post subject.
           $gSUBJECT = $gPARENTSUBJECT;
         } else {
           // Add the prefix to the subject.
           $gSUBJECT = __("Re") . $gPARENTSUBJECT;
         } // if
        } // if

        $gPARENTAUTHOR = $zHTML->CreateUserLink ($zPARENT->Owner_Username, $zPARENT->Owner_Domain);
      } else {
        $gREPLYDATA = array ("ACTION" => "ADD",
                             "PARENTID" => 0);

        $replyfile = "new";
      } // if

      global $zAUTHUSER;
      // Create the icons list.
      if (!$zAUTHUSER->Remote) {
        $zLOCALUSER->userIcons->BuildIconMenu ($zLOCALUSER->uID);
      } else {
        $zAUTHUSER->BuildIconMenu ();
      } // if

      $result = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/$gGROUPVIEWFLAG/$replyfile.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($result);

    } // AddForm

    function Top () {

      global $gCONTENTGROUPSINFOTAB;
      global $gCONTENTGROUPSTAB;
      global $gCONTENTGROUPSMEMBERSTAB;
      global $gCONTENTGROUPSOPTIONSTAB;

      global $gFRAMELOCATION;
      global $gGROUPVIEWFLAG;

      global $gREADDATA, $gADDDATA, $gPOSTDATA;

      global $gSCROLLSTART;

      global $gTARGET;

      global $zOLDAPPLE;

      $gPOSTDATA["SCROLLSTART[" . $this->groupContent->Context . "]"] = $gSCROLLSTART[$this->groupContent->Context];

      $gTARGET = $_SERVER[REQUEST_URI];

      $result = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/$gGROUPVIEWFLAG/main.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($result);

    } // Top

    function Listing ($pPARENTID, $pTHREADID = NULL) {

      global $zOLDAPPLE;

      global $zHTML;

      global $zAUTHUSER;
      global $zLOCALUSER, $zFOCUSUSER;

      global $gFRAMELOCATION;

      global $gGROUPVIEW, $gGROUPVIEWFLAG;

      global $gPOSTDATA;

      global $gREPLYDATA;

      global $gREPLYLABEL, $gPARENTLABEL, $gTHREADLABEL, $gDELETELABEL;

      global $bDELETEBUTTON, $bADDRESS;

      global $gCONTINUEFLAG;

      global $gSCROLLSTEP, $gSCROLLSTART, $gSCROLLMAX;
      global $gSCROLLCOUNT;

      $gREPLYLABEL = __("Reply");

      $gPARENTLABEL = __("Parent");

      $gTHREADLABEL = __("Thread");

      $gDELETELABEL = __("Delete");

      $returnbuffer = "";

      if ($pTHREADID) {
        $criteria = array ("tID"                  => $pTHREADID,
                           "groupInformation_tID" => $this->tID);
      } else {
        $criteria = array ("parent_tID"           => $pPARENTID,
                           "groupInformation_tID" => $this->tID);
      } // if

      $gSORT = "Stamp ASC";

      $this->groupContent->SelectByMultiple ($criteria, $gSORT);

      // Count the maximum number of posts attached.

      $context = $this->groupContent->Context;
      $gSCROLLMAX[$context] = $this->CountPosts ();

      // Check if no posts have been found.
      if ( ($this->groupContent->CountResult () == 0) and ($pPARENTID == 0) ) {

        ob_start ();
        
        $this->groupContent->Message = __("No Results Found");
        $this->groupContent->Broadcast();

        $returnbuffer = ob_get_clean ();

        return ($returnbuffer);
      } // if

      $start = $gSCROLLSTART[$this->groupContent->Context];
      $max = $gSCROLLSTART[$this->groupContent->Context] + $gSCROLLSTEP[$this->groupContent->Context];

      while ($this->groupContent->FetchArray ()) {

        $gSCROLLCOUNT[$this->groupContent->Context]++;

        $current = $gSCROLLCOUNT[$this->groupContent->Context];
        // We've hit the max number of posts for a page.
        if ($current > $max) {
          return ($returnbuffer);
        } // if

        // See if we're listing a parent or child post.
        $nestflag = "first";
        if ($pPARENTID != 0) {
          $nestflag = "inner";
        }

        // Check if the post has been deleted.
        $deletedflag = NULL;
        if ($this->groupContent->Body == DELETED_GROUP_ENTRY) $deletedflag = "deleted.";

        global $gTARGET;

        global $gTHEMELOCATION;

        global $gSUBJECT, $gBODY, $gDATE, $gTIME;
        global $gAUTHOR, $gTHREAD, $gBYLINE;
        global $gADDRESS;
        global $gICON, $gICONX, $gICONY;
        global $gLINK, $gSTAMP;
        global $gCHECKED, $gACTION;
        
        global $gPARENTDATA, $gTHREADDATA;

        global $bICON, $bONLINENOW;

        $gCHECKED = FALSE;
        // Select 
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        // Generic
        $gSUBJECT = $this->groupContent->Subject;
        $gBODY = $zOLDAPPLE->Format ($this->groupContent->Body, FORMAT_BASIC);
        
        $gAUTHOR = $zHTML->CreateUserLink ($this->groupContent->Owner_Username, $this->groupContent->Owner_Domain, FALSE);
        $gADDRESS = $this->groupContent->Owner_Address;
        global $gAUTHORFULLNAME;
        
        $bONLINENOW = OUTPUT_NBSP;

        // If user activity in the last 3 minutes, consider them online.
        list ($gAUTHORFULLNAME, $online) = $zOLDAPPLE->GetUserInformation($this->groupContent->Owner_Username, $this->groupContent->Owner_Domain);
        if ($online) {
          $bONLINENOW = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/onlinenow.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
        } // if

        $bICON = $zOLDAPPLE->BufferUserIcon ($this->groupContent->Owner_Username, $this->groupContent->Owner_Domain, $this->groupContent->Owner_Icon);

        $gREPLYDATA = $gPOSTDATA;
        $gREPLYDATA["ACTION"] = "ADD";
        $gREPLYDATA["PARENTID"] = $this->groupContent->tID;

        $gTHREADDATA = $gPOSTDATA;
        $gTHREADDATA["STARTINGID"] = $this->groupContent->tID;
        unset ($gTHREADDATA["SCROLLSTART[" . $this->groupContent->Context . "]"]);

        $gPARENTDATA = $gPOSTDATA;
        $gPARENTDATA["STARTINGID"]  = $this->groupContent->parent_tID;
        unset ($gPARENTDATA["SCROLLSTART[" . $this->groupContent->Context . "]"]);

        $stamp = strtotime ($this->groupContent->Stamp);
        $gDATE = date ("M j, Y", $stamp);
        $gTIME = date ("g:i a", $stamp);
        $gSTAMP = __("Group Stamp", array ( "date" => $gDATE, "time" => $gTIME ) );

        // Threaded -specific
        global $gGROUPREQUEST;
        $threadtarget = "/group/" . $gGROUPREQUEST . "/thread/" . $this->groupContent->tID;
        $gLINK = $zHTML->CreateLink ($threadtarget, $this->groupContent->Subject, $gTHREADDATA);
        $gTHREAD = __("Group Thread", array ( "link" => $gLINK, "author" => $gAUTHOR, "date" => $gDATE, "time" => $gTIME ) );

        // Compact -specific
        $gBYLINE = __("By", array ( "author" => $gAUTHOR ) );

        /* */

        //Count how many children this post has.
        $INFO = new cGROUPCONTENT ();
        $targetcriteria = array ("parent_tID"   => $this->groupContent->tID,
                                 "groupInformation_tID"          => $this->tID);
        $INFO->SelectByMultiple ($targetcriteria);
        $countchildren = $INFO->CountResult ();
        unset ($INFO);

        $bDELETEBUTTON = OUTPUT_NBSP;
        $bADDRESS = OUTPUT_NBSP;
        if ($this->CheckGroupAccess () ) {

          global $gPOSTDATA;
          $gPOSTDATA['tID'] = $this->groupContent->tID;
          $gPOSTDATA['ACTION'] = "DELETE";

          global $gTARGETID; 
          $gTARGETID = "";
          if ($countchildren > 0) $gTARGETID = $this->groupContent->tID;

          $bDELETEBUTTON = $zHTML->CreateButton ('group_delete', __("Confirm Delete"), ENABLED, "DELETE", "ACTION");
          $bADDRESS = "(" . $gADDRESS . ")";

          unset ($gPOSTDATA['tID']);
          unset ($gPOSTDATA['ACTION']);

        } // if

        global $gTARGET, $gSITEDOMAIN, $gGROUPREQUEST;

        // Store old target.
        $oldtarget = $gTARGET;
        
        global $bPARENTBUTTON;
        $bPARENTBUTTON = NULL;
        $gTARGET = 'http://' . $gSITEDOMAIN . '/group/' . $gGROUPREQUEST . '/thread/' . $this->groupContent->parent_tID;
        if ($this->groupContent->parent_tID != 0) $bPARENTBUTTON = $zHTML->CreateButton ('group_parent', NULL, ENABLED, NULL, "ACTION");

        global $bTHREADBUTTON;
        $bTHREADBUTTON = NULL;
        $gTARGET = 'http://' . $gSITEDOMAIN . '/group/' . $gGROUPREQUEST . '/thread/' . $this->groupContent->tID;
        if ($countchildren > 0) $bTHREADBUTTON = $zHTML->CreateButton ('group_thread', NULL, ENABLED, NULL, "ACTION");

        // Restore old target.
        $gTARGET = $oldtarget;

        global $gID;
        $gID = $this->groupContent->tID;

        if ($current > $start) {
          $returnbuffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/$gGROUPVIEWFLAG/$deletedflag$nestflag.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

          $RECURSE = new cGROUPINFORMATION ($zOLDAPPLE->Context);
          $RECURSE->tID = $this->tID;
          $RECURSE->groupContent->Context = $this->groupContent->Context;

          $returnbuffer .= $RECURSE->Listing ($this->groupContent->tID);

          $returnbuffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/$gGROUPVIEWFLAG/$deletedflag$nestflag.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } else {
          $RECURSE = new cGROUPINFORMATION ($zOLDAPPLE->Context);
          $RECURSE->tID = $this->tID;
          $RECURSE->groupContent->Context = $this->groupContent->Context;

          $returnbuffer .= $RECURSE->Listing ($this->groupContent->tID);
        } // if

      } // while

      return ($returnbuffer);

    } // Listing

    function TopicsListing () {
      global $zOLDAPPLE, $zHTML;

      global $gFRAMELOCATION, $gTARGET, $gSITEDOMAIN, $gGROUPREQUEST;

      global $gSUBJECT, $gLATEST, $gSTARTED, $gCOUNT;

      $return = NULL;

      $return = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/topics/main.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $topiccriteria = array ("groupInformation_tID"  => $this->tID,
                              "parent_tID"            => 0);

      $this->groupContent->SelectByMultiple ($topiccriteria, "latest_tID DESC, tID DESC"); 

      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/topics/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      while ($this->groupContent->FetchArray ()) {
        $gTARGET = 'http://' . $gSITEDOMAIN . '/group/' . $gGROUPREQUEST . '/thread/' . $this->groupContent->tID;
        $gSUBJECT = $zHTML->CreateLink ($gTARGET, $this->groupContent->Subject);
        $gSTARTED = $zHTML->CreateUserLink ($this->groupContent->Owner_Username, $this->groupContent->Owner_Domain);
        $gCOUNT = $this->CountPosts ($this->groupContent->tID);
        global $gLATESTDATE, $gLATESTID;
        $gLATESTDATE = NULL; $gLATESTID = NULL;
        list ($latest_owner, $latest_domain) = $this->FindLatest ($this->groupContent->tID); 
        $gLATEST = $zHTML->CreateUserLink ($latest_owner, $latest_domain);

        $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/topics/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // while
      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/topics/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $gTARGET = 'http://' . $gSITEDOMAIN . '/group/' . $gGROUPREQUEST . '/';
      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/topics/main.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($return);
    } // TopicsListing

    function CheckGroupAccess  () {
      global $zAUTHUSER, $zLOCALUSER, $zFOCUSUSER;
      // if Group Ownership or Editor or Context Ownership 
      if ( ( ($zAUTHUSER->Username == $this->groupContent->Owner_Username) and 
             ($zAUTHUSER->Domain == $this->groupContent->Owner_Domain) ) or 
           ($zLOCALUSER->userAccess->e == TRUE) or
           ( ($zLOCALUSER->uID == $zFOCUSUSER->uID) and 
             ($zLOCALUSER->Username) ) ) {
        return (TRUE);
      } // if

      return (FALSE);
    } // CheckGroupAccess 

    function Bottom () {

      global $gFRAMELOCATION;
      global $gGROUPVIEWFLAG;

      global $zOLDAPPLE;

      $result = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/$gGROUPVIEWFLAG/main.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($result);

    } // Bottom

    // Determine which mode we are viewing in, and set flags accordingly.
    function Flags () {
      global $gGROUPVIEWFLAG;

      global $gGROUPVIEW;

      global $zAUTHUSER, $zLOCALUSER;
 
      global $gFOCUSUSERNAME;

      switch ($gGROUPVIEW) {
        case GROUP_VIEW_EDITOR:
    
          // Set the default view.
          $gGROUPVIEWFLAG = "nested";

          // Check if user is admin or is viewing their own page.
          if ( ($gFOCUSUSERNAME == $zAUTHUSER->Username) and
               ($gSITEDOMAIN == $zAUTHUSER->Domain) ) {
            $gGROUPVIEWFLAG = "editor";
          } else {
            if ($zLOCALUSER->userAccess->a == TRUE) {
              $gGROUPVIEWFLAG = "editor";
            } // if
          } // if

        break;

        case GROUP_VIEW_DEFAULT:
          $gGROUPVIEWFLAG = "nested";
        break;

        case GROUP_VIEW_NESTED:
          $gGROUPVIEWFLAG = "nested";
        break; 

        case GROUP_VIEW_THREADED:
          $gGROUPVIEWFLAG = "threaded";
        break; 

        case GROUP_VIEW_FLAT:
          $gGROUPVIEWFLAG = "flat";
        break; 

        case GROUP_VIEW_COMPACT:
          $gGROUPVIEWFLAG = "compact";
        break;

        default:
          $gGROUPVIEWFLAG = 'nested';
          $gGROUPVIEW = GROUP_VIEW_NESTED;
        break;

      } // switch
    } // Flags

    // Handle the workflow for the posts box.
    function Handle () {
      global $zOLDAPPLE;

      global $bMAINSECTION;

      global $gACTION;
      global $gADDTAB, $gREADTAB;
      global $gSELECTBUTTON;
      global $gMASSLIST;
      global $gSTARTINGID;

      $bMAINSECTION = "";

      global $gREADDATA, $gADDDATA;
   
      $gADDDATA   = array ("ACTION" => "ADD");
      $gREADDATA  = array ("ACTION" => "READ");

      // Change the select button if anything is eelected.
      if ($zOLDAPPLE->ArrayIsSet ($gMASSLIST) ) $gSELECTBUTTON = 'Select None';

      // PART I: Take Action
      switch ($gACTION) {
        case 'SUBMIT':
          $this->Save ();
          if ($this->groupContent->Error == 0) {
            $gACTION = "";
          } else {
            $gACTION = "START_TOPIC";
          } // if
        break;

        case 'DELETE':
          global $gtID;
          if ($gtID) {
            $this->groupContent->Select ("parent_tID", $gtID);
            $this->groupContent->FetchArray ();

            if ($this->groupContent->CountResult () == 0) {
              // No child posts.  Delete.
              $this->groupContent->Synchronize ();
              $this->groupContent->Delete ();
            } else {
              // Child posts exist.  Create placemark.
              $this->groupContent->Select ("tID", $gtID);
              $this->groupContent->FetchArray ();
              $this->groupContent->Subject = NULL;
              $this->groupContent->Owner_Icon = NULL;
              $this->groupContent->Owner_Username = NULL;
              $this->groupContent->Owner_Domain = NULL;
              $this->groupContent->Body = DELETED_GROUP_ENTRY;
              $this->groupContent->Update ();
            } // if
            $this->CleanUp ();
          } // if
        break;

        case 'DELETE_ALL':
          // Check if any items were selected.
          if (!$gMASSLIST) {
            $this->groupContent->Message = __("None Selected");
            $this->groupContent->Error = -1;
            break;
          } // if

          foreach ($gMASSLIST as $count => $tid) {
            $this->groupContent->Select ("parent_tID", $tid);
            $this->groupContent->FetchArray ();

            if ($this->groupContent->CountResult () == 0) {
              // No child posts.  Delete.
              $this->groupContent->tID = $tid;
              $this->groupContent->Delete ();
            } else {
              // Child posts exist.  Create placemark.
              $this->groupContent->Select ("tID", $tid);
              $this->groupContent->FetchArray ();
              $this->groupContent->Subject = NULL;
              $this->groupContent->Owner_Icon = NULL;
              $this->groupContent->Owner_Username = NULL;
              $this->groupContent->Owner_Domain = NULL;
              $this->groupContent->Body = DELETED_GROUP_ENTRY;
              $this->groupContent->Update ();
            } // if
            $this->CleanUp ();
          } // if
          $gMASSLIST = array ();
          $gSELECTBUTTON = 'Select All';
        break;

        case 'SELECT_ALL':
          $gSELECTBUTTON = 'Select None';
        break;

        case 'SELECT_NONE':
          $gMASSLIST = array ();
          $gSELECTBUTTON = 'Select All';
        break;

      } // switch
    
      // PART II: Parse the HTML.
      switch ($gACTION) {
        case 'EDIT':
          $bMAINSECTION = "";
        break;
    
        case 'ADD':
        case 'START_TOPIC':
          $gADDTAB = "";
          $bMAINSECTION .= $this->AddForm ();
        break;
    
        default:
          global $gGROUPSECTION; 
          switch ($gGROUPSECTION) {
           case 'thread':
           default:
            $gREADTAB = "";
            $start = 0;
            if ($gSTARTINGID) {
              $bMAINSECTION .= $this->Top ();
              $bMAINSECTION .= $this->Listing (0, $gSTARTINGID);
              $bMAINSECTION .= $this->Bottom ();
            } else {
              $bMAINSECTION = $this->TopicsListing ();
            } // if
          break;
          } // switch 
        break;
      } // switch
    } // Handle

    // Count the posts attached to certain Context/Reference ID
    function CountPosts ($pPARENTID, $pTOTAL = 0) {

      $COUNT = new cGROUPCONTENT ();

      $criteria = array ("groupInformation_tID"  => $this->tID,
                         "parent_tID"            => $pPARENTID);

      $COUNT->SelectByMultiple ($criteria);

      $total = $COUNT->CountResult ();

      while ($COUNT->FetchArray ()) {
        $total += $this->CountPosts ($COUNT->tID, $total);
      } // while

      unset ($COUNT);

      return ($total);

    } // CountPosts

    // Find the latest post attached to this topic.
    function Latest ($pTOPICID) {

      global $gLATESTDATE, $gLATESTID;

      $LATEST = new cGROUPCONTENT ();

      $criteria = array ("groupInformation_tID"  => $this->tID,
                         "parent_tID"            => $pTOPICID);

      $LATEST->SelectByMultiple ($criteria);

      while ($LATEST->FetchArray ()) {
        $stamp = strtotime ($LATEST->Stamp);
        if ($stamp > $gLATESTDATE) {
          $gLATESTDATE = $stamp;
          $gLATESTID = $LATEST->tID;
        } // if

        $this->FindLatest ($LATEST->tID);
      } // while

      unset ($LATEST);

      return ($gLATESTID);

    } // Latest

    function FindLatest ($pTOPICID) {
      $latestid = $this->Latest ($pTOPICID);

      if ($latestid) {
        $LATEST = new cGROUPCONTENT ();
        $LATEST->Select ("tID", $latestid);
        $LATEST->FetchArray ();
        $returnarray = array ($LATEST->Owner_Username, $LATEST->Owner_Domain);
        unset ($LATEST);
      } else {
        $returnarray = array ($this->groupContent->Owner_Username, $this->groupContent->Owner_Domain);
      } // if

      return ($returnarray);
    }

    // Notify the user that a post has been replied to.
    function NotifyReply ($pEMAIL, $pREPLIEDUSER, $pREPLIEDUSERNAME, $pREPLYINGUSER) {
      global $zOLDAPPLE, $zFOCUSUSER;

      // Return if post notification is turned off.
      if ($zFOCUSUSER->userSettings->Get ("GroupReplyNotification") == NOTIFICATION_OFF) {
        return (FALSE);
      } // 

      if (!$pREPLYINGUSER) {
        $pREPLYINGUSER = __( "Anonymous User" );
      } // if

      global $gREPLYINGUSER, $gREPLIEDUSER;
      $gREPLYINGUSER = $pREPLYINGUSER;
      $gREPLIEDUSER = $pREPLIEDUSER;

      global $gINFOURL, $gSITEURL;
      global $gGROUPREQUEST;
      global $gSITEDOMAIN;

      $gINFOURL = $gSITEURL . 'group/' . $gGROUPREQUEST . '/thread/' . $this->groupContent->tID; 

      $to = $pEMAIL;

      $subject = __("Group Subject", array ( "fromusername" => $gREPLYINGUSER ) );

      $body = __("Group Body", array ( "touser" => $gREPLIEDUSER, "fromuser" => $gREPLYINGUSER, "link" => $gINFOURL ) );

      $from = __("groups@site", array ( "domain" => $gSITEDOMAIN ) );

      $fromname = __( "Group From" );

      $zOLDAPPLE->Mailer->From = $from;
      $zOLDAPPLE->Mailer->FromName = $fromname;
      $zOLDAPPLE->Mailer->Body = $body;
      $zOLDAPPLE->Mailer->Subject = $subject;
      $zOLDAPPLE->Mailer->AddAddress ($to);
      $zOLDAPPLE->Mailer->AddReplyTo ($from);

      $zOLDAPPLE->Mailer->Send();

      $zOLDAPPLE->Mailer->ClearAddresses();

      unset ($to);
      unset ($subject);
      unset ($body);

      return (TRUE);

    } // NotifyReply

    // Goes through and removes all deleted posts with no children.
    function CleanUp () {
      global $zOLDAPPLE;

      $criteria = array ("groupInformation_tID"     => $this->tID,
                         "Body" => DELETED_GROUP_ENTRY);
      $this->groupContent->SelectByMultiple ($criteria);

      // Break out if no deleted posts left.  
      if ($this->groupContent->CountResult() == 0) return (TRUE);

      while ($this->groupContent->FetchArray ()) {
        $CHILDREN = new cGROUPCONTENT ();
        $childcriteria = array ("parent_tID" => $this->groupContent->tID,
                                "groupInformation_tID"        => $this->tID,
                                "Context"    => $zOLDAPPLE->Context);
        $CHILDREN->SelectByMultiple ($childcriteria);

        // If no children, delete.
        if ($CHILDREN->CountResult () == 0) {
          $this->groupContent->Delete ();
          $this->CleanUp ();
        } // if
        unset ($CHILDREN);
      } // while
    } // CleanUp

    // Check if user has editor access.
    function CheckEditorAccess () {
      global $zOLDAPPLE, $zLOCALUSER; 

      // Check if user has ownership access to this page.
      if ($this->userAuth_uID != $zLOCALUSER->uID) {
        // Error out if user does not have access privileges.
        if ($zLOCALUSER->userAccess->e == FALSE) {
          return (FALSE);
        } // if
      } // if

      return (TRUE);
    } // CheckEditorAccess

    function CheckViewAccess () {
      global $zAUTHUSER, $zLOCALUSER;

      // Check if localuser has editor access.
      if ($zLOCALUSER->userAccess->e == TRUE) {
        return (TRUE);
      } // if

      switch ($this->Access) {
        case GROUP_ACCESS_APPROVAL_PRIVATE:
        case GROUP_ACCESS_INVITE_PRIVATE:
          // Check if authuser is a member of this group.
          $membercriteria = array ("Username"                 => $zAUTHUSER->Username,
                                   "Domain"                   => $zAUTHUSER->Domain,
                                   "Verification"             => GROUP_VERIFICATION_APPROVED,
                                   "groupInformation_tID"     => $this->tID);
          $this->groupMembers->SelectByMultiple ($membercriteria);
          if ($this->groupMembers->CountResult() == 0) {
            // Authorized user is not a member.
            return (FALSE);
          } else {
            // Authorized user is a member.
            return (TRUE);
          } // if
        break;
        case GROUP_ACCESS_OPEN:
        case GROUP_ACCESS_OPEN_MEMBERSHIP:
        case GROUP_ACCESS_APPROVAL_PUBLIC:
        case GROUP_ACCESS_INVITE_PUBLIC:
        default:
        break;
      } // switch

      return (TRUE);
    } // CheckViewAccess
 
    function CheckUserAccess () {
      global $zAUTHUSER, $zLOCALUSER;

      // Check if localuser has editor access.
      if ($zLOCALUSER->userAccess->e == TRUE) {
        return (TRUE);
      } // if

      switch ($this->Access) {
        case GROUP_ACCESS_APPROVAL_PRIVATE:
        case GROUP_ACCESS_INVITE_PRIVATE:
          // Check if authuser is a member of this group.
          $membercriteria = array ("Username"                 => $zAUTHUSER->Username,
                                   "Domain"                   => $zAUTHUSER->Domain,
                                   "Verification"             => GROUP_VERIFICATION_APPROVED,
                                   "groupInformation_tID"     => $this->tID);
          $this->groupMembers->SelectByMultiple ($membercriteria);
          if ($this->groupMembers->CountResult() == 0) {
            // Authorized user is not a member.
            return (FALSE);
          } else {
            // Authorized user is a member.
            return (TRUE);
          } // if
        break;
        case GROUP_ACCESS_APPROVAL_PUBLIC:
        case GROUP_ACCESS_INVITE_PUBLIC:
        case GROUP_ACCESS_OPEN:
        case GROUP_ACCESS_OPEN_MEMBERSHIP:
        default:
          // Check if authuser is a member of this group.
          $membercriteria = array ("Username"                 => $zAUTHUSER->Username,
                                   "Domain"                   => $zAUTHUSER->Domain,
                                   "groupInformation_tID"     => $this->tID);
          $this->groupMembers->SelectByMultiple ($membercriteria);
          if ($this->groupMembers->CountResult() == 0) {
            // Authorized user is not a member.
            return (FALSE);
          } else {
            // Authorized user is a member.
            return (TRUE);
          } // if
        break;
      } // switch

      return (TRUE);
    } // CheckUserAccess
 
    function CheckJoinAccess () {
      global $zAUTHUSER;

      // User is anonymous, cannot join.
      if ($zAUTHUSER->Anonymous) {
        return (FALSE);
      } // if

      // Group is invite-only, no one can join.
      if ( ($this->Access == GROUP_ACCESS_INVITE_PUBLIC) or
           ($this->Access == GROUP_ACCESS_INVITE_PRIVATE) ) {
        return (FALSE);
      } // if

      return (TRUE);
    } // CheckJoinAccess

    function CheckApproval () {
      global $zAUTHUSER;
      
      // NOTE:  I'm not sure why I have to null this out here. If I don't,
      // then when you leave the group, it doesn't report the right value
      // until a page reload.
      $this->groupMembers->Verification = NULL;

      $membercriteria = array ("Username"                 => $zAUTHUSER->Username,
                               "Domain"                   => $zAUTHUSER->Domain,
                               "groupInformation_tID"     => $this->tID);
      $this->groupMembers->SelectByMultiple ($membercriteria);
      $this->groupMembers->FetchArray();

      // Check if the record is pending.
      if ($this->groupMembers->Verification == GROUP_VERIFICATION_PENDING) {
        return (FALSE);
      } // if

      return (TRUE);
    } // CheckApproval

    // Determine which tab file to load.
    function DetermineTabs () {
      global $zLOCALUSER;

      // Currently logged in user is an editor.
      if ($this->CheckEditorAccess()) return ("owner");

      if ($this->CheckViewAccess()) {
        // Group is public
        $returntab = "public";
      } else {
        // Group is private, hide everything from non-members except for info.
        $returntab = "private";
      } // if

      return ($returntab);

    } // DetermineTabs

   function GetInformation ($pGROUPNAME, $pGROUPDOMAIN) {
     global $gSITEDOMAIN, $zXML;
      global $gAPPLESEEDVERSION;

     if ($gSITEDOMAIN != $pGROUPDOMAIN) {
       $zREMOTE = new cREMOTE ($pGROUPDOMAIN);
       $datalist = array ("gACTION"        => "ASD_GROUP_INFORMATION",
                          "gVERSION"       => $gAPPLESEEDVERSION,
                          "gGROUPNAME"     => $pGROUPNAME);
       $zREMOTE->Post ($datalist);

       $zXML->Parse ($zREMOTE->Return);
       $success = $zXML->GetValue ("success", 0);

       $this->Name = $pGROUPNAME;
       $this->Fullname = $zXML->GetValue ("fullname", 0);
       $this->Description = $zXML->GetValue ("description", 0);
       $this->Stamp = $zXML->GetValue ("stamp", 0);
       $this->Tags = $zXML->GetValue ("tags", 0);
       $this->Members = $zXML->GetValue ("members", 0);
     } else {
       $this->Select ("Name", $pGROUPNAME);
       $this->FetchArray ();
       if ($this->CountResult() == 0) return (FALSE);
     } // if

     return (TRUE);
   } // GetInformation

   function BufferMemberList () {
      global $zOLDAPPLE;

      global $gFRAMELOCATION;
      global $gTHEMELOCATION;

      global $gSCROLLMAX, $gSCROLLSTEP;
      global $gLISTCOUNT;

      // Choose the member list for this group.
      $membercriteria = array ("groupInformation_tID" => $this->tID,
                               "Verification"         => GROUP_VERIFICATION_APPROVED);
      $this->groupMembers->SelectByMultiple ($membercriteria);

      // Calculate scroll values.
      $gSCROLLMAX[$zOLDAPPLE->Context] = $this->groupMembers->CountResult();

      // Set page view to 50.
      $gSCROLLSTEP[$zOLDAPPLE->Context] = 50;
  
      // Check if any results were found.
      if ($gSCROLLMAX[$zOLDAPPLE->Context] == 0) {
        $this->groupMembers->Message = __("No Results Found");
        $return .= $this->groupMembers->CreateBroadcast();
      } // if

      // Loop through the list.
      for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zOLDAPPLE->Context]; $gLISTCOUNT++) {
        if ($this->groupMembers->FetchArray()) {

        global $gMEMBERNAME, $bMEMBERICON, $bONLINENOW;

        // Retrieve user info.
        list ($gMEMBERNAME, $online) = $zOLDAPPLE->GetUserInformation($this->groupMembers->Username, $this->groupMembers->Domain);

        // Load the user icon.
        $bMEMBERICON = $zOLDAPPLE->BufferUserIcon ($this->groupMembers->Username, $this->groupMembers->Domain, NULL);

        // Load the online icon.
        if ($online) $bONLINENOW = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/onlinenow.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);

        $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/members/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if
      } // for

      return ($return);
   } // BufferMemberList

    function Join ($pGROUPNAME, $pGROUPDOMAIN) {
    	
      global $gSITEDOMAIN;
      global $gAPPLESEEDVERSION;
      
      global $zLOCALUSER, $zXML;

      $success = FALSE;

      if ($pGROUPDOMAIN != $gSITEDOMAIN) {
        $token = $this->Token ($zLOCALUSER->Username, $pGROUPDOMAIN);

        $zREMOTE = new cREMOTE ($pGROUPDOMAIN);
        $datalist = array ("gACTION"        => "ASD_GROUP_JOIN",
                           "gTOKEN"         => $token,
                           "gGROUPNAME"     => $pGROUPNAME,
                           "gUSERNAME"      => $zLOCALUSER->Username,
                           "gVERSION"       => $gAPPLESEEDVERSION,
                           "gDOMAIN"        => $gSITEDOMAIN);
        $zREMOTE->Post ($datalist);

        $zXML->Parse ($zREMOTE->Return);
        $success = $zXML->GetValue ("success", 0);
        $message = $zXML->GetValue ("message", 0);
      } else {

        // Join Locally.
        $this->Select ("Name", $pGROUPNAME);
        if ($this->CountResult() == 0) {
          $this->Message = __("Group Not Found");
          $this->Error = -1;
          return (FALSE);
        } // if
        $this->FetchArray ();
        $membercriteria = array ("Username"                 => $zLOCALUSER->Username,
                                 "Domain"                   => $pGROUPDOMAIN,
                                 "groupInformation_tID"     => $this->tID);
        $this->groupMembers->SelectByMultiple ($membercriteria);
        $this->groupMembers->FetchArray ();

        // Check for existing group membership record.
        if ($this->groupMembers->CountResult() == 0) {
          $this->groupMembers->groupInformation_tID = $this->tID;
          $this->groupMembers->Username = $zLOCALUSER->Username;
          $this->groupMembers->Domain = $gSITEDOMAIN;
          if ( ($this->Access == GROUP_ACCESS_OPEN) or 
               ($this->Access == GROUP_ACCESS_OPEN_MEMBERSHIP) ) {
            $message = "MESSAGE.JOINED";
            $this->groupMembers->Verification = GROUP_VERIFICATION_APPROVED;
          } else {
            $message = "MESSAGE.PENDING";
            $this->groupMembers->Verification = GROUP_VERIFICATION_PENDING;
          } // if

          if ($pVERIFICATION)
            $this->groupMembers->Verification = $pVERIFICATION;

          $this->groupMembers->Stamp = SQL_NOW;
          $this->groupMembers->Add ();
        } // if
        $success = TRUE;
      } // if

      // If we're successful, add group to the users groups list.
      if ($success) {
        // Add membership to the user list.
        $groupcriteria = array ("Name"                     => $pGROUPNAME,
                                "Domain"                   => $pGROUPDOMAIN,
                                "userAuth_uID"             => $zLOCALUSER->uID);
        $zLOCALUSER->userGroups->SelectByMultiple ($groupcriteria);
        $zLOCALUSER->userGroups->FetchArray ();

        // Check for existing user group record.
        if ($zLOCALUSER->userGroups->CountResult() == 0) {
          $zLOCALUSER->userGroups->userAuth_uID = $zLOCALUSER->uID;
          $zLOCALUSER->userGroups->Name = $pGROUPNAME;
          $zLOCALUSER->userGroups->Domain = $pGROUPDOMAIN;
          $zLOCALUSER->userGroups->Add ();
        } // if
      } else {
        if (!$message) $message = "Unknown Error";
        $this->Error = -1;
      } // if

      $this->Message = __($message);

      if ($this->groupMembers->Error != -1) return (TRUE);
      return (FALSE);
    } // Join

    function Leave ($pGROUPNAME, $pGROUPDOMAIN) {
      global $gSITEDOMAIN;
      global $gAPPLESEEDVERSION;

      global $zLOCALUSER, $zXML;

      $success = FALSE;

      $token = $this->Token ($zLOCALUSER->Username, $pGROUPDOMAIN);

      if ($pGROUPDOMAIN != $gSITEDOMAIN) {
        $zREMOTE = new cREMOTE ($pGROUPDOMAIN);
        $datalist = array ("gACTION"        => "ASD_GROUP_LEAVE",
                           "gTOKEN"         => $token,
                           "gVERSION"       => $gAPPLESEEDVERSION,
                           "gGROUPNAME"     => $pGROUPNAME);
        $zREMOTE->Post ($datalist);
        $zXML->Parse ($zREMOTE->Return);
        $success = $zXML->GetValue ("success", 0);
        $message = $zXML->GetValue ("message", 0);
      } else {

        // Leave Locally.
        $this->Select ("Name", $pGROUPNAME);
        if ($this->CountResult() == 0) {
          $this->Message = __("Group Not Found");
          $this->Error = -1;
          return (FALSE);
        } // if
        $this->FetchArray ();
        $deletecriteria = array ("Username"                 => $zLOCALUSER->Username,
                                 "Domain"                   => $pGROUPDOMAIN,
                                 "groupInformation_tID"     => $this->tID);
        $this->groupMembers->SelectByMultiple ($deletecriteria);
        $this->groupMembers->FetchArray ();
        $this->groupMembers->Delete ();
        $success = TRUE;
      } // if

      // If we're successful, remove group from the users groups list.
      if ($success) {
        // Add membership to the user list.
        $groupcriteria = array ("Name"                     => $pGROUPNAME,
                                "Domain"                   => $pGROUPDOMAIN,
                                "userAuth_uID"             => $zLOCALUSER->uID);
        $zLOCALUSER->userGroups->SelectByMultiple ($groupcriteria);
        $zLOCALUSER->userGroups->FetchArray ();
        $zLOCALUSER->userGroups->Delete ();
      } else {
        if (!$message) $message = "Unknown Error";
        $this->Error = -1;
      } // if

      $this->Message = __($message);

      if ($this->groupMembers->Error != -1) return (TRUE);
      return (FALSE);
    } // Leave

    function Approve ($pUSERNAME, $pDOMAIN) {

      $criteria = array ("Username" => $pUSERNAME,
                         "Domain"   => $pDOMAIN,
                         "groupInformation_tID" => $this->tID);
      $this->groupMembers->SelectByMultiple ($criteria);

      if ($this->groupMembers->CountResult() == 0) return (FALSE);

      $this->groupMembers->FetchArray ();

      $this->groupMembers->Verification = GROUP_VERIFICATION_APPROVED;

      $this->groupMembers->Update ();

      return (TRUE);
       
    } // Approve

    function BufferMemberEditor () {
      global $zOLDAPPLE;

      global $gFRAMELOCATION;
      global $gSITEDOMAIN;
      global $gMEMBERCOUNT;

      $membercriteria = array ("groupInformation_tID" => $this->tID,
                               "Verification"         => GROUP_VERIFICATION_APPROVED);
      $this->groupMembers->SelectByMultiple ($membercriteria);
      $gMEMBERCOUNT = $this->groupMembers->CountResult ();
      $return = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/members/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      if ($this->groupMembers->CountResult() == 0) {
        $this->groupMembers->Message = __("No Results Found");
        $return .= $this->groupMembers->CreateBroadcast ();
      } else {
        while ($this->groupMembers->FetchArray ()) {
          $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/members/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // while
      } // if
      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/members/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($return);
    } // BufferMemberEditor

    function BufferPendingEditor () {
      global $zOLDAPPLE;

      global $gFRAMELOCATION;

      global $gPENDINGCOUNT;

      $pendingcriteria = array ("groupInformation_tID" => $this->tID,
                                "Verification"         => GROUP_VERIFICATION_PENDING);
      $this->groupMembers->SelectByMultiple ($pendingcriteria);
      $gPENDINGCOUNT = $this->groupMembers->CountResult ();
      $return = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/pending/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      if ($this->groupMembers->CountResult() == 0) {
        $this->groupMembers->Message = __("No Results Found");
        $return .= $this->groupMembers->CreateBroadcast ();
      } else {
        while ($this->groupMembers->FetchArray ()) {
          $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/pending/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // while
      } // if
      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/pending/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($return);
    } // BufferPendingditor

    function BufferInviteEditor () {
      global $zOLDAPPLE;

      global $gFRAMELOCATION;

      global $gINVITECOUNT;

      $pendingcriteria = array ("groupInformation_tID" => $this->tID,
                                "Verification"         => GROUP_VERIFICATION_INVITED);
      $this->groupMembers->SelectByMultiple ($pendingcriteria);
      $gINVITECOUNT = $this->groupMembers->CountResult ();
      $return = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/invite/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      if ($this->groupMembers->CountResult() == 0) {
        $this->groupMembers->Message = __("No Results Found");
        $return .= $this->groupMembers->CreateBroadcast ();
      } else {
        while ($this->groupMembers->FetchArray ()) {
          $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/invite/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // while
      } // if
      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/invite/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/invite/main.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($return);
    } // BufferInviteEditor

    // Save General Options.
    function SaveGeneral () {

      // Save the table id.
      $tid = $this->tID;

      // Synchronize global values.
      $this->Synchronize ();

      // Restore the table id.
      $this->tID = $tid;

      // Group name, ownership and created stamp cannot be changed.
      $this->Name = SQL_SKIP;
      $this->Stamp = SQL_SKIP;
      $this->userAuth_uID = SQL_SKIP;

      // Sanity check variables.
      $this->Sanity();
      if (!$this->Error) {
        $this->Update ();
      } else {
        return (FALSE);
      } // if

      return (TRUE);
    } // SaveGeneral

    // Process the invite list.
    function ProcessInvites () {
      global $zLOCALUSER;

      global $gINVITES, $gSITEDOMAIN;
      global $gINVITINGUSER, $gGROUPFULLNAME, $gGROUPURL;
      global $gGROUPINVITEACTION, $gGROUPINVITEUSERNAME, $gGROUPINVITEDOMAIN;

      // Loop through the action list.
      foreach ($gGROUPINVITEACTION as $count => $action) {
        $username = $gGROUPINVITEUSERNAME[$count];
        $domain  = $gGROUPINVITEDOMAIN[$count];
        switch ($action) {
          case GROUP_ACTION_REMOVE:
           $USER = new cOLDUSER ();
           $USER->Select ("Username", $username);
           $USER->FetchArray ();
           if ($USER->uID != $this->userAuth_uID) {
              //$this->Leave ($username, $domain);
           } // if
           unset ($USER);
          break;
        } // switch
      } // foreach

      $gINVITINGUSER = $zLOCALUSER->userProfile->GetAlias ();
      $gGROUPFULLNAME = $this->Fullname;
      $groupaddress = $this->Name . '@' . $gSITEDOMAIN;
      $gGROUPURL = "!##asd group='$groupaddress' /##!";

      $invitelist = explode (',', $gINVITES);
      foreach ($invitelist as $id => $address) {
        list ($username, $domain) = explode ('@', $address);

        $checkcriteria = array ("groupInformation_tID" => $this->tID,
                                "Username"             => $username,
                                "Domain"               => $domain);
        $this->groupMembers->SelectByMultiple ($checkcriteria);
        if ($this->groupMembers->CountResult () > 0) {
          // User is already in the list.  Continue.
          continue;
        } // if
        $this->groupMembers->Username = $username;
        $this->groupMembers->Domain = $domain;
        $this->groupMembers->groupInformation_tID = $this->tID;
        $this->groupMembers->Verification = GROUP_VERIFICATION_INVITED;
        $this->groupMembers->Stamp = SQL_NOW;
        $this->groupMembers->Add ();
        
        // NOTE:  Message shouldn't use globals for recipients.
        $MESSAGE = new cMESSAGE ();

        global $gRECIPIENTNAME, $gRECIPIENTDOMAIN, $gRECIPIENTADDRESS;
        $gRECIPIENTNAME = $username;
        $gRECIPIENTDOMAIN = $domain;
        $gRECIPIENTADDRESS = $gRECIPIENTNAME . '@' . $gRECIPIENTDOMAIN;
        
        global $gSUBJECT, $gBODY;
        $gBODY = __("Group Invite Body", array ( "name" => $gRECIPIENTNAME, "domain" => $gRECIPIENTDOMAIN, "address" => $gRECIPIENTADDRESS ) );
        $gBODY = str_replace ("!##", "<", $gBODY);
        $gBODY = str_replace ("##!", ">", $gBODY);
        $gSUBJET = __("Group Invite Subject", array ( "name" => $gRECIPIENTNAME) );
        $MESSAGE->Send ($zLOCALUSER->Username);
        unset ($MESSAGE);
      } // foreach

      $this->Message = __("User Invited To Group");
      $this->Error = 0;

      return (TRUE);

    } // ProcessInvites

    // Save Member Editor Values.
    function SaveMemberEditor () {

      global $gSITEDOMAIN;

      global $gGROUPMEMBERACTION, $gGROUPMEMBERUSERNAME, $gGROUPMEMBERDOMAIN;

      // Loop through the action list.
      foreach ($gGROUPMEMBERACTION as $count => $action) {
        $username = $gGROUPMEMBERUSERNAME[$count];
        $domain  = $gGROUPMEMBERDOMAIN[$count];
        switch ($action) {
          case GROUP_ACTION_APPROVE:
           $this->Approve ($username, $domain);
          break;
          case GROUP_ACTION_REMOVE:
           $USER = new cOLDUSER ();
           $USER->Select ("Username", $username);
           $USER->FetchArray ();
           if ($USER->uID != $this->userAuth_uID) {
              //$this->Leave ($username, $domain);
           } // if
           unset ($USER);
          break;
        } // switch
      } // foreach

      $this->Message = __("Member List Saved");
      return (TRUE);
    } // SaveMemberEditor

    // Save Pending Editor Values.
    function SavePendingEditor () {

      global $gSITEDOMAIN;

      global $gGROUPPENDINGACTION, $gGROUPPENDINGUSERNAME, $gGROUPPENDINGDOMAIN;

      // Loop throuogh the action list.
      foreach ($gGROUPPENDINGACTION as $count => $action) {
        $username = $gGROUPPENDINGUSERNAME[$count];
        $domain  = $gGROUPPENDINGDOMAIN[$count];
        switch ($action) {
          case GROUP_ACTION_APPROVE:
           $this->Approve ($username, $domain);
          break;
          case GROUP_ACTION_REMOVE:
           $USER = new cOLDUSER ();
           $USER->Select ("Username", $username);
           $USER->FetchArray ();
           if ($USER->uID != $this->userAuth_uID) {
             $this->Leave ($username, $domain);
           } // if
           unset ($USER);
          break;
        } // switch
        $this->Message = __("Member Pending List Saved");
      } // foreach

      return (TRUE);
    } // SavePendingEditor

    function FindTopic ($pCHILDID) {

      $FIND = new cGROUPINFORMATION ();
      $FIND->groupContent->Select ("tID", $pCHILDID);
      $FIND->groupContent->FetchArray ();

      if ($FIND->groupContent->parent_tID == 0) return ($FIND->groupContent->tID);

      $return = $FIND->FindTopic ($FIND->groupContent->parent_tID);
      unset ($FIND);

      return  ($return);
    } // FindTopic

    function SetLatest ($pCHILDID, $pPARENTID) {
      // If no parent id, then we're starting a topic, set the
      // latest id to the current child value.
      if (!$pPARENTID) {
        $this->groupContent->latest_tID = $pCHILDID;
        $this->groupContent->Update ();
        return (TRUE);
      } // if

      // Traverse the tree upwards to find the original topic entry
      $FIND = new cGROUPINFORMATION ();
      $topic = $FIND->FindTopic ($pCHILDID);
      $FIND->groupContent->Select ("tID", $topic);
      $FIND->groupContent->FetchArray ();
      $FIND->groupContent->latest_tID = $pCHILDID;

      // Update the latest value to the current child.
      $FIND->groupContent->Update ();
      unset ($FIND);
      return (TRUE);
    } // SetLatest

    function Token ($pUSERNAME, $pDOMAIN) {
      $VERIFY = new cAUTHTOKENS ();
      
      $VERIFY->LoadToken ($pUSERNAME, $pDOMAIN);
      
      $token = $VERIFY->Token;
      
      if (!$token) {
        $VERIFY->CreateToken ($pUSERNAME, $pDOMAIN);
        $token = $VERIFY->Token;
      } // if
      
      unset ($VERIFY);
      
      return ($token);
    } // Token

  } // cGROUPINFORMATION

  // Group content class.
  class cGROUPCONTENT extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID, $groupInformation_tID, $parent_tID;
    
    // Variables
    var $Subject, $Body, $Stamp;

    function cGROUPCONTENT ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'groupContent';
      $this->tID = '';
      $this->groupInformation_tID = '';
      $this->Context = '';
      $this->userAuth_uID = '';
      $this->parent_tID = '';
      $this->Subject = '';
      $this->Body = '';
      $this->Stamp = '';
      $this->Error = 0;
      $this->Message = '';
      $this->PageContext = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

'groupInformation_tID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'parent_tID'     => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'latest_tID'     => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Subject'        => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Body'           => array ('max'        => '65536',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),


        'Owner_Username' => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Owner_Domain'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Owner_Icon'     => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Owner_Address'  => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
  } // cGROUPCONTENT

  // Group members class.
  class cGROUPMEMBERS extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID, $groupInformation_tID, $parent_tID;
    
    // Variables
    var $Username, $Domain, $Verification, $Stamp;

    function cGROUPMEMBERS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'groupMembers';
      $this->tID = '';
      $this->Context = '';
      $this->userAuth_uID = '';
      $this->parent_tID = '';
      $this->Username = '';
      $this->Domain = '';
      $this->Verification = '';
      $this->Error = 0;
      $this->Message = '';
      $this->PageContext = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

'groupInformation_tID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Username'       => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Verification'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
    } // Constructor

    function CountMembers ($pNAME) {

      // NOTE: Merge this into one big join/select statement.
      $GROUPINFO = new cGROUPINFORMATION ();
      $GROUPINFO->Select ("Name", $pNAME);

      // No group by this name was found, error out.
      if ($GROUPINFO->CountResult () == 0) return (FALSE);

      $GROUPINFO->FetchArray ();

      $this->Statement = "SELECT COUNT(Username) As CountResult " . 
                         "FROM   " . $this->TableName . " " .  
                         "WHERE  groupInformation_tID = '" . $GROUPINFO->tID . "' " .
                         "AND    Verification = '" . GROUP_VERIFICATION_APPROVED . "'"; 
      $this->Query ($this->Statement); 
      $this->FetchArray ();

      unset ($GROUPINFO);

      return ($this->CountResult);
      
    } // CountMembers;

    // Check if specified user is a member of 
    function CheckMembership ($pGROUPID, $pUSERNAME, $pDOMAIN) {
      $membershipcriteria = array ("groupInformation_tID" => $pGROUPID,
                                   "Username" => $pUSERNAME,
                                   "Domain"   => $pDOMAIN);
      $this->SelectByMultiple ($membershipcriteria);

      if ($this->CountResult() > 0) return (TRUE);

      return (FALSE);
    } // CheckMembership

 } // cGROUPMEMBERS

?>
