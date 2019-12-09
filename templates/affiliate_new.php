<?php
    $action = "add";
?>
<div class="wrap">
<h1>
<?php
    echo __('Create New Affiliate Link','jms-affiliate-link-engine');
?>
</h1>

<form method="post" novalidate="novalidate">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'add_affiliate_link' ); ?>
    
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="affiliate_name"><?php echo __('Name','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_name" type="text" id="affiliate_name" value="" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="affiliate_desc"><?php echo __('Description','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_desc" type="text" id="affiliate_desc" value="" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="affiliate_link"><?php echo __('Affiliate Link','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_link" type="text" id="affiliate_link" value="" class="regular-text">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="affiliate_alias"><?php echo __('Affiliate Alias','jms-affiliate-link-engine'); ?></label></th>
            <td>
                <input name="affiliate_alias" type="text" id="affiliate_alias" value="" class="regular-text">
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