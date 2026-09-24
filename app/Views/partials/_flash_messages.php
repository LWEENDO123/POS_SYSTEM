<?php
/**
 * Shared flash-message + validation-error feedback partial.
 *
 * Include it right after the page's main content wrapper opens, e.g.:
 *     <?= view('partials/_flash_messages') ?>
 *
 * It renders, when present:
 *   - flashdata 'error'    (red)
 *   - flashdata 'warning'  (amber)
 *   - flashdata 'success'  (green)
 *   - flashdata 'message'  (teal/info)
 *   - validation errors    (passed in to the view as $validation or $validate)
 *
 * Inline styles are used on purpose so the feedback is visible on every page
 * regardless of that page's own CSS / theme.
 */

$__session = function_exists('session') ? session() : null;
$__blocks  = [];

if ($__session !== null) {
    foreach ([
        'error'   => '#AE3E37',
        'warning' => '#B8860B',
        'success' => '#108B04',
        'message' => '#0A5741',
    ] as $__key => $__color) {
        $__value = $__session->getFlashdata($__key);
        if (!empty($__value)) {
            $__values = is_array($__value) ? $__value : [$__value];
            foreach ($__values as $__item) {
                if ($__item !== null && $__item !== '') {
                    $__blocks[] = [$__color, (string) $__item];
                }
            }
        }
    }
}

// Validation errors (the controllers pass the validator object as $validate).
$__validator = $validation ?? $validate ?? null;
if ($__validator !== null) {
    $__errors = [];
    if (is_object($__validator) && method_exists($__validator, 'getErrors')) {
        $__errors = $__validator->getErrors();
    } elseif (is_array($__validator)) {
        $__errors = $__validator;
    }
    foreach ($__errors as $__field => $__msg) {
        if (is_array($__msg)) {
            foreach ($__msg as $__m) {
                $__blocks[] = ['#AE3E37', (string) $__m];
            }
        } else {
            $__blocks[] = ['#AE3E37', (string) $__msg];
        }
    }
}

unset($__session, $__value, $__values, $__item, $__validator, $__errors, $__field, $__msg);

if ($__blocks === []) {
    return;
}
?>
<div class="flash-messages" style="margin-bottom:14px;">
  <?php foreach ($__blocks as $__block): ?>
    <?php [$__color, $__text] = $__block; ?>
    <div style="background:<?= $__color ?>;color:#fff;padding:10px 14px;border-radius:8px;margin-bottom:8px;font-size:14px;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,0.18);">
      <?= esc($__text) ?>
    </div>
  <?php endforeach; ?>
</div>