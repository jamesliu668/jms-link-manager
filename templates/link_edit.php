<?php
    $action = "update-save";
?>
<div class="wrap">
<h1>
<?php
    echo __('Edit Link','jms-link-manager');
?>
</h1>

<form method="post" novalidate="novalidate" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'update_link' ); ?>
    <input type="hidden" name="id" value="<?php echo $result[0]["id"]; ?>"/>
    <input type="hidden" name="thumb_old" value="<?php echo $result[0]["thumb"]; ?>"/>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="link_name"><?php echo __('Name','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_name" type="text" id="link_name" value="<?php echo stripslashes($result[0]["name"]); ?>" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="link_desc"><?php echo __('Description','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_desc" type="text" id="link_desc" value="<?php echo stripslashes($result[0]["description"]); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="link"><?php echo __('link Link','jms-link-manager'); ?></label></th>
            <td>
                <input name="link" type="text" id="link" value="<?php echo stripslashes($result[0]["link"]); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="link_alias"><?php echo __('link Alias','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_alias" type="text" id="link_alias" value="<?php echo stripslashes($result[0]["alias"]); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="link_level"><?php echo __('Link Level','jms-link-manager'); ?></label></th>
            <td>
                <input name="link_level" type="text" id="link_level" value="<?php echo stripslashes($result[0]["level"]); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="cover-image"><?php echo __('封面','jms-link-manager'); ?></label></th>
            <td>
                <p>
                <?php
                    if(empty($result[0]["thumb"])) {
                        echo "<img src=\"".plugins_url( '/../thumb/image.jpg', __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    } else {
                        echo "<img src=\"".plugins_url( '/../thumb/'.$result[0]["thumb"], __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    }
                ?>
                </p>
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