<?php
    $action = "edit";
?>
<div class="wrap">
<h1>
<?php
    echo __('Edit Affiliate Link','jms-affiliate-link-engine');
?>
</h1>

<form method="post" novalidate="novalidate">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'edit_affiliate_link' ); ?>
    <input type="hidden" name="id" value="<?php echo $result[0]["id"]; ?>"/>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="affiliate_name"><?php echo __('Name','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_name" type="text" id="affiliate_name" value="<?php echo stripslashes($result[0]["name"]); ?>" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="affiliate_desc"><?php echo __('Description','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_desc" type="text" id="affiliate_desc" value="<?php echo stripslashes($result[0]["description"]); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="affiliate_link"><?php echo __('Affiliate Link','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_link" type="text" id="affiliate_link" value="<?php echo stripslashes($result[0]["link"]); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="affiliate_alias"><?php echo __('Affiliate Alias','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_alias" type="text" id="affiliate_alias" value="<?php echo stripslashes($result[0]["alias"]); ?>" class="regular-text">
            </td>
        </tr>
	</tbody>
</table>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save','jms-affiliate-link-engine');?>">
<a class="button" style="margin-left: 10px;" onclick="window.history.back();"><?php echo __('Cancel','jms-affiliate-link-engine');?></a>
</p>

</form>

</div>