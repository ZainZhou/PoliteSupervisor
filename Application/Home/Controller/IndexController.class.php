<?php
namespace Home\Controller;
use Org\Util\String;

class IndexController extends BaseController {
    private $appid = 'wx81a4a4b77ec98ff4';
    private $acess_token = 'gh_68f0a1ffc303';
    public function index(){
        $signature = $this->JSSDKSignature();
        $this->assign('signature', $signature);
        $this->assign('appid', $this->appid);
        $this->display();
    }
    public function ranklist(){
        $data = $this->rank();
        $signature = $this->JSSDKSignature();
        $this->assign('signature', $signature);
        $this->assign('appid', $this->appid);
        $this->assign('data', $data);
        $this->display();
    }

    public function record() {
        $state = I('post.state', 0);
        $time = I('post.time', '');
        if ($time == '' || $state == 0) {
            $this->ajaxReturn(array(
                'status' => 200,
                'info'   => '非法数据',
            ));
        }
        switch ($state) {
            case '1':
                $timestamp = time();
                session('token', $timestamp);
                session('state1', $time . '|' . $timestamp);
                break;
            case '2':
                $timestamp = session('token');
                session('state2', $time . '|' . $timestamp);
                break;
            case '3':
                $openid = session('openid');
                $timestamp = session('token');
                session('state3', $time . '|' . $timestamp);
                $time1 = $this->getStateSession('state1');
                $time1 = $time1[0];
                $time2 = $this->getStateSession('state2');
                $time2 = $time2[0];
                $total = $time + $time1 + $time2;
                $users = M('rank');
                $user = $users->where(array('openid' => $openid))->find();
                if ($total < $user['total'] || $user['total'] == '') {
                    $data = array(
                        'state1' => $time1,
                        'state2' => $time2,
                        'state3' => $time,
                        'total'  => $total,
                    );
                    $users->where(array('openid' => $openid))->save($data);
                }
                $map['total'] = array('LT', $total);
                $rank = $users->where($map)->count();
                $rank += 1;
//                if ($rank <= 50) {
//                    $list = $users->order('total desc')->field('nickname, avatar, total as time')->limit(50)->select();
//                    foreach ($list as $key => &$value) {
//                        if ($rank != '∞' && $rank <= 50 && $value['nickname'] == $user['nickname']) {
//                            $rank = $key + 1;
//                        }
//                    }
//                }
                $this->ajaxReturn(array(
                    'status' => 200,
                    'info'   => '成功',
                    'data'   => array(
                        'rank' => $rank,
                        'time' => $total
                    ),
                ));
                break;
            default:
                $this->ajaxReturn(array(
                    'status' => 400,
                    'info'   => '非法数据',
                ));
        }
        $this->ajaxReturn(array(
            'status' => 200,
            'info'   => '成功',
        ));

    }

    public function rank() {
        $users = M('rank');
        $openid = session('openid');
        $user = $users->where(array('openid' => $openid))->find();
        if ($user['total'] == '') {
            $rank = '∞';
        } else {
            $token = session('token');
            $time1 = $this->getStateSession('state1');
            $time2 = $this->getStateSession('state2');
            $time3 = $this->getStateSession('state3');
            if ($time1[1] != $token || $time2[1] != $token || $time3[1] != $token) {
                $total =  $user['total'];
            } else {
                $total = $time1[0] + $time2[0] + $time3[0];
            }
            $map['total'] = array('GT', $total);
            $rank = $users->where($map)->count();
            $rank += 1;
        }
        $list = $users->order('total desc')->field('nickname, avatar, total as time')->limit(50)->select();
        foreach ($list as $key => &$value) {
            $value['rank'] = $key+1;
            if ($rank != '∞' && $rank <= 50 && $value['nickname'] == $user['nickname']) {
                $rank = $key+1;
            }
        }
        $len = count($list);
        if ( $len < 10) {
            for ($i = 0; $i < 10 - $len; $i++) {
                $list[] = array(
                    'nickname' => '',
                    'avatar' => '',
                    'time' => '',
                );
            }
        }
        return array(
            'ranklist' => $list,
            'me' => array(
                'rank' => $rank,
                'nickname' => $user['nickname'],
                'avatar' => $user['avatar'],
                'time' => $user['total']
            ),
        );
    }

