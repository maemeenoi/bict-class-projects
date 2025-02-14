<?php
namespace Agora\Core\Http;
class URI
{
    private $site;
    private $path;
    private $requestMethod;
    private $baseDir;
    private $queryParams;
    private $routeParams = [];

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->baseDir = '/Agora_V.3';

        // Get the site URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $this->site = $protocol . "://" . $_SERVER['HTTP_HOST'] . $this->baseDir;

        // Get the request path and remove base directory and public
        $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace($this->baseDir, '', $fullPath);
        $path = str_replace('/public', '', $path);
        $this->path = trim($path, '/');

        // Parse query parameters
        $this->queryParams = [];
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $this->queryParams);
        }
    }

    public function setParams(array $params)
    {
        $this->routeParams = $params;
    }

    public function getParam($index)
    {
        return $this->routeParams[$index] ?? null;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSite(): string
    {
        return $this->site;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getFilter($key): ?string
    {
        return isset($this->queryParams[$key]) ? (string) $this->queryParams[$key] : null;
    }

    public function getAllFilters(): array
    {
        return $this->queryParams;
    }
}