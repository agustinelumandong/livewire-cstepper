<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UI Framework Integration
    |--------------------------------------------------------------------------
    |
    | Choose the UI framework for stepper components. Currently supports WireUI
    | for modern, accessible components with consistent styling.
    |
    */
    'ui_framework' => 'wireui',

    /*
    |--------------------------------------------------------------------------
    | Strict Validation
    |--------------------------------------------------------------------------
    |
    | Enable strict validation to ensure all form data is serializable.
    | This helps prevent Livewire 3 serialization issues.
    |
    */
    'strict_validation' => env('LIVEWIRE_CSTEPPER_STRICT_VALIDATION', true),

    /*
    |--------------------------------------------------------------------------
    | Step Jumping
    |--------------------------------------------------------------------------
    |
    | Allow users to jump to non-adjacent steps. When disabled, users can only
    | move to the next/previous step or go backwards to any completed step.
    |
    */
    'allow_step_jumping' => env('LIVEWIRE_CSTEPPER_ALLOW_JUMPING', false),

    /*
    |--------------------------------------------------------------------------
    | Progress Bar Style
    |--------------------------------------------------------------------------
    |
    | Configure the default styling for the stepper progress bar.
    | Available options: 'modern', 'classic', 'minimal'
    |
    */
    'progress_bar_style' => 'modern',

    /*
    |--------------------------------------------------------------------------
    | Animations
    |--------------------------------------------------------------------------
    |
    | Enable smooth animations when transitioning between steps.
    | This enhances user experience with visual feedback.
    |
    */
    'animation_enabled' => env('LIVEWIRE_CSTEPPER_ANIMATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Auto Save
    |--------------------------------------------------------------------------
    |
    | Automatically save form data as users progress through steps.
    | This helps prevent data loss if users navigate away.
    |
    */
    'auto_save_enabled' => env('LIVEWIRE_CSTEPPER_AUTO_SAVE', false),

    /*
    |--------------------------------------------------------------------------
    | Auto Save Interval
    |--------------------------------------------------------------------------
    |
    | Interval in seconds for auto-saving form data when auto_save_enabled is true.
    |
    */
    'auto_save_interval' => 30,

    /*
    |--------------------------------------------------------------------------
    | URL Step Tracking
    |--------------------------------------------------------------------------
    |
    | Enable or disable URL-based step tracking. When enabled, the current
    | step will be reflected in the URL query parameters.
    |
    */
    'url_step_tracking' => env('LIVEWIRE_CSTEPPER_URL_TRACKING', true),

    /*
    |--------------------------------------------------------------------------
    | Default View Path
    |--------------------------------------------------------------------------
    |
    | This option controls the default view path for stepper components.
    | You can override this in individual step classes by setting the $view property.
    |
    */
    'default_view_path' => 'livewire-cstepper',

    /*
    |--------------------------------------------------------------------------
    | Step Validation Strategy
    |--------------------------------------------------------------------------
    |
    | Choose when to validate steps:
    | - 'on_advance': Validate only when moving to the next step
    | - 'real_time': Validate in real-time as users interact
    | - 'on_submit': Validate only when the stepper is submitted
    |
    */
    'validation_strategy' => 'on_advance',

    /*
    |--------------------------------------------------------------------------
    | Progress Indicator Icons
    |--------------------------------------------------------------------------
    |
    | Default icons for different step states. These can be overridden
    | per step using the stepIcon() method.
    |
    */
    'icons' => [
        'pending' => 'clock',
        'current' => 'arrow-right',
        'completed' => 'check-circle',
        'error' => 'exclamation-circle',
    ],

    /*
    |--------------------------------------------------------------------------
    | WireUI Integration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to WireUI integration
    |
    */
    'wireui' => [
        'card_variant' => 'default',
        'button_variant' => 'primary',
        'progress_color' => 'primary',
    ],
];