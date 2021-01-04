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
    var fixedIndex = localStorage.getItem('egg-table-fixed-index');
    return {
      tableHeight: navigator.userAgent.toLowerCase().indexOf('andriod') > -1 ? '90vh' : '97.5vh',
      stocks: JSON.parse(window.originalStocks) || [],
      tRealDate: window.tRealDate,
      tEstimateDate: window.tEstimateDate,
      dialogTitle: '',
      isShowDialog: false,
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
      fixedIndex: fixedIndex ? parseInt(fixedIndex) : 0
    };
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
      this.isShowDialog = true;
      this.$nextTick(function () {
        return _this.$refs['stockDetail'].clearValidate();
      });
    },
    handleEdit: function handleEdit(row) {
      this.assignStockDetails(row);
      this.dialogTitle = 'Edit Stock';
      this.isShowDialog = true;
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
          computedColumns = ['cost_amount', 'profit_amount', 'profit_ratio'];
      var totalCost = 0,
          totalProfit = 0,
          totalProfitRatio = 0;
      data.forEach(function (item) {
        totalCost += Number(item.cost_amount);
        totalProfit += Number(item.profit_amount);
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

        sums[index] = column.property === 'cost_amount' ? totalCost : column.property === 'profit_amount' ? totalProfit : totalProfitRatio;
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

module.exports = __webpack_require__(/*! /var/www/Egg/resources/js/home.js */"./resources/js/home.js");


/***/ })

/******/ });