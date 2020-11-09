new Vue({
  el: '#egg',
  data() {
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
    }
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
      console.log(prop, order);
      localStorage.setItem('egg-table-sort-prop', prop);
      localStorage.setItem('egg-table-sort-order', order);
    },
    handleCreate() {
      if (this.isUpdate !== false) {
        this.assignStockDetails();
        this.isUpdate = false;
      }
      this.dialogTitle = 'New Stock';
      this.isShowDialog = true;
      this.$nextTick(() => this.$refs['stockDetail'].clearValidate());
    },

    handleEdit(row) {
      this.assignStockDetails(row);
      this.dialogTitle = 'Edit Stock';
      this.isShowDialog = true;
      this.isUpdate = true;
      this.updateUrl = row.edit;
    },

    handleDelete(url) {
      this.$confirm('确认删除此持仓？', '', {
        type: 'warning',
      })
        .then(() => {
          this.sendRequest('delete', url);
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

        this.sendRequest(method, url, this.stockDetail);
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
          setTimeout(() => location.reload(), 666);
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
          computedColumns = ['cost_amount', 'profit_amount', 'profit_ratio'];
        let totalCost = 0,
          totalProfit = 0,
          totalProfitRatio = 0;
        data.forEach(item => {
          totalCost += Number(item.cost_amount);
          totalProfit += Number(item.profit_amount);
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
          sums[index] = column.property === 'cost_amount'
            ? totalCost
            : column.property === 'profit_amount' ? totalProfit : totalProfitRatio;
        });

        return sums;
      }
  },
});
