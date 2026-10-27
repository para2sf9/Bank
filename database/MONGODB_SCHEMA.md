# PGi Bankings & Finance - MongoDB Atlas Schema

## Collections

### 1. users
```json
{
  "_id": ObjectId,
  "first_name": "string",
  "last_name": "string",
  "email": "string (unique)",
  "phone": "string",
  "password_hash": "string (bcrypt)",
  "address": "string",
  "dob": "string (YYYY-MM-DD)",
  "role": "string (customer|staff|admin)",
  "status": "string (active|inactive|suspended|pending)",
  "kyc_status": "string (pending|verified|rejected)",
  "created_at": ISODate,
  "updated_at": ISODate
}
```

### 2. accounts
```json
{
  "_id": ObjectId,
  "user_id": ObjectId (ref: users),
  "account_number": "string (unique)",
  "account_type": "string (savings|current|business)",
  "balance": 0.00,
  "status": "string (active|inactive|frozen|closed)",
  "created_at": ISODate
}
```

### 3. transactions
```json
{
  "_id": ObjectId,
  "transaction_id": "string (unique)",
  "account_id": ObjectId (ref: accounts),
  "user_id": ObjectId (ref: users),
  "type": "string (deposit|withdrawal|transfer|payment|loan)",
  "amount": 0.00,
  "description": "string",
  "receiver_account": "string",
  "status": "string (completed|pending|failed|reversed)",
  "created_at": ISODate
}
```

### 4. loans
```json
{
  "_id": ObjectId,
  "user_id": ObjectId (ref: users),
  "loan_type": "string (home|auto|education|personal|sme)",
  "amount": 0.00,
  "interest_rate": 0.00,
  "duration_months": 0,
  "monthly_payment": 0.00,
  "total_payable": 0.00,
  "purpose": "string",
  "status": "string (pending|approved|rejected|active|closed)",
  "applied_at": ISODate,
  "approved_at": ISODate
}
```

### 5. cards
```json
{
  "_id": ObjectId,
  "user_id": ObjectId (ref: users),
  "card_number": "string (unique)",
  "card_type": "string (visa|mastercard)",
  "expiry_date": "string (MM/YY)",
  "cvv": "string",
  "status": "string (active|blocked|expired)",
  "daily_limit": 5000.00,
  "created_at": ISODate
}
```

### 6. tickets (Support)
```json
{
  "_id": ObjectId,
  "user_id": ObjectId (ref: users),
  "subject": "string",
  "message": "string",
  "category": "string (general|technical|billing|loan|card)",
  "status": "string (open|in_progress|resolved|closed)",
  "created_at": ISODate
}
```

### 7. activity_log
```json
{
  "_id": ObjectId,
  "user_id": ObjectId (ref: users),
  "action": "string",
  "ip_address": "string",
  "user_agent": "string",
  "created_at": ISODate
}
```

## Setup Instructions

1. Install MongoDB PHP Extension:
   pecl install mongodb
   # Add to php.ini: extension=mongodb.so

2. Install MongoDB PHP Library:
   composer require mongodb/mongodb

3. Create MongoDB Atlas Cluster (Free Tier):
   - Visit https://www.mongodb.com/cloud/atlas
   - Sign up and create a FREE M0 cluster
   - Create a database user
   - Add IP whitelist: 0.0.0.0/0
   - Get your connection string

4. Update includes/config.php with your connection string

5. No need to create collections manually - MongoDB creates them automatically on first insert
