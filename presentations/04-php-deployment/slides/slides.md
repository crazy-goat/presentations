# Running PHP Apps <!-- .element: class="r-fit-text" -->
# FROM scratch <!-- .element: class="r-fit-text" style="font-style:italic" -->

*From green meadows of FTP to the dungeons where dragons live.*

Note: Play on words — FROM scratch is both a Docker instruction and starting from zero.

---

## Agenda

1. History of PHP deployment
2. Does PHP have to work this way?<!-- .element: class="fragment" -->
3. Alternative runtimes<!-- .element: class="fragment" -->
4. Smaller images<!-- .element: class="fragment" -->
5. FROM scratch<!-- .element: class="fragment" -->
6. 🐉 Here be dragons<!-- .element: class="fragment" -->

Note: Deliberately not revealing the ending in the agenda.

---

## Era: FTP <!-- .element: class="r-fit-text" -->

---

### How it looked

- Zend IDE / Eclipse with built-in FTP sync
- FileZilla open on production<!-- .element: class="fragment" -->
- Upload file by file<!-- .element: class="fragment" -->
- Pray nobody else is editing the same file<!-- .element: class="fragment" -->

---

### First deployment

- Upload `app.tar.gz` via FTP<!-- .element: class="fragment" -->
- But how do you unpack it? No SSH access...<!-- .element: class="fragment" -->
- Solution: `deploy.php` — a script that unpacks the archive<!-- .element: class="fragment" -->
- Run it in the browser. Done. **Now delete it** (or forget — security nightmare)<!-- .element: class="fragment" -->
- On larger projects: request timeout halfway through 🙃<!-- .element: class="fragment" -->

---

### Real world: Sote ~2005

Polish e-commerce platform — classic FTP era

- Installation: upload `sote.tar.gz`, run install script in browser<!-- .element: class="fragment" -->
- Set permissions, configure DB, set up paths manually<!-- .element: class="fragment" -->
- Updates delivered as `.diff` files (unified diff format)<!-- .element: class="fragment" -->
- A PHP script parsed and applied the diff — basically `patch`, but in PHP, in the browser<!-- .element: class="fragment" -->
- Hotfixes and debugging: upload the fixed file directly via FTP<!-- .element: class="fragment" -->

---

### Real world: Sote 5.0 ~2008

