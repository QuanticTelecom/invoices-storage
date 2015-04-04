<?php namespace QuanticTelecom\InvoicesStorage;

use Illuminate\Support\ServiceProvider;

/**
 * Class InvoicesStorageServiceProvider
 * @package QuanticTelecom\InvoicesStorage
 */
class InvoicesStorageServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('quantic-telecom/invoices-storage');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
