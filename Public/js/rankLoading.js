/**
 * Created by Zain on 1/9/2017.
 */
$(function(){
   var container = $('.container');
    var imgs = [
        'boy.png',
        'girl.png',
        'playAgain.png',
        'gold.png',
        'silver.png',
        'copper.png',
        'VoiceofYoung.png',
        'background.jpg',
        'RankListHeader.png'
    ];
    var imgCount = 0;
    for(i = 0; i < imgs.length ; i++){
        img  = new Image();
        img.src= '../../Public/images/'+imgs[i];
        img.onload = function(){
            imgCount += 1;
            if(imgCount == imgs.length){
                setTimeout(function(){
                    container.animate({'opacity':1},1000);
                },10);
            }
        };
    }
});