const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('./src/Resources/public/')
    .setPublicPath('/')
    .setManifestKeyPrefix('bundles/ibexacustomsettings')
    .addEntry('ibexa_custom_settings', './assets/app.js')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableReactPreset()
;

module.exports = Encore.getWebpackConfig();