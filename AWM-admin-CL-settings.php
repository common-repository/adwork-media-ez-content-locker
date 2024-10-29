<?
	updateDatabase();	
?>
<div class="wrap">

<div class="clear">&nbsp;</div>	


<?

if ($_POST['AWM_create_submit']) {	
		if(empty($_REQUEST['new_code'])) {
			echo '<div class="updated"><p>Error, please enter your AdWork Media Content Locker code!</p></div>';	
		} else {	
			if(!is_array($_REQUEST['new_pages']))	{ 
				$newPages=$_REQUEST['new_pages'];
			} else {
				$newPages=implode(',',$_REQUEST['new_pages']);
			}
			if(!is_array($_REQUEST['new_posts'])) {
				$newPosts=$_REQUEST['new_posts'];
			} else {
				$newPosts=implode(',', $_REQUEST['new_posts']);
			}
		    createLocker($_REQUEST['new_profile_name'], $_REQUEST['new_code'], $newPages,  $newPosts, $_REQUEST['new_display'], $_REQUEST['hideShow']);			
				//echo '<div class="updated"><p><strong>Success, your preferences have been updated.</strong></p></div>';		
			$created=true;
	}
}

if ($_POST['AWM_update_submit']) {			 			
			$count=$_REQUEST['lockerID'];
			if(empty($_REQUEST['code'.$count])) {
				echo '<div class="updated"><p>Error, please enter your AdWork Media Content Locker code!</p></div>';	
			} else {
				if(!is_array($_REQUEST['pages'.$count]))	{ 
					$updatePages=$_REQUEST['pages'.$count];
				} else {
					$updatePages=implode(',',$_REQUEST['pages'.$count]);
				}
				if(!is_array($_REQUEST['posts'.$count])) {
					$updatePosts=$_REQUEST['posts'.$count];
				} else {
					$updatePosts=implode(',', $_REQUEST['posts'.$count]);
				}									
					updateLocker($count, $_REQUEST['profile_name'.$count], $_REQUEST['code'.$count], $_REQUEST['status'.$count], $updatePages, $updatePosts, $_REQUEST['display'.$count], $_REQUEST['hideShow'.$count]);																	
			}		
		//echo '<div class="updated"><p>Success, your preferences have been updated.</p></div>';		
}

if($_POST['delete_locker']=='Delete Profile') {	
	$count=$_REQUEST['lockerID'];
	deleteLocker($count);
}

$findLockers=$wpdb->get_results("SELECT * FROM ".AWM_TABLE." WHERE lockType=0",ARRAY_A);

?>
<? if($wpdb->num_rows>0) { ?> 
<h3>Current Content Lockers</h3>
<p>Manage your existing Content Lockers &raquo;</p>

<?php foreach($findLockers as $rowLocker){//while($rowLocker=mysql_fetch_array($findLockers)) { ?>
<form method="post" action="">
    <div class="metabox-holder">				
        <div class="postbox">        
        <h3>Profile Name: <input name="profile_name<?=$rowLocker['ID'];?>" type="text" value="<?php echo stripslashes($rowLocker['name']); ?>" size="50" /> <span style="padding-left:20px;">Status:</span>
        <select name="status<?=$rowLocker['ID'];?>">
        	<option value="1" <? if($rowLocker['status']==1) { echo 'selected="selected"'; }?>>Active</option>
            <option value="0" <? if($rowLocker['status']==0) { echo 'selected="selected"'; }?>>Disabled</option>
        </select>
        <span style="padding-left:20px;">Display Style:</span>
        <select name="display<?=$rowLocker['ID'];?>">
        	<option value="0" <? if($rowLocker['display']==0) { echo 'selected="selected"'; }?>>Always Show on Selected Posts and Pages</option>
            <option value="1" <? if($rowLocker['display']==1) { echo 'selected="selected"'; }?>>Never Show on Selected Posts and Pages</option>
        </select>
        </h3>
            <div class="inside">
            <table border="0" width="90%">
            	<tr>
                	<td>
                    <p>Paste your AdWork Media Gateway Code Here - Visit the <strong><a target="_blank" href="https://www.adworkmedia.com/publisher/index.php?option=tools&section=gateway_manager&utm_source=WP_Plugin&utm_medium=plugin&utm_campaign=WP">Gateway Manager</a></strong> and click the "Get Code" icon to access your code.</p>
                    <p>
                    <textarea name="code<?=$rowLocker['ID'];?>" cols="75" rows="5"><?php echo stripslashes($rowLocker['locker_code']); ?></textarea>
                    </p>
                	</td>                     
                    <td style="padding-left:10px;"><b>Selected Posts:</b><br /><?=choosePosts(explode(',', $rowLocker['posts']), $rowLocker['ID']);?><br /><small>*Hold CTRL to select multiple entries</small></td>          
                    <td style="padding-left:10px;"><b>Selected Pages:</b><br /><?=choosePages(explode(',', $rowLocker['pages']), $rowLocker['ID']);?><br /><small>*Hold CTRL to select multiple entries</small></td>                   
                </tr>
                <tr>
                	<td colspan="3"><h3>User/Admin Bypass Settings:
        <select name="hideShow<?=$rowLocker['ID'];?>">
        	<option value="0" <? if($rowLocker['hideShow']==0) { echo 'selected="selected"'; }?>>No Preference</option>
            <option value="1" <? if($rowLocker['hideShow']==1) { echo 'selected="selected"'; }?>>Hide Locker For Logged In Users</option>
            <option value="2" <? if($rowLocker['hideShow']==2) { echo 'selected="selected"'; }?>>Hide Locker For Logged In Admins</option>
            <option value="3" <? if($rowLocker['hideShow']==3) { echo 'selected="selected"'; }?>>Hide For Logged In Users &amp; Admins</option>
        </select></h3><br /><strong><u>Total Profile Loads:</u> <?=number_format($rowLocker['totalLeads']);?></strong><br /><br />
                    <input type="submit" class="button-primary" value="Update Preferences &raquo;" name="AWM_update_submit"><br /><input style="margin-top:5px;" type="submit" class="button-primary" value="Delete Profile" onClick="return confirm('Are you sure you want to delete this profile?');" name="delete_locker"></td>
                </tr>
             </table>
            </div>
        </div>
    </div>
    <input type="hidden" name="lockerID" value="<?=$rowLocker['ID'];?>" />
</form>      
    <? } ?>         

<? } ?>

