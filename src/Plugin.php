<?php

namespace Required\WpConfig;

use Composer\Config;
use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface {

	/**
	 * Composer.
	 *
	 * @var \Composer\Composer
	 */
	protected $composer;

	/**
	 * Input/Output helper interface.
	 *
	 * @var \Composer\IO\IOInterface
	 */
	protected $io;

	/**
	 * Vendor directory.
	 *
	 * @var string
	 */
	protected $vendorDir = 'vendor';

	/**
	 * Name of of the WordPress core package.
	 */
	protected const WORDPRESS_CORE_PACKAGE_NAME = 'johnpbloch/wordpress-core';

	/**
	 * Applies plugin modifications to Composer.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$this->composer = $composer;
		$this->io       = $io;

		$config = $this->composer->getConfig();

		$this->vendorDir = $config->get( 'vendor-dir', Config::RELATIVE_PATHS ) ?? $this->vendorDir;
	}

	/**
	 * Subscribes to package update/install events
	 *
	 * @return array Subscribed events.
	 */
	public static function getSubscribedEvents() {
		return [
			PackageEvents::POST_PACKAGE_INSTALL => [
				[ 'copyWpConfig' ],
			],
			PackageEvents::POST_PACKAGE_UPDATE => [
				[ 'copyWpConfig' ],
			],
		];
	}

	/**
	 * Copies wp-config.php after WordPress is being installed or updated.
	 *
	 * @param \Composer\Installer\PackageEvent $event The current event.
	 */
	public function copyWpConfig( PackageEvent $event ) {
		$operation = $event->getOperation();

		if ( $operation instanceof InstallOperation ) {
			$package = $operation->getPackage();
		} elseif ( $operation instanceof UpdateOperation ) {
			$package = $operation->getTargetPackage();
		} else {
			throw new \Exception( 'Unknown operation: ' . get_class( $operation ) );
		}

		if ( self::WORDPRESS_CORE_PACKAGE_NAME !== $package->getName() ) {
			return;
		}

		$installationManager = $event->getComposer()->getInstallationManager();
		$wordpressInstallDir = $installationManager->getInstallPath( $package );

		if ( ! is_dir( $wordpressInstallDir ) ) {
			$this->io->write( '<warning>The installation path of WordPress seems to be broken. wp-config.php not copied.</warning>' );
			return;
		}

		// Get the relative path of the vendor directory to the WordPress installation.
		$vendorDirRelative = self::getRelativePath( '/' . $wordpressInstallDir, '/' . $this->vendorDir );

		$source = dirname( __DIR__ ) . '/res/wp-config.tpl.php';
		$dest   = dirname( $wordpressInstallDir ) . '/wp-config.php';

		// Replace template variables.
		$wpConfig = file_get_contents( $source );
		$wpConfig = str_replace(
			[
				'{{{VENDOR_DIR}}}'
			],
			[
				$vendorDirRelative
			],
			$wpConfig
		);

		$copied = file_put_contents( $dest, $wpConfig );

		if ( false !== $copied ) {
			$this->io->write( '    wp-config.php has been copied to ' . $dest );
		} else {
			$this->io->write( '<error>wp-config.php could not be copied to ' . $dest . '</warning>' );
		}
	}

	/**
	 * Returns the target path as relative reference from the base path.
	 *
	 * Part of the Symfony package licensed under the MIT License.
	 * @link https://github.com/symfony/Routing/blob/1a19ff2/Generator/UrlGenerator.php#L290-L339
	 *
	 * @param string $basePath   The base path.
	 * @param string $targetPath The target path.
	 * @return string The relative target path.
	 */
	public static function getRelativePath( $basePath, $targetPath ) {
		if ( $basePath === $targetPath ) {
			return '';
		}

		$sourceDirs = explode( '/', isset( $basePath[0] ) && '/' === $basePath[0] ? substr( $basePath, 1 ) : $basePath );
		$targetDirs = explode( '/', isset( $targetPath[0] ) && '/' === $targetPath[0] ? substr( $targetPath, 1 ) : $targetPath );
		array_pop( $sourceDirs );

		$targetFile = array_pop($targetDirs);
		foreach ( $sourceDirs as $i => $dir ) {
			if ( isset( $targetDirs[ $i ] ) && $dir === $targetDirs[ $i ] ) {
				unset( $sourceDirs[ $i ], $targetDirs[ $i ] );
			} else {
				break;
			}
		}

		$targetDirs[] = $targetFile;

		$path = str_repeat( '../', count( $sourceDirs ) ) . implode( '/', $targetDirs );

		// A reference to the same base directory or an empty subdirectory must be prefixed with "./".
		// This also applies to a segment with a colon character (e.g., "file:colon") that cannot be used
		// as the first segment of a relative-path reference, as it would be mistaken for a scheme name
		// (see http://tools.ietf.org/html/rfc3986#section-4.2).
		return '' === $path || '/' === $path[0]
			|| false !== ( $colonPos = strpos( $path, ':' ) ) && ( $colonPos < ( $slashPos = strpos( $path, '/' ) ) || false === $slashPos )
			? "./$path" : $path;
	}
}
