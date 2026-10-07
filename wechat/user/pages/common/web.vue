<template>
	<view>
		<web-view :src="url"></web-view>
	</view>
</template>

<script>
	import {
		mapState,
	} from 'vuex';
	export default {
		data() {
			return {
				url: ''
			}
		},
		computed: mapState({
			primaryColor: state => state.config.configInfo.primaryColor,
			subColor: state => state.config.configInfo.subColor,
		}),
		async onLoad(options) {
			this.$util.setNavigationBarColor({
				bg: this.primaryColor
			})
			let {
				url
			} = options;
			this.url = decodeURIComponent(url);
		}
	}
</script>

<style lang="scss">
</style>
