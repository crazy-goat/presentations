# Running PHP Apps FROM scratch

Presentation about PHP deployment history — from FTP to a statically compiled binary in a `FROM scratch` Docker image.

## Commands

```shell
make build       # build Docker image
make run         # run on http://localhost:3210
make push        # build + push to Docker Hub
make run-dev     # run locally (requires PHP) on http://localhost:3210
make stop-dev    # stop local server
make clean       # stop container + remove image
```

## Slides keyboard shortcuts

- `s` — speaker view
- `f` — full screen
- `esc` — slides overview
