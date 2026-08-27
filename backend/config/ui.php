<?php
declare(strict_types=1);

/*
 * UI helpers shared by the create/edit forms.
 *
 * trending_options() / trending_value_options() render <option> lists that
 * "follow the trend": on a NEW record the values the factory has been using
 * most recently (and most often) float to the top under a "Recently used"
 * group, with the single most likely choice pre-selected. On an EDIT the
 * stored value is simply marked selected and the list keeps its natural
 * order, so editing is never surprising.
 */

/**
 * Format an amount in Ghana Cedis (GH₵) for display.
 * Single source of truth for the currency symbol across views.
 */
function money(float|int|string|null $amount): string {
    return 'GH₵ ' . number_format((float)$amount, 2);
}

/**
 * The real Propine Fruity product flavours (from the product labels).
 * Single source of truth used by production, finished goods and pricing.
 */
function flavour_options(): array {
    return [
        'Pineapple',
        'Orange',
        'Mango',
        'Beet Root',
        'Ginger',
        'Cocktail',
        'Pineapple Ginger',
        'Coconut',
        'Pine-Ginger',
        'Mango-Pine-Ginger',
        'Beetroot-Pine-Ginger',
        'Mango Passion',
        'Mango Ginger',
    ];
}

/**
 * Entity selects (machines, materials, customers, ...).
 *
 * @param array  $items       rows, e.g. machines
 * @param string $idKey       key of the option value in each row
 * @param string $labelKey    key of the option label in each row
 * @param ?array $trend       Controller::trendIds() result (null = plain order)
 * @param ?string $selected   stored id when editing
 * @param string $placeholder empty first option label ('' = none)
 * @param array  $extraAttrs  per-id extra attributes: ['MCH-001' => 'data-unit="kg"']
 */
function trending_options(
    array $items,
    string $idKey,
    string $labelKey,
    ?array $trend = null,
    ?string $selected = null,
    string $placeholder = '',
    array $extraAttrs = []
): void {
    $byId = [];
    foreach ($items as $row) {
        $id = (string)($row[$idKey] ?? '');
        if ($id === '') continue;
        $byId[$id] = $row;
    }

    // Trending subset that still exists among current records.
    $trendingIds = [];
    if ($trend !== null && $selected === null) {
        foreach ($trend as $t) {
            $v = (string)$t['value'];
            if ($v !== '' && isset($byId[$v]) && !in_array($v, $trendingIds, true)) {
                $trendingIds[] = $v;
            }
        }
    }

    if ($placeholder !== '') {
        echo '<option value="">' . sanitize($placeholder) . '</option>';
    }

    $rendered = [];
    $renderOpt = function (string $id) use (&$rendered, $byId, $idKey, $labelKey, $selected, $extraAttrs): void {
        if (!isset($byId[$id]) || in_array($id, $rendered, true)) return;
        $row = $byId[$id];
        $sel = $selected !== null && $selected === $id ? ' selected' : '';
        $attrs = $extraAttrs[$id] ?? '';
        echo '<option value="' . sanitize($id) . '"' . $sel . ($attrs !== '' ? ' ' . $attrs : '') . '>'
            . sanitize((string)($row[$labelKey] ?? $id)) . '</option>';
        $rendered[] = $id;
    };

    if ($trendingIds) {
        echo '<optgroup label="Recently used">';
        foreach ($trendingIds as $id) $renderOpt($id);
        echo '</optgroup>';
    }

    // Remaining options alphabetically.
    $rest = array_diff(array_keys($byId), $rendered);
    usort($rest, fn($a, $b) => strcasecmp((string)($byId[$a][$labelKey] ?? $a), (string)($byId[$b][$labelKey] ?? $b)));
    foreach ($rest as $id) $renderOpt((string)$id);
}

/**
 * Static value selects (flavours, waste types, shifts, ...).
 *
 * @param array   $values     allowed values
 * @param ?array  $trend      trendIds() result for this column
 * @param ?string $selected   stored value when editing
 * @param string  $placeholder
 */
function trending_value_options(array $values, ?array $trend = null, ?string $selected = null, string $placeholder = ''): void
{
    if ($placeholder !== '') {
        echo '<option value="">' . sanitize($placeholder) . '</option>';
    }

    $trending = [];
    if ($trend !== null && $selected === null) {
        foreach ($trend as $t) {
            $v = (string)$t['value'];
            if ($v !== '' && in_array($v, $values, true) && !in_array($v, $trending, true)) {
                $trending[] = $v;
            }
        }
    }

    $done = [];
    $emit = function (string $v) use (&$done, $values, $selected): void {
        if (!in_array($v, $values, true) || in_array($v, $done, true)) return;
        $sel = $selected !== null && $selected === $v ? ' selected' : '';
        echo '<option value="' . sanitize($v) . '"' . $sel . '>' . sanitize($v) . '</option>';
        $done[] = $v;
    };

    if ($trending) {
        echo '<optgroup label="Recently used">';
        foreach ($trending as $v) $emit($v);
        echo '</optgroup>';
    }
    foreach ($values as $v) $emit((string)$v);
}
