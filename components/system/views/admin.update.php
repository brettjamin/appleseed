<script type="text/javascript" src="/components/system/assets/javascript/admin.update.js"></script>

<h1>Admin System Update Title</h1> 
<p id='update-message'></p>
 
<p>Admin System Update Description</p> 
 
<form id="update" name="update" method="POST"> 
	<input type="hidden" name="Task" />
	<input type="hidden" name="Context" />
	
 	<fieldset>
 		<label for="Current">Current Version</label> 
 		<input type="text" disabled="disabled" name="Current" class="Current" maxlength="255" value="" />
 		
 		<label for="Server">Update Server Location</label> 
 		<select name="Server"></select>
	
 		<label for="NewServer">Add Server</label> 
 		<input type="text" name="NewServer" class="NewServer" maxlength="255" value="" />
 		<button id="add-server" type="submit" name="Task" value="AddServer">Add Server Button</button>
 		
 		<label for="version">Version</label> 
		<select class="version required" name="Version"> 
		</select> 
	
		<label for="BackupDirectory">Backup Directory</label> 
		<input type="text" name="BackupDirectory" class="BackupDirectory" maxlength="255" value="" /> 
		
		<label for="SkipBackups">Skip Backups</label> 
		<input type="checkbox" name="SkipBackups" class="SkipBackups" > 
		
	</fieldset>
	<button type="submit" id="update-button" name="Task" value="Update">Update</button>
 </form>
