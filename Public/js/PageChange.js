/**
 * Created by Zain on 28/8/2017.
 */
$(function(){
    var insBtn = $('.InstructionBtn');
    var startBtn = $('.StartBtn');
    var getBtn = $('.GetBtn');
    var Bc = $('.BtnContainer');
    var Gc = $('.GamelogoContainer');
    var Ic = $('.InstructionContainer');
    var Cr = $('.copyright');
    var DBtn = $('.DeveloperBtn');
    var oMask = $('.mask');
    var DeveloperList = $('.DeveloperList');
    var closeBtn = $('.closeBtn');
    closeBtn.on('click',function(){
        oMask.css('display','none');
        DeveloperList.css('display','none');
    });
    insBtn.on('click',function(){
        insBtn.css({'display':'none'});
        startBtn.css({'display':'none'});
        getBtn.css({'display':'block'});
        Gc.css({'display':'none'});
        Ic.css({'display':'block'});
        Bc.css({'margin':'5.2% auto'});
        Cr.css({'display':'none'});
        DBtn.css({'display':'block'});
    });
    getBtn.on('click',function(){
        insBtn.css({'display':'block'});
        startBtn.css({'display':'block'});
        getBtn.css({'display':'none'});
        Gc.css({'display':'block'});
        Ic.css({'display':'none'});
        Bc.css({'margin':'12% auto'});
        Cr.css({'display':'block'});
        DBtn.css({'display':'none'});
    });
    DBtn.on('click',function(){
        oMask.css('display','block');
        DeveloperList.css('display','block');
    });
    startBtn.on('click',function(){
        $.mobile.changePage('#GamePage',{
            transition:'pop'
        })
    });
});