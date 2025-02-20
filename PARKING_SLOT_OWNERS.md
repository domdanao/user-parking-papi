# Parking Slot Owners Integration Plan

## Overview

This document outlines the implementation plan for integrating parking slot owners into the parking payment platform. Parking slot owners are the owners of real estate who allow their parking slots to be used through the platform. They will be responsible for registering parking slots and setting rates.

## Database Changes

### 1. New Table: parking_slot_owners

```php
Schema::create('parking_slot_owners', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('contact_number');
    $table->string('business_name');
    $table->text('business_address');
    $table->json('payment_details');  // For receiving payments
    $table->enum('status', ['active', 'suspended'])->default('active');
    $table->timestamps();
});
```

### 2. Modifications to Existing Tables

#### slots table

```php
// Add new columns
$table->foreignId('parking_slot_owner_id')->constrained();
$table->string('address');  // Physical address of the slot
$table->text('description')->nullable();
```

#### rate_cards table

```php
// Remove default rates seeding
// Add new columns
$table->foreignId('parking_slot_owner_id')->constrained();
$table->foreignId('slot_id')->constrained();
$table->string('name');  // To identify different rate schemes
$table->text('description')->nullable();
$table->boolean('is_active')->default(true);
```

## Model Relationships

### ParkingSlotOwner Model

```php
class ParkingSlotOwner extends Model
{
    // Relationships
    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function rateCards()
    {
        return $this->hasMany(RateCard::class);
    }
}
```

### Slot Model Updates

```php
class Slot extends Model
{
    // Add new relationship
    public function owner()
    {
        return $this->belongsTo(ParkingSlotOwner::class, 'parking_slot_owner_id');
    }

    public function rateCards()
    {
        return $this->hasMany(RateCard::class);
    }
}
```

### RateCard Model Updates

```php
class RateCard extends Model
{
    // Add new relationships
    public function owner()
    {
        return $this->belongsTo(ParkingSlotOwner::class, 'parking_slot_owner_id');
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }
}
```

## Authentication & Authorization

1. Create separate auth guard for parking slot owners
2. Implement owner-specific middleware
3. Create authentication endpoints:
    - Registration
    - Login
    - Password reset
    - Email verification

## Features & Endpoints

### 1. Owner Management

-   Registration
-   Profile management
-   Password management
-   Business details update
-   Payment details management

### 2. Slot Management

-   Create new parking slots
-   Update slot details
-   View all slots
-   Disable/Enable slots
-   Delete slots (if no active sessions)

### 3. Rate Management

-   Create rate cards
-   Update rates
-   View all rates
-   Activate/Deactivate rate cards
-   Delete rate cards (if no active sessions)

### 4. Dashboard Features

-   Active parking sessions overview
-   Revenue reports
    -   Daily earnings
    -   Monthly earnings
    -   Annual summary
-   Slot utilization statistics
    -   Usage patterns
    -   Peak hours
    -   Popular slots
-   Transaction history
-   Withdrawal management

## Business Rules

1. Slot Management

    - Owners can have multiple parking slots
    - Each slot must have a valid address and location
    - Slots can be temporarily disabled for maintenance
    - Cannot delete slots with active parking sessions

2. Rate Management

    - Each slot must have at least one active rate card
    - Multiple rate cards can exist for different times/days
    - Rate changes don't affect active parking sessions
    - Rates must be set in the platform's currency

3. Financial Rules

    - System takes a percentage of each transaction (platform fee)
    - Owners can view their earnings in real-time
    - Withdrawal requests process after clearing period
    - Minimum withdrawal amount applies

4. Security & Compliance
    - Owner verification required before activation
    - Regular security audits
    - Compliance with local parking regulations
    - Data protection and privacy compliance

## Implementation Phases

### Phase 1: Core Infrastructure

1. Database migrations and models
2. Basic authentication system
3. Owner registration and profile management

### Phase 2: Slot Management

1. Slot CRUD operations
2. Rate card management
3. Basic dashboard views

### Phase 3: Financial Integration

1. Payment processing
2. Revenue tracking
3. Withdrawal system

### Phase 4: Analytics & Reporting

1. Usage statistics
2. Revenue reports
3. Performance analytics

## Future Considerations

1. Mobile app for owners
2. Advanced analytics dashboard
3. Integration with parking hardware
4. Multiple payment gateway support
5. Automated compliance checking
6. Dynamic pricing based on demand
