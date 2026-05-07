# Save Expiry API Documentation

Last updated: 2026-05-07

## Overview

This document covers the save/purchase API behavior after introducing `expire_date` in `saves`.

- Each paid save grants **6 months** access.
- Re-purchasing the same series extends access by **another 6 months**.
- Access checks now require active (non-expired) save records.

## Database requirement

`saves` table must include:

- `id`
- `user_id`
- `series_id`
- `date`
- `expire_date` (`DATETIME`, required)

Migration SQL:

```sql
ALTER TABLE saves
ADD COLUMN expire_date DATETIME NULL AFTER date;

UPDATE saves
SET expire_date = DATE_ADD(date, INTERVAL 6 MONTH)
WHERE expire_date IS NULL;

ALTER TABLE saves
MODIFY COLUMN expire_date DATETIME NOT NULL;
```

## Authentication

Protected endpoints use JWT in the request header:

- `Authorization: <token>`

If token validation fails, API returns authorization failure.

---

## 1) Purchase Series

**Endpoint**: `POST /api/series/purchase.php`  
**Auth**: Required (JWT)

### Request body

- `series_id` (required)

> `user_id` is taken from JWT server-side and should not be trusted from client payload.

### Behavior

1. Validate token.
2. Validate request method is `POST`.
3. Check user points against series point cost.
4. Deduct points.
5. Save logic:
   - No existing row for `(user_id, series_id)`:
     - insert new row
     - `expire_date = NOW() + INTERVAL 6 MONTH`
   - Existing row:
     - `expire_date = DATE_ADD(IF(expire_date > NOW(), expire_date, NOW()), INTERVAL 6 MONTH)`
     - update `date = NOW()`
6. Increment `series.save`.

### Success response

```json
{
  "status": "success",
  "msg": "Purchase success"
}
```

### Failure response (examples)

```json
{
  "status": "fail",
  "msg": "Authorization Fail"
}
```

```json
{
  "status": "fail",
  "msg": "Purchase Error"
}
```

```json
{
  "status": "fail",
  "msg": "Method not allow"
}
```

---

## 2) My Purchased Series

**Endpoint**: `GET /api/series/my_series.php`  
**Auth**: Required (JWT)

### Behavior

- Returns only active saves (`saves.expire_date >= NOW()`).
- Includes both raw and formatted expiry fields per series item.

### Response shape

```json
{
  "total_series": 2,
  "series": [
    {
      "id": 15,
      "title": "Example",
      "point": 120,
      "expire_date": "2026-11-07 21:20:00",
      "expire_date_readable": "07 Nov 2026"
    }
  ]
}
```

---

## 3) Access check endpoints impacted by expiry

These endpoints use `isSaved()` / save lookups that now require active expiry:

- `GET /api/series/detail.php`
  - `saved` is true only when `expire_date >= NOW()`.
- `GET /api/chapters/get-content.php`
  - access allowed for paid content only with active save (or if free/inactive chapter rules apply).
- `GET /api/chapters/download.php`
  - download for protected chapter requires active save (`expire_date >= NOW()`).

---

## Business rules summary

- First purchase of series: +6 months from now.
- Re-purchase before expiry: +6 months from current `expire_date`.
- Re-purchase after expiry: +6 months from now.
- `my_series` contains only currently active purchases.

## Notes

- If you want guaranteed one row per `(user_id, series_id)`, add a unique constraint and clean duplicates before applying it.
- Time calculations use database server time (`NOW()`).
