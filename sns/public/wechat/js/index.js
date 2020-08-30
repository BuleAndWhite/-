//����Ĭ����ʽ


var wid = document.width;
var hei = $(window).height();
$(".layout").css({widht:wid,height:hei});

var elementHei = function(ele,ele1){
    var elehei = $(ele).height();
    $(ele1).css({"line-height":elehei+"px"})
};

elementHei(".invite-friend .pic",".invite-friend .tit span");

//�л���ͼ1
var view1 = function(){
    $(".index").show().siblings().hide();
};
//�л���ͼ2
var view2 = function(){
    $(".invite-friend").show().siblings().hide();
};
//�л���ͼ3
var view3 = function(){
    $(".chongdian").show().siblings().hide();
};

var swithView = function(id){
    eval(eval('view'+id+'()'));
};

//�л���ͼ �ӿ� ֱ�Ӳ崫ID ����





//�����ť��ʾ������ʾ
var showAlertInvite = function(){
    $(".alert-invite").show()
};
//�ر�������ʾ
var hideAlertInvite = function(){
    $(".alert-invite").hide()
};

$(".invite1-btn").click(function(){
    showAlertInvite()
});
$(".alert-invite").click(function(){
    hideAlertInvite()
});





//�����ť��ʾ�齱
var showAlertCJ = function(){
    $(".alert-cj").show()
};
//�ر�������ʾ
var hideAlertCJ = function(event){
    $(".alert-cj").hide();
};
$(".cj-btn").click(function(){
    showAlertCJ()
});
//��ֹʱ��ð��
$(".alert-main").click(function(e){
    e.stopPropagation();
});
$(".alert-cj").click(function(){
    hideAlertCJ()
});





//�ύ��ť��ʾ���ں�
var showAlertgzh = function(){
    $(".alertgzh").show()
};
//�ر�������ʾ
var hideAlertgzh = function(event){
    $(".alertgzh").hide();
};







//���ֵ���


var Ascending = function(num){
    var infonumber = 0;
    index = 10;
    $(".animation li").eq(index).nextAll().removeClass("active");
    add();
    function add (){
        if(infonumber == num){return false}
        setTimeout(function(){
            infonumber+=1;
            $(".invite-friend .animation .number span").html(infonumber)
            add();
            if(infonumber<=100){
                addAnimation(infonumber)
            }
        },30)
    }
};


var index = 10;
var addAnimation = function(num){
    if(num<=100&&num%4==0){
        index+=1;
        $(".animation li").eq(index).addClass("active");
    }
}

