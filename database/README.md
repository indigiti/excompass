# Future database compatibility

ExCompass currently runs without a database. The active runtime adapter is JSON/file storage behind repository interfaces.

The SQL files in this directory are not required by the web application. They document and prepare the relational structure for a future MySQL adapter covering:

- verticals and dynamic attributes
- localities and entities
- evidence verification
- ranking criteria
- immutable score versions
- ranking publication versions and positions
- users and roles
- leads
- audit logs

Because application code depends on repository contracts rather than PDO, a later database migration can be implemented as new repository adapters without changing public pages or editorial workflows.

The migration command in bin/migrate.php is retained only for that future migration path.
