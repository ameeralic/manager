# Expense Manager — Implementation Plan

## Overview

A full expense tracking module for the Home Manager app. Users can log expenses with a category, tags, who paid, payment method, and amount. All core entities (expenses, categories, tags, payment methods) have their own CRUD screens, accessible from a sidebar under an "Expense Manager" parent group.

---

## 1. Database Schema

### `expense_categories`
| Column     | Type         | Notes                  |
|------------|--------------|------------------------|
| id         | bigIncrements |                        |
| name       | string       | unique                 |
| color      | string(7)    | nullable, hex color    |
| timestamps |              |                        |

### `expense_tags`
| Column     | Type         | Notes                  |
|------------|--------------|------------------------|
| id         | bigIncrements |                        |
| name       | string       | unique                 |
| timestamps |              |                        |

### `expense_category_expense_tag` _(pivot)_
| Column              | Type    | Notes                           |
|---------------------|---------|---------------------------------|
| expense_category_id | bigint  | FK → expense_categories.id      |
| expense_tag_id      | bigint  | FK → expense_tags.id            |

Many-to-many: a category can have suggested tags, a tag can belong to many categories.

### `payment_methods`
| Column     | Type    | Notes                          |
|------------|---------|--------------------------------|
| id         | bigIncrements |                          |
| name       | string  | e.g. "PhonePe", "HDFC CC"     |
| timestamps |         |                                |

Default seed data: Cash, PhonePe, GooglePay, HDFC CC, SBI CC (Ameer), SBI CC (Jinsi), ICICI CC, FederalBank CC, InduslandBank CC.

### `expenses`
| Column              | Type           | Notes                             |
|---------------------|----------------|-----------------------------------|
| id                  | bigIncrements  |                                   |
| title               | string         | nullable                          |
| expense_category_id | bigint         | FK → expense_categories.id        |
| payment_method_id   | bigint         | FK → payment_methods.id           |
| paid_by             | enum           | 'Ameer', 'Jinsi'                  |
| amount              | decimal(10,2)  | in Indian Rupees                  |
| timestamps          |                |                                   |
| deleted_at          | timestamp      | nullable, soft delete             |

### `expense_expense_tag` _(pivot)_
| Column         | Type   | Notes                   |
|----------------|--------|-------------------------|
| expense_id     | bigint | FK → expenses.id        |
| expense_tag_id | bigint | FK → expense_tags.id    |

Many-to-many: each expense can have multiple tags.

---

## 2. Models & Relationships

### `ExpenseCategory`
- `hasMany` Expense
- `belongsToMany` ExpenseTag (via `expense_category_expense_tag`)
- `$fillable`: name, color

### `ExpenseTag`
- `belongsToMany` ExpenseCategory (via `expense_category_expense_tag`)
- `belongsToMany` Expense (via `expense_expense_tag`)
- `$fillable`: name

### `PaymentMethod`
- `hasMany` Expense
- `$fillable`: name

### `Expense`
- `belongsTo` ExpenseCategory
- `belongsTo` PaymentMethod
- `belongsToMany` ExpenseTag (via `expense_expense_tag`)
- `$fillable`: title, expense_category_id, payment_method_id, paid_by, amount
- `$casts`: amount → decimal:2, paid_by → string

---

## 3. Migrations (in order)

1. `create_expense_categories_table`
2. `create_expense_tags_table`
3. `create_expense_category_expense_tag_table` _(pivot)_
4. `create_payment_methods_table`
5. `create_expenses_table`
6. `create_expense_expense_tag_table` _(pivot)_

---

## 4. Seeders

- `PaymentMethodSeeder` — seeds the 9 default payment methods
- `ExpenseCategorySeeder` — seeds default categories: Food, Travel, Petrol, Rent, Water Bill, Electricity Bill, Groceries
- `ExpenseTagSeeder` — seeds default tags: Amazon, Flipkart, Swiggy, Zomato, Instamart, Zepto, Shop, Zudio, Trends
- All called from `DatabaseSeeder`

---

## 5. Controllers (Resource Controllers)

| Controller                   | Route Prefix           |
|------------------------------|------------------------|
| `ExpenseController`          | `/expenses`            |
| `ExpenseCategoryController`  | `/expense-categories`  |
| `ExpenseTagController`       | `/expense-tags`        |
| `PaymentMethodController`    | `/payment-methods`     |

Each resource controller exposes: `index`, `create`, `store`, `edit`, `update`, `destroy`.  
The `ExpenseController` also passes categories, tags, and payment methods to `create`/`edit` views.

---

## 6. Routes

All routes under `auth` middleware, grouped under a prefix `/expense-manager` conceptually but using standard resource routes:

```php
Route::resource('expenses', ExpenseController::class);
Route::resource('expense-categories', ExpenseCategoryController::class);
Route::resource('expense-tags', ExpenseTagController::class);
Route::resource('payment-methods', PaymentMethodController::class);

// Inline category creation (AJAX) from the expense form
Route::post('expense-categories/quick-create', [ExpenseCategoryController::class, 'quickCreate'])
    ->name('expense-categories.quick-create');
```

---

## 7. Vue Pages (Inertia)

```
resources/js/Pages/
  Expenses/
    Index.vue       — table of all expenses, with filters by category/tag/paid_by
    Create.vue      — expense submission form
    Edit.vue        — edit expense form (reuses form component)
  ExpenseCategories/
    Index.vue       — list + inline create/edit/delete
  ExpenseTags/
    Index.vue       — list + inline create/edit/delete
  PaymentMethods/
    Index.vue       — list + inline create/edit/delete
```

