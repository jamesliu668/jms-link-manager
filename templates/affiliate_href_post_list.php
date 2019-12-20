<?php
    global $wp;
    $editURL = $wp->request."post.php?post=%d&action=edit";
    $viewURL = site_url()."/?p=%d";
?>

<div class="wrap">
<h1>
<?php
    echo __('Article List','jms-affiliate-link-engine');
?>
</h1>


<form id="jms-affiliate-link-engine-filter" method="get" action="<?php echo $currentURL?>">



<h2 class="screen-reader-text">List</h2>

<table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
        <th scope="col" id="id" class="manage-column column-author">
            <?php echo __('ID','jms-affiliate-link-engine');?>
        </th>
        <th scope="col" id="title" class="manage-column column-categories">
            <?php echo __('Title','jms-affiliate-link-engine');?>
        </th>
        <th scope="col" id="action" class="manage-column column-categories">
            <?php echo __('Action','jms-affiliate-link-engine');?>
        </th>
    </tr>
	</thead>

	<tbody id="the-list">
    <?php
    if(isset($result)) {
        $loopIndex = 0;
        foreach($result as $data) {
            $loopIndex++;
    ?>
		<tr id="post-20" class="iedit author-self level-0 post-20 type-post status-publish format-standard hentry category-uncategorized">
            <td class="author column-author">
                <?php
                    echo $data["ID"];
                ?>
            </td>

            <td class="author column-author">
                <a target="_blank" href="
                    <?php
                        printf($editURL, $data["ID"]);
                    ?>
                "><?php echo $data["post_title"]; ?></a>
            </td>

            <td class="author column-author">
                <span>
                <a target="_blank" href="
                    <?php
                        printf($viewURL, $data["ID"]);
                    ?>
                ">View</a>
                </span>
            </td>
        </tr>
    <?php
        }
    }
    ?>
	</tbody>

	<tfoot>
   	</tfoot>
</table>


</form>
<br class="clear">
</div>