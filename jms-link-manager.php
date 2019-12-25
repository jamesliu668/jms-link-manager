<?php
/* 
Plugin Name: JMS Link Manager
Plugin URI: http://www.jmsliu.com
Description: Create an link link in posts and pages
Author: James Liu
Version: 2.0.0
Author URI: http://jmsliu.com/
License: GPL2

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

global $jms_link_db_version;
$jms_link_db_version = '1.0';
    
//install database
register_activation_hook( __FILE__, 'installJMSLink' );

add_shortcode( 'jms-link-eng', 'jmsLinkShortCodeURL');
add_action( 'admin_menu', 'jmsLinkAdmin' );

# add a new query variable 
add_filter('query_vars', 'addJMSLinkQueryVar', 10, 1);
# parse the query variable in url
add_action('parse_request', 'parseJMSLinkQueryVar');

function installJMSLink() {
    global $jms_link_db_version;
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    $dbVersion = get_option( "jms_link_db_version", null );
    if ( $dbVersion == null ) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . "jms_link_manager";
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `description` VARCHAR(255) NULL,
                `link` VARCHAR(255) NOT NULL,
                `hash_id` VARCHAR(10) NOT NULL,
                `create_date` DATETIME NOT NULL,
                `update_date` DATETIME NOT NULL,
                `alias` VARCHAR(255) NULL,
                `level` INT UNSIGNED NULL,
                `thumb` VARCHAR(255) NULL,
                PRIMARY KEY (`id`))
                 ".$charset_collate.";";
        dbDelta( $sql );

        $table_name = $wpdb->prefix . "jms_link_tracker";
        $linked_table_name = $wpdb->prefix . "jms_link_manager";
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `link_id` INT NOT NULL,
                `access_date` DATETIME NOT NULL,
                `uid` INT UNSIGNED NULL,
                `open_id` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                INDEX `to-link-id_idx` (`link_id` ASC),
                CONSTRAINT `to-link-id`
                    FOREIGN KEY (`link_id`)
                    REFERENCES `".$linked_table_name."` (`id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                 ".$charset_collate.";";
        dbDelta( $sql );
        $dbVersion = "1.0";   
        add_option( "jms_link_db_version", $dbVersion );
    }
    
    if($dbVersion == "1.0") {
        $table_name = $wpdb->prefix . "jms_link_manager";
        $sql = "ALTER TABLE `$table_name` 
        CHANGE COLUMN `description` `description` MEDIUMTEXT CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_520_ci' NULL DEFAULT NULL ,
        CHANGE COLUMN `link` `link` MEDIUMTEXT CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_520_ci' NOT NULL ;";
        $wpdb->query( $sql  );
        $dbVersion = "1.1";   
        update_option( "jms_link_db_version", $dbVersion );
    }
}

function parseJMSLinkQueryVar($wp) {
    require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
    $linkController = new JMSLinkController();
    $linkController->parseRequest($wp);
}

function addJMSLinkQueryVar($vars) {
    #wechat arguments
    $vars[] = 'from';
    $vars[] = 'isappinstalled';
    $vars[] = 'link';
    $vars[] = 'sign';
    $vars[] = 'openid';
    return $vars;
}

function jmsLinkShortCodeURL($atts, $content="My Link") {
    $affLink = "<a rel=\"nofollow\" href=\"".site_url()."?link=".$atts["link"]."\">$content</a>";
    return $affLink;
}

function jmsLinkGetURL($linkID) {
    return site_url()."?link=".$linkID;
}

function jmsLinkAdmin() {
    add_menu_page(
        "链接管理",
        "链接管理",
        'manage_options',
        'jms-link-top',
        'jmsLinkAdminOptions' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page(
        'jms-link-top',
        '添加',
        '添加',
        'manage_options',
        'jms-link-add',
        'jmsLinkAddPage');
}

function jmsLinkAdminOptions() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    
    if( isset($_POST["action"]) ) {
        if($_POST[ "action" ] == "new-save") {
            if(check_admin_referer( 'new_link' )) {
                require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
                $linkController = new JMSLinkController();

                $name = trim($_POST[ "link_name" ]);
                $desc = trim($_POST[ "link_desc" ]);
                $link = trim($_POST[ "link" ]);
                $alias = trim($_POST[ "link_alias" ]);
                $level = trim($_POST[ "link_level" ]);
                $thumbFile = NULL;

                #check cover thumb
                if(file_exists($_FILES['cover-image']['tmp_name']) && is_uploaded_file($_FILES['cover-image']['tmp_name'])
                    && $_FILES['cover-image']["error"] == 0 && $linkController->checkThumbnailFile('cover-image')) {
                        $thumbFile = $linkController->uploadThumbnail('cover-image');
                }

                $linkController->addLink($name, $desc, $link, $alias, $level, $thumbFile);
            } else {
                echo __( '页面安全密钥已过期，请重新打开添加页面提交视频。' );
            }
        } else if($_POST[ "action" ] == "update-save") {
            if(check_admin_referer( 'update_link' )) {
                require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
                $linkController = new JMSLinkController();

                $id = trim($_POST[ "id" ]);
                $name = trim($_POST[ "link_name" ]);
                $desc = trim($_POST[ "link_desc" ]);
                $link = trim($_POST[ "link" ]);
                $alias = trim($_POST[ "link_alias" ]);
                $level = trim($_POST[ "link_level" ]);
                $thumbFile = trim($_POST[ "thumb_old" ]);;

                #check cover thumb
                if(file_exists($_FILES['cover-image']['tmp_name']) && is_uploaded_file($_FILES['cover-image']['tmp_name'])
                    && $_FILES['cover-image']["error"] == 0 && $linkController->checkThumbnailFile('cover-image')) {
                        $thumbFile = $linkController->uploadThumbnail('cover-image');
                }

                $linkController->updateLink($id, $name, $desc, $link, $alias, $level, $thumbFile);
            } else {
                echo __( '页面安全密钥已过期，请重新打开编辑页面提交视频。' );
            }
        }
    } else if( isset($_GET[ "action" ]) ) {
        if($_GET[ "action" ] == 'new') {
            require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
            $linkController = new JMSLinkController();
            $linkController->showAddForm();
        } else if($_GET[ "action" ] == 'edit') {
            if(isset($_GET["id"])) {
                $linkID = trim($_GET["id"]);
                require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
                $linkController = new JMSLinkController();
                $linkController->showEditForm($linkID);
            } else {
                echo __('未找到指定链接。', 'jms-link-manager');
            }
        } else if($_GET[ "action" ] == 'delete') {
            if(isset($_GET["id"])) {
                $linkID = trim($_GET["id"]);
                if(isset($_GET["_wpnonce"]) && wp_verify_nonce( trim($_GET["_wpnonce"]), 'delete-link-'.$linkID )) {
                    require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
                    $linkController = new JMSLinkController();
                    $linkController->deleteLink($linkID);
                } else {
                    echo __('页面安全密钥已过期，无法删除指定链接。', 'jms-link-manager');
                }
            } else {
                echo __('未找到指定链接。', 'jms-link-manager');
            }
        }
    } else {
        //show list
        require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
        $linkController = new JMSLinkController();

        $searchTerm = "";
        if( isset($_GET[ "s" ]) ) {
            $searchTerm = trim($_GET["s"]);
        }

        $paged = 1;
        if( isset($_GET[ "paged" ]) ) {
            $paged = (int)trim($_GET["paged"]);
        }

        $linkController->showLinkList($searchTerm, $paged);
    }
}

function jmsLinkAddPage() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    require_once(dirname(__FILE__)."/controllers/JMSLinkController.php");
    $linkController = new JMSLinkController();
    $linkController->showAddForm();
}

function jmsLinkAdminSub2() {
    global $wpdb, $wp;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

    $table_name = $wpdb->prefix . "posts";
    $sql = "SELECT * FROM `$table_name` WHERE `post_status`='publish' AND (
        `post_content` LIKE '%http://www.amazon.com/%' 
        OR `post_content` LIKE '%https://www.amazon.com/%'
        OR `post_content` LIKE '%http://amzn.to/%' 
    )";
    
    $result = $wpdb->get_results($sql, ARRAY_A);
    require_once(dirname(__FILE__)."/template/link_href_post_list.php");
}
?>
