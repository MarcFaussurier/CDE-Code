const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
process.env.NODE_ENV = 'production';
const devMode = process.env.NODE_ENV !== 'production';
module.exports = {
    mode: "production",
    devtool: devMode ? 'source-map' : '',
    entry: './src/client/main.tsx',
    plugins: [
        new MiniCssExtractPlugin({
            // Options similar to the same options in webpackOptions.output
            // both options are optional
            filename: devMode ? '[name].css' : '[name].[hash].css',
            chunkFilename: devMode ? '[id].css' : '[id].[hash].css',
        })
    ],
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                use: 'ts-loader',
                exclude: /node_modules/
            },
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    devMode ? 'style-loader' : MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {
                            plugins: () => [require('autoprefixer')]
                        }
                    },
                    'sass-loader',
                ],
            }
        ]
    },
    resolve: {
        extensions: [ '.tsx', '.ts', '.js', 'tsx']
    },
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, '../../www/assets/build'),
    },
};