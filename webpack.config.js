const Encore = require("@symfony/webpack-encore");

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore.setOutputPath("public/build/")
  .setPublicPath("/build")

  //Default
  .addStyleEntry("styles", "./assets/styles/app.scss")
  .addEntry("app1", "./assets/app.js")

  //Admin
  .addStyleEntry("admin", "./assets/styles/admin.scss")
  .addEntry("admin-app", "./assets/admin-app.js")

  //Loto
  .addStyleEntry("Lotostyles", "./assets/styles/src/react/Loto.scss")
  .addEntry("Loto", "./assets/react/Loto/index.js")

  //Alfa
  .addStyleEntry("AlfaStyle", "./assets/react/Apps/Alfa/Style/Alfa.scss")
  .addEntry("Alfa", "./assets/react/Apps/Alfa/index.js")

  //Touch
  .addStyleEntry("TouchStyle", "./assets/react/Apps/Touch/Style/Touch.scss")
  .addEntry("Touch", "./assets/react/Apps/Touch/index.js")

  //windsl
  .addStyleEntry("WindslStyle", "./assets/react/Apps/Windsl/Style/Windsl.scss")
  .addEntry("Windsl", "./assets/react/Apps/Windsl/index.js")

  //Ogero
  .addStyleEntry("OgeroStyle", "./assets/react/Apps/Ogero/Style/Ogero.scss")
  .addEntry("Ogero", "./assets/react/Apps/Ogero/index.js")

 //TerraNet
  .addEntry("TerraNet", "./assets/react/Apps/TerraNet/index.js")
  .addStyleEntry("terraNetstyle", "./assets/react/Apps/TerraNet/Style/TerraNet.scss")

  //Gift2Games
   .addEntry("Gift2Games", "./assets/react/Apps/Gift2Games/index.js")
   .addStyleEntry("Gift2GamesStyle", "./assets/react/Apps/Gift2Games/Style/Gift2Games.scss")

  .addStyleEntry("iveristyles", "./assets/styles/src/react/Iveri.scss")
  .addStyleEntry("SodetelStyle", "./assets/styles/src/react/Sodetel.scss")

  .addEntry("app1", "./assets/app.js")
  .addEntry("admin-app", "./assets/admin-app.js")


   //Simly
  .addStyleEntry("SimlyStyle", "./assets/react/Apps/Simly/Style/Simly.scss")
  .addEntry("Simly", "./assets/react/Apps/Simly/index.js")

  //Legal Enrollement
  .addStyleEntry("LegalEnrollementStyle", "./assets/styles/src/react/LegalEnrollement.scss")
  .addEntry("LegalEnrollement", "./assets/react/LegalEnrollement/index.js")

  //Iveri
  .addStyleEntry("iveristyles", "./assets/styles/src/react/Iveri.scss")
  .addEntry("iveri", "./assets/react/Iveri/index.js")


  //Sodetel
  .addStyleEntry("SodetelStyle", "./assets/styles/src/react/Sodetel.scss")
  .addEntry("Sodetel", "./assets/react/Sodetel/index.js")


  .copyFiles({
    from: "./assets/images",
    to: "images/[path][name].[ext]",
  })
  .enableSassLoader()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableReactPreset()
  .autoProvidejQuery();

module.exports = Encore.getWebpackConfig();
