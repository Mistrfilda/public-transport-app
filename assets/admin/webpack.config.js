var Encore = require('@symfony/webpack-encore');
require('dotenv').config();

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

var publicPath = '/public-transport-app/www/build/admin';
var prodHost = process.env.PROD_HOST;
if (prodHost !== undefined) {
    publicPath = '/build/admin';
}

Encore
    .setOutputPath('../../www/build/admin')
    .setPublicPath(publicPath)
    .addEntry('admin', './js/app.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild(['**/*', '!.gitkeep'])
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .autoProvideVariables({
        'naja': 'naja',
        'markerCluster': 'markerCluster'
    })
    .autoProvidejQuery();

module.exports = Encore.getWebpackConfig();
