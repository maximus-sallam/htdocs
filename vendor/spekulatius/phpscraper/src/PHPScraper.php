<?php

namespace Spekulatius\PHPScraper;

/**
 * This class manages the Clients and connections.
 *
 * Most calls are passed through to the Core class.
 */

use Goutte\Client as GoutteClient;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;

class PHPScraper
{
    /**
     * Holds the config for the clients.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Holds the Core class. It handles the actual scraping.
     *
     * @var \Spekulatius\PHPScraper\Core;
     */
    protected $core = null;

    public function __construct(?array $config = [])
    {
        // Prepare the core. It delegates all further processing.
        $this->core = new Core();

        // And set the config.
        $this->setConfig($config);
    }

    /**
     * Sets the config, generates the required Clients and updates the core with the new clients.
     *
     * @var ?array $config = []
     */
    public function setConfig(?array $config = []): self
    {
        // Define the default values
        $defaults = [
            // We assume that we want to follow any redirects, in reason.
            'follow_redirects' => true,
            'follow_meta_refresh' => true,
            'max_redirects' => 5,

            /**
             * Agent can be overwritten using:
             *
             * ```php
             * $web->setConfig(['agent' => 'My Agent']);
             * ```
             */
            'agent' => 'Mozilla/5.0 (compatible; PHP Scraper/1.x; +https://phpscraper.de)',

            /**
             * Agent can be overwritten using:
             *
             * ```php
             * $web->setConfig(['proxy' => 'http://user:password@127.0.0.1:3128']);
             * ```
             */
            'proxy' => null,

            /**
             * Timeout in seconds.
             *
             * ```php
             * $web->setConfig(['timeout' => 15]);
             * ```
             */
            'timeout' => 10,

            /**
             * Disable SSL (not recommended unless really needed).
             *
             * @var bool
             */
            'disable_ssl' => false,
        ];

        // Add the defaults in
        $this->config = array_merge($defaults, $config);

        // Symfony HttpClient
        $httpClient = SymfonyHttpClient::create([
            'proxy' => $this->config['proxy'],
            'timeout' => $this->config['timeout'],
            'verify_host' => $this->config['disable_ssl'],
            'verify_peer' => $this->config['disable_ssl'],
        ]);

        // Goutte Client and set some config needed for it.
        $client = new GoutteClient($httpClient);
        $client->followRedirects($this->config['follow_redirects']);
        $client->followMetaRefresh($this->config['follow_meta_refresh']);
        $client->setMaxRedirects($this->config['max_redirects']);
        $client->setServerParameter('HTTP_USER_AGENT', $this->config['agent']);

        // Set the client on the core.
        $this->core->setClient($client);
        $this->core->setHttpClient($httpClient);

        return $this;
    }

    /**
     * Catch alls to properties and process them accordingly.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        // We are assuming that all calls for properties actually method calls...
        return $this->call($name);
    }

    /**
     * Catches the method calls and tries to satisfy them.
     *
     * @param string $name
     * @param array $arguments = null
     * @return mixed
     */
    public function __call(string $name, array $arguments = null)
    {
        if ($name == 'call') {
            $name = $arguments[0];
            $result = $this->core->$name();
        } else {
            $result = $this->core->$name(...$arguments);
        }

        // Did we get a Core class element? Keep this.
        if ($result instanceof Core) {
            $this->core;

            return $this;
        }

        // Otherwise: just return whatever the core returned.
        return $result;
    }
}