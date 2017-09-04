/**
 * Created by Zain on 1/9/2017.
 */
$(function(){
    var RanklistHeader = $('.RanklistHeader');
    var AvatarBox = $('.AvatarBox');
    var aLi = $('.RanklistUl').find('li');
    var aAvatar = $('.avatar');
    var playerName = $('.playerName');
    aAvatar.css('height',$(window).width()*0.0922);
    aLi.css('height',$(window).width()*0.212);
    playerName.css('line-height',$(window).width()*0.165+'px');
    for(var i = 0 ; i < aLi.length ; i++){
        if(i%2 != 0){
            aLi.eq(i).css({'background-color':'#94d8ff','box-shadow':'inset 0 0 8px 5px #37abef'});
        }
    }
    AvatarBox.css('height',$(window).width()*0.1608);
    RanklistHeader.css('height',$(window).width()*0.8);
});