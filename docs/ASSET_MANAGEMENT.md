# Asset Management

Open **IT & General Affairs > Asset Management** at /admin/assets.

The inventory supports creating and editing assets, searching by code/name/serial/location/custodian, filtering by company/status/condition, and soft deletion/restoration through the Trash filter. Permanent deletion is disabled. Asset codes remain reserved when a record is archived.

## Access

Access is granted to IT/System Administrators (Superadmin), Admin users whose position is HRD, and members of the exact departments configured in config/assets.php. Defaults: IT, GA, IT/GA, IT / GA, Admin/HR, Admin / HR, HRD. Department access applies regardless of role as long as that role is allowed into the panel. Finance Admins and managers do not gain access merely through their role. A manager who belongs to an allowed department does have access.

AssetPolicy protects list, create, edit, archive, and restore actions, including direct page access. Membership is queried afresh; removed or soft-deleted departments do not retain access. Assign department membership through the existing user administration workflow. Adjust the explicit department allowlist to match approved company department names; do not use partial-name matching.

## Fields

Unique asset code, name, category, serial number, company, location, assigned employee, purchase date and cost, status, condition, and notes. This is an inventory register; depreciation, loan/return approvals, and mobile APIs are not included.

## Installation and checks

Run the additive migration: php artisan migrate --path=database/migrations/2026_10_24_000001_create_assets_table.php

Run: php artisan test --filter=AssetManagementTest

The test suite uses the isolated insys_db_testing database configured in phpunit.xml, never the operational database.
