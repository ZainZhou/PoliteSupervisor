/**
 * Created by Zain on 31/8/2017.
 */
var selections = [
    '富强','民主','文明','和谐',
'自由','平等','公正','法治',
'法治','法治','诚信','友善',
'遵纪','守法','明礼','守信',
'团结','勤俭','勤学','劳动'
];
function Timer(){
    this.msecond = 0;
    this.second = 0;
    this.mTimer = null;
    this.Timer = null;
}
Timer.prototype = {
    constructor : Timer,
    setmTimer : function(){
        var _this = this;
        this.mTimer = setInterval(function(){
            _this.msecond += 1;
            if(_this.msecond == 100){
                _this.msecond = 0;
            }
        },10);
    },
    setTimer : function(){
        var _this = this;
        this.Timer = setInterval(function(){
            _this.second += 1;
            if(_this.second == 1000){
                _this.second = 999;
            }
        },1000)
    },
    clearTimer : function(){
        clearInterval(this.Timer);
        clearInterval(this.mTimer);
    },
    getmSecond : function(){
        if(this.msecond >= 10){
            return this.msecond;
        }else{
            return '0'+this.msecond;
        }
    },
    getSecond : function(){
        if(this.second >= 10){
            return this.second;
        }else{
            return '0'+this.second;
        }
    }
};

$(function(){
    var StartBtn = $('.StartBtn');
    var state = [['第一关',1],['第二关',3],['第三关',5]];
    var timesum,time,stateIndex;
    var s = $('.sencond');
    var ms = $('.msecond');
    var timer = null;
    var aLi = $('.wordUl').find('li');
    aLi.on('click',function(){
        $(this).css({'color':'#ff6767','background-image':'url("Public/images/selectedback.png")'});
        timer.clearTimer();
    });
    StartBtn.on('click',function(){
        time = [];
        timesum = 0;
        stateIndex = 0;
        timer = null;
        timer = new Timer();
        timer.setmTimer();
        timer.setTimer();
        var showTime = setInterval(function(){
            s.html(timer.getSecond());
            ms.html(timer.getmSecond());
        },10);
        $.mobile.changePage('#GamePage',{
            transition:'pop'
        })
    });
});