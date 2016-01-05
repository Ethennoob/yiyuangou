<?php

    namespace AppMain\controller\Api;
    use \System\BaseClass;

    class WechatController extends BaseClass {

        private $weixinData;    //微信接受的信息
        private $weObj;   //实例微信类对象
        private $sceneID;  //扫描的场景ID
        private $isFans = 0;
        private $userInfo = '';  //关注人信息（已有用户）

        public function index() {
            $weObj = new \System\lib\Wechat\Wechat($this->config('WEIXIN_CONFIG'));
            
            $this->weObj = $weObj;
            //对接验证
            $weObj->valid();

            $type = $weObj->getRev()->getRevType(); // 获取数据类型
            $data = $weObj->getRev()->getRevData(); // 获取微信服务器发来的信息
            $this->weixinData = $data; 
            //$openid = $this->weixinData['FromUserName'];
            //记录日志
            file_put_contents("Log/wechat.txt",json_encode($data)."  日期：".date("Y-m-d H:i:s").PHP_EOL,FILE_APPEND);

            switch ($type) {
                case $weObj::MSGTYPE_TEXT : // 文本类型
                    // 记录文本消息到数据库
                    $this->_saveTextMsg();
                    //---特殊操作优先---
                    // 关键字回复
                    $result = $this->_getKeywordReply();
                    //自动回复
                    $auotMsg = $this->_getAutoReply();
                    
                    break;
                case $weObj::MSGTYPE_EVENT : // 事件类型
                    if ($data ['Event'] == "subscribe") { // 关注事件

                        //添加粉丝操作
                        //$this->_addFans();

                        //关注回复
                        $this->_getFollowReply();
                    } elseif ($data ['Event'] == "unsubscribe") { // 取消关注事件
                        //记录取消关注事件
                       $this->_saveFollowMsg();

                        //粉丝操作
                        $this->_updateFans();
                    } elseif ($data ['Event'] == "LOCATION") { // 获取上报的地理位置事件
                        //记录用户自动上传的地址位置
                        $this->_saveLocationMsgAuto();
                    } elseif ($data ['Event'] == "CLICK") { // 自定义菜单
                        // 记录自定义菜单消息
                        $this->_saveMenuMsg();

                        //菜单点击事件
                        $content = $data ["EventKey"];
                        $result = $this->_getKeywordReply($content);
                    } elseif ($data ['Event'] == "VIEW") { // 点击菜单跳转链接时的事件推送
                        // 记录自定义菜单消息
                        $this->_saveMenuMsg();
                    } elseif ($data['Event'] == "SCAN") {   //扫二维码进入公众号
                        // 记录自定义菜单消息
                        $this->_saveScanMsg();

                    } elseif (!empty($data['KfAccount'])) {  //客服时间
                        $this->_saveKfMsg();
                    }
                    break;
                case $weObj::MSGTYPE_IMAGE : // 图片类型
                    // 记录图片消息
                    $this->_saveImgMsg();
                    break;
                case $weObj::MSGTYPE_LOCATION : // 地理位置类型
                    $this->_saveLocationMsg();
                    $weObj->text("地理位置已接收")->reply();
                    break;

                case $weObj::MSGTYPE_LINK : // 链接消息
                    $this->_saveLinkMsg();
                    $weObj->text("链接消息已接收")->reply();
                    break;
                default :
                    $weObj->text("help info")->reply();
            }
        }

        /*     * ****收取微信信息 start****** */

        /**
         * 记录文本信息
         */
        private function _saveTextMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                'msg_style' => 1,
                'from_name' => $weixin_data ['FromUserName'],
                'to_name' => $weixin_data ['ToUserName'],
                'create_time' => $weixin_data ['CreateTime'],
                'msg_type' => $weixin_data ['MsgType'],
                'content' => $weixin_data ['Content'],
                'msg_id' => $weixin_data ['MsgId']
            );

            $result = $this->table('wechat_receive')->save($data);

            return $result;
        }

        /**
         * 记录菜单事件
         */
        private function _saveMenuMsg() {
            $weixin_data = $this->weixinData;
            $data = array(
                "msg_style" => 2,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "event" => $weixin_data ['Event'],
                "event_key" => $weixin_data ['EventKey']
            );

            $result = $this->table("wechat_receive")->save($data);

            return $result;
        }

        /**
         * 记录链接消息
         */
        private function _saveLinkMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 1,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "description" => $weixin_data ['Description'],
                "url" => $weixin_data ['Url'],
                "msg_id" => $weixin_data ['MsgId']
            );

            $result = $this->table("wechat_receive")->save($data);

            return $result;
        }

        /**
         * 记录图片信息
         */
        private function _saveImgMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 1,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "pic_url" => $weixin_data ['PicUrl'],
                "url" => $weixin_data ['Url'],
                "msg_id" => $weixin_data ['MsgId']
            );

            $result = $this->table("wechat_receive")->save($data);

            return $result;
        }

        /**
         *  记录关注，取消关注
         */
        private function _saveFollowMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 2,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "event" => $weixin_data ['Event'],
            );

            if (!empty($weixin_data ['EventKey'])) {
                $data['event_key'] = $weixin_data ['EventKey'];
                $data['ticket'] = $weixin_data ['Ticket'];
                $scArray = explode("_", $weixin_data ['EventKey']);
                $data['scene_id'] = $scArray[1];
                $this->sceneID = $data['scene_id'];
            }

            $result = $this->table("wechat_receive")->save($data);
            return $result;
        }

        /**
         * 记录用户主动发送的地址位置
         */
        private function _saveLocationMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 1,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "location_x" => $weixin_data ['Location_X'],
                "location_y" => $weixin_data ['Location_Y'],
                "scale" => $weixin_data ['Scale'],
                "label" => $weixin_data ['Label'],
                "msg_id" => $weixin_data ['MsgId'],
            );

            $result = $this->table("wechat_receive")->save($data);
            return $result;
        }

        /**
         * 记录用户自动上传的地址位置
         */
        private function _saveLocationMsgAuto() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 2,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "event" => $weixin_data ['Event'],
                "location_x" => $weixin_data ['Longitude'],
                "location_y" => $weixin_data ['Latitude'],
                "scale" => $weixin_data ['Precision'],
            );

            $result = $this->table("wechat_receive")->save($data);
            return $result;
        }

        /**
         * 记录扫码信息
         */
        private function _saveScanMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 2,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "event" => $weixin_data ['Event'],
                "event_key" => $weixin_data ['EventKey'],
                "ticket" => $weixin_data ['Ticket'],
                "scene_id" => $weixin_data ['EventKey'],
            );

            $result = $this->table("wechat_receive")->save($data);
            return $result;
        }

        /**
         * 记录客服信息
         */
        private function _saveKfMsg() {
            $weixin_data = $this->weixinData;

            $data = array(
                "msg_style" => 2,
                "from_name" => $weixin_data ['FromUserName'],
                "to_name" => $weixin_data ['ToUserName'],
                "create_time" => $weixin_data ['CreateTime'],
                "msg_type" => $weixin_data ['MsgType'],
                "event" => $weixin_data ['Event'],
                "kf_account" => $weixin_data['KfAccount']
            );

            $result = $this->table("wechat_receive")->save($data);
            return $result;
        }

        /*     * ****收取微信信息 end****** */

        /**
         * 关键字回复
         * $reply "reply" or "custom"
         */
        private function _getKeywordReply($keyword = '', $reply = "reply") {
            if (empty($keyword)) {
                $keyword = $this->weixinData['Content'];
            }

            $isKeyword = $this->table("wechat_response")
                    ->where(array("is_on" => 1, "keywords" => $keyword, "type" => 0))
                    ->get(null, true);

            //error_log($this->table("wechat_response")->getLastSql());

            if (!$isKeyword) {
                return false;
            }

            if ($isKeyword['rsp_type'] == 0) {   //文本回复
                if ($reply == "reply") {
                    $this->weObj->text($isKeyword['text'])->reply();
                } elseif ($reply == "custom") {
                    $data = array(
                        "touser" => $this->weixinData['FromUserName'],
                        "msgtype" => "text",
                        "text" => array(
                            "content" => $isKeyword['text']
                        )
                    );
                    $this->weObj->sendCustomMessage($data);
                }

                exit;
            } elseif ($isKeyword['rsp_type'] == 1) {   //图文回复
                $news_temp = $this->table("wechat_news")->where(array("id" => $isKeyword['news'], 'is_on' => 1))->order('sort asc')->get();

                foreach ($news_temp as $a) {
                    $news[] = array(
                        'Title' => $a['title'],
                        'Description' => $a['desc'],
                        'PicUrl' =>$a['img_thumb'],
                        // 'PicUrl' => $this->config('IMG_PATH') . $a['img_thumb_360'],
                        'Url' => empty($a['url']) ? $this->config('WEIXIN_ARTICLE_PATH') . $a['id'] : $a['url']
                    );
                }

                error_log(json_encode($news));

                if ($reply == "reply") {
                    $this->weObj->news($news)->reply();
                } elseif ($reply == "custom") {
                    foreach ($news as $key => $a) {
                        $news[$key] = array(
                            "title" => $a['Title'],
                            "description" => $a['Description'],
                            "url" => $a['Url'],
                            "picurl" => $a['PicUrl']
                        );
                    }

                    $data = array(
                        "touser" => $this->weixinData['FromUserName'],
                        "msgtype" => "news",
                        "news" => array(
                            "articles" => $news
                        )
                    );

                    $this->weObj->sendCustomMessage($data);
                }

                exit;
            }
        }
        private function sendCustomMessage($data){
            if (!$this->access_token && !$this->checkAuth()) return false;
            $result = $this->http_post(self::API_URL_PREFIX.self::CUSTOM_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));
            if ($result)
            {
                $json = json_decode($result,true);
                if (!$json || !empty($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                return $json;
            }
            return false;
        }

        /**
         * 自动回复
         */
        private function _getAutoReply() {
            $autoReply = $this->table("wechat_response")->where(array("is_on" => 1, "type" => 1))->get(null, true);

            if (!$autoReply) {
                return false;
            }

            if ($autoReply['rsp_type'] == 0) {   //文本回复
                $this->weObj->text($autoReply['text'])->reply();
                exit;
            }

            if ($autoReply['rsp_type'] == 1) {   //图文回复
                $news_temp = $this->table("wechat_news")->where(array("media_id" => $autoReply['news'], 'is_on' => 1))->order('sort asc')->get();

                foreach ($news_temp as $a) {
                    $news[] = array(
                        'Title' => $a['title'],
                        'Description' => $a['desc'],
                        'PicUrl' => $a['img_thumb_360'],
                        'Url' => empty($a['url']) ? empty($a['url']) ? $this->config('WEIXIN_ARTICLE_PATH') . $a['id'] : $a['url'] : $a['url']
                    );
                }
                $this->weObj->news($news)->reply();
                exit;
            }
        }

        /**
         * 获取关注回复
         */
        private function _getFollowReply() {
            if (!empty($this->userInfo)) {
                //活动回复
                if (!empty($user['parent'] && !empty($user['parent_share_type']) && !empty($user['activity_id']))) {
                    //是否在进行活动
                    $isActivity = $this->table('activity_share_' . $user['parent_share_type'])->where(['is_on' => 1, 'is_show' => 1])->get(['id', 'start_time', 'end_time'], true);
                    if ($isActivity) {
                        if (time() > $isActivity['start_time'] && time() < $isActivity['end_time']) {
                            switch ($user['parent_share_type']) {
                                case 1 :
                                    $content = '欢迎关注广融在线！你已获得参与广融活动“邀好友送特权利率”活动的资格。马上<a href="' . getHost() . '/activity/activity_1_1.html?activity_id=' . $user['activity_id'] . '">点击这里</a>进行领取！';
                                    break;
                                case 2 :
                                    //$content='欢迎关注广融在线！你可参加“投资送现金活动”。马上<a href="'.getHost().'/activity/activity_2_1.html?activity_id='.$user['activity_id'].'">点击这里</a>了解活动详情！';
                                    $content = '对！骚年你没关注错 ！' . "\r\n";
                                    $content.='这是稳拿8%~13%收益的赚钱平台！' . "\r\n";
                                    $content.='投资随投随取，快捷方便！' . "\r\n";
                                    $content.='收益完胜各大银行和各类宝宝！' . "\r\n";
                                    $content.='快来参与日赚百元计划“领100元红包”吧！' ."\r\n";
                                    $content.='红包有限！先到先得哦~' . "\r\n";
                                    $content.='<a href="' . getHost() . '/activity/activity_2_1.html?activity_id=' . $user['activity_id'] . '">点击领100元红包</a>';
                                    break;
                            }
                        }
                    }
                }
            }
            $followReply = $this->table("wechat_response")->where(array("type" => 2, 'is_on' => 1))->get(null, true);

            if (!$followReply) {
                return false;
            }

            if ($followReply['rsp_type'] == 0) {   //文本回复
                $this->weObj->text($followReply['text'])->reply();
                exit;
            }

            if ($followReply['rsp_type'] == 1) {   //图文回复(未实现)
                // $news_temp = $this->table("wechat_news")->where(array("id" => $followReply['news'], 'is_on' => 1))->order('sort_order asc')->get(null, true);
                $news_temp = $this->table("wechat_news")->where(array("id" => $followReply['news'], 'is_on' => 1))->get();//get()内不能
                // $this->weObj->text("text")->reply();exit;
                foreach ($news_temp as $a) {
                    $news[] = array(
                        'Title' => $a['title'],
                        'Description' => $a['desc'],
                        // 'PicUrl' => $this->config('IMG_PATH') . $a['img_thumb_360'],
                        'PicUrl' => $a['img_thumb_360'],
                        'Url' => empty($a['url']) ? empty($a['url']) ? $this->config('WEIXIN_ARTICLE_PATH') . $a['id'] : $a['url'] : $a['url']
                    );
                }

                $this->weObj->news($news)->reply();
                exit;
            }
        }

        /**
         * 增加一个粉丝
         */
        private function _addFans() {
            $openid = $this->weixinData['FromUserName'];

            //更新或者插入用户表
            $isUser = $this->isUser();
            if ($isUser) {
                $this->userInfo = $isUser;
                $data = array(
                    "is_follow" => 1,
                    "update_time" => time()
                );

                $update = $this->table("user")->where(['id' => $isUser['id']])->update($data);

                $userID = $isUser['id'];
            } else {
                //获取用户信息
                $fans = $this->weObj->getUserInfo($openid);

                if (!$fans) {
                    return false;
                }
                $this->fans = $fans;
                $data = array(
                    "openid" => $openid,
                    "nickname" => $fans['nickname'],
                    //"sex" => $fans['sex'],
                    "user_img" => $fans['headimgurl'],
                    //"area" => $fans['country'].$fans['province'].$fans['city'],
                    "area" => $fans['province'].$fans['city'],
                    "is_follow" => 1,
                    "add_time" => time()
                );
                $add = $this->table('user')->save($data);
                $userID = $add;
            }
        }

        /**
         * 获取关注者详细信息
         */
        private function getUserInfo($openid){
            if (!$this->access_token && !$this->checkAuth()) return false;
            $result = $this->http_get(self::API_URL_PREFIX.self::USER_INFO_URL.'access_token='.$this->access_token.'&openid='.$openid);
            if ($result)
            {
                $json = json_decode($result,true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                return $json;
            }
            return false;
        }

        /**
         * 粉丝取消关注
         */
        private function _updateFans() {
            $openid = $this->weixinData['FromUserName'];

            $isUser = $this->isUser();
            if ($isUser) {
                $data = array(
                    "is_follow" => 0,
                    "update_time" => time()
                );
                $update = $this->table('user')->where(['id' => $isUser['id']])->update($data);
            }
        }

        /**
         * 当前openid是否已经成为用户user
         */
        private function isUser() {
            $weixin_data = $this->weixinData;
            $openid = $this->weixinData['FromUserName'];

            //用户是否存在
            //$isUser = $this->table('user')->where(['openid' => $openid])->get(['id', 'parent', 'parent_share_type', 'activity_id'], true);
            $isUser = $this->table('user')->where(['openid' => $openid])->get(['id'], true);
            if (!$isUser) {
                return false;
            }
            return $isUser;
        }
    }
