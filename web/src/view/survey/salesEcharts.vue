<template>
  <e-charts
    v-if="!isEmpty"
    id="count-echarts"
    theme="ovilia-green"
    :options="echartsOptions"
    ref="myecharts"
  ></e-charts>
  <div v-else class="empty">暂无数据</div>
</template>

<script>
import ECharts from 'vue-echarts/components/ECharts.vue'
import 'echarts/lib/chart/pie'
import 'echarts/lib/component/polar'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'
import 'echarts/lib/chart/line'
export default {
  props: {
    datas: {
      type: Array,
      default: () => {
        return []
      }
    }
  },
  data () {
    return {
      isEmpty: false,
      echartsOptions: {
        color: ['#00DDFF', '#37A2FF', '#FF0087'],
        legend: {
          top: 20,
          data: ['销售额']
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross',
            label: {
              backgroundColor: '#6a7985'
            }
          }
        },
        xAxis: [
          {
            type: 'category',
            boundaryGap: false,
            data: []
          }
        ],
        yAxis: [
          {
            type: 'value'
          }
        ],
        series: [{
          name: '销售额',
          type: 'line',
          stack: '总量',
          smooth: true,
          areaStyle: {
            color: new ECharts.graphic.LinearGradient(0, 0, 0, 1, [{
              offset: 0,
              color: 'rgba(0, 221, 255)'
            },
            {
              offset: 1,
              color: 'rgba(77, 119, 255)'
            }])
          },
          emphasis: {
            focus: 'series'
          },
          data: []
        }]
      }
    }
  },
  components: {
    ECharts
  },
  created () {
    if (this.datas.length) {
      this.handleDatas(this.datas)
    }
  },
  mounted () {
    window.addEventListener('resize', this.loadEcharts)
  },
  methods: {
    handleDatas (data) {
      this.echartsOptions.series[0].data = data.map(item => {
        return item.shop_price || 0
      })
      this.echartsOptions.xAxis[0].data = data.map(item => {
        return item.time_text
      })
    },
    loadEcharts () {
      if (this.$refs['myecharts']) {
        setTimeout(() => {
          this.$refs['myecharts'].resize()
        }, 10)
      }
    }
  },
  watch: {
    datas (val) {
      if (!val.length) {
        this.isEmpty = true
      } else {
        this.isEmpty = false
        this.handleDatas(val)
      }
    }
  },
  destroyed () {
    window.removeEventListener('resize', this.loadEcharts)
  }
}
</script>

<style lang="scss" scoped>
#count-echarts {
  width: 100%;
  height: 400px;
}
.empty {
  height: 400px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 20px;
  color: #09f;
}
</style>
