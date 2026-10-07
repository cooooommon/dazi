<!--
 * @Descripttion: 交易设置
 * @Author: xiao li
 * @Date: 2020-09-28 15:24:24
 * @LastEditors: wen kun
 * @LastEditTime: 2024-08-07 14:03:19
-->
<template>
  <div class="lb-system-city">
    <top-nav />
    <div class="page-main">
      <lb-tips>2022年11月15日以前创建的城市数据需手动修改</lb-tips>
      <el-row class="page-top-operate">
        <lb-button
          size="medium"
          type="primary"
          icon="el-icon-plus"
          style="margin-right: 5px"
          @click="toShowDialog({ city_type: 1 })"
          v-hasPermi="`${$route.name}-add`"
          >{{ $t('menu.SystemCityAdd') }}</lb-button
        >
        <lb-button
          type="danger"
          :icon="isTreeOpen ? 'el-icon-arrow-up' : 'el-icon-arrow-down'"
          @click="switchTreeOpen"
          >全部{{ isTreeOpen ? '关闭' : '展开' }}</lb-button
        >
      </el-row>
      <el-table
        v-loading="loading"
        v-if="showTable"
        :data="tableData"
        :header-cell-style="{ background: '#f5f7fa', color: '#606266' }"
        style="width: 100%"
        row-key="id"
        :default-expand-all="isTreeOpen"
        :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
      >
        <el-table-column prop="title" label="城市"></el-table-column>
        <el-table-column prop="lng" label="经度"></el-table-column>
        <el-table-column prop="lat" label="纬度"></el-table-column>
        <el-table-column prop="create_time" label="创建时间">
          <template slot-scope="scope">
            <p>{{ scope.row.create_time | handleTime(1) }}</p>
            <p>{{ scope.row.create_time | handleTime(2) }}</p>
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right">
          <template slot-scope="scope">
            <div class="table-operate">
              <lb-button
                size="mini"
                plain
                type="primary"
                @click="toShowDialog(scope.row)"
                v-show="scope.row.city_type !== 3"
                v-hasPermi="`${$route.name}-edit`"
                >{{ $t('action.edit') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                type="danger"
                @click="confirmDel(scope.row.id, scope.row.pid)"
                v-hasPermi="`${$route.name}-delete`"
                >{{ $t('action.delete') }}</lb-button
              >
              <lb-button
                size="mini"
                plain
                :type="scope.row.city_type === 1 ? 'success' : 'warning'"
                @click="
                  toShowDialog({
                    pid: scope.row.id,
                    city_type: scope.row.city_type === 1 ? 2 : 1,
                    province_code: scope.row.province_code,
                    city_code: scope.row.city_code,
                    area_code: scope.row.area_code,
                    province: scope.row.province,
                    city: scope.row.city,
                    area: scope.row.area,
                    is_screen: scope.row.is_screen
                  })
                "
                v-show="
                  (scope.row.city_type === 3 &&
                    !provinceArr.includes(scope.row.province_code * 1)) ||
                  (scope.row.city_type === 1 &&
                    scope.row.province_code * 1 !== 710000)
                "
                v-hasPermi="`${$route.name}-add`"
                >{{
                  $t(
                    scope.row.city_type === 1
                      ? 'menu.SystemCityAreaAdd'
                      : 'menu.SystemCityAdd'
                  )
                }}</lb-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>
      <lb-page
        :batch="false"
        :page="searchForm.page"
        :pageSize="searchForm.limit"
        :total="total"
        @handleSizeChange="handleSizeChange"
        @handleCurrentChange="handleCurrentChange"
      >
      </lb-page>
    </div>
    <el-dialog
      :title="
        $t(
          subForm.city_type === 2
            ? subForm.id
              ? 'menu.SystemCityAreaEdit'
              : 'menu.SystemCityAreaAdd'
            : subForm.id
            ? 'menu.SystemCityEdit'
            : 'menu.SystemCityAdd'
        )
      "
      :visible.sync="showDialog"
      width="500px"
      center
    >
      <el-form
        @submit.native.prevent
        :model="subForm"
        ref="subForm"
        :rules="subFormRules"
        label-width="100px"
      >
        <el-form-item
          :label="cityType[subForm.city_type]"
          prop="selectedOptions"
        >
          <el-cascader
            :disabled="subForm.id && subForm.city_type !== 2 ? true : false"
            size="large"
            :options="areaOptions"
            v-model="subForm.selectedOptions"
            @change="handleChange"
            :placeholder="`请选择${cityType[subForm.city_type]}`"
            :props="{ checkStrictly: true }"
          ></el-cascader>
        </el-form-item>
        <el-form-item label="经度" prop="lng">
          <el-input v-model="subForm.lng" placeholder="请输入经度"></el-input>
        </el-form-item>
        <el-form-item label="纬度" prop="lat">
          <el-input v-model="subForm.lat" placeholder="请输入纬度"></el-input>
        </el-form-item>
        <el-form-item label="" prop="">
          <lb-button
            class="mr-md"
            @click="showMap = true"
            plain
            type="primary"
            >{{ $t('action.getLatLng') }}</lb-button
          >
        </el-form-item>
        <el-form-item
          label="是否被筛选"
          prop="company_pay"
          v-if="subForm.city_type === 2"
        >
          <el-radio-group v-model="subForm.is_screen">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
          <lb-tool-tips>选择开启，可在手机端选择该区县</lb-tool-tips>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">{{
          $t('action.cancel')
        }}</el-button>
        <el-button type="primary" @click="submitForm" v-preventReClick>{{
          $t('action.comfirm')
        }}</el-button>
      </span>
    </el-dialog>
    <lb-map
      :address="
        subForm.area
          ? subForm.province === subForm.city
            ? `${subForm.city}${subForm.area}`
            : `${subForm.province}${subForm.city}${subForm.area}`
          : subForm.city
          ? subForm.province === subForm.city
            ? subForm.city
            : `${subForm.province}${subForm.city}`
          : subForm.province
      "
      :dialogVisible.sync="showMap"
      @selectedLatLng="getLatLng"
    ></lb-map>
  </div>
