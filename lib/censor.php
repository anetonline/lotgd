<?php
declare(strict_types=1);

function soap(string $input, bool $debug = false, bool $skiphook = false): string
{
    // Censorship is disabled: just return what was given.
    return $input;
}

function good_word_list(): array
{
    // No good words needed, censorship is disabled.
    return [];
}

function nasty_word_list(): array
{
    // No nasty words, censorship is disabled.
    return [];
}
