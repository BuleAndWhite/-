$(function () {
    //标签
    $(document).on("click", ".itSkillResult", function () {
        var itSkill =$(this).attr("data-table-key");
        localStorage.setItem("labelName","")
        localStorage.setItem("labelUrl","")
        localStorage.setItem("labelName",$(this).attr("data-label-id"))
        localStorage.setItem("labelUrl",$(this).attr("data-label-url"))
        var scope = $(this); //使用this对象指向作用域
        $(".itSkillResult").itskillSelect({
            containerClass:itSkill,
            className: "big-window",
            classId: "itSkillResult1",
            maxCount: 10,
            type: "标签",
            scope: scope
        });
    });
    //标签 -- itskill
    $.fn.itskillSelect = function (settings) {

        if (this.length < 1) {
            return;
        }
        ;

        // 默认值
        settings = $.extend({
            containerClass: null,
            className: null,
            classId: null,
            required: true,
            maxCount: null,
            onConfirm: null,
            type: null,
            scope: null
        }, settings);

        scope = settings.scope;
        var container = $("." + settings.containerClass);
        var num = settings.containerClass;
        var title = "<div class=\"title\"><b>选择" + settings.type + "</b><span class=\"tip\">(最多选择" + settings.maxCount + "项)</span> &nbsp&nbsp;<span class='tip' style='font-weight:bold;' id='tip_" + num + "'></span><a href=\"javascript:void(0)\" ></a></div>";
        var selectedResult = "<div class=\"sele-tag\"><dl><dt>已选择：</dt><dd id='ddResult_" + num + "'><a id='btnSure_" + num + "' href='javascript:void(0)' class='btn'>确定</a></dd></dl></div>";

        function setValue(value) {
            // var type = $("." + settings.classId);
            // if (type.attr("type") == "text") {
            //     type.val(value);
            // }
            // else
            //     type.html(value);
            if (scope.attr("type") == "text") {
                scope.val(value);
            }
            else
                scope.html(value);
        }

        function init() {
            if ($("body #" + num + "_bg").length <= 0) {
                $("body").append("<div id='" + num + "_bg' class='mask-Bg'></div>");
            }
            if (settings.className != null) {
                container.addClass(settings.className);
            }

            var data = eval("(" + professionaldata + ")");
            // console.info(data.positionlist);

            if ($.trim(container.html()) == "") {
                container.append(title);
                container.append(selectedResult);
                container.append("<div class=\"position-menu\" id='" + num + "_datalist'></div>");
                var datalist = $("#" + num + "_datalist");
                datalist.append("<div class='menu' id='firstMenu'></div>");
                var firstMenu = datalist.find("#firstMenu");
                firstMenu.append("<ul></ul>");
                $.each(data.positionlist, function (i, obj) {   // 循环第一级
                    console.log(obj)
                    $(firstMenu).find("ul").append("<li id='dl_" + i + "' name='" + i + "'>" + obj.p + "</li>");
                });

                if (datalist.find("div[class='sub-menu']").length <= 0) {
                    datalist.append("<div class='sub-menu' id='secondMenu'></div>");
                }
                var secondMenu = datalist.find("#secondMenu");

                // 第一级菜单鼠标悬浮事件，弹出二级菜单和三级菜单项
                $("#" + num + "_datalist #firstMenu ul li").bind("mouseover", function () {
                    secondMenu.html("");
                    $("#" + num + "_datalist #firstMenu ul li").removeClass("sele");
                    $(this).addClass("sele");

                    var index = $(this).attr("name");
                    $.each(data.positionlist[index].c, function (j, item) {
                        secondMenu.append("<dl id='dl_" + j + "'></dl>");
                        var dtItem = "<dt id='dt_" + j + "'>" + item.n + "</dt>";
                        secondMenu.find("dl[id='dl_" + j + "']").append(dtItem);
                        secondMenu.find("dl[id='dl_" + j + "']").append("<dd id='dd_" + j + "'></dd>");
                        $.each(data.positionlist[index].c[j].a, function (m, dist) {
                            var threeMenu = "<a href='javascript:void(0)' data-id='" + dist.id + "'  id='item_" + index + "_" + j + "_" + m + "'>" + dist.s + "</a>";
                            secondMenu.find("dl[id='dl_" + j + "'] dd[id='dd_" + j + "']").append(threeMenu);

                        });
                    });

                    //根据已选择的项，将相同的列表展示项添加样式
                    var resultItems = container.find("#ddResult_" + num + " a");
                    $.each(resultItems, function (n, ritem) {
                        var rid = $(ritem).attr("id").substr(2, $(ritem).attr("id").length);
                        secondMenu.find("a[id='" + rid + "']").addClass("sele");
                    });

                    // 列表项点击事件，选中则在结果容器中显示添加选中样式
                    secondMenu.find("a").bind("click", function () {
                        var selectedCount = container.find("#ddResult_" + num + " a").length - 1;

                        if ($(this).hasClass("sele")) {
                            $(this).removeClass("sele");
                            container.find("#ddResult_" + num).find("a[id='c_" + $(this).attr("id") + "']").remove();
                        }
                        else {
                            if (selectedCount >= settings.maxCount) {
                                container.find("#tip_" + num).html("提示:最多只能选择" + settings.maxCount + "项！").css("color", "red");
                                setTimeout(function () {
                                    container.find("#tip_" + num).empty();
                                }, 3000);
                            }
                            else {
                                $(this).addClass("sele");
                                inputHidden = $(this).html();
                                container.find("#ddResult_" + num + " #btnSure_" + num).before("<a href='#' data-id='" + $(this).attr('data-id') + "' id='c_" + $(this).attr("id") + "'>" + $.trim($(this).html()) + "</a>");
                            }
                        }

                        // 结构容器中选中项点击事件，移除并将列表中的选中样式取消
                        container.find("#ddResult_" + num + " a").bind("click", function () {
                            var rid = $(this).attr("id");
                            if (rid != "btnSure_" + num) {
                                var sid = rid.substr(2, rid.length);
                                $(this).remove();
                                secondMenu.find("a[id='" + sid + "']").removeClass("sele");
                            }
                        });
                    });

                });

                // 默认显示第一级
                $(firstMenu).find("ul li:first").addClass("sele").trigger("mouseover");


                // 关闭选择框
                container.find(".title a").bind("click", function () {
                    container.hide();
                    $("#" + num + "_bg").hide();
                });

                // 确定
                container.find("#ddResult_" + num + " a[id='btnSure_" + num + "']").bind("click", function () {
                    var selectedItems = container.find("#ddResult_" + num + " a[id!='btnSure_" + num + "']");
                    var results = "";
                    var id = ""; //获取得到的树形id
                    $.each(selectedItems, function (i, item) {
                        results += $.trim($(item).html()) + ",";
                        id += $.trim($(item).attr("data-id")) + ",";
                    });
                    if (results.length > 0) {
                        results = results.substr(0, results.length - 1);
                        id = id.substr(0, id.length - 1);
                    }
                    //$(this).parents('.select_parents').children(".selHidden").val(id);//设置选择后的结果
                    // scope.prev().val(id);
                    // scope.next().val(inputHidden);
                    // setValue(results);
                    var sof_id = localStorage.getItem("labelName");
                    var sofUrl = localStorage.getItem("labelUrl");
                    $.get(sofUrl,{id:id,sof_id:sof_id},function(data){
                        scope.prev().val(id);
                        scope.next().val(results);
                        container.hide();
                        $("#" + num + "_bg").hide();

                    });

                });

            }
            container.show();
            $("#" + num + "_bg").show();
        }

        init();
    };

});

