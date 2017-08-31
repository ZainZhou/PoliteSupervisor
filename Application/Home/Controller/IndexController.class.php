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
                $map['total'] = array('GT', $total);
                $rank = $users->where($map)->count();
                $rank += 1;
                if ($rank <= 50) {
                    $list = $users->order('total desc')->field('nickname, avatar, total as time')->limit(50)->select();
                    foreach ($list as $key => &$value) {
                        if ($rank != '∞' && $rank <= 50 && $value['nickname'] == $user['nickname']) {
                            $rank = $key + 1;
                        }
                    }
                }
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
        $this->ajaxReturn(array(
            'status' => 200,
            'data'   => array(
                'ranklist' => $list,
                'me' => array(
                    'rank' => $rank,
                    'nickname' => $user['nickname'],
                    'avatar' => $user['avatar'],
                    'time' => $user['total']
                ),
            )
        ));
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
        $noPolite = array('浪费','失信','欺诈','黄毒',
            '懒惰','虚伪','狡诈','厌学',
            '违法','乱纪','偷盗','暴力',
            '自私','打架','斗殴','滋事',
            '信谣','传谣','酗酒','旷课',
            '舞弊','造假','行贿','受贿',
            '迟到','早退','忘义','不仁',
            '懦弱','损人','自满','骄傲',
            '虚荣','奢靡','懈怠','腐化',
            '堕落','骄奢','淫逸','焦躁',
            '粗俗','败家','蛮横','霸道',
            '偷窥','晚归','粗口','邋遢',
            '嫉妒','造谣','冷漠','诽谤',
            '恐吓','野蛮','邪恶','赌博',
            '喧闹','推诿','萎靡','独断',
            '贪污','腐败','破坏','污染',
            '抄袭','剽窃','徇私','枉法',
            '妄议','锋芒','阴险','孤陋',
            '陋习','媚俗','肤浅','刻薄',
            '践踏','侵占','无耻','卑鄙',
            '悲观','攀比','背叛','挥霍',
            '愚昧','浮夸','忘形','自大');
        $polite = array('富强','民主','文明','和谐',
            '自由','平等','公正','法治',
            '爱过','敬业','诚信','友善',
            '遵纪','守法','明礼','守信',
            '团结','勤俭','勤学','劳动',
            '奉献','助人','自强','文明',
            '礼貌','环保','服务','尊老',
            '爱幼','和睦','规范','明辨',
            '修德','博学','求实','创新',
            '求真','笃实','廉洁','自律',
            '守时','坚韧','清正','担当',
            '为公','高尚','有规','有矩',
            '慎独','慎微','慎始','慎终',
            '自守','自省','自警','宽容',
            '感恩','仁义','亲仁','崇德',
            '身正','正义','正气','坦荡',
            '励志','正心','守礼','务实',
            '谦虚','谨慎','有节','尊师',
            '孝顺','谦逊','顽强','英勇',
            '壮志','奋斗','创业','齐家',
            '治国','理智','高尚','沉稳',
            '和睦','乐观','向善','自觉',
            '自信','忠诚','开朗','智慧',
            '恭敬','仁爱','儒雅','知耻');
        $good = $total - $bad;
        $good_arr_keys = array_rand($polite, $good+1);
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