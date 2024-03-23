const Encore = require('@symfony/webpack-encore');
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .addStyleEntry('css/dashboard', ['./assets/css/dashboard.css'])
    .addStyleEntry('css/login', ['./assets/css/login.css'])
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