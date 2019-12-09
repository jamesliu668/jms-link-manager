<?php
    require_once(dirname(__FILE__)."/../models/JMSCustomerModel.php");
    class JMSCustomerController {
        private $model;

        function __construct() {
            $this->model = new JMSCustomerModel();
        }

        function showCustomerList($searchTerm, $paged) {
            global $wpdb;

            $numberOfRecord = 10;
            $numberOfCustomer = $this->model->numberOfCustomer($searchTerm);
            $totalPage = ceil($numberOfCustomer / $numberOfRecord) ;
    
            if($paged > $totalPage) {
                $paged = $totalPage > 0 ? $totalPage : 1;
            } else if($paged < 1) {
                $paged = 1;
            }

            $result = $this->model->getCustomerList($paged, $numberOfRecord, $searchTerm);
            require_once(dirname(__FILE__)."/../templates/customer_list.php");
        }

        function showAddForm() {
            require_once(dirname(__FILE__)."/../templates/customer_new.php");
        }

        function showEditForm($customerID) {
            if(empty($customerID)) {
                echo __('未找到指定的客户', 'jms-customer-manager');
            } else {
                $result = $this->model->getCustomerByID($customerID);
                require_once(dirname(__FILE__)."/../templates/customer_edit.php");
            }
        }
        
        function addCustomer($name, $wechatID, $desc, $childInfo, $interest, $sellTier) {
            global $wpdb;
            if(empty($name)) {
                echo __('用户昵称不能为空', 'jms-customer-manager');
            } else {
                $allowToAdd = true;

                # check user avator
                // if($_FILES["customer_thumb"]["error"] == 0) {
                //     $allowToAdd = $this->checkThumbnailFile();
                // }

                if($allowToAdd) {
                    $currentDate = current_time('mysql', 0); //show local time
                    $result = $this->model->addCustomer($name, $wechatID, $desc, $childInfo, $interest, $sellTier, $currentDate);
                    if($result !== false) {
                        // update avator
                        // $lastid = $wpdb->insert_id;
                        // if($_FILES["customer_thumb"]["error"] == 0) {
                        //     $filename = $this->uploadThumbnail();
                        //     $result = $this->model->updateCustomer($lastid, $title, $vid, $categoryid, $desc, $isPublish, $currentDate, $filename);
                        // }
    
                        $message = sprintf(__('客户添加成功! <a href="%s">返回客户列表</a>', 'jms-customer-manager'), $wp->request."admin.php?page=jms-customer-manager-top");
                        echo "<h1>".$message."</h1>";
                    } else {
                        echo __('客户添加失败, 数据库操作失败!', 'jms-customer-manager');
                    }
                } else {
                    echo __('客户添加失败, 请检查客户信息是否符合要求!', 'jms-customer-manager');
                }
            }
        }

        function updateCustomer($id, $name, $wechatID, $desc, $childInfo, $interest, $sellTier) {
            $currentDate = current_time('mysql', 0); //show local time
            $result = $this->model->updateCustomer($id, $name, $wechatID, $desc, $childInfo, $interest, $sellTier, $currentDate);
            if($result !== false) {
                $message = sprintf(__('客户更新成功! <a href="%s">返回客户列表</a>', 'jms-customer-manager'), $wp->request."admin.php?page=jms-customer-manager-top");
                echo "<h1>".$message."</h1>";
            } else {
                echo __('客户更新失败, 数据库操作失败!', 'jms-customer-manager');
            }
        }

        function deleteCustomer($customerID) {
            $result = $this->model->getCustomerByID($customerID);
            if(count($result) > 0) {
                $thumbnail = trim($result[0]["thumb"]);
                if(!empty($thumbnail) && !$this->deleteThumbnail($result[0]["thumb"])) {
                    echo __('客户删除失败，找不到指定客户封面!', 'jms-customer-manager');
                }

                $result = $this->model->deleteCustomerByID($customerID);
                if($result !== false) {
                    $message = sprintf(__('客户删除成功! <a href="%s">返回客户列表</a>', 'jms-customer-manager'), $wp->request."admin.php?page=jms-customer-manager-top");
                    echo "<h1>".$message."</h1>";
                } else {
                    echo __('客户删除失败，找不到指定客户封面!', 'jms-customer-manager');
                }
            } else {
                echo __('客户删除失败，找不到指定客户!', 'jms-customer-manager');
            }
        }

        function mt_rand_str($length, $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
            $randomString = "";
            for($i = 0; $i < $length; $i++) {
                $randomString .= $c[mt_rand(0, strlen($c)-1)];
            }
            return $randomString;
        }

        function checkThumbnailFile() {
            $imageFileType = strtolower(pathinfo($_FILES["customer_thumb"]["name"], PATHINFO_EXTENSION));
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
                echo __('客户封面的格式必须为图片格式，包括jpg，png，jpeg!', 'jms-customer-manager');
                return false;
            }

            if ($_FILES["customer_thumb"]["size"] > 5000000) {
                echo __('客户封面的大小必须小于5M!', 'jms-customer-manager');
                return false;
            }

            return true;
        }

        function uploadThumbnail() {
            $targetFolder = dirname(__FILE__)."/../thumb/";
            $targetFileName = $this->mt_rand_str(32);
            $target_file = $targetFolder . $targetFileName;
            while (file_exists($target_file)) {
                $targetFileName = $this->mt_rand_str(32);
                $target_file = $targetFolder . $targetFileName;
            }
            
            if (move_uploaded_file($_FILES["customer_thumb"]["tmp_name"], $target_file)) {
                return $targetFileName;
            } else {
                return NULL;
            }
        }

        function deleteThumbnail($fileName) {
            $targetFolder = dirname(__FILE__)."/../thumb/";
            $target_file = $targetFolder . $fileName;
            if(file_exists($target_file)) {
                return unlink($target_file);
            }

            return true;
        }

        function search() {
            $query = trim($_REQUEST['q']);
            $start = trim($_REQUEST['start']);
            $count = 10; # search for 10 items

            $result = $this->model->search($query, $start, $count);
            if(count($result) > 0) {
                foreach ($result as $k => $value) {
                    $result[$k]['thumb'] = plugins_url( '/../thumb/'.$value['thumb'], __FILE__ );
                }
            }
            echo wp_json_encode($result);
        }
    }
?>