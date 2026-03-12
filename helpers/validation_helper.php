<?php

declare(strict_types=1);

function validate_required(array $data, array $fields): array
{
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    return $errors;
}

function validate_file_no_in_range(int $fileNo, int $start, int $end): bool
{
    return $fileNo >= $start && $fileNo <= $end;
}
