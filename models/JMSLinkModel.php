<?php
    class JMSLinkModel {
        private $tableName = "jms_link_manager";
        private $trackTableName = "jms_link_tracker";

        function numberOfLink($searchTerm) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $sql = "SELECT count(*) AS total FROM $table_name";
            if($searchTerm != "") {
                $sql = "SELECT count(*) AS total FROM $table_name WHERE `wechat_id` LIKE '%".$searchTerm."%' or `name` LIKE '%".$searchTerm."%'";
            }
            $totalNumber = $wpdb->get_results($sql, OBJECT);
            return $totalNumber[0]->total;
        }

        function getLinkList($paged, $numberOfRecord, $searchTerm) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $startIndex = ($paged - 1) * $numberOfRecord;
            $sql = "SELECT * FROM $table_name ORDER BY `id` ASC LIMIT $startIndex, $numberOfRecord";
            if($searchTerm != "") {
                $sql = "SELECT * FROM $table_name WHERE `description` LIKE '%".$searchTerm."%' or `name` LIKE '%".$searchTerm."%' ORDER BY `id` ASC LIMIT $startIndex,$numberOfRecord";
            }
            $result = $wpdb->get_results($sql, ARRAY_A);
            return $result;
        }

        function addLink($name, $desc, $link, $alias, $level, $currentDate, $thumb) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            //insert
            $query = $wpdb->prepare(
                "INSERT INTO $table_name (`name`, `description`, `link`, create_date, update_date, alias, `level`, thumb)
                    VALUES (%s, %s, %s, %s, %s, %s, %d, %s)",
                array(
                    $name,
                    $desc,
                    $link,
                    $currentDate,
                    $currentDate,
                    $alias,
                    $level,
                    $thumb,
                    )
            );

            $result = $wpdb->query($query);
            return $result;
        }


        function updateLink($id, $name, $desc, $link, $alias, $level, $currentDate, $thumb) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $query = $wpdb->prepare(
                "UPDATE $table_name SET `name`=\"%s\", `description`=\"%s\", `link`=\"%s\", update_date=\"%s\",  alias=\"%s\", `level`=%d, thumb=\"%s\" WHERE id = %d",
                array(
                    $name,
                    $desc,
                    $link,
                    $currentDate,
                    $alias,
                    $level,
                    $thumb,
                    $id
                    )
            );
            $result = $wpdb->query($query);
            return $result; //true or false
        }

        function getLinkByID($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            return $wpdb->get_results("SELECT * FROM $table_name WHERE id=".(int)$id, ARRAY_A);
        }

        function deleteLinkByID($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            $result = $wpdb->query($wpdb->prepare(
                "DELETE FROM $table_name WHERE `id` = %d",
                array($id)
            ));

            return $result;
        }

        function search($query, $start, $count) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            if(empty($start)) {
                $start = 0;
            }

            if(empty($query)) {
                return $wpdb->get_results("SELECT id, title, description, update_date, vid, thumb FROM $table_name WHERE published=1 ORDER BY id DESC LIMIT $start, $count", ARRAY_A);
            } else {
                return $wpdb->get_results("SELECT id, title, description, update_date, vid, thumb FROM $table_name WHERE published=1 AND `title` LIKE '%".$query."%' ORDER BY id DESC LIMIT $start, $count", ARRAY_A);
            }
        }

        function addTrackRecord($linkID, $uid, $openID, $accessDate) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->trackTableName;
            $wpdb->show_errors( true );

            //insert
            $query = $wpdb->prepare(
                "INSERT INTO $table_name (`link_id`, `uid`, `open_id`, access_date)
                    VALUES (%d, %d, %s, %s)",
                array(
                    $linkID,
                    $uid,
                    $openID,
                    $accessDate
                    )
            );

            $result = $wpdb->query($query);
            return $result;
        }
    }
?>