### Expense Form Fields (Create/Edit)
1. **Title** — text input, optional
2. **Category** — searchable dropdown with "Create new…" option that opens a quick-create modal/inline field
3. **Tags** — multi-select pill input (tags from the selected category are highlighted/suggested first, then all tags)
4. **Paid By** — radio or select: Ameer / Jinsi
5. **Payment Method** — select dropdown from `payment_methods` table
6. **Amount** — numeric input (₹), decimal allowed
7. **Submit** button

---

## 8. Layout — Adding the Sidebar

`AppLayout.vue` currently has a top bar + full-width `<main>`. It needs a sidebar.

### New layout structure:
```
┌─────────────────────────────────────────────┐
│  Top Bar (sticky, full-width)               │
├────────────┬────────────────────────────────┤
│  Sidebar   │  <slot /> (main content)       │
│  (fixed w) │                                │
│            │                                │
└────────────┴────────────────────────────────┘
```

### Sidebar nav items:
```
● Dashboard

▼ Expense Manager
  ├── Expenses
  ├── Categories
  ├── Tags
  └── Payment Methods
```

- Sidebar width: `w-56` (224px), collapsible on mobile
- Active link highlighted
- The "Expense Manager" group is collapsible (open by default when on any expense route)
- Sidebar uses `<Link>` from `@inertiajs/vue3` for SPA navigation

---

## 9. CRUD Behaviour for Reference Tables

For **Categories**, **Tags**, and **Payment Methods**:
- `Index.vue` shows a table/list with name and action buttons (Edit, Delete)
- Inline editing (click to edit row in place) or a small modal — to be decided at implementation time based on what's simpler
- Deletion is guarded: if a category/tag/payment method is in use by an expense, show an error rather than cascade-deleting

---

## 10. Implementation Sequence

The recommended order to build and merge features (one Linear issue per step):

| # | Scope | Description |
|---|-------|-------------|
| 1 | Layout | Add sidebar to `AppLayout.vue` with nav groups (Dashboard + Expense Manager placeholder links) |
| 2 | DB + Models | All 6 migrations, 4 models, factories, seeders |
| 3 | Payment Methods CRUD | Controller + Index.vue |
| 4 | Categories CRUD | Controller + Index.vue |
| 5 | Tags CRUD | Controller + Index.vue |
| 6 | Expenses — Create | Controller store action + Create.vue form |
| 7 | Expenses — Index | Index.vue with list, filters, delete |
| 8 | Expenses — Edit | Edit.vue + update action |

---

## 11. Decisions

1. **Sidebar — mobile** — On mobile, the sidebar is replaced by a sticky bottom navigation bar (tab bar style). It shows icon-only tabs: Dashboard, Expense Manager (receipt icon). Tapping "Expense Manager" navigates to the Expenses index. A floating **+** (FAB) button in the bottom-right corner opens the Add Expense form. On desktop the left sidebar remains as designed.

2. **Quick-create category** — An inline input appears directly below the category dropdown when the user types a name that doesn't match any existing category and clicks "Create…". No modal.

3. **Tag filtering by category** — Selecting a category auto-filters the tag multi-select to show only tags associated with that category. If no tags are linked to that category, all tags are shown as a fallback.

4. **Deletion behaviour** — Expenses are **soft-deleted** (`deleted_at` column via `SoftDeletes` trait). Categories, tags, and payment methods are hard-deleted but guarded against deletion if they are referenced by any expense.

5. **Expense list filters** — All filters exposed: date range, category, tag(s), paid_by, payment method. Filters are applied via query string so they are shareable/bookmarkable.

6. **Amount formatting** — Indian number system with ₹ symbol (e.g., ₹1,23,456.00). A shared Vue composable `useIndianCurrency(amount)` will handle formatting across all views.

---

## 12. Files to Create/Modify Summary

### New PHP files
- `database/migrations/*` (6 migrations)
- `database/seeders/PaymentMethodSeeder.php`
- `database/seeders/ExpenseCategorySeeder.php`
- `database/seeders/ExpenseTagSeeder.php`
- `app/Models/Expense.php`
- `app/Models/ExpenseCategory.php`
- `app/Models/ExpenseTag.php`
- `app/Models/PaymentMethod.php`
- `app/Http/Controllers/ExpenseController.php`
- `app/Http/Controllers/ExpenseCategoryController.php`
- `app/Http/Controllers/ExpenseTagController.php`
- `app/Http/Controllers/PaymentMethodController.php`

### Modified PHP files
- `routes/web.php` — add resource routes
- `database/seeders/DatabaseSeeder.php` — call new seeders

### New Vue files
- `resources/js/Pages/Expenses/Index.vue`
- `resources/js/Pages/Expenses/Create.vue`
- `resources/js/Pages/Expenses/Edit.vue`
- `resources/js/Pages/ExpenseCategories/Index.vue`
- `resources/js/Pages/ExpenseTags/Index.vue`
- `resources/js/Pages/PaymentMethods/Index.vue`

### New Vue composables
- `resources/js/composables/useIndianCurrency.js` — formats numbers as ₹1,23,456.00

### Modified Vue files
- `resources/js/Layouts/AppLayout.vue` — add sidebar (desktop) + sticky bottom nav (mobile)
