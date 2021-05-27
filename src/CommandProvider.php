<?php
/**
 * CommandProvider class
 */

namespace Required\WpConfig;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * Class used to provide Composer commands.
 */
class CommandProvider implements CommandProviderCapability {

	/**
	 * Retrieves an array of commands.
	 *
	 * @return \Composer\Command\BaseCommand[] List of commands.
	 */
	public function getCommands(): array {
		return [ new Command\BuildCommand() ];
	}
}
