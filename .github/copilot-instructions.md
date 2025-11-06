## Quick context

- This is a small PHP-based REST-style backend (no framework). Endpoints live under `api/` grouped by domain (e.g. `api/services/`, `api/user/`, `api/orders/`).
- Database access is done with PDO in `config/database.php`. Models are plain PHP classes in `models/` (e.g. `models/Service.php`, `models/Order.php`, `models/User.php`).

## Big picture (what an AI should know)

- Routing: there is no router framework — each endpoint is a standalone PHP script (file-per-route). Scripts include `config/database.php` and a model, then read input from `php://input` (JSON) or `$_GET` for query params.
- Data flow: request JSON -> endpoint script (in `api/...`) -> model method (in `models/*.php`) -> PDO -> JSON response. Many endpoints return either `{"data": [...]}` or `{"success":..., "message":...}`.
- Authentication: none built-in. `api/user/login.php` verifies password with `password_verify()` and returns user info (no JWT/session). Caller is expected to handle authorization/identity via fields like `provider_id` in requests.

## Important project conventions

- JSON I/O: endpoints use `json_decode(file_get_contents('php://input'))` for request bodies and `json_encode(...)` for responses. Use `http_response_code()` consistently.
- Responses: list endpoints typically return an object with a `data` array (see `api/services/get_services.php`). Create/update endpoints return `success`/`message` keys and appropriate HTTP status codes (201, 200, 400, 503, 404).
- Images: service images are stored as JSON-encoded strings in the `images` DB column and decoded in responses (see `models/Service.php` and `api/services/*`). When creating/updating, accept either an array or a JSON string.
- DB practices: models use prepared statements with named parameters (e.g. `:provider_id`). Keep this pattern to avoid SQL injection and to match existing style.
- Soft deletes: `Service::delete()` sets `is_active = 0` rather than removing rows. Many queries filter `is_active = 1` for visible services.

## Files to reference when editing or extending

- Request/response examples: `api/services/get_services.php`, `api/services/get_service_detail.php`, `api/orders/create_order.php`
- Model implementations: `models/Service.php`, `models/Order.php`, `models/User.php`
- DB connection and headers: `config/database.php` (note it sets Content-Type and CORS headers and handles OPTIONS preflight)

## Concrete examples (copy-paste-friendly)

- Create service (POST JSON to `api/services/create_service.php`): required fields: `title`, `description`, `price`, `category_id`, `provider_id`. `images` can be an array or a JSON string.
- Get services (GET `api/services/get_services.php`): returns `{"data":[{...}]}`; `images` will be an array in the JSON response.
- Create order (POST JSON to `api/orders/create_order.php`): required `customer_id`, `service_id`, `total_price`. Returns `order_id` on success.

## Developer workflows and debugging tips

- Local run: drop this folder into XAMPP `htdocs` (already the case). Start Apache + MySQL from XAMPP. DB config is in `config/database.php` (default DB name `jasaku_db`, user `root`, empty password).
- Quick manual test: use curl or Postman against `http://localhost/jasakuapp/api/services/get_services.php` or `http://localhost/jasakuapp/api/user/login.php`.
- Common debug signals: many endpoints return 400 for missing fields and 503 for DB failures. Check web server error logs and enable PDO exceptions (already enabled in `config/database.php`).

## Guidance for AI edits (do this to stay consistent)

- Preserve `config/database.php` headers (CORS/Content-Type/OPTIONS handling) when moving code.
- Keep file-per-endpoint pattern and relative includes like `include_once __DIR__ . '/../../models/Service.php'` so paths resolve correctly.
- Use PDO prepared statements and named params — follow the exact binding style used in `models/*.php`.
- When adding responses, follow existing shapes: either `{"data": [...]}` for list routes or `{"success":bool, "message":string}` for mutation routes.

## Things not present (be conservative)

- No centralized auth, no middleware, no routing framework. Do not add framework-specific assumptions. If adding auth, document required changes to all endpoints.

---
If anything here looks wrong or you want more examples (e.g. exact curl examples for each endpoint, DB schema notes, or tests), tell me which part to expand and I'll update the file.