</template>

<script>
import moment from 'moment'
import { provinceAndCityData, regionData } from 'element-china-area-data'
export default {
  data () {
    let checkCityArea = (rule, value, callback) => {
      let { city_type: ctype } = this.subForm
      let { provinceArr, cityArr } = this
      if (ctype === 1 && (value.length === 0 || (value.length < 2 && !provinceArr.includes(value[0] * 1)))) {
        callback(new Error('请选择城市'))
      } else if (ctype === 2 && (value.length === 1 || (value.length < 3 && !cityArr.includes(value[0] * 1)))) {
        callback(new Error('请选择具体区县'))
      } else {
        callback()
      }
    }
    return {
      total: 0,
      cityType: {
        1: '城市',
        2: '区县',
        3: '省'
      },
      areaOptions: provinceAndCityData,
      // 110000 北京
      // 120000 天津
      // 310000 上海
      // 500000 重庆
      // 710000 台湾
      // 810000 香港
      // 820000 澳门
      // 410000 河南
      // 420000 湖北
      // 460000 海南
      // 650000 新疆
      provinceArr: [110000, 120000, 310000, 500000, 710000, 810000, 820000],
      cityArr: [710000, 810000, 820000],
      cityCodeArr: [410000, 420000, 460000, 650000],
      loading: false,
      showDialog: false,
      isTreeOpen: true,
      showTable: true,
      showMap: false,
      searchForm: {
        page: 1,
        limit: 10
      },
      subForm: {
        id: 0,
        city_type: 1,
        pid: 0,
        selectedOptions: [],
        title: '',
        lng: '',
        lat: '',
        province: '',
        city: '',
        area: '',
        province_code: '',
        city_code: '',
        area_code: '',
        is_screen: 1
      },
      tableData: [],
      subFormRules: {
        selectedOptions: { required: true, validator: checkCityArea, trigger: 'blur' },
        lng: { required: true, validator: this.$reg.isLng, trigger: 'blur' },
        lat: { required: true, validator: this.$reg.isLat, trigger: 'blur' }
      }
    }
  },
  created () {
    this.getTableDataList(1)
  },
  methods: {
    handleSizeChange (val) {
      this.searchForm.limit = val
      this.handleCurrentChange(1)
    },
    handleCurrentChange (val) {
      this.searchForm.page = val
      this.getTableDataList()
    },
    async getTableDataList (flag) {
      if (flag) this.searchForm.page = 1
      this.tableData = []
      this.loading = true
      let { code, data } = await this.$api.system.cityList(this.searchForm)
      this.loading = false
      if (code !== 200) return
      this.tableData = data.data
      this.total = data.total
    },
    switchTreeOpen () {
      this.isTreeOpen = !this.isTreeOpen
    },
    toShowDialog (item = { is_screen: 1 }) {
      let data = JSON.parse(JSON.stringify(item))
      let { pid = 0, city_type: ctype, province_code: pcode, city_code: ccode, children = [] } = data
      let regionDataCopy = JSON.parse(JSON.stringify(regionData))
      let provinceAndCityDataCopy = JSON.parse(JSON.stringify(provinceAndCityData))
      let cityAndAreaArr = regionDataCopy.filter(item => {
        return item.value === '500000'
      })
      // return
      regionDataCopy.map((item, index) => {
        if (this.cityCodeArr.includes(item.value * 1)) {
          item.children.map((citem, cindex) => {
            if (citem.label === '省直辖县级行政区划' || citem.label === '自治区直辖县级行政区划') {
              let ccitem = JSON.parse(JSON.stringify(citem.children))
              regionDataCopy[index].children.splice(cindex, 1, ...ccitem)
              provinceAndCityDataCopy[index].children.splice(cindex, 1, ...ccitem)
            }
          })
        }
      })
      if (pid) {
        // 重庆市
        if (pcode === '500000') {
          let a = cityAndAreaArr[0].children[0].children
          let b = cityAndAreaArr[0].children[1].children
          cityAndAreaArr[0].children[0].children = a.concat(b)
          cityAndAreaArr[0].children = [cityAndAreaArr[0].children[0]]
        } else {
          cityAndAreaArr = regionDataCopy.filter(item => {
            return item.value === pcode
          })
          cityAndAreaArr.map(item => {
            if (item.children) {
              item.children.map(aitem => {
                if (aitem.value === ccode) {
                  if (ctype === 1 && children.length === 0) {

                  } else {
                    cityAndAreaArr[0].children = [aitem]
                  }
                }
              })
            }
          })
        }
      }

      provinceAndCityDataCopy.map(item => {
        if (item.value * 1 === 500000 || [810000, 820000].includes(item.value * 1) || (item.children && item.children.length === 1 && item.children[0].label === '市辖区')) {
          delete item.children
        }
      })

      let areaOptions = (ctype === 1 && pid) || ctype === 2 ? cityAndAreaArr : provinceAndCityDataCopy
      let arr = ['province_code']
      if (ctype === 1) {
        arr.push('city_code')
      }
      if (ctype === 1 && pid) {
        areaOptions.map(item => {
          item.disabled = true
          if (item.children) {
            item.children.map(aitem => {
              delete aitem.children
            })
          }
        })
      }
      if (ctype === 2) {
        arr = arr.concat(['city_code', 'area_code'])
        areaOptions.map(item => {
          item.disabled = true
          if (!this.cityArr.includes(pcode * 1) && item.children) {
            if (item.children) {
              item.children.map(aitem => {
                aitem.disabled = true
              })
            }
          }
        })
      }
      this.areaOptions = areaOptions
      data.selectedOptions = []
      arr.map(aitem => {
        if (data[aitem]) {
          data.selectedOptions.push(data[aitem])
        }
      })
      for (let key in this.subForm) {
        this.subForm[key] = data[key]
      }
      this.showDialog = true
    },
    handleChange (e) {
      let { pid = 0 } = this.subForm
      let provinceArr = [110000, 120000, 310000, 500000]
      let codeArr = {
        110000: 110100,
        120000: 120100,
        310000: 310100,
        500000: 500100
      }
      let province = e[0] || ''
      let city = provinceArr.includes(province) ? codeArr[province] : e && e.length > 1 ? e[1] : ''
      let area = e && e.length > 2 ? e[2] : ''
      this.subForm.province_code = province
      this.subForm.city_code = city
      if (pid) {
        this.subForm.area_code = area
      }
      this.subForm.province = ''
      this.subForm.city = ''
      this.subForm.area = ''
      this.subForm.title = ''
      this.subForm.lat = ''
      this.subForm.lng = ''
      this.areaOptions.map((item, index) => {
        if (item.value === e[0]) {
          this.subForm.province = item.label
          if (item.children && item.children.length > 0) {
            item.children.map(aitem => {
              if (aitem.value === e[1]) {
                this.subForm.city = aitem.label
                if (pid && aitem.children && aitem.children.length > 0) {
                  aitem.children.map(bitem => {
                    if (bitem.value === e[2]) {
                      this.subForm.area = bitem.label
                    }
                  })
                }
              }
            })
          }
        }
      })
    },
    clickCity (e) {
      this.showMap = true
    },
    getLatLng (latLng) {
      this.subForm.lng = latLng.lng
      this.subForm.lat = latLng.lat
    },
    confirmDel (id, pid) {
      this.$confirm(this.$t('tips.confirmDelete'), this.$t('tips.reminder'), {
        confirmButtonText: this.$t('action.comfirm'),
        cancelButtonText: this.$t('action.cancel'),
        type: 'warning'
      }).then(() => {
        this.$api.system.cityUpdate({ id, status: -1 }).then((res) => {
          if (res.code === 200) {
            this.$message.success(this.$t('tips.successOper'))
            if (!pid) {
              this.searchForm.page = this.searchForm.page < Math.ceil((this.total - 1) / this.searchForm.limit) ? this.searchForm.page : Math.ceil((this.total - 1) / this.searchForm.limit)
            }
            this.getTableDataList()
          }
        })
      })
    },
    submitForm () {
      this.$refs['subForm'].validate((valid) => {
        if (valid) {
          let subForm = JSON.parse(JSON.stringify(this.subForm))
          let { selectedOptions } = subForm
          let { provinceArr, cityArr } = this
          subForm.city_type = subForm.id ? subForm.city_type : subForm.area || (subForm.pid && cityArr.includes(selectedOptions[0] * 1)) ? 2 : subForm.city || (provinceArr.includes(selectedOptions[0] * 1)) ? 1 : 0
          delete subForm.selectedOptions
          // city_type 1市；2区县；3省
          if (!subForm.city || ['市辖区', '县'].includes(subForm.city)) {
            subForm.city = subForm.province
          }
          subForm.title = subForm.area || subForm.city || subForm.province
          let type = subForm.id ? 'cityUpdate' : 'cityAdd'
          this.$api.system[type](subForm).then((res) => {
            if (res.code === 200) {
              this.$message.success(this.$t('tips.successSub'))
              this.showDialog = false
              this.getTableDataList()
            }
          })
        }
      })
    }
  },
  watch: {
    isTreeOpen () {
      this.showTable = false
      this.$nextTick(() => {
        this.showTable = true
      })
    }
  },
  filters: {
    handleTime (val, type) {
      let time = type === 1 ? moment(val * 1000).format('YYYY-MM-DD') : type === 2 ? moment(val * 1000).format('HH:mm:ss') : moment(val * 1000).format('YYYY-MM-DD HH:mm:ss')
      return time
    }
  }
}
</script>

<style lang="scss" scoped>
.lb-system-city {
  .el-form {
    width: 100%;
    .el-form-item {
      margin-bottom: 24px;
      .el-cascader,
      .el-select,
      .el-input-number,
      .el-input {
        width: 300px;
      }
    }
  }
}
</style>
