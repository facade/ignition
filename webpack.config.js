module.exports = {
    entry: {
        ignition: './resources/js/app.js',
    },

    output: {
        path: `${__dirname}/resources/compiled`,
        publicPath: '/',
        filename: '[name].js',
    },

    stats: 'minimal',

    performance: {
        hints: false,
    },
};
