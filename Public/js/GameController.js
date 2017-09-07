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
    var state = [['第一关',1],['第二关',3],['第三关',5],['第四关',7],['第五关',9]];
    var stateIndex;
    var s = $('.sencond');
    var ms = $('.msecond');
    var timer = null;
    var showTime = null;
    var aLi = $('.wordUl').find('li');
    var Tlink = tlink;
    var Qlink = qlink;
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
    var CompleteNumber = $('.now');
    var TotalNumber = $('.all');
    playAgain.on('click',function(){
        var url = 'url("'+public+'/images/selector.png")';
        aLi.css({'color':'#f8883d','background-image':url});
        oMask.css('display','none');
        alertBox.css('display','none');
        AlertTitle.html('完成任务!');
        $.mobile.changePage('#StartPage',{
           transition:'flow'
        });
    });
    aLi.on('click',function(){
        var _this = $(this);
        if(_this.attr('isRight') == 0){
            var url = 'url("'+public+'/images/selectedback.png")';
            _this.css({'color':'#ff6767','background-image':url});
            rightNum += 1;
            CompleteNumber.html(rightNum);
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
            if(stateIndex < 5){
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
        var _data = {};
        _data.state = stateIndex;
        $.post(Qlink,_data,function(data){
            $.mobile.loading('hide');
            if(data.status == 200){
                for(var i = 0 ; i < aLi.length ; i++){
                    aLi.eq(i).html(data.data[i].word);
                    aLi.eq(i).attr('isRight',data.data[i].isRight);
                }
                TotalNumber.html(state[stateIndex][1]);
                CompleteNumber.html(rightNum);
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
        var url = 'url("'+public+'/images/selector.png")';

        aLi.css({'color':'#f8883d','background-image':url});
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