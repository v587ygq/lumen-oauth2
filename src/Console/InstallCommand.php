<?php
namespace V587ygq\OAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use phpseclib\Crypt\RSA;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:install
                            {--force : Overwrite keys they already exist}
                            {--length=4096 : The length of the private key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands necessary to prepare public and private keys for oauth';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(RSA $rsa)
    {
        [$publicKey, $privateKey] = [base_path().'/oauth-public.key', base_path().'/oauth-private.key'];

        if ((file_exists($publicKey) || file_exists($privateKey)) && ! $this->option('force')) {
            $this->error('Encryption keys already exist. Use the --force option to overwrite them.');
        } else {
            $keys = $rsa->createKey($this->input ? (int) $this->option('length') : 4096);

            file_put_contents($publicKey, Arr::get($keys, 'publickey'));
            file_put_contents($privateKey, Arr::get($keys, 'privatekey'));

            $this->info('Encryption keys generated successfully.');
        }
    }
}
