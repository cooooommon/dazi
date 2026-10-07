import Vue from 'vue'
import Vuex from 'vuex'
import routes from './modules/routes'
import operate from './modules/operate'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    routes,
    operate
  },
  state: {
    adSwitch: false,
    sideBarSwitch: false,
    uploadStatus: false
  },
  getters: {
    adSwitch: state => {
      return state.adSwitch
    },
    sideBarSwitch: state => {
      return state.sideBarSwitch
    },
    uploadStatus: state => {
      return state.uploadStatus
    }
  },
  mutations: {
    handleAdSwitch (state, value) {
      state.adSwitch = value
    },
    handleSideBarSwitch (state, value) {
      state.sideBarSwitch = value
    },
    handleUploadStatus (state, value) {
      state.uploadStatus = value
    }
  },
  actions: {}
})

export default store
