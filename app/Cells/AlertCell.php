<?php

namespace App\Cells;

class AlertCell
{

    public function info(array $params): string
    {
        return $this->generate('info', 'information', $params['message'] ?? '');
    }

    public function success(array $params): string
    {
        return $this->generate('success', 'check-circle', $params['message'] ?? '');
    }

    public function warn(array $params): string
    {
        return $this->generate('warn', 'alert', $params['message'] ?? '');
    }

    public function error(array $params): string
    {
        return $this->generate('error', 'alert-octagon', $params['message'] ?? '');
    }

    public function custom(array $params): string
    {
        return $this->generate($params['type'] ?? 'custom', $params['message'] ?? '', $params['message'] ?? '');
    }

    protected function generate(string $type, string $icon, string $message): string
    {
        $html = "<div class=\"alert $type\">\n";
        $html .= "    <span class=\"iconify\" data-icon=\"mdi-$icon\"></span>\n";
        $html .= "    <span class=\"alert-$type\">$message</span>\n";
        $html .= "</div>\n";
        return $html;
    }

}