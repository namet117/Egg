/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue');
let elementUI = require('element-ui');
Vue.use(elementUI);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

window.layuiTableMerge = (res, curr, count) => {
  var data = res.data;
  var mergeIndex = 0;//定位需要添加合并属性的行数
  var mark = 1; //这里涉及到简单的运算，mark是计算每次需要合并的格子数
  var columsName = ['cate1'];//需要合并的列名称
  var columsIndex = [0];//需要合并的列索引值

  for (var k = 0; k < columsName.length; k++)//这里循环所有要合并的列
  {
    var trArr = $(".layui-table-body>.layui-table").find("tr");//所有行
    for (var i = 1; i < res.data.length; i++) { //这里循环表格当前的数据
      var tdCurArr = trArr.eq(i).find("td").eq(columsIndex[k]);//获取当前行的当前列
      var tdPreArr = trArr.eq(mergeIndex).find("td").eq(columsIndex[k]);//获取相同列的第一列
      if (data[i][columsName[k]] === data[i - 1][columsName[k]]) { //后一行的值与前一行的值做比较，相同就需要合并
        mark += 1;
        tdPreArr.each(function () {//相同列的第一列增加rowspan属性
          $(this).attr("rowspan", mark);
        });
        tdCurArr.each(function () {//当前行隐藏
          $(this).css("display", "none");
        });
      } else {
        mergeIndex = i;
        mark = 1;//一旦前后两行的值不一样了，那么需要合并的格子数mark就需要重新计算
      }
    }
  }
}
