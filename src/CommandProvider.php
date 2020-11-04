<?php
/**
 * CommandProvider class
 */

namespace Required\WpConfig;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability {

	/**
	 * Retrieves an array of commands
	 *
	 * @return \Composer\Command\BaseCommand[]
	 */
	public function getCommands() {
		return [ new Command\BuildCommand() ];
	}
}
