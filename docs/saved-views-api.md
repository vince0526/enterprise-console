# Saved Views API

Lightweight JSON API for persisting and managing user saved views (filter presets) used by the Core Databases registry UI.

Base path: `/emc/core/saved-views`

Authentication: standard Laravel session auth. Authorization: ownership-based via `SavedViewPolicy`.

Rate limiting: configured via route throttling group (defaults to `60,1`).

## Endpoints

- GET `/emc/core/saved-views`
  - Query params:
    - `context` (string, optional, default: `core_databases`)
    - `q` (string, optional): search by name (LIKE)
    - `limit` (int, optional): page size. Default and max are enforced via config `emc.saved_views.default_limit` and `emc.saved_views.limit_cap`.
    - `offset` (int, optional): zero-based offset
  - Success 200 JSON: array of objects `{ id, name, filters }`
  - Headers:
    - `X-SavedViews-Total`: total rows
    - `X-SavedViews-Limit`: effective limit
    - `X-SavedViews-Returned`: number in this page
    - `Link`: RFC 5988 prev/next, e.g.: `<...&offset=10&limit=10>; rel="next", <...&offset=0&limit=10>; rel="prev"`

- POST `/emc/core/saved-views`
  - Body (JSON): `{ name: string, context?: string = 'core_databases', filters: object }`
  - Upserts by `(user_id, context, name)`
  - Success 201 JSON: full SavedView
  - Errors: 422 on validation failures

- PATCH `/emc/core/saved-views/{id}`
  - Body (JSON): `{ name?: string, context?: string, filters?: object }`
  - Allows renaming, context change (validated), or filters update
  - Conflict: returns 422 with `{ errors: { name: ['duplicate'] } }` if renaming to an existing name in the same context
  - Success 200 JSON: full SavedView

- POST `/emc/core/saved-views/{id}/duplicate`
  - Body (JSON): `{ name: string, context?: string }`
  - Duplicates an existing view the user owns
  - Conflict: 422 duplicate name
  - Success 201 JSON: new SavedView

- DELETE `/emc/core/saved-views/{id}`
  - Deletes a view the user owns
  - Success 200 JSON: `{ status: 'deleted' }`

## Error Codes

- 401 Unauthorized: not logged in
- 403 Forbidden: attempting to modify a view you don't own
- 422 Unprocessable Entity: validation errors (duplicate name, invalid inputs)
- 429 Too Many Requests: throttled by rate limit

## Examples

List first page (default limit):

```http
GET /emc/core/saved-views HTTP/1.1
Accept: application/json
```

Create or update existing by name:

```http
POST /emc/core/saved-views HTTP/1.1
Content-Type: application/json
X-CSRF-TOKEN: <token>

{
  "name": "Prod PostgreSQL",
  "context": "core_databases",
  "filters": { "env": "Prod", "engine": "PostgreSQL" }
}
```

Rename (handle duplicate):

```http
PATCH /emc/core/saved-views/123 HTTP/1.1
Content-Type: application/json
X-CSRF-TOKEN: <token>

{ "name": "Prod Postgres (primary)" }
```

Duplicate:

```http
POST /emc/core/saved-views/123/duplicate HTTP/1.1
Content-Type: application/json
X-CSRF-TOKEN: <token>

{ "name": "Prod Postgres (copy)" }
```

Delete:

```http
DELETE /emc/core/saved-views/123 HTTP/1.1
X-CSRF-TOKEN: <token>
```

## Config

`config/emc.php`

```php
return [
  'saved_views' => [
    'default_limit' => 50,
    'limit_cap' => 100,
  ],
];
```