function masterUP() {

    var data_url = $("#master_uid").attr("master_url");
    var master_uid = $("#master_uid").val();

    $.ajax({
        type: "POST",
        url: data_url,
        data: {"Search": master_uid},
        //dataType:"json",
        success: function (dataObj) {
            if (dataObj.code == 1) {
                $(".user_list").html("");
                var info = dataObj.info;
                $str = "<ul>"
                for (var i in info) {
                    $str += "<li  style='list-style-type:none' value='" + info[i]['id'] + "'>" + info[i]['user_nicename'] + "(ID)" + info[i]['id'] + "<span class='addUser'attr-data-id='" + info[i]['id'] +"' attr-data-name='" + info[i]['user_nicename']  +"'>&nbsp;+</span></li>";
                }
                $str += "</ul>";
                $(".user_list").append($str);
                $(".addUser").click(function () {
                    var $this = $(this)
                    var name =$this.attr('attr-data-name');
                    var id ="#"+$this.attr('attr-data-id')+"#";
                    $this.remove();
                    $(".uids").append("<span class='userOne'><span>"+name+"<input type='hidden' name='adminId[]' value='"+id+"' /></span> <span  class='is_del'>X&nbsp;&nbsp;</span></span>");
                    $(".is_del").click(function () {
                        var $this = $(this);
                        $this.parent().remove();
                    })
                });
            }
            else {
                $(".user_list").html("");
            }

        }
    })

}

