@extends('egg.layout')

@section('content')
  <el-container>
    <el-main>
      <el-table
        :data="stocks"
        size="mini"
        border
        :height="tableHeight"
        class="t-table"
        show-summary
        :summary-method="getSummaries"
        :default-sort="defaultTableSort"
        @sort-change="handleSortChange"
      >
        <el-table-column prop="cate1" label="板块" width="50" fixed sortable>
        </el-table-column>
        <el-table-column prop="name" label="名称" fixed>
        </el-table-column>
        <el-table-column prop="code" label="编码" width="105">
          <!--<tempalte slot-scope="scope">-->
          <!--  <a :href="`http://fund.eastmoney.com/${scope.row.code}.html`" target="_blank">-->
          <!--    @{{ scope.row.code }}-->
          <!--  </a>-->
          <!--</tempalte>-->
        </el-table-column>

        <el-table-column prop="estimate_ratio" label="估值" width="100" sortable>
          <template slot="header" slot-scope="scope">
            估值
            <span v-if="{{ !empty($t_estimate_date) }}" class="t-table-tips">{{ $t_estimate_date }}</span>
          </template>
          <template slot-scope="scope">
            <el-tooltip
              effect="dark"
              :content="`估值：${scope.row.estimate}`"
              placement="top"
            >
              <span
                :class="scope.row.estimate_ratio > 0 ? 't-red' : scope.row.estimate_ratio < 0 ? 't-green' : 't-grey'"
                :title="scope.row.estimate"
              >
                @{{ scope.row.estimate_ratio }}%
                <span v-if="scope.row.estimate_date && tEstimateDate !== scope.row.estimate_date" class="t-table-tips">
                  <br>
                  @{{ scope.row.estimate_date}}
                </span>
              </span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column prop="real_ratio" label="净值" width="100" sortable>
          <template slot="header" slot-scope="scope">
            净值
            <span v-if="{{ !empty($t_real_date) }}" class="t-table-tips">{{ $t_real_date }}</span>
          </template>
          <template slot-scope="scope">
            <el-tooltip
              effect="dark"
              :content="`净值：${scope.row.real}`"
              placement="top">
              <span
                :class="scope.row.real_ratio > 0 ? 't-red' : scope.row.real_ratio < 0 ? 't-green' : 't-grey'"
                :title="scope.row.real"
              >
                @{{ scope.row.real_ratio }}%
                <span v-if="scope.row.real_date && tRealDate !== scope.row.real_date" class="t-table-tips">
                  <br>
                  @{{ scope.row.real_date}}
                </span>
              </span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column prop="cost_amount" label="持仓" width="105" sortable>
          <template slot-scope="scope">
            <span :title="scope.row.cost">@{{ scope.row.cost_amount }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="profit_amount" label="收益" width="105" sortable>
          <template slot="header" slot-scope="scope">
            收益
            <span v-if="{{ !empty($t_real_date) }}" class="t-table-tips">{{ $t_real_date }}</span>
          </template>
          <template slot-scope="scope">
            <el-tooltip
              effect="dark"
              :content="`(${scope.row.real} - ${scope.row.cost}) x ${scope.row.hold_num} = ${scope.row.profit_amount}`"
              placement="top"
            >
              <span
                :class="scope.row.profit_amount > 0 ? 't-red' : scope.row.profit_amount < 0 ? 't-green' : 't-grey'"
                :title="`(${scope.row.real} - ${scope.row.cost}) x ${scope.row.hold_num} = ${scope.row.profit_amount}`"
              >
                @{{ scope.row.profit_amount }}
                <span v-if="scope.row.real_date && tRealDate !== scope.row.real_date" class="t-table-tips">
                  <br>
                  @{{ scope.row.real_date}}
                </span>
              </span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column prop="profit_ratio" label="收益率" width="100" sortable>
          <template slot="header" slot-scope="scope">
            收益率
            <span v-if="{{ !empty($t_real_date) }}" class="t-table-tips">{{ $t_real_date }}</span>
          </template>
          <template slot-scope="scope">
            <el-tooltip
              effect="dark"
              :content="`(${scope.row.real} - ${scope.row.cost}) / ${scope.row.cost} * 100 = ${scope.row.profit_ratio}%`"
              placement="top"
            >
              <span
                :class="scope.row.profit_ratio > 0 ? 't-red' : scope.row.profit_ratio < 0 ? 't-green' : 't-grey'"
                >
                @{{ scope.row.profit_ratio }}%
                <span v-if="scope.row.real_date && tRealDate !== scope.row.real_date" class="t-table-tips">
                  <br>
                  @{{ scope.row.real_date}}
                </span>
              </span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="100">
          <template slot-scope="scope">
            <el-button @click="handleEdit(scope.row)" type="primary" icon="el-icon-edit" size="mini" circle></el-button>
            <el-button @click="handleDelete(scope.row.delete)" type="danger" icon="el-icon-delete" size="mini" circle ></el-button>
{{--            <el-button @click="handleEdit(scope.row)" type="text" size="mini" icon="el-icon-edit">编辑</el-button>--}}
{{--            <br>--}}
{{--            <el-popconfirm--}}
{{--              confirm-button-text='确认'--}}
{{--              cancel-button-text='算了'--}}
{{--              icon="el-icon-info"--}}
{{--              icon-color="red"--}}
{{--              title="确认删除此持仓信息？"--}}
{{--              @on-confirm="handleDelete(scope.row.delete)"--}}
{{--            >--}}
{{--              <el-button slot="reference" type="text" size="mini" icon="el-icon-delete" @click="handleDelete(scope.row.delete)">删除</el-button>--}}
{{--            </el-popconfirm>--}}
          </template>
        </el-table-column>
      </el-table>
    </el-main>
  </el-container>

  <el-dialog :title="dialogTitle" :visible.sync="isShowDialog" :close-on-click-modal="false" class="t-detail">
    <el-form ref="stockDetail" label-position="right" label-width="80px" :model="stockDetail" :rules="stockRules" status-icon>
      <el-form-item label="名称" prop="name">
{{--        <el-autocomplete v-model="stockDetail.name" @select="handleSelectFund"></el-autocomplete>--}}
{{--        <el-input maxlength="50" show-word-limit v-model="stockDetail.name"></el-input>--}}
        <el-select
          v-model="stockDetail.name"
          filterable
          remote
          placeholder="请输入名称或代码"
          style="width: 100%"
          :remote-method="search"
          :loading="isSearching"
          @change="handleSelectedStock"
        >
          <el-option
            v-for="item in suggestOptions"
            :key="item.code"
            :label="item.name"
            :value="item.code">
            <span style="float: left">@{{ item.name }}</span>
            <span style="float: right; color: #8492a6; font-size: 12px">@{{ item.code }}</span>
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="代码" prop="code">
        <span v-html="stockDetail.code"></span>
{{--        <el-input maxlength="50" show-word-limit v-model="stockDetail.code"></el-input>--}}
      </el-form-item>
      <el-form-item label="板块" prop="cate1">
        <el-input maxlength="10" show-word-limit v-model="stockDetail.cate1"></el-input>
      </el-form-item>
{{--      <el-form-item label="小类" prop="cate2">--}}
{{--        <el-input maxlength="10" show-word-limit v-model="stockDetail.cate2"></el-input>--}}
{{--      </el-form-item>--}}
      <el-form-item label="成本价" prop="cost">
        <el-input-number v-model="stockDetail.cost" :step="0.0001" step-strictly :min="0"></el-input-number>
      </el-form-item>
      <el-form-item label="持有份数" prop="hold_num">
        <el-input-number v-model="stockDetail.hold_num" :step="0.01" step-strictly :min="0"></el-input-number>
      </el-form-item>
    </el-form>

    <div slot="footer">
      <el-button @click="isShowDialog = false">取 消</el-button>
      <el-button type="primary" @click="submitForm" :loading="isLoading">确 定</el-button>
    </div>
  </el-dialog>

  <div class="t-create-btn el-backtop" @click="handleCreate">
    <i class="el-icon-circle-plus-outline"></i>
  </div>
@endsection

@section('custom_script')
  <script>
    window.originalStocks = '{!! json_encode($stocks) !!}';
    window.storeUrl = '{{ route('egg.userStock.store') }}';
    window.searchUrl = '{{ route('egg.search') }}';
    window.tRealDate = '{{ $t_real_date }}';
    window.tEstimateDate = '{{ $t_estimate_date }}';
  </script>
  <script src="{{ mix('js/egg/index.js') }}"></script>
@endsection
