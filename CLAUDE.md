# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Structure

This is a monorepo of reveal.js-based presentation slides, each in its own directory under `presentations/`:

- `presentations/00-template/` — base template for new presentations
- `presentations/01-esi/` — ESI presentation
- `presentations/02-php-runtime/` — PHP runtime presentation
- `presentations/03-single-endpoint/` — Single endpoint presentation

Each presentation is self-contained with its own `Dockerfile`, `Makefile`, and `slides/` directory.

## Architecture

Each presentation follows the same structure:

- `slides/index.html` — the main reveal.js presentation file (edit this to change slides)
- `slides/` — reveal.js v5 setup with `gulpfile.js` for local dev (live reload via `gulp-connect`)
- `Dockerfile` — multi-stage build: `base` (nginx + supervisord serving static files) and `dev` (adds npm for live reload)
- `config/nginx/default.conf` — nginx serves `/var/www/slides` on port 80
- `config/supervisor/` — supervisord configs for production (`supervisord.conf`) and dev (`supervisord-dev.conf`)

The `dist/` folder inside `slides/` contains pre-built reveal.js assets — do not regenerate them; they're checked in.

## Working with Presentations

All `make` commands must be run from within the specific presentation directory (e.g., `presentations/03-single-endpoint/`).

### Development (with live reload)

```shell
# From within a presentation directory:
make run-dev
# Slides served at http://127.0.0.1:3210, live-reloads on HTML/Markdown changes
```

The dev container mounts `./slides` as a volume, so edits to `slides/index.html` (or other `.html`/`.md` files) trigger live reload.

### Production build & run

```shell
make build   # builds crazygoat/presentation-<NAME>:latest Docker image
make run     # runs the production image on port 3210
make push    # build + push to Docker Hub
make clean   # remove node_modules, stop containers, remove images
```

### Local dev without Docker

```shell
cd slides && npm i && npm start
# visit http://127.0.0.1:8000
# alternate port: npm start -- --port=8001
```

### Presentation keyboard shortcuts

- `s` — speaker view
- `f` — full screen
- `esc` — slides overview

## Creating a New Presentation

Copy `presentations/00-template/` to a new numbered directory, update `NAME` in the `Makefile`, and edit `slides/index.html`. The template `Dockerfile` targets `Linux_64-bit` supervisord; `03-single-endpoint` uses `Linux_ARMv7` — match the target architecture if needed.

## Slide Authoring Notes

Slides are written as HTML `<section>` elements in `slides/index.html`. Key reveal.js patterns used in this repo:

- `data-auto-animate` / `data-auto-animate-id` — animated transitions between slides sharing the same id
- `class="fragment"` — step-by-step reveal of list items
- `class="r-fit-text"` — auto-scaling text
- `<aside class="notes">` — speaker notes
- Multiple HTML files in `slides/` are supported (e.g., `decodo.html`, `short.html` in `03-single-endpoint`)