- Installation: copy a single `installer.php` to the server, open in browser<!-- .element: class="fragment" -->
- It downloads the package itself, checks dependencies, runs setup<!-- .element: class="fragment" -->
- Modular architecture — PEAR packages (Composer didn't exist yet)<!-- .element: class="fragment" -->
- Updates: upgrade individual modules, not the whole shop<!-- .element: class="fragment" -->
- File integrity check before install — **did not overwrite client customizations**<!-- .element: class="fragment" -->
- Custom plugin system, built on Symfony 1.0<!-- .element: class="fragment" -->
- Fixes and debugging: delivered as a PEAR package update, not raw FTP<!-- .element: class="fragment" -->

Note: This was ahead of its time — essentially a package manager + installer + update system built in 2008.

---

### The pain

- No versioning — "I overwrote prod"<!-- .element: class="fragment" -->
- No rollback<!-- .element: class="fragment" -->
- Binary vs ASCII transfer mode — corrupted files, broken encodings<!-- .element: class="fragment" -->
- File permissions — chmod everything via FTP client<!-- .element: class="fragment" -->
- Symlinks — FTP doesn't support them<!-- .element: class="fragment" -->
- "Works on my machine"<!-- .element: class="fragment" -->

---

### Think this era is over?

Think again.

There are entire armies of WordPress sites still maintained exactly this way.<!-- .element: class="fragment" -->

---

## Era: Own scripts <!-- .element: class="r-fit-text" -->

---

### deploy.sh

```bash
#!/bin/bash
rsync -avz --exclude='.git' ./ user@prod:/var/www/app/
ssh user@prod "cd /var/www/app && php bin/console cache:clear"
```

---

### Real world: Komputronik.pl ~2015

- CI on Jenkins — pull from git to **stage**, run tests<!-- .element: class="fragment" -->
- Tests pass → rsync to **4 prod instances**, sequentially from stage<!-- .element: class="fragment" -->
- Mandatory cache clear after each deploy<!-- .element: class="fragment" -->
- ~10 deploys per day<!-- .element: class="fragment" -->
- Revert: `git revert` + redeploy<!-- .element: class="fragment" -->
- DB migrations run at night — no instant DDL in MySQL back then<!-- .element: class="fragment" -->
- Deploy access: **wizards only** — not everyone had the power<!-- .element: class="fragment" -->
- Script in Bash... with fragments in Ruby<!-- .element: class="fragment" -->
- Debugging: SSH directly into prod<!-- .element: class="fragment" -->

---

### The pain

- How do you sync to multiple servers?<!-- .element: class="fragment" -->
- Migrations — where do you run them? On which server? Before or after rsync?<!-- .element: class="fragment" -->
- High PR volume — who triggers the deploy? When?<!-- .element: class="fragment" -->
- Night deploys — "let's do it at 2am when traffic is low"<!-- .element: class="fragment" -->
- Hard to test outside your own dev environment<!-- .element: class="fragment" -->
- No documentation, no standards — every company had their own<!-- .element: class="fragment" -->
- Bus factor: 1 — only the author knew how it worked<!-- .element: class="fragment" -->
- Written in whatever the author liked — Ruby, Perl, Bash, Make...<!-- .element: class="fragment" -->

---

### 🙋 Quick question

Anyone here still maintaining a large project with heavily customised deploy scripts?

---

## Era: Ansible <!-- .element: class="r-fit-text" -->

---

### How it works

```yaml
- name: Deploy PHP app
  hosts: production
  tasks:
    - name: Pull latest release
      git:
        repo: git@github.com:org/app.git
        dest: /var/www/releases/{{ release_name }}
    - name: Install dependencies
      composer:
        working_dir: /var/www/releases/{{ release_name }}
    - name: Run migrations
      command: php bin/console doctrine:migrations:migrate --no-interaction
      args:
        chdir: /var/www/releases/{{ release_name }}
    - name: Switch symlink
      file:
        src: /var/www/releases/{{ release_name }}
        dest: /var/www/current
        state: link
```

---

### What got better

- Multiple releases kept on server — rollback = flip a symlink<!-- .element: class="fragment" -->
- Run migrations on one node first, then roll out code<!-- .element: class="fragment" -->
- Rolling release — replace nodes one at a time, zero downtime<!-- .element: class="fragment" -->
- Native multi-node support out of the box<!-- .element: class="fragment" -->

---

### The pain

- Server must have PHP, nginx, php-fpm installed and configured<!-- .element: class="fragment" -->
- Environments drift over time — "works on staging, fails on prod"<!-- .element: class="fragment" -->
- Requires SSH access and **Python** on every target server<!-- .element: class="fragment" -->
- Why do I need Python on a PHP server?<!-- .element: class="fragment" -->

---

### The common problem

FTP, scripts, Ansible — three different eras, one shared flaw:

**The application and the environment were managed separately.**<!-- .element: class="fragment" -->

- App updated regularly — environment left to age on its own<!-- .element: class="fragment" -->
- Dev team owned the code, ops team owned the server<!-- .element: class="fragment" -->
- Every upgrade became a negotiation<!-- .element: class="fragment" -->
- "It worked last time we deployed..."<!-- .element: class="fragment" -->
- Dev environment looked nothing like prod<!-- .element: class="fragment" -->
- Setting up a new dev environment: 2-3 days of README spelunking<!-- .element: class="fragment" -->
- Abandoned systems were nearly impossible to recreate<!-- .element: class="fragment" -->

---

### Real world: The forgotten microservice

A microservice. Running on bare metal. On a very old Ubuntu.<!-- .element: class="fragment" -->

Need to update the LDAP extension.<!-- .element: class="fragment" -->
App refuses to run on anything newer.<!-- .element: class="fragment" -->
Must compile on the existing OS.<!-- .element: class="fragment" -->

---

### Real world: The forgotten microservice

No package mirror available for that Ubuntu version.<!-- .element: class="fragment" -->

Found one mirror. In Portugal.<!-- .element: class="fragment" -->
Download time estimate: **7 days**.<!-- .element: class="fragment" -->

It would have been faster to fly there with a hard drive.<!-- .element: class="fragment" -->

---

### Real world: The forgotten microservice

Solution:<!-- .element: class="fragment" -->
- Build a local mirror from what was on disk<!-- .element: class="fragment" -->
- Rebuild the extension inside an LXC container<!-- .element: class="fragment" -->
- Ship the container<!-- .element: class="fragment" -->

The app was eventually replaced.<!-- .element: class="fragment" -->

---

### Real world: Gerrit on bare metal

The entire organisation's source code. CI. Everything.<!-- .element: class="fragment" -->
On a single physical server. Struggling for a long time.<!-- .element: class="fragment" -->
Updates failing. Nobody wanted to touch it.<!-- .element: class="fragment" -->

---

### Real world: Gerrit on bare metal

Power outage in the server room.<!-- .element: class="fragment" -->
Server does not come back up.<!-- .element: class="fragment" -->

Need to recover data from the disks — but the hardware RAID is model-specific.<!-- .element: class="fragment" -->
Had to find a **donor machine with identical RAID hardware** just to read the disks.<!-- .element: class="fragment" -->

---

### Real world: Gerrit on bare metal

Data recovered. Migrated to an LXC container. Ran on a cluster.<!-- .element: class="fragment" -->

Eventually replaced with a self-hosted GitLab.<!-- .element: class="fragment" -->

**Lesson:** if you can't recreate it, you can't recover it.<!-- .element: class="fragment" -->

---

## Era: Docker <!-- .element: class="r-fit-text" -->

---

### The big shift

Application and environment packaged together — **the image is the deployment artifact.**<!-- .element: class="fragment" -->

```dockerfile
FROM php:8.3-fpm
COPY . /var/www/app
RUN composer install --no-dev
```

```yaml
# docker-compose.yml
services:
  nginx:
    image: nginx:alpine
  php:
    build: .
```

---

### Architecture

In practice: nginx + php-fpm bundled in **one container**, managed by supervisord

```
┌──────────────────────────────────┐
│          one container           │
│                                  │
│  ┌──────────┐   ┌─────────────┐  │
│  │  nginx   │   │   php-fpm   │  │
│  └──────────┘   └─────────────┘  │
│        supervisord               │
└──────────────────────────────────┘
```

---

### What got better

- Dev = prod — same image, same behaviour<!-- .element: class="fragment" -->
- Onboarding: `docker compose up` instead of 2-3 days<!-- .element: class="fragment" -->
- Rollback = pull previous image tag<!-- .element: class="fragment" -->
- Environment isolation per project<!-- .element: class="fragment" -->
- Abandoned system? Just keep the image.<!-- .element: class="fragment" -->

---

### The pain

- nginx + php-fpm in one container — need supervisord or s6 to manage both processes<!-- .element: class="fragment" -->
- Bundling everything together makes images large and complex<!-- .element: class="fragment" -->
- Heavy base images — `php:8.3-fpm` is ~490MB of Debian<!-- .element: class="fragment" -->
- Docker alone didn't solve clustering — Docker Swarm came later<!-- .element: class="fragment" -->
- Databases still often run on bare metal — scaling requires planning<!-- .element: class="fragment" -->
- New tooling to learn: Dockerfile, networking, volumes, compose...<!-- .element: class="fragment" -->

---

### 🙋 Quick question

Anyone running PHP production images **larger than 1GB**?

---

## Era: Kubernetes <!-- .element: class="r-fit-text" -->

---

### What Docker alone doesn't solve

Clustering & availability:

- Run across multiple nodes<!-- .element: class="fragment" -->
- Automatic failover — dead container? K8s restarts it<!-- .element: class="fragment" -->
- Self-healing, health checks built in<!-- .element: class="fragment" -->

---

### What Docker alone doesn't solve

Scaling & traffic:

- Horizontal Pod Autoscaler — scale on CPU/memory/custom metrics<!-- .element: class="fragment" -->
- Built-in load balancing between pods<!-- .element: class="fragment" -->
- Rolling deployments — zero downtime out of the box<!-- .element: class="fragment" -->
- Service discovery<!-- .element: class="fragment" -->

---

### What Docker alone doesn't solve

CI/CD & GitOps:

- ArgoCD / Flux watch your git repo<!-- .element: class="fragment" -->
- Push to git = deploy to cluster<!-- .element: class="fragment" -->
- Declarative config — cluster state is always in sync with repo<!-- .element: class="fragment" -->

---

### Architecture: PHP in Kubernetes

In K8s "one process per container" is the right way — nginx is back as a sidecar

```
┌─────────────────────────────────────┐
│                 Pod                 │
│  ┌──────────────┐ ┌──────────────┐  │
│  │    nginx     │ │   php-fpm    │  │
│  │   sidecar    │ │  container   │  │
│  └──────────────┘ └──────────────┘  │
└─────────────────────────────────────┘
```

---

### The pain

- Enormous complexity — needs a dedicated platform team<!-- .element: class="fragment" -->
- Three screens of YAML to deploy Hello World<!-- .element: class="fragment" -->
- The tooling ecosystem is itself complex: ArgoCD, Flux, Helm, Kustomize...<!-- .element: class="fragment" -->
- Networking, storage, ingress — each is its own rabbit hole<!-- .element: class="fragment" -->
- Overkill for small projects<!-- .element: class="fragment" -->
- You *can* throw more pods at a slow PHP app — but that's treating the symptom, not the cause<!-- .element: class="fragment" -->

---

### We solved deployment. But...

Our Docker images are still:

- **Heavy** — hundreds of megabytes of OS, libraries, tools<!-- .element: class="fragment" -->
- **Complex** — nginx, php-fpm, supervisord, dozens of system packages<!-- .element: class="fragment" -->
- **Vulnerable** — every dependency is a potential attack surface<!-- .element: class="fragment" -->

We containerised the mess. We didn't remove it.<!-- .element: class="fragment" -->

---

## Does it have to be this way? <!-- .element: class="r-fit-text" -->

---

### Node.js

```javascript
const http = require('http');
http.createServer((req, res) => {
  res.end('Hello World');
}).listen(3000);
```

```bash
node server.js
# Done. No nginx. No fpm.
```

Simpler architecture — but still ships with `node_modules` 📦<!-- .element: class="fragment" -->

---

### Go

```go
func main() {
    http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintln(w, "Hello World")
    })
    http.ListenAndServe(":3000", nil)
}
```

```bash
go build -o app
./app   # one binary, zero dependencies
```

No dependencies. Small image. Fast startup. Fast execution.<!-- .element: class="fragment" -->

---

### Why is PHP different?

PHP was designed as CGI — **request → process → die**<!-- .element: class="fragment" -->

nginx and php-fpm are a workaround for that limitation.<!-- .element: class="fragment" -->

**But does it have to stay that way?**<!-- .element: class="fragment" -->

---

## php -S <!-- .element: class="r-fit-text" -->

---

### PHP has a built-in web server

```bash
php -S 0.0.0.0:8080 public/index.php
```

It works. No nginx. No php-fpm.<!-- .element: class="fragment" -->

---

### But...

- Single-threaded — one request at a time<!-- .element: class="fragment" -->
- PHP docs: **"not designed to be used on a public network"**<!-- .element: class="fragment" -->
- Still the classic PHP model — state cleared after every request<!-- .element: class="fragment" -->
- Development only<!-- .element: class="fragment" -->

Architecturally simpler — but PHP still drags along a full runtime and its dependencies.<!-- .element: class="fragment" -->

A proof of concept. Not a solution.<!-- .element: class="fragment" -->

---

## ReactPHP / AmPHP <!-- .element: class="r-fit-text" -->

---

### Event loop in PHP

- Long-running PHP process — code loaded once, not per request<!-- .element: class="fragment" -->
- One process handles many concurrent connections<!-- .element: class="fragment" -->
- No nginx. No php-fpm.<!-- .element: class="fragment" -->
- Performance comparable to Node.js<!-- .element: class="fragment" -->

---

### The trade-off

- Callback hell / promise chains — async all the way down<!-- .element: class="fragment" -->
- Can't use blocking PHP functions — `file_get_contents`, standard PDO...<!-- .element: class="fragment" -->
- Requires rewriting the entire application<!-- .element: class="fragment" -->
- Steep learning curve for PHP developers<!-- .element: class="fragment" -->

- CPU-intensive operations block the entire event loop<!-- .element: class="fragment" -->
- Long-running processes — not all libraries and frameworks are designed for this<!-- .element: class="fragment" -->

A valid approach — but not ideal:<!-- .element: class="fragment" -->

- Still requires a full PHP runtime with all its dependencies<!-- .element: class="fragment" -->
- Not all workloads are a good fit<!-- .element: class="fragment" -->

---

## Workerman <!-- .element: class="r-fit-text" -->

---

### Multi-process, synchronous PHP

- Battle-tested in production — massively popular in China<!-- .element: class="fragment" -->
- Multi-process model — each worker is a separate PHP process<!-- .element: class="fragment" -->
- Synchronous code — write normal PHP, no async, no callbacks<!-- .element: class="fragment" -->
- **Webman** — HTTP framework built on top of Workerman<!-- .element: class="fragment" -->

---

### How it looks

```php
// Normal synchronous PHP — no async required
public function index(Request $request): Response
{
    $users = User::all();
    return response()->json($users);
}
```

```bash
php start.php start
# Workers: 4 processes
# Listening: http://0.0.0.0:8787
```

---

### What you gain

- No nginx. No php-fpm.<!-- .element: class="fragment" -->
- Application code loaded once per worker — not on every request<!-- .element: class="fragment" -->
- ~10x faster than traditional php-fpm<!-- .element: class="fragment" -->

---

### Limitations

- Existing libraries work without modifications — but watch out for memory leaks<!-- .element: class="fragment" -->
- Long-running processes need careful state management<!-- .element: class="fragment" -->
- Not all frameworks work out-of-the-box — Symfony requires adapters<!-- .element: class="fragment" -->
- Simple apps: use Webman directly<!-- .element: class="fragment" -->

---

### We have performance. We have simplicity.

No nginx. No php-fpm. Synchronous code. Fast.

Now let's talk about what we're still carrying around.<!-- .element: class="fragment" -->

---

## How much are we carrying? <!-- .element: class="r-fit-text" -->

---

### Image sizes

| Image | Size |
|---|---|
| `php:8.3` (Debian) | ~580 MB |
| `php:8.3-slim` | ~180 MB |
| `php:8.3-alpine` | ~50 MB |
| `FROM scratch` | ??? |

---

### What's hiding in there

- A full operating system<!-- .element: class="fragment" -->
- Package manager, shell, system tools<!-- .element: class="fragment" -->
- Every installed package is a potential attack vector<!-- .element: class="fragment" -->

We only need PHP. So why are we shipping an OS?<!-- .element: class="fragment" -->

---

### Do we really need all of this?

To run a PHP application, do we need:

- A full Linux distribution?<!-- .element: class="fragment" -->
- A shell?<!-- .element: class="fragment" -->
- A package manager?<!-- .element: class="fragment" -->
- System libraries we never call directly?<!-- .element: class="fragment" -->

Or do we just need... PHP?<!-- .element: class="fragment" -->

---

## Statically compiled PHP <!-- .element: class="r-fit-text" -->

---

### static-php-cli

Compile PHP with all extensions baked in — no shared libraries, no OS dependencies.

```bash
bin/spc build "bcmath,curl,openssl,pdo,pdo_mysql,zip" \
    --build-cli
```

Result: a single `php` binary that runs anywhere.<!-- .element: class="fragment" -->

---

### FROM scratch

```dockerfile
FROM scratch
COPY --from=builder /app/php /php
COPY --from=builder /app /app
ENTRYPOINT ["/php", "/app/start.php"]
```

- Zero OS<!-- .element: class="fragment" -->
- Zero shell<!-- .element: class="fragment" -->
- Zero attack surface<!-- .element: class="fragment" -->

---

### How small can we go?

| Image | Size |
|---|---|
| `php:8.3` (Debian) | ~580 MB |
| `php:8.3-slim` | ~180 MB |
| `php:8.3-alpine` | ~50 MB |
| `FROM scratch` | ~30 MB |

---

### What's in the image

Only two things:

- The `php` binary<!-- .element: class="fragment" -->
- Your application files<!-- .element: class="fragment" -->

Nothing else.<!-- .element: class="fragment" -->

---

### Security bonus

No shell means no shell exploits.<!-- .element: class="fragment" -->
No package manager means no supply chain attacks through the OS.<!-- .element: class="fragment" -->

But — you can still `docker exec` into the container and use the PHP binary to run arbitrary code.<!-- .element: class="fragment" -->
The attack surface is smaller, not zero.<!-- .element: class="fragment" -->

---

## 🐉 Here be dragons <!-- .element: class="r-fit-text" -->

---

### What this means in practice

- Works in my environment — your mileage may vary<!-- .element: class="fragment" -->
- Not battle-tested in large production projects<!-- .element: class="fragment" -->
- Hard to integrate into standard CI/CD pipelines<!-- .element: class="fragment" -->
- Limited community support and documentation<!-- .element: class="fragment" -->
- Use with care. Not a drop-in for production tomorrow.<!-- .element: class="fragment" -->
