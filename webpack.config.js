// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
    // каталог проекта , где будут храниться все скомпилированные ресурсы
    .setOutputPath('public/build/')

    // публичный путь, используемый веб-сервером для доступа к предыдущему каталогу
    .setPublicPath('/build')

    // создаст public/build/app.js и public/build/app.css
    .addEntry('app', './assets/js/app.js')

    // позволит обработку файлов sass/scss
    .enableSassLoader()

    // позволить приложениям наследования использовать $/jQuery в качестве глобальной переменной
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // очистить outputPath dir перед каждым построением
    .cleanupOutputBeforeBuild()

    // показать уведомления ОС при окончании/неудаче построения
    .enableBuildNotifications()

// создать хешированные имена файлов (например, app.abc123.css)
// .enableVersioning()
;

// экспортировать финальную конфигурацию
module.exports = Encore.getWebpackConfig();