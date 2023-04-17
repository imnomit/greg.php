<?php

namespace nomit\Kernel\Command;

use nomit\Console\Command\Command;
use nomit\Console\Definition\Option\Option;
use nomit\Console\Definition\Option\OptionInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Kernel\Secret\VaultInterface;

final class GenerateSecretKeysCommand extends Command
{

    protected VaultInterface $vault;

    protected VaultInterface $local_vault;

    public function __construct(VaultInterface $vault, VaultInterface $localVault)
    {
        $this->vault = $vault;
        $this->local_vault = $localVault;

        parent::__construct('secrets:generate-keys');
    }

    public function configure(): void
    {
        $this
            ->setDescription('Generates new encryption keys')
            ->addOption(new Option('local', 'l', OptionInterface::VALUE_NONE, 'Update the local vault.'))
            ->addOption(new Option('rotate', 'r', OptionInterface::VALUE_NONE, 'Re-encrypt existing secrets with the newly generated keys.'))
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command generates a new encryption key.
    <info>%command.full_name%</info>
If encryption keys already exist, the command must be called with
the <info>--rotate</info> option in order to override those keys and re-encrypt
existing secrets.
    <info>%command.full_name% --rotate</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ConsoleStyle($input, $output);

        $vault = $input->getOption('local') ? $this->local_vault : $this->vault;

        if (null === $vault) {
            $io->success('The local vault is disabled.');

            return 1;
        }

        if (!$input->getOption('rotate')) {
            if ($vault->generateKeys()) {
                $io->success($vault->getLastMessage());

                if ($this->vault === $vault) {
                    $io->caution('DO NOT COMMIT THE DECRYPTION KEY FOR THE PROD ENVIRONMENT⚠️');
                }

                return 0;
            }

            $io->warn($vault->getLastMessage());

            return 1;
        }

        $secrets = [];

        foreach ($vault->list(true) as $name => $value) {
            if (null === $value) {
                $io->error($vault->getLastMessage());

                return 1;
            }

            $secrets[$name] = $value;
        }

        if (!$vault->generateKeys(true)) {
            $io->warn($vault->getLastMessage());

            return 1;
        }

        $io->success($vault->getLastMessage());

        if ($secrets) {
            foreach ($secrets as $name => $value) {
                $vault->seal($name, $value);
            }

            $io->comment('Existing secrets have been rotated to the new keys.');
        }

        if ($this->vault === $vault) {
            $io->caution('Do not commit the decryption key for the production environment. ⚠️');
        }

        return 0;
    }

}