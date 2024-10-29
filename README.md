

# Project Documentation

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Folder Structure](#folder-structure)
4. [Routing Overview](#routing-overview)
5. [Controllers](#controllers)
6. [Models](#models)
7. [Middleware](#middleware)
8. [Utilities and Libraries](#utilities-and-libraries)

---

### Overview

This project is a Laravel-based multimedia streaming platform that supports video and audio streaming, playlist management, watch parties, live television, and an admin panel for comprehensive control over content and user management.

---

### Features

- **User Authentication and Authorization**: Registration, login, password reset, email/mobile verification, and 2FA.
- **Watch Parties with Real-time Chat**: Users can create and join watch parties to enjoy content with friends and family.
- **Streaming Support**: Playlists, video, and audio items are fully manageable from the admin panel.
- **Admin Panel**: Provides controls for managing categories, playlists, users, notifications, advertisements, and reports.
- **Support Tickets**: Ticketing system for users to report issues or ask for help.
- **Notification Management**: Supports email and SMS notifications to keep users informed about updates and events.
- **Subscription and Payment**: Allows users to subscribe to premium content and manage payment history.

---

### Folder Structure

The application structure includes key directories as follows:

- **app/**
    - **Http/Controllers**: Houses controllers for handling user actions, categorized by Admin, API, and User.
    - **Models**: Contains models that represent database entities such as `User`, `Playlist`, `Category`, and others.
    - **Middleware**: Middleware classes to enforce security, handle language preferences, and manage access control.
    - **Lib**: Custom libraries for handling media uploads, authentication, and utility functions.
    - **Helpers**: Helper functions used across the application for common tasks.

- **config/**: Configuration files for database, services, and file storage.
- **routes/**: Defines routes for web, admin, and API endpoints, grouped into `web.php`, `admin.php`, and `api.php`.

---

### Routing Overview

#### Web Routes (`web.php`)

The `web.php` file defines the user-facing routes, including:

- **User Support Tickets**: Manage support tickets with routes for creating, viewing, and replying.
- **Wishlist Management**: Routes for adding and removing items from a user's wishlist.
- **Streaming and Live TV**: Routes to watch live TV, video content, and audio previews.
- **Subscription and Contact**: Allows users to subscribe to services and contact support.

#### Admin Routes (`admin.php`)

The `admin.php` file defines routes exclusively for admin operations, including:

- **Dashboard and Profile**: Access to the admin dashboard and profile settings.
- **Category and Subcategory Management**: Routes for adding, updating, and deleting categories and subcategories.
- **Item Management**: CRUD operations for video and audio items, including uploading and configuring streaming options.
- **User Management**: Routes for managing users, updating statuses, and sending notifications.
- **Notifications and Reports**: Access to manage notification templates and view reports.

#### API Routes (`api.php`)

The `api.php` file provides a RESTful API layer for frontend and mobile apps:

- **Authentication**: API routes for login, registration, and social login.
- **User Profile and Settings**: Endpoints to update profile details, passwords, and preferences.
- **Watch Party**: API routes for creating, joining, and managing watch parties in real-time.
- **Media Content**: API endpoints for fetching categories, playlists, movies, and live television channels.
- **Subscription and Payments**: API routes for handling payments, subscriptions, and transaction history.

---

### Controllers

#### Admin Controllers (`app/Http/Controllers/Admin`)

These controllers manage the backend operations and admin views:

- **CategoryController**: Manages categories and subcategories for organizing content.
- **ItemController**: Handles video and audio items, including CRUD operations and playlist management.
- **WatchPartyController**: Admin functions for monitoring and controlling active watch parties.
- **UserController**: Manages user data, notifications, and account statuses.
- **GeneralSettingController**: Handles global settings like site preferences, contact information, and more.

#### API Controllers (`app/Http/Controllers/Api`)

These controllers serve the API endpoints for mobile and frontend clients:

- **UserController**: Provides user-related actions such as profile updates and accessing watch history.
- **AuthorizationController**: Manages user verification steps like email, mobile, and 2FA.
- **WatchPartyController**: Manages API endpoints for creating, joining, and leaving watch parties.
- **PaymentController**: Handles payment methods, deposit confirmations, and subscription statuses.

#### User Controllers (`app/Http/Controllers/User`)

Handles user-facing interactions, such as:

- **Auth/RegisterController**: User registration and social authentication.
- **TicketController**: Manages user support tickets.
- **SiteController**: Provides main site functions like search, language switching, and static pages (e.g., privacy policy).

---

### Models

The application’s models are located in `app/Models` and represent key data entities:

- **User**: Represents a registered user and their profile data.
- **Playlist**: Manages playlists created by users and associated media items.
- **WatchParty**: Represents a watch party session, enabling real-time interactions.
- **Category** and **SubCategory**: Define categories for media content.
- **Item**: Represents individual video or audio items, with attributes like title, description, and media type.

Each model typically includes relationships and scopes to streamline data retrieval and manipulation.

---

### Middleware

The application includes custom middleware in `app/Http/Middleware`:

- **CheckStatus**: Ensures a user’s account is active before granting access.
- **LanguageMiddleware**: Sets the preferred language based on user settings or URL parameters.
- **RegistrationStep**: Ensures all registration steps are completed for new users.
- **RedirectIfNotVerified**: Redirects users to verification pages if their account is unverified.

Middleware are registered in `Kernel.php` for specific route groups or globally.

---

### Utilities and Libraries

The **Lib** directory (`app/Lib`) contains custom libraries that enhance the platform's functionality:

- **VideoUploader** and **AudioUploader**: Handle file uploads for media content to various storage solutions.
- **FileManager**: Manages file operations like moving, deleting, and renaming files.
- **SocialLogin**: Integrates social authentication for supported providers.
- **GoogleAuthenticator**: Provides two-factor authentication using Google Authenticator.

These utilities can be leveraged by controllers or models to perform common tasks, making the codebase modular and reusable.
