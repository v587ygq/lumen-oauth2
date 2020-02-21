<?php
namespace V587ygq\OAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use V587ygq\OAuth\Models\Client;

class ClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:client
            {--authorization_code : Create an authorization code grant client}
            {--client_credentials : Create a client credentials grant client}
            {--implicit : Create an implicit credentials grant client}
            {--password : Create a password grant client}
            {--name= : The name of the client}
            {--redirect_uri= : The URI to redirect to after authorization }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a OAuth client';

    /**
     * Execute the console command.
     *
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @return void
     */
    public function handle()
    {
        if ($this->option('authorization_code')) {
            $this->createAuthorizationCodeClient();
        } elseif ($this->option('client_credentials')) {
            $this->createClientCredentialsClient();
        } elseif ($this->option('implicit')) {
            $this->createImplicitClient();
        } elseif ($this->option('password')) {
            $this->createPasswordClient();
        } else {
            $this->error('need a grant type');
        }
    }

    /**
     * Create an authorization code grant client.
     *
     * @return void
     */
    protected function createAuthorizationCodeClient()
    {
        //
    }

    /**
     * Create a client credentials grant client.
     *
     * @return void
     */
    protected function createClientCredentialsClient()
    {
        $name = $this->option('name') ?: $this->ask('What should we name the client?');

        $secret = Str::random(40);
        $client = Client::create([
            'name' => $name,
            'secret' => $secret,
            'grant_type' => 'client_credentials',
            'revoked' => false,
        ]);

        $this->info('Client credentials grant client created successfully.');
        $this->line('<comment>Client ID:</comment> '.$client->id);
        $this->line('<comment>Client secret:</comment> '.$secret);
    }

    /**
     * Create an implicit grant client.
     *
     * @return void
     */
    protected function createImplicitClient()
    {
        $name = $this->option('name') ?: $this->ask('What should we name the client?');
        $redirect = $this->option('redirect_uri') ?: $this->ask('Where should we redirect the request after authorization?');
        $client = Client::create([
            'name' => $name,
            'redirect' => $redirect,
            'grant_type' => 'implicit',
            'revoked' => false,
        ]);

        $this->info('Implicit grant client created successfully.');
        $this->line('<comment>Client ID:</comment> '.$client->id);
    }

    /**
     * Create a password grant client.
     *
     * @return void
     */
    protected function createPasswordClient()
    {
        $name = $this->option('name') ?: $this->ask('What should we name the client?');
        $secret = Str::random(40);
        $client = Client::create([
            'name' => $name,
            'secret' => $secret,
            'grant_type' => 'password',
            'revoked' => false,
        ]);

        $this->info('Password grant client created successfully.');
        $this->line('<comment>Client ID:</comment> '.$client->id);
        $this->line('<comment>Client secret:</comment> '.$secret);
    }
}
