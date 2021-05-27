<?php
/**
 * BuildCommand class
 */

namespace Required\WpConfig\Command;

use Composer\Command\BaseCommand;
use Required\WpConfig\Plugin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class used to provide the build-wp-config command.
 */
class BuildCommand extends BaseCommand {

	/**
	 * Configures the command.
	 */
	protected function configure(): void {
		$this->setName( 'build-wp-config' );
		$this->setDescription( 'Builds wp-config.php and copies it to the WordPress installation directory.' );
	}

	/**
	 * Executes the command.
	 *
	 * @throws \RuntimeException When WordPress package not found.
	 *
	 * @param \Command\Symfony\Component\Console\Input\InputInterface  $input Input interface.
	 * @param \Command\Symfony\Component\Console\Input\OutputInterface $output Input interface.
	 * @return int 0 if everything went fine, or an exit code.
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$composer         = $this->getComposer();
		$wordpressPackage = $composer->getRepositoryManager()->getLocalRepository()->findPackage( Plugin::WORDPRESS_CORE_PACKAGE_NAME, '*' );

		if ( ! $wordpressPackage ) {
			throw new \RuntimeException( 'WordPress package not found' );
		}

		$copied = Plugin::copyWpConfig( $composer, $wordpressPackage, $this->getIo() );
		return $copied ? 0 : 1;
	}
}
