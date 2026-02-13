# SeAT Timerboard Plugin

A powerful and feature-rich timerboard plugin for tracking structure reinforce timers in Eve Online within SeAT.

## Features

*   **Structure Timers**: Track reinforce timers for Upwell structures and POCOs.
*   **Discord Notifications**: Automated notifications sent to Discord channels via SeAT's notification system.
    *   Includes structure images, location links (Dotlan), and relative timestamps.
    *   Configurable role-based filtering for notifications.
*   **Role-Based Access Control**:
    *   Restrict timer visibility to specific SeAT roles or make them public.
    *   **Admin Bypass**: Users with the `superuser` role can view all timers regardless of restrictions.
*   **Smart Search**: Integrated ESI search for Systems and Corporations.
*   **Visual Dashboard**:
    *   "Current" timers (future + last 2 hours).
    *   "Elapsed" timers (older than 2 hours).
    *   Structure type icons for quick identification.
*   **Customizable Tags**: Color-coded tags (Armor, Hull, Friendly, Hostile, etc.) managed via settings.
*   **Maintenance Tools**:
    *   "Delete All Elapsed" button to clean up old history.
    *   "Delete All Timers" (Danger Zone) for full resets.

## Installation

1.  **Require the Package**:
    Either add "raikia/seat-timerboard" to your .env plugins array or run:

    ```bash
    composer require raikia/seat-timerboard
    ```

2.  **Run Migrations**:
    Creates the necessary tables for timers, tags, and settings.
    ```bash
    php artisan migrate
    ```

3.  **Seed the Database**:
    **Important:** This populates the default tags (Armor, Hull, etc.).
    ```bash
    php artisan db:seed --class=Raikia\SeatTimerboard\Database\Seeds\TimerboardSeeder
    ```
    *Note: If specific tags are missing, you can create them manually in the Settings page.*

4.  **Clear Caches**:
    ```bash
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    ```

## Permissions

Assign these permissions to your roles in SeAT:

| Permission | Description |
| :--- | :--- |
| `seat-timerboard.view` | Access the timerboard dashboard and view timers. |
| `seat-timerboard.create` | Create new timers. |
| `seat-timerboard.edit` | Edit existing timers. |
| `seat-timerboard.delete` | Delete individual timers and cleanup elapsed timers. |
| `seat-timerboard.delete-all` | **Dangerous**: Access the "Delete All" button to wipe the database. |
| `seat-timerboard.settings` | Access the settings page to manage tags and defaults. |

## Configuration

### Discord Notifications
1.  Go to **Configuration -> Notifications -> Groups** in SeAT.
2.  Create a new group (e.g., "Timerboard").
3.  Add the **Discord** integration to this group.
4.  Under **Alerts**, subscribe to the `seat_timerboard_new_timer` alert.
5.  In the **Timerboard Settings** page, ensure "Enable Notifications" is checked.

### Default Access
You can set a default role for new timers in the **Settings** page. This is useful if you want all created timers to be restricted to a specific group by default.

## Usage

Navigate to **Timerboard** in the sidebar.
*   **Add Timer**: Click the "Add Timer" button (requires permission).
*   **View**: Timers are sorted by time. "Current" tab shows upcoming timers and those that elapsed < 2 hours ago.
*   **Search**: Use the search bar in the creation modal to find systems and corporations (requires ESI scope).


## Development

Raikia Nardieu