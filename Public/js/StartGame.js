/**
 * Created by Zain on 31/8/2017.
 */
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
    var showTime = null;
    var aLi = $('.wordUl').find('li');
    var Qlink = 'http://localhost/PoliteSupervisor/Home/Index/getQuestions';
    aLi.on('click',function(){
        var _this = $(this);
        _this.css({'color':'#ff6767','background-image':'url("Public/images/selectedback.png")'});
        timer.clearTimer();
        clearInterval(showTime);
        if(_this.attr('isRight') == 0){
            alert('选择正确!')
        }else {
            alert('选择错误!')
        }
    });
    StartBtn.on('click',function(){
        time = [];
        timesum = 0;
        stateIndex = 0;
        timer = null;
        timer = new Timer();
        timer.setmTimer();
        timer.setTimer();
        $.mobile.loading('show');
        $.post(Qlink,stateIndex,function(data){
            $.mobile.loading('hide');
            if(data.status == 200){
                for(var i = 0 ; i < aLi.length ; i++){
                   aLi.eq(i).html(data.data[i].word);
                    aLi.eq(i).attr('isRight',data.data[i].isRight);
                }
                $.mobile.changePage('#GamePage',{
                    transition:'pop'
                });
                showTime = setInterval(function(){
                    s.html(timer.getSecond());
                    ms.html(timer.getmSecond());
                },10);
            }else {
                console.log(data);
            }
        })
    });
});