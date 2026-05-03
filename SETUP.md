# Manager App — Local Setup Guide

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (must be running)
- [PHP 8.2+](https://www.php.net/downloads) + [Composer](https://getcomposer.org/download/)
- [Git](https://git-scm.com/)

---

## Step 1 — Create the Laravel Project

```bash
composer create-project laravel/laravel manager
cd manager
```

---

## Step 2 — Install Sail

Sail is already in `composer.json` as a dev dependency. Just run:

```bash
php artisan sail:install
```

When prompted, select **mysql** (press space to select, enter to confirm).

This generates a `docker-compose.yml` in the project root — no manual Docker config needed.

---

## Step 3 — Start the Environment

```bash
./vendor/bin/sail up -d
```

First run pulls Docker images, so it takes a few minutes.

Visit **http://localhost** — you should see the Laravel welcome page.

---

## Step 4 — Add a Sail Alias (optional but recommended)

Add this to your `~/.zshrc` so you can type `sail` instead of `./vendor/bin/sail`:

```bash
alias sail='./vendor/bin/sail'
```

Then reload: `source ~/.zshrc`

---

## Step 5 — Run Migrations

```bash
./vendor/bin/sail artisan migrate
```

---

## Step 6 — Initialise Git

```bash
git init
git add .
git commit -m "Initial Laravel + Sail setup"
```

Create a repository on GitHub named `manager`, then:

```bash
git remote add origin https://github.com/YOUR_USERNAME/manager.git
git branch -M main
git push -u origin main
```

---

## Common Sail Commands

| Task | Command |
|---|---|
| Start | `sail up -d` |
| Stop | `sail down` |
| View logs | `sail logs -f` |
| Run artisan | `sail artisan <command>` |
| Run composer | `sail composer <command>` |
| Open MySQL shell | `sail mysql` |
| Run tests | `sail test` |

---

## Next Steps (after local setup is working)

- Set up GitHub Actions for CI/CD
- Configure Hostinger hosting for `manager.ameer.website`
- Set up auto-deploy from the `main` branch
</thinking>
