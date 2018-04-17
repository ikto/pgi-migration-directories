<?php

namespace IKTO\PgiMigrationDirectories\Processor;

use IKTO\PgI\Database\ConvenientDatabaseInterface;
use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgiMigrationDirectories\Migration\StandardMigrationDefinition;
use Symfony\Component\Finder\Finder;

class StandardProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyMigration(ConvenientDatabaseInterface $db, DefinitionInterface $migration)
    {
        /** @var StandardMigrationDefinition $migration */
        $sqlCommands = $this->getSqlCommandsFromMigration($migration);

        foreach ($sqlCommands as $sqlCommand) {
            $db->doQuery($sqlCommand);
        }
    }

    /**
     * Extracts SQL commands from the migration.
     *
     * @param StandardMigrationDefinition $migration
     *   The migration to extract SQL commands from.
     *
     * @return string[]
     */
    protected function getSqlCommandsFromMigration(StandardMigrationDefinition $migration)
    {
        $files = [];
        $finder = new Finder();
        $finder
            ->files()
            ->in($migration->getBase())
            ->name('/\.sql$/')
            ->notName('/^\./')
            ->notName('/\~$/')
            ->sortByName()
        ;

        foreach ($finder as $fileInfo) {
            $files[] = $fileInfo->getRealPath();
        }

        $commands = [];
        foreach ($files as $file) {
            $commands = array_merge($commands, $this->getSqlCommandsFromFile($file));
        }

        return $commands;
    }

    /**
     * Extracts SQL commands from the SQL file.
     *
     * @param string $filename
     *   The SQL file name.
     *
     * @return string[]
     */
    protected function getSqlCommandsFromFile($filename)
    {
        // Read file, exclude commented out lines.
        $content = implode('', preg_grep('/^\s*\-\-/', file($filename), PREG_GREP_INVERT));
        // The following code is a crap IMHO, but at least it works.
        // The idea has been taken from Data::Record CPAN module.
        // @see http://search.cpan.org/~ovid/Data-Record-0.02/lib/Data/Record.pm
        // The target is - split SQL file to commands, but don't split parts what shouldn't be split.
        // TODO: Think about more clean way to do the same thing.
        $replacementToken = $this->createReplacementToken($content);
        $values = [];
        $index = 0;
        // Mask non-splittable parts by replacement token.
        $nonSplittablePattern = '/\$\$.*?\$\$/s';
        $content = preg_replace_callback(
            $nonSplittablePattern,
            function ($matches) use (&$values, &$index, $replacementToken) {
                $values[$index] = $matches[0];
                $replacement = $replacementToken.$index.$replacementToken;
                $index++;

                return $replacement;
            },
            $content
        );
        // Perform actual split.
        $commands = preg_split('/;\s*\n/s', $content);
        // Unmask non-splittable parts.
        foreach ($commands as &$command) {
            for ($i = 0; $i < $index; $i++) {
                $command = str_replace($replacementToken.$i.$replacementToken, $values[$i], $command);
            }
        }
        unset($command);
        // Remove blank commands.
        $commands = preg_grep('/\S/s', $commands);

        return $commands;
    }

    /**
     * Creates the replacement token for masking non-splittable parts.
     *
     * The main requirement - token shouldn't occur in the input content.
     *
     * @param string $content
     *   The input content.
     *
     * @return string
     */
    protected function createReplacementToken($content)
    {
        $tokens = array_map(function ($str) {
            return str_repeat($str, 6);
        }, ['~', '`', '?', '"', '{', '}', '!', '@', '$', '%', '^', '&', '*', '-', '_', '+', '=']);

        $tokenIndex = 0;
        while (strpos($content, $tokens[$tokenIndex]) !== false) {
            $tokenIndex++;
            if ($tokenIndex >= count($tokens)) {
                throw new \InvalidArgumentException('Could not create token for the input data');
            }
        }

        return $tokens[$tokenIndex];
    }
}
