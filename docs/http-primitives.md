# Request, Response, Cookies, and Session

## Request (`Core\Http\Request`)

### Core methods

- `method(): string`
- `uri(): string`
- `query(?string $key = null, mixed $default = null): mixed`
- `input(?string $key = null, mixed $default = null): mixed`
- `header(?string $key = null, mixed $default = null): mixed`
- `json(): array`
- `all(): array`
- `jsonError(): ?string`

### Notes

- `uri()` strips query string via `strtok($uri, '?')`
- headers are normalized to lowercase, kebab-case names
- `json()` parses only if `Content-Type` contains `application/json`
- invalid JSON returns `[]` and records parser message in `jsonError()`
- `all()` merges `query + input + json`

## Response (`Core\Http\Response`)

### Core methods

- `setStatus(int $code): static`
- `setHeader(string $key, string $value): static`
- `write(string $content): static`
- `json(array|object $data, int $status = 200): static`
- `redirect(string $to, int $status = 302): static`
- `send(): void`

### Behavior

- response is mutable and chainable
- `json()` sets UTF-8 JSON content type and overwrites body
- `redirect()` sets status + `Location` header
- `send()` emits status, headers, then content

## Cookie (`Core\Http\Cookie`)

### Methods

- `set(string $name, string $value, int $minutes = 60): void`
- `get(string $name, mixed $default = null): mixed`
- `delete(string $name): void`

### Security defaults

If cookie config keys are not explicitly set, secure defaults are derived from:

- `APP_ENV=production`
- `APP_URL` scheme (`https://`)

Default effective behavior:

- `secure`: true in production or https URL
- `httponly`: true
- `samesite`: `Lax`
- `path`: `/`

## Session (`Core\Session\Session`)

### Methods

- `start()`
- `set(string $key, mixed $value)`
- `get(string $key, mixed $default = null)`
- `has(string $key): bool`
- `remove(string $key): void`
- `all(): array`
- `destroy(): void`

### Behavior

- session starts lazily on first operation
- cookie params are configured similarly to `Cookie::set`
- `destroy()` clears state and resets internal started flag

## Flash (`Core\Session\Flash`)

One-request session messages backed by `$_SESSION['_flash']`.

### Methods

- `set(string $key, mixed $value)`
- `get(string $key, mixed $default = null)` (consumes key)
- `has(string $key): bool`
- `all(): array` (consumes all flash data)

Example:

```php
Flash::set('success', 'Profile updated');
return (new Response())->redirect('/profile');

// next request
$message = Flash::get('success');
```
