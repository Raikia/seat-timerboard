# SeAT Timerboard Plugin

A timerboard plugin for structure reinforce timers in Eve Online for SeAT.

## Installation

1.  Navigate to your SeAT `packages` directory.
2.  Clone this repository or ensure the files are present in `packages/raikia/seat-timerboard`.
3.  Add the repository to your main SeAT `composer.json` repositories:

    ```json
    "repositories": [
        {
            "type": "path",
            "url": "packages/raikia/seat-timerboard"
        }
    ]
    ```

4.  Require the package:

    ```bash
    composer require raikia/seat-timerboard
    ```

5.  Run the migrations (if/when added):

    ```bash
    php artisan migrate
    ```

6.  Clear the cache:

    ```bash
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    ```

7.  Assign the `View Timerboard` permission to your role.

## Usage

Navigate to the Timerboard entry in the sidebar.
