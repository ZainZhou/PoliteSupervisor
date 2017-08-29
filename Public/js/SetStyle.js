/**
 * Created by Zain on 28/8/2017.
 */
$(function(){
    $('.DeveloperList').css({'height':$(window).width()*0.84});
    $('.recordContainer').css({'height':$(window).width()*0.1573});
    $('.wordContainer').css({'height':$(window).width()*1.396});
    var aLi = $('.wordUl').find('li');
    aLi.css('line-height',$(window).width()*0.13389+'px');
    for(var i = 0 ; i < aLi.length ; i++){
        if(i%3 == 2)continue;
        aLi.eq(i).css('margin-right','5.8%');
    }
    aLi.on('click',function(){
       $(this).css({'color':'#ff6767','background-image':'url("Public/images/selectedback.png")'});
    });
});