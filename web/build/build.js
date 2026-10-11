'use strict'
require('./check-versions')()

process.env.NODE_ENV = 'production'

const ora = require('ora')
const rm = require('rimraf')
const path = require('path')
const chalk = require('chalk')
const webpack = require('webpack')
const config = require('../config')
const webpackConfig = require('./webpack.prod.conf')

const spinner = ora('building for production...')
spinner.start()

rm(path.join(config.build.assetsRoot, config.build.assetsSubDirectory), err => {
  if (err) throw err
  webpack(webpackConfig, (err, stats) => {
    spinner.stop()
    if (err) throw err
    process.stdout.write(stats.toString({
      colors: true,
      modules: false,
      children: false, // If you are using ts-loader, setting this to true will make TypeScript errors show up during build.
      chunks: false,
      chunkModules: false
    }) + '\n\n')

    if (stats.hasErrors()) {
      console.log(chalk.red('  Build failed with errors.\n'))
      process.exit(1)
    }

    console.log(chalk.cyan('  Build complete.\n'))
    console.log(chalk.yellow(
      '  Tip: built files are meant to be served over an HTTP server.\n' +
      '  Opening index.html over file:// won\'t work.\n'
    ))

    //自动把构建产物同步到本地后端静态目录（本地后台 nginx 直接生效，免去手动拷贝）
    //生产部署仍需把 backend/public/static 上传服务器
    const shell = require('shelljs')
    const destStatic = path.resolve(__dirname, '../../backend/public/static')
    if (require('fs').existsSync(path.resolve(__dirname, '../../backend/public'))) {
      const syncSpinner = ora('syncing dist/static -> backend/public/static...')
      syncSpinner.start()
      shell.rm('-rf', destStatic)
      shell.cp('-R', path.join(config.build.assetsRoot, config.build.assetsSubDirectory), destStatic)
      syncSpinner.stop()
      console.log(chalk.cyan('  static 已同步到 backend/public/static（本地后台生效）；生产部署请上传该目录。\n'))
    }
  })
})
