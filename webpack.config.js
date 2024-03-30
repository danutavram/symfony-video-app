const Encore = require('@symfony/webpack-encore');
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/assets/')
    // public path used by the web server to access the output path
    .setPublicPath('/assets')
    // only needed for CDN's or sub-directory deploy
    // .addEntry('app', './assets/js/app.js')
    .addEntry('js/likes', './assets/js/likes.js')
    .addStyleEntry('css/dashboard', ['./assets/css/dashboard.css'])
    .addStyleEntry('css/login', ['./assets/css/login.css'])
    .addStyleEntry('css/likes', ['./assets/css/likes.css'])
    
    // .enableStimulusBridge('./assets/controllers.json').splitEntryChunks()
    .enableSingleRuntimeChunk()
    // .enableBuildNotifications()

    // .configureBabel((config) => {
    //     config.plugins.push('@babel/plugin-transform-class-properties');
    // })

    // .configureBabelPresetEnv((config) => {
    //     config.useBuiltIns = 'usage';
    //     config.corejs = 3;
    // })
    
;

module.exports = Encore.getWebpackConfig();