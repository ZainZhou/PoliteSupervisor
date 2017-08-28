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
    insBtn.on('click',function(){
        insBtn.css({'display':'none'});
        startBtn.css({'display':'none'});
        getBtn.css({'display':'block'});
        Gc.css({'display':'none'});
        Ic.css({'display':'block'});
        Bc.css({'margin':'8% auto'});
    });
    getBtn.on('click',function(){
        insBtn.css({'display':'block'});
        startBtn.css({'display':'block'});
        getBtn.css({'display':'none'});
        Gc.css({'display':'block'});
        Ic.css({'display':'none'});
        Bc.css({'margin':'12% auto'});
    });
});