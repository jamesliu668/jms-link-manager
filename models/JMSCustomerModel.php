<?php
    class JMSCustomerModel {
        private $tableName = "jms_customer";

        function numberOfCustomer($searchTerm) {
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

        function getCustomerList($paged, $numberOfRecord, $searchTerm) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $startIndex = ($paged - 1) * $numberOfRecord;
            $sql = "SELECT * FROM $table_name ORDER BY `id` ASC LIMIT $startIndex, $numberOfRecord";
            if($searchTerm != "") {
                $sql = "SELECT * FROM $table_name WHERE `title` LIKE '%".$searchTerm."%' or `name` LIKE '%".$searchTerm."%' ORDER BY `id` ASC LIMIT $startIndex,$numberOfRecord";
            }
            $result = $wpdb->get_results($sql, ARRAY_A);
            return $result;
        }

        function addCustomer($name, $wechatID, $desc, $childInfo, $interest, $sellTier, $currentDate, $sign) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            //insert
            $query = $wpdb->prepare(
                "INSERT INTO $table_name (`name`, wechat_id, `desc`, child_info, interest, tier, create_date, update_date, `sign`)
                    VALUES (%s, %s, %s, %s, %s, %d, %s, %s, %s)",
                array(
                    $name,
                    $wechatID,
                    $desc,
                    $childInfo,
                    $interest,
                    $sellTier,
                    $currentDate,
                    $currentDate,
                    $sign
                    )
            );

            $result = $wpdb->query($query);
            return $result;
        }

        function updateCustomer($id, $name, $wechatID, $desc, $childInfo, $interest, $sellTier, $currentDate, $age=NULL, $gender=NULL, $open_id=NULL, $phone=NULL) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $query = $wpdb->prepare(
                "UPDATE $table_name SET `name`=\"%s\", wechat_id=\"%s\", `desc`=\"%s\", child_info=\"%s\",  interest=\"%s\", tier=%d, update_date=\"%s\", age=%d, gender=%d, open_id=\"%s\", phone=\"%s\" WHERE id = %d",
                array(
                    $name,
                    $wechatID,
                    $desc,
                    $childInfo,
                    $interest,
                    $sellTier,
                    $currentDate,
                    $age,
                    $gender,
                    $open_id,
                    $phone,
                    $id
                    )
            );
            $result = $wpdb->query($query);

            return $result; //true or false
        }



        function getCustomerByID($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            return $wpdb->get_results("SELECT * FROM $table_name WHERE id=".(int)$id, ARRAY_A);
        }

        function getCustomerByWechatID($wid) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE wechat_id=\"%s\"",
                array($wid)
            );
            return $wpdb->get_results($query, ARRAY_A);
        }

        function getCustomerBySign($sign) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE sign=\"%s\"",
                array($sign)
            );
            return $wpdb->get_results($query, ARRAY_A);
        }

        function deleteCustomerByID($id) {
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
    }
?>