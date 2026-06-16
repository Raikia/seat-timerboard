# SeAT Timerboard

`raikia/seat-timerboard` is a SeAT v5 plugin for tracking structure timers in EVE.

It adds a `Timerboard` section to SeAT with:

- current and elapsed timer views
- batch timer creation in a single modal
- inline note indicators with modal note viewing/editing
- edit and delete actions
- tags and optional role-based visibility
- Discord notifications for new timers
- automatic timer imports from supported SeAT notifications
- settings for defaults, notifications, display, auto-import, and cleanup

The board keeps timers in `Current` until 2 hours after they elapse, then moves them to `Elapsed`.

## Features

- Track system/location, structure type, structure name, owner, attacker, timer, notes, tags, and access role
- Add multiple timers at once with duplicate-row helpers and collapsed row summaries
- View and edit notes in dedicated modals so optional context stays available without cluttering the table
- Search systems from SeAT's SDE data and owners/attackers from ESI
- Filter timers by structure type, tag, region, visibility, note presence, owner, attacker, or free-text search
- Restrict timers to specific SeAT roles or leave them public
- Send Discord notifications when new timers are created
- Configure local time display in either 24-hour or AM/PM format
- Automatically import friendly timers from supported SeAT notifications with duplicate protection and source tagging
- Manage tags, default visibility, notifications, auto-import scope, and cleanup tools from the settings page

### Supported auto-import notifications

When auto-import is enabled and the notification belongs to a tracked corporation or alliance member corporation, the plugin can create timers from these SeAT character notification types:

- `StructureAnchoring`
- `StructureLostArmor`
- `StructureLostShields`
- `SkyhookLostShields`
- `OrbitalReinforced`
- `SovStructureReinforced`

Imported timers are tagged automatically where appropriate, such as `Auto Imported`, `Friendly`, `Anchoring`, `Reinforced`, `Skyhook`, and `Sovereignty`.

## Requirements

- SeAT v5
- `eveseat/services`
- `eveseat/eveapi`
- `eveseat/web`

For owner/attacker search, users need a valid SeAT main character token since that lookup uses the character ESI search endpoint.

## Installation

Install the package in your SeAT instance:

```bash
composer require raikia/seat-timerboard
```

Then run the usual SeAT setup steps:

```bash
php artisan migrate
php artisan db:seed --class=Raikia\\SeatTimerboard\\Database\\Seeds\\TimerboardSeeder
```

Notes:

- The seeder creates a few default tags
- If you run SeAT in Docker, run these commands inside the SeAT app container
- In Docker-based SeAT environments, restart `front`, `worker`, and `scheduler` after plugin updates so SeAT reloads the package cleanly

## Initial Setup

After installation:

1. Grant the desired Timerboard permissions to your SeAT roles.
2. Open `Timerboard -> Settings`.
3. Set an optional default access role for new timers.
4. Review the default tags.
5. Configure notifications if you want Discord alerts.
6. Configure display and auto-import settings if you want local time formatting or automatic friendly timer ingestion.

## Permissions

The plugin registers the following permissions:

| Permission | Description |
| :--- | :--- |
| `seat-timerboard.view` | View the timerboard dashboard and search endpoints. |
| `seat-timerboard.create` | Create timers, including batch creation. |
| `seat-timerboard.edit` | Edit existing timers. |
| `seat-timerboard.delete` | Delete timers and clear elapsed timers. |
| `seat-timerboard.delete-all` | Delete every timer on the board. |
| `seat-timerboard.settings` | Access Timerboard settings. |

## Settings

The settings page currently supports five areas.

### Notifications

You can enable or disable notifications and limit them to specific timer access roles. Include `Public (Everyone)` if you want public timers to notify.

To send Discord notifications:

1. In SeAT, create or choose a notification group.
2. Add a Discord integration to that group.
3. Subscribe the group to the `seat_timerboard_new_timer` alert.
4. Enable notifications in `Timerboard -> Settings`.

### Defaults

Set a default access role for new timers, or leave it blank to make them public by default.

### Display

Set the local time display format for the dashboard. EVE time remains in UTC, while local time can be shown in either `24-hour` or `AM/PM` format.

### Auto Import

Choose which corporations and alliances should be watched for friendly structure notifications.

- Tracked corporations are selected from corporations already known to SeAT
- Tracked alliances are stored as alliances, but imports resolve against their current member corporations
- Only new notifications going forward are imported
- Duplicate imports are ignored using a notification-derived fingerprint

### Tags

Create, edit, and delete color-coded tags from the settings page.

## Usage

Open `Timerboard` from the SeAT sidebar.

### Creating Timers

Click `Add Timers`, fill out one or more rows, and save the batch in one submission.

Time input supports:

- Absolute UTC time: `YYYY.MM.DD HH:MM`
- Absolute UTC time with seconds: `YYYY.MM.DD HH:MM:SS`
- Relative time: `1d 4h 30m`, `2 days 3 hours`, and similar formats

Optional notes can be added while creating timers and later opened from the note icon shown beside timers that have saved context.

### Editing Timers

Use the pencil action on a timer row to update it in the edit modal. Notes can be updated from the edit flow without taking over the main form layout.

### Filtering and Views

- `Current` and `Elapsed` timers are shown in separate dashboard views
- The collapsible filter panel lets you narrow the list without leaving the page
- Table sorting works on timer values rather than the formatted display text

## Repository

- GitHub: `https://github.com/raikia/seat-timerboard`
- Packagist package: `raikia/seat-timerboard`

## Author

Raikia Nardieu
