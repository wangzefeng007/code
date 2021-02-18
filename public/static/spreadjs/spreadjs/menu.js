

/*

  触发事件---------------

*/
let url = 'getlist'  //初始化树目录

let urlbom = 'example/excel/category'  //树目录下一级

let addurl = 'example/excel/getlist'   //添加分组重新加载树目录

let srciframe = '/admin.php/example/excel/datalist?id='  //页面跳转

let moban = 'example/excel/getmodel' //模版列表

let formlist = 'example/excel/getformlist' //小语句变量

let formlist1 = 'example/excel/sys_var' //系统变量

let toast_id = '' //模版ID

let toastData

let vari

let itemdata

let ids

function GetRequest() {
  var urls = location.search;
  //获取url中"?"符后的字串  
  var theRequest = new Object();
  if (urls.indexOf("?") != -1) {
    var str = urls.substr(1);
    strs = str.split("&");
    for (var i = 0; i < strs.length; i++) {
      theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
    }
  }
  return theRequest;
}
GetRequest()
var req = GetRequest();




$(function () {

  //搜索框的获取焦点

  $('.left-search input').focus('click', function () {

    $('.left-search').css('border', '1px solid #5F97F5FF');

  })

  //搜索框的失去焦点

  $('.left-search input').blur('click', function () {

    $('.left-search').css('border', '1px solid #E4E4E4FF');

  })



  //树目录的点击，文件图片的切换, 同时调用网络请求方法

  $('.left-tree').on('click', '.tree-dom', function () {

    let status = $(this).children('input').val();

    if (status <= 0) {

      $(this).children('img').attr('src', '/assets/img/common/menu/tree_kai.png');

      $(this).children('input').val('1');

      $(this).css('color', '#5F97F5FF');

      const treePid = $(this).data('pid');

      const treeType = $(this).data('type');

      menuTreeElement($(this), treePid, treeType);

      return

    }

    $(this).children('img').attr('src', '/assets/img/common/menu/tree_guan.png');

    $(this).children('input').val('0');

    $(this).siblings('.tree-wjli-padding').slideUp();

    $(this).css('color', '#333');

  })



  //侧边栏隐藏

  let leftFlag = 0;  //需要判断拉出还是隐藏

  $('.arrow-double').on('click', function () {

    if (leftFlag == 0) {

      $('.body-tree').removeClass('my-active-sidebars');

      $('.body-tree').addClass('my-active-sidebar');

      $(this).attr('src', '/assets/img/common/menu/arrow-double-right.png');

      leftFlag = 1;

    } else {

      $('.body-tree').removeClass('my-active-sidebar');

      $('.body-tree').addClass('my-active-sidebars');

      $(this).attr('src', '/assets/img/common/menu/arrow-double-left.png');

      leftFlag = 0;

    }

  })



  //点击文件将pid和type存在本地

  $('.left-tree').on('click', '.dd-click', function () {

    const pid = $(this).data('pid');

    const url1 = formlist;

    const url2 = formlist1;

    $('.spread-all-ul1').html('');

    $('.spread-all-ul2').html('');

    ajaxFormList(url1, pid);

    ajaxFormList1(url2);

  })



  $('#creadsx').on('click', function () {

    $('.left-tree').html('');

    ajaxcread(addurl);

  })

  // 点击选择模版
  $('.left-tree').on('click', '.toast-img', function (event) {

    toast_id=''

    const pid = $(this).data('pid');

    ids = $(this).data('id');

    const url3 = moban;
    ajaxmoban(url3, pid)

    $('.toast').show()
    $('.mask').show()

    event.stopPropagation();
  })

  $('.toask-dele').on('click', function () {
    $('.toast').hide()
    $('.mask').hide()
  })

  //模版点击编辑
  $(".toast_content_select").change(function () {
    toast_id = $('.toast_content_select option:selected').val()
  });

  $('.toast_btn').on('click', function () {


    if (toast_id) {
      const url4 = srciframe + toast_id + '&type=' + 0;
      $('.iframe-border').attr('src', url4);

      const id = ids;

      const url1 = formlist;

      const url2 = formlist1;

      $('.spread-all-ul1').html('');

      $('.spread-all-ul2').html('');

      ajaxFormList(url1, itemdata[id].pid);

      ajaxFormList1(url2);


      $('.toast').hide()
      $('.mask').hide()
    } else {

      if (toastData.length > 0) {
        const url5 = srciframe + toastData[0].id + '&type=' + 0;

        $('.iframe-border').attr('src', url5);

        const id = ids;

        const url1 = formlist;

        const url2 = formlist1;

        $('.spread-all-ul1').html('');

        $('.spread-all-ul2').html('');

        ajaxFormList(url1, itemdata[id].pid);

        ajaxFormList1(url2);

        $('.toast').hide()
        $('.mask').hide()
      } else {

        alert('请选择模版')

      }
    }

  })


  // 切换变量
  $('.spread-all-cut div:eq(0)').click(function () {
    $(this).addClass('select-cut1');

    $('.spread-all-cut div:eq(1)').removeClass('select-cut1');

    $('.spread-all-right1').show()

    $('.spread-all-right2').hide()

  })

  $('.spread-all-cut div:eq(1)').click(function () {
    $(this).addClass('select-cut1');

    $('.spread-all-cut div:eq(0)').removeClass('select-cut1');

    $('.spread-all-right2').show()

    $('.spread-all-right1').hide()

  })

})



