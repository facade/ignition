const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
    entry: {
        ignition: './resources/js/app.js',
    },

    output: {
        path: `${__dirname}/resources/compiled`,
        publicPath: '/',
        filename: '[name].js',
    },

    module: {
        rules: [
            {
                test: /\.(js|tsx?)$/,
                use: 'babel-loader',
                exclude: /node_modules/,
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
            {
                test: /\.css$/,
                use: [
                    'style-loader',
                    { loader: 'css-loader', options: { url: false } },
                    'postcss-loader',
                ],
            },
        ],
    },

    plugins: [new VueLoaderPlugin()],

    resolve: {
        extensions: ['.css', '.js', '.ts', '.vue'],
        alias: {
            vue$: 'vue/dist/vue.esm.js',
        },
    },

    stats: 'minimal',

    performance: {
        hints: false,
    },
};
