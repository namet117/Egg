/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************!*\
  !*** ./resources/js/home.js ***!
  \******************************/
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

new Vue({
  el: '#egg',
  data: function data() {
    var fixedIndex = localStorage.getItem('egg-table-fixed-index');
    return {
      tableHeight: navigator.userAgent.toLowerCase().indexOf('andriod') > -1 ? '90vh' : '97.5vh',
      stocks: JSON.parse(window.originalStocks) || [],
      tRealDate: window.tRealDate,
      tEstimateDate: window.tEstimateDate,
      dialogTitle: '',
      isShowEditDialog: false,
      stockDetail: {
        cate1: '',
        name: '',
        code: '',
        cost: '',
        hold_num: ''
      },
      defaultTableSort: {
        prop: localStorage.getItem('egg-table-sort-prop') || '',
        order: localStorage.getItem('egg-table-sort-order') || 'ascending'
      },
      stockRules: {
        cate1: [{
          required: true,
          message: '',
          trigger: 'change'
        }],
        name: [{
          required: true,
          message: '',
          trigger: 'change'
        }],
        code: [{
          required: true,
          message: '',
          trigger: 'change'
        }],
        cost: [{
          required: true,
          type: 'number',
          message: '',
          trigger: 'change'
        }],
        hold_num: [{
          required: true,
          type: 'number',
          message: '',
          trigger: 'change'
        }]
      },
      updateUrl: '',
      storeUrl: window.storeUrl,
      searchUrl: window.searchUrl,
      deleteUrl: '',
      isUpdate: false,
      isLoading: false,
      suggestOptions: [],
      isSearching: false,
      searchTimer: null,
      touchFixedTimer: null,
      lastTouchFixedTime: 0,
      fixedIndex: fixedIndex ? parseInt(fixedIndex) : 0,
      isImagePreview: false,
      previewImage: '',
      isShowUploadDialog: false,
      maxImageNum: 10,
      uploadImages: [],
      uploadPercent: 0,
      toUpdated: [],
      isShowUploadedDialog: false,
      uploadedImagesInfo: [],
      currentInfoIndex: 0,
      currentImageInfo: false
    };
  },
  computed: {
    imageUploadLeft: function imageUploadLeft() {
      return this.maxImageNum - this.uploadImages.length;
    },
    saveUploadedInfoBtnTxt: function saveUploadedInfoBtnTxt() {
      switch (true) {
        case this.isLoading:
          return '处理中..';

        case this.currentImageInfo.method === 'DELETE':
          return '删除';

        case this.currentImageInfo.method === 'PUT':
          return '更新';

        case this.currentImageInfo.method === 'POST':
          return '新增';

        default:
          return 'Error';
      }
    }
  },
  methods: {
    assignStockDetails: function assignStockDetails(detail) {
      detail = detail || {};
      this.stockDetail = {
        cate1: detail.cate1 || '',
        name: detail.name || '',
        code: detail.code || '',
        cost: detail.cost || 0,
        hold_num: detail.hold_num || 0
      };
    },
    handleSortChange: function handleSortChange(_ref) {
      var prop = _ref.prop,
          order = _ref.order;

      if (order === null) {
        localStorage.removeItem('egg-table-sort-prop');
      } else {
        localStorage.setItem('egg-table-sort-prop', prop);
        localStorage.setItem('egg-table-sort-order', order);
      }
    },
    handleCreate: function handleCreate() {
      var _this = this;

      if (this.isUpdate !== false) {
        this.assignStockDetails();
        this.isUpdate = false;
      }

      this.dialogTitle = 'New Stock';
      this.isShowEditDialog = true;
      this.$nextTick(function () {
        return _this.$refs['stockDetail'].clearValidate();
      });
    },
    handleEdit: function handleEdit(row) {
      this.assignStockDetails(row);
      this.dialogTitle = 'Edit Stock';
      this.isShowEditDialog = true;
      this.isUpdate = true;
      this.updateUrl = row.edit;
    },
    handleDelete: function handleDelete(url) {
      var _this2 = this;

      this.$confirm('确认删除此持仓？', '', {
        type: 'warning'
      }).then(function () {
        _this2.sendRequest('delete', url).then(function (b) {
          if (b) {
            setTimeout(function () {
              return location.reload();
            }, 666);
          }
        });
      })["catch"](function () {});
    },
    submitForm: function submitForm() {
      var _this3 = this;

      this.$refs['stockDetail'].validate(function (res) {
        if (res !== true) {
          return false;
        }

        _this3.isLoading = true;
        var url = _this3.isUpdate ? _this3.updateUrl : _this3.storeUrl,
            method = _this3.isUpdate ? 'put' : 'post';

        _this3.sendRequest(method, url, _this3.stockDetail).then(function (b) {
          if (b) {
            setTimeout(function () {
              return location.reload();
            }, 666);
          }
        });
      });
    },
    sendRequest: function sendRequest(method, url, data) {
      var _this4 = this;

      return axios({
        method: method,
        url: url,
        data: data
      }).then(function (response) {
        _this4.$message.success('操作成功');

        return true;
      })["catch"](function (error) {
        console.log(error.response);
        _this4.isLoading = false;

        if (!error.response || error.response.status !== 422 || !error.response.data || _typeof(error.response.data.errors) !== 'object') {
          return _this4.$message.error('操作失败：' + "".concat(error.message, ", Code:").concat(error.code));
        }

        var data = error.response.data,
            errors = data.errors;
        var msg = [];
        Object.keys(errors).forEach(function (v) {
          msg.push(errors[v][0]);
        });

        _this4.$message.error(msg.join('；'));

        return false;
      });
    },
    search: function search(keyword) {
      var _this5 = this;

      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(function () {
        var params = {
          keyword: keyword,
          type: 'fund'
        };
        axios.get(_this5.searchUrl, {
          params: params
        }).then(function (response) {
          _this5.suggestOptions = response.data || [];
        })["catch"](function (error) {
          _this5.suggestOptions = [];
        }).then(function () {
          return _this5.isSearching = false;
        });
      }, 600);
    },
    handleSelectedStock: function handleSelectedStock(code) {
      this.stockDetail.code = code;
    },
    getSummaries: function getSummaries(param) {
      var columns = param.columns,
          data = param.data,
          sums = [],
          computedColumns = ['estimate_ratio', 'real_ratio', 'cost_amount', 'profit_amount', 'profit_ratio'];
      var totalCost = 0,
          totalProfit = 0,
          totalProfitRatio = 0,
          totalTodayEstimateAmount = 0,
          totalTodayRealAmount = 0;
      data.forEach(function (item) {
        totalCost += Number(item.cost_amount);
        totalProfit += Number(item.profit_amount);
        totalTodayEstimateAmount += Number(item.today_estimate);
        totalTodayRealAmount += Number(item.today_real);
      });
      totalCost = totalCost.toFixed(2);
      totalProfit = totalProfit.toFixed(2);
      totalProfitRatio = (totalProfit / totalCost * 100).toFixed(2) + '%';
      columns.forEach(function (column, index) {
        if (index === 0) {
          sums[index] = '合计';
          return;
        }

        if (computedColumns.indexOf(column.property) === -1) {
          sums[index] = '';
          return;
        }

        var map = {
          estimate_ratio: totalTodayEstimateAmount.toFixed(2),
          // > 0 ? this.withColor(totalTodayEstimateAmount) : '',
          real_ratio: totalTodayRealAmount.toFixed(2),
          // > 0 ? this.withColor(totalTodayRealAmount) : '',
          cost_amount: totalCost,
          profit_amount: totalProfit,
          profit_ratio: totalProfitRatio
        };
        sums[index] = map[column.property] || '';
      });
      return sums;
    },
    handleFixedTouchStart: function handleFixedTouchStart(e) {
      var _this6 = this;

      e.preventDefault();
      this.lastTouchFixedTime = new Date().getTime();
      this.touchFixedTimer = setTimeout(function () {
        _this6.touchFixedTimer = null;

        if (_this6.fixedIndex >= 1) {
          _this6.fixedIndex = 0;
        } else {
          _this6.fixedIndex++;
        }

        localStorage.setItem('egg-table-fixed-index', _this6.fixedIndex);
      }, 1000);
    },
    handleFixedTouchEnd: function handleFixedTouchEnd() {
      clearTimeout(this.touchFixedTimer);

      if (new Date().getTime() - this.lastTouchFixedTime < 1000) {
        this.handleClickFixed();
      }
    },
    handleClickFixed: function handleClickFixed() {
      switch (this.fixedIndex) {
        case 0:
          this.handleCreate();
          break;

        case 1:
          location.reload();
          break;
      }
    },
    withColor: function withColor(num) {
      var className = num > 0 ? 't-red' : num < 0 ? 't-green' : 't-grey';
      return "<span class=\"".concat(className, "\">").concat(num, "</span>");
    },
    handlePreviewImage: function handlePreviewImage(url) {
      if (!url) return;
      this.previewImage = url;
      this.isImagePreview = true;
    },
    previewBeforeUpload: function previewBeforeUpload(file) {
      this.handlePreviewImage(file.url);
    },
    exceedMaxUploadImage: function exceedMaxUploadImage() {
      this.$message.error('单次最多可上传10张，还可以选' + this.imageUploadLeft + '张');
    },
    beforeRemoveUploadImage: function beforeRemoveUploadImage() {
      return this.$confirm('确认删除移除此图片？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      });
    },
    handleUploadImagesChange: function handleUploadImagesChange(file, fileList) {
      var _this7 = this;

      this.uploadImages = fileList.filter(function (value) {
        if (value.size > 10 * 1024 * 1024) {
          _this7.$message.error('已过滤超过2M的图片！');

          return false;
        }

        return true;
      });
    },
    startUpload: function startUpload() {
      var _this8 = this;

      if (!(this.uploadImages.length > 0)) {
        return this.$message.error('至少选择一张图片');
      }

      this.isLoading = true;
      var form = new FormData(),
          fileNum = 0;
      this.uploadImages.forEach(function (image) {
        if (image.raw) {
          form.append('image[]', image.raw, image.name);
          fileNum++;
        }
      });

      if (fileNum === 0) {
        this.isLoading = false;
        return this.$message.error('暂无可上传的图片！');
      }

      var axiosConfig = {
        onUploadProgress: function onUploadProgress(progressEvent) {
          _this8.uploadPercent = (progressEvent.loaded / progressEvent.total * 100).toFixed(1);
        }
      };
      axios.post(window.updateByImgUrl, form, axiosConfig).then(function (res) {
        var data = res.data || {};

        if (data.code !== 0) {
          return _this8.$message.error(data.msg || '图片上传功能异常');
        }

        _this8.uploadedImagesInfo = data.data || [];

        _this8.assignUploadImageInfo();

        _this8.isShowUploadedDialog = true;
      })["catch"](function (e) {
        console.error(e);

        _this8.$message.error(e.msg || '上传功能异常');
      }).then(function () {
        return _this8.isLoading = false;
      });
    },
    assignUploadImageInfo: function assignUploadImageInfo() {
      var info = this.uploadedImagesInfo[this.currentInfoIndex] || {},
          data = info.data || {};

      if (data["new"]) {
        info['cost'] = Number(info.data["new"].cost);
        info['hold_num'] = Number(info.data["new"].hold_num);
      }

      info.old = data.old || false;
      info.newCate1 = info.cate1 + '';
      this.currentImageInfo = info;
    },
    handlePageChange: function handlePageChange(next) {
      var _this9 = this;

      this.currentInfoIndex += next ? 1 : -1;
      this.$nextTick(function () {
        return _this9.assignUploadImageInfo();
      });
    },
    saveUploaded: function saveUploaded() {
      var _this10 = this;

      console.log(this.currentImageInfo);
      this.currentImageInfo.cate1 = this.currentImageInfo.newCate1 + '';
      this.$refs['uploadedInfo'].validate(function (res) {
        if (!res) {
          return;
        }

        _this10.isLoading = true;

        _this10.sendRequest(_this10.currentImageInfo.method, _this10.currentImageInfo.request, _this10.currentImageInfo).then(function () {
          return _this10.isLoading = false;
        });
      });
    },
    closeUploadedDialog: function closeUploadedDialog() {
      if (this.currentInfoIndex == this.uploadedImagesInfo.length - 1) {
        location.reload();
        return this.isShowUploadedDialog = false;
      }

      this.$confirm('还未全部确认，是否关闭？', '').then(function () {
        return location.reload();
      })["catch"](function () {});
    }
  }
});
/******/ })()
;