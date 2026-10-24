<?php

namespace App\Menu\Filters;

use ColorlibHQ\AdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Gate;

class SubmenuGateFilter implements FilterInterface
{
    public function transform(array $item): ?array
    {
        if (!$this->allowed($item)) {
            return null;
        }

        if (isset($item['submenu'])) {
            $item['submenu'] = array_values(array_filter(
                array_map(fn($child) => $this->transform($child), $item['submenu'])
            ));

            if ($item['submenu'] === []) {
                return null;
            }
        }

        return $item;
    }

    private function allowed(array $item): bool
    {
        if (!isset($item['can'])) {
            return true;
        }

        foreach ((array) $item['can'] as $ability) {
            if (Gate::allows($ability, $item['can_params'] ?? [])) {
                return true;
            }
        }

        return false;
    }
}