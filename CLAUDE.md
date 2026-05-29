# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a Statamic addon that adds an image hotspots fieldtype to the control panel. It supports both Statamic 5 and 6 (`statamic/cms: ^5.0|^6.0`, PHP `^8.2`). Hotspot positions are stored as percentages (x/y) to support responsive absolute positioning. The addon provides a Vue UI component, an Antlers template tag, and GraphQL support.

## Commands

### PHP
```bash
composer lint          # Auto-fix PHP code style with Pint
composer lint:check    # Check PHP style without modifying
```

### JavaScript
```bash
npm run dev     # Start Vite dev server (watch mode)
npm run build   # Compile assets to resources/dist/
npm run format  # Format JS/Vue/CSS with Prettier
```

## Architecture

### PHP Backend (`src/`)

- **`ServiceProvider.php`** — Addon bootstrap; registers the Antlers tag and GraphQL types; configures Vite assets pointing to `resources/js/main.js` and `resources/css/main.css`.
- **`Fieldtypes/ImageHotSpots.php`** — Core fieldtype class. Handles augmentation (resolving asset URLs), processing/pre-processing (recursive field handling for nested fieldtypes in hotspot content), and config (deadzone boundaries, asset container, fieldset for hotspot content).
- **`Tags/HotSpotImageTag.php`** — Antlers tag `{{ image_hot_spots }}`. Handles both field objects (standard entry fields via `value()`) and raw array data (global field references).
- **`GraphQL/`** — Three types: `ImageHotSpotsType` (top-level), `HotImageType` (image file data), `HotspotType` (x, y, content).

### JavaScript Frontend (`resources/js/`)

- **`main.js`** — In `Statamic.booting()`, registers the component as `image_hot_spots-fieldtype` (Statamic derives this name from the fieldtype handle + `-fieldtype` suffix).
- **`components/ImageHotspots.vue`** — Vue component using `mixins: [Fieldtype]`. Handles image upload (delegating to Statamic's `assets-fieldtype`), visual hotspot placement via click/drag, deadzone visualization, and dynamic nested field rendering for hotspot content using configured fieldtypes. Communicates value changes via `@update:value` and `meta-updated` events (Statamic 6 convention).

### Build setup (`vite.config.js`)

The CP bundle is built for Statamic 6's runtime, not as a standalone app:
- `vue` is marked **external** and mapped to the `Vue` global — the addon shares the CP's Vue instance rather than bundling its own.
- Output is a single **IIFE** with `inlineDynamicImports`.
- A `banner` shims the global `Fieldtype` mixin from `window.__STATAMIC__.core.FieldtypeMixin` so `ImageHotspots.vue` can `mixins: [Fieldtype]`.

This pattern is what makes the same source work inside the Statamic 6 CP. Changing the externals/banner will break fieldtype registration.

### Assets (`resources/`)

- Tailwind CSS with `i-` prefix on all utilities (to avoid conflicts with Statamic's CP styles) and `preflight` disabled.
- Compiled output goes to `resources/dist/` (committed to repo).
- Node v22 (see `.nvmrc`).

## Key Data Structure

A field value looks like:
```json
{
  "image": "<asset-id>",
  "hotspots": [
    {
      "x": 45.2,
      "y": 30.1,
      "content": { "title": "...", "description": "..." }
    }
  ]
}
```

Hotspot `x`/`y` are percentages (0–100) relative to the image dimensions.

## Important Notes

- **Cross-version compatibility**: Code must run on both Statamic 5 and 6. Where the two diverge (e.g. renamed CP routes — `blueprints.asset-containers.edit` became `asset-containers.blueprint.edit` in 5.73), resolve the difference at runtime (check `Route::has(...)`) rather than hardcoding one name.
- **Tailwind prefix**: All Tailwind classes use the `i-` prefix (e.g., `i-flex`, `i-text-sm`) to avoid CP style conflicts.
- **Compiled assets**: `resources/dist/` is committed; always run `npm run build` before committing frontend changes.
- **Deadzone config**: Fieldtype config supports `deadzone_left`, `deadzone_right`, `deadzone_top`, `deadzone_bottom` (percentages) to restrict where hotspots can be placed.
- **CI**: `.github/workflows/ci.yml` runs `pint --test` on PRs and pushes to `main`. There is no test suite — Pint style is the only gate.
