---
description: "Use when writing, reviewing, or refactoring Blade views with Bootstrap 5. Covers cards, forms, tables, lists, modals, badges, alerts, spacing, and layout."
applyTo: "resources/views/**/*.blade.php"
---

# Bootstrap 5 – Project UI Guidelines

## Cards

- Always use `card > card-header + card-body` nesting (add `card-footer` only when needed).
- Use `border-0 bg-light` for summary/stat cards to distinguish them visually.
- Summary cards use `py-2` (not the default `py-4`) and a horizontal `d-flex justify-content-between align-items-center` layout inside `card-body`.
- For compact list panels (e.g. inside a two-column layout), set `max-height` with `overflow-y: auto` on the scrollable section directly — do not wrap the whole card.

```blade
{{-- Stat card --}}
<div class="card bg-light border-0">
    <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <p class="small text-muted mb-0">Label</p>
        <p class="h5 mb-0 text-primary">{{ $count }}</p>
    </div>
</div>
```

## Buttons

- Default action buttons: `btn btn-primary`.
- Secondary/cancel actions: `btn btn-outline-secondary`.
- Destructive actions: `btn btn-danger`.
- Use `btn-sm` inside card headers, toolbars, and list items — never `btn-lg`.
- Group related buttons with `d-flex gap-2`, not `btn-group` (unless you need radio-like toggle semantics).
- Disable submit/action buttons in JS until required selections are made; re-enable via `updateCounts()` pattern.

## Forms

- Wrap each field in `<div class="mb-3">` with `form-label` + `form-control`.
- Multi-column forms: use `<div class="row g-3">` with `col-md-*` children.
- Search bars: `form-control form-control-sm` with a `btn btn-sm btn-outline-secondary` trigger.
- For search that navigates rather than submits: update URL params via `window.location.href` in JS instead of form submission.
- Hidden inputs preserve filter state across POST redirects:

```blade
<input type="hidden" name="search" value="{{ $search }}" />
```

## Tables

- Always wrap in `<div class="table-responsive">`.
- Base classes: `table table-hover table-sm`.
- Header rows: `class="table-warning"` on `<tr>` with `scope="col"` on `<th>`.
- Data highlight rows: `class="table-primary"` on `<tr>`.

## List Groups

- Use `list-group list-group-flush` inside cards.
- For compact items: add `py-1 px-2` to each `.list-group-item` — do not rely on Bootstrap defaults when space is tight.
- Use `label.list-group-item.list-group-item-action` when the whole row must be clickable (checkbox/radio selection).
- Inner layout: `d-flex gap-2 align-items-start` (prefer `gap-2` over `gap-3` in compact contexts).
- Text inside list items: use `mb-0` and inline `style="font-size: 0.8rem"` for the primary line, `0.7rem` for secondary details — prefer `text-truncate` on all text nodes.

## Modals

- Always use the project's `<x-modal>` component — never raw Bootstrap modal HTML.
- Modals must **always** be rendered **outside** any `<form>` element (invalid HTML, causes DOM errors).
- For fullscreen preview modals: `<x-modal fullscreen="true" ...>`.
- Trigger buttons inside forms must call `event.preventDefault(); event.stopPropagation();` to avoid accidental form submission.

```blade
{{-- Trigger (inside form or list) --}}
<button type="button" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}"
        onclick="event.preventDefault(); event.stopPropagation();">Open</button>

{{-- Modal (OUTSIDE form) --}}
<x-modal modal-title="Title" :modal-id="$modalId" fullscreen="true">
    <x-slot:body>...</x-slot>
</x-modal>
```

## Alerts / Flash Messages

- Always use the project's `@include('partials.flash')` partial — do not write inline alert HTML.
- Alert types: `alert-success`, `alert-warning`, `alert-danger`.
- Always add `alert-dismissible fade show` + `.btn-close` button.
- Use `d-flex align-items-center` with SVG icon + `me-2` spacing inside the alert.

## Badges

- Standard status: `badge text-bg-secondary`.
- Use semantic colors: `bg-success` (complete/OK), `bg-warning` (missing/attention), `bg-danger` (error), `bg-info` (extra/informational).


## Spacing

- Bottom margin default: `mb-3` between standalone elements; `mb-1` or `mb-0` inside compact list items.
- Flex gap: `gap-2` in compact toolbars and list rows; `gap-3` only when items need more breathing room (e.g. main page sections).
- Vertical padding in card bodies: `py-2` for compact stat/summary cards; default (`py-4`) for content cards.
- Do not use `my-*` when only one direction is needed — prefer explicit `mt-*` / `mb-*`.

## Layout & Grid

- Use `col-md-*` as the primary breakpoint (project assumes medium screens minimum).
- Two-column panels: `col-lg-4` (narrow list) + `col-lg-8` (wide content), wrapped in `row g-3`.
- Do not hardcode widths with inline `style="width: Xrem"` unless the element is a fixed-dimension thumbnail — use responsive classes instead.
- Thumbnails in list items: use inline `style="width/height"` with `object-fit: cover; flex-shrink: 0` — keep sizes uniform (e.g. `56×44px` compact, `80×60px` standard).

## Do / Don't

| Do | Don't |
|----|-------|
| `btn btn-sm btn-outline-secondary` | `btn-lg` anywhere |
| `list-group-flush` inside cards | standalone `list-group` outside cards |
| Modals outside `<form>` | Nested modals inside `<form>` |
| `badge text-bg-*` for status labels | Pure text or inline color for status |
| `table-responsive` wrapper | Bare `<table>` without wrapper |
| `<x-modal>` component | Raw `<div class="modal">` HTML |
| `d-flex gap-2` for button rows | `float-*` or margins between buttons |
