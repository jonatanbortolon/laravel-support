const mix = require('laravel-mix');
const path = require('path')
const { exec } = require('child_process');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
 mix.extend('ziggy', new class {
    register(config = {}) {
        this.watch = config.watch ?? ['routes/**/*.php'];
        this.path = config.path ?? '';
        // eslint-disable-next-line no-undef
        this.enabled = config.enabled ?? !Mix.inProduction();
    }

    boot() {
        if (!this.enabled) return;

        const command = () => exec(
            `php artisan ziggy:generate ${this.path}`,
            (error, stdout) => console.log(stdout)
        );

        command();

        // eslint-disable-next-line no-undef
        if (Mix.isWatching() && this.watch) {
            ((require('chokidar')).watch(this.watch))
                .on('change', (path) => {
                    console.log(`${path} changed...`);
                    command();
                });
        }
    }
}());

mix.ts('resources/js/app.ts', 'public/js')
    .vue()
    .postCss("resources/css/app.css", "public/css", [
        require("tailwindcss"),
    ])
    .ziggy();

mix.webpackConfig({
    resolve:{
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
        }
    },
    module: {
        rules: [
          {
            test: /\.(postcss)$/,
            use: [
              'vue-style-loader',
              { loader: 'css-loader', options: { importLoaders: 1 } },
              'postcss-loader'
            ]
          }
        ]
    }
})
