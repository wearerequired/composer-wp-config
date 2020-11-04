<?php
/**
 * Plugin class
 */

namespace Required\WpConfig;

use Composer\Composer;
use Composer\Config;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

/**
 * Class used to hook into Composer.
 */
class Plugin implements PluginInterface, EventSubscriberInterface, Capable {

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
	 * Name of the WordPress core package.
	 */
	public const WORDPRESS_CORE_PACKAGE_NAME = 'johnpbloch/wordpress-core';

	/**
	 * Package name of this plugin.
	 */
	public const PLUGIN_PACKAGE_NAME = 'wearerequired/composer-wp-config';

	/**
	 * Applies plugin modifications to Composer.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$this->composer = $composer;
		$this->io       = $io;
	}

	/**
	 * Removes any hooks from Composer.
	 *
	 * This will be called when a plugin is deactivated before being
	 * uninstalled, but also before it gets upgraded to a new version
	 * so the old one can be deactivated and the new one activated.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function deactivate( Composer $composer, IOInterface $io ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	}

	/**
	 * Prepares the plugin to be uninstalled.
	 *
	 * This will be called after deactivate.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function uninstall( Composer $composer, IOInterface $io ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	}

	/**
	 * Subscribes to package update/install events
	 *
	 * @return array Subscribed events.
	 */
	public static function getSubscribedEvents() {
		return [
			PackageEvents::POST_PACKAGE_INSTALL   => [
				[ 'copyWpConfigOnPackageInstall' ],
			],
			PackageEvents::POST_PACKAGE_UPDATE    => [
				[ 'copyWpConfigOnPackageInstall' ],
			],
			PackageEvents::POST_PACKAGE_UNINSTALL => [
				[ 'deleteWpConfigOnPackageUninstall' ],
			],
		];
	}

	public function getCapabilities() {
		return [
			'Composer\Plugin\Capability\CommandProvider' => 'Required\WpConfig\CommandProvider',
		];
	}

	/**
	 * Deletes wp-config.php after WordPress is being uninstalled.
	 *
	 * @param \Composer\Installer\PackageEvent $event The current event.
	 */
	public function deleteWpConfigOnPackageUninstall( PackageEvent $event ) {
		/** @var \Composer\DependencyResolver\Operation\UninstallOperation $operation */
		$operation = $event->getOperation();
		$package   = $operation->getPackage();

		if ( self::WORDPRESS_CORE_PACKAGE_NAME !== $package->getName() ) {
			return;
		}

		$installationManager = $event->getComposer()->getInstallationManager();
		$wordpressInstallDir = $installationManager->getInstallPath( $package );
		$wpConfigFile        = dirname( $wordpressInstallDir ) . '/wp-config.php';

		if ( is_file( $wpConfigFile ) ) {
			unlink( $wpConfigFile );
			$this->io->writeError( '    wp-config.php has been removed.' );
		}
	}

	/**
	 * Copies wp-config.php after WordPress is being installed or updated.
	 *
	 * @param \Composer\Installer\PackageEvent $event The current event.
	 */
	public function copyWpConfigOnPackageInstall( PackageEvent $event ) {
		$operation = $event->getOperation();

		if ( $operation instanceof InstallOperation ) {
			$package = $operation->getPackage();
		} elseif ( $operation instanceof UpdateOperation ) {
			$package = $operation->getTargetPackage();
		} else {
			throw new \Exception( 'Unknown operation: ' . \get_class( $operation ) );
		}

		$wordpressPackage = null;
		if ( self::WORDPRESS_CORE_PACKAGE_NAME === $package->getName() ) {
			$wordpressPackage = $package;
		} elseif ( self::PLUGIN_PACKAGE_NAME === $package->getName() ) {
			$wordpressPackage = $event->getComposer()->getRepositoryManager()->getLocalRepository()->findPackage( self::WORDPRESS_CORE_PACKAGE_NAME, '*' );
		}

		if ( ! $wordpressPackage ) {
			return;
		}

		self::copyWpConfig( $this->composer, $wordpressPackage, $this->io );
	}

