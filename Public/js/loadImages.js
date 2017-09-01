/**
 * Created by Zain on 29/8/2017.
 */
$(function(){
   var imgs = [
       'background.jpg',
       'close.png',
       'developerBack.png',
       'GameBack.jpg',
       'GameLogo.png',
       'getit.png',
       'instruction.png',
       'instructions.png',
       'recordBack.png',
       'selector.png',
       'selectedback.png',
       'ShowDeveloper.png',
       'StartGame.png',
       'timer.png',
       'VoiceofYoung.png',
       'wordBack.png',
       'wordnum.png',
       'popbox.png',
       'overBtn.png',
       'nextState.png',
       'depressed.png'
   ];
    var imgCount = 0;
    $.mobile.loading('show');
    for(i = 0; i < imgs.length ; i++){
        img  = new Image();
        img.src= 'Public/images/'+imgs[i];
        img.onload = function(){
            imgCount += 1;
            if(imgCount == imgs.length){
                $.mobile.loading('hide');
                setTimeout(function(){
                    $.mobile.changePage('#StartPage',{
                        transition:'fade'
                    })
                },100);
            }
        };
    }
});