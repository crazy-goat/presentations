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
