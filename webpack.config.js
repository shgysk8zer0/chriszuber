var path = require('path');
var webpack = require('webpack');

module.exports = {
    entry: './scripts/imp.es6',
    output: {
        path: __dirname,
        filename: './bundle.js'
    },
    module: {
        loaders: [
            {
                loader: 'babel-loader',
                test: /\.(es6)|(js)$/,
                exclude: /node_modules/,
                query: {
                  presets: 'es2015',
                },
            }
        ]
    },
    plugins: [
        // Avoid publishing files when compilation fails
        new webpack.NoErrorsPlugin()
    ],
    stats: {
        // Nice colored output
        colors: true
    },
    // Create Sourcemaps for the bundle
    devtool: 'source-map',
};
