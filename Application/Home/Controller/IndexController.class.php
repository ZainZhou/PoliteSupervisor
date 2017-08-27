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
                session('state1', $time + ';' + $timestamp);
                break;
            case '2':
                $timestamp = session('token');
                session('state2', $time + ';' + $timestamp);
                break;
            case '3':
                $openid = session('openid');
                $timestamp = session('token');
                session('state3', $time + ';' + $timestamp);
                $time1 = $this->getStateSession('state1');
                $time2 = $this->getStateSession('state2');
                $total = $time + $time1 + $time2;
                $users = M('rank');
                $user = $users->where(array('openid' => $openid))->find();
                if ($user['total'] < $total || $user['total'] == '') {
                    $data = array(
                        'state1' => $time1,
                        'state2' => $time2,
                        'state3' => $time,
                        'total'  => $total,
                    );
                    $users->where(array('openid' => $openid))->save($data);
                }
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
        $map['total'] = array('GT', $user['total']);
        $rank = $users->where($map)->count();
        $rank += 1;
        $list = $users->order('total desc')->field('nickname, imgurl, score')->limit(10)->select();
        if ($rank <= 50) {
            $real = $users->order('score desc')->field('nickname, imgurl, score')->limit(50)->select();
        }
        foreach ($real as $key => $value) {
            if ($value['nickname'] == $user['nickname']) {
                $rank = $key+1;
            }
        }
        if ($user['days'] == 0) {
            $rank = '∞';
        }
        $this->ajaxReturn(array(
            'status' => 200,
            'data'   => array(
                'list' => $list,
                'rank' => $rank,
                'nickname' => $user['nickname'],
                'avatar' => $user['imgurl'],
                'days' => $user['days'],
                'groups' => $user['count']
            )
        ));
    }

    private function getStateSession($name) {
        $s = session($name);
        if ($s == null || length($s) == 0) {
            return '';
        }
        $data = explode(';', $s);
        return $data[0];
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