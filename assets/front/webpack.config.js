var Encore = require('@symfony/webpack-encore');
require('dotenv').config();

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

var publicPath = '/public-transport-app/www/build/front';
var prodHost = process.env.PROD_HOST;
if (prodHost !== undefined) {
    publicPath = 'www/build/front';
}

Encore
    .setOutputPath('../../www/build/front')
    .setPublicPath(publicPath)
    .addEntry('front', './ts/app.ts')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enablePostCssLoader()
    .enableTypeScriptLoader();

module.exports = Encore.getWebpackConfig();
