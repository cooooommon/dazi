<template>
  <el-dialog
    title="获取经纬度"
    :visible.sync="centerDialogVisible"
    width="600px"
    center
  >
    <div class="dialog-inner">
      <div class="map-search">
        <el-input placeholder="输入地址" v-model="address"></el-input>
        <lb-button size="mini" type="primary" @click="searchMapAddr"
          >搜 索</lb-button
        >
      </div>
      <div id="container"></div>
    </div>
    <span slot="footer" class="dialog-footer">
      <el-button @click="centerDialogVisible = false">{{
        $t('action.cancel')
      }}</el-button>
      <el-button type="primary" @click="confirmLatLng">{{
        $t('action.comfirm')
      }}</el-button>
    </span>
  </el-dialog>
</template>

<script>
export default {
  props: {
    dialogVisible: {
      type: Boolean,
      default: false
    },
    address: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      centerDialogVisible: false,
      map: null,
      info: null,
      marker: '',
      geocoder: null,
      latLng: {
        lat: 30.657535,
        lng: 104.065783
      }
    }
  },
  methods: {
    async initMap () {
      let that = this
      let { lat, lng } = this.latLng
      // 中心坐标
      // eslint-disable-next-line no-undef
      let center = new qq.maps.LatLng(lat, lng)
      // eslint-disable-next-line no-undef
      let map = new qq.maps.Map(document.getElementById('container'), {
        center: center,
        zoom: 12
      })
      that.map = map
      // eslint-disable-next-line no-undef
      that.info = new qq.maps.InfoWindow({
        map: map
      })
      // eslint-disable-next-line no-undef
      qq.maps.event.addListener(map, 'click', function (val, el) {
        if (that.marker) {
          that.marker.setMap(null)
        }
        let { lat, lng } = val.latLng
        that.latLng = val.latLng
        // eslint-disable-next-line no-undef
        that.marker = new qq.maps.Marker({
          // 标记的位置
          // eslint-disable-next-line no-undef
          position: new qq.maps.LatLng(lat, lng),
          map: map
        })
        that.info.open()
        that.info.setContent(`<div style="margin:10px;">
        <p>纬度：${lat}</p>
        <p>经度：${lng}</p>
        </div>`)
        // eslint-disable-next-line no-undef
        that.info.setPosition(new qq.maps.LatLng(lat, lng))
      })
      if (this.address) {
        this.searchMapAddr()
      }
    },
    openQQMap () {
      setTimeout(() => {
        this.initMap()
        // this.searchMapAddr();
      }, 500)
    },
    /**
     * @method 根据位置搜索坐标
     */
    async searchMapAddr () {
      // this.geocoder.getLocation(address);
      let { address } = this
      let that = this
      let { data } = await this.$api.base.addressToLocation({ address })
      // 后端在地址无法解析（缺市级/无结果）时返回 data:null
      if (!data || data.status !== 0) {
        that.$message.error('请输入包含市级的地址！')
        return
      }
      let { lat, lng } = data.result.location
      that.latLng = data.result.location
      that.map.setCenter(new qq.maps.LatLng(lat, lng))
      // eslint-disable-next-line no-undef
      that.marker = new qq.maps.Marker({
        map: that.map,
        position: new qq.maps.LatLng(lat, lng)
      })
      that.info.open()
      that.info.setContent(`<div style="margin:10px;">
        <p>纬度：${lat}</p>
        <p>经度：${lng}</p>
        </div>`)
      // eslint-disable-next-line no-undef
      that.info.setPosition(new qq.maps.LatLng(lat, lng))
    },
    initGeocoder () {
      let that = this
      // eslint-disable-next-line no-undef
      that.geocoder = new qq.maps.Geocoder()

      that.geocoder.setComplete(function (result) {
        let { lat, lng } = result.detail.location
        that.latLng = result.detail.location
        that.map.setCenter(result.detail.location)
        // eslint-disable-next-line no-undef
        that.marker = new qq.maps.Marker({
          map: that.map,
          position: result.detail.location
        })
        that.info.open()
        that.info.setContent(`<div style="margin:10px;">
        <p>纬度：${lat}</p>
        <p>经度：${lng}</p>
        </div>`)
        // eslint-disable-next-line no-undef
        that.info.setPosition(new qq.maps.LatLng(lat, lng))
      })
      // 若服务请求失败，则运行以下函数
      that.geocoder.setError(function () {
        that.$message.error('请输入包含市级的地址！')
      })
    },
    /**
     * @method 确定经纬度
     */
    async confirmLatLng () {
      this.centerDialogVisible = false
      let { data } = await this.$api.base.locationToAddress(this.latLng)
      let address = ''
      if (data.status === 0) {
        address = data.result.formatted_addresses.standard_address + ' ' + data.result.formatted_addresses.recommend
      } else {
        this.$message.error(data.message)
      }
      this.$emit('selectedLatLng', { ...this.latLng, address })
    }
  },
  watch: {
    dialogVisible (newValue, oldValue) {
      if (newValue) {
        this.centerDialogVisible = true
        this.openQQMap()
      }
    },
    centerDialogVisible (val) {
      if (!val) {
        this.$emit('update:dialogVisible', false)
      }
    }
  }
}
</script>

<style lang="scss" scoped>
#container {
  width: 500px;
  height: 400px;
  margin: 0 auto;
}
.dialog-inner {
  .map-search {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    .el-input {
      width: 300px;
      margin-right: 10px;
    }
  }
}
</style>
