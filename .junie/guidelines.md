Project: Home Economics — Advanced Dev Guidelines

Audience: Experienced Laravel 11 + Inertia/React developers working on this codebase. This document captures only repo-specific knowledge, gotchas, and working practices.

1) Build and Configuration (Backend: Laravel 11)
- Versions in use
  - PHP: ^8.2 (composer.json)
  - Laravel: ^11.0 (composer.json)
  - Inertia (server): inertiajs/inertia-laravel ^2.0
  - Frontend: React 18, Vite 4, @inertiajs/react ^2.0, Ziggy ^1.0
- Environment/bootstrap
  - Copy .env.example to .env and set the DB, APP_KEY, and other secrets.
  - Generate key: php artisan key:generate
  - Migrate/seed as needed: php artisan migrate [--seed]
- Running locally
  - Native PHP: php artisan serve (ensure MySQL reachable per .env)
  - With Sail (docker-compose.yml present)
    - Prereq: Docker running.
    - Start stack: ./vendor/bin/sail up -d
    - First run will build image defined at vendor/laravel/sail/runtimes/8.4
    - Services: MySQL 8, Redis, Meilisearch, Mailhog, Selenium (Chrome). Ports forwarded by env vars.
  - Queue in dev: QUEUE_CONNECTION=sync (default in phpunit.xml for tests).

2) Build and Configuration (Frontend: React/Inertia)
- Install deps
  - npm ci or npm install (package.json uses Vite ^4, React ^18)
- Dev server
  - npm run dev (Vite) — Vite port comes from VITE_PORT env (docker-compose exposes it)
  - Ziggy is configured; ensure Ziggy route manifest is available to React where needed.
- Production build
  - npm run build (outputs per Vite config). Ensure laravel-vite-plugin ^0.7.5 integration remains intact.

3) Testing (PHPUnit)
- Framework
  - phpunit.xml defines two suites (Unit, Feature) with testing env overrides (array cache, array mail, sync queue). No DB connection required by default; enable DB only if a test needs it.
- Running tests
  - Native: vendor/bin/phpunit or vendor\bin\phpunit (Windows PowerShell)
  - Sail: ./vendor/bin/sail test or ./vendor/bin/sail phpunit
  - Run a suite: vendor/bin/phpunit --testsuite Unit
- Adding tests
  - Unit tests under tests/Unit; aim for pure-PHP tests without DB.
  - Feature tests under tests/Feature; if hitting HTTP kernel, use Tests\TestCase (Laravel) and not PHPUnit\Framework\TestCase directly.
  - If a test needs DB: use RefreshDatabase and point .env.testing to a disposable DB (Sail auto-creates testing DB via create-testing-database.sh).
- Example minimal test (Unit) — verified pattern
  - Create file: tests/Unit/SmokeTest.php
    - Contents:
      <?php
      namespace Tests\Unit;
      use PHPUnit\Framework\TestCase;
      class SmokeTest extends TestCase {
          public function test_phpunit_is_working(): void {
              $this->assertTrue(true);
          }
      }
  - Run: vendor/bin/phpunit --testsuite Unit
  - Remove: delete the file when done to keep the repo minimal.

4) Codebase Structure and Conventions
- Namespaces (composer.json autoload)
  - App\ → app/
  - Core\ → src/Core/ (custom domain/core code; prefer placing cross-cutting concerns here)
  - Database\Factories\, Database\Seeders\ as usual
- Backend patterns (Laravel)
  - Eloquent model layering: e.g., App\Models\Transaction extends App\Models\ModelBase\TransactionBase. Keep base/domain rules in ModelBase, with project-specific queries in leaf class.
  - Query-building methods return Illuminate\Database\Eloquent\Builder for composability. Example in Transaction:
    - getReceipts(), getFixedExpenses(), getVariableExpenses(), getPeople(), getTaxes(), getTransfers() produce typed Builder instances.
    - applyFilters(Builder $query, string $dateFilter, string $accountFilter, string $paidFilter): centralizes date/account/paid filters; reuse this when adding new categories to guarantee consistent filtering.
    - getMonthTotals($dateFilter): computes aggregate totals; ensure updates across all categories when introducing new transaction types.
    - getFullBalance($dateFilter, $accountFilter, $paidFilter, $backwardsBalance): builds a unioned/categorized list and derives balances; maintain consistent sign conventions (receipts positive, expenses positive as magnitudes; transfer handling is special-cased).
  - When adding new transaction categories/types:
    - Provide a getXxx(): Builder method.
    - Integrate getXxx into getAmounts(), getMonthTotals(), and any aggregates/exports.
    - Respect applyFilters so that UI filters work consistently.
  - DB access
    - Use Query Builder/Eloquent. Transaction methods already leverage where('transaction_type', '...'). Align new logic with existing ENUM/flag values.
  - Auth-aware queries
    - Where relevant, scope by Auth::id() to avoid leaking cross-user data. Review existing queries and add constraints if a new context requires it.
