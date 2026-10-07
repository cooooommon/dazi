<!--
 * @Description:
 * @Author: wen kun
 * @Date: 2023-10-19 16:35:52
 * @LastEditTime: 2024-02-27 17:26:35
 * @LastEditors: wen kun
-->
<template>
  <div class="flex-center pt-md">
    <div class="pl-lg pr-lg flex-between module ml-md" v-if="storeplus">
      <div class="rel" style="width: 177px">
        <img
          class="wizard"
          src="https://lbqny.migugu.com/admin/playwith/service/01.png"
          mode="aspectFill"
        />
        <span class="c-base f-sm-title abs wizard-title"
          >{{ $t('action.attendantName') }}入驻</span
        >
      </div>
      <div class="">
        <div class="rel">
          <img
            class="business"
            src="https://lbqny.migugu.com/admin/playwith/service/02.png"
            mode="aspectFill"
          />
          <span class="c-base f-sm-title abs business-title">商家入驻</span>
        </div>
        <div class="mt-sm rel" style="width: 178px">
          <img
            class="join"
            src="https://lbqny.migugu.com/admin/playwith/service/03.png"
            mode="aspectFill"
          />
          <span class="c-base f-sm-title abs join-title">合作加盟</span>
        </div>
      </div>
    </div>
    <div class="pl-lg pr-lg flex-between module ml-md" v-else>
      <div class="rel mr-md" style="width: 166px">
        <img
          class="_wizard"
          :src="`https://lbqny.migugu.com/admin/playwith/service/04.png`"
          mode="aspectFill"
        />
        <span class="c-base f-sm-title abs _wizard-title"
          >{{ $t('action.attendantName') }}入驻</span
        >
      </div>
      <div class="rel" style="width: 178px">
        <img
          class="_join"
          src="https://lbqny.migugu.com/admin/playwith/service/03.png"
          mode="aspectFill"
        />
        <span class="c-base f-sm-title abs join-title">合作加盟</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'search',
  props: {
    o: Object,
    storeplus: Number
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.module {
  .wizard {
    width: 177px;
    height: 130px;
  }
  .business {
    width: 168px;
    height: 60px;
  }
  .join {
    width: 178px;
    height: 60px;
    margin-left: -10px;
  }
  .wizard-title {
    top: 14px;
    left: 14px;
  }
  ._wizard-title {
    top: 11px;
    left: 14px;
  }
  .business-title,
  .join-title {
    top: 11px;
    left: 20px;
  }
  ._wizard {
    width: 166px;
    height: 60px;
  }
  ._join {
    width: 178px;
    height: 60px;
    margin-left: -10px;
  }
}
</style>
