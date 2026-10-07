<template>
	<view class="video-box" :style="{paddingBottom: number}">
		<video class="my-video" object-fit="fill" preload="meta" :src="url" @play="play" @pause="pause" @ended="ended"
			@waiting="waitingCallback" @error="errorCallback" @loadedmetadata="loadedmetadata" autoplay="true"
			controls></video>
	</view>
</template>

<script>
	import {
		mapState,
		mapActions
	} from 'vuex';
	export default {
		data() {
			return {
				url: '',
				number: '',
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
			let url = decodeURIComponent(options.url)
			this.url = url
		},
		methods: {
			play(e) {
				this.$util.log("play=> ", e);
			},
			pause(e) {
				this.$util.log("pause=> ", e);
			},
			ended(e) {
				this.$util.log("ended=> ", e);
			},
			waitingCallback(e) {
				this.$util.log("waitingCallback=> ", e);
			},
			errorCallback(e) {
				this.$util.log("errorCallback=> ", e);
			},
			loadedmetadata(e) {
				this.$util.log("loadedmetadata=> ", e);
				let {
					width,
					height
				} = e.detail;
				let num = ((height / width) * 100).toFixed(2)
				this.number = num ? `${num}%` : `56.25%`
			}
		}

	}
</script>

<style>
	.video-box {
		position: relative;
		width: 100%;
		height: 0;
		/* padding-bottom: 56.25%; */
		/*用 9/16 得出，其他比例类似*/
	}

	.my-video {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		align-items: center;
	}
</style>
