<?php

namespace Salahhusa9\GeetestCaptcha\Commands;

use Illuminate\Console\Command;

class GeetestCaptchaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geetest:install
                            {--config : Publish the configuration file}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install GeeTest Captcha package';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('GeeTest Captcha Package Installation');
        $this->line('');

        if ($this->option('config')) {
            $this->publishConfig();
        } else {
            $this->showUsageInfo();
        }

        return 0;
    }

    /**
     * Publish the configuration file
     */
    protected function publishConfig()
    {
        $force = $this->option('force');

        $this->call('vendor:publish', [
            '--tag' => 'geetest-captcha-config',
            '--force' => $force,
        ]);

        $this->info('Configuration file published successfully!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Add your GeeTest credentials to your .env file:');
        $this->line('   GEETEST_ID=your_captcha_id');
        $this->line('   GEETEST_KEY=your_captcha_key');
        $this->line('');
        $this->line('2. Add @geetestCaptchaAssets() to your layout head section');
        $this->line('3. Use @geetestCaptchaInit(\'element-id\') where you want the captcha');
    }

    /**
     * Show usage information
     */
    protected function showUsageInfo()
    {
        $this->line('Available options:');
        $this->line('  --config    Publish the configuration file');
        $this->line('  --force     Overwrite existing files');
        $this->line('');
        $this->line('Example usage:');
        $this->line('  php artisan geetest:install --config');
        $this->line('');
        $this->line('Configuration:');
        $this->line('Add these variables to your .env file:');
        $this->line('  GEETEST_ID=your_captcha_id');
        $this->line('  GEETEST_KEY=your_captcha_key');
        $this->line('');
        $this->line('Usage in Blade templates:');
        $this->line('  @geetestCaptchaAssets() - Add to head section');
        $this->line('  @geetestCaptchaInit(\'captcha-element\') - Initialize captcha');
    }
}