	/**
	 * Copies wp-config.php to the WordPress installation directory.
	 *
	 * @param \Composer\Composer                $composer         Composer.
	 * @param Composer\Package\PackageInterface $wordpressPackage WordPress package..
	 * @param \Composer\IO\IOInterface          $io               Input/Output helper interface.
	 */
	public static function copyWpConfig( Composer $composer, PackageInterface $wordpressPackage, IOInterface $io ) {
		$installationManager = $composer->getInstallationManager();
		$wordpressInstallDir = $installationManager->getInstallPath( $wordpressPackage );

		if ( ! is_dir( $wordpressInstallDir ) ) {
			$io->writeError( '<warning>The installation path of WordPress seems to be broken. wp-config.php not copied.</warning>' );
			return false;
		}

		// Get the relative path of the vendor directory to the WordPress installation.
		$config            = $composer->getConfig();
		$vendorDir         = $config->get( 'vendor-dir', Config::RELATIVE_PATHS ) ?? 'vendor';
		$vendorDirRelative = self::getRelativePath( '/' . $wordpressInstallDir, '/' . $vendorDir );

		$env_paths_code = [];
		$extra          = $composer->getPackage()->getExtra();
		if ( ! empty( $extra['wp-config-env-paths'] ) ) {
			$env_paths = (array) $extra['wp-config-env-paths'];

			foreach ( $env_paths as $env_path ) {
				$env_path = ltrim( $env_path, '/' );
				if ( $env_path ) {
					$env_paths_code[] = sprintf(
						// Don't use __DIR__ as it will cause a parse error in Composer\Plugin\PluginManager.
						'realpath( __DI' . 'R__ . \'%s\' )', // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
						'/' . ltrim( $env_path, '/' )
					);
				} else {
					$env_paths_code[] = '__DI' . 'R__'; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
				}
			}
		} else {
			$env_paths_code[] = '__DI' . 'R__'; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
		}

		$source = dirname( __DIR__ ) . '/res/wp-config.tpl.php';
		$dest   = dirname( $wordpressInstallDir ) . '/wp-config.php';

		// Replace template variables.
		$wpConfig = file_get_contents( $source );
		$wpConfig = str_replace(
			[
				'___WP_CONFIG_VENDOR_DIR___',
				'___WP_CONFIG_ENV_PATHS___',
			],
			[
				$vendorDirRelative,
				'[ ' . implode( ', ', $env_paths_code ) . ' ]',
			],
			$wpConfig
		);

		$copied = file_put_contents( $dest, $wpConfig );

		if ( false === $copied ) {
			$io->writeError( '<error>wp-config.php could not be copied to ' . $dest . '.</error>' );
			return false;
		}

		$io->writeError( '    wp-config.php has been copied to ' . $dest . '.' );
		return true;
	}

	/**
	 * Returns the target path as relative reference from the base path.
	 *
	 * Part of the Symfony package licensed under the MIT License.
	 *
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

		$targetFile = array_pop( $targetDirs );
		foreach ( $sourceDirs as $i => $dir ) {
			if ( isset( $targetDirs[ $i ] ) && $dir === $targetDirs[ $i ] ) {
				unset( $sourceDirs[ $i ], $targetDirs[ $i ] );
			} else {
				break;
			}
		}

		$targetDirs[] = $targetFile;

		$path = str_repeat( '../', \count( $sourceDirs ) ) . implode( '/', $targetDirs );

		// A reference to the same base directory or an empty subdirectory must be prefixed with "./".
		// This also applies to a segment with a colon character (e.g., "file:colon") that cannot be used
		// as the first segment of a relative-path reference, as it would be mistaken for a scheme name
		// (see http://tools.ietf.org/html/rfc3986#section-4.2).
		// phpcs:disable
		return '' === $path || '/' === $path[0]
			|| false !== ( $colonPos = strpos( $path, ':' ) ) && ( $colonPos < ( $slashPos = strpos( $path, '/' ) ) || false === $slashPos )
			? "./$path" : $path;
		// phpcs:enable
	}
}
