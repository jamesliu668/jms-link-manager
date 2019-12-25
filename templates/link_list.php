<?php
    global $wp;
    $currentURL = $wp->request."admin.php?page=jms-link-top";
    $addLinkURL = $wp->request."admin.php?page=jms-link-top";

    if($searchTerm != '') {
        $currentURL .= "&s=".$searchTerm;
    }
    
    if($sortBy != '') {
        $currentURL .= "&sort-by-type=".$sortBy;
    }

    add_thickbox();
?>



<div class="wrap">
<h1>
<?php
    echo __('Link List','jms-link-manager');
?>
<a href="
<?php
echo $addLinkURL."&action=new";
?>" class="page-title-action">
<?php
    echo __('Add New Link','jms-link-manager');
?>
</a></h1>

<p>add short code [jms-link-eng link=1]My Link[/jms-link-eng]; or you can access the link as: <?php echo site_url()."?link=1"; ?></p>


<form id="jms-link-manager-filter" method="get" action="<?php echo $currentURL?>">

<p class="search-box">
	<label class="screen-reader-text" for="post-search-input">Search:</label>
	<input type="search" id="post-search-input" name="s" value="<?php echo $searchTerm; ?>">
	<input type="submit" id="search-submit" class="button" value="Search">
</p>

<input type="hidden" id="page" name="page" value="jms-link-top">

<div class="tablenav top">
    <div class="alignleft actions">
        <label class="screen-reader-text" for="sort-by-type">Sort by type</label>
        <select id="sort-by-type" name="sort-by-type">
            <option value="">ID</option>
            <option
                <?php
                    if($sortBy == "clicks") {
                        echo 'selected="selected"';
                    }
                ?>
             value="clicks">Clicks</option>
            
        </select>
        <input type="submit" name="sort_action" id="sort_action_submit" class="button" value="Sort by ">
    </div>

    <div class="tablenav-pages">
        <span class="displaying-num"><?php echo $totalRecord." Items"?></span>
        <span class="pagination-links">
        <?php
            if($paged == 1) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">«</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged=1">';
                echo '<span class="screen-reader-text">Start</span><span aria-hidden="true">«</span>';
                echo '</a>';

                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged - 1).'">';
                echo '<span class="screen-reader-text">Prev</span><span aria-hidden="true">‹</span>';
                echo '</a>';
            }
        ?>

<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label>
<input class="current-page" id="current-page-selector" name="paged" value="<?php echo $paged; ?>" size="1" aria-describedby="table-paging" type="text">
<span class="tablenav-paging-text">页，共<span class="total-pages"><?php echo $totalPage; ?></span>页</span></span>

        <?php
            if($paged == $totalPage) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">»</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged + 1).'">';
                echo '<span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span>';
                echo '</a>';

                echo '<a class="last-page" href="'.$currentURL.'&paged='.$totalPage.'">';
                echo '<span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span>';
                echo '</a>';
            }
        ?>


</span>
</div>
<br class="clear">
</div>

<h2 class="screen-reader-text">List</h2>