<? if($created!=true) { ?>

<form method="post" action="">    
<h3>Add New Content Locker</h3>
<p>Enter the details below to create a new Content Locker profile...</p>
    <div class="metabox-holder">				
        <div class="postbox">
        <h3>Profile Name: <input name="new_profile_name" type="text" value="<?php echo stripslashes($_REQUEST['new_profile_name']); ?>" size="50" />
        <span style="padding-left:20px;">Display Style:</span>
        <select name="new_display">
        	<option value="0" <? if($_REQUEST['display']==0) { echo 'selected="selected"'; }?>>Always Show on Selected Posts and Pages</option>
            <option value="1" <? if($_REQUEST['display']==1) { echo 'selected="selected"'; }?>>Never Show on Selected Posts and Pages</option>
        </select>
        </h3>
            <div class="inside">
            <table border="0" width="90%">
            	<tr>
                	<td>
                    <p>Paste your AdWork Media Gateway Code Here - Visit the <strong><a target="_blank" href="https://www.adworkmedia.com/publisher/index.php?option=tools&section=gateway_manager&utm_source=WP_Plugin&utm_medium=plugin&utm_campaign=WP">Gateway Manager</a></strong> and click the "Get Code" icon to access your code.</p>
                    <p>
                    <textarea name="new_code" cols="75" rows="7"><?php echo stripslashes($_REQUEST['new_code']); ?></textarea>
                    </p>
                	</td>
                    <td style="padding-left:10px;"><b>Selected Posts:</b><br /><?=choosePosts(0, 'new');?><br /><small>*Hold CTRL to select multiple entries</small></td>          
                    <td style="padding-left:10px;"><b>Selected Pages:</b><br /><?=choosePages(0, 'new');?><br /><small>*Hold CTRL to select multiple entries</small></td>                        
                </tr>
                <tr>
                	<td colspan="3"><h3>User/Admin Bypass Settings:
        <select name="hideShow">
        	<option value="0" <? if($rowLocker['hideShow']==0) { echo 'selected="selected"'; }?>>No Preference</option>
            <option value="1" <? if($rowLocker['hideShow']==1) { echo 'selected="selected"'; }?>>Hide Locker For Logged In Users</option>
            <option value="2" <? if($rowLocker['hideShow']==2) { echo 'selected="selected"'; }?>>Hide Locker For Logged In Admins</option>
            <option value="3" <? if($rowLocker['hideShow']==3) { echo 'selected="selected"'; }?>>Hide For Logged In Users &amp; Admins</option>
        </select></h3></td>
                </tr>
             </table><br />
             <input type="submit" class="button-primary" value="Create Profile &raquo;" name="AWM_create_submit">
            </div>
        </div>
    </div>    
        
</form>

<? } ?>

<p style="padding-left:20px;"><b>Don't have an AWM account?</b> This plugin requires a valid account at AdWorkMedia.com to generate the locker code.<br />
      New users can activate a 5 Hour, 5% Earnings Bonus with the <strong>Promo Code</strong> <strong>"AWMEZWP"</strong>.<br /><br />
      <a target="_blank" href="https://www.adworkmedia.com/affiliate-publisher.php?utm_source=WP_Plugin&utm_medium=plugin&utm_campaign=WP"><strong>Click here to apply for a free AdWork Media account &raquo;</strong></a>
</p>

<div class="clear">&nbsp;</div>

<p style="text-align:center;">
	<small>*Profile settings may override each other</small><br /><br />
	Copyright &copy; 2011 - <?=date('Y');?> AdWork Media Group, LLC - <a target="_blank" href="https://www.adworkmedia.com/?utm_source=WP_Plugin&utm_medium=plugin&utm_campaign=WP">AdWorkMedia.com</a>
</p>
            
</div>    