/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/home.js":
/*!******************************!*\
  !*** ./resources/js/home.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

new Vue({
  el: '#egg',
  data: function data() {
    var fixedIndex = localStorage.getItem('egg-table-fixed-index'); // For Test

    var uploadedImagesInfo = JSON.parse('[{"url":"/storage/upload/20210129/8d9b56e4dc5361c81011ace1b3d161ea.png","data":false},{"url":"/storage/upload/20210129/f06b4e10344ef8b000f4f4ef811f82c8.png","code":"001593","name":"天弘创业板ETF联接基金C","cate1":"指数","request":"http://127.0.0.1:8000/userStock/41","method":"PUT","data":{"old":{"cost":"1.2011","hold_num":"3330.29"},"new":{"cost":"1.0464","hold_num":"955.66"}}},{"url":"/storage/upload/20210129/bd62e94d9204595cb313fc7390c159ad.png","code":"161724","name":"招商中证煤炭等权指数分级","cate1":"煤炭","request":"http://127.0.0.1:8000/userStock/78","method":"DELETE","data":{"old":{"cost":"1.0264","hold_num":"6820.18"},"new":{"cost":"0.0000","hold_num":"2233.08"}}}]');
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
      uploadedImagesInfo: uploadedImagesInfo,
      //: [],
      currentInfoIndex: 0,
      currentImageInfo: {}
    };
  },
  computed: {
    imageUploadLeft: function imageUploadLeft() {
      return this.maxImageNum - this.uploadImages.length;
    }
  },
  created: function created() {
    this.assignUploadImageInfo();
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
        _this2.sendRequest('delete', url);
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

        _this3.sendRequest(method, url, _this3.stockDetail);
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

        setTimeout(function () {
          return location.reload();
        }, 666);
      })["catch"](function (error) {
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
    assignUploadImageInfo: function assignUploadImageInfo() {
      this.currentImageInfo = this.uploadedImagesInfo[this.currentInfoIndex] || {};
      console.log(this.currentImageInfo);
    },
    handlePageChange: function handlePageChange(next) {
      var _this8 = this;

      this.currentInfoIndex += next ? 1 : -1;
      this.$nextTick(function () {
        return _this8.assignUploadImageInfo();
      });
    },
    startUpload: function startUpload() {
      var _this9 = this;

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
          _this9.uploadPercent = (progressEvent.loaded / progressEvent.total * 100).toFixed(1);
        }
      };
      axios.post(window.updateByImgUrl, form, axiosConfig).then(function (res) {
        var data = res.data || {};

        if (data.code !== 0) {
          return _this9.$message.error(data.msg || '图片上传功能异常');
        }

        _this9.uploadedImagesInfo = data.data || [];

        _this9.assignUploadImageInfo();

        _this9.isShowUploadedDialog = true;
      })["catch"](function (e) {
        console.error(e);

        _this9.$message.error(e.msg || '上传功能异常');
      }).then(function () {
        return _this9.isLoading = false;
      });
    }
  }
});

/***/ }),

/***/ 1:
/*!************************************!*\
  !*** multi ./resources/js/home.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/namet/Code/Egg/resources/js/home.js */"./resources/js/home.js");


/***/ })

/******/ });