<?php

namespace Salahhusa9\GeetestCaptcha;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Salahhusa9\GeetestCaptcha\Commands\GeetestCaptchaCommand;

class GeetestCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/geetest-captcha.php', 'geetest-captcha'
        );

        $this->app->singleton(GeetestCaptcha::class, function ($app) {
            return new GeetestCaptcha();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/geetest-captcha.php' => config_path('geetest-captcha.php'),
        ], 'geetest-captcha-config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GeetestCaptchaCommand::class,
            ]);
        }

        // Register Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Register Blade directives for GeeTest Captcha
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        // Blade directive for including GeeTest assets
        Blade::directive('geetestCaptchaAssets', function () {
            $jsUrl = config('geetest-captcha.js_url', 'https://static.geetest.com/v4/gt4.js');
            return "<?php echo '<script src=\"{$jsUrl}\"></script>'; ?>";
        });

        // Blade directive for initializing GeeTest captcha
        Blade::directive('geetestCaptchaInit', function ($elementId) {
            $elementId = trim($elementId, '\'"');

            $html = <<<HTML
<?php echo '
<script>
    var captchaId = "<?php echo config("geetest-captcha.captcha_id", env("GEETEST_ID")); ?>";

    if (typeof initGeetest4 !== "undefined" && captchaId) {
        initGeetest4({
            captchaId: captchaId,
        }, function (geetest) {
            window.geetest = geetest;
            geetest
                .appendTo("#{$elementId}")
                .onSuccess(function (e) {
                    var result = JSON.stringify(geetest.getValidate());
                    result = result.replace(/"/g, "&quot;");

                    // Remove existing hidden input if any
                    var existingInput = document.querySelector("input[name=\"geetest_captcha\"]");
                    if (existingInput) {
                        existingInput.remove();
                    }

                    // Add new hidden input
                    var hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "geetest_captcha";
                    hiddenInput.value = result;

                    var captchaElement = document.getElementById("{$elementId}");
                    if (captchaElement && captchaElement.parentNode) {
                        captchaElement.parentNode.appendChild(hiddenInput);
                    }
                });
        });
    } else {
        console.error("GeeTest: initGeetest4 function not found or captcha ID not configured");
    }
</script>
'; ?>
HTML;

            return $html;
        });
    }
}
