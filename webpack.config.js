import path from "path";
import { dirname } from 'path';
import webpack from "webpack";
import { fileURLToPath } from 'url';
import MiniCssExtractPlugin from "mini-css-extract-plugin";
import { VueLoaderPlugin } from "vue-loader";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);


/* BASE DEFINITIONS
******************************************************/
const mode = process.env.NODE_ENV;
const dev = (mode === "development");

/**
 * @type {import("webpack").webpack.Configuration}
 */
const config = {
    mode,
    devServer: {
        // watchContentBase: true,
        // webSocketServer: false,
        hot: false,
        host: 'localhost',
        // hotOnly: true,
        // client: {
        //     overlay: false
        // },
        devMiddleware: {
            publicPath: '/assets/',
        },
        // hotMiddleware: {
        //     overlay: true
        // },
        headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, PATCH, OPTIONS",
            "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization"
        }
    },
    resolve: {
        alias: {},
        extensions: [],
    },
    entry: {},
    output: {},
    module: { rules: [] },
    plugins: [],
};

const here = src => path.resolve(__dirname, src);


/* TARGET
******************************************************/
config.target = "web";


/* MODULE RESOLUTION
******************************************************/
const { alias, extensions } = config.resolve;

alias["@vuee"] = here("resources/vue/");
alias["@components"] = here("resources/vue/Components");
alias["@scss"] = here("dev/scss/");

alias.$vue = "vue/dist/vue.esm";


extensions.push(
    ".js",
    ".vue",
    ".scss",
    ".css"
);


/* ENTRIES
******************************************************/
config.entry.app = "@vuee/app.js";


/* OUTPUTS
******************************************************/
const { output } = config;
output.path = here("public/assets/");
output.filename = `[name].bundle.js`; // ${dev ? "" : "[chunkhash:8]."}
output.publicPath = "/assets/";


/* MODULE RESOLUTION
******************************************************/
const { rules } = config.module;

rules.push({
    test: /\.s[ac]ss$/i,
    use: [
        // 'vue-style-loader',
        // 'css-loader',
        // 'sass-loader'

        {
            loader: MiniCssExtractPlugin.loader,
            options: {},
        },
        {
            loader: "css-loader",
            options: {
                sourceMap: true,
                url: false,
            },
        },
        // {
        //     loader: "vue-style-loader",
        //     options: {},
        // },
        // // "postcss-loader",
        {
            loader: "sass-loader",
            options: { sourceMap: true },
        },
        // {
        //     loader: "vue-style-loader",
        //     options: {},
        // },
    ],
});

rules.push({
    test: /\.css$/i,
    use: [
        // 'vue-style-loader',
        // 'css-loader',
        // 'sass-loader'

        {
            loader: MiniCssExtractPlugin.loader,
            options: {},
        },
        {
            loader: "css-loader",
            options: {
                sourceMap: true,
                url: false,
            },
        },
        // {
        //     loader: "vue-style-loader",
        //     options: {},
        // },
        // // "postcss-loader",
        {
            loader: "sass-loader",
            options: { sourceMap: true },
        },
        // {
        //     loader: "vue-style-loader",
        //     options: {},
        // },
    ],
});

rules.push({
    test: /\.vue$/i,
    loader: "vue-loader",
});


/* PLUGINS RESOLUTION
******************************************************/
const { plugins } = config;

plugins.push(new VueLoaderPlugin());

plugins.push(new MiniCssExtractPlugin({
    filename: `css/[name].css`,
    chunkFilename: "css/[id].css",
}));


/* EXPORT
******************************************************/
export default config;