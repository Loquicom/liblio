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
