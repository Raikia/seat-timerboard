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
- peer-to-peer sync between SeAT instances
- settings for defaults, notifications, display, auto-import, sync, tags, and cleanup

The board keeps timers in `Current` until 2 hours after they elapse, then moves them to `Elapsed`.

## Features

- Track system/location, structure type, structure name, owner, attacker, timer, notes, tags, and access role
- Add multiple timers at once with duplicate-row helpers and collapsed row summaries
- View and edit notes in dedicated modals so optional context stays available without cluttering the table
- Search systems from SeAT's SDE data and owners/attackers from ESI
- Filter timers by structure type, tag, region, visibility, note presence, owner, attacker, or free-text search
- Restrict timers to specific SeAT roles or leave them public
- Send Discord notifications when new timers are created
- Configure per-notification-group role and tag filters so different Discord/Slack groups receive different timers
- Configure local time display in either 24-hour or AM/PM format
- Automatically import friendly timers from supported SeAT notifications with duplicate protection and source tagging
- Sync timers directly with other SeAT instances using tag-controlled peer connections
- Preserve optional structure notes for fittings, handoff context, or manual reminders
- Manage tags, default visibility, notifications, auto-import scope, sync peers, and cleanup tools from the settings page

### Supported auto-import notifications

When auto-import is enabled and the notification belongs to a tracked corporation or alliance member corporation, the plugin can create timers from these SeAT character notification types:

- `StructureAnchoring`
- `StructureLostArmor`
- `StructureLostShields`
- `SkyhookLostShields`
- `MercenaryDenReinforced`
- `OrbitalReinforced`
- `SovStructureReinforced`

Imported timers are tagged automatically where appropriate, such as `Auto Imported`, `Friendly`, `Anchoring`, and `Reinforced`.

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

- The seeder creates the default system and common-use tags
- If you run SeAT in Docker, run these commands inside the SeAT app container
- In Docker-based SeAT environments, restart `front`, `worker`, and `scheduler` after plugin updates so SeAT reloads the package cleanly

## Initial Setup

After installation:

1. Grant the desired Timerboard permissions to your SeAT roles.
2. Open `Timerboard -> Settings`.
3. Set an optional default access role for new timers.
4. Review the default tags.
5. Configure notifications if you want Discord or Slack alerts.
6. Configure display and auto-import settings if you want local time formatting or automatic friendly timer ingestion.
7. Configure sync peers if you want to exchange timers with other SeAT instances.

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

The settings page currently supports six areas.

### Notifications

You can enable or disable notifications, then configure each subscribed SeAT notification group independently.

Each notification group can define:

- which timer access roles it receives
- which timer tags it explicitly allows
- which timer tags it blocks

To send Discord notifications:

1. In SeAT, create or choose a notification group.
2. Add a Discord integration to that group.
3. Subscribe the group to the `seat_timerboard_new_timer` alert.
4. Enable notifications in `Timerboard -> Settings`.

Slack groups can be configured the same way through SeAT notification integrations.

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
- Imported timers reuse the default access role and receive protected system tags such as `Auto Imported`, `Friendly`, `Anchoring`, `Reinforced`, and `Remote Synced` where applicable

### Sync

Timerboard Sync allows direct SeAT-to-SeAT timer sharing without forwarding through a mesh.

- Sync is peer-to-peer only: timers are sent only to the remote SeAT instances you explicitly configure
- Outbound sync is tag-controlled per peer
- Incoming timers are marked locally with `Remote Synced`
- Remote timers keep their source notes and tags, plus a local sync note
- Peer settings let you choose the incoming default role and whether remote deletes should also remove the local copy

### Tags

Create, edit, and delete color-coded tags from the settings page.

Some system tags are protected because plugin features depend on them. Protected tags can have their colors changed, but their names and deletion are locked.

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

### Syncing Timers

Open `Timerboard -> Sync` to configure remote SeAT peers.

For each peer you can define:

- the remote SeAT instance UUID and base URL
- the API token used for authentication
- which local tags should cause timers to be pushed outbound
- which local access role inbound timers should use
- whether deletes on this SeAT should also delete the remote copy

## Repository

- GitHub: `https://github.com/raikia/seat-timerboard`
- Packagist package: `raikia/seat-timerboard`

## Author

Raikia Nardieu
