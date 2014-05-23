<?php
namespace Barany\Core;

abstract class AppConfig {
    public abstract function getPlaidApiEndpoint();
    public abstract function getPlaidApiClientId();
    public abstract function getPlaidApiSecret();
    public abstract function getDatabaseCredentials();

    public function getPlaidApiCredentials() {
        return array(
            'client_id' => $this->getPlaidApiClientId(),
            'secret' => $this->getPlaidApiSecret(),
        );
    }
} 