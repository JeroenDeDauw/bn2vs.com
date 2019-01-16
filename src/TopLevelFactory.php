<?php

declare( strict_types = 1 );

namespace App;

use App\DataAccess\Blog\BlogRepository;
use App\DataAccess\Blog\WordpressApiBlogRepository;
use FileFetcher\FileFetcher;
use FileFetcher\PsrCacheFileFetcher;
use FileFetcher\SimpleFileFetcher;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Framework independent object graph construction
 */
class TopLevelFactory {

	private $container = [];

	public function __construct( Stopwatch $stopwatch, CacheInterface $cache ) {
		$this->container[Stopwatch::class] = $stopwatch;
		$this->container[CacheInterface::class] = $cache;
	}

	/**
	 * @return mixed
	 */
	private function getSharedService( string $serviceName, callable $constructionFunction ) {
		if ( !array_key_exists( $serviceName, $this->container ) ) {
			$this->container[$serviceName] = $constructionFunction();
		}

		return $this->container[$serviceName];
	}

	public function newNewsRepository(): BlogRepository {
		return new WordpressApiBlogRepository( $this->getStopwatchFileFetcher() );
	}

	private function getStopwatchFileFetcher(): FileFetcher {
		return $this->getSharedService(
			FileFetcher::class,
			function() {
				return new StopwatchFileFetcher(
					$this->newCachingFileFetcher(),
					$this->getStopwatch()
				);
			}
		);
	}

	private function newCachingFileFetcher(): FileFetcher {
		return new PsrCacheFileFetcher(
			new SimpleFileFetcher(),
			$this->getCache()
		);
	}

	public function setFileFetcher( FileFetcher $fileFetcher ) {
		$this->container[FileFetcher::class] = $fileFetcher;
	}

	private function getStopwatch(): Stopwatch {
		return $this->container[Stopwatch::class];
	}

	private function getCache(): CacheInterface {
		return $this->container[CacheInterface::class];
	}

}
