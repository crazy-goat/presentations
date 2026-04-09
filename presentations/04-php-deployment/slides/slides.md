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

- FileZilla open on production
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

### The pain

- No versioning — "I overwrote prod"<!-- .element: class="fragment" -->
- No rollback<!-- .element: class="fragment" -->
- Binary vs ASCII transfer mode — corrupted files, broken encodings<!-- .element: class="fragment" -->
- File permissions — chmod everything via FTP client<!-- .element: class="fragment" -->
- Symlinks — FTP doesn't support them<!-- .element: class="fragment" -->
- "Works on my machine"<!-- .element: class="fragment" -->