- Frontend patterns (React/Inertia)
  - Inertia pages receive server props; use @inertiajs/react ^2 components.
  - Components under resources/js (e.g., Components/TableListTransactions.jsx) consume categorized transaction arrays; keep prop contracts in sync with backend changes to categories/fields.
  - Vite imports are ESM (type: module). Prefer aliasing via jsconfig.json if paths grow.
  - Use Ziggy for route generation from Laravel routes to avoid hardcoded paths.

5) Styling, Quality, and Tooling
- Code style: Use Laravel Pint (composer dev) — run vendor/bin/pint to auto-fix. Adopt PSR-12 with Laravel presets.
- Static analysis: Not configured here; if introduced, recommend Larastan (phpstan.neon) with level ≥ 6 for models and builders.
- Frontend linting: Not currently present; if adding ESLint/Prettier, align with React 18 and Vite 4 configs.

6) Debugging and Local Dev Tips
- Date/amount semantics in Transaction
  - Percentage calculations in getMonthTotals default to 100% when totals are zero to avoid division by zero. Preserve this behavior if refactoring.
  - Transfers (TRANS) are filtered but not included in EXPEN totals; ensure transfers don’t inflate expense aggregates.
- Performance
  - ApplyFilters composes constraints; avoid duplicating filters. Add database indexes aligned with date, user_id, and category filters if query volume grows.
- Migrations
  - Keep MySQL 8 compatibility (Sail uses mysql/mysql-server:8.0). Use utf8mb4 and appropriate column sizes for indexes.

7) Version-specific Notes
- Laravel 11 dropped some older helpers; leverage modern Http and Routing APIs.
- Inertia Laravel ^2.0 pairs with @inertiajs/react ^2.x. Use the new shared data APIs and SSR if introduced later.
- Vite 4 + laravel-vite-plugin 0.7.x: ensure you include @vite(['resources/js/app.jsx', 'resources/css/app.css']) in Blade layout when wiring new entrypoints.

8) Performance guidelines (repo-specific)
- Use Inertia lazy props for heavy server-side computations
  - Wrap props like items, monthTotals, balances, and large option sets in closures in controllers (see TransactionController::index). This ensures router.reload({ only: [...] }) only recomputes requested props.
- Avoid in-memory filtering on collections
  - Do NOT use Model::all()->where(...). Always query the database directly: Model::query()->where(...)->get(). Updated in: CrudController::getDefaultOptionsModel and TransactionController combo* helpers.
- Minimize repeated aggregate computations
  - If you need per-account balances with the same date filter, memoize within the request to avoid recomputing Transaction::getFullBalance for each option (see comboAccounts implementation).
- Keep payloads small
  - Prefer lightweight option structures (id, name, label) and format heavy derived fields (like balances) only when required by the UI filter panel.
- Frontend partial reloads
  - Continue using router.reload with only: [...] to update just the necessary props after actions (toggle paid, create/update).

9) How to verify your setup (quick checklist)
- Backend
  - composer install
  - cp .env.example .env; set DB and APP_URL
  - php artisan key:generate
  - php artisan migrate
  - vendor/bin/phpunit (ensure Unit and Feature suites are discovered)
- Frontend
  - npm ci
  - npm run dev (confirm Vite and Inertia hot reload)
- Performance smoke checks
  - Open Transactions page; capture server response times before/after. Toggling PAID or saving a form should trigger partial reloads that only recompute requested props (verify via network tab: smaller JSON, faster timings).

Appendix: Temporary Test Flow Used Internally
- We validate that the PHPUnit wiring works with a trivial Unit test (see example above). After confirming, delete tests/Unit/SmokeTest.php to keep the repo clean.

This document will evolve; prefer adding repo-specific gotchas and patterns rather than generic Laravel/React information.
