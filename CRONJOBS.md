# Cronjobs Documentation

This document provides a comprehensive overview of all scheduled cronjobs in the ProximaRide application.

## Table of Contents

- [Scheduled Cronjobs](#scheduled-cronjobs)
- [Manual Commands](#manual-commands)
- [Setup Instructions](#setup-instructions)

---

## Scheduled Cronjobs

These commands are automatically executed by Laravel's task scheduler. Make sure the scheduler is running (see [Setup Instructions](#setup-instructions)).

### 1. Expire Bookings Command

**Command:** `bookings:expire`  
**Schedule:** Every minute  
**File:** `app/Console/Commands/ExpireBookingsCommand.php`

**Description:**  
Processes expired bookings and sends notifications to users. This command was moved from middleware to prevent database connection timeouts during regular requests.

**What it does:**
- Finds all bookings where `expires_at < now()`
- Creates notifications for expired bookings
- Sends FCM push notifications to users
- Sends email notifications (if enabled)
- Sends SMS notifications via Twilio (if enabled)
- Deletes bookings after email notification is sent
- Updates rating deadlines (`live_limit` and `reply_deadline`)

**Features:**
- Error handling for individual bookings
- Prevents overlapping executions
- Logs errors on failure

---

### 2. Student Card Expiry Cron

**Command:** `student-card-expiry:cron`  
**Schedule:** Daily  
**File:** `app/Console/Commands/StudentCardExpiryCron.php`

**Description:**  
Sends notifications to students about their card expiration status.

**What it does:**
- **1st of month:** Sends "about to expire" notifications to students whose cards expire this month
- **23rd of month:** Sends reminder notifications
- **Last day of month:** Sends "expired" notifications

**Notifications sent:**
- Email (if enabled)
- Database notification
- FCM push notification

---

### 3. Student Annual Renewal Cron

**Command:** `student-annual-renewal:cron`  
**Schedule:** Daily  
**File:** `app/Console/Commands/StudentAnnualRenewalCron.php`

**Description:**  
Sends annual reminders to students to confirm their student status.

**What it does:**
- Finds students whose student card upload anniversary is today
- Sends annual renewal email (only once per year)
- Sends reminders at 30, 60, and 90 days after initial email (if no response)
- Creates database notifications
- Sends FCM push notifications

**Logic:**
- Only sends to students who uploaded their card at least one year ago
- Checks if notification was already sent this year
- Tracks if user has updated their card since notification

---

### 4. Delete Old Messages Cron

**Command:** `delete-old-messages:cron`  
**Schedule:** Daily  
**File:** `app/Console/Commands/DeleteOldMessagesCron.php`

**Description:**  
Deletes messages that have been marked as old for more than one month.

**What it does:**
- Finds messages with `status = 'old'` and `updated_at < one month ago`
- Permanently deletes these messages from the database

---

### 5. User Birthday Wish Cron

**Command:** `user-birthday-wish:cron`  
**Schedule:** Daily  
**File:** `app/Console/Commands/UserBirthdayWishCron.php`

**Description:**  
Sends birthday wishes to users on their birthday.

**What it does:**
- Finds all active users whose birthday is today
- Sends birthday email (if email notifications enabled)
- Creates database notification with birthday message
- Sends FCM push notifications

**Requirements:**
- User must have a valid `dob` (date of birth) field
- User must be active
- Email notifications must be enabled for email delivery

---

### 6. Holiday Season Cron

**Command:** `holiday-season:cron`  
**Schedule:** Daily (but only executes on December 20th)  
**File:** `app/Console/Commands/HolidaySeasonCron.php`

**Description:**  
Sends holiday greetings (Christmas and New Year) to all users.

**What it does:**
- Only runs on December 20th
- Sends holiday email to all active users with email notifications enabled
- Creates database notifications for all active users
- Sends FCM push notifications

**Note:** This command is scheduled twice in `Kernel.php` (line 35-36), which appears to be a duplicate.

---

### 7. Send Passenger List Cron

**Command:** `send-passenger-list:cron`  
**Schedule:** Every 15 minutes  
**File:** `app/Console/Commands/SendPassengerList.php`

**Description:**  
Sends passenger list to drivers 1 hour before their ride departure.

**What it does:**
- Finds rides scheduled for today
- Checks if ride is exactly 1 hour before departure
- Sends email with passenger list (if email notifications enabled)
- Creates database notification
- Sends FCM push notifications
- Sends SMS via Twilio (if SMS notifications enabled)

**Passenger list includes:**
- Passenger names
- Phone numbers (formatted)
- Number of seats booked

---

### 8. Ride Complete Cron

**Command:** `ride-complete:cron`  
**Schedule:** Every 30 minutes  
**File:** `app/Console/Commands/RideCompleteCron.php`

**Description:**  
Processes completed rides and handles payouts, refunds, and review invitations.

**What it does:**
- Finds rides with `status = '0'` (active) that have passed their completion date/time
- Updates ride status to `'3'` (completed)
- Calculates driver payouts (with tax deductions if configured)
- Handles booking fee refunds for verified students
- Creates payout records
- Sends review invitation emails to drivers and passengers
- Creates review notification prompts
- Sends FCM push notifications
- Marks old notifications and messages as deleted/old

**Payout calculation:**
- Considers booking fees (may go to driver or student)
- Applies tax deductions if configured
- Handles state-wise or global tax rates

**Refund handling:**
- Supports PayPal refunds
- Supports Stripe refunds
- Supports account balance refunds
- Supports coffee wallet credits

---

### 9. Database Backup

**Command:** `backup:run --only-db`  
**Schedule:** Daily at 01:30  
**Package:** Spatie Laravel Backup

**Description:**  
Creates a database backup using the Spatie backup package.

**What it does:**
- Backs up the database only (not files)
- Logs success/failure

**Note:** Requires Spatie Laravel Backup package to be installed and configured.

---

## Manual Commands

These commands are available but not scheduled. They can be run manually when needed.

### 10. Deactive User Account Cron

**Command:** `deactive-user-account:cron`  
**File:** `app/Console/Commands/DeactiveUserAccountCron.php`

**Description:**  
Deactivates user accounts based on cancellation history.

**What it does:**
- Checks all users with `admin_deactive_account = '0'`
- Counts driver cancellations (grouped by ride_id)
- Counts passenger cancellations (grouped by booking_id)
- Sets `admin_deactive_account = '1'` if user has any cancellations

**Note:** This command is not scheduled. Run manually when needed.

---

### 11. Send SMS Cron

**Command:** `send-sms:cron`  
**File:** `app/Console/Commands/SendSmsCron.php`

**Description:**  
Sends passenger list SMS to drivers before ride departure.

**What it does:**
- Finds rides scheduled 1 hour from now
- Sends SMS with passenger list via Twilio
- Only runs in non-local environments

**Note:** This command appears to be a duplicate/alternative to `send-passenger-list:cron`. It's not scheduled and may have some bugs (references undefined variables).

---

### 12. SQL Sync Migrations

**Command:** `sql:sync-migrations`  
**File:** `app/Console/Commands/SqlSyncMigrations.php`

**Description:**  
Creates Laravel migrations for tables that exist in SQL structure but not in migrations.

**Options:**
- `--dry-run`: Only list missing tables, do not create migrations
- `--force`: Create migrations without confirmation
- `--sql=`: Path to SQL structure file (default: `proximaride.sql` in project root)

**What it does:**
- Parses SQL structure file
- Compares with existing migrations
- Creates migration files for missing tables

**Usage:**
```bash
php artisan sql:sync-migrations
php artisan sql:sync-migrations --dry-run
php artisan sql:sync-migrations --force
```

---

### 13. Sync Cities From API

**Command:** `cities:sync`  
**File:** `app/Console/Commands/SyncCitiesFromApi.php`

**Description:**  
Syncs cities from an external API into the database.

**Options:**
- `--country=`: Specific country name to sync
- `--state=`: Specific state name to sync
- `--fresh`: Clear existing cities before syncing

**What it does:**
- Fetches cities from CountryStateCity API
- Creates or updates city records
- Can sync all countries, specific country, or specific state

**Usage:**
```bash
php artisan cities:sync
php artisan cities:sync --country="United States"
php artisan cities:sync --country="United States" --state="California"
php artisan cities:sync --fresh
```

---

## Setup Instructions

### 1. Ensure Laravel Scheduler is Running

The Laravel scheduler needs to be running as a cron job. Add this to your server's crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**For Windows (Task Scheduler):**
Create a scheduled task that runs every minute:
- Program: `php`
- Arguments: `artisan schedule:run`
- Start in: `D:\temp\radu_proxima\www` (or your project path)

### 2. Verify Commands are Registered

Check if commands are available:
```bash
php artisan list
```

### 3. Test Individual Commands

Test a command manually:
```bash
php artisan bookings:expire
php artisan student-card-expiry:cron
# etc.
```

### 4. Monitor Logs

Check Laravel logs for cronjob execution:
```bash
tail -f storage/logs/laravel.log
```

### 5. Check Schedule Status

View scheduled tasks:
```bash
php artisan schedule:list
```

---

## Notes

1. **Duplicate Schedule:** The `holiday-season:cron` command is scheduled twice in `Kernel.php` (lines 35-36). Consider removing one.

2. **Unused Commands:** Some commands like `deactive-user-account:cron` and `send-sms:cron` are not scheduled and may need manual execution or scheduling.

3. **Error Handling:** Most commands include error handling and logging. Check logs regularly for issues.

4. **Performance:** Commands that process large datasets use chunking to avoid memory issues.

5. **Environment:** Some commands (like SMS sending) only run in non-local environments.

---

## Command Summary Table

| Command | Schedule | Status | Purpose |
|---------|----------|--------|---------|
| `bookings:expire` | Every minute | ✅ Active | Process expired bookings |
| `student-card-expiry:cron` | Daily | ✅ Active | Student card expiry notifications |
| `student-annual-renewal:cron` | Daily | ✅ Active | Annual student renewal reminders |
| `delete-old-messages:cron` | Daily | ✅ Active | Clean up old messages |
| `user-birthday-wish:cron` | Daily | ✅ Active | Birthday wishes |
| `holiday-season:cron` | Daily (Dec 20) | ✅ Active | Holiday greetings |
| `send-passenger-list:cron` | Every 15 min | ✅ Active | Passenger list to drivers |
| `ride-complete:cron` | Every 30 min | ✅ Active | Process completed rides |
| `backup:run --only-db` | Daily 01:30 | ✅ Active | Database backup |
| `deactive-user-account:cron` | None | ⚠️ Manual | Deactivate users |
| `send-sms:cron` | None | ⚠️ Manual | Send SMS (duplicate?) |
| `sql:sync-migrations` | None | ⚠️ Manual | Sync SQL to migrations |
| `cities:sync` | None | ⚠️ Manual | Sync cities from API |

---

**Last Updated:** Generated automatically from codebase analysis  
**Maintained By:** Development Team
