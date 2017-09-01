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
    var stateIndex;
    var s = $('.sencond');
    var ms = $('.msecond');
    var timer = null;
    var showTime = null;
    var aLi = $('.wordUl').find('li');
    var Tlink = 'http://localhost/PoliteSupervisor/Home/Index/record';
    var Qlink = 'http://localhost/PoliteSupervisor/Home/Index/getQuestions';
    var wrongTip = $('.notRight');
    var rightNum = 0;
    var alertBox = $('.alertBox');
    var oMask = $('.mask');
    var stateTime = $('.stateTime');
    var nextBtn = $('.nextBtn');
    var Checkpoint = $('.Checkpoint');
    var AlertTitle = $('.AlertTitle');
    var TimeShowBox = $('.TimeShowBox');
    var RankShowBox = $('.RankShowBox');
    var rank = $('.rank');
    var timesum = $('.timesum');
    var playAgain = $('.playAgain');
    var overBtn = $('.overBtn');
    playAgain.on('click',function(){
        aLi.css({'color':'#f8883d','background-image':'url("Public/images/selector.png")'});
        oMask.css('display','none');
        alertBox.css('display','none');
        $.mobile.changePage('#StartPage',{
           transition:'flow'
        });
    });
    overBtn.on('click',function(){
        window.close();
    });
    aLi.on('click',function(){
        var _this = $(this);
        if(_this.attr('isRight') == 0){
            _this.css({'color':'#ff6767','background-image':'url("Public/images/selectedback.png")'});
            rightNum += 1;
        }else {
            wrongTip.animate({'opacity':1},500);
            setTimeout(function(){
                wrongTip.animate({'opacity':0},500);
            },1200)
        }
        if(rightNum == state[stateIndex][1]){
            timer.clearTimer();
            clearInterval(showTime);
            stateIndex += 1;
            if(stateIndex < 3){
                $.mobile.loading('show');
                var _data = {};
                _data.state = (stateIndex);
                _data.time = parseInt(timer.getSecond())+(parseInt(timer.getmSecond()))/100;
                $.post(Tlink,_data,function(data){
                    $.mobile.loading('hide');
                    if(data.status == 200){
                        stateTime.html(timer.getSecond() + '.' + timer.getmSecond());
                        oMask.css('display','block');
                        alertBox.css('display','block');
                        console.log(data);
                    }else{
                        alert(data.info);
                    }
                });
                rightNum = 0;
            }else{
                $.mobile.loading('show');
                var _data = {};
                _data.state = (stateIndex);
                _data.time = parseInt(timer.getSecond())+(parseInt(timer.getmSecond()))/100;
                $.post(Tlink,_data,function(data){
                    $.mobile.loading('hide');
                    if(data.status == 200){
                        rank.html(data.data.rank);
                        timesum.html(data.data.time);
                        AlertTitle.html('胜利通关!');
                        oMask.css('display','block');
                        TimeShowBox.css('display','none');
                        RankShowBox.css('display','block');
                        alertBox.css('display','block');

                        rightNum = 0;
                    }else{
                        alert(data.info);
                    }
                });
            }
        }
    });
    function getQuestion(){
        timer = null;
        timer = new Timer();
        timer.setmTimer();
        timer.setTimer();
        Checkpoint.html(state[stateIndex][0]);
        $.mobile.loading('show');
        console.log(stateIndex);
        var _data = {};
        _data.state = stateIndex;
        $.post(Qlink,_data,function(data){
            $.mobile.loading('hide');
            if(data.status == 200){
                console.log(data);
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
                alert('错误编号:'+data.status);
            }
        })
    }
    nextBtn.on('click',function(){
        aLi.css({'color':'#f8883d','background-image':'url("Public/images/selector.png")'});
        oMask.css('display','none');
        alertBox.css('display','none');
        getQuestion()
    });
    StartBtn.on('click',function(){
        stateIndex = 0;
        getQuestion();
        TimeShowBox.css('display','block');
        RankShowBox.css('display','none');
    });
});