    public function getQuestions() {
        $total = 21;
        $state = I('post.state', '0');
        switch ($state){
            case '0':
                $bad = 1;
                break;
            case '1':
                $bad = 3;
                break;
            case '2':
                $bad = 5;
                break;
            default:
                $bad = 5;
                break;
        }
        $noPolite = array('浪 费','失 信','欺 诈','黄 毒',
            '懒 惰','虚 伪','狡 诈','厌 学',
            '违 法','乱 纪','偷 盗','暴 力',
            '自 私','打 架','斗 殴','滋 事',
            '信 谣','传 谣','酗 酒','旷 课',
            '舞 弊','造 假','行 贿','受 贿',
            '迟 到','早 退','忘 义','不 仁',
            '懦 弱','损 人','自 满','骄 傲',
            '虚 荣','奢 靡','懈 怠','腐 化',
            '堕 落','骄 奢','淫 逸','焦 躁',
            '粗 俗','败 家','蛮 横','霸 道',
            '偷 窥','晚 归','粗 口','邋 遢',
            '嫉 妒','造 谣','冷 漠','诽 谤',
            '恐 吓','野 蛮','邪 恶','赌 博',
            '喧 闹','推 诿','萎 靡','独 断',
            '贪 污','腐 败','破 坏','污 染',
            '抄 袭','剽 窃','徇 私','枉 法',
            '妄 议','锋 芒','阴 险','孤 陋',
            '陋 习','媚 俗','肤 浅','刻 薄',
            '践 踏','侵 占','无 耻','卑 鄙',
            '悲 观','攀 比','背 叛','挥 霍',
            '愚 昧','浮 夸','忘 形','自 大');
        $polite = array('富 强','民 主','文 明','和 谐',
            '自 由','平 等','公 正','法 治',
            '爱 过','敬 业','诚 信','友 善',
            '遵 纪','守 法','明 礼','守 信',
            '团 结','勤 俭','勤 学','劳 动',
            '奉 献','助 人','自 强','文 明',
            '礼 貌','环 保','服 务','尊 老',
            '爱 幼','和 睦','规 范','明 辨',
            '修 德','博 学','求 实','创 新',
            '求 真','笃 实','廉 洁','自 律',
            '守 时','坚 韧','清 正','担 当',
            '为 公','高 尚','有 规','有 矩',
            '慎 独','慎 微','慎 始','慎 终',
            '自 守','自 省','自 警','宽 容',
            '感 恩','仁 义','亲 仁','崇 德',
            '身 正','正 义','正 气','坦 荡',
            '励 志','正 心','守 礼','务 实',
            '谦 虚','谨 慎','有 节','尊 师',
            '孝 顺','谦 逊','顽 强','英 勇',
            '壮 志','奋 斗','创 业','齐 家',
            '治 国','理 智','高 尚','沉 稳',
            '和 睦','乐 观','向 善','自 觉',
            '自 信','忠 诚','开 朗','智 慧',
            '恭 敬','仁 爱','儒 雅','知 耻');
        $good = $total - $bad;
        $good_arr_keys = array_rand($polite, $good);
        foreach ($good_arr_keys as $v) {
            $good_arr[] = array('word' => $polite[$v], 'isRight' => '1');
        }
        $bad_arr_keys = array_rand($noPolite, $bad);
        if(count($bad_arr_keys) == 1){
            $bad_arr_keys = array($bad_arr_keys);
        }
        foreach ($bad_arr_keys as $v) {
            $bad_arr[] = array('word' => $noPolite[$v], 'isRight' => '0');
        }
        $result = array_merge($good_arr, $bad_arr);
        shuffle($result);
        $this->ajaxReturn(array(
            'status' => 200,
            'data'   => $result
        ));
    }

    private function getStateSession($name) {
        $s = session($name);
        if ($s == null || strlen($s) == 0) {
            return '';
        }

        $data = explode('|', $s);
        return $data;
    }

    public function JSSDKSignature(){
        $string = new String();
        $jsapi_ticket =  $this->getTicket();
        $data['jsapi_ticket'] = $jsapi_ticket['data'];
        $data['noncestr'] = $string->randString();
        $data['timestamp'] = time();
        $data['url'] = 'https://'.$_SERVER['HTTP_HOST'].__SELF__;//生成当前页面url
        $data['signature'] = sha1($this->ToUrlParams($data));
        return $data;
    }

    private function ToUrlParams($urlObj){
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if($k != "signature") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }


    /*curl通用函数*/
    private function curl_api($url, $data=''){
        // 初始化一个curl对象
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        // 运行curl，获取网页。
        $contents = json_decode(curl_exec($ch), true);
        // 关闭请求
        curl_close($ch);
        return $contents;
    }

    private function getTicket() {
        $time = time();
        $str = 'abcdefghijklnmopqrstwvuxyz1234567890ABCDEFGHIJKLNMOPQRSTWVUXYZ';
        $string='';
        for($i=0;$i<16;$i++){
            $num = mt_rand(0,61);
            $string .= $str[$num];
        }
        $secret =sha1(sha1($time).md5($string)."redrock");
        $t2 = array(
            'timestamp'=>$time,
            'string'=>$string,
            'secret'=>$secret,
            'token'=>$this->acess_token,
        );
        $url = "http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/apiJsTicket";
        return $this->curl_api($url, $t2);
    }
}