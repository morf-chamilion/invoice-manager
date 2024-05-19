const mix = require("laravel-mix");
const glob = require("glob");
const path = require("path");
const ReplaceInFileWebpackPlugin = require("replace-in-file-webpack-plugin");
const rimraf = require("rimraf");
const del = require("del");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js").postCss(
    "resources/css/app.css",
    "public/css",
    [
        //
    ]
);

const dir = "resources/theme/src";

mix.options({
    cssNano: {
        discardComments: false,
    },
});

// Remove existing generated assets from public folder
del.sync(["public/assets/*"]);

// Build 3rd party plugins css/js
mix.sass(
    "resources/mix/plugins.scss",
    `public/assets/plugins/global/plugins.bundle.css`
)
    .then(() => {
        // remove unused preprocessed fonts folder
        rimraf(path.resolve("public/fonts"), () => {});
        rimraf(path.resolve("public/images"), () => {});
    })
    .sourceMaps(!mix.inProduction())
    // .setResourceRoot('./')
    .options({ processCssUrls: false })
    .scripts(
        require("./resources/mix/plugins.js"),
        `public/assets/plugins/global/plugins.bundle.js`
    );

// Build theme css/js
mix.sass(`${dir}/sass/style.scss`, `public/assets/css/style.bundle.css`, {
    sassOptions: { includePaths: ["node_modules"] },
})
    // .options({processCssUrls: false})
    .scripts(
        require(`./resources/mix/scripts.js`),
        `public/assets/js/scripts.bundle.js`
    );

// Build custom 3rd party plugins
(glob.sync(`resources/mix/vendors/**/*.js`) || []).forEach((file) => {
    mix.scripts(
        require("./" + file),
        `public/assets/${file.replace(
            "resources/mix/vendors/",
            "plugins/custom/"
        )}`
    );
});
(glob.sync(`resources/mix/vendors/**/*.scss`) || []).forEach((file) => {
    mix.sass(
        file,
        `public/assets/${file
            .replace("resources/mix/vendors/", "plugins/custom/")
            .replace("scss", "css")}`
    );
});

// Process files from 'common' directory
(glob.sync("resources/js/common/**/*.js") || []).forEach((file) => {
    const output = `public/assets/${file.replace("resources/js", "js")}`;
    mix.js(file, output);
});

// Process files from 'admin' directory
(glob.sync(`resources/js/admin/**/*.js`) || []).forEach((file) => {
    var output = `public/assets/${file.replace(`resources/js`, "js")}`;
    mix.js(file, output);
});

// Process styles from 'admin' directory
mix.sass(
    `resources/css/admin/master.scss`,
    `public/assets/css/admin/master.css`
);

// Build media
mix.copyDirectory(`${dir}/media`, `public/assets/media`);
mix.copyDirectory(`resources/media/front`, `public/assets/media/front`);
mix.copyDirectory(`resources/media/admin`, `public/assets/media/admin`);
mix.copyDirectory(`resources/media/common`, `public/assets/media/common`);

let plugins = [
    new ReplaceInFileWebpackPlugin([
        {
            // rewrite font paths
            dir: path.resolve(`public/assets/plugins/global`),
            test: /\.css$/,
            rules: [
                {
                    // fontawesome
                    search: /url\((\.\.\/)?webfonts\/(fa-.*?)"?\)/g,
                    replace: "url(./fonts/@fortawesome/$2)",
                },
                {
                    // lineawesome fonts
                    search: /url\(("?\.\.\/)?fonts\/(la-.*?)"?\)/g,
                    replace: "url(./fonts/line-awesome/$2)",
                },
                {
                    // bootstrap-icons
                    search: /url\(.*?(bootstrap-icons\..*?)"?\)/g,
                    replace: "url(./fonts/bootstrap-icons/$1)",
                },
                {
                    // fonticon
                    search: /url\(.*?(fonticon\..*?)"?\)/g,
                    replace: "url(./fonts/fonticon/$1)",
                },
                {
                    // keenicons
                    search: /url\(.*?((keenicons-.*?)\..*?)'?\)/g,
                    replace: "url(./fonts/$2/$1)",
                },
            ],
        },
    ]),
];

mix.webpackConfig({
    plugins: plugins,
    ignoreWarnings: [
        {
            module: /esri-leaflet/,
            message: /version/,
        },
    ],
});

// Webpack.mix does not copy fonts, manually copy
(glob.sync(`${dir}/plugins/**/*.+(woff|woff2|eot|ttf|svg)`) || []).forEach(
    (file) => {
        mix.copy(
            file,
            `public/assets/plugins/global/fonts/${
                path.parse(file).name
            }/${path.basename(file)}`
        );
    }
);
(
    glob.sync(
        "node_modules/+(@fortawesome|socicon|line-awesome|bootstrap-icons)/**/*.+(woff|woff2|eot|ttf)"
    ) || []
).forEach((file) => {
    var folder = file.match(/node_modules\/(.*?)\//)[1];
    mix.copy(
        file,
        `public/assets/plugins/global/fonts/${folder}/${path.basename(file)}`
    );
});
(
    glob.sync("node_modules/jstree/dist/themes/default/*.+(png|gif)") || []
).forEach((file) => {
    mix.copy(
        file,
        `public/assets/plugins/custom/jstree/${path.basename(file)}`
    );
});

// Widgets
mix.scripts(
    glob.sync(`${dir}/js/widgets/**/*.js`) || [],
    `public/assets/js/widgets.bundle.js`
);
