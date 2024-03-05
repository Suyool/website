const Encore = require("@symfony/webpack-encore");
//const WebpackConcatPlugin = require('webpack-concat-files-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
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
  .setOutputPath("public/build/")
  // public path used by the web server to access the output path
  .setPublicPath("/build")
  // only needed for CDN's or sub-directory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */
  .addStyleEntry("styles", "./assets/styles/app.scss")
  .addStyleEntry("admin", "./assets/styles/admin.scss")
  .addStyleEntry("Lotostyles", "./assets/styles/src/react/Loto.scss")
  .addStyleEntry("LegalEnrollementStyle", "./assets/styles/src/react/LegalEnrollement.scss")

  //Alfa
  .addStyleEntry("AlfaStyle", "./assets/react/Apps/Alfa/Style/Alfa.scss")
  .addEntry("Alfa", "./assets/react/Apps/Alfa/index.js")

  //Touch
  .addStyleEntry("TouchStyle", "./assets/react/Apps/Touch/Style/Touch.scss")
  .addEntry("Touch", "./assets/react/Apps/Touch/index.js")
  // .addStyleEntry("TouchStyle", "./assets/styles/src/react/Touch.scss")
  // .addEntry("Touch", "./assets/react/Touch/index.js")

  //Ogero
  .addStyleEntry("OgeroStyle", "./assets/react/Apps/Ogero/Style/Ogero.scss")
  .addEntry("Ogero", "./assets/react/Apps/Ogero/index.js")
  // .addStyleEntry("OgeroStyle", "./assets/styles/src/react/Ogero.scss")
  // .addEntry("Ogero", "./assets/react/Ogero/index.js")

  .addStyleEntry("Gift2GamesStyle", "./assets/styles/src/react/Gift2Games.scss")
  .addStyleEntry("iveristyles", "./assets/styles/src/react/Iveri.scss")
  .addStyleEntry("terraNetstyle", "./assets/styles/src/react/TerraNet.scss")
  .addStyleEntry("SodetelStyle", "./assets/styles/src/react/Sodetel.scss")
  .addStyleEntry('SimlyStyle', './assets/styles/src/react/Simly.scss')
  // .addStyleEntry('adminStyles', './assets/styles/admin.scss')
  // .addEntry('app', './assets/app.js')
  // .addEntry('app', './assets/react/index.js')
  .addEntry("app1", "./assets/app.js")
  .addEntry("admin-app", "./assets/admin-app.js")
  .addEntry("Loto", "./assets/react/Loto/index.js")
  .addEntry("LegalEnrollement", "./assets/react/LegalEnrollement/index.js")
  //   .addEntry("Alfa", "./assets/react/Alfa/index.js")
  .addEntry("Gift2Games", "./assets/react/Gift2Games/index.js")
  .addEntry("iveri", "./assets/react/Iveri/index.js")
  .addEntry("TerraNet", "./assets/react/TerraNet/index.js")
  .addEntry("Sodetel", "./assets/react/Sodetel/index.js")
  .addEntry('Simly','./assets/react/Simly/index.js')
  // .addEntry('admin', './assets/admin.js')
  //    .addEntry('app', ['./assets/js/common.js','./assets/js/breakingNews.js','./assets/js/adCounter.js'])
  // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
  //.enableStimulusBridge('./assets/controllers.json')
  .copyFiles({
    from: "./assets/images",
    to: "images/[path][name].[ext]",
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
  .enableReactPreset()

  // uncomment to get integrity="..." attributes on your script & link tags
  // requires WebpackEncoreBundle 1.4 or higher
  //.enableIntegrityHashes(Encore.isProduction())

  // uncomment if you're having problems with a jQuery plugin
  .autoProvidejQuery();

module.exports = Encore.getWebpackConfig();
