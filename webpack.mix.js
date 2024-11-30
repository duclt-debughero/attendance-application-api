const mix = require('laravel-mix');
const fs = require('fs');

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

const cssDirs = [
];

const jsDirs = [
];

const getFiles = function (dir) {
    return fs.readdirSync(dir).filter(file => {
        return fs.statSync(`${dir}/${file}`).isFile();
    });
};

cssDirs.forEach(function (path) {
    getFiles(path.in).forEach(function (filepath) {
        mix.css(path.in + '/' + filepath, path.out);
    });
});

jsDirs.forEach(function (path) {
    getFiles(path.in).forEach(function (filepath) {
        mix.js(path.in + '/' + filepath, path.out);
    });
});

mix.version();
mix.disableNotifications();
