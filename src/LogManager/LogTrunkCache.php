<?php

namespace EzPlatformLogsUi\Bundle\LogManager;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class LogTrunkCache
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 *
 * @package EzPlatformLogsUi\Bundle\LogManager
 */
class LogTrunkCache {

    /** @var CacheInterface */
    private $cacheSystem;

    /** @var string */
    private $logPath;

    /** @var string */
    private $cacheNamespace;

    /**
     * LogTrunkCache constructor.
     *
     * @param string $logPath
     * @param string $cacheDirectory
     * @param string $cacheNamespace
     */
    public function __construct(string $logPath, string $cacheDirectory, string $cacheNamespace = '') {
        $this->logPath = $logPath;
        $this->cacheNamespace = $cacheNamespace;

        $this->cacheSystem = new FilesystemCache($cacheNamespace, 0, $cacheDirectory);
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    public function getCacheKey(string $subject = 'logs'): string {
        return $this->cacheNamespace . '.' . $subject . '.' . md5($this->logPath);
    }

    /**
     * @param int $chunkId
     *
     * @return string
     */
    public function getChunkIdentifier(int $chunkId): string {
        return $this->getCacheKey() . '.chunk.' . $chunkId;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param int $chunkId
     *
     * @return bool
     */
    public function hasChunk(int $chunkId): bool {
        try {
            return $this->cacheSystem->has($this->getChunkIdentifier($chunkId));
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Fetches a value from the cache.
     *
     * @param int $chunkId
     * @param null $default
     *
     * @return mixed
     */
    public function getChunk(int $chunkId, $default = null) {
        try {
            return $this->cacheSystem->get($this->getChunkIdentifier($chunkId), $default);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param int $currentChunkId
     * @param int $total
     * @param null $default
     *
     * @return bool|mixed|null
     */
    public function getLastChunk(int $currentChunkId, int $total, $default = null) {
        try {
            $lastChunkCacheKey = $this->getChunkIdentifier(ceil($total / 20) - ($currentChunkId - 1));

            return $this->cacheSystem->get($lastChunkCacheKey, $default);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param int $chunkId
     * @param mixed $value
     * @param null|int|\DateInterval $ttl
     *
     * @return bool
     */
    public function setChunk(int $chunkId, $value, $ttl = 7200): bool {
        try {
            return $this->cacheSystem->set($this->getChunkIdentifier($chunkId), $value, $ttl);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param int $total
     *
     * @return bool
     */
    public function clearChunks(int $total): bool {
        $numberOfChunks = ceil($total / 20);
        $chunkCacheKeys = [];

        for ($chunkId = 1; $chunkId <= $numberOfChunks; $chunkId++) {
            $chunkCacheKeys[] = $this->getChunkIdentifier($chunkId);
        }

        try {
            return $this->cacheSystem->deleteMultiple($chunkCacheKeys);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @return CacheInterface
     */
    public function getCacheSystem(): CacheInterface {
        return $this->cacheSystem;
    }

}
