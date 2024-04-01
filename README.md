<p align="center" style="font-size:42px !important;">🔑 Built Mighty Password Reset</p>

## About
Our password reset plugin gives site administrators better tools around requiring users to reset their passwords. It includes both a timed password reset and a bulk password reset. The timed reset requires users to update their password every x amount of days. The bulk reset, however, requires users of a certain user level to reset their password, via a link sent to their email, on login and does not allow them to login until they've done so.

## Installation
To install, add the plugin like any other WordPress plugin. Then go to Settings > Password Reset to configure.

## Developers
The plugin comes with several actions and filters for you to modify specific pieces of the plugin.

### Actions
These are the following actions that are available. All of these actions include the user ID.

* `builtpass_before_notice` - An action that runs before the password reset notice page content. Shown to bulk reset users.
* `builtpass_reset_notice` - An action that runs within the content of the password reset notice page. Shown to bulk reset users.
* `builtpass_after_notice` - An action that runs after the password reset notice page content. Shown to bulk reset users.
* `builtpass_before_external` - An action that runs before the external password reset form. Shown to bulk reset users.
* `builtpass_after_external` - An action that runs before the external password reset form. Shown to bulk reset users.
* `builtpass_before_internal` - An action that runs before the internal password reset form. Shown to timed reset users.
* `builtpass_after_internal` - An action that runs after the internal password reset form. Shown to timed reset users.
* `builtpass_before_expired` - An action that runs before the expired password request form. Shown to bulk reset users.
* `builtpass_after_expired` - An action that runs after the expired password request form. Shown to bulk reset users.

Example:
```
add_action( 'builtpass_before_notice', 'custom_builtpass_before_notice', 10, 1 );
function custom_builtpass_before_notice( $user_id ) {

    // Do something with the $user_id or output additional content.

}
```

### Filters
These are the following filters that are available.

#### Reset Times
This is an admin setting where you can remove or add reset times. Times are a multi-dimensional array with the key being numeric and the value being the label.
```
add_filter( 'builtpass_reset_times', 'custom_builtpass_reset_times', 10, 1 );
function custom_builtpass_reset_times( $times ) {

    // Remove 60 day reset.
    unset( $times['60'] );

    // Add time.
    $times['730'] = 'Every 2 Years'; 

    // Return.
    return $times;

}
```

### Bulk Reset + Timed Fields
This is an admin setting field where you can filer the available fields.

```
add_filter( 'builtpass_bulk_fields', 'custom_builtpass_bulk_fields', 10, 1 );
function custom_builtpass_bulk_fields( $fields ) {

    // Filter the multi-dimensional array of $fields.

}
```

### Mail Filters
There are filters for different pieces of mail.

## 1.0.0

* Initial plugin creation.