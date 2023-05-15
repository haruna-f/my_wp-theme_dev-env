/** @type { import ('webpack').Configuration } **/

const path = require('node:path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerWebpackPlugin = require('css-minimizer-webpack-plugin');
const WebpackRemoveEmotyScripts = require('webpack-remove-empty-scripts');
const CopyWebpackPlugin = require('copy-webpack-plugin');

const src = './src';
const dist = './dist';

module.exports = {
    entry: {
        main: `${src}/js/main.js`,
        style: `${src}/css/scss/app.scss`,
        custom_editor_style: `${src}/css/scss/custom_editor_style.scss`,
    },
    output: {
        filename: 'js/[name].bandle.js',
        path: path.resolve(__dirname, 'dist'),
        publicPath: '/',
        clean: true,
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                '@babel/preset-env',
                            ],
                        },
                    },
                ],
            },
            {
                test: /\.(scss|css)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: true,
                        },
                    },
                    'sass-loader'
                ],
            },
            {
                test: /\.png$/,
				generator: {
					filename: './images/[name][ext]',
				},
				type: 'asset/resource',
            },
        ],
    },
    optimization: {
        minimizer: [
            `...`,
            new CssMinimizerWebpackPlugin({
                parallel: true,
            }),
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: (pathData) => {
                return pathData.chunk.name === 'style' ? '[name].css' : 'css/[name].css';
            },
        }),
        new WebpackRemoveEmotyScripts(),
        new CopyWebpackPlugin({
            patterns: [
                {
                    from: `${__dirname}/src/`,
                    to: `${__dirname}/dist/`,
                    globOptions: {
                        ignore: [
                            '**/.DS_Store',
                            '**/_*.*',
                            '**/*.scss',
                            '**/*.js',
                            '**/ress.css',
                        ]
                    }
                }
            ],
        }),
    ],
    // devtool: 'source-map',
    devServer: {
        hot: true,
        static: {
            directory: dist,
        },
        devMiddleware: {
      	    writeToDisk: true,
        }
    },
    stats: {
        children: true,
        errorDetails: true,
    },
}