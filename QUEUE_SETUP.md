# Laravel Queue System Setup Guide

This document explains how to set up and configure the Laravel queue system for processing queued emails and other background jobs.

## Overview

The queue system allows your application to defer time-consuming tasks (like sending emails) to be processed in the background, improving response times for users. When you use `Mail::to()->queue()`, the email is added to a queue and processed by a queue worker.

## Prerequisites

- Laravel application
- Database configured and accessible
- PHP CLI access

## Step 1: Configure Queue Connection

### Update `.env` File

Open your `.env` file and set the queue connection:

```env
QUEUE_CONNECTION=database
```

**Available Queue Drivers:**

- **`database`** - Stores jobs in database table (Recommended for most setups)
  - Pros: Simple setup, no additional services required
  - Cons: Slower than Redis, database overhead
  
- **`redis`** - Uses Redis for queue storage (Faster, requires Redis)
  - Pros: Very fast, scalable
  - Cons: Requires Redis server setup
  
- **`sync`** - Processes jobs immediately (No queue)
  - Pros: No setup required
  - Cons: Blocks request, not suitable for production

### Verify Configuration

Check your `config/queue.php` file to ensure the default connection is set correctly:

```php
'default' => env('QUEUE_CONNECTION', 'sync'),
```

## Step 2: Create Jobs Table

The jobs table stores queued jobs. If it doesn't exist, create it:

### Option A: Using Existing Migration

If the migration file already exists (`database/migrations/*_create_jobs_table.php`), run:

```bash
php artisan migrate
```

### Option B: Create New Migration

If the migration doesn't exist, create it:

```bash
php artisan queue:table
php artisan migrate
```

This will create a `jobs` table in your database with the following structure:
- `id` - Job ID
- `queue` - Queue name
- `payload` - Serialized job data
- `attempts` - Number of attempts
- `reserved_at` - When job was reserved
- `available_at` - When job becomes available
- `created_at` - Job creation timestamp

## Step 3: Run Queue Worker

The queue worker processes jobs from the queue. You need to keep it running.

### Development Environment

Run the worker in your terminal:

```bash
php artisan queue:work
```

**Options:**
- `--queue=high,default` - Process specific queues
- `--tries=3` - Number of times to attempt a job before failing
- `--timeout=60` - Timeout in seconds
- `--sleep=3` - Seconds to wait when queue is empty

**Example:**
```bash
php artisan queue:work --tries=3 --timeout=60
```

### Production Environment

For production, you should run the worker as a daemon or use a process manager.

#### Option A: Daemon Mode

```bash
php artisan queue:work --daemon --tries=3 --timeout=60
```

#### Option B: Supervisor (Recommended)

Supervisor keeps the queue worker running and automatically restarts it if it crashes.

**1. Install Supervisor (Ubuntu/Debian):**
```bash
sudo apt-get install supervisor
```

**2. Create Supervisor Configuration:**

Create `/etc/supervisor/conf.d/proxima-worker.conf`:

```ini
[program:proxima-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/radu/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/radu/www/storage/logs/worker.log
stopwaitsecs=3600
```

**Important:** Replace `/path/to/your/project` with your actual project path.

**3. Update Supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start proxima-worker:*
```

**4. Check Status:**
```bash
sudo supervisorctl status
```

#### Option C: Systemd (Alternative)

Create `/etc/systemd/system/laravel-worker.service`:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Then:
```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

## Step 4: Monitor Queue Status

### Check Queue Jobs

View pending jobs in database:
```sql
SELECT * FROM jobs;
```

### Check Failed Jobs

View failed jobs:
```bash
php artisan queue:failed
```

### Retry Failed Jobs

Retry all failed jobs:
```bash
php artisan queue:retry all
```

Retry specific failed job:
```bash
php artisan queue:retry {job-id}
```

### Clear Failed Jobs

Delete a specific failed job:
```bash
php artisan queue:forget {job-id}
```

Delete all failed jobs:
```bash
php artisan queue:flush
```

## Step 5: Testing the Queue

### Test Email Queue

1. Ensure queue connection is set to `database` in `.env`
2. Start the queue worker: `php artisan queue:work`
3. Trigger an action that queues an email (e.g., update user email)
4. Check the `jobs` table to see if the job was queued
5. Watch the worker terminal to see it process the job
6. Verify the email was sent

### Debug Queue Issues

**Check Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Check Worker Logs (if using Supervisor):**
```bash
tail -f storage/logs/worker.log
```

**Test Queue Connection:**
```bash
php artisan tinker
>>> dispatch(new \App\Jobs\TestJob());
```

## Step 6: Production Best Practices

### 1. Use Multiple Workers

For high-traffic applications, run multiple workers:

```ini
[program:laravel-worker]
numprocs=4  # Run 4 worker processes
```

### 2. Set Appropriate Timeouts

```bash
php artisan queue:work --timeout=60
```

### 3. Handle Job Failures

Implement retry logic in your mailable/job classes:

```php
public $tries = 3;
public $backoff = [10, 30, 60]; // Wait 10s, 30s, 60s between retries
```

### 4. Monitor Queue Health

Set up monitoring to alert if:
- Queue size grows too large
- Worker stops running
- Failed jobs accumulate

### 5. Use Queue Priorities

For important emails, use priority queues:

```php
Mail::to($user->email)->queue(new EmailAddressUpdatedEmail($data))->onQueue('high');
```

Then process high priority queue first:
```bash
php artisan queue:work --queue=high,default
```

## Troubleshooting

### Queue Worker Not Processing Jobs

1. **Check if worker is running:**
   ```bash
   ps aux | grep "queue:work"
   ```

2. **Check queue connection:**
   ```bash
   php artisan tinker
   >>> config('queue.default')
   ```

3. **Check database connection:**
   ```bash
   php artisan migrate:status
   ```

4. **Restart worker:**
   ```bash
   php artisan queue:restart
   ```

### Jobs Stuck in Queue

1. **Check for errors in logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Clear stuck jobs:**
   ```bash
   php artisan queue:flush
   ```

3. **Restart worker:**
   ```bash
   php artisan queue:restart
   ```

### Memory Issues

If workers consume too much memory:

```bash
php artisan queue:work --max-jobs=1000 --max-time=3600
```

This restarts the worker after processing 1000 jobs or 1 hour.

## Code Implementation

### Mailable with Queue

Your mailable class should implement `ShouldQueue`:

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailAddressUpdatedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $tries = 3; // Retry 3 times on failure
    public $timeout = 60; // Timeout after 60 seconds
    
    // ... rest of your mailable code
}
```

### Queue Email in Controller

```php
use Illuminate\Support\Facades\Mail;

// Queue email
Mail::to($user->email)->queue(new EmailAddressUpdatedEmail($data));
```

## Additional Resources

- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Supervisor Documentation](http://supervisord.org/)

## Summary

1. ✅ Set `QUEUE_CONNECTION=database` in `.env`
2. ✅ Run `php artisan migrate` to create jobs table
3. ✅ Start queue worker: `php artisan queue:work`
4. ✅ For production: Use Supervisor or Systemd to keep worker running
5. ✅ Monitor failed jobs and logs regularly

Your queue system is now ready to process emails and other background jobs!
