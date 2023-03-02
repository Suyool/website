const Encore = require('@symfony/webpack-encore');
//const WebpackConcatPlugin = require('webpack-concat-files-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
/*    .addPlugin(
        new MergeIntoSingle({
            files: {
                'app.js': [
                    'assets/js/!*.js'
                ],
            }
        })
    )
    .addPlugin(
        new WebpackConcatPlugin({
            bundles: [
                {
                    src: './assets/js/src/*.js',
                    dest: './assets/js/script.js'
                },
            ],
        })
    )*/
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addStyleEntry('styles', './assets/styles/app.scss')
    .addStyleEntry('adminStyles', './assets/styles/admin.scss')
    .addEntry('app', './assets/app.js')
    .addEntry('admin', './assets/admin.js')
//    .addEntry('app', ['./assets/js/common.js','./assets/js/breakingNews.js','./assets/js/adCounter.js'])
    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]'
    })
    // processes files ending in .scss or .sass
    .enableSassLoader()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())


    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
