<?php
/* 
Plugin Name: JMS Affiliate Link Engine
Plugin URI: http://www.jmsliu.com
Description: Create an affiliate link in posts and pages
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

add_shortcode( 'jms-aff-link-eng', 'jmsLinkShortCodeURL');
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
                `alias` VARCHAR(255) NULL,
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
        $table_name = $wpdb->prefix . "jms_link_hash";
        $sql = "ALTER TABLE `$table_name` 
        CHANGE COLUMN `description` `description` MEDIUMTEXT CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_520_ci' NULL DEFAULT NULL ,
        CHANGE COLUMN `link` `link` MEDIUMTEXT CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_520_ci' NOT NULL ;";
        $wpdb->query( $sql  );
        $dbVersion = "1.1";   
        update_option( "jms_link_db_version", $dbVersion );
    }
}

function parseJMSLinkQueryVar($wp) {
    global $wpdb, $wp;
    if(!empty($wp->query_vars['jmsaff'])) {
        $jmsAffID = $wp->query_vars['jmsaff'];
        //get the real link from database by jmsAffID
        $table_name = $wpdb->prefix . 'jms_link_hash';
        $sql = "SELECT link FROM $table_name WHERE id=".(int)$jmsAffID;
        $result = $wpdb->get_results($sql, ARRAY_A);
        $link = $result[0]["link"];

        //set click record
        $tracker_table_name = $wpdb->prefix . 'jms_link_tracker';
        $result = $wpdb->query($wpdb->prepare(
            "
                INSERT INTO $tracker_table_name
                ( link_id, access_date )
                VALUES ( %d, %s )
            ",
            array(
                $jmsAffID,
                date("Y-m-d H:i:s")
            )
        ));

        //redirect
        redirect($link);
    }
}

function addJMSLinkQueryVar($vars) {
    $vars[] = 'jmsaff';
    $vars[] = 'uid';
    return $vars;
}

function jmsLinkShortCodeURL($atts, $content="Affiliate Link") {
    $affLink = "<a rel=\"nofollow\" href=\"".site_url()."?jmsaff=".$atts["aff-id"]."\">$content</a>";
    return $affLink;
}

function jmsLinkAdmin() {
    add_menu_page(
        "JMS Affiliate Tracker",
        "JMS Affiliate Tracker",
        'manage_options',
        'jms-affiliate-link-top',
        'jmsLinkAdminOptions' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page(
        'jms-affiliate-link-top',
        'Add Affiliate Link',
        'Add Affiliate Link',
        'manage_options',
        'jms-affiliate-link-sub1',
        'jmsLinkAdminSub1');

    // Add a submenu to the custom top-level menu:
    add_submenu_page(
        'jms-affiliate-link-top',
        'Find Amazon Link',
        'Find Amazon Link',
        'manage_options',
        'jms-affiliate-link-sub2',
        'jmsLinkAdminSub2');
}

function jmsLinkAdminOptions() {
    global $wpdb, $wp;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

        //show list
        $paged = 1;
        $numberOfRecord = 10;
        $searchTerm = "";
        if( isset($_GET[ "s" ]) ) {
            $searchTerm = trim($_GET["s"]);
        }

        $sortBy = "";
        if (isset($_GET["sort-by-type"])) {
            $sortBy = trim($_GET["sort-by-type"]);
        }

        $table_name = $wpdb->prefix . 'jms_link_hash';
        $tracker_table_name = $wpdb->prefix . 'jms_link_tracker';
        $wpdb->show_errors( true );

        $sql = "SELECT count(*) AS total FROM $table_name";
        if($searchTerm != "") {
            $sql = "SELECT count(*) AS total FROM $table_name WHERE `name` LIKE '%".$searchTerm."%'";
        }

        $totalNumber = $wpdb->get_results($sql, OBJECT);
        $totalRecord = $totalNumber[0]->total;
        $totalPage = ceil($totalRecord / $numberOfRecord) ;

        if( isset($_GET[ "paged" ]) ) {
            $paged = (int)trim($_GET["paged"]);
            if($paged > $totalPage) {
                $paged = $totalPage > 0 ? $totalPage : 1;
            } else if($paged < 1) {
                $paged = 1;
            }
        }
        $startIndex = ($paged - 1) * $numberOfRecord;
        $sql = "SELECT a.*, count(b.link_id) as click FROM $table_name as a LEFT JOIN $tracker_table_name as b on a.id=b.link_id GROUP BY a.`id` ORDER BY a.`id` ASC LIMIT $startIndex,$numberOfRecord";
        if($sortBy == "clicks") {
            $sql = "SELECT a.*, count(b.link_id) as click FROM $table_name as a LEFT JOIN $tracker_table_name as b on a.id=b.link_id GROUP BY a.`id` ORDER BY click DESC LIMIT $startIndex,$numberOfRecord";
        }
        
        if($searchTerm != "") {
            $sql = "SELECT a.*, count(b.link_id) as click FROM $table_name as a LEFT JOIN $tracker_table_name as b on a.id=b.link_id WHERE `name` LIKE '%".$searchTerm."%' GROUP BY a.`id` ORDER BY a.`id` ASC LIMIT $startIndex,$numberOfRecord";
            if($sortBy == "clicks") {
                $sql = "SELECT a.*, count(b.link_id) as click FROM $table_name as a LEFT JOIN $tracker_table_name as b on a.id=b.link_id WHERE `name` LIKE '%".$searchTerm."%' GROUP BY a.`id` ORDER BY click DESC LIMIT $startIndex,$numberOfRecord";
            }
        }
        $result = $wpdb->get_results($sql, ARRAY_A);
        require_once(dirname(__FILE__)."/template/affiliate_list.php");
}

function jmsLinkAdminSub1() {
    global $wpdb, $wp;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    if( isset($_POST["action"]) ) {
        if($_POST[ "action" ] == "add") {
            if(check_admin_referer( 'add_affiliate_link' )) {
                if(isset($_POST[ "affiliate_name" ]) && trim($_POST[ "affiliate_name" ]) != false
                    && isset($_POST[ "affiliate_link" ]) && trim($_POST[ "affiliate_link" ]) != false
                    && isset($_POST[ "affiliate_alias" ]) && trim($_POST[ "affiliate_alias" ]) != false
                ) {
                    $affName = trim($_POST[ "affiliate_name" ]);
                    $affDesc = trim($_POST[ "affiliate_desc" ]);
                    $affLink = trim($_POST[ "affiliate_link" ]);
                    $affAlias = trim($_POST[ "affiliate_alias" ]);

                    //save to database
                    $wpdb->show_errors( true );
                    $table_name = $wpdb->prefix . "jms_link_hash";
                    $result = $wpdb->query($wpdb->prepare(
                        "
                            INSERT INTO $table_name
                            ( name, description, link, hash_id, create_date, alias )
                            VALUES ( %s, %s, %s, %s, %s, %s)
                        ",
                        array(
                            $affName,
                            $affDesc,
                            $affLink,
                            mt_rand_str(10),
                            date("Y-m-d H:i:s"),
                            $affAlias
                        )
                    ));
                    
                    if($result !== false) {
                        $message = sprintf(__('Create a new affiliate link successfully! <a href="%s">Go to List</a>','jms-affiliate-link-engine'), $wp->request."admin.php?page=jms-affiliate-link-top");
                        echo "<h1>".$message."</h1>";
                    } else {
                        echo __('Insert New Affiliate Link, DB operation failed!','jms-affiliate-link-engine');
                    }
                } else {
                    echo __('Cannot find affiliate field data','jms-affiliate-link-engine');
                }
            } else {
                echo __( 'You do not have sufficient permissions to access this page.' );
            }
        } else if($_POST[ "action" ] == "edit") {
            if(check_admin_referer( 'edit_affiliate_link' )) {
                if(isset($_POST[ "id" ]) && trim($_POST[ "id" ]) != false
                    && isset($_POST[ "affiliate_link" ]) && trim($_POST[ "affiliate_link" ]) != false
                    && isset($_POST[ "affiliate_alias" ]) && trim($_POST[ "affiliate_alias" ]) != false
                    && isset($_POST[ "affiliate_alias" ]) && trim($_POST[ "affiliate_alias" ]) != false
                ) {
                    $affID = (int)trim($_POST[ "id" ]);
                    $affName = trim($_POST[ "affiliate_name" ]);
                    $affDesc = trim($_POST[ "affiliate_desc" ]);
                    $affLink = trim($_POST[ "affiliate_link" ]);
                    $affAlias = trim($_POST[ "affiliate_alias" ]);





                    //save to database
                    $wpdb->show_errors( true );
                    $table_name = $wpdb->prefix . "jms_link_hash";
                    $result = $wpdb->query($wpdb->prepare(
                        "UPDATE $table_name SET `name`=\"%s\", `description`=\"%s\", `link`=\"%s\", `alias`=\"%s\" WHERE `id`=%d",
                        array(
                            $affName,
                            $affDesc,
                            $affLink,
                            $affAlias,
                            $affID
                        )
                    ));
                    
                    if($result !== false) {
                        $message = sprintf(__('Update affiliate link successfully! <a href="%s">Go to List</a>','jms-affiliate-link-engine'), $wp->request."admin.php?page=jms-affiliate-link-top");
                        echo "<h1>".$message."</h1>";
                    } else {
                        echo __('Update Affiliate Link, DB operation failed!','jms-affiliate-link-engine');
                    }
                }
            }
        }
    } else if(isset($_GET["action"])) {
        if($_GET[ "action" ] == "edit" && isset($_GET[ "id" ]) && trim($_GET[ "id" ]) != false) {
            $table_name = $wpdb->prefix . "jms_link_hash";
            $sql = "SELECT * FROM $table_name WHERE `id`=".(int)$_GET[ "id" ];
            $result = $wpdb->get_results($sql, ARRAY_A);
            if($wpdb->num_rows > 0) {
                require_once(dirname(__FILE__)."/template/affiliate_edit.php");
            }
        }
    } else {
        //show new form
        require_once(dirname(__FILE__)."/template/affiliate_new.php");
    }
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
    require_once(dirname(__FILE__)."/template/affiliate_href_post_list.php");
}

/**
 * status code: 301 permanent, 302 temporary, 303 other
 */
function redirect($url, $statusCode = 302) {
   header('Location: ' . $url, true, $statusCode);
   die();
}

function mt_rand_str($length, $s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
    $r = "";
    for($i=0; $i<$length; $i++) {
        $r .= $s[mt_rand(0, strlen($s)-1)];
    }
    return $r;
}
?>
