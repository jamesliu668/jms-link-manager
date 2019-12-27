<?php
    require_once(dirname(__FILE__)."/../models/JMSLinkModel.php");
    require_once(dirname(__FILE__)."/../models/JMSCustomerModel.php");
    class JMSLinkController {
        private $model;

        function __construct() {
            $this->model = new JMSLinkModel();
            $this->customerModel = new JMSCustomerModel();
        }

        function showLinkList($searchTerm, $paged) {
            global $wpdb;

            $numberOfRecord = 10;
            $numberOfLink = $this->model->numberOfLink($searchTerm);
            $totalPage = ceil($numberOfLink / $numberOfRecord) ;
    
            if($paged > $totalPage) {
                $paged = $totalPage > 0 ? $totalPage : 1;
            } else if($paged < 1) {
                $paged = 1;
            }

            $result = $this->model->getLinkList($paged, $numberOfRecord, $searchTerm);
            require_once(dirname(__FILE__)."/../templates/link_list.php");
        }

        function showAddForm() {
            require_once(dirname(__FILE__)."/../templates/link_new.php");
        }

        function showEditForm($linkID) {
            if(empty($linkID)) {
                echo __('未找到指定的链接', 'jms-link-manager');
            } else {
                $result = $this->model->getLinkByID($linkID);
                require_once(dirname(__FILE__)."/../templates/link_edit.php");
            }
        }
        

        function addLink($name, $desc, $link, $alias, $level, $thumb) {
            if(empty($name)) {
                echo __('链接名不存在', 'jms-link-manager');
            } else {
                $isUnique = false;
                $hash = NULL;
                while(!$isUnique) {
                    $hash = $this->mt_rand_str(10);
                    $result = $this->model->getLinkByHash($hash);
                    if(count($result) == 0) {
                        $isUnique = true;
                    }
                }

                $currentDate = current_time('mysql', 0); //show local time                    
                $result = $this->model->addLink($name, $desc, $link, $alias, $level, $currentDate, $thumb, $hash);
                //$lastid = $wpdb->insert_id;
                if($result !== false) {
                    $message = sprintf(__('链接添加成功! <a href="%s">返回链接列表</a>', 'jms-link-manager'), $wp->request."admin.php?page=jms-link-top");
                    echo "<h1>".$message."</h1>";
                } else {
                    echo __('链接添加失败, 数据库操作失败!', 'jms-link-manager');
                }
            }
        }

        function updateLink($id, $name, $desc, $link, $alias, $level, $thumb) {
            if(empty($id)) {
                echo __('链接不存在', 'jms-link-manager');
            } else {
                $result = $this->model->getLinkByID($id);
                if(count($result) > 0) {
                    $currentDate = current_time('mysql', 0); //show local time
                    $result = $this->model->updateLink($id, $name, $desc, $link, $alias, $level, $currentDate, $thumb);
                    if($result !== false) {
                        $message = sprintf(__('链接更新成功! <a href="%s">返回链接列表</a>', 'jms-link-manager'), $wp->request."admin.php?page=jms-link-top");
                        echo "<h1>".$message."</h1>";
                    } else {
                        echo __('链接更新失败, 数据库操作失败!', 'jms-link-manager');
                    }
                } else {
                    echo __('链接不存在', 'jms-link-manager');
                }
            }
        }

        function deleteLink($linkID) {
            $result = $this->model->getLinkByID($linkID);
            if(count($result) > 0) {
                $thumbnail = trim($result[0]["thumb"]);
                if(!empty($thumbnail) && !$this->deleteThumbnail($result[0]["thumb"])) {
                    echo __('链接删除失败，找不到指定链接封面!', 'jms-link-manager');
                }

                $result = $this->model->deleteLinkByID($linkID);
                if($result !== false) {
                    $message = sprintf(__('链接删除成功! <a href="%s">返回链接列表</a>', 'jms-link-manager'), $wp->request."admin.php?page=jms-link-top");
                    echo "<h1>".$message."</h1>";
                } else {
                    echo __('链接删除失败，找不到指定链接封面!', 'jms-link-manager');
                }
            } else {
                echo __('链接删除失败，找不到指定链接!', 'jms-link-manager');
            }
        }

        function mt_rand_str($length, $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
            $randomString = "";
            for($i = 0; $i < $length; $i++) {
                $randomString .= $c[mt_rand(0, strlen($c)-1)];
            }
            return $randomString;
        }

        function checkThumbnailFile($fileKey) {
            $imageFileType = strtolower(pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION));
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
                echo __('链接封面的格式必须为图片格式，包括jpg，png，jpeg!', 'jms-link-manager');
                return false;
            }

            if ($_FILES[$fileKey]["size"] > 5000000) {
                echo __('链接封面的大小必须小于5M!', 'jms-link-manager');
                return false;
            }

            return true;
        }

        function uploadThumbnail($fileKey) {
            $targetFolder = dirname(__FILE__)."/../thumb/";
            $targetFileName = $this->mt_rand_str(32);
            $target_file = $targetFolder . $targetFileName;
            while (file_exists($target_file)) {
                $targetFileName = $this->mt_rand_str(32);
                $target_file = $targetFolder . $targetFileName;
            }
            
            if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $target_file)) {
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

        function searchLinkByID() {
            $query = trim($_REQUEST['q']);
            $result = $this->model->getLinkByID($query);
            echo wp_json_encode($result);
        }

        function parseRequest($wp) {
            if(!empty($wp->query_vars['link'])
                && !empty($wp->query_vars['sign'])) {
                $linkID = $wp->query_vars['link'];
                $sign = $wp->query_vars['sign'];

                $from = $wp->query_vars['from'];
                $isappinstalled = $wp->query_vars['isappinstalled'];

                $result = $this->model->getLinkByHash($linkID);
                if(count($result) > 0) {
                    $openID = $wp->query_vars['openid'];
                    if($openID != NULL) {
                        $accessDate = current_time('mysql', 0); //show local time
                        $user = $this->customerModel->getCustomerBySign($sign);
                        if(count($user) > 0) {
                            $this->model->addTrackRecord($result[0]["id"], $user[0]["id"], $openID, $accessDate);
                        }
                    } else {
                        $url = trim($result[0]["link"]);
                        $url = $url."?link=$linkID&sign=$sign";
                        if($from != NULL) {
                            $url = $url."&from=$from";
                        }

                        if($isappinstalled != NULL) {
                            $url = $url."&isappinstalled=$isappinstalled";
                        }

                        $this->redirect($url, 301);
                    }
                }
            }
        }

        
        /**
         * status code: 301 permanent, 302 temporary, 303 other
         */
        function redirect($url, $statusCode = 302) {
            header('Location: ' . $url, true, $statusCode);
            die();
        }
    }
?>