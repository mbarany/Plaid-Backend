<?php
namespace Barany\Core;

abstract class AppConfig {
    public abstract function getPlaidApiEndpoint();
    public abstract function getPlaidApiCredentials();
    public abstract function getDatabaseCredentials();
    public abstract function getGoogleCredentials();
} 