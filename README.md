# Moodle Local Plugin – Subject (local_subject)

## Overview
The `local_subject` plugin is a custom Moodle local plugin developed to manage academic subjects within a Moodle-based school learning management system.

This plugin allows administrators to create and manage subjects while automatically creating the corresponding Moodle course when a subject is added. It helps align the school's academic structure (Academic Year → Class → Division → Subject) with Moodle’s course structure.

## Features
- Create, update, and delete subjects
- Automatically create Moodle courses when a subject is added
- Organize and filter subjects based on Academic Year, Class, and Division
- Secure form handling using Moodle Form API
- Capability checks for proper access control
- Dynamic interface using AJAX for filtering and updates

## Project Context
This plugin was developed as part of an internal School Learning Management System project built on Moodle. The goal was to simplify how institutions manage subjects and automatically link them with Moodle courses.

Only the components that I personally worked on are included here as a code sample.

## My Contribution
My responsibilities while developing this plugin included:

- Designing the plugin structure following Moodle plugin standards
- Implementing CRUD functionality for subject management
- Integrating subject creation with Moodle’s course creation process
- Handling database operations using Moodle `$DB` API
- Creating AJAX-based functionality for dynamic filtering
- Implementing capability checks and session security
- Debugging and testing the plugin functionality

## Installation

### Install using ZIP
1. Log in to your Moodle site as an administrator.
2. Go to **Site administration → Plugins → Install plugins**.
3. Upload the plugin ZIP file.
4. Follow the on-screen instructions to complete the installation.

### Manual Installation
1. Copy the plugin folder into your Moodle directory:

```
{your/moodle}/local/subject
```

2. Log in as an administrator and visit:

**Site administration → Notifications**

3. Moodle will automatically detect the plugin and install it.

Alternatively, run the upgrade command:

```
php admin/cli/upgrade.php
```

## Technologies Used
- PHP
- Moodle Plugin APIs
- Moodle Database API
- MySQL / MariaDB
- HTML / CSS
- JavaScript
- Mustache templates

## AI Usage Disclosure
AI tools were used only for improving documentation and formatting before publishing the code publicly. The plugin functionality, structure, and implementation were developed by me.

## Author
Sinsila Ansar  
PHP / Moodle Developer
