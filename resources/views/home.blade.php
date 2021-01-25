@extends('layout.index')

@section('body')
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
          </template>
        </el-table-column>
      </el-table>
    </el-main>
  </el-container>

  <el-dialog :title="dialogTitle" :visible.sync="isShowEditDialog" :close-on-click-modal="false" class="t-detail">
    <el-form ref="stockDetail" label-position="right" label-width="80px" :model="stockDetail" :rules="stockRules" status-icon>
      <el-form-item label="名称" prop="name">
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
      </el-form-item>
      <el-form-item label="板块" prop="cate1">
        <el-input maxlength="10" show-word-limit v-model="stockDetail.cate1"></el-input>
      </el-form-item>
      <el-form-item label="成本价" prop="cost">
        <el-input-number v-model="stockDetail.cost" :step="0.0001" step-strictly :min="0"></el-input-number>
      </el-form-item>
      <el-form-item label="持有份数" prop="hold_num">
        <el-input-number v-model="stockDetail.hold_num" :step="0.01" step-strictly :min="0"></el-input-number>
      </el-form-item>
    </el-form>

    <div slot="footer">
      <el-button @click="isShowEditDialog = false">取 消</el-button>
      <el-button type="primary" @click="submitForm" :loading="isLoading">确 定</el-button>
    </div>
  </el-dialog>

  <el-dialog :title="dialogTitle" :visible.sync="isShowUploadDialog" :close-on-click-modal="false" class="t-uploader">
    <el-upload
      multiple
      action="https://jsonplaceholder.typicode.com/posts/"
      list-type="picture-card"
      :auto-upload="false"
      :file-list="uploadImages"
      :on-preview="previewBeforeUpload"
      accept="image/png, image/jpeg"
      :limit="10"
    >
      <template slot="tip">
        一次性最多上传10张图片
      </template>
      <i class="el-icon-plus"></i>
    </el-upload>

    <div slot="footer">
      <el-button @click="isShowUploadDialog = false">取 消</el-button>
      <el-button type="primary" @click="startUpload" :loading="isLoading">开始上传</el-button>
    </div>
  </el-dialog>

  <el-dialog :visible.sync="isImagePreview">
    <img width="100%" :src="previewImage" alt="">
  </el-dialog>

  <div
    class="t-upload-btn el-backtop"
    @click="isShowUploadDialog = true"
  >
    <i class="el-icon-upload"></i>
  </div>

  <div
    class="t-create-btn el-backtop"
    @click="handleClickFixed"
    @touchstart="handleFixedTouchStart"
    @touchend="handleFixedTouchEnd"
  >
    <i class="el-icon-circle-plus-outline" v-if="fixedIndex === 0"></i>
    <i class="el-icon-refresh" v-else-if="fixedIndex === 1"></i>
  </div>
@endsection

@section('custom_footer')
  <script>
    window.originalStocks = '{!! json_encode($stocks) !!}';
    window.storeUrl = '{{ route('egg.userStock.store') }}';
    window.searchUrl = '{{ route('egg.search') }}';
    window.tRealDate = '{{ $t_real_date }}';
    window.tEstimateDate = '{{ $t_estimate_date }}';
  </script>
  <script src="{{ mix('js/home.js') }}"></script>
@endsection
