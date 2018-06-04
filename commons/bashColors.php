<?php

function red(string $string): void {
    echo implode('', [
        "\033[0;31m",
        $string,
        "\033[0m",
    ]);
}

function green(string $string): void {
    echo implode('', [
        "\033[0;32m",
        $string,
        "\033[0m",
    ]);
}