<table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
            <!--<input id="cb-select-all-1" type="checkbox">-->
        </td>
        <th scope="col" id="id" class="manage-column column-author">
            <?php echo __('ID','jms-link-manager');?>
        </th>
        <th scope="col" id="name" class="manage-column column-categories">
            <?php echo __('Name','jms-link-manager');?>
        </th>
        <th scope="col" id="author" class="manage-column column-categories" style="width:180px;">
            <?php echo __('Cover','jms-link-manager');?>
        </th>
        <th scope="col" id="description" class="manage-column column-categories">
            <?php echo __('Description','jms-link-manager');?>
        </th>
        <th scope="col" id="link" class="manage-column column-categories">
            <?php echo __('Link','jms-link-manager');?>
        </th>
        <th scope="col" id="alias" class="manage-column column-categories">
            <?php echo __('Alias','jms-link-manager');?>
        </th>
        <th scope="col" id="level" class="manage-column column-categories">
            <?php echo __('Level','jms-link-manager');?>
        </th>
        <th scope="col" id="date" class="manage-column column-categories">
            <?php echo __('Create Date','jms-link-manager');?>
        </th>
        <th scope="col" id="clicks" class="manage-column column-categories">
            <?php echo __('Number of Clicks','jms-link-manager');?>
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
			<th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-20">选择文章2</label>
                <!--<input id="cb-select-20" type="checkbox" name="post[]" value="20">-->
                <div class="locked-indicator"></div>
            </th>

            <td class="author column-author">
                <?php
                    echo $data["id"];
                ?>
            </td>

            <td class="title column-title has-row-actions column-primary page-title">
                <strong><a class="row-title" href="<?php echo $wp->request; ?>admin.php?page=jms-link-top&id=<?php echo $data["id"];?>&action=edit"><?php echo $data["name"]; ?></a></strong>

                <div class="row-actions">
                    <span class="edit"><a href="<?php echo $wp->request; ?>admin.php?page=jms-link-top&id=<?php echo $data["id"];?>&action=edit">
                    <?php echo __('Edit','jms-patient-profile'); ?>
                    </a> | </span>
                    <span class="trash"><a href="<?php echo $wp->request; ?>admin.php?page=jms-link-top&id=<?php echo $data["id"];?>&action=delete&_wpnonce=<?php echo wp_create_nonce( 'delete-link-'.$data["id"] );?>" class="submitdelete">移至回收站</a></span>
                </div>
            </td>

            <td class="categories column-categories">
                <?php
                    if(empty($data["thumb"])) {
                        echo "<img src=\"".plugins_url( '/../thumb/image.jpg', __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    } else {
                        echo "<img src=\"".plugins_url( '/../thumb/'.$data["thumb"], __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    }
                ?>
            </td>
    
            <td class="author column-author">
                <div id="<?php echo "desc-id-".$loopIndex; ?>" style="display:none;">
                    <p>
                        <?php
                            echo $data["description"];
                        ?>
                    </p>
                </div>

                <a href="#TB_inline?width=600&height=300&inlineId=<?php echo "desc-id-".$loopIndex; ?>" class="thickbox">Details</a>
            </td>
            
            <td class="categories column-categories">
                <a target="_blank" href="<?php echo $data["link"]; ?>">Click to Open</a>
            </td>

            <td class="categories column-categories">
                <?php echo $data["alias"]; ?>
            </td>

            <td class="categories column-categories">
                <?php echo $data["level"]; ?>
            </td>
            
            <td class="date column-date" data-colname="日期">
                <abbr title="<?php echo $data["create_date"]; ?>">
                    <?php echo $data["create_date"]; ?>
                </abbr>
            </td>

            <td class="categories column-categories">
                <?php echo $data["click"]; ?>
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

<div class="tablenav bottom">
    <div class="tablenav-pages">
        <span class="displaying-num"><?php echo $totalRecord."项目"?></span>
        <span class="pagination-links">
        <?php
            if($paged == 1) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">«</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged=1">';
                echo '<span class="screen-reader-text">首页</span><span aria-hidden="true">«</span>';
                echo '</a>';

                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged - 1).'">';
                echo '<span class="screen-reader-text">上一页</span><span aria-hidden="true">‹</span>';
                echo '</a>';
            }
        ?>

<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label>
<input class="current-page" id="current-page-selector" name="paged" value="<?php echo $paged; ?>" size="1" aria-describedby="table-paging" type="text">
<span class="tablenav-paging-text">页，共<span class="total-pages"><?php echo $totalPage; ?></span>页</span></span>

        <?php
            if($paged == $totalPage) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">»</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged + 1).'">';
                echo '<span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span>';
                echo '</a>';

                echo '<a class="last-page" href="'.$currentURL.'&paged='.$totalPage.'">';
                echo '<span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span>';
                echo '</a>';
            }
        ?>


</span>
</div>
<br class="clear">
</div>

</form>
<br class="clear">
</div>