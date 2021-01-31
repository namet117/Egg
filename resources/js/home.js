new Vue({
  el: '#egg',
  data() {
    let fixedIndex = localStorage.getItem('egg-table-fixed-index');
    // For Test
    let uploadedImagesInfo = JSON.parse('[{"url":"/storage/upload/20210131/bc9945428f8111c9736d820e9ce7f670.png","code":"501018","name":"南方原油A","cate1":"原油","request":"http://dev.namet.xyz:10000/userStock/31","method":"PUT","data":{"old":{"cost":"0.8101","hold_num":"9875.21"},"new":{"cost":"0.8101","hold_num":"9875.21"}}},{"url":"/storage/upload/20210131/7b3c5829ab095d8427944748e68391e1.png","code":"008087","name":"华夏中证5G通信主题ETF联接C","cate1":"5G","request":"http://dev.namet.xyz:10000/userStock/26","method":"PUT","data":{"old":{"cost":"1.2511","hold_num":"3885.60"},"new":{"cost":"1.2384","hold_num":"4733.06"}}},{"url":"/storage/upload/20210131/18a4a5aaf1054de367766bce8a53141b.png","code":"000083","name":"汇添富消费行业混合","cate1":"消费","request":"http://dev.namet.xyz:10000/userStock/72","method":"PUT","data":{"old":{"cost":"7.5916","hold_num":"1510.76"},"new":{"cost":"8.8972","hold_num":"224.79"}}}]');
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
        hold_num: '',
      },
      defaultTableSort: {
        prop: localStorage.getItem('egg-table-sort-prop') || '',
        order: localStorage.getItem('egg-table-sort-order') || 'ascending',
      },
      stockRules: {
        cate1: [
          { required: true, message: '', trigger: 'change' },
        ],
        name: [
          { required: true, message: '', trigger: 'change' },
        ],
        code: [
          { required: true, message: '', trigger: 'change' },
        ],
        cost: [
          { required: true, type: 'number', message: '', trigger: 'change' },
        ],
        hold_num: [
          { required: true, type: 'number', message: '', trigger: 'change' },
        ]
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
      uploadedImagesInfo, // : [],
      currentInfoIndex: 0,
      currentImageInfo: {},
    }
  },
  computed: {
    imageUploadLeft() {
      return this.maxImageNum - this.uploadImages.length;
    },
    saveUploadedInfoBtnTxt() {
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
    },
  },
  created() {
    this.assignUploadImageInfo();
  },
  methods: {
    assignStockDetails(detail) {
      detail = detail || {};
      this.stockDetail = {
        cate1: detail.cate1 || '',
        name: detail.name || '',
        code: detail.code || '',
        cost: detail.cost || 0,
        hold_num: detail.hold_num || 0,
      };
    },
    handleSortChange({ prop, order }) {
      if (order === null) {
        localStorage.removeItem('egg-table-sort-prop');
      } else {
        localStorage.setItem('egg-table-sort-prop', prop);
        localStorage.setItem('egg-table-sort-order', order);
      }
    },
    handleCreate() {
      if (this.isUpdate !== false) {
        this.assignStockDetails();
        this.isUpdate = false;
      }
      this.dialogTitle = 'New Stock';
      this.isShowEditDialog = true;
      this.$nextTick(() => this.$refs['stockDetail'].clearValidate());
    },

    handleEdit(row) {
      this.assignStockDetails(row);
      this.dialogTitle = 'Edit Stock';
      this.isShowEditDialog = true;
      this.isUpdate = true;
      this.updateUrl = row.edit;
    },

    handleDelete(url) {
      this.$confirm('确认删除此持仓？', '', {
        type: 'warning',
      })
        .then(() => {
          this.sendRequest('delete', url)
            .then(b => {
              if (b) {
                setTimeout(() => location.reload(), 666);
              }
            });
        })
        .catch(() => { });
    },

    submitForm() {
      this.$refs['stockDetail'].validate(res => {
        if (res !== true) {
          return false;
        }
        this.isLoading = true;
        let url = this.isUpdate ? this.updateUrl : this.storeUrl,
          method = this.isUpdate ? 'put' : 'post';

        this.sendRequest(method, url, this.stockDetail)
          .then(b => {
            if (b) {
              setTimeout(() => location.reload(), 666);
            }
          });
      })
    },

    sendRequest(method, url, data) {
      return axios({
        method,
        url,
        data,
      })
        .then(response => {
          this.$message.success('操作成功');
          return true;
        })
        .catch(error => {
          this.isLoading = false;
          if (
            !error.response
            || error.response.status !== 422
            || !error.response.data
            || typeof error.response.data.errors !== 'object'
          ) {
            return this.$message.error('操作失败：' + `${error.message}, Code:${error.code}`);
          }
          let data = error.response.data, errors = data.errors;
          let msg = [];
          Object.keys(errors).forEach(v => {
            msg.push(errors[v][0]);
          });
          this.$message.error(msg.join('；'));
          return false;
        });
    },

    search(keyword) {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        let params = {
          keyword,
          type: 'fund',
        };
        axios.get(this.searchUrl, { params })
          .then(response => {
            this.suggestOptions = response.data || [];
          })
          .catch(error => {
            this.suggestOptions = [];
          })
          .then(() => this.isSearching = false);
      }, 600);
    },

    handleSelectedStock(code) {
      this.stockDetail.code = code;
    },

    getSummaries(param) {
      const { columns, data } = param,
        sums = [],
        computedColumns = ['estimate_ratio', 'real_ratio', 'cost_amount', 'profit_amount', 'profit_ratio'];
      let totalCost = 0,
        totalProfit = 0,
        totalProfitRatio = 0,
        totalTodayEstimateAmount = 0,
        totalTodayRealAmount = 0;
      data.forEach(item => {
        totalCost += Number(item.cost_amount);
        totalProfit += Number(item.profit_amount);
        totalTodayEstimateAmount += Number(item.today_estimate);
        totalTodayRealAmount += Number(item.today_real);
      });
      totalCost = totalCost.toFixed(2);
      totalProfit = totalProfit.toFixed(2);
      totalProfitRatio = ((totalProfit / totalCost) * 100).toFixed(2) + '%';

      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = '合计';
          return;
        }
        if (computedColumns.indexOf(column.property) === -1) {
          sums[index] = '';
          return;
        }
        let map = {
          estimate_ratio: totalTodayEstimateAmount.toFixed(2), // > 0 ? this.withColor(totalTodayEstimateAmount) : '',
          real_ratio: totalTodayRealAmount.toFixed(2), // > 0 ? this.withColor(totalTodayRealAmount) : '',
          cost_amount: totalCost,
          profit_amount: totalProfit,
          profit_ratio: totalProfitRatio,
        };

        sums[index] = map[column.property] || '';
      });

      return sums;
    },

    handleFixedTouchStart(e) {
      e.preventDefault();
      this.lastTouchFixedTime = new Date().getTime();
      this.touchFixedTimer = setTimeout(() => {
        this.touchFixedTimer = null;
        if (this.fixedIndex >= 1) {
          this.fixedIndex = 0;
        } else {
          this.fixedIndex++;
        }
        localStorage.setItem('egg-table-fixed-index', this.fixedIndex);
      }, 1000);
    },

    handleFixedTouchEnd() {
      clearTimeout(this.touchFixedTimer);
      if (new Date().getTime() - this.lastTouchFixedTime < 1000) {
        this.handleClickFixed();
      }
    },

    handleClickFixed() {
      switch (this.fixedIndex) {
        case 0:
          this.handleCreate();
          break;
        case 1:
          location.reload();
          break;
      }
    },

    withColor(num) {
      let className = num > 0 ? 't-red' : (num < 0 ? 't-green' : 't-grey');

      return `<span class="${className}">${num}</span>`;
    },

    handlePreviewImage(url) {
      if (!url) return ;
      this.previewImage = url;
      this.isImagePreview = true;
    },

    previewBeforeUpload(file) {
      this.handlePreviewImage(file.url);
    },
    exceedMaxUploadImage() {
      this.$message.error('单次最多可上传10张，还可以选' + this.imageUploadLeft + '张');
    },
    beforeRemoveUploadImage() {
      return this.$confirm('确认删除移除此图片？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      });
    },
    handleUploadImagesChange(file, fileList) {
      this.uploadImages = fileList.filter(value => {
        if (value.size > 10 * 1024 * 1024) {
          this.$message.error('已过滤超过2M的图片！');
          return false;
        }
        return true;
      });
    },
    startUpload() {
      if (!(this.uploadImages.length > 0)) {
        return this.$message.error('至少选择一张图片');
      }
      this.isLoading = true;
      let form = new FormData, fileNum = 0;
      this.uploadImages.forEach(image => {
        if (image.raw) {
          form.append('image[]', image.raw, image.name);
          fileNum ++;
        }
      });
      if (fileNum === 0) {
        this.isLoading = false;
        return this.$message.error('暂无可上传的图片！');
      }

      let axiosConfig = {
        onUploadProgress: progressEvent => {
          this.uploadPercent = (progressEvent.loaded / progressEvent.total * 100).toFixed(1);
        },
      };
      axios.post(window.updateByImgUrl, form, axiosConfig)
        .then(res => {
          let data = res.data || {};
          if (data.code !== 0) {
            return this.$message.error(data.msg || '图片上传功能异常');
          }
          this.uploadedImagesInfo = data.data || [];
          this.assignUploadImageInfo();
          this.isShowUploadedDialog = true;
        })
        .catch(e => {
          console.error(e);
          this.$message.error(e.msg || '上传功能异常');
        })
        .then(() => this.isLoading = false);
    },
    assignUploadImageInfo() {
      let info = this.uploadedImagesInfo[this.currentInfoIndex] || {},
        data = info.data || {};
      if (data.new) {
        info['cost'] = info.data.new.cost;
        info['hold_num'] = info.data.new.hold_num;
      }
      info.old = data.old || false;
      this.currentImageInfo = info;
    },
    handlePageChange(next) {
      this.currentInfoIndex += (next ? 1 : -1);
      this.$nextTick(() => this.assignUploadImageInfo());
    },
    saveUploaded() {
      this.$refs['uploadedInfo'].validate(res => {
        if (!res) {
          return ;
        }
        this.isLoading = true;
        this.sendRequest(this.currentImageInfo.method, this.currentImageInfo.request, this.currentImageInfo)
          .then(() => this.isLoading = false);
      });
    },
    closeUploadedDialog() {
      if (this.currentInfoIndex == (this.uploadedImagesInfo.length - 1)) {
        location.reload();
        return this.isShowUploadedDialog = false;
      }
      this.$confirm('还未全部确认，是否关闭？', '')
        .then(() => location.reload())
        .catch(() => {});
    },
  },
});
