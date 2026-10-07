<template>
  <div class="lb-page">
    <slot name="button">
      <!-- 插入按钮 -->
    </slot>
    <div v-if="batch">
      <div :class="[{ isShowBatch: isShowBatch }]">已选 {{ selected }} 条</div>
      <div>
        <span v-if="isShowBatch">批量</span>
        <slot>
          <!-- 插入按钮 -->
        </slot>
      </div>
    </div>
    <div v-if="slotName">
      <slot>
        <!-- 插入按钮 -->
      </slot>
    </div>
    <!-- <lb-button size="mini" type="primary" @click="batchUpperAll(1)">
           <i class="iconfont" :class="[{'iconyduifuxuankuang':multipleSelection.length < tableData.length},{'iconyduifuxuankuangxuanzhong': multipleSelection.length === tableData.length}]"></i>
           全选</lb-button>
        <lb-button
          size="mini"
          type="danger"
          @click="batchUpperAll(2)"
          style="margin-left:10px"
        >
          <i class="iconfont" :class="[{'iconyduifuxuankuang':multipleSelection.length < tableData.length},{'iconyduifuxuankuangxuanzhong': multipleSelection.length === tableData.length}]"></i>反选
        </lb-button> -->
    <span v-else></span>
    <el-pagination
      v-if="isPagination"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
      :current-page="currentPage"
      :page-sizes="[5, 10, 20]"
      :page-size="currentPageSize"
      layout="total, sizes, prev, pager, next, jumper"
      :total="total"
    >
    </el-pagination>
    <span v-else></span>
  </div>
</template>

<script>
export default {
  props: {
    isPagination: {
      type: Boolean,
      default: true
    },
    isShowBatch: {
      type: Boolean,
      default: true
    },
    batch: {
      type: Boolean,
      default: true
    },
    slotName: {
      type: Boolean,
      default: false
    },
    page: {
      type: Number,
      default: 1
    },
    pageSize: {
      type: Number,
      default: 10
    },
    total: {
      type: Number,
      default: 0
    },
    selected: {
      type: Number,
      default: 0
    }
  },
  data () {
    return {
      currentPage: this.page,
      currentPageSize: this.pageSize
    }
  },
  methods: {
    handleSizeChange (val) {
      this.currentPageSize = val
      this.$emit('handleSizeChange', val)
    },
    handleCurrentChange (val) {
      this.currentPage = val
      this.$emit('handleCurrentChange', val)
    },
    // 全选 反选
    batchUpperAll (type) {
      if (type === 1) {
        this.$refs.multipleTable.clearSelection()
        this.multipleSelection = this.tableData
        this.tableData.map(item => {
          this.$refs.multipleTable.toggleRowSelection(item)
        })
      } else {
        let data = JSON.parse(JSON.stringify(this.multipleSelection))
        let arr = []
        data.map(item => {
          arr.push(item.id)
        })
        this.$refs.multipleTable.clearSelection()
        this.multipleSelection = []
        this.tableData.map(item => {
          if (!arr.includes(item.id)) {
            this.multipleSelection.push(item)
            this.$refs.multipleTable.toggleRowSelection(item)
          }
        })
      }
    }
  },
  watch: {
    page (val) {
      this.currentPage = val
    },
    pageSize (val) {
      this.currentPageSize = val
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-page {
  width: 100%;
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  height: 40px;
  font-size: 14px;
  > div {
    display: flex;
    align-items: center;
    white-space: nowrap;
    > div.isShowBatch {
      border-right: 1px solid #e8e8e8;
    }
    > div {
      &:first-child {
        height: 40px;
        padding-right: 30px;
        margin-right: 30px;
        line-height: 40px;
      }
      .el-button {
        margin-left: 10px;
      }
    }
  }
}
</style>
