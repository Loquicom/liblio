<?php

function respond_page($data, $total = null, $page = null): array
{
    if ($total == null) $total = $data['total'];
    if ($page == null) $page = $data['page'];
    if (isset($data['data'])) $data = $data['data'];

    return [
        'success' => true,
        'data' => [
            'total' => $total,
            'page' => $page,
            'result' => count($data),
            'values' => $data
        ]
    ];
}

function respond_success($data = []): array
{
    return [
        'success' => true,
        'data' => $data
    ];
}

function respond_error($message): array
{
    return [
        'success' => false,
        'message' => $message
    ];
}

function adapt_rules_and_data_for_update($entity, $data, $rules, $exclude = []): array
{
    $newRules = $rules;
    $newData = $data;
    foreach ($rules as $rule => $validation) {
        if (in_array($rule, $exclude)) continue;
        if (isset($data[$rule])) {
            // No change, remove
            if ($entity[$rule] === $data[$rule]) {
                unset($newData[$rule]);
                unset($newRules[$rule]);
            }
        } else {
            // No data for the rules remove
            unset($newRules[$rule]);
        }
    }

    return [
        'rules' => $newRules,
        'data' => $newData
    ];
}