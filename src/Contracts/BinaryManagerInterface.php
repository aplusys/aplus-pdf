<?php

namespace Aplus\Pdf\Contracts;

interface BinaryManagerInterface
{
    /**
     * Detect the path to the binary.
     *
     * @param string $driver
     * @return string|null
     */
    public function detect(string $driver): ?string;

    /**
     * Install the binary for the given driver.
     *
     * @param string $driver
     * @param string|null $platform
     * @param bool $force
     * @return bool
     */
    public function install(string $driver, ?string $platform = null, bool $force = false): bool;

    /**
     * Verify if the binary is executable and working.
     *
     * @param string $binaryPath
     * @return bool
     */
    public function verify(string $binaryPath): bool;
}
