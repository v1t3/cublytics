// webpack.config.js
const Encore = require('@symfony/webpack-encore');
const CopyPlugin = require('copy-webpack-plugin');

Encore
    // каталог проекта , где будут храниться все скомпилированные ресурсы
    .setOutputPath('public/build/')

    // публичный путь, используемый веб-сервером для доступа к предыдущему каталогу
    .setPublicPath('/build')

    // создаст public/build/app.js и public/build/app.css
    .addEntry('app', './assets/js/app.js')

    // позволит обработку файлов sass/scss
    .enableSassLoader()

    .enableSourceMaps(!Encore.isProduction())

    // очистить outputPath dir перед каждым построением
    .cleanupOutputBeforeBuild()

    // показать уведомления ОС при окончании/неудаче построения
    .enableBuildNotifications()

    .enableVueLoader()

    .addPlugin(new CopyPlugin({
        patterns: [
            {from: './assets/img', to: 'img'}
        ]
    }))

// создать хешированные имена файлов (например, app.abc123.css)
// .enableVersioning()
;

// экспортировать финальную конфигурацию
module.exports = Encore.getWebpackConfig();