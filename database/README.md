# Database layer

The first migration establishes the production data spine without coupling the current presentation layer to MySQL yet.

Core domains covered:
- verticals and dynamic attributes
- localities and entities
- evidence verification
- ranking criteria
- immutable score versions
- ranking publication versions and positions
- users and roles
- leads
- audit logs

Apply migrations only after configuring the DB_* environment variables:

php bin/migrate.php

The migration runner records applied files in schema_migrations and wraps each migration in a transaction.
