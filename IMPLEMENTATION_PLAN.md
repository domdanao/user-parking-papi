# Parking Payment System Implementation Plan

## System Overview

A web-based system allowing car owners to pay for street parking through QR code scanning and automated wallet payments.

## Database Structure

### 1. Slots Table

Stores information about parking spaces

```sql
CREATE TABLE slots (
    id BIGINT PRIMARY KEY,
    identifier VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    location POINT,                              -- Stores coordinates as (longitude,latitude)
    status ENUM('available', 'occupied', 'unavailable'),
    metadata JSON,                               -- Flexible storage for additional slot properties
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 2. Rate Cards Table

Defines hourly parking rates

```sql
CREATE TABLE rate_cards (
    id BIGINT PRIMARY KEY,
    hour_block INT,
    rate INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

Initial rates:

-   First 2 hours: ₱60
-   3rd hour: ₱40
-   4th hour: ₱50
-   5th hour: ₱60
-   6th hour: ₱70
-   7th hour: ₱80
-   8th hour: ₱90
-   P100 after 8th hour

### 3. Wallets Table

Manages user payment balances

```sql
CREATE TABLE wallets (
    id BIGINT PRIMARY KEY,
    user_id BIGINT UNIQUE,
    balance INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 4. Parking Sessions Table

Tracks active and historical parking usage

```sql
CREATE TABLE parking_sessions (
    id BIGINT PRIMARY KEY,
    slot_id BIGINT,
    user_id BIGINT,                              -- Link to user who created the session
    wallet_id BIGINT,                            -- Link to wallet for payment
    plate_number VARCHAR(20),
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    status ENUM('active', 'completed'),
    total_amount INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (slot_id) REFERENCES slots(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (wallet_id) REFERENCES wallets(id)
);
```

### 5. Wallet Transactions Table

Records all wallet operations

```sql
CREATE TABLE wallet_transactions (
    id BIGINT PRIMARY KEY,
    wallet_id BIGINT,
    amount INT,
    type ENUM('debit', 'credit'),
    reference VARCHAR(255),
    parking_session_id BIGINT NULL,              -- Optional link to related parking session
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id),
    FOREIGN KEY (parking_session_id) REFERENCES parking_sessions(id)
);
```

## Data Relationships

### User & Wallet

-   One-to-one relationship: Each user has exactly one wallet
-   Wallet is automatically created upon user registration
-   Wallet is deleted when user account is deleted
-   Balance operations are atomic and validated

### Wallet & Transactions

-   One-to-many relationship: Each wallet has multiple transactions
-   Transactions maintain wallet balance history
-   Each transaction references the wallet and parking session

### User & Parking Sessions

-   One-to-many relationship: User can have multiple active/historical sessions
-   Sessions track vehicle details and payment status
-   Linked to wallet for payment processing

## Core Features

### 1. Slot Management

-   Unique QR code generation for each slot
-   URL pattern: https://domain/slot/slot-identifier
-   Real-time slot status tracking
-   Slot availability updates
-   Geographic location tracking
-   Proximity-based slot discovery
-   Location-based search and filtering

### 2. Payment Processing

-   QR code scanning and redirection
-   Plate number/car sticker validation
-   Wallet balance verification
-   Initial payment processing (first 2 hours)
-   Automated additional hour charges

### 3. Wallet System

-   Secure wallet creation
-   Balance management
-   Top-up functionality
    -   Credit card integration
    -   Debit card support
    -   Bank transfer options
-   Transaction history
-   Low balance alerts

### 4. Session Management

-   Active session tracking
-   Time monitoring
-   Automatic payment processing
-   Session extension handling
-   Historical session records

### 5. Notification System

-   Time expiry warnings (15 minutes before)
-   Low wallet balance alerts
-   Payment confirmations
-   Clamping risk notifications
-   Session status updates

## API Endpoints

### Slot Management

```
GET /api/slots
- List all parking slots
- Query parameters for filtering
- Geographic radius search (lat, long, radius)
- Sort by distance from coordinates

GET /api/slots/{identifier}
- Get specific slot details
- Includes current status

POST /api/slots
- Create new parking slot
- Generate QR code
```

### Payment & Sessions

```
GET /slot/{identifier}
- Display payment form
- Show current rates

POST /api/parking-sessions
- Start new parking session
- Initial payment processing

GET /api/parking-sessions/active
- View current active session
- Time remaining

PATCH /api/parking-sessions/{id}
- Extend parking duration
- Process additional payment
```

### Wallet Operations

```
GET /api/wallet/balance
- Check current balance
- Recent transactions

POST /api/wallet/top-up
- Add funds to wallet
- Payment gateway integration

GET /api/wallet/transactions
- Transaction history
- Filter and sort options
```

## Implementation Steps

### Phase 1: Foundation

1. Database setup

    - Create migrations
    - Set up models
    - Define relationships
    - Implement wallet creation hooks
    - Add balance operation safeguards

2. Basic slot management

    - Slot CRUD operations
    - QR code generation
    - Identifier system

3. Wallet system
    - Basic wallet operations
    - Transaction logging
    - Balance management

### Phase 2: Core Features

1. Payment processing

    - Payment form implementation
    - Rate calculation system
    - Session tracking

2. Notification system

    - Time tracking
    - Alert configurations
    - Notification dispatch

3. Automated processes
    - Payment scheduling
    - Balance checks
    - Session management

### Phase 3: Enhancement

1. User experience

    - Mobile responsiveness
    - Error handling
    - Loading states

2. Security

    - Input validation
    - Transaction safety
    - Rate limiting

3. Performance
    - Query optimization
    - Caching strategy
    - Background jobs

## Testing Strategy

### Unit Tests

-   Rate calculation accuracy
-   Wallet operations
-   Session management
-   Notification triggers

### Integration Tests

-   Payment flow
-   Session creation
-   Wallet interactions
-   API endpoints

### End-to-End Tests

-   Complete parking flow
-   Payment processing
-   Notification delivery
-   QR code functionality

## Monitoring & Maintenance

### System Health

-   Server monitoring
-   Database performance
-   API response times
-   Error tracking

### Business Metrics

-   Usage statistics
-   Revenue tracking
-   Session analytics
-   Payment success rates

## Security Considerations

### Data Protection

-   Encryption at rest
-   Secure transmission
-   PCI compliance
-   Personal data handling

### Transaction Safety

-   Payment validation
-   Balance locks
-   Concurrent operation handling
-   Audit logging

## Future Enhancements

### Potential Features

-   Mobile app integration
-   Advanced analytics
-   Dynamic pricing
-   Loyalty programs
-   Multiple vehicle support

### Scalability

-   Horizontal scaling
-   Load balancing
-   Cache optimization
-   Database sharding strategy
