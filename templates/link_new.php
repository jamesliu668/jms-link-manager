<?php
    global $wp;
    $currentURL = $wp->request."admin.php?page=jms-link-top";
    $action = "new-save";
?>
<div class="wrap">
<h1>
<?php
    echo __('Create New Link','jms-link-manager');
?>
</h1>

<form method="post" novalidate="novalidate" action="<?php echo $currentURL; ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'new_link' ); ?>
    
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="link_name"><?php echo __('Name','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_name" type="text" id="link_name" value="" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="link_desc"><?php echo __('Description','jms-link-manager'); ?></label></th>
            <td>
                <textarea name="link_desc" id="link_desc" rows="5" cols="50"></textarea>
            </td>
        </tr>
    
        <tr>
            <th scope="row"><label for="link"><?php echo __('Link','jms-link-manager'); ?></label></th>
            <td>
                <input name="link" type="text" id="link" value="" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="link_alias"><?php echo __('Link Alias','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_alias" type="text" id="link_alias" value="" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="link_level"><?php echo __('Link Level','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_level" type="text" id="link_level" value="" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="cover-image"><?php echo __('封面','jms-link-manager'); ?></label></th>
            <td>
                <input name="cover-image" type="file" id="cover-image">
                <p class="description" id="tagline-description"><?php echo __('上传封面图片，尺寸为160x90，320x180，640x360，或者1600x900大小','jms-link-manager'); ?></p>
            </td>
        </tr>
	</tbody>
</table>

<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save','jms-link-manager');?>">
<a class="button" style="margin-left: 10px;" onclick="window.history.back();"><?php echo __('Cancel','jms-link-manager');?></a>
</p>

</form>

</div>