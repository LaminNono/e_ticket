# SQL Tips for E-ticket Project

## Common Queries

- **Select all users:**
  ```sql
  SELECT * FROM users;
  ```
- **Select bookings for a user:**
  ```sql
  SELECT * FROM bookings WHERE user_id = ?;
  ```
- **Join bookings with users and buses:**
  ```sql
  SELECT b.*, u.name, bu.bus_name
  FROM bookings b
  JOIN users u ON b.user_id = u.user_id
  JOIN buses bu ON b.bus_id = bu.bus_id;
  ```

## Useful Joins

- **Get all bookings with route info:**
  ```sql
  SELECT b.*, r.origin, r.destination
  FROM bookings b
  JOIN routes r ON b.route_id = r.route_id;
  ```
- **Get all payments with booking and user info:**
  ```sql
  SELECT p.*, b.*, u.name
  FROM payments p
  JOIN bookings b ON p.booking_id = b.booking_id
  JOIN users u ON b.user_id = u.user_id;
  ```

## Security Tips

- Always use prepared statements to prevent SQL injection.
- Never trust user input; validate and sanitize all data.
- Limit data exposure by selecting only needed columns.
- Use transactions for multi-step operations (e.g., booking + payment).

## Performance Tips

- Use indexes on columns that are frequently searched or joined (e.g., user_id, booking_id).
- Avoid SELECT \* in production; specify only the columns you need.
- Use LIMIT for pagination on large tables.

## Debugging SQL

- Use `EXPLAIN` to analyze query performance:
  ```sql
  EXPLAIN SELECT * FROM bookings WHERE user_id = 1;
  ```
- Check for slow queries in your MySQL logs.
