# Managed schedule for laravel

Alternative artisan command for invoke commands by schedule with ability to manage its schedule and activity via database.

## Installation

```bash
composer require madridianfox/laravel-managed-schedule
```

## Usage

Just invoke `schedule:run` command.  
After first execution of that command, static schedule from Console/Kernel will be copied to database to `managed_schedule_items` table.  

You can change schedule for every command via `managed_schedule_items.schedule` and activity via `managed_schedule_items.active` field.