/*

  网络请求封装------------

*/



$(document).ready(function () {

  ajaxcread(url);

})



function ajaxcread(url) {

  $.ajax({

    url: url,

    type: 'get',

    success: function (res) {

      if (res.code == 1) {

        const dataValue = res.data;

        dataValue.forEach(item => {

          menuTreeBodyElement(item, $('.left-tree'));

        });

      }

    }

  })

}


// 模版列表请求
function ajaxmoban(url, id) {

  $('.toast_content_select').html('')

  toastData = []

  $.ajax({

    url: url,

    type: 'POST',

    data: {
      id: id
    },

    success: function (res) {

      if (res.code == 1) {

        toastData = res.data;

        toastData.forEach(item => {

          mobanBodyElement(item, $('.toast_content_select'));

        });

      }

    }

  })

}

//变量请求
function ajaxFormList(url, id) {

  $.ajax({

    url: url,

    type: 'POST',

    data: {
      formtype: req.type,
      formid: id
    },

    success: function (res) {

      if (res.code == 1) {

        const dataValue = res.data.sentence;

        dataValue.forEach(item => {

          variBodyElement(item, $('.spread-all-ul2'));

        });

      }

    }

  })

}

function ajaxFormList1(url) {

  $.ajax({

    url: url,

    type: 'GET',

    success: function (res) {

      let data = JSON.parse(res)

      if (data.code == 1) {

        vari = data.data;

        vari.forEach(item => {

          variBodyElement1(item, $('.spread-all-ul1'));

        });

      }

    }

  })

}

// 复制
function copy(e) {

  let data = e.target.value.split("(")

  e.target.value = '{{' + data[0] + '}}'
  copyMethod(e)
  e.target.value = e.target.value.replace('{{', '').replace('}}', '') + '(' + data[1] + ')'

}

function copy1(e) {


  for (let i = 0; i < vari.length; i++) {
    if (e.target.value == vari[i].name) {
      e.target.value = '{{' + vari[i].var + ',2' + '}}'
      this.copyMethod(e)
      e.target.value = vari[i].name.replace('{{', '').replace('}}', '').replace(',2', '')
      return
    }
  }

}

function copyMethod(e) {
  var listData = e.target
  listData.select()
  document.execCommand("copy")
}




/*

  树目录格式渲染方法---------

*/



//文件夹的渲染

function menuTreeBodyElement(value, treedom) {

  const treement = `

    <ul>

      <li class="tree-wjli tree-dom" data-type="${value.type}" data-pid="${value.pid}">

        <input type="hidden" value="0"/>

        <img src="/assets/img/common/menu/tree_guan.png" alt="">

        <text title="${value.name}">${value.name}</text>

      </li>

      <li class="tree-wjli tree-wjli-padding">



      </li>

    </ul>

  `

  treedom.append(treement);

  treedom.slideDown();

}

function mobanBodyElement(value, treedom) {

  const treement = `
  <option value="${value.id}">${value.name}</option>

  `

  treedom.append(treement);

  treedom.slideDown();

}

function variBodyElement(value, treedom) {

  const treement = `
  <li>
    <input class='list' type='text' name='' title='${value.name},${value.beizhuValue}' onclick='copy(event)' readonly='readonly' value="${value.name}(${value.beizhuValue})">
  </li>
  `

  treedom.append(treement);

  treedom.slideDown();
}

function variBodyElement1(value, treedom) {

  const treement = `
  <li>
    <input class='list' type='text' name='' title='${value.name}' onclick='copy1(event)' readonly='readonly' value="${value.name}">
  </li>
  `

  treedom.append(treement);

  treedom.slideDown();
}




//文件的渲染

function menuTreeFileElement(value,index, treedom) {

  const file = `

    <dd data-pid="${value.pid}" 

      data-type="${value.type}" class="dd-click">

      <img src="/spreadjs/css/images/toast.png" alt="" data-id=${index} data-pid="${value.pid}" class='toast-img' style="width: 20px;">

      <img src="/assets/img/common/menu/tree_wj_blue.png" alt="" style="width: 20px;">

      <text title="${value.name}">${value.name}</text>

    </dd>

  `

  treedom.append(file);

  treedom.slideDown();

}



//点击目录请求是否有下一层

function menuTreeElement(appdom, pid, type) {

  itemdata = []

  let doms = appdom.siblings('.tree-wjli-padding');

  let domslist = doms.children().length;

  if (domslist > 0) {

    doms.slideDown();

    return

  }

  $.ajax({

    url: urlbom,

    type: 'post',

    data: {

      type: type,

      pid: pid

    },

    success: function (res) {

      if (res.code == 3) {

        const valueItem = res.data;

        valueItem.forEach((value) => {

          menuTreeBodyElement(value, doms);

        })

      } else if (res.code == 1) {

        itemdata = res.data;

        itemdata.forEach((item,index) => {

          menuTreeFileElement(item, index, doms)

        })

      }

    }

  